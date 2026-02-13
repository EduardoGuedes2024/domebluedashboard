<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EcommerceUfController extends Controller
{

    //// funcao gerar pdf \\\\\\
    public function exportPdf(Request $request)
    {
        $data_inicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $data_fim = $request->get('data_fim', now()->format('Y-m-d'));

        $vendasUf = DB::table('VW_VENDAS_ECOMMERCE_UF')
            ->select('entrega_uf', DB::raw('SUM(vtotal) as total_vendas'))
            ->whereBetween('data_nota', [$data_inicio . ' 00:00:00', $data_fim . ' 23:59:59'])
            ->groupBy('entrega_uf')
            ->orderBy('total_vendas', 'desc')
            ->get();

        $totalGeral = $vendasUf->sum('total_vendas');

        // Carrega a view pdf
        $pdf = Pdf::loadView('relatorios.ecommerce_Uf_pdf', [
            'vendasUf'    => $vendasUf,
            'totalGeral'  => $totalGeral,
            'data_inicio' => $data_inicio,
            'data_fim'    => $data_fim
        ])->setPaper('a4', 'portrait');
        
        return $pdf->download('Relatorio_Vendas_UF.pdf'); 
    }

    public function index(Request $request)
    {
        $data_inicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $data_fim = $request->get('data_fim', now()->format('Y-m-d'));

        // Busca os dados agrupados por UF
        $vendasUf = DB::table('VW_VENDAS_ECOMMERCE_UF')
            ->select('entrega_uf', DB::raw('SUM(vtotal) as total_vendas'))
            ->whereBetween('data_nota', [$data_inicio, $data_fim])
            ->groupBy('entrega_uf')
            ->orderBy('total_vendas', 'desc')
            ->get();

        return view('ecommerce_uf', compact('vendasUf', 'data_inicio', 'data_fim'));
    }
}