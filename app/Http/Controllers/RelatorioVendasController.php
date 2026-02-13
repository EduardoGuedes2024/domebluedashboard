<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class RelatorioVendasController extends Controller
{
    public function index(Request $request)
    {
        //  Só processa se clicar em filtrar 
        if (!$request->filled('data_inicio')) {
            return view('vendas', [
                'cards' => [],
                'pagination' => null,
                'lojasFixas' => [
                    'Alphaville iguatemi', 
                    'Amissima JK', 
                    'RJ', 
                    'ATACADO', 
                    'E-COMMERCE', 
                    'Amissima Curitiba'
                    ]
            ]);
        }

        $inicio = $request->get('data_inicio');
        $fim = $request->get('data_fim');
        $empresa = $request->get('empresa');

        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        $filtroEmpresa = "";
        if ($empresa) {
            $filtroEmpresa = "AND empresa ='{$empresa}'";
        }

        
        // Primeiro descobrimos o total para a paginação
        $sqlTotal = "
            SELECT COUNT(*) as total 
            FROM (
                SELECT produto_pai 
                FROM VW_VENDAS_TODOS 
                WHERE data_venda BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
                {$filtroEmpresa}
                GROUP BY produto_pai
            ) t
        ";
        $totalPais = (int) (DB::selectOne($sqlTotal)->total ?? 0);

        //  pegamos os Pais do ranking quem vendeu mais aparece primeiro
        $sqlRanking = "
            SELECT produto_pai
            FROM (
                SELECT 
                    produto_pai, 
                    SUM(quantidade) as total_vendas,
                    ROW_NUMBER() OVER (ORDER BY SUM(quantidade) DESC) as rn
                FROM VW_VENDAS_TODOS
                WHERE data_venda BETWEEN '{$inicio} 00:00:00' AND '{$fim} 23:59:59'
                {$filtroEmpresa}
                GROUP BY produto_pai
            ) x
            WHERE x.rn BETWEEN ? AND ?
            ORDER BY x.rn
        ";

        $pais = collect(DB::select($sqlRanking, [$start, $end]))->pluck('produto_pai');

        $pagination = new LengthAwarePaginator(
            $pais,
            $totalPais,
            $perPage,
            $page,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        // Busca detalhes dos produtos e variações
        $cards = [];

        $lojasFixas = [
            'Alphaville iguatemi', 
            'Amissima JK' , 
            'RJ', 
            'ATACADO' , 
            'E-COMMERCE', 
            'Amissima Curitiba'
        ];

        foreach ($pais as $pai) {

            // Pega informações básicas do produto
            $info = DB::table('VW_VENDAS_TODOS as v')

                ->join('VW_SALDO_ESTOQUE as e', 'v.produto_pai', '=', 'e.cod_produto_pai') //inner join para pega refid e precos 

                ->select([
                    'v.descricao_produto',
                    'v.grupo_produto',
                    'v.sub_grupo_produto',
                    'e.refid_pai',
                    'e.preco_01',
                    'e.preco_02'
                ])
                ->where('v.produto_pai', $pai)
                ->first();

            // Pega as variações vendidas
            $vendas = DB::table('VW_VENDAS_TODOS')
                ->select(
                    'codigo_produto', 
                    'complemento_produto', 
                    'origem'
                    )
                ->selectRaw('SUM(quantidade) as qtd')
                ->where('produto_pai', $pai)
                ->whereBetween('data_venda', ["$inicio 00:00:00", "$fim 23:59:59"])
                ->when($empresa, function ($query, $empresa) {
                    return $query->where('empresa', $empresa);
                })
                ->groupBy('codigo_produto', 'complemento_produto', 'origem')
                ->get();

            $matriz = [];
            foreach ($vendas as $v) {
                $vcodigo = trim($v->codigo_produto);
                if (!isset($matriz[$vcodigo])) {
                    $matriz[$vcodigo] = [
                        'codigo' => $vcodigo,
                        'desc' => $v->complemento_produto,
                        'lojas' => array_fill_keys($lojasFixas, 0),
                        'total_linha' => 0
                    ];
                }
                // Garante que só soma se a loja estiver no mapa
                if (isset($matriz[$vcodigo]['lojas'][$v->origem])) {
                    $matriz[$vcodigo]['lojas'][$v->origem] = (int)$v->qtd;
                }
                $matriz[$vcodigo]['total_linha'] += (int)$v->qtd;
            }

            $cards[] = [
                'produto_pai' => trim($pai),
                'refid'       => trim($info->refid_pai ?? $pai), 
                'descricao'   => $info->descricao_produto ?? 'Produto Sem Nome',
                'preco_v'     => $info->preco_01 ?? 0, // Varejo
                'preco_a'     => $info->preco_02 ?? 0, // Atacado
                'grupo'       => $info->grupo_produto ?? '-',
                'subgrupo'    => $info->sub_grupo_produto ?? '-',
                'matriz'      => array_values($matriz),
                'total_geral' => collect($matriz)->sum('total_linha')
            ];
        }

        return view('vendas', [
            'cards'      => $cards,
            'pagination' => $pagination->appends($request->all()), // Isso mantém os filtros na URL ao trocar de página
            'lojasFixas' => $lojasFixas
        ]);
    }
}