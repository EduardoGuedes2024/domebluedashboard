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


        $graficoVendasAtual = [];

        
        try {
            DB::connection()->getPdo();
            $conexaoStatus = "Conectado ao SQL Server";

            $buscarDados = function ($ini, $fim) {
                return DB::table('dash_vendas')
                    ->select([
                        //ALPHAVILLE
                        DB::raw('SUM(ISNULL(alphaville, 0)) as alphaville_amissima'),
                        DB::raw('SUM(ISNULL(alphaville_syssa, 0)) as alphaville_syssa'),
                        DB::raw('SUM(ISNULL(alphaville_peca, 0)) as alphaville_peca'),
                        DB::raw('SUM(ISNULL(alphaville_pedidos, 0)) as alphaville_pedidos'),
                        DB::raw('SUM(ISNULL(alphaville_clientes, 0)) as alphaville_clientes'),

                        // SYSSA
                        DB::raw('SUM(ISNULL(syssa, 0)) as syssa_venda'),
                        DB::raw('SUM(ISNULL(syssa_peca, 0)) as syssa_peca'),
                        DB::raw('SUM(ISNULL(syssa_pedidos, 0)) as syssa_pedidos'),
                        DB::raw('SUM(ISNULL(syssa_clientes, 0)) as syssa_clientes'),

                        // JK
                        DB::raw('SUM(ISNULL(jk, 0)) as jk_amissima'),
                        DB::raw('SUM(ISNULL(jk_syssa, 0)) as jk_syssa'),
                        DB::raw('SUM(ISNULL(jk_peca, 0)) as jk_peca'),
                        DB::raw('SUM(ISNULL(jk_pedidos, 0)) as jk_pedidos'),
                        DB::raw('SUM(ISNULL(jk_clientes, 0)) as jk_clientes'),

                        // RIO
                        DB::raw('SUM(ISNULL(rio, 0)) as rio_amissima'),
                        DB::raw('SUM(ISNULL(rio_syssa, 0)) as rio_syssa'),
                        DB::raw('SUM(ISNULL(rio_peca, 0)) as rio_peca'),
                        DB::raw('SUM(ISNULL(rio_pedidos, 0)) as rio_pedidos'),
                        DB::raw('SUM(ISNULL(rio_clientes, 0)) as rio_clientes'),

                        // ATACADO
                        DB::raw('SUM(ISNULL(atacado, 0)) as atacado_amissima'),
                        DB::raw('SUM(ISNULL(atacado_syssa, 0)) as atacado_syssa'),
                        DB::raw('SUM(ISNULL(atacado_peca, 0)) as atacado_peca'),
                        DB::raw('SUM(ISNULL(atacado_pedidos, 0)) as atacado_pedidos'),
                        DB::raw('SUM(ISNULL(atacado_clientes, 0)) as atacado_clientes'),

                        // ECOMMERCE
                        DB::raw('SUM(ISNULL(ecommerce, 0)) as ecommerce_venda'),
                        DB::raw('SUM(ISNULL(ecommerce_peca, 0)) as ecommerce_peca'),
                        DB::raw('SUM(ISNULL(ecommerce_pedidos, 0)) as ecommerce_pedidos'),
                        DB::raw('SUM(ISNULL(ecommerce_clientes, 0)) as ecommerce_clientes'),

                        // CURITIBA
                        DB::raw('SUM(ISNULL(curitiba, 0)) as curitiba_amissima'),
                        DB::raw('SUM(ISNULL(curitiba_syssa, 0)) as curitiba_syssa'),
                        DB::raw('SUM(ISNULL(curitiba_peca, 0)) as curitiba_peca'),
                        DB::raw('SUM(ISNULL(curitiba_pedidos, 0)) as curitiba_pedidos'),
                        DB::raw('SUM(ISNULL(curitiba_clientes, 0)) as curitiba_clientes'),
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
                    'cor' => 'blue',

                    // ATUAL
                    'amissima_atual' => $dadosAtual->alphaville_amissima ?? 0,
                    'syssa_atual'    => $dadosAtual->alphaville_syssa ?? 0,
                    'total_atual'    => ($dadosAtual->alphaville_amissima ?? 0) + ($dadosAtual->alphaville_syssa ?? 0),
                    'pecas_atual'    => $dadosAtual->alphaville_peca ?? 0,
                    'pedidos_atual'  => $dadosAtual->alphaville_pedidos ?? 0,
                    'clientes_atual' => $dadosAtual->alphaville_clientes ?? 0,
                    // ANTERIOR
                    'amissima_anterior'   => $dadosAnterior->alphaville_amissima ?? 0,
                    'syssa_anterior'      => $dadosAnterior->alphaville_syssa ?? 0,
                    'total_anterior'      => ($dadosAnterior->alphaville_amissima ?? 0) + ($dadosAnterior->alphaville_syssa ?? 0),
                    'pecas_anterior'      => $dadosAnterior->alphaville_peca ?? 0,
                    'pedidos_anterior'    => $dadosAnterior->alphaville_pedidos ?? 0,
                    'clientes_anterior'   => $dadosAnterior->alphaville_clientes ?? 0,
                ],

                [
                    'nome' => 'Ecomm Syssa', 
                    'cor' => 'purple',
                    
                    // ATUAL
                    'total_atual'     => $dadosAtual->syssa_venda ?? 0, 
                    'pecas_atual'     => $dadosAtual->syssa_peca ?? 0,
                    'pedidos_atual'   => $dadosAtual->syssa_pedidos ?? 0,
                    'clientes_atual'  => $dadosAtual->syssa_clientes ?? 0,

                    // ANTERIOR
                    'total_anterior'     => $dadosAnterior->syssa_venda ?? 0,
                    'pecas_anterior'     => $dadosAnterior->syssa_peca ?? 0,
                    'pedidos_anterior'   => $dadosAnterior->syssa_pedidos ?? 0,
                    'clientes_anterior'  => $dadosAnterior->syssa_clientes ?? 0
                ],

                [
                    'nome' => 'JK Iguatemi', 
                    'cor' => 'orange',

                    // ATUAL
                    'amissima_atual' => $dadosAtual->jk_amissima ?? 0, 
                    'syssa_atual' => $dadosAtual->jk_syssa ?? 0,
                    'total_atual' => ($dadosAtual->jk_amissima ?? 0) + ($dadosAtual->jk_syssa ?? 0),
                    'pecas_atual' => $dadosAtual->jk_peca ?? 0,
                    'pedidos_atual' => $dadosAtual->jk_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->jk_clientes ?? 0,
                    
                    // ANTERIOR
                    'amissima_anterior' => $dadosAnterior->jk_amissima ?? 0, 
                    'syssa_anterior' => $dadosAnterior->jk_syssa ?? 0,
                    'total_anterior' => ($dadosAnterior->jk_amissima ?? 0) + ($dadosAnterior->jk_syssa ?? 0),
                    'pecas_anterior' => $dadosAnterior->jk_peca ?? 0,
                    'pedidos_anterior' => $dadosAnterior->jk_pedidos ?? 0,
                    'clientes_anterior' => $dadosAnterior->jk_clientes ?? 0
                ],

                [
                    'nome' => 'Rio de Janeiro', 
                    'cor' => 'green',

                    //ATUAL
                    'amissima_atual' => $dadosAtual->rio_amissima ?? 0, 
                    'syssa_atual' => $dadosAtual->rio_syssa ?? 0,
                    'total_atual' => ($dadosAtual->rio_amissima ?? 0) + ($dadosAtual->rio_syssa ?? 0),
                    'pecas_atual' => $dadosAtual->rio_peca ?? 0,
                    'pedidos_atual' => $dadosAtual->rio_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->rio_clientes ?? 0,

                    //ANTERIOR
                    'amissima_anterior' => $dadosAnterior->rio_amissima ?? 0, 
                    'syssa_anterior' => $dadosAnterior->rio_syssa ?? 0,
                    'total_anterior' => ($dadosAnterior->rio_amissima ?? 0) + ($dadosAnterior->rio_syssa ?? 0),
                    'pecas_anterior' => $dadosAnterior->rio_peca ?? 0,
                    'pedidos_anterior' =>$dadosAnterior->rio_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->rio_clientes ?? 0,
                ],

                [
                    'nome' => 'Atacado', 
                    'cor' => 'pink',

                    //ATUAL
                    'amissima_atual' => $dadosAtual->atacado_amissima ?? 0, 
                    'syssa_atual' => $dadosAtual->atacado_syssa ?? 0,
                    'total_atual' => ($dadosAtual->atacado_amissima ?? 0) + ($dadosAtual->atacado_syssa ?? 0),
                    'pecas_atual' => $dadosAtual->atacado_peca ?? 0,
                    'pedidos_atual' => $dadosAtual->atacado_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->atacado_clientes ?? 0,
                    
                    
                    //ANTERIOR
                    'amissima_anterior' => $dadosAnterior->atacado_amissima ?? 0, 
                    'syssa_anterior' => $dadosAnterior->atacado_syssa ?? 0,
                    'total_anterior' => ($dadosAnterior->atacado_amissima ?? 0) + ($dadosAnterior->atacado_syssa ?? 0),
                    'pecas_anterior' => $dadosAnterior->atacado_peca ?? 0,
                    'pedidos_anterior' =>$dadosAnterior->atacado_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->atacado_clientes ?? 0,
                ],

                [
                    'nome' => 'Ecomm Amissima', 
                    'cor' => 'yellow',

                     
                    'total_atual' => ($dadosAtual->ecommerce_venda ?? 0),
                    'pecas_atual' => $dadosAtual->ecommerce_peca ?? 0,
                    'pedidos_atual' => $dadosAtual->ecommerce_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->ecommerce_clientes ?? 0,
                    
                    //ANTERIOR
                    'total_anterior' => ($dadosAnterior->ecommerce_venda ?? 0),
                    'pecas_anterior' => $dadosAnterior->ecommerce_peca ?? 0,
                    'pedidos_anterior' =>$dadosAnterior->ecommerce_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->ecommerce_clientes ?? 0,
                ],

                [
                    'nome' => 'Curitiba', 
                    'cor' => 'red',
                    
                    //ATUAL
                    'amissima_atual' => $dadosAtual->curitiba_amissima ?? 0, 
                    'syssa_atual' => $dadosAtual->curitiba_syssa ?? 0,
                    'total_atual' => ($dadosAtual->curitiba_amissima ?? 0) + ($dadosAtual->curitiba_syssa ?? 0),
                    'pecas_atual' => $dadosAtual->curitiba_peca ?? 0,
                    'pedidos_atual' => $dadosAtual->curitiba_pedidos ?? 0, 
                    'clientes_atual' => $dadosAtual->curitiba_clientes ?? 0,
                    
                    //ANTERIOR
                    'amissima_anterior' => $dadosAnterior->curitiba_amissima ?? 0, 
                    'syssa_anterior' => $dadosAnterior->curitiba_syssa ?? 0,
                    'total_anterior' => ($dadosAnterior->curitiba_amissima ?? 0) + ($dadosAnterior->curitiba_syssa ?? 0),
                    'pecas_anterior' => $dadosAnterior->curitiba_peca ?? 0,
                    'pedidos_anterior' =>$dadosAnterior->curitiba_pedidos ?? 0,
                    'clientes_anterior' =>$dadosAnterior->curitiba_clientes ?? 0,
                ],
            ];

            // =========================
            // DADOS DO GRÁFICO 
            // =========================
            $graficoVendasAtual = [
                'Alphaville'      => (float) ($dadosAtual->alphaville_amissima ?? 0) + (float) ($dadosAtual->alphaville_syssa ?? 0),
                'Ecomm Syssa'     => (float) ($dadosAtual->syssa_venda ?? 0),
                'JK Iguatemi'     => (float) ($dadosAtual->jk_amissima ?? 0) + (float) ($dadosAtual->jk_syssa ?? 0),
                'Rio de Janeiro'     => (float) ($dadosAtual->rio_amissima ?? 0) + (float) ($dadosAtual->rio_syssa ?? 0),
                'Atacado'         => (float) ($dadosAtual->atacado_amissima ?? 0) + (float) ($dadosAtual->atacado_syssa ?? 0),
                'Ecomm Amissima'   => (float) ($dadosAtual->ecommerce_venda ?? 0),
                'Curitiba'        => (float) ($dadosAtual->curitiba_amissima ?? 0) + (float) ($dadosAtual->curitiba_syssa ?? 0),
            ];
            
            $grafico=[];
            foreach($graficoVendasAtual as $loja => $valor){
                $lojalimpo = mb_convert_encoding($loja, 'UTF-8', 'ISO-8859-1');
                $grafico[$lojalimpo] = $valor;
                };
                 

        } catch (\Exception $e) {
            $conexaoStatus = "Erro SQL: " . $e->getMessage();
            $lojas = [
                ['nome' => 'Alphaville', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'blue', 'pedidos_atual' => 0, 'clientes_atual' => 0, 'pedidos_anterior' => 0, 'clientes_anterior' => 0],
                ['nome' => 'Ecomm Syssa', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'purple', 'pedidos_atual' => 0, 'clientes_atual' => 0, 'pedidos_anterior' => 0, 'clientes_anterior' => 0],
                ['nome' => 'JK Iguatemi', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'orange', 'pedidos_atual' => 0, 'clientes_atual' => 0, 'pedidos_anterior' => 0, 'clientes_anterior' => 0],
                ['nome' => 'Rio de Janeiro', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'green', 'pedidos_atual' => 0, 'clientes_atual' => 0, 'pedidos_anterior' => 0, 'clientes_anterior' => 0],
                ['nome' => 'Atacado', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'pink', 'pedidos_atual' => 0, 'clientes_atual'=> 0,'pedidos_anterior'=> 0,'clientes_anterior'=> 0],
                ['nome' =>'Ecomm Amissima', 'total_atual'=> 0,'total_anterior'=> 0,'cor'=>'yellow','pedidos_atual'=> 0,'clientes_atual'=> 0,'pedidos_anterior'=> 0,'clientes_anterior'=>0],
                ['nome' => 'Curitiba', 'total_atual' => 0, 'total_anterior' => 0, 'cor' => 'red', 'pedidos_atual' => 0, 'clientes_atual' => 0, 'pedidos_anterior' => 0, 'clientes_anterior' => 0],
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
