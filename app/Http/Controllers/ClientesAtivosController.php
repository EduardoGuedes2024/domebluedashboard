<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientesAtivosController extends Controller
{
    public function index(Request $request)
    {
        // 1. CONFIGURAÇÃO DE PAGINAÇÃO (Padrão SQL Server que funciona no seu note)
        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        // 2. FILTROS DINÂMICOS
        $busca = $request->get('busca_cliente');
        $dataInicio = $request->get('data_inicio', now()->subMonths(2)->format('Y-m-d')); // Padrão 2 meses
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));

        // 3. BUSCA DE IDs COM RANKING )
        $sqlRanking = "
            SELECT cod_cliente FROM (
                SELECT 
                    c.cod_cliente, 
                    ROW_NUMBER() OVER (ORDER BY c.cod_cliente DESC) as rn
                FROM dbo.cadcli c
                WHERE c.tipo_cliente = 'J'
                " . ($busca ? " AND (c.cod_cliente LIKE '%{$busca}%' OR c.raz_cliente LIKE '%{$busca}%')" : "") . "
            ) x WHERE x.rn BETWEEN ? AND ?
        ";

        $idsPaginados = collect(DB::select($sqlRanking, [$start, $end]))
            ->pluck('cod_cliente')
            ->map(fn($id) => (int) trim($id)) // Limpeza essencial
            ->toArray();

        $totalClientes = DB::table('cadcli')->where('tipo_cliente', 'J')->count();

        // 4. BUSCA DETALHES (Endereço + Inteligência de Compra)
        $detalhes = collect();
        if (!empty($idsPaginados)) {
            $detalhes = DB::table('cadcli as c')
                ->leftJoin('dbo.pedidov as p', 'c.cod_cliente', '=', 'p.cod_cliente')
                ->select([
                    'c.cod_cliente', 
                    'c.raz_cliente', 
                    'c.des_endereco', 
                    'c.numero', 
                    'c.cep_cliente', 
                    'c.cod_municipio', 
                    'c.uf_cliente',
                    'c.cnpj_cliente',
                    DB::raw("CAST(c.red_cliente AS NVARCHAR(MAX)) as red_cliente"),
                    // Verifica se houve compra EXATAMENTE no período filtrado
                    DB::raw("MAX(CASE WHEN p.data_emissao BETWEEN '{$dataInicio}' AND '{$dataFim}' THEN p.data_emissao END) as compra_periodo"),
                    DB::raw("MAX(p.data_emissao) as ultima_compra_geral"),
                    // Subquery para pegar a marca do último produto comprado
                    DB::raw("(SELECT TOP 1 e.refid_pai 
                              FROM pedidov it 
                              JOIN VW_SALDO_ESTOQUE e ON it.cod_produto = e.cod_produto_pai
                              WHERE it.num_pedido = (SELECT TOP 1 p2.num_pedido FROM dbo.pedidov p2 WHERE p2.cod_cliente = c.cod_cliente ORDER BY p2.data_emissao DESC)
                             ) as marca_refid")
                ])
                ->whereIn('c.cod_cliente', $idsPaginados)
                ->groupBy(
                    'c.cod_cliente', 
                    'c.raz_cliente', 
                    'c.des_endereco', 
                    'c.numero', 
                    'c.cep_cliente', 
                    'c.cod_municipio', 
                    'c.uf_cliente', 
                    'c.cnpj_cliente', 
                    DB::raw("CAST(c.red_cliente AS NVARCHAR(MAX))"))
                ->get();
        }

        $clientes = new LengthAwarePaginator(
            $detalhes, 
            $totalClientes, 
            $perPage, 
            $page, 
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('clientes_Ativos', compact('clientes', 'dataInicio', 'dataFim'));
    }
}