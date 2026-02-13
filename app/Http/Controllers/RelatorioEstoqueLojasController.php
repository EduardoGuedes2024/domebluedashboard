<?php

namespace App\Http\Controllers;  // define onde o arquivo mora (pastas)

use Illuminate\Http\Request; // aqui lida com oque o usauario vai enviar (filtros , paginaçao)
use Illuminate\Support\Facades\DB; // contato com o banco de dados 
use Illuminate\Pagination\LengthAwarePaginator; // onde cria as paginas 
use Barryvdh\DomPDF\Facade\Pdf; // biblioteca dompdf o gerador de PDF
use Illuminate\Support\Facades\Http; // aqui onde vai ne internet e busca as imagens 

class RelatorioEstoqueLojasController extends Controller
{
    
    //=== FUNÇAO QUE DEFINE SE É TELA OU PDF =======
    public function index(Request $request) 
    {
        //  Verifica se o usuário clicou em 'Buscar' (se enviou algum filtro)
        // Se não enviou nada, criamos um array básico para não dar erro no Blade
        if (!$request->filled('local') && !$request->filled('grupo') && !$request->filled('subgrupo')) {
            $data = [
                'cards'      => [], // Começa sem nenhum produto
                'local'      => 0,
                
                'lojasFixas' => [
                    'ALPHAVILLE', 
                    'JK', 
                    'RIO', 
                    'ATACADO', 
                    'ECOMMERCE', 
                    'CURITIBA'
                    ],
                'pagination' => null
            ];
        } else {
            // Se houver filtro, aí sim chama o motor pesado
            $data = $this->buildRelatorio($request, true);
        }

        // Mantemos a busca dos grupos/subgrupos para alimentar os selects do topo
        $grupos = DB::table('VW_SALDO_ESTOQUE')
            ->select('des_grupo')
            ->whereNotNull('des_grupo')
            ->groupBy('des_grupo')
            ->orderBy('des_grupo')
            ->pluck('des_grupo')
            ->map(fn($item) => mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1'));

        $subgrupos = DB::table('VW_SALDO_ESTOQUE')
            ->select('des_sgrupo')
            ->whereNotNull('des_sgrupo')
            ->groupBy('des_sgrupo')
            ->orderBy('des_sgrupo')
            ->pluck('des_sgrupo')
            ->map(fn($item) => mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1'));

        return view('estoque_lojas', array_merge($data, [
            'grupos'    => $grupos,
            'subgrupos' => $subgrupos
        ]));
    }


    //===== FUNÇAO CHAMA PDF ========
    public function exportPdf(Request $request)
    {
        // sem paginação (puxa tudo)
        $data = $this->buildRelatorio($request, false);

        $local = (int) ($data['local'] ?? 0);
        $mapLoja = $data['mapLoja'] ?? [];
        $localLabel = $local === 0 ? 'TODAS' : ($mapLoja[$local] ?? (string)$local); // define o nome da loja no titulo do PDF

        $pdf = Pdf::loadView('relatorios.estoque_lojas_pdf', [ // carrega o arquivo do PDP 
            'cards'      => $data['cards'],
            'lojasFixas' => $data['lojasFixas'],
            'localLabel' => $localLabel,
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = 'estoque_lojas_' . ($local ?: 'todas') . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($nomeArquivo); // comando para fazer o download
    }


    //==== FUNÇAO PRINCIPAL FILTRO ======
    private function buildRelatorio(Request $request, bool $usarPaginacao = true): array
    {
        // ======= MAPAS NOMES COLUNAS =======
        // (local VW_SALDO_ESTOQUE -> label coluna TELA)
        $mapLoja = [
            8  => 'ALPHAVILLE',
            5  => 'JK',
            15 => 'RIO',
            2  => 'ATACADO',
            11 => 'ECOMMERCE',
            18 => 'CURITIBA',
        ];

        // (local numerico -> coluna da VW_SALDO_GERAL)
        $mapSaldoCol = [
            8  => 'saldo_outlet',
            5  => 'saldo_jk',
            15 => 'saldo_rj',
            2  => 'saldo_atacado',
            11 => 'saldo_ecommerce',
            18 => 'saldo_curitiba',
        ];

        $lojasFixas = array_values($mapLoja);

        // filtro: 0 = todas
        $local = (int) $request->get('local', 0);

        $grupo = $request->get('grupo');

        $subgrupo = $request->get('subgrupo');

        $filtro_grupos = "";
        if($grupo) {
            $filtro_grupos .= "AND des_grupo = '$grupo' ";
        } 
        if($subgrupo) {
            $filtro_grupos .= "AND des_sgrupo = '$subgrupo'";
        } 

        // ======= PEGAR "PAIS" (cod_produdo_pai) =======
        $pais = collect();
        $paisPage = null;

        if ($usarPaginacao) {
            $perPage = 10;
            $page  = max(1, (int) $request->get('page', 1));
            $start = (($page - 1) * $perPage) + 1; // rn inicial (1-based)
            $end   = $page * $perPage;             // rn final

            if ($local === 0) {
                // --- SELECT DE TODAS as lojas base VW_SALDO_ESTOQUE 
                $sqlTotal = "
                    SELECT COUNT(*) as total
                    FROM (
                        SELECT cod_produto_pai, des1_produto,
                        FROM VW_SALDO_ESTOQUE
                        WHERE 1=1 {$filtro_grupos}
                        GROUP BY cod_produto_pai
                        HAVING SUM(COALESCE(vquantidade,0)) > 0
                    ) t
                ";
                $totalPais = (int) (DB::selectOne($sqlTotal)->total ?? 0);

                $sqlPais = "
                    SELECT cod_produto_pai
                    FROM (
                        SELECT
                            cod_produto_pai 
                            ROW_NUMBER() OVER (ORDER BY cod_produto_pai DESC) as rn 
                        FROM (
                            SELECT cod_produto_pai
                            FROM VW_SALDO_ESTOQUE
                            WHERE 1=1 {$filtro_grupos}
                            GROUP BY cod_produto_pai, des1_produto,
                            HAVING SUM(COALESCE(vquantidade,0)) > 0
                        ) p
                    ) x
                    WHERE x.rn BETWEEN ? AND ?
                    ORDER BY x.rn
                ";

                $pais = collect(DB::select($sqlPais, [$start, $end]))
                    ->pluck('cod_produto_pai')
                    ->values();

                $paisPage = new LengthAwarePaginator(
                    $pais,
                    $totalPais,
                    $perPage,
                    $page,
                    ['path' => url()->current(), 'query' => $request->query()]
                );
            } else {
                // --- FILTRO PELA TABELA VW_SALDO_GERAL 
                $colSaldo = $mapSaldoCol[$local] ?? null; 

                if (!$colSaldo) {
                    $totalPais = 0;
                    $pais = collect();
                } else {
                    $sqlTotal = "
                        SELECT COUNT(*) as total
                        FROM (
                            SELECT cod_produto_pai
                            FROM VW_SALDO_GERAL
                            WHERE {$colSaldo} > 0 {$filtro_grupos}
                            GROUP BY cod_produto_pai
                        ) t
                    ";
                    $totalPais = (int) (DB::selectOne($sqlTotal)->total ?? 0);

                    $sqlPais = "
                        SELECT cod_produto_pai
                        FROM (
                            SELECT
                                cod_produto_pai,
                                ROW_NUMBER() OVER (ORDER BY cod_produto_pai DESC) as rn
                            FROM (
                                SELECT cod_produto_pai
                                FROM VW_SALDO_GERAL
                                WHERE {$colSaldo} > 0 {$filtro_grupos}
                                GROUP BY cod_produto_pai
                            ) p
                        ) x
                        WHERE x.rn BETWEEN ? AND ?
                        ORDER BY x.rn
                    ";

                    $pais = collect(DB::select($sqlPais, [$start, $end]))
                        ->pluck('cod_produto_pai')
                        ->values();
                }

                $paisPage = new LengthAwarePaginator(
                    $pais,
                    $totalPais,
                    $perPage,
                    $page,
                    ['path' => url()->current(), 'query' => $request->query()]
                );
            }

        } else {
            // ======= SEM PAGINAÇÃO (PDF) =======
            if ($local === 0) {
                $pais = DB::table('VW_SALDO_ESTOQUE')
                    ->select('cod_produto_pai', 'des1_produto')
                    ->whereRaw("1=1 {$filtro_grupos}")
                    ->groupBy('cod_produto_pai')
                    ->havingRaw('SUM(COALESCE(vquantidade,0)) > 0')
                    ->orderBy('cod_produto_pai', 'DESC')
                    ->pluck('cod_produto_pai')
                    ->values();
            } else {
                $colSaldo = $mapSaldoCol[$local] ?? null;

                $pais = $colSaldo
                    ? DB::table('VW_SALDO_GERAL')
                        ->select('cod_produto_pai')
                        ->whereRaw("{$colSaldo} > 0 {$filtro_grupos}")
                        ->groupBy('cod_produto_pai')
                        ->orderBy('cod_produto_pai', 'DESC')
                        ->pluck('cod_produto_pai')
                        ->values()
                    : collect();
            }
        }

        // layout vazio (sem produto) mas página abre
        if ($pais->isEmpty()) {
            return [
                'local'      => $local,
                'lojasFixas' => $lojasFixas,
                'mapLoja'    => $mapLoja,
                'cards'      => [],
                'pagination' => $paisPage,
            ];
        }

        
        $resumos = DB::table('VW_SALDO_ESTOQUE')
            ->select([
                'cod_produto_pai',
                'des_produto',
                'des1_produto',
                'refid_pai',
                'preco_01',
                'preco_02',
            ])
            ->whereIn('cod_produto_pai', $pais)
            ->groupBy('cod_produto_pai', 'des_produto', 'des1_produto', 'refid_pai', 'preco_01', 'preco_02')
            ->get()
            ->keyBy('cod_produto_pai');

        // ==============
        // local=0 ->comportamento atual (tudo, por loja)
        // local!=0 ->somente variações com saldo no local filtrado, tabela VW_SALDO_GERAL
        if ($local === 0) {
            $linhas = DB::table('VW_SALDO_ESTOQUE')
                ->select([
                    'cod_produto_pai',
                    'cod_produto',
                    'des_tamanho',
                    'des1_produto',
                    'des_cor',
                    'local',
                    DB::raw('SUM(COALESCE(vquantidade,0)) as qtd'),
                ])
                ->whereIn('cod_produto_pai', $pais)
                ->groupBy('cod_produto_pai','cod_produto','des_tamanho', 'des1_produto','des_cor','local')
                ->orderBy('cod_produto_pai')
                ->orderBy('cod_produto')
                ->get();

            $modoPivot = false;
        } else {
            $colSaldo = $mapSaldoCol[$local] ?? null;

            if (!$colSaldo) {
                $linhas = collect();
            } else {
                // pega SOMENTE as variações que existem no local saldo_col > 0),
                // mas trazendo as demais colunas para comparação
                $in = implode(',', array_fill(0, $pais->count(), '?'));

                $sql = "
                    SELECT
                        CONVERT(NVARCHAR(50), cod_produto_pai) AS cod_produto_pai,
                        CONVERT(NVARCHAR(50), cod_produto)     AS cod_produto,
                        CONVERT(NVARCHAR(255), des1_produto)   AS des1_produto,
                        saldo_outlet,
                        saldo_jk,
                        saldo_rj,
                        saldo_atacado,
                        saldo_ecommerce,
                        saldo_curitiba
                    FROM VW_SALDO_GERAL
                    WHERE {$colSaldo} > 0
                      AND CONVERT(NVARCHAR(50), cod_produto_pai) IN ($in)
                    ORDER BY CONVERT(NVARCHAR(50), cod_produto_pai), CONVERT(NVARCHAR(50), cod_produto)
                ";

                // força tudo como string pra não dar treta de tipo no SQL Server
                $bindings = array_map(fn($v) => (string)$v, $pais->toArray());
                $linhas = collect(DB::select($sql, $bindings));
            }

            $modoPivot = true;
        }

        // ===== MONTAR CARDS =======
        $cards = [];

        foreach ($pais as $pai) {

            $p = $resumos->get($pai);

            if (!$p) {
                $p = (object)[
                    'cod_produto_pai' => $pai,
                    'des_produto'     => 'Produto',
                    'refid_pai'       => null,
                    'preco_01'        => null,
                    'preco_02'        => null,
                ];
            }

            // ===== IMAGEM BASE64 SÓ NO PDF - sem paginação =====
            $imgBase64 = null;
            if (!$usarPaginacao && !empty($p->refid_pai)) {
                try {
                    $site = str_starts_with(strtoupper((string)$pai), 'SY') ? 'syssa' : 'amissima';
                    $url  = ($site === 'syssa')
                        ? "https://syssaoficial.com.br/imgitens/{$p->refid_pai}_0.webp"
                        : "https://www.amissima.com.br/imgitens/{$p->refid_pai}_0.webp";

                    $resp = Http::withoutVerifying()->timeout(12)->get($url);

                    if ($resp->ok()) {
                        $bin = $resp->body();
                        $img = @imagecreatefromstring($bin);

                        if ($img) {
                            // reduz um pouco pra não explodir memória no PDF
                            $maxW = 380;
                            $w = imagesx($img);
                            $h = imagesy($img);

                            if ($w > $maxW) {
                                $newW = $maxW;
                                $newH = (int) round($h * ($newW / $w));
                                $tmp = imagecreatetruecolor($newW, $newH);
                                imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                                imagedestroy($img);
                                $img = $tmp;
                            }

                            ob_start();
                            imagejpeg($img, null, 80);
                            $jpgData = ob_get_clean();

                            $imgBase64 = 'data:image/jpeg;base64,' . base64_encode($jpgData);
                            imagedestroy($img);
                        }
                    }
                } catch (\Throwable $e) {
                    $imgBase64 = null;
                }
            }

            $matriz = [];
            $totaisPorLoja = array_fill_keys($lojasFixas, 0);
            $totalGeral = 0;

            // === local=0 trazer tudo por loja ===
            if (!$modoPivot) {
                foreach ($linhas as $r) {
                    if ((string)$r->cod_produto_pai !== (string)$pai) continue;

                    $localNum = (int) $r->local;
                    if (!isset($mapLoja[$localNum])) continue;

                    $loja = $mapLoja[$localNum];
                    $qtd  = (int) ($r->qtd ?? 0);

                    $variacao = (string) $r->cod_produto;
                    $desc = trim(($r->des_cor ?? '') . '/' . ($r->des_tamanho ?? ''));

                    if (!isset($matriz[$variacao])) {
                        $matriz[$variacao] = [
                            'variacao' => $variacao,
                            'desc'     => $desc,
                            'des1_produto' => $r->des1_produto ,
                            'lojas'    => array_fill_keys($lojasFixas, 0),
                            'total'    => 0,
                        ];
                    }

                    $matriz[$variacao]['lojas'][$loja] += $qtd;
                    $matriz[$variacao]['total'] += $qtd;

                    $totaisPorLoja[$loja] += $qtd;
                    $totalGeral += $qtd;
                }
            }
            // === local!=0 se  filtro por loja traz resultado conforme loja filtrada  ===
            else {
                foreach ($linhas as $r) {
                    if ((string)$r->cod_produto_pai !== (string)$pai) continue;

                    $variacao = (string) $r->cod_produto;
                    $desc = '';

                    if (!isset($matriz[$variacao])) {
                        $matriz[$variacao] = [
                            'variacao' => $variacao,
                            'desc'     => $desc,
                            'des1_produto' => $r->des1_produto ?? '',
                            'lojas'    => array_fill_keys($lojasFixas, 0),
                            'total'    => 0,
                        ];
                    }

                    // preenche colunas de comparação mesmas colunas do layout
                    $vals = [
                        'ALPHAVILLE' => (int)($r->saldo_alphaville ?? 0),
                        'JK'         => (int)($r->saldo_jk ?? 0),
                        'RIO'        => (int)($r->saldo_rj ?? 0),
                        'ATACADO'    => (int)($r->saldo_atacado ?? 0),
                        'ECOMMERCE'  => (int)($r->saldo_ecommerce ?? 0),
                        'CURITIBA'   => (int)($r->saldo_curitiba ?? 0),
                    ];

                    foreach ($vals as $loja => $qtd) {
                        if (isset($matriz[$variacao]['lojas'][$loja])) {
                            $matriz[$variacao]['lojas'][$loja] = $qtd;
                            $totaisPorLoja[$loja] += $qtd;
                        }
                    }

                    $linhaTotal = array_sum($vals);
                    $matriz[$variacao]['total'] = $linhaTotal;
                    $totalGeral += $linhaTotal;
                }
            }

            $cards[] = [
                'produto'       => $p,
                'matriz'        => array_values($matriz),
                'totaisPorLoja' => $totaisPorLoja,
                'totalGeral'    => $totalGeral,
                'imgLocal'      => $imgBase64, // usado no PDF
            ];
        }

        return [
            'local'      => $local,
            'lojasFixas' => $lojasFixas,
            'mapLoja'    => $mapLoja,
            'cards'      => $cards,
            'pagination' => $paisPage,
            
        ];
    }
}
