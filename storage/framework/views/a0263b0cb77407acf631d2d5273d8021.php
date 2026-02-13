<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
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
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
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
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-blue-50 p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col h-full">
    <div class="flex justify-between items-start mb-4">
        <span class="px-2 py-1 bg-<?php echo e($cor); ?>-100 text-<?php echo e($cor); ?>-600 text-[18px] font-bold rounded uppercase tracking-wider">
            <?php echo e($titulo); ?>

        </span>
        <span class="text-green-500 text-xs font-bold"><?php echo e($porcentagem); ?></span>
    </div>

    <div class="space-y-3">
        <div>
            <h3 class="text-green-400 text-[15px] font-bold uppercase">
                Venda Atual
                <?php if($dataAtual): ?>
                    <span class="text-gray-400 text-[11px] font-bold ml-1">(<?php echo e($dataAtual); ?>)</span>
                <?php endif; ?>
            </h3>
            <p class="text-xl font-black text-gray-800 tracking-tight"><?php echo e($valor); ?></p>
        </div>

        <div class="mt-auto pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">

            <div class="text-center border-r border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-sm font-black text-slate-700"><?php echo e($pedidosAtual); ?></p>
            </div>

            <div class="text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-sm font-black text-slate-700"><?php echo e($clientesAtual); ?></p>
            </div>
            
        </div>

        <div class="pt-4 border-t border-gray-300">
            <h3 class="text-gray-600 text-[12px] font-bold uppercase">
                Período Anterior
                <?php if($dataAnterior): ?>
                    <span class="text-gray-400 text-[11px] font-bold ml-1">(<?php echo e($dataAnterior); ?>)</span>
                <?php endif; ?>
            </h3>
            <p class="text-md font-bold text-gray-8
            00"><?php echo e($anterior); ?></p>
        </div>

        <div class="mt-auto pt-4 border-t border-gray-50 grid grid-cols-2 gap-2">

            <div class="text-center border-r border-gray-300">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Pedidos</p>
                <p class="text-sm font-black text-slate-700"><?php echo e($pedidosAnterior); ?></p>
            </div>

            <div class="text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase">Clientes</p>
                <p class="text-sm font-black text-slate-700"><?php echo e($clientesAnterior); ?></p>
            </div>
            
        </div>
    </div>

    
</div>
<?php /**PATH C:\domeblue-dash\resources\views/components/card-indicador.blade.php ENDPATH**/ ?>