<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class MovimentacaoEstoqueController extends Controller
{

    //// funcao gerar pdf \\\\\\
    public function exportPdf(Request $request)
    {
        $codigo = trim($request->get('cod_produto'));
        $localId = (int)$request->get('local');

        // Pegamos exatamente a mesma query do index
        $movimentos = DB::table('movimento_estoque')
            ->join('cadprod', 'movimento_estoque.cod_produto', '=', 'cadprod.cod_produto')
            ->select('movimento_estoque.*', 
            'cadprod.cod_produto_pai', 
            'cadprod.des_produto',
            'des1_produto'
            )
            ->where('cadprod.cod_produto_pai', 'LIKE', $codigo . '%')
            ->where('movimento_estoque.local', $localId)
            ->orderBy('movimento_estoque.data_responsavel', 'ASC')
            ->orderBy('movimento_estoque.hora_responsavel', 'ASC')
            ->get();

        $grupos = $movimentos->map(function($item) {
            $item->cod_produto = trim($item->cod_produto);
            return $item;
        })->groupBy('cod_produto');

        $lojas = [
            8 => 'Alphaville', 
            5 => 'JK', 
            15 => 'RJ', 
            2 => 'Atacado', 
            11 => 'Ecommerce', 
            18 => 'Curitiba'
            ];

        $nomeLoja = $lojas[$localId] ?? 'Nao identificada';

        $pdf = Pdf::loadView('relatorios.movimentacao_pdf', [
            'grupos' => $grupos,
            'nomeLoja' => $nomeLoja,
            'codigoPai' => $codigo
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Movimentacao_{$codigo}_{$nomeLoja}.pdf");
    }


    ///// funçao principal banco de dados \\\\\\\
    public function index(Request $request)
    {
        $gruposPorTamanho = null;
        
        // Lista de lojas sem a opção "Todas"
        $locais = [
            8  => 'Alphaville', 
            5  => 'JK', 
            15 => 'RJ', 
            2  => 'Atacado', 
            11 => 'Ecommerce', 
            18 => 'Curitiba'
        ];

        if ($request->filled('cod_produto')) {
            $codigo = trim($request->get('cod_produto'));
            
            // Se não vier local no request, definimos um padrão (ex: Alphaville - 8)
            $localSelecionado = $request->get('local', 8);

            $movimentos = DB::table('movimento_estoque')
                ->join('cadprod', 'movimento_estoque.cod_produto', '=', 'cadprod.cod_produto')
                ->select(
                    'movimento_estoque.*', 
                    'cadprod.cod_produto_pai', 
                    'cadprod.des_produto',
                    'des1_produto'
                )
                ->where('cadprod.cod_produto_pai', 'LIKE', $codigo . '%')
                // Filtro agora é direto pelo local selecionado
                ->where('movimento_estoque.local', (int)$localSelecionado)
                ->orderBy('movimento_estoque.data_responsavel', 'ASC')
                ->orderBy('movimento_estoque.hora_responsavel', 'ASC')
                ->get();

            $gruposPorTamanho = $movimentos->map(function($item) {
                $item->cod_produto = trim($item->cod_produto);
                return $item;
            })->groupBy('cod_produto'); 
        }

        return view('movimentacao_estoque', [
            'gruposPorTamanho' => $gruposPorTamanho,
            'locais'           => $locais
        ]);
    }
}