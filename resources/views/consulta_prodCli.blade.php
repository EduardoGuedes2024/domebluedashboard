@extends('layouts.app')

@section('title', 'Consulta Produto por Cliente - DomeBlue')

@section('content')

{{-- Header / Filtro --}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Consulta de Venda por Produto</h1>
            <p class="text-gray-500 text-sm">Veja quais clientes compraram um determinado produto.</p>
        </div>

        <form method="GET" action="{{ route('consulta_prodCli') }}" class="flex flex-wrap items-end justify-end gap-3">

            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código Pai do Produto</label>
                <input
                    type="text"
                    name="cod_produto_pai"
                    value="{{ $codProdutoPai ?? '' }}"
                    placeholder="Ex: A5433"
                    class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
                    required
                >
            </div>

            <div class="w-full sm:w-48">

                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Status do Pedido</label>
                <select name="status_pedido" class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="todos" @selected(($statusPedido ?? 'todos') == 'todos')>Todos</option>
                    <option value="aberto" @selected(($statusPedido ?? '') == 'aberto')>Abertos</option>
                    <option value="fechado" @selected(($statusPedido ?? '') == 'fechado')>Fechados</option>
                </select>

            </div>

            <div class="flex gap-2">

                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                @if($codProdutoPai)
                    <a href="{{ route('consulta_prodCli') }}"
                    class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition flex items-center">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>
</header>

{{-- Bloco da Imagem do Produto --}}
@if($codProdutoPai && !$resultadosAgrupados->isEmpty() && isset($imgUrl))
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6 flex items-center gap-6">
    <div class="w-32 h-40 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center border">
        <img src="{{ $imgUrl }}" class="object-contain w-full h-full" onerror="this.src='{{ asset('imagens/sem-foto.png') }}';">
    </div>
    <div>
        <h2 class="text-2xl font-black text-slate-800 uppercase">
            {{ $codProdutoPai }}
            @if(!empty($descricaoProduto))
                - {{ mb_convert_encoding($descricaoProduto, 'UTF-8', 'ISO-8859-1') }}
            @endif
        </h2>
        <p class="text-slate-500 uppercase font-bold text-sm">Resumo de Vendas por Cliente</p>
    </div>
</div>
@endif


@if(isset($resultadosAgrupados))

    @if($resultadosAgrupados->isEmpty())

        @if($codProdutoPai)

            <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-8 text-center mt-6">
                <i class="fa-solid fa-circle-exclamation text-amber-500 text-3xl mb-2"></i>
                <p class="text-amber-800 font-bold">Nenhuma venda encontrada para o produto "{{ $codProdutoPai }}".</p>
                <p class="text-amber-600 text-sm">Verifique o código ou se já houve vendas para este item.</p>
            </div>

        @else

            <div class="bg-white p-10 rounded-xl shadow-sm border border-blue-200 text-center mt-6">
                <i class="fa-solid fa-filter text-blue-300 text-5xl mb-4"></i>
                <h3 class="text-gray-700 font-bold text-xl">Pronto para pesquisar!</h3>
                <p class="text-blue-800 font-bold mt-2">Digite um código de produto para ver o histórico de vendas por cliente.</p>
            </div>

        @endif

    @else

        <div class="space-y-6 mt-6">
            @foreach($resultadosAgrupados as $clienteNome => $vendas)
                <section class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                    {{-- Cabeçalho do Card com nome do Cliente --}}
                    <div class="bg-slate-50 border-b p-4 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-slate-800">
                            <i class="fa-solid fa-user mr-2 text-blue-500"></i>
                            {{ mb_convert_encoding($clienteNome, 'UTF-8', 'ISO-8859-1') }}
                        </h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">{{ $vendas->pluck('num_pedido')->unique()->count() }} pedido(s) | Total: {{ (int)$vendas->sum('quantidade') }} peças</span>

                        
                    </div>

                    {{-- Tabela de compras para este cliente --}}
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-slate-500 border-b">
                                    <tr class="uppercase text-[11px] font-bold">
                                        <th class="text-left p-3">Cor / Tamanho</th>
                                        <th class="text-left p-3">Data Pedido</th>
                                        <th class="text-center p-3">Quantidade</th>
                                        <th class="text-center p-3">Pedido Aberto?</th>
                                        <th class="text-left p-3">Nº Pedido</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($vendas as $venda)
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="p-3 font-medium text-slate-700">{{ mb_convert_encoding($venda->des1_produto, 'UTF-8', 'ISO-8859-1') }} / {{ mb_convert_encoding($venda->des_produto, 'UTF-8', 'ISO-8859-1') }}</td>
                                            <td class="p-3 text-slate-600">{{ \Carbon\Carbon::parse($venda->data_emissao)->format('d/m/Y') }}</td>
                                            <td class="p-3 text-center font-bold text-slate-800">{{ (int)$venda->quantidade }}</td>
                                            <td class="p-3 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-black {{ $venda->flag_encerrado == 'N' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $venda->flag_encerrado == 'N' ? 'SIM' : 'NÃO' }}</span></td>
                                            <td class="p-3 text-slate-500">{{ $venda->num_pedido }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    @endif
@endif

@endsection