

<?php $__env->startSection('title', 'Estoque - DomeBlue'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');

    // $produto sempre existe (vem como "vazio" no controller)
    $p = $produto;

    // se encontrou produto real, vai usar o cod pai real no header
    $codPaiHeader = $p->cod_produto_pai ?: ($codigoPai ?: '—');

    // controle de estado visual
    $temBusca = (trim($codigoPai ?? '') !== '');
    $temResultado = (bool) ($encontrou ?? false);
?>


<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Estoque</h1>
            <p class="text-gray-500 text-sm">Consulta por produto e disponibilidade por loja</p>
        </div>

        <form method="GET" action="<?php echo e(route('estoque')); ?>" class="flex flex-col sm:flex-row gap-2 items-end">
            <div class="w-full sm:w-72">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Código Pai</label>
                <input
                    type="text"
                    name="codigo_pai"
                    value="<?php echo e($codigoPai); ?>"
                    placeholder="Ex: A5201 ou SY0129"
                    class="w-full border rounded-lg p-2 text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none"
                >
            </div>

            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Buscar
            </button>

            <?php if($temBusca): ?>
                <a href="<?php echo e(route('estoque')); ?>"
                   class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                    Limpar
                </a>
            <?php endif; ?>
        </form>
    </div>
</header>


<section class="bg-blue-50 rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h2 class="text-lg font-black text-gray-800">
            <?php echo e(mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1')); ?>

        </h2>

        <span class="text-xs font-bold px-3 py-1 rounded-full bg-slate-800 text-white">
            Cód. Pai: <?php echo e($codPaiHeader); ?>

        </span>
    </div>

    <div class="p-6 space-y-6">

        
        <?php if($temBusca && !$temResultado): ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="text-red-700 font-bold">
                    Nenhum produto encontrado para o Código Pai: <?php echo e($codigoPai); ?>

                </p>
            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

            
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
                        src="<?php echo e($imgUrl ?? asset('imagens/sem-foto.png')); ?>"
                        alt="<?php echo e(mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1')); ?>"
                        class="w-full h-full object-contain max-h-[380px] rounded-lg"
                    />

                </div>
            </div>


            
            <div class="md:col-span-10">
                <div class="bg-white border border-gray-200 rounded-xl p-5 h-full">
                    <div class="space-y-2">
                        <p class="text-sm text-gray-500 font-bold uppercase">Descrição</p>
                        <p class="text-lg font-black text-gray-800">
                            <?php echo e(mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1')); ?>

                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <span class="text-xs font-bold text-gray-500 uppercase">Cód:</span>
                            <span class="text-sm font-black text-slate-800">
                                <?php echo e($p->refid_pai ?: ''); ?>

                            </span>
                        </div>
                    </div>

                    <div class="my-4 border-t border-gray-200"></div>

                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ Varejo</span>
                            <span class="text-sm font-black text-slate-800">
                                <?php echo e($temResultado ? $brl($p->preco_01) : ''); ?>

                            </span>
                        </div>

                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ Atacado</span>
                            <span class="text-sm font-black text-slate-800">
                                <?php echo e($temResultado ? $brl($p->preco_02) : ''); ?>

                            </span>
                        </div>

                       <!--- <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$ </span>
                            <span class="text-sm font-black text-slate-800">
                                <?php echo e($temResultado ? $brl($p->preco_03) : ''); ?>

                            </span>
                        </div>

                        <div class="flex items-center justify-between bg-slate-50 border border-gray-200 rounded-lg px-4 py-3">
                            <span class="text-xs font-bold text-gray-500 uppercase">R$</span>
                            <span class="text-sm font-black text-slate-800">
                                <?php echo e($temResultado ? ($p->preco_04 ?? '-') : ''); ?>

                            </span> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        
        <div class="md:hidden space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $matriz; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white border border-gray-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <p class="font-black text-slate-800">
                            <?php echo e($row['codigo']); ?>

                            <span class="text-gray-400 font-medium">(<?php echo e($row['desc'] ?: ''); ?>)</span>
                        </p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-800 text-white">Variação</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $qtd = (int)($row['lojas'][$loja] ?? 0); ?>
                            <div class="bg-slate-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-500 uppercase"><?php echo e($loja); ?></p>
                                <p class="font-black <?php echo e($qtd==0 ? 'text-gray-400' : ($qtd<3 ? 'text-amber-600' : 'text-slate-800')); ?>">
                                    <?php echo e($qtd); ?>

                                </p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
                <div class="bg-white border border-gray-200 rounded-xl p-4 opacity-70">
                    <div class="flex items-center justify-between">
                        <p class="font-black text-slate-800">— <span class="text-gray-400 font-medium">(—)</span></p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full bg-slate-800 text-white">Variação</span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-slate-50 border border-gray-200 rounded-lg p-3">
                                <p class="text-[10px] font-bold text-gray-500 uppercase"><?php echo e($loja); ?></p>
                                <p class="font-black text-gray-400">0</p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="bg-slate-800 text-white border border-slate-900 rounded-xl p-4">
                <p class="font-black mb-3">TOTAL</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white/10 rounded-lg p-3">
                            <p class="text-[10px] font-bold uppercase opacity-80"><?php echo e($loja); ?></p>
                            <p class="font-black"><?php echo e((int)($totaisPorLoja[$loja] ?? 0)); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        
        <div class="hidden md:block bg-white border border-gray-200 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-blue-100">
                        <tr class="text-gray-600 text-xs uppercase">
                            <th class="text-left px-4 py-3 font-black">Variação (Cód/Desc)</th>
                            <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="text-center px-4 py-3 font-black"><?php echo e($loja); ?></th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $matriz; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 font-bold text-slate-800">
                                    <?php echo e($row['codigo']); ?>

                                    <span class="text-gray-400 font-medium">(<?php echo e($row['desc'] ?: ''); ?>)</span>
                                </td>

                                <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $qtd = (int)($row['lojas'][$loja] ?? 0); ?>
                                    <td class="text-center px-4 py-3 font-black <?php echo e($qtd==0 ? 'text-gray-400' : ($qtd<3 ? 'text-amber-600' : 'text-slate-800')); ?>">
                                        <?php echo e($qtd); ?>

                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            
                            <tr>
                                <td class="px-4 py-6 text-gray-500 font-bold">—</td>
                                <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="text-center px-4 py-6 font-black text-gray-400">0</td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <tfoot class="bg-slate-800 text-white">
                        <tr>
                            <td class="px-4 py-3 font-black">TOTAL</td>
                            <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <td class="text-center px-4 py-3 font-black"><?php echo e((int)($totaisPorLoja[$loja] ?? 0)); ?></td>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/estoque.blade.php ENDPATH**/ ?>