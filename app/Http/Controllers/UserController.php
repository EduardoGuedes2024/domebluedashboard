<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index() 
    {
        $usuarios = DB::table('operador')->orderBy('nome', 'asc')->get();
        return view('configuracoes.usuarios', compact('usuarios'));
    }

    public function update(Request $request, $id) {

        $query = DB::table('operador')
        ->whereRaw("CAST(codigo_acesso AS VARCHAR(50)) = ?", [(string)$id]);


        $dados = [
            // Permissões dashboard
            'domeblue'    => $request->has('domeblue') ? 1 : 0,
            'ecommerce_uf' => $request->has('ecommerce_uf') ? 1 : 0,

            // Permissões vendas
            'vendas_jk' => $request->has('vendas_jk') ? 1 : 0,
            'vendas_alphaville' => $request->has('vendas_alphaville') ? 1 : 0,
            'vendas_rio' => $request->has('vendas_rio') ? 1 : 0,
            'vendas_atacado' => $request->has('vendas_atacado') ? 1 : 0,
            'vendas_ecommerce' => $request->has('vendas_ecommerce') ? 1 : 0,
            'vendas_curitiba' => $request->has('vendas_curitiba') ? 1 : 0,
            'vendas_showroom' => $request->has('vendas_showroom') ? 1 : 0,

            //permissões estoque
            'domeblue_estoque' => $request->has('domeblue_estoque') ? 1 : 0,
            'relatorios_lojas' => $request->has('relatorios_lojas') ? 1 : 0,
            'movimento_estoque' => $request->has('movimento_estoque') ? 1 : 0,
            'consulta_prod_cli' => $request->has('consulta_prod_cli') ? 1 : 0,
            'giro_estoque'     => $request->has('giro_estoque') ? 1 : 0,

            //permissões clientes
            'clientes_ativos' => $request->has('clientes_ativos') ? 1 : 0,


            //permissão configurações
            'configuracoes' => $request->has('configuracoes') ? 1 : 0,
        ];

        if ($request->filled('nova_senha')) {
            $dados['senha'] = $request->nova_senha;
            $dados['senha_hash'] = Hash::make($request->nova_senha);
        }

        $query->update($dados);

        return back()->with('success', 'Usuário atualizado com sucesso!');
    }
}
