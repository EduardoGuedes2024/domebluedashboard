@extends('layouts.app')

@section('title', 'Estoque por Lojas - DomeBlue')

@section('content')

@php
    $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');

    
    // local 0 = todas
    $opcoes = [
        0  => 'Todas as Lojas',
        8  => 'Alphaville',
        5  => 'JK',
        15 => 'RJ',
        2  => 'Atacado',
        11 => 'Ecommerce',
        18 => 'Curitiba',
    ];
@endphp

     {{---//// LOADER \\\\\--}}
<div id="loader-overlay" style="display: none; position: fixed; inset: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.9); z-index: 999999; display: none; align-items: center; justify-content: center; cursor: wait;">
    
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
        <img src="{{ asset('imagens/loader.gif') }}" alt="Carregando..." style="width: 50px; height: 50px;">
        
        <div style="text-align: center; margin-top: 20px;">
            <h3 style="font-family: sans-serif; color: #1e3a8a; font-weight: 900; margin: 0;">
                PROCESSANDO RELATÓRIO
            </h3>
            <p style="font-family: sans-serif; color: #64748b; font-size: 14px; margin-top: 5px;">
                Aguarde, estamos organizando o estoque e imagens...
            </p>
        </div>
    </div>
</div>

   {{-- ////////// Header / Filtro \\\\\\\\\\\\--}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Relatório por Lojas</h1>
            <p class="text-gray-500 text-sm">Lista produtos </p>
        </div>

        <form method="GET" action="{{ route('estoque_lojas') }}" class="flex flex-col sm:flex-row gap-2 items-end">

            {{-- filtro local estoque --}}
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Loja</label>

                <select name="local"
                    class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                    @foreach($opcoes as $k => $nome)
                        <option value="{{ $k }}" @selected((int)$local === (int)$k)>{{ $nome }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Filtro de Grupo --}}
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Grupo (Peça)</label>
                <select name="grupo" class="w-full border rounded-lg p-2 text-sm">
                    <option value="">Todos os Grupos</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g }}" @selected(request('grupo') == $g)>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro de Subgrupo --}}
            <div class="w-full sm:w-48">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Subgrupo (Tecido)</label>
                <select name="subgrupo" class="w-full border rounded-lg p-2 text-sm">
                    <option value="">Todos os Tecidos</option>
                    @foreach($subgrupos as $sg)
                        <option value="{{ $sg }}" @selected(request('subgrupo') == $sg)>{{ $sg }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- botao buscar--}}
            <button type="submit" id="btnBuscar"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>
            
            {{-- botao limpar--}}
            <a href="{{ route('estoque_lojas') }}"
                class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                Limpar
            </a>

            {{-- botao pdf--}}
            <a href="javascript:void(0)" id="btnPdf"
                class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fa-solid fa-download"></i> PDF
            </a>
        </form>
    </div>
</header>


{{-- ///// MONTAGEM DOS CARDS  \\\\\\\--}}

@if(empty($cards))

    {{-- MENSAGEM QUANDO NÃO HÁ RESULTADOS APÓS UMA BUSCA --}}
    @if(request()->query() != [])
        <section class="bg-blue-50 rounded-xl shadow-sm border border-gray-200 p-8 text-center mt-6">
            <p class="text-gray-600 font-bold">Nenhum produto com saldo encontrado para esse filtro.</p>
        </section>
    @else
        {{-- MENSAGEM INICIAL: Aparece no primeiro acesso--}}
        <div class="bg-white p-10 rounded-xl shadow-sm border border-blue-200 text-center mt-6">
            <i class="fa-solid fa-filter text-blue-300 text-5xl mb-4"></i>
            <h3 class="text-gray-700 font-bold text-xl">Pronto para pesquisar!</h3>
            <p class="text-blue-800 font-bold mt-2">Utilize os filtros acima para iniciar sua consulta.</p>
        </div>
    @endif
@else

    <div class="space-y-6">

        @foreach($cards as $card)
            @php
                $p = $card['produto'];

                // amissima por padrão e syssa se o cod_pai começar com "sy"
                $codPai = (string)($p->cod_produto_pai ?? '');
                $refid  = $p->refid_pai ?? null;

                $isSy = str_starts_with(strtolower($codPai), 'sy');

                // monta URL para pega imagem 
                $img = null;
                if ($refid) {
                    $img = $isSy
                        ? "https://syssaoficial.com.br/imgitens/{$refid}_0.webp"
                        : "https://www.amissima.com.br/imgitens/{$refid}_0.webp";
                }
            @endphp

            <section class="bg-blue-50 rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Cabeçalho do card --}}
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                    <div class="flex items-center gap-3">

                        <span class="text-xs font-black px-3 py-1 rounded-full bg-slate-800 text-white">
                            Ref: {{ $p->refid_pai ?? '—' }}
                        </span>

                        <h2 class="text-lg font-black text-gray-800">
                            {{ mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1') }}
                        </h2>

                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-white border border-gray-200">
                            Varejo: <span class="font-black text-slate-800">{{ $brl($p->preco_01) }}</span>
                        </span>

                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-white border border-gray-200">
                            Atacado: <span class="font-black text-slate-800">{{ $brl($p->preco_02) }}</span>
                        </span>

                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-white">
                            Cód. Pai: {{ $p->cod_produto_pai ?? '—' }}
                        </span>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    {{-- Linha de cima foto + tabela --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                        {{-- Card Foto --}}
                        <div class="lg:col-span-2">
                            <div class="bg-white border border-gray-200 rounded-xl p-3 h-full flex items-center justify-center overflow-hidden">
                                @if($img)
                                    <img
                                        src="{{ $img }}"
                                        alt="{{ $p->des_produto ?? 'Produto' }}"
                                        class="w-full h-full object-contain max-h-[360px] rounded-lg"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <div class="w-full h-[260px] rounded-lg bg-slate-100 flex items-center justify-center text-gray-400 font-bold" style="display: none;">
                                        Sem foto
                                    </div>
                                @else
                                    <div class="w-full h-[260px] rounded-lg bg-slate-100 flex items-center justify-center text-gray-400 font-bold">
                                        Sem foto
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tabela (desktop) --}}
                        <div class="lg:col-span-10 hidden md:block">

                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                                <div class="overflow-x-auto">

                                    <table class="w-full text-sm">

                                        <thead class="bg-blue-100">

                                            <tr class="text-gray-600 text-xs uppercase">

                                                <th class="text-left px-4 py-3 font-black">Variação (Cor/Tam)</th>

                                                @foreach($lojasFixas as $loja)
                                                    <th class="text-center px-4 py-3 font-black">{{ $loja }}</th>
                                                @endforeach

                                                <th class="text-center px-4 py-3 font-black">Total</th>

                                            </tr>

                                        </thead>

                                        <tbody class="divide-y divide-gray-200">
                                            
                                            @forelse($card['matriz'] as $row)


                                                <tr class="hover:bg-slate-50">

                                                    <td class="px-4 py-3 font-bold text-slate-800">
                                                        {{-- CÓDIGO EM NEGRITO --}}
                                                        <span class="font-black text-slate-800">
                                                            {{ $row['variacao'] }}
                                                        </span>
                                                        {{-- DESCRIÇÃO EM CINZA E ENTRE PARÊNTESES --}}
                                                        <span class="text-slate-400 font-medium ml-1">
                                                            ({{ mb_convert_encoding($row['des1_produto'] ?: $row['desc'], 'UTF-8', 'ISO-8859-1') }})
                                                        </span>
                                                    </td>

                                                    @foreach($lojasFixas as $loja)

                                                        @php $qtd = (int)(
                                                            $row['lojas'][$loja] ?? 0); 
                                                        @endphp

                                                        <td class="text-center px-4 py-3 font-black {{ $qtd==0 ? 'text-gray-400' : ('text-slate-800') }}">
                                                            {{ $qtd }}
                                                        </td>
                                                        
                                                    @endforeach

                                                    <td class="text-center px-4 py-3 font-black">
                                                        {{ (int)($row['total'] ?? 0) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="px-4 py-6 text-gray-500 font-bold">—</td>
                                                    @foreach($lojasFixas as $loja)
                                                        <td class="text-center px-4 py-6 font-black text-gray-400">0</td>
                                                    @endforeach
                                                    <td class="text-center px-4 py-6 font-black text-gray-400">0</td>
                                                </tr>


                                            @endforelse
                                        </tbody>

                                        <tfoot class="bg-slate-800 text-white">
                                            <tr>
                                                <td class="px-4 py-3 font-black">TOTAL</td>
                                                @foreach($lojasFixas as $loja)
                                                    <td class="text-center px-4 py-3 font-black">
                                                        {{ (int)($card['totaisPorLoja'][$loja] ?? 0) }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center px-4 py-3 font-black">
                                                    {{ (int)($card['totalGeral'] ?? 0) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile (cards de variação) --}}
                        <div class="lg:col-span-9 md:hidden space-y-3">
                            @forelse($card['matriz'] as $row)
                                <div class="bg-white border border-gray-200 rounded-xl p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="font-black text-slate-800">
                                            {{ $row['desc'] ?: $row['variacao'] }}
                                        </p>
                                        <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-800 text-white">
                                            Total: {{ (int)($row['total'] ?? 0) }}
                                        </span>
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
                                <div class="bg-white border border-gray-200 rounded-xl p-4 text-gray-500 font-bold">
                                    Sem variações
                                </div>
                            @endforelse

                            <div class="bg-slate-800 text-white border border-slate-900 rounded-xl p-4">
                                <p class="font-black mb-3">TOTAL</p>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    @foreach($lojasFixas as $loja)
                                        <div class="bg-white/10 rounded-lg p-3">
                                            <p class="text-[10px] font-bold uppercase opacity-80">{{ $loja }}</p>
                                            <p class="font-black">{{ (int)($card['totaisPorLoja'][$loja] ?? 0) }}</p>
                                        </div>
                                    @endforeach
                                    <div class="bg-white/10 rounded-lg p-3 col-span-2">
                                        <p class="text-[10px] font-bold uppercase opacity-80">Total Geral</p>
                                        <p class="font-black">{{ (int)($card['totalGeral'] ?? 0) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </section>
        @endforeach

        {{-- paginação --}}
        <div class="mt-6">
            {{ $pagination->links() }}
        </div>

    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader-overlay');

        // Função padrão para mostrar o loader
        const showLoader = () => {
            loader.style.display = 'flex';
        };

        // Ao clicar no botão Buscar
        const btnBuscar = document.getElementById('btnBuscar');
        if (btnBuscar) {
            btnBuscar.addEventListener('click', function() {
                // Só mostra se o formulário for válido (opcional)
                showLoader();
            });
        }

        // Ao clicar no botão PDF
        //EXPORTAÇÃO PDF (Capturando filtros atuais)
        const btnPdf = document.getElementById('btnPdf');
        if (btnPdf) {
            btnPdf.addEventListener('click', function(e) {
                e.preventDefault();

                // Pegamos os valores atuais dos filtros na tela
                const local = document.querySelector('select[name="local"]').value;
                const grupo = document.querySelector('select[name="grupo"]').value;
                const subgrupo = document.querySelector('select[name="subgrupo"]').value;

                // 2. Montamos a URL com os parâmetros dinâmicos
                const baseUrl = "{{ route('estoque_lojas.export.pdf') }}";
                const params = new URLSearchParams({
                    local: local,
                    grupo: grupo,
                    subgrupo: subgrupo
                });

                //  Mostramos o loader e redirecionamos
                showLoader();
                window.location.href = `${baseUrl}?${params.toString()}`;
                
                // Timer de segurança para esconder o loader
                setTimeout(() => {
                    const loader = document.getElementById('loader-overlay');
                    loader.style.setProperty('display', 'none', 'important');
                }, 15000); 
            });
        }

        //  Paginação (Apenas nos links de números lá embaixo)
        // Seleciona links dentro da navegação de paginação para não pegar o menu lateral
        const pgLinks = document.querySelectorAll('nav[role="navigation"] a');
        pgLinks.forEach(link => {
            link.addEventListener('click', showLoader);
        });
    });
</script>
@endpush