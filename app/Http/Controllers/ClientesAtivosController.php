<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientesAtivosController extends Controller
{

    public function getMunicipios($uf) 
    {
        // Agrupamos pelo código do município para garantir que ele seja único
        $municipiosRaw = DB::table('dbo.cadcli')
            ->select('cod_municipio', DB::raw("MAX(des_cidade) as des_cidade"))
            ->where('uf_cliente', trim($uf))
            ->whereNotNull('cod_municipio')
            ->where('cod_municipio', '<>', '')
            ->groupBy('cod_municipio') // A mágica acontece aqui: um registro por código
            ->get();

        $municipios = $municipiosRaw->map(function($item) {
            // Limpamos e convertemos o nome que veio do MAX()
            $nomeLimpo = trim($item->des_cidade);
            $nomeConvertido = mb_convert_encoding($nomeLimpo, 'UTF-8', 'ISO-8859-1');

            return [
                'codigo' => trim($item->cod_municipio),
                'nome' => mb_strtoupper($nomeConvertido) // Padroniza tudo em maiúsculo
            ];
        })->sortBy('nome'); // Ordena alfabeticamente pelo nome para facilitar a busca

        return response()->json($municipios->values());
    }


    public function index(Request $request)
    {
        
        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $start = (($page - 1) * $perPage) + 1;
        $end = $page * $perPage;

        // FILTROS DINÂMICOS
        $busca = $request->get('busca_cliente');
        $dataInicio = $request->get('data_inicio', now()->subMonths(2)->format('Y-m-d')); // Padrão 2 meses
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        $municipio = $request->get('busca_municipio');

        //Pega todos os Estados únicos para o primeiro select
        $estados = DB::table('dbo.cadcli')
            ->select('uf_cliente')
            ->whereNotNull('uf_cliente')
            ->where('tipo_cliente', 'J')
            ->distinct()
            ->orderBy('uf_cliente', 'asc')
            ->pluck('uf_cliente');


        // BUSCA DE IDs COM PRIORIDADE PARA ATIVOS NO PERÍODO
        $sqlRanking = "
            SELECT cod_cliente FROM (
                SELECT 
                    c.cod_cliente, 
                    ROW_NUMBER() OVER (
                        ORDER BY 
                            -- PESO 1: Quem comprou no período (Independente de UF ou Filtro)
                            (SELECT COUNT(*) FROM dbo.pedidov p 
                            WHERE p.cod_cliente = c.cod_cliente 
                            AND p.data_emissao BETWEEN '{$dataInicio}' AND '{$dataFim}') DESC,
                            
                            -- PESO 2: Última compra geral (Para os ativos ficarem por ordem de recência)
                            (SELECT MAX(data_emissao) FROM dbo.pedidov p3 WHERE p3.cod_cliente = c.cod_cliente) DESC,
                            
                            -- PESO 3: Código do cliente
                            c.cod_cliente DESC
                    ) as rn
                FROM dbo.cadcli c
                WHERE c.tipo_cliente = 'J'
                " . ($busca ? " AND (c.cod_cliente LIKE '%{$busca}%' OR c.raz_cliente LIKE '%{$busca}%')" : "") . "
                " . ($request->get('busca_uf') ? " AND RTRIM(c.uf_cliente) = '{$request->get('busca_uf')}'" : "") . "
                " . ($request->get('busca_municipio') ? " AND c.cod_municipio = '{$request->get('busca_municipio')}'" : "") . "
                
            ) x WHERE x.rn BETWEEN ? AND ?
        ";

        $idsPaginados = collect(DB::select($sqlRanking, [$start, $end]))
            ->pluck('cod_cliente')
            ->map(fn($id) => (int) trim($id)) 
            ->toArray();

        $totalClientes = DB::table('cadcli')->where('tipo_cliente', 'J')->count();

        // BUSCA DETALHES Endereço + Compra
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

        return view('clientes_Ativos', compact('clientes', 'dataInicio', 'dataFim', 'estados'));
    }
}
