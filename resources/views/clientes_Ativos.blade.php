@extends('layouts.app')

@section('title', 'Consulta de Clientes - DomeBlue')

@section('content')

{{---//// LOADER PADRÃO \\\\\--}}
<div id="loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 999999; align-items: center; justify-content: center; cursor: wait;">
    <div class="flex flex-col items-center">
        <img src="{{ asset('imagens/loader.gif') }}" alt="Carregando..." class="w-12 h-12">
        <div class="mt-4 text-center">
            <h3 class="text-blue-900 font-black uppercase tracking-widest">Analisando Carteira</h3>
            <p class="text-slate-500 text-sm">Aguarde, processando filtros e marcas...</p>
        </div>
    </div>
</div>

{{-- ////////// Header / Título e Filtros \\\\\\\\\\\\--}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Consulta de Clientes PJ</h1>
            <p class="text-gray-500 text-sm">Lista de clientes e análise de retenção</p>
        </div>

        <form method="GET" action="{{ route('clientes_Ativos') }}" class="flex flex-wrap gap-2 items-end">

            {{-- filtro clinte--}}
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Pesquisar Cliente</label>
                <input type="text" name="busca_cliente" value="{{ request('busca_cliente') }}" 
                       placeholder="Cód ou Razão..." class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            {{-- filtro estado--}}
            <div class="w-full sm:w-32">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Estado (UF)</label>
                <select name="busca_uf" id="select-uf" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">UF</option>
                    @foreach($estados as $uf)
                        <option value="{{ $uf }}" {{ request('busca_uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>

            {{-- filtro muncipio--}}
            <div class="w-full sm:w-64">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Município</label>
                <select name="busca_municipio" id="select-municipio" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="">Selecione a UF</option>
                    {{-- Será preenchido pelo JavaScript --}}
                </select>
            </div>

            {{-- filtro peeriodo inicio--}}
            <div class="w-full sm:w-40">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Início Período</label>
                <input type="date" name="data_inicio" value="{{ $dataInicio }}" class="w-full border rounded-lg p-2 text-sm">
            </div>

            {{-- filtro peeriodo fim--}}
            <div class="w-full sm:w-40">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Fim Período</label>
                <input type="date" name="data_fim" value="{{ $dataFim }}" class="w-full border rounded-lg p-2 text-sm">
            </div>

            {{-- botao filtrar--}}
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>
            
            {{-- botao botao limpar--}}
            <a href="{{ route('clientes_Ativos') }}" class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">Limpar</a>

        </form>
    </div>
</header>

@if(!request()->has('busca_cliente') && !request()->has('data_inicio'))
    <div class="bg-white p-10 rounded-xl shadow-sm border border-blue-200 text-center mt-6">
        <i class="fa-solid fa-filter text-blue-300 text-5xl mb-4"></i>
        <h3 class="text-gray-700 font-bold text-xl uppercase">Consulta de Carteira</h3>
        <p class="text-blue-800 font-bold mt-2">Utilize os filtros acima para pesquisar clientes e analisar a retenção.</p>
    </div>

{{-- 2. SEM RESULTADOS: Usuário pesquisou, mas o banco não retornou nada --}}
@elseif($clientes->isEmpty())
    <div class="bg-red-50 p-10 rounded-xl shadow-sm border border-red-200 text-center mt-6">
        <i class="fa-solid fa-magnifying-glass-slash text-red-300 text-5xl mb-4"></i>
        <h3 class="text-red-700 font-bold text-xl uppercase">Nenhum resultado encontrado</h3>
        <p class="text-gray-600 mt-2">Não encontramos clientes para os termos: <span class="font-bold">"{{ request('busca_cliente') }}"</span>.</p>
        <p class="text-xs text-gray-500 mt-1">Tente revisar o código ou a data selecionada.</p>
    </div>
@else
    <div class="space-y-4">
        @foreach($clientes as $cliente)
            @php
                $isAtivo = !empty($cliente->compra_periodo);
                $isSy = str_starts_with(strtolower((string)$cliente->marca_refid), 'sy'); // Lógica Syssa/Amissima
            @endphp

            <section class="bg-blue-50 rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- Cabeçalho do Card --}}
                <div class="px-6 py-3 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between bg-white/60">

                    <div class="flex items-center gap-3">

                        <span class="text-[12px] font-black px-3 py-1 rounded-full bg-slate-800 text-white">
                            Cód cliente: {{ $cliente->cod_cliente }}
                        </span>

                        <h2 class="text-base font-black text-gray-800 uppercase">
                            {{ mb_convert_encoding($cliente->raz_cliente, 'UTF-8', 'ISO-8859-1') }}
                        </h2>

                    </div>
                    
                    <div class="flex gap-2">

                        <span class="text-[10px] font-black px-3 py-1 rounded-full {{ $isAtivo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} border border-current uppercase">
                            {{ $isAtivo ? 'ATIVO NO PERÍODO' : 'INATIVO NO PERÍODO' }}
                        </span>

                        <span class="text-[10px] font-black px-3 py-1 rounded-full bg-blue-900 text-white uppercase">
                            MARCA: {{ $isSy ? 'SYSSA' : 'AMISSIMA' }}
                        </span>

                    </div>

                </div>

                {{-- Conteúdo do Card --}}
                <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                    

                    {{-- DADOS ENDEREÇO--}}
                    <div class="md:col-span-2">

                        <p class="text-[10px] font-bold text-gray-400 uppercase">Endereço</p>

                        <p class="font-bold text-slate-700">
                            {{ $cliente->des_endereco }}, {{ $cliente->numero }}
                        </p>

                        <p class="text-[13px] text-slate-500 italic">
                            CEP: {{ $cliente->cep_cliente }} | {{ $cliente->cod_municipio }} - {{ $cliente->uf_cliente }}
                        </p>

                    </div>

                    {{-- CNPJ --}}
                    <div>

                        <p class="text-[10px] font-bold text-gray-400 uppercase">CNPJ</p>

                        <p class="font-mono text-slate-700 font-bold">{{ $cliente->cnpj_cliente }}</p>

                    </div>

                    {{-- ULTIMA COMPRA --}}
                    <div class="text-right">

                        <p class="text-[10px] font-bold text-gray-400 uppercase">Última Compra Geral</p>
                        
                        <p class="font-black text-slate-800">
                            {{ $cliente->ultima_compra_geral ? date('d/m/Y', strtotime($cliente->ultima_compra_geral)) : 'SEM REGISTRO' }}
                        </p>

                    </div>

                </div>

            </section>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $clientes->appends(request()->query())->links() }}
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader-overlay');
        const showLoader = () => loader.style.display = 'flex';
        
        document.querySelector('form').addEventListener('submit', showLoader);
        document.querySelectorAll('nav[role="navigation"] a').forEach(link => {
            link.addEventListener('click', showLoader);
        });
    });

    document.getElementById('select-uf').addEventListener('change', function() {
    const uf = this.value;
    const selectMun = document.getElementById('select-municipio');
    
    selectMun.innerHTML = '<option value="">Carregando...</option>';
    
    if (!uf) {
        selectMun.innerHTML = '<option value="">Selecione a UF</option>';
        return;
    }

    
    fetch(`{{ url('/api/municipios') }}/${uf}`)
    .then(response => response.json())
    .then(data => {
        selectMun.innerHTML = '<option value="">Todos os Municípios</option>';
        data.forEach(mun => {
            // mun.codigo é o que vai pro banco, mun.nome é o que o usuário vê
            if(mun.nome !== "") { 
                selectMun.innerHTML += `<option value="${mun.codigo}">${mun.nome}</option>`;
            }
        });
    })
    .catch(error => {
        console.error('Erro:', error);
        selectMun.innerHTML = '<option value="">Erro ao carregar</option>';
    });
});
</script>
@endpush