@props([
    'titulo',
    'valor',
    'anterior',
    'cor',
    'porcentagem',
    'pedidosAtual',
    'clientesAtual',
    'pedidosAnterior',
    'clientesAnterior',
    'dataAtual' => null,
    'dataAnterior' => null
])

<div class="bg-blue-50 p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex justify-between items-start mb-4">
        <span class="px-2 py-1 bg-{{ $cor }}-100 text-{{ $cor }}-600 text-[18px] font-bold rounded uppercase tracking-wider">
            {{ $titulo }}
        </span>
        <span class="text-green-500 text-xs font-bold">{{ $porcentagem }}</span>
    </div>

    <div class="space-y-3">
        <div>
            <h3 class="text-green-400 text-[15px] font-bold uppercase">
                Venda Atual
                @if($dataAtual)
                    <span class="text-gray-400 text-[11px] font-bold ml-1">({{ $dataAtual }})</span>
                @endif
            </h3>
            <p class="text-xl font-black text-gray-800 tracking-tight">{{ $valor }}</p>
        </div>

        <div class="mt-auto pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">

            <div class="text-center border-r border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-sm font-black text-slate-700">{{ $pedidosAtual }}</p>
            </div>

            <div class="text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-sm font-black text-slate-700">{{ $clientesAtual }}</p>
            </div>
            
        </div>

        <div class="pt-4 border-t border-gray-300">
            <h3 class="text-gray-600 text-[12px] font-bold uppercase">
                Período Anterior
                @if($dataAnterior)
                    <span class="text-gray-400 text-[11px] font-bold ml-1">({{ $dataAnterior }})</span>
                @endif
            </h3>
            <p class="text-md font-bold text-gray-8
            00">{{ $anterior }}</p>
        </div>

        <div class="mt-auto pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">

            <div class="text-center border-r border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-sm font-black text-slate-700">{{ $pedidosAnterior }}</p>
            </div>

            <div class="text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-sm font-black text-slate-700">{{ $clientesAnterior }}</p>
            </div>
            
        </div>
    </div>

    
</div>
