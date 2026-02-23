@props([
    'titulo', 'cor', 'loja' 
])

<div class="bg-blue-50 p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full text-center">
    {{-- TÍTULO DA LOJA --}}
    <div class="mb-3">
        <span class="px-4 py-1 bg-{{ $cor }}-100 text-{{ $cor }}-600 text-xl font-black rounded uppercase tracking-tighter">
            {{ $titulo }}
        </span>
    </div>

    {{-- BLOCO ATUAL --}}
    <div class="space-y-2 border-b-2 border-gray-200 pb-3">
        <h3 class="text-gray-500 text-[12px] font-bold uppercase">Venda Atual</h3>
        <p class="text-2xl font-black text-gray-800">R$ {{ number_format($loja['total_atual'], 2, ',', '.') }}</p>

        <div class="grid grid-cols-2 gap-2 border-y border-gray-300 py-2">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Amissima</p>
                <p class="text-sm font-bold text-slate-700">R$ {{ number_format($loja['amissima_atual'] ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="border-l border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Syssa</p>
                <p class="text-sm font-bold text-slate-700">R$ {{ number_format($loja['syssa_atual'] ?? 0, 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center py-1">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-md font-black text-slate-800">{{ $loja['pedidos_atual'] }}</p>
            </div>
            <div class="border-l border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-md font-black text-slate-800">{{ $loja['clientes_atual'] }}</p>
            </div>
        </div>

        <div class="bg-slate-200 rounded py-1">
            <p class="text-[10px] text-gray-500 font-bold uppercase">Total Peças</p>
            <p class="text-lg font-black text-blue-700">{{ number_format($loja['pecas_atual'] ?? 0, 0, '', '.') }}</p>
        </div>
    </div>

    {{-- BLOCO ANTERIOR--}}
    <div class="mt-3 opacity-70">
        <h3 class="text-gray-400 text-[11px] font-bold uppercase">Período Anterior</h3>
        <p class="text-lg font-bold text-gray-600">R$ {{ number_format($loja['total_anterior'], 2, ',', '.') }}</p>
        <div class="grid grid-cols-2 gap-2 border-y border-gray-300 py-2">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Amissima</p>
                <p class="text-sm font-bold text-slate-700">R$ {{ number_format($loja['amissima_anterior'] ?? 0, 2, ',', '.') }}</p>
            </div>
            <div class="border-l border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Syssa</p>
                <p class="text-sm font-bold text-slate-700">R$ {{ number_format($loja['syssa_anterior'] ?? 0, 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center py-1">
            <div>
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-md font-black text-slate-800">{{ $loja['pedidos_anterior'] }}</p>
            </div>
            <div class="border-l border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-md font-black text-slate-800">{{ $loja['clientes_anterior'] }}</p>
            </div>
        </div>

        <div class="bg-slate-200 rounded py-1">
            <p class="text-[10px] text-gray-500 font-bold uppercase">Total Peças</p>
            <p class="text-lg font-black text-blue-700">{{ number_format($loja['pecas_anterior'] ?? 0, 0, '', '.') }}</p>
        </div>
    </div>
</div>