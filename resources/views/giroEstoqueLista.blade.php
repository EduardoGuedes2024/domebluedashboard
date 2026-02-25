@extends('layouts.app')

@section('title', 'Giro de Estoque - DomeBlue')

@section('content')

<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200 flex justify-between items-center">
    
    <div>
        <h1 class="text-2xl font-black text-slate-800 uppercase">{{ $unidade }} - {{ $periodo }} Dias</h1>
        <p class="text-slate-500 italic text-sm">Listagem de produtos com tempo de permanência identificado</p>
    </div>

    <a href="{{ route('giroEstoque') }}" class="btn-voltar bg-slate-500 text-black px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
        
        Voltar ao Gráfico
        <i class="fa-solid fa-arrow-right-to-bracket"></i>
    </a>
</header>

<main class="p-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($produtos as $p)
    <div class="bg-white border rounded-xl p-3 shadow-sm hover:shadow-md transition">
        
        {{-- Lógica de Imagem Dinâmica --}}
        @php
            // Usamos o refid_pai 
            $refid = $p->refid_pai ?? null;
            $codPai = (string)($p->cod_produto ?? '');
            
            // Verifica se o produto começa com 'SY' para trocar a URL
            $isSy = str_starts_with(strtolower($codPai), 'sy');
            
            $imgUrl = $refid ? ($isSy 
                ? "https://syssaoficial.com.br/imgitens/{$refid}_0.webp" 
                : "https://www.amissima.com.br/imgitens/{$refid}_0.webp") 
            : null;
        @endphp

        <div class="aspect-square bg-slate-100 rounded-lg overflow-hidden mb-3 flex items-center justify-center border border-slate-50 relative">
            @if($imgUrl)
                <img src="{{ $imgUrl }}" 
                     class="w-full h-full object-cover" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                {{-- Placeholder escondido que só aparece se o link da imagem falhar --}}
                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-[10px] text-gray-400 font-black uppercase tracking-tighter" style="display: none;">
                    SEM FOTO
                </div>
            @else
                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-[10px] text-gray-400 font-black uppercase tracking-tighter">
                    SEM FOTO
                </div>
            @endif

            {{-- Badge de Saldo sobre a foto --}}
            <div class="absolute top-2 right-2 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2 py-1 rounded-md">
                {{ (int)$p->saldo }} UN
            </div>
        </div>

        {{-- Detalhes do Produto --}}
        <div class="space-y-1">

            <span class="text-[12px] font-bold text-blue-600 uppercase">{{ $p->cod_produto_pai }}</span>

            <h4 class="text-[11px] font-bold text-slate-800 leading-tight uppercase truncate" title="{{ $p->des_produto }}">
                {{ mb_convert_encoding($p->des_produto ,'UTF-8', 'ISO-8859-1') }}
            </h4>
            <div class="pt-1 flex justify-between items-center border-t border-slate-50">
                <p class="text-[10px] text-slate-400 font-bold">PREÇO</p>
                <p class="text-xs font-black text-slate-900">R$ {{ number_format($p->preco, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>
@endforeach
</main>
@endsection