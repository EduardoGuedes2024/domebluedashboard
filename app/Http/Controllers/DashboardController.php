<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        
        $temFiltro =
            $request->filled('atual_inicio') || 
            $request->filled('atual_fim') ||
            $request->filled('anterior_inicio') || 
            $request->filled('anterior_fim');

        if ($temFiltro) {
            // ATUAL
            $inicioAtual = Carbon::parse($request->input('atual_inicio'))->startOfDay();
            $fimAtual    = Carbon::parse($request->input('atual_fim'))->endOfDay();

            // ANTERIOR
            $inicioAnterior = Carbon::parse($request->input('anterior_inicio'))->startOfDay();
            $fimAnterior    = Carbon::parse($request->input('anterior_fim'))->endOfDay();
        } else {
            
            

            $inicioAtual = Carbon::now()->startOfDay();
            $fimAtual = Carbon::now()->endOfDay();

            // Anterior: 30 dias antes do início atual
            $inicioAnterior = Carbon::yesterday()->startOfDay();
            $fimAnterior = Carbon::yesterday()->endOfDay();
        }

        try {
            DB::connection()->getPdo();
            $conexaoStatus = "Conectado ao SQL Server";

            $buscarDados = function ($ini, $fim) {
                return DB::table('dash_vendas')
                    ->select([
                        DB::raw('SUM(alphaville) as alphaville_venda'),
                        DB::raw('SUM(alphaville_pedidos) as alphaville_pedidos'),
                        DB::raw('SUM(alphaville_clientes) as alphaville_clientes'),

                        DB::raw('SUM(syssa) as syssa_venda'),
                        DB::raw('SUM(syssa_pedidos) as syssa_pedidos'),
                        DB::raw('SUM(syssa_clientes) as syssa_clientes'),

                        DB::raw('SUM(jk) as jk_venda'),
                        DB::raw('SUM(jk_pedidos) as jk_pedidos'),
                        DB::raw('SUM(jk_clientes) as jk_clientes'),

                        DB::raw('SUM(rio) as rio_venda'),
                        DB::raw('SUM(rio_pedidos) as rio_pedidos'),
                        DB::raw('SUM(rio_clientes) as rio_clientes'),

                        DB::raw('SUM(atacado) as atacado_venda'),
                        DB::raw('SUM(atacado_pedidos) as atacado_pedidos'),
                        DB::raw('SUM(atacado_clientes) as atacado_clientes'),

                        DB::raw('SUM(ecommerce) as ecommerce_venda'),
                        DB::raw('SUM(ecommerce_pedidos) as ecommerce_pedidos'),
                        DB::raw('SUM(ecommerce_clientes) as ecommerce_clientes'),

                        DB::raw('SUM(curitiba) as curitiba_venda'),
                        DB::raw('SUM(curitiba_pedidos) as curitiba_pedidos'),
                        DB::raw('SUM(curitiba_clientes) as curitiba_clientes'),
                    ])
                    ->whereBetween('data_movimento', [$ini, $fim])
                    ->first();
            };

            $dadosAtual    = $buscarDados($inicioAtual, $fimAtual);
            $dadosAnterior = $buscarDados($inicioAnterior, $fimAnterior);

            //============
            // dados para os cards
            //============

            $lojas = [
                [
                    'nome' => 'Alphaville', 
                    'atual' => $dadosAtual->alphaville_venda ?? 0, 
                    'anterior' => $dadosAnterior->alphaville_venda ?? 0, 
                    'cor' => 'blue',

                    'pedidos_atual' => $dadosAtual->alphaville_pedidos ?? 0,
                    'clientes_atual' => $dadosAtual->alphaville_clientes ?? 0,

                    'pedidos_anterior' => $dadosAnterior->alphaville_pedidos ?? 0,
                    'clientes_anterior' => $dadosAnterior->alphaville_clientes ?? 0],

                [
                    'nome' => 'Syssa', 
                    'atual' => $dadosAtual->syssa_venda ?? 0, 
                    'anterior' => $dadosAnterior->syssa_venda ?? 0, 
                    'cor' => 'purple',

                    'pedidos_atual' => $dadosAtual->syssa_pedidos ?? 0,
                    'clientes_atual' => $dadosAtual->syssa_clientes ?? 0,

                    'pedidos_anterior' => $dadosAnterior->syssa_pedidos ?? 0,
                    'clientes_anterior' => $dadosAnterior->syssa_clientes ?? 0],

                [
                    'nome' => 'JK Iguatemi', 
                    'atual' => $dadosAtual->jk_venda ?? 0, 
                    'anterior' => $dadosAnterior->jk_venda ?? 0, 
                    'cor' => 'orange',

                    'pedidos_atual' => $dadosAtual->jk_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->jk_clientes ?? 0,

                    'pedidos_anterior' => $dadosAnterior->jk_pedidos ?? 0,
                    'clientes_anterior' => $dadosAnterior->jk_clientes ?? 0],

                [
                    'nome' => 'Rio de Janeiro', 
                    'atual' => $dadosAtual->rio_venda ?? 0, 
                    'anterior' => $dadosAnterior->rio_venda ?? 0, 
                    'cor' => 'green',

                    'pedidos_atual' => $dadosAtual->rio_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->rio_clientes ?? 0,

                    'pedidos_anterior' =>$dadosAnterior->rio_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->rio_clientes ?? 0],

                [
                    'nome' => 'Atacado', 
                    'atual' => $dadosAtual->atacado_venda ?? 0, 
                    'anterior' => $dadosAnterior->atacado_venda ?? 0, 
                    'cor' => 'pink',

                    'pedidos_atual' => $dadosAtual->atacado_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->atacado_clientes ?? 0,

                    'pedidos_anterior' =>$dadosAnterior->atacado_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->atacado_clientes ?? 0],

                [
                    'nome' => 'Ecommerce', 
                    'atual' => $dadosAtual->ecommerce_venda ?? 0, 
                    'anterior' => $dadosAnterior->ecommerce_venda ?? 0, 
                    'cor' => 'yellow',

                    'pedidos_atual' => $dadosAtual->ecommerce_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->ecommerce_clientes ?? 0,
                    
                    'pedidos_anterior' =>$dadosAnterior->ecommerce_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->ecommerce_clientes ?? 0],

                [
                    'nome' => 'Curitiba', 
                    'atual' => $dadosAtual->curitiba_venda ?? 0, 
                    'anterior' => $dadosAnterior->curitiba_venda ?? 0, 
                    'cor' => 'red',

                    'pedidos_atual' => $dadosAtual->curitiba_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->curitiba_clientes ?? 0,
                    
                    'pedidos_anterior' =>$dadosAnterior->curitiba_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->curitiba_clientes ?? 0],
            ];

            // =========================
            // DADOS DO GRÁFICO 
            // =========================
            $graficoVendasAtual = [
                'Alphaville'   => (float) ($dadosAtual->alphaville_venda ?? 0),
                'Syssa'        => (float) ($dadosAtual->syssa_venda ?? 0),
                'JK Iguatemi'  => (float) ($dadosAtual->jk_venda ?? 0),
                'Rio Janeiro'  => (float) ($dadosAtual->rio_venda ?? 0),
                'Atacado'      => (float) ($dadosAtual->atacado_venda ?? 0),
                'Ecommerce'    => (float) ($dadosAtual->ecommerce_venda ?? 0),
                'Curitiba'     => (float) ($dadosAtual->curitiba_venda ?? 0),
            ];


        } catch (\Exception $e) {
            $conexaoStatus = "Erro SQL: " . $e->getMessage();
            $lojas = [
                ['nome' => 'Alphaville', 'atual' => 0, 'anterior' => 0, 'cor' => 'blue', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'Syssa', 'atual' => 0, 'anterior' => 0, 'cor' => 'purple', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'JK Iguatemi', 'atual' => 0, 'anterior' => 0, 'cor' => 'orange', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'Rio de Janeiro', 'atual' => 0, 'anterior' => 0, 'cor' => 'green', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'Atacado', 'atual' => 0, 'anterior' => 0, 'cor' => 'pink', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'Ecommerce', 'atual' => 0, 'anterior' => 0, 'cor' => 'yellow', 'pedidos' => 0, 'clientes' => 0],
                ['nome' => 'Curitiba', 'atual' => 0, 'anterior' => 0, 'cor' => 'red', 'pedidos' => 0, 'clientes' => 0],
            ];
        }

        // =========================
        // SELECT DO  FINANCEIRO 
        // =========================
        $financeiro = DB::table('VW_RECEBIMENTOS')
            ->select([
                'forma',
                DB::raw('SUM(COALESCE(quantidade, 0)) as qtd'),
                DB::raw('SUM(COALESCE(vtotal, 0)) as valor'),
            ])
            ->whereBetween('emissao', [$inicioAtual, $fimAtual]) // usa o filtro ATUAL
            ->groupBy('forma')
            ->orderByDesc(DB::raw('SUM(COALESCE(vtotal, 0))'))
            ->get()
            ->map(function ($row) {
                return [
                    'forma' => $row->forma ?? 'Sem forma',
                    'qtd'   => (int) ($row->qtd ?? 0),
                    'valor' => (float) ($row->valor ?? 0),
                ];
            })
            ->toArray();


        // Valores para preencher os inputs (sempre definidos)
        $atual_inicio = $inicioAtual->toDateString();
        $atual_fim = $fimAtual->toDateString();

        $anterior_inicio = $inicioAnterior->toDateString();
        $anterior_fim = $fimAnterior->toDateString();

        return view('dashboard', compact(
            'lojas',
            'financeiro',
            'conexaoStatus',
            'atual_inicio',
            'atual_fim',
            'anterior_inicio',
            'anterior_fim',
            'graficoVendasAtual'
        ));
    }
}
