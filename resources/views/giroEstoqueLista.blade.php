@extends('layouts.app')

@section('title', 'Giro de Estoque - DomeBlue')

@section('content')

<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200 flex flex-wrap justify-between items-center gap-4">
    
    <div class="flex items-center gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase">{{ strtoupper($unidade) }} - {{ $periodo }} Dias</h1>
            <p class="text-slate-500 italic text-sm">Produtos identificados nesta faixa de permanência</p>
        </div>

        {{-- Mini Cards de Resumo da Lista --}}
        <div class="hidden lg:flex gap-3 ml-6 border-l pl-6 border-blue-200">
            <div class="bg-white px-4 py-2 rounded-lg border border-blue-100 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Peças no período</p>
                <p class="text-lg font-black text-blue-600">{{ number_format($totalPecas, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg border border-blue-100 shadow-sm">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Valor Estimado Acumulado no Período</p>
                <p class="text-lg font-black text-green-600">R$ {{ number_format($totalValor, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="flex gap-2">
        <a href="javascript:void(0)" id="btnPdf"
        class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
            <i class="fa-solid fa-download"></i> PDF
        </a>

        <a href="{{ route('giroEstoque', ['loja' => $unidade, 'grupo' => request('grupo')]) }}" class="bg-blue-800 text-white px-5 py-2 rounded-lg font-bold hover:bg-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao Gráfico
        </a>
    </div>
</header>




<main class="p-6 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
    @foreach($produtos as $p)
    <div class="bg-white border rounded-xl p-3 shadow-sm hover:shadow-md transition">
        
        {{-- Lógica de Imagem Dinâmica --}}
        @php
            // Usamos o refid_pai 
            $refid = $p->refid_pai ?? '';
            $codPai = (string)($p->cod_produto_pai ?? '');
            
            // Verifica se o produto começa com 'SY' para trocar a URL
            $isSy = str_starts_with(strtolower($codPai), 'sy');
            
            $imgUrl = $refid ? ($isSy 
                ? "https://syssaoficial.com.br/imgitens/{$refid}_0.webp" 
                : "https://www.amissima.com.br/imgitens/{$refid}_0.webp") 
            : null;
        @endphp

        <div class="aspect-[3/4] bg-slate-100 rounded-lg overflow-hidden mb-3 flex items-center justify-center border border-slate-50 relative">
            @if($imgUrl)
                <img src="{{ $imgUrl }}" 
                     class="w-full h-full object-cover" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                {{-- Placeholder escondido que só aparece se o link da imagem falhar --}}
                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-[12px] text-gray-400 font-black uppercase tracking-tighter" style="display: none;">
                    SEM FOTO
                </div>
            @else
                <div class="w-full h-full bg-slate-100 flex items-center justify-center text-[12px] text-gray-400 font-black uppercase tracking-tighter">
                    SEM FOTO
                </div>
            @endif

            
        </div>

        {{-- Detalhes do Produto --}}
        <div class="space-y-1">

            <div class="pt-1 flex justify-between items-center border-t border-slate-50">
                <span class="text-[14px] font-bold text-blue-500 uppercase">{{ $p->cod_produto_pai }}</span>
                <span class="text-[13px] font-bold text-blue-500 uppercase">{{$p->cod_produto}}</span>
            </div>
            

            <h4 class="text-[13px] font-bold text-slate-900 leading-tight uppercase truncate" title="{{ $p->des_produto }}">
                {{ mb_convert_encoding($p->des_produto ,'UTF-8', 'ISO-8859-1') }}
            </h4>

            <h4 class="text-[13px] font-bold text-slate-900 leading-tight uppercase truncate" title="{{ $p->des_produto }}">
                {{ mb_convert_encoding($p->des1_produto ,'UTF-8', 'ISO-8859-1') }}
            </h4>

            <div class="pt-1 flex justify-between items-center border-t border-slate-50">
                <p class="text-[12px] text-slate-900 font-bold">PREÇO</p>
                <p class="text-sm font-black text-slate-900">R$ {{ number_format($p->preco, 2, ',', '.') }}</p>
            </div>

            {{-- --}}
            <div class="pt-1 flex justify-between items-center border-t border-slate-50">
                <p class="text-[12px] text-slate-900 font-bold">SALDO ATUAL</p>
                <p class="text-sm font-black text-slate-900">{{ (int)$p->saldo }} UN</p>
            </div>

        </div>
    </div>
@endforeach
</main>
@endsection

@push('scripts')
<script>
    document.getElementById('btnPdf').addEventListener('click', function() {
    // Captura os parâmetros atuais da URL (loja, periodo, grupo)
    const urlParams = new URLSearchParams(window.location.search);
    
    // Monta a URL para a rota de geração de PDF
    const pdfUrl = "{{ route('giroEstoque.pdf') }}?" + urlParams.toString();
    
    // Abre o PDF em uma nova aba para não tirar o usuário da listagem
    window.open(pdfUrl, '_blank');
});
</script>
@endpush