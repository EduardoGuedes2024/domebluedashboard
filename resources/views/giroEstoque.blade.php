@extends('layouts.app')

@section('title', 'Giro de Estoque - DomeBlue')

@section('content')

@php
    // Helper para formatar porcentagem conforme rascunho
    $pct = fn($v) => number_format($v, 2, ',', '.') . '%';
@endphp

{{-- FILTROS --}}
<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Giro de Estoque</h1>
            <p class="text-gray-500 text-sm">Análise tempo de permanência e giro de estoque</p>
        </div>

        <form method="GET" action="{{ route('giroEstoque') }}" class="flex flex-wrap items-end gap-3">
            {{-- Unidade --}}
            <div class="w-48">

                <label class="block text-[12px] font-black text-slate-400 uppercase mb-1">loja</label>

                <select name="loja" class="w-full border rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">

                    <option value="todas" @selected(request('loja') == 'todas')>Todas as Lojas</option>

                    <option value="jk" @selected(request('loja') == 'jk')>JK Iguatemi</option>

                    <option value="alphaville" @selected(request('loja') == 'alphaville')>Alphaville</option>

                    <option value="curitiba" @selected(request('loja') == 'curitiba')>Curitiba</option>

                    <option value="rio" @selected(request('loja') == 'rio')>Rio de Janeiro</option>

                    <option value="atacado" @selected(request('loja') == 'atacado')>Atacado</option>

                    <option value="ecommerce" @selected(request('loja') == 'ecommerce')>Ecommerce</option>

                </select>

            </div>

            {{-- Grupo --}}
            <div class="w-48">

                <label class="block text-[12px] font-black text-slate-400 uppercase mb-1">Grupo</label>

                <select name="grupo" class="w-full border rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">

                    <option value="">Todos</option>
                    @foreach($grupos as $g)
                        <option value="{{ $g }}" @selected(request('grupo') == $g)>{{ $g }}</option>
                    @endforeach
                    
                </select>

            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Filtrar
            </button>

        </form>

    </div>

</header>


<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    {{-- Coluna do Gráfico --}}
    <div class="lg:col-span-7 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">

        <h3 class="text-lg font-black text-slate-800 mb-4 uppercase tracking-tighter">
            {{ $LojaSelecionada == 'todas' ? 'Rede Total' : strtoupper($LojaSelecionada) }}
        </h3>

        <div class="h-[450px]">
            <canvas id="chartGiroEstoque"></canvas>
        </div>

    </div>

    {{-- Coluna dos Dados--}}
    <div class="lg:col-span-5 flex flex-col gap-4">

        {{-- Card Saldo Total --}}
        <div class="bg-blue-100 text-slate-800 p-6 rounded-xl shadow-lg">
            
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Saldo Total em Peças</p>
                    <h2 class="text-4xl font-black mt-1">{{ number_format($totalGeralPecas, 0, ',', '.') }}</h2>
                </div>
                
                {{-- NOVO: VALOR TOTAL GERAL --}}
                <div class="text-right">
                    <p class="text-[11px] font-bold text-slate-800 uppercase tracking-widest">Valor estimado em Estoque</p>
                    <h2 class="text-2xl font-black mt-1 text-slate-800">
                        R$ {{ number_format($totalGeralValor, 2, ',', '.') }}
                    </h2>
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-slate-700 flex justify-between items-center text-sm">
                <span class="text-slate-500 font-medium">Unidade Selecionada:</span>
                <span class="font-black uppercase text-blue-400">{{ $LojaSelecionada }}</span>
            </div>

        </div>

        {{-- Tabela de Faixas --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm flex-1">

            <table class="w-full text-sm">

                <thead class="bg-slate-50 border-b">

                    <tr class="text-[13px] font-black text-slate-500 uppercase">
                        <th class="px-4 py-3 text-left">Período (Últ. Venda)</th>
                        <th class="px-4 py-3 text-center">Qtd Peças</th>
                        <th class="px-4 py-3 text-right">valor</th>
                        <th class="px-4 py-3 text-end">% Giro</th>
                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 font-bold text-slate-700">

                    {{--- 30 dias ---}}
                    <tr onclick="window.location='{{ route('giroEstoque.lista', ['loja' => $LojaSelecionada, 'periodo' => '30']) }}'" class="cursor-pointer hover:bg-slate-50">
                        
                        <td class="px-4 py-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-green-500"></div> 30 Dias</td>
                        
                        <td class="px-4 py-4 text-center">{{ number_format($resumoGiro['30 dias'], 0, ',', '.') }}</td>
                        
                        <td class="px-4 py-4 text-right font-bold text-blue-600">
                            R$ {{ number_format($valoresGiro['30 dias'], 2, ',', '.') }}
                        </td>
                        
                        <td class="px-4 py-4 text-right text-green-600">{{ $pct($porcentagens['30 dias']) }}</td>
                    
                    </tr>

                    {{--- 60 dias---}}
                    <tr onclick="window.location='{{ route('giroEstoque.lista', ['loja' => $LojaSelecionada, 'periodo' => '60']) }}'" class="cursor-pointer hover:bg-slate-50">
                        
                        <td class="px-4 py-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-yellow-400"></div> 60 Dias</td>
                        
                        <td class="px-4 py-4 text-center">{{ number_format($resumoGiro['60 dias'], 0, ',', '.') }}</td>
                        
                        <td class="px-4 py-4 text-right font-bold text-blue-600">
                            R$ {{ number_format($valoresGiro['60 dias'], 2, ',', '.') }}
                        
                        </td>
                        
                        <td class="px-4 py-4 text-right text-yellow-600">{{ $pct($porcentagens['60 dias']) }}</td>

                    </tr>

                    {{--- 90 dias ---}}
                    <tr onclick="window.location='{{ route('giroEstoque.lista', ['loja' => $LojaSelecionada, 'periodo' => '90']) }}'" class="cursor-pointer hover:bg-slate-50">

                        <td class="px-4 py-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-orange-400"></div> 90 Dias</td>

                        <td class="px-4 py-4 text-center">{{ number_format($resumoGiro['90 dias'], 0, ',', '.') }}</td>

                        <td class="px-4 py-4 text-right font-bold text-blue-600">
                            R$ {{ number_format($valoresGiro['90 dias'], 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-4 text-right text-orange-600">{{ $pct($porcentagens['90 dias']) }}</td>

                    </tr>

                    {{---- 120 dias ---}}
                    <tr onclick="window.location='{{ route('giroEstoque.lista', ['loja' => $LojaSelecionada, 'periodo' => '120']) }}'" class="cursor-pointer hover:bg-slate-50">

                        <td class="px-4 py-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-red-500"></div> 120 Dias</td>

                        <td class="px-4 py-4 text-center">{{ number_format($resumoGiro['120 dias'], 0, ',', '.') }}</td>

                        <td class="px-4 py-4 text-right font-bold text-blue-600">
                            R$ {{ number_format($valoresGiro['120 dias'], 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-4 text-right text-red-500">{{ $pct($porcentagens['120 dias']) }}</td>

                    </tr>

                    {{--- 150 dias --}}
                    <tr onclick="window.location='{{ route('giroEstoque.lista', ['loja' => $LojaSelecionada, 'periodo' => '150']) }}'" class="bg-red-50/50 cursor-pointer hover:bg-slate-50">

                        <td class="px-4 py-4 flex items-center gap-2 font-black text-red-700"><div class="w-2 h-2 rounded-full bg-red-800"></div> Acima 150 Dias</td>

                        <td class="px-4 py-4 text-center text-red-700 font-black">{{ number_format($resumoGiro['150 dias'], 0, ',', '.') }}</td>

                        <td class="px-4 py-4 text-right font-bold text-blue-600">
                            R$ {{ number_format($valoresGiro['150 dias'], 2, ',', '.') }}
                        </td>

                        <td class="px-4 py-4 text-right text-red-800 font-black">{{ $pct($porcentagens['150 dias']) }}</td>

                    </tr>

                </tbody>

            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('chartGiroEstoque').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['30 Dias', '60 Dias', '90 Dias', '120 Dias', '150+ Dias'],
                datasets: [{
                    data: [
                        {{ $resumoGiro['30 dias'] ?? 0 }}, 
                        {{ $resumoGiro['60 dias'] ?? 0 }}, 
                        {{ $resumoGiro['90 dias'] ?? 0 }}, 
                        {{ $resumoGiro['120 dias'] ?? 0 }}, 
                        {{ $resumoGiro['150 dias'] ?? 0 }}
                    ],
                    backgroundColor: ['#22c55e', '#facc15', '#fb923c', '#ef4444', '#991b1b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20, font: { weight: 'bold' } }
                    }
                }
            }
        });
    });
</script>
@endpush