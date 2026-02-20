<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RelatorioVendasAtacadoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->filled('data_inicio')) {
            return view('vendas_Atacado', ['cards' => [], 'pagination' => null]);
        }

        $inicio = $request->get('data_inicio');
        $fim = $request->get('data_fim');
        $empresa = $request->get('empresa');
        $lojaAlvo = 'ATACADO'; // LOJA TRAVADA AQUI

        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        $filtroEmpresa = $empresa ? "AND empresa ='{$empresa}'" : "";

        // 1. Total de produtos que venderam NESTA LOJA
        $sqlTotal = "
            SELECT COUNT(*) as total 
            FROM (
                SELECT produto_pai 
                FROM VW_VENDAS_TODOS 
                WHERE data_venda BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
                AND origem = '{$lojaAlvo}'
                {$filtroEmpresa}
                GROUP BY produto_pai
            ) t
        ";
        $totalPais = (int) (DB::selectOne($sqlTotal)->total ?? 0);


        //Cálculo do Faturamento Total (DASH_VENDAS)
        $sqlFaturamento = "
            SELECT 
                SUM(atacado) as faturamento_total
            FROM dash_vendas 
            WHERE data_movimento BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
        ";
        $faturamento = DB::selectOne($sqlFaturamento);


        //Soma do Total de Peças (VW_VENDAS_TODOS)
        $sqlPecas = "
            SELECT 
                SUM(quantidade) as total_pecas
            FROM VW_VENDAS_TODOS 
            WHERE data_venda BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
            AND origem = '{$lojaAlvo}'
            {$filtroEmpresa}
        ";
        $pecas = DB::selectOne($sqlPecas);

        // Montamos o objeto resumo para o Blade não dar erro
        $resumo = (object)[
            'faturamento_total' => $faturamento->faturamento_total ?? 0,
            'total_pecas' => $pecas->total_pecas ?? 0
        ];

        // 2. Ranking de vendas exclusivo da loja alvo
        $sqlRanking = "
            SELECT produto_pai
            FROM (
                SELECT 
                    produto_pai, 
                    SUM(quantidade) as total_vendas,
                    ROW_NUMBER() OVER (ORDER BY SUM(quantidade) DESC) as rn
                FROM VW_VENDAS_TODOS
                WHERE data_venda BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
                AND origem = '{$lojaAlvo}'
                {$filtroEmpresa}
                GROUP BY produto_pai
            ) x
            WHERE x.rn BETWEEN ? AND ?
            ORDER BY x.rn
        ";

        $pais = collect(DB::select($sqlRanking, [$start, $end]))->pluck('produto_pai');

        $pagination = new LengthAwarePaginator(
            $pais, $totalPais, $perPage, $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        $cards = [];
        foreach ($pais as $pai) {
            $info = DB::table('VW_VENDAS_TODOS as v')
                ->join('VW_SALDO_ESTOQUE as e', 'v.produto_pai', '=', 'e.cod_produto_pai')
                ->select(['v.descricao_produto','v.grupo_produto','v.sub_grupo_produto','e.refid_pai','e.preco_01','e.preco_02'])
                ->where('v.produto_pai', $pai)
                ->first();

            $vendas = DB::table('VW_VENDAS_TODOS')
                ->select('codigo_produto', 'complemento_produto')
                ->selectRaw('SUM(quantidade) as qtd')
                ->where('produto_pai', $pai)
                ->where('origem', $lojaAlvo) // Filtro da loja na matriz
                ->whereBetween('data_venda', ["$inicio 00:00:00", "$fim 23:59:59"])
                ->groupBy('codigo_produto', 'complemento_produto')
                ->get();

            $matriz = [];
            foreach ($vendas as $v) {
                $matriz[] = [
                    'codigo' => trim($v->codigo_produto),
                    'desc' => $v->complemento_produto,
                    'qtd' => (int)$v->qtd
                ];
            }

            $cards[] = [
                'produto_pai' => trim($pai),
                'refid' => trim($info->refid_pai ?? $pai), 
                'descricao' => $info->descricao_produto ?? 'Produto Sem Nome',
                'preco_v' => $info->preco_01 ?? 0,
                'preco_a' => $info->preco_02 ?? 0,
                'grupo' => $info->grupo_produto ?? '-',
                'subgrupo' => $info->sub_grupo_produto ?? '-',
                'matriz' => $matriz,
                'total_geral' => collect($matriz)->sum('qtd')
            ];
        }

        return view('vendas_Atacado', compact('cards', 'pagination', 'resumo'));
    }
}