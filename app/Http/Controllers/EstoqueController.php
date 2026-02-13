<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class EstoqueController extends Controller
{
    public function index(Request $request)
    {
        $codigoPai = trim((string) $request->get('codigo_pai', ''));
        

        $mapLoja = [
            8  => 'ALPHAVILLE',
            5  => 'JK',
            15 => 'RIO',
            2  => 'ATACADO',
            11 => 'ECOMMERCE',
            18 => 'CURITIBA',
        ];

        $lojasFixas = array_values($mapLoja);
        $totaisPorLoja = array_fill_keys($lojasFixas, 0);

        // "Produto vazio" para renderizar o layout SEM quebrar
        $produtoVazio = (object) [
            'cod_produto_pai' => null,
            'des_produto'     => null,
            'refid_pai'       => null,
            'preco_01'        => null,
            'preco_02'        => null,
            'preco_03'        => null,
            'preco_04'        => null,
        ];

        // Abre a tela completa (sem dados) quando não tem busca
        if ($codigoPai === '') {
            return view('estoque', [
                'codigoPai'     => '',
                'produto'       => $produtoVazio,
                'lojasFixas'    => $lojasFixas,
                'matriz'        => [],
                'totaisPorLoja' => $totaisPorLoja,
                'encontrou'     => false,
            ]);
        }

        // 1) Produto (pega 1 linha do pai)
        $produto = DB::table('VW_SALDO_ESTOQUE')
            ->select([
                'cod_produto_pai',
                'cod_produto',
                'refid_pai',
                'des_produto',
                'preco_01',
                'preco_02',
                'preco_03',
                'preco_04',
            ])
            ->where('cod_produto_pai', $codigoPai)
            ->first();
        

        // Se não achou, mantém layout completo e sinaliza que não encontrou
        if (!$produto) {
            return view('estoque', [
                'codigoPai'     => $codigoPai,
                'produto'       => $produtoVazio,
                'lojasFixas'    => $lojasFixas,
                'matriz'        => [],
                'totaisPorLoja' => $totaisPorLoja,
                'encontrou'     => false,
            ]);
        }

        // 2) Linhas (variação x loja) 
        try {
            $linhas = DB::table('VW_SALDO_ESTOQUE')
                ->select([
                    'cod_produto_pai',
                    'cod_produto',
                    'des_tamanho',
                    'des_cor',
                    'des1_produto',
                    'local',
                    DB::raw('SUM(COALESCE(vquantidade,0)) as qtd'),
                ])
                ->where('cod_produto_pai', $codigoPai)
                ->groupBy('cod_produto_pai', 'cod_produto', 'des_tamanho', 'des_cor','des1_produto', 'local')
                ->orderBy('cod_produto')
                ->get();
        } catch (QueryException $e) {
            // fallback caso em algum ambiente a view esteja com "quantidade"
            $linhas = DB::table('VW_SALDO_ESTOQUE')
                ->select([
                    'cod_produto_pai',
                    'cod_produto',
                    'des_tamanho',
                    'des_cor',
                    'des1_produto',
                    'local',
                    DB::raw('SUM(COALESCE(quantidade,0)) as qtd'),
                ])
                ->where('cod_produto_pai', $codigoPai)
                ->groupBy('cod_produto_pai', 'cod_produto', 'des_tamanho', 'des_cor','des1_produto', 'local')
                ->orderBy('cod_produto')
                ->get();
        }

        // 3) Monta matriz
        $matriz = [];

        foreach ($linhas as $r) {
            $localNum = (int) $r->local;
            if (!isset($mapLoja[$localNum])) continue;

            $loja = $mapLoja[$localNum];

            $variacaoCodigo = (string) $r->cod_produto;
            $variacaoDesc   = trim(($r->des_cor ?? '') . ' ' . ($r->des_tamanho ?? ''));

            if (!isset($matriz[$variacaoCodigo])) {
                $matriz[$variacaoCodigo] = [
                    'codigo' => $variacaoCodigo,
                    'desc'   => $variacaoDesc,
                    'lojas'  => array_fill_keys($lojasFixas, 0),
                ];
            }

            $qtd = (int) ($r->qtd ?? 0);
            $matriz[$variacaoCodigo]['lojas'][$loja] = $qtd;
            $totaisPorLoja[$loja] += $qtd;
        }

        return view('estoque', [
            'codigoPai'     => $codigoPai,
            'produto'       => $produto,
            'lojasFixas'    => $lojasFixas,
            'matriz'        => $matriz,
            'totaisPorLoja' => $totaisPorLoja,
            'encontrou'     => true,
        ]);
    }
}
