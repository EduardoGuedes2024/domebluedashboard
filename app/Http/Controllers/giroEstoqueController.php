<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiroEstoqueController extends Controller
{
    public function index(Request $request)
    {
        // select grupos
        $grupos = DB::table('VW_SALDO_GERAL')
            ->select('des_grupo')
            ->distinct()
            ->orderBy('des_grupo')
            ->pluck('des_grupo')
            ->map(fn($item) => is_string($item) ? mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1') : $item)
            ->toArray();

        $unidade = $request->get('loja');
        $grupoFiltro = $request->get('grupo');
        $filtrado = $request->has('loja');

        // Se não houver filtro, inicia a tela com card de infos
        if (!$filtrado) {
            $resumoZerado = [
                '30 dias' => 0, 
                '60 dias' => 0, 
                '90 dias' => 0, 
                '120 dias' => 0, 
                '150 dias' => 0
            ];

            return view('giroEstoque', [
                'filtrado'        => false,
                'grupos'          => $grupos,
                'LojaSelecionada' => null,
                'totalGeralPecas' => 0,
                'totalGeralValor' => 0,
                'resumoGiro'      => $resumoZerado,
                'valoresGiro'     => $resumoZerado,
                'porcentagens'    => $resumoZerado
            ]);
        }

        // Mapeamento Lojas
        $map = [
            'jk'         => ['saldo' => 'saldo_jk',         'data' => 'ultima_venda_jk'],
            'alphaville' => ['saldo' => 'saldo_outlet',     'data' => 'ultima_venda_outlet'],
            'curitiba'   => ['saldo' => 'saldo_curitiba',   'data' => 'ultima_venda_cj'],
            'rio'        => ['saldo' => 'saldo_rj',         'data' => 'ultima_venda_rj'],
            'atacado'    => ['saldo' => 'saldo_atacado',    'data' => 'ultima_venda_atacado'],
            'ecommerce'  => ['saldo' => 'saldo_ecommerce',  'data' => 'ultima_venda_ecommerce'],
        ];

        $saldoCol = $map[$unidade]['saldo'] ?? 0;
        $dataCol  = $map[$unidade]['data'] ?? 0;
        $filtroExtra = $grupoFiltro ? " AND des_grupo = '{$grupoFiltro}' " : "";

        //Select lojas 
        $sqlBase = "
            SELECT
                SUM(CASE WHEN $dataCol >= DATEADD(DAY, -30, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_30,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_60,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_90,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_120,
                SUM(CASE WHEN ($dataCol < DATEADD(DAY, -120, GETDATE()) OR $dataCol = '1900-01-01' OR $dataCol IS NULL) THEN $saldoCol ELSE 0 END) AS acima_150,
                
                SUM(CASE WHEN $dataCol >= DATEADD(DAY, -30, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_30,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_60,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_90,
                SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_120,
                SUM(CASE WHEN ($dataCol < DATEADD(DAY, -120, GETDATE()) OR $dataCol = '1900-01-01' OR $dataCol IS NULL) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_150,

                SUM($saldoCol) AS total_pecas,
                SUM($saldoCol * preco_01) AS total_valor
            FROM VW_SALDO_GERAL
            WHERE $saldoCol > 0 $filtroExtra
        ";

        $dados = DB::select($sqlBase);
        $giro = (array) $dados[0];

        $resumoGiro = [
            '30 dias'  => (int)$giro['dias_30'],
            '60 dias'  => (int)$giro['dias_60'],
            '90 dias'  => (int)$giro['dias_90'],
            '120 dias' => (int)$giro['dias_120'],
            '150 dias' => (int)$giro['acima_150']
        ];

        $valoresGiro = [
            '30 dias'  => (float)$giro['v_30'],
            '60 dias'  => (float)$giro['v_60'],
            '90 dias'  => (float)$giro['v_90'],
            '120 dias' => (float)$giro['v_120'],
            '150 dias' => (float)$giro['v_150']
        ];

        $totalGeralPecas = (int)$giro['total_pecas'];
        $totalGeralValor = (float)$giro['total_valor'];
        
        $porcentagens = [];
        foreach ($resumoGiro as $faixa => $qtd) {
            $porcentagens[$faixa] = $totalGeralPecas > 0 ? ($qtd / $totalGeralPecas) * 100 : 0;
        }

        return view('giroEstoque', [
            'filtrado'        => true,
            'resumoGiro'      => $resumoGiro,
            'valoresGiro'     => $valoresGiro, 
            'porcentagens'    => $porcentagens,
            'totalGeralPecas' => $totalGeralPecas,
            'totalGeralValor' => $totalGeralValor,
            'LojaSelecionada' => $unidade,
            'grupos'          => $grupos
        ]);
    }

    public function listaProdutos(Request $request) 
    {
        $unidade = $request->get('loja');
        $grupo = $request->get('grupo');
        $periodo = $request->get('periodo');

        $map = [
            'jk' => ['saldo' => 'saldo_jk', 'data' => 'ultima_venda_jk'],

            'alphaville' => ['saldo' => 'saldo_outlet', 'data' => 'ultima_venda_outlet'],

            'curitiba' => ['saldo' => 'saldo_curitiba', 'data' => 'ultima_venda_cj'],

            'rio' => ['saldo' => 'saldo_rj', 'data' => 'ultima_venda_rj'],

            'atacado' => ['saldo' => 'saldo_atacado', 'data' => 'ultima_venda_atacado'],

            'ecommerce' => ['saldo' => 'saldo_ecommerce', 'data' => 'ultima_venda_ecommerce'],
        ];

        $saldoCol = $map[$unidade]['saldo'] ?? 0;

        $dataCol = $map[$unidade]['data'] ?? 0;

        $filtroGrupo = $grupo ? "AND G.des_grupo = '{$grupo}'" : "";
        $filtroGrupoTotal = $grupo ? "AND des_grupo = '{$grupo}'" : "";

        $condicaoData = match($periodo) {
            '30'  => "{$dataCol} >= DATEADD(DAY, -30, GETDATE())",
            '60'  => "{$dataCol} BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE())",
            '90'  => "{$dataCol} BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE())",
            '120' => "{$dataCol} BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE())",
            '150' => "({$dataCol} < DATEADD(DAY, -120, GETDATE()) OR {$dataCol} = '1900-01-01' OR {$dataCol} IS NULL)",
        };

        // 2. Criamos a versão para a LISTA colocando o prefixo G.
        $condicaoLista = str_replace($dataCol, "G.{$dataCol}", $condicaoData);

        // 3. Query da LISTA (Cards)
        $sqlLista = "
            SELECT DISTINCT
                G.cod_produto, G.cod_produto_pai, G.des_produto, G.des1_produto, 
                G.{$saldoCol} as saldo, G.preco_01 as preco, E.refid_pai 
            FROM VW_SALDO_GERAL G
            LEFT JOIN VW_SALDO_ESTOQUE E ON G.cod_produto = E.cod_produto
            WHERE CAST(G.{$saldoCol} AS INT) > 0 
            AND G.{$dataCol} IS NOT NULL
            AND G.{$dataCol} <> '1900-01-01'
            AND ({$condicaoLista}) -- Agora vai ler: (G.ultima_venda_jk BETWEEN ...)
            $filtroGrupo
            ORDER BY G.cod_produto_pai ASC, G.des1_produto ASC
        ";

        $produtos = DB::select($sqlLista);
        // 4. Query de TOTAIS (Resumo do Header)
        $sqlTotal = "
            SELECT 
                SUM(CAST({$saldoCol} AS INT)) as total_pecas,
                SUM(CAST({$saldoCol} AS INT) * preco_01) as total_valor
            FROM VW_SALDO_GERAL
            WHERE CAST({$saldoCol} AS INT) > 0 
            
            AND ({$condicaoData}) -- Agora vai ler: (ultima_venda_jk BETWEEN ...)
            $filtroGrupoTotal
        ";

        $dadosTotal = DB::select($sqlTotal);

        $totalPecas = $dadosTotal[0]->total_pecas ?? 0;
        $totalValor = $dadosTotal[0]->total_valor ?? 0;

        return view('giroEstoqueLista', compact('produtos', 'unidade', 'periodo', 'totalPecas', 'totalValor', 'grupo'));
    }
}