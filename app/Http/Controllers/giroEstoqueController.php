<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiroEstoqueController extends Controller
{
    public function index(Request $request)
    {
        $unidade = $request->get('loja', 'todas');
        $grupoFiltro = $request->get('grupo');

        $map = [
            'jk'         => ['saldo' => 'saldo_jk',         'data' => 'ultima_venda_jk'],
            'alphaville' => ['saldo' => 'saldo_outlet',     'data' => 'ultima_venda_outlet'],
            'curitiba'   => ['saldo' => 'saldo_curitiba',   'data' => 'ultima_venda_cj'],
            'rio'        => ['saldo' => 'saldo_rj',         'data' => 'ultima_venda_rj'],
            'atacado'    => ['saldo' => 'saldo_atacado',    'data' => 'ultima_venda_atacado'],
            'ecommerce'  => ['saldo' => 'saldo_ecommerce',  'data' => 'ultima_venda_ecommerce'],
        ];
            
        $filtroExtra = $grupoFiltro ? " AND des_grupo = '{$grupoFiltro}' " : "";

        if ($unidade === 'todas') {
            $saldoCol = "(saldo_jk + saldo_curitiba + saldo_atacado + saldo_rj + saldo_outlet + saldo_ecommerce)";
            
            $sqlBase = "
                SELECT 
                    -- SOMA DE QUANTIDADES 
                    SUM(CASE WHEN ca.data_final >= DATEADD(DAY, -30, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_30,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_60,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_90,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_120,
                    SUM(CASE WHEN (ca.data_final < DATEADD(DAY, -120, GETDATE()) OR ca.data_final = '1900-01-01' OR ca.data_final IS NULL) THEN $saldoCol ELSE 0 END) AS acima_150,
                    
                    -- SOMA DE VALORES 
                    SUM(CASE WHEN ca.data_final >= DATEADD(DAY, -30, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_30,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_60,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_90,
                    SUM(CASE WHEN ca.data_final BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE()) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_120,
                    SUM(CASE WHEN (ca.data_final < DATEADD(DAY, -120, GETDATE()) OR ca.data_final = '1900-01-01' OR ca.data_final IS NULL) THEN ($saldoCol * preco_01) ELSE 0 END) AS v_150,
                    
                    SUM($saldoCol) AS total_pecas,
                    SUM($saldoCol * preco_01) AS total_valor
                FROM VW_SALDO_GERAL
                CROSS APPLY (
                    SELECT MAX(v) as data_final FROM (VALUES (ultima_venda_jk),(ultima_venda_cj),(ultima_venda_atacado),(ultima_venda_rj),(ultima_venda_ecommerce),(ultima_venda_outlet)) AS value(v)
                ) ca
                WHERE $saldoCol > 0 $filtroExtra
            ";
        } else {
            
            $saldoCol = $map[$unidade]['saldo'] ?? 'saldo_jk';
            $dataCol  = $map[$unidade]['data'] ?? 'ultima_venda_jk';
            
            $sqlBase = "
                SELECT
                    SUM(CASE WHEN $dataCol >= DATEADD(DAY, -30, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_30,
                    SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -60, GETDATE()) AND DATEADD(DAY, -31, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_60,
                    SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -90, GETDATE()) AND DATEADD(DAY, -61, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_90,
                    SUM(CASE WHEN $dataCol BETWEEN DATEADD(DAY, -120, GETDATE()) AND DATEADD(DAY, -91, GETDATE()) THEN $saldoCol ELSE 0 END) AS dias_120,
                    SUM(CASE WHEN ($dataCol < DATEADD(DAY, -120, GETDATE()) OR $dataCol = '1900-01-01' OR $dataCol IS NULL) THEN $saldoCol ELSE 0 END) AS acima_150,
                    
                    -- VALORES R$ INDIVIDUAL
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
        }

        $dados = DB::select($sqlBase);
        $giro = (array) $dados[0];

        // Resumo de Quantidades
        $resumoGiro = [
            '30 dias'  => (int)$giro['dias_30'],
            '60 dias'  => (int)$giro['dias_60'],
            '90 dias'  => (int)$giro['dias_90'],
            '120 dias' => (int)$giro['dias_120'],
            '150 dias' => (int)$giro['acima_150']
        ];

        // Resumo de Valores R$
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

        // Limpeza dos grupos (UTF-8)
        $grupos = DB::table('VW_SALDO_GERAL')->select('des_grupo')->distinct()->orderBy('des_grupo')->pluck('des_grupo')
            ->map(fn($item) => is_string($item) ? mb_convert_encoding($item, 'UTF-8', 'ISO-8859-1') : $item)->toArray();

        return view('giroEstoque', [
            'resumoGiro'      => $resumoGiro,
            'valoresGiro'     => $valoresGiro, 
            'porcentagens'    => $porcentagens,
            'totalGeralPecas' => $totalGeralPecas,
            'totalGeralValor' => $totalGeralValor,
            'LojaSelecionada' => $unidade,
            'grupos'          => $grupos
        ]);
    }
}