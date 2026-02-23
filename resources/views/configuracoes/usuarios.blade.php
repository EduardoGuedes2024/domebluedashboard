@extends('layouts.app')

@section('title', 'Configurações de Usuários - DomeBlue')

@section('content')


<header class="mb-8 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestão de Acessos e Usuários</h1>
            <p class="text-gray-500 text-sm">Controle as permissões de acesso e sincronização de senhas entre ERP e Dashboard</p>
        </div>
    </div>
</header>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col" style="max-height: calc(100vh - 200px);">
    
    <div class="overflow-x-auto overflow-y-auto custom-scrollbar w-full">

        <table class="w-full text-left border-collapse min-w-[1500px]">

            <thead class="sticky top-0 z-10 bg-blue-50">

                <tr class="bg-blue-50 text-gray-600 text-xs uppercase font-bold">
                    <th class="p-4 border-b">Usuário (ERP)</th>
                    <th class="p-4 border-b text-center">Dashboard vendas</th>
                    <th class="p-4 border-b text-center">Dashboard ecomm</th>
                    <th class="p-4 border-b text-center">Vendas JK</th>
                    <th class="p-4 border-b text-center">Vendas Alphaville</th>
                    <th class="p-4 border-b text-center">Vendas Rio</th>
                    <th class="p-4 border-b text-center">Vendas Atacado</th>
                    <th class="p-4 border-b text-center">Vendas Ecommerce</th>
                    <th class="p-4 border-b text-center">Vendas Curitiba</th>
                    <th class="p-4 border-b text-center">consulta Prod</th>
                    <th class="p-4 border-b text-center">Relatorio Lojas</th>
                    <th class="p-4 border-b text-center">Movimentação</th>
                    <th class="p-4 border-b text-center">Consulta Clientes</th>
                    <th class="p-4 border-b text-center">Config</th>
                    <th class="p-4 border-b">Nova Senha</th>
                    <th class="p-4 border-b text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @foreach($usuarios as $user)
                <tr class="border-b hover:bg-gray-50 transition">
                    <form action="{{ route('users.update', $user->codigo_acesso) }}" method="POST">
                        @csrf
                        <td class="p-4">
                            <span class="font-bold block text-gray-800">{{ $user->nome }}</span>
                            <span class="text-xs text-gray-400">ID: {{ $user->codigo_acesso }}</span>
                        </td>
                        
                        {{-- FLAGS DE ACESSO (1 ou 0) --}}

                        {{----Dash's----}}
                        <td class="p-4 text-center">
                            <input type="checkbox" name="domeblue" value="1" {{ (int)$user->domeblue === 1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                        </td>

                        <td class="p-4 text-center">
                            <input type="checkbox" name="ecommerce_uf" value="1" {{ (int)$user->ecommerce_uf === 1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                        </td>

                        
                        {{----Vendas----}}
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_jk" value="1" {{ (int)$user->vendas_jk === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_alphaville" value="1" {{ (int)$user->vendas_alphaville === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_rio" value="1" {{ (int)$user->vendas_rio === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_atacado" value="1" {{ (int)$user->vendas_atacado === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_ecommerce" value="1" {{ (int)$user->vendas_ecommerce === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>
                        <td class="p-4 text-center">
                            <input type="checkbox" name="vendas_curitiba" value="1" {{ (int)$user->vendas_curitiba === 1 ? 'checked' : '' }} class="w-4 h-4 text-orange-500">
                        </td>


                        {{---estoques---}}
                        <td class="p-4 text-center">
                            <input type="checkbox" name="domeblue_estoque" value="1" {{ (int)$user->domeblue_estoque === 1 ? 'checked' : '' }} class="w-4 h-4 text-blue-600">
                        </td>

                        <td class="p-4 text-center">
                            <input type="checkbox" name="relatorios_lojas" value="1" {{ (int)$user->relatorios_lojas === 1 ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                        </td>

                        <td class="p-4 text-center">
                            <input type="checkbox" name="movimento_estoque" value="1" {{ (int)($user->movimento_estoque ?? 0) === 1 ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                        </td>

                        {{---clientes---}}
                        <td class="p-4 text-center">
                            <input type="checkbox" name="clientes_ativos" value="1" {{ (int)($user->clientes_ativos ?? 0) === 1 ? 'checked' : '' }} class="w-4 h-4 text-purple-600">
                        </td>

                        {{---configuracoes---}}

                        <td class="p-4 text-center">
                            <input type="checkbox" name="configuracoes" value="1" {{ (int)($user->configuracoes ?? 0) === 1 ? 'checked' : '' }} class="w-4 h-4 text-red-600">
                        </td>

                        {{-- CAMPO DE SENHA --}}
                        <td class="p-4">
                            <input type="password" name="nova_senha" placeholder="Alterar senha..." 
                                   class="border rounded-lg p-1 text-xs w-32 focus:ring-2 focus:ring-blue-500 outline-none">
                        </td>

                        <td class="p-4 text-center">
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-slate-900 transition">
                                <i class="fas fa-save"></i> Salvar
                            </button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection