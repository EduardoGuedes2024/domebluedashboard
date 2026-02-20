@extends('layouts.app')

@section('title', 'Ranking de Vendas Ecommerce - DomeBlue')

@section('content')

@php
    $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');
@endphp

{{-- Overlay de Carregamento --}}
<div id="loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 9999; align-items: center; justify-content: center; cursor: wait;">
    <div class="flex flex-col items-center">
        <img src="{{ asset('imagens/loader.gif') }}" alt="Carregando..." class="w-12 h-12">
        <h3 class="text-blue-900 font-black mt-4">PROCESSANDO VENDAS Ecommerce</h3>
        <p class="text-slate-500 text-sm">Analisando performance exclusiva da unidade Ecommerce...</p>
    </div>
</div>

{{--- TITULOS E FILTROS ---}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Vendas - Ecommerce</h1>
            <p class="text-gray-500 text-sm">Ranking de produtos ordenados por volume de saída nesta loja</p>
        </div>

        <form method="GET" action="{{ route('vendas_Ecommerce') }}" id="formVendas" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-end gap-3">
            
            {{--- Filtro data Inicio---}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Início</label>
                <input type="date" name="data_inicio" value="{{ request('data_inicio', now()->startOfMonth()->format('Y-m-d')) }}" 
                       class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{--- Filtro data fim---}}
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Fim</label>
                <input type="date" name="data_fim" value="{{ request('data_fim', now()->format('Y-m-d')) }}" 
                       class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{--- Filtro Por marca ---}}
            <div class="w-full sm:w-48">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Marca</label>
                <select name="empresa" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="" @selected(request('empresa') == '')>Todas as Marcas</option>
                    <option value="Amissima" @selected(request('empresa') == 'Amissima')>Amissima</option>
                    <option value="Syssa" @selected(request('empresa') == 'Syssa')>Syssa</option>
                </select>
            </div>

            {{--- BOTÕES ---}}
            <div class="flex gap-2">

                {{--- Botao Filtrar---}}
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>

                {{--- Botao limpar---}}
                <a href="{{ route('vendas_Ecommerce') }}" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                    Limpar
                </a>

            </div>

        </form>

    </div>
</header>


{{--- Card Informativo tela incial---}}
@if(empty($cards))
    <div class="bg-white p-12 rounded-xl border border-blue-100 text-center shadow-sm">
        <i class="fa-solid fa-shop text-blue-200 text-6xl mb-4"></i>
        <h3 class="text-slate-700 font-black text-xl">Consulta por Loja</h3>
        <p class="text-blue-600 font-bold mt-2">Selecione o período para ver o que o Ecommerce mais vendeu.</p>
    </div>
@else

{{-- Cards de Resumo de Vendas total valor e qntd --}}
@if(!empty($cards))

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

    {{--- Card Valor TOTAL---}}
    <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-sm flex items-center gap-4">

        <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-dollar-sign"></i>
        </div>

        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Valor Total Geral ($)  Amissima + Syssa no Período</p>
            <p  class="text-[10px] font-black text-slate-400 uppercase tracking-widest">no Período filtrado</p>
            <h3 class="text-2xl font-black text-slate-800">{{ $brl($resumo->faturamento_total ?? 0) }}</h3>
        </div>

    </div>

    {{--- Card Qntd Total ---}}
    <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-sm flex items-center gap-4">

        <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-full flex items-center justify-center text-xl">
            <i class="fa-solid fa-box-open"></i>
        </div>

        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Peças</p>
            <h3 class="text-2xl font-black text-slate-800">{{ number_format($resumo->total_pecas ?? 0, 0, ',', '.') }} 
                <span class="text-sm font-bold text-slate-400">PEÇAS</span>
            </h3>
        </div>

    </div>

</div>

@endif

    <div class="space-y-6">
        @foreach($cards as $card)
            <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Cabeçalho do Card --}}
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="bg-slate-800 text-white text-[13px] font-black px-3 py-1 rounded-full uppercase">
                            Ref: {{ $card['produto_pai'] }}
                        </span>
                        <h2 class="text-lg font-black text-slate-800 uppercase">
                            {{ mb_convert_encoding($card['descricao'], 'UTF-8', 'ISO-8859-1') }}
                        </h2>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <span class="text-[12px] font-bold px-3 py-1 bg-white border border-gray-200 rounded-full">
                            VAREJO: <span class="text-slate-900 font-black">{{ $brl($card['preco_v'] ?? 0) }}</span>
                        </span>
                        <span class="text-[12px] font-bold px-3 py-1 bg-white border border-gray-200 rounded-full">
                            ATACADO: <span class="text-slate-900 font-black">{{ $brl($card['preco_a'] ?? 0) }}</span>
                        </span>
                        <span class="text-[12px] font-bold px-3 py-1 bg-blue-100 text-blue-800 rounded-full border border-blue-200">
                            {{ $card['grupo'] }} / {{ $card['subgrupo'] }}
                        </span>
                        <span class="text-[13px] font-black px-3 py-1 bg-slate-800 text-white rounded-full">
                            RANK Ecommerce: #{{ $loop->iteration + ($pagination->firstItem() - 1) }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        {{-- Coluna da Foto --}}
                        <div class="lg:col-span-2">
                            <div class="w-full h-[350px] bg-white border border-gray-200 rounded-xl overflow-hidden flex items-center justify-center">
                                @php
                                    $refid = $card['refid'] ?? null;
                                    $codPai = (string)($card['produto_pai'] ?? '');
                                    $isSy = str_starts_with(strtolower($codPai), 'sy');
                                    $imgUrl = $refid ? ($isSy ? "https://syssaoficial.com.br/imgitens/{$refid}_0.webp" : "https://www.amissima.com.br/imgitens/{$refid}_0.webp") : null;
                                @endphp

                                @if($imgUrl)
                                    <img src="{{ $imgUrl }}" class="w-full h-full object-cover rounded-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-gray-400 font-bold" style="display: none;">SEM FOTO</div>
                                @else
                                    <div class="w-full h-full bg-slate-100 flex items-center justify-center text-gray-400 font-bold">SEM FOTO</div>
                                @endif
                            </div>
                        </div>

                        {{-- Tabela Simplificada para Gerente --}}
                        <div class="lg:col-span-10">
                            <div class="border rounded-xl overflow-hidden bg-white">
                                <table class="w-full text-sm">
                                    <thead class="bg-slate-100 border-b">
                                        <tr class="text-slate-500 text-[10px] uppercase font-black">
                                            <th class="px-4 py-3 text-left">Variação (Cód/Desc)</th>
                                            <th class="px-4 py-3 text-center bg-blue-50 text-blue-700 font-black w-48">Vendas Realizadas no Ecommerce</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($card['matriz'] as $row)
                                            <tr class="hover:bg-blue-50/30 transition">
                                                <td class="px-4 py-3 font-bold text-slate-800">
                                                    {{ $row['codigo'] }}
                                                    <span class="text-slate-400 font-medium ml-1">
                                                        ({{ mb_convert_encoding($row['desc'] ?? '', 'UTF-8', 'ISO-8859-1') }})
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center font-black bg-blue-50 text-blue-700 text-base">
                                                    {{ $row['qtd'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-slate-800 text-white font-black text-xs">
                                        <tr>
                                            <td class="px-4 py-3">TOTAL ACUMULADO NESTE PRODUTO</td>
                                            <td class="text-center px-4 py-3 bg-blue-600 text-base">
                                                {{ $card['total_geral'] }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endforeach

        {{-- Paginação mantendo filtros --}}
        <div class="mt-8">
            {{ $pagination->appends(request()->all())->links() }}
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader-overlay');
        const form = document.getElementById('formVendas');

        form.addEventListener('submit', () => {
            loader.style.display = 'flex';
        });

        // Aplicar loader na navegação de páginas
        document.querySelectorAll('nav[role="navigation"] a').forEach(link => {
            link.addEventListener('click', () => {
                loader.style.display = 'flex';
            });
        });
    });
</script>
@endpush