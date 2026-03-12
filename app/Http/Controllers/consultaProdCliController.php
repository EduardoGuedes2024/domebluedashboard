<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class consultaProdCliController extends Controller
{
    public function index(Request $request)
    {
        $codProdutoPai = trim($request->input('cod_produto_pai'));
        $statusPedido = $request->input('status_pedido', 'todos'); // Padrão: 'todos'
        $resultadosAgrupados = collect([]);
        $imgUrl = null;
        $descricaoProduto = null;

        if ($codProdutoPai) {
            $query = DB::table('pedidov')
                ->join('cadprod', 'pedidov.cod_produto', '=', 'cadprod.cod_produto')
                ->join('cadcli', 'pedidov.cod_cliente', '=', 'cadcli.cod_cliente')
                ->select(
                    'cadcli.raz_cliente',
                    'cadprod.cod_produto_pai',
                    'cadprod.des_produto',
                    'cadprod.des1_produto',
                    'pedidov.data_emissao',
                    'pedidov.quantidade',
                    'pedidov.flag_encerrado',
                    'pedidov.num_pedido'
                )
                ->where('cadprod.cod_produto_pai', $codProdutoPai)
                ->where('pedidov.flag_cancelado', 0); // Não traz pedidos cancelados

            // Aplica o filtro de status do pedido
            if ($statusPedido === 'aberto') {
                $query->where('pedidov.flag_encerrado', 'N');
            } elseif ($statusPedido === 'fechado') {
                $query->where('pedidov.flag_encerrado', 'S');
            }

            $resultados = $query->orderBy('pedidov.data_emissao', 'desc')->get();
            $resultadosAgrupados = $resultados->groupBy('raz_cliente');

            // Se houver resultados, busca a imagem e a descrição
            if ($resultadosAgrupados->isNotEmpty()) {
                $produtoInfo = DB::table('VW_SALDO_ESTOQUE')
                    ->select('refid_pai', 'des_produto')
                    ->where('cod_produto_pai', $codProdutoPai)
                    ->first();

                if ($produtoInfo) {
                    if (!empty($produtoInfo->refid_pai)) {
                        $foto = trim($produtoInfo->refid_pai);
                        $isSy = str_starts_with(strtolower($codProdutoPai), 'sy');
                        $imgUrl = $isSy
                            ? "https://syssaoficial.com.br/imgitens/{$foto}_0.webp"
                            : "https://www.amissima.com.br/imgitens/{$foto}_0.webp";
                    }
                    $descricaoProduto = $produtoInfo->des_produto;
                }
            }
        }

        return view('consulta_prodCli', [
            'resultadosAgrupados' => $resultadosAgrupados,
            'codProdutoPai' => $codProdutoPai,
            'statusPedido' => $statusPedido,
            'imgUrl' => $imgUrl,
            'descricaoProduto' => $descricaoProduto
        ]);
    }
}
