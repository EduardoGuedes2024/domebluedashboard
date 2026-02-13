@extends('layouts.app')

@section('title', 'Vendas - DomeBlue')

@section('content')

        
            
            <header class="mb-8 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                    {{-- ESQUERDA (título) --}}
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Resumo de Performance</h1>
                        <p class="text-gray-500 text-sm">
                            Olá <span class="font-bold text-gray-800">{{ auth()->user()->nome ?? 'Usuário' }}</span> , visualize os dados e Performance de cada Loja
                        </p>
                    </div>

                    {{-- DIREITA (filtros) --}}
                    <div class="flex flex-col items-end gap-3">


                        {{-- filtros --}}
                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end justify-end gap-3">

                            {{-- FILTRO ATUAL --}}
                            <div>
                                <label class="block text-xs font-bold text-black-800 uppercase mb-1">Atual Início</label>
                                <input type="date"
                                    name="atual_inicio"
                                    value="{{ request('atual_inicio', $atual_inicio) }}"
                                    class="border rounded-lg p-2 text-sm text-black-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-black-800 uppercase mb-1">Atual Fim</label>
                                <input type="date"
                                    name="atual_fim"
                                    value="{{ request('atual_fim', $atual_fim) }}"
                                    class="border rounded-lg p-2 text-sm text-black-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            

                            {{-- FILTRO ANTERIOR --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Anterior Início</label>
                                <input type="date"
                                    name="anterior_inicio"
                                    value="{{ request('anterior_inicio', $anterior_inicio) }}"
                                    class="border rounded-lg p-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Anterior Fim</label>
                                <input type="date"
                                    name="anterior_fim"
                                    value="{{ request('anterior_fim', $anterior_fim) }}"
                                    class="border rounded-lg p-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center">
                                <i class="fas fa-filter mr-2"></i> Aplicar Filtro
                            </button>

                        </form>
                    </div>
                </div>
            </header>


            <!---lembra de remover status de conecçao
            @if(isset($conexaoStatus))
            <div style="padding:10px; border:1px solid #ccc; margin-bottom:12px;">
                <strong>Status:</strong> {{ $conexaoStatus }}
            </div>
            @endif --->


            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-10">
                @foreach($lojas as $loja)
                    <x-card-indicador 
                        :titulo="$loja['nome']" 
                        :valor="'R$ ' . number_format($loja['atual'], 2, ',', '.')"
                        :anterior="'R$ ' . number_format($loja['anterior'], 2, ',', '.')"
                        :cor="$loja['cor']" 
                        icone="store" 
                        :porcentagem="($loja['atual'] > $loja['anterior'] ? '+' : '-') " 
                        :pedidosAtual="$loja['pedidos_atual']"
                        :clientesAtual="$loja['clientes_atual']"
                        :pedidosAnterior="$loja['pedidos_anterior']"
                        :clientesAnterior="$loja['clientes_anterior']"
                        
                    />
                @endforeach

            </section>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Grafico de Vendas por Unidade</h3>
                        <i class="fas fa-store text-gray-400"></i>
                    </div>
                    <canvas id="salesChart" height="200"></canvas>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Financeiro por Forma de Pagamento</h3>
                        
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 border-b border-gray-100 text-xs uppercase">
                                    <th class="pb-3 font-medium">Forma Pag.</th>
                                    <th class="pb-3 font-medium text-center">QTD</th>
                                    <th class="pb-3 font-medium text-right">R$ Valor</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-gray-700">
                                @foreach($financeiro as $item)
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-3 font-medium">{{ $item['forma'] }}</td>
                                    <td class="py-3 text-center">{{ $item['qtd'] }}</td>
                                    <td class="py-3 text-right font-bold">R$ {{ number_format($item['valor'], 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="2" class="py-3 px-2 font-bold text-gray-800">TOTAL GERAL</td>
                                    <td class="py-3 px-2 text-right font-black text-blue-600 text-lg">
                                        R$ {{ number_format(array_sum(array_column($financeiro, 'valor')), 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const graficoVendasAtual = @json($graficoVendasAtual);

        const labels = Object.keys(graficoVendasAtual);
        const valores = Object.values(graficoVendasAtual);

        // CORES DOS CARDS
        const cores = [
            '#3b82f6', // Alphaville - Azul
            '#a855f7', // Syssa - Roxo
            '#f97316', // JK - Laranja
            '#22c55e', // Rio - Verde
            '#ec4899', // Atacado - Rosa
            '#eab308', // Ecommerce - Amarelo
            '#dc2626', // Curitiba - Vermelho
        ];

        const ctx = document.getElementById('salesChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Vendas Totais (R$)',
                    data: valores,
                    backgroundColor: cores,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                }
            }
        });
    </script>

@endsection

