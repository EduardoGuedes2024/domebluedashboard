@extends('layouts.app')

@section('title', 'Estoque - DomeBlue')

@section('content')

@php
    $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');

    // $produto sempre existe (vem como "vazio" no controller)
    $p = $produto;

    // se encontrou produto real, vai usar o cod pai real no header
    $codPaiHeader = $p->cod_produto_pai ?: ($codigoPai ?: '—');

    // controle de estado visual
    $temBusca = (trim($codigoPai ?? '') !== '');
    $temResultado = (bool) ($encontrou ?? false);
@endphp

{{-- Header / Filtro --}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Estoque</h1>
            <p class="text-gray-500 text-sm">Consulta por produto e disponibilidade por loja</p>
        </div>

        <form method="GET" action="{{ route('estoque') }}" class="flex flex-col sm:flex-row gap-2 items-end">
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código Pai</label>
                <input
                    type="text"
                    name="codigo_pai"
                    value="{{ $codigoPai }}"
                    placeholder="Ex: A5201 ou SY0129"
                    class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
                >
            </div>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>

            @if($temBusca)
                <a href="{{ route('estoque') }}"
                   class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                    Limpar
                </a>
            @endif
        </form>
    </div>
</header>

{{-- CARD DO PRODUTO --}}
<section class="bg-blue-50 rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- Título do Card --}}
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-black text-gray-800">
            {{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}
        </h2>

        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-white">
            Cód. Pai: {{ $codPaiHeader }}
        </span>
    </div>

    <div class="p-6 space-y-6">

        {{-- Aviso se buscou e não encontrou --}}
        @if($temBusca && !$temResultado)
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-red-700 font-bold">
                    Nenhum produto encontrado para o Código Pai: {{ $codigoPai }}
                </p>
            </div>
        @endif

        {{-- PARTE 1: RESUMO DO PRODUTO --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

            {{-- FOTO --}}
            <?php

            $imgUrl = null; // começa vazio

                if($temResultado && $p->refid_pai){ // se tem resultado pega refid_pai

                    // Se for SY (Syssa)
                    if(str_starts_with(strtoupper($p->cod_produto_pai), 'SY')){
                        $imgUrl = "https://syssaoficial.com.br/imgitens/{$p->refid_pai}_0.webp";
                    }
                    // Senão é Amissima
                    else{
                        $imgUrl = "https://www.amissima.com.br/imgitens/{$p->refid_pai}_0.webp";
                    }

                }
            ?>

            <div class="md:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl p-3 h-full flex items-center justify-center overflow-hidden">
                    <img
                        src="{{ $imgUrl ?? asset('imagens/sem-foto.png') }}"
                        alt="{{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}"
                        class="w-full h-full object-contain max-h-[380px] rounded-lg"
                    />

                </div>
            </div>


            {{-- INFOS --}}
            <div class="md:col-span-10">
                <div class="bg-white border border-gray-200 rounded-xl p-5 h-full">
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500 font-bold uppercase">Descrição</p>
                        <p class="text-lg font-black text-gray-800">
                            {{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Cód:</span>
                            <span class="text-sm font-black text-slate-800">
                                {{ $p->refid_pai ?: '' }}
                            </span>
                        </div>
                    </div>

                    <div class="my-4 border-t border-gray-200"></div>

                    {{-- métricas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ Varejo</span>
                            <span class="text-sm font-black text-slate-800">
                                {{ $temResultado ? $brl($p->preco_01) : '' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ Atacado</span>
                            <span class="text-sm font-black text-slate-800">
                                {{ $temResultado ? $brl($p->preco_02) : '' }}
                            </span>
                        </div>

                       <!--- <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ </span>
                            <span class="text-sm font-black text-slate-800">
                                {{ $temResultado ? $brl($p->preco_03) : '' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$</span>
                            <span class="text-sm font-black text-slate-800">
                                {{ $temResultado ? ($p->preco_04 ?? '-') : '' }}
                            </span> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PARTE 2: MATRIZ DE ESTOQUE --}}

        {{-- MOBILE (cards) --}}
        <div class="md:hidden space-y-3">
            @forelse($matriz as $row)
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-black text-slate-800">
                            {{ $row['codigo'] }}
                            <span class="text-gray-400 font-medium">({{ $row['desc'] ?: '' }})</span>
                        </p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-800 text-white">Variação</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        @foreach($lojasFixas as $loja)
                            @php $qtd = (int)($row['lojas'][$loja] ?? 0); @endphp
                            <div class="bg-slate-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">{{ $loja }}</p>
                                <p class="font-black {{ $qtd==0 ? 'text-gray-400' : ($qtd<3 ? 'text-amber-600' : 'text-slate-800') }}">
                                    {{ $qtd }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                {{-- Layout completo mesmo vazio: mostra 1 card "placeholder" --}}
                <div class="bg-white border border-gray-200 rounded-xl p-4 opacity-70">
                    <div class="flex items-center justify-between">
                        <p class="font-black text-slate-800">— <span class="text-gray-400 font-medium">(—)</span></p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-800 text-white">Variação</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        @foreach($lojasFixas as $loja)
                            <div class="bg-slate-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-500 uppercase">{{ $loja }}</p>
                                <p class="font-black text-gray-400">0</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforelse

            {{-- TOTAL (mobile) --}}
            <div class="bg-slate-800 text-white border border-slate-900 rounded-xl p-4">
                <p class="font-black mb-3">TOTAL</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @foreach($lojasFixas as $loja)
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-[10px] font-bold uppercase opacity-80">{{ $loja }}</p>
                            <p class="font-black">{{ (int)($totaisPorLoja[$loja] ?? 0) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- DESKTOP (tabela) --}}
        <div class="hidden md:block bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-600 text-xs uppercase">
                            <th class="text-left px-4 py-3 font-black">Variação (Cód/Desc)</th>
                            @foreach($lojasFixas as $loja)
                                <th class="text-center px-4 py-3 font-black">{{ $loja }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($matriz as $row)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    {{ $row['codigo'] }}
                                    <span class="text-gray-400 font-medium">({{ $row['desc'] ?: '' }})</span>
                                </td>

                                @foreach($lojasFixas as $loja)
                                    @php $qtd = (int)($row['lojas'][$loja] ?? 0); @endphp
                                    <td class="text-center px-4 py-3 font-black {{ $qtd==0 ? 'text-gray-400' : ($qtd<3 ? 'text-amber-600' : 'text-slate-800') }}">
                                        {{ $qtd }}
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            {{-- mantém a tabela inteira, mas vazia --}}
                            <tr>
                                <td class="px-4 py-6 text-gray-500 font-bold">—</td>
                                @foreach($lojasFixas as $loja)
                                    <td class="text-center px-4 py-6 font-black text-gray-400">0</td>
                                @endforeach
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot class="bg-slate-800 text-white">
                        <tr>
                            <td class="px-4 py-3 font-black">TOTAL</td>
                            @foreach($lojasFixas as $loja)
                                <td class="text-center px-4 py-3 font-black">{{ (int)($totaisPorLoja[$loja] ?? 0) }}</td>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</section>

@endsection
