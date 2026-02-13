

<?php $__env->startSection('title', 'Ranking de Vendas Geral - DomeBlue'); ?>

<?php $__env->startSection('content'); ?>

<?php
    $brl = fn($v) => 'R$ ' . number_format((float)($v ?? 0), 2, ',', '.');
    
    $opcoes = [
        '' => 'Todas as Lojas',
        
    ];
?>


<div id="loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 9999; align-items: center; justify-content: center; cursor: wait;">
    
    <div class="flex flex-col items-center">
        
        <img src="<?php echo e(asset('imagens/loader.gif')); ?>" alt="Carregando..." class="w-12 h-12">
        
        <h3 class="text-blue-900 font-black mt-4">
            PROCESSANDO RANKING GERAL
        </h3>
        <p class="text-slate-500 text-sm">
            Analisando performance consolidada de todas as lojas...
        </p>
    </div>
</div>


<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Vendas Geral</h1>
            <p class="text-gray-500 text-sm">Ranking de performance por volume de saída</p>
        </div>

        <form method="GET" action="<?php echo e(route('vendas')); ?>" id="formVendas" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:items-end gap-3">
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Início</label>
                <input type="date" name="data_inicio" value="<?php echo e(request('data_inicio', now()->startOfMonth()->format('Y-m-d'))); ?>" 
                       class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Fim</label>
                <input type="date" name="data_fim" value="<?php echo e(request('data_fim', now()->format('Y-m-d'))); ?>" 
                       class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            
            <div class="w-full sm:w-48">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Empresa</label>
                <select name="empresa" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="" <?php if(request('empresa') == ''): echo 'selected'; endif; ?>>Todas as Empresas</option>
                    <option value="Amissima" <?php if(request('empresa') == 'Amissima'): echo 'selected'; endif; ?>>Amissima</option>
                    <option value="Syssa" <?php if(request('empresa') == 'Syssa'): echo 'selected'; endif; ?>>Syssa</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Loja</label>
                <select name="loja" class="w-full border rounded-lg p-2 text-sm">
                    <?php $__currentLoopData = $opcoes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('loja') == $val): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="flex gap-2">

                
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>

                
                <a href="<?php echo e(route('vendas')); ?>" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition">
                    Limpar
                </a>

            </div>
        </form>
    </div>
</header>

        

<?php if(empty($cards)): ?>

    
    <div class="bg-white p-12 rounded-xl border border-blue-100 text-center shadow-sm">
        <i class="fa-solid fa-chart-line text-blue-200 text-6xl mb-4"></i>
        <h3 class="text-slate-700 font-black text-xl">Pronto para pesquisar!</h3>
        <p class="text-blue-600 font-bold mt-2">Ajuste os filtros para carregar o ranking.</p>
    </div>

<?php else: ?>

    <div class="space-y-6">

        <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            // amissima por padrão e syssa se o cod_pai começar com "sy"
                $refid = $card['refid'] ?? null;

                $isSy = str_starts_with(strtolower($card['produto_pai']), 'sy');
                
                $imgUrl = null;
                if ($refid) {
                    $imgUrl = $isSy 
                        ? "https://syssaoficial.com.br/imgitens/{$refid}_0.webp"
                        : "https://www.amissima.com.br/imgitens/{$refid}_0.webp";
                }
            ?>


            <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                
                <div class="px-6 py-4 bg-slate-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
                    
                    <div class="flex items-center gap-3">
                        
                        <span class="bg-slate-800 text-white text-[13px] font-black px-3 py-1 rounded-full uppercase">
                            Ref: <?php echo e($card['produto_pai']); ?>

                        </span>
                        
                        <h2 class="text-lg font-black text-slate-800 uppercase">
                            <?php echo e(mb_convert_encoding($card['descricao'], 'UTF-8', 'ISO-8859-1')); ?>

                        </h2>
                    
                    </div>
                    
                    
                    <div class="flex gap-2">

                        <span class="text-[12px] font-bold px-3 py-1 bg-white border border-gray-200 rounded-full">
                            VAREJO: <span class="text-slate-900 font-black"><?php echo e($brl($card['preco_v'] ?? 0)); ?></span>
                        </span>

                        <span class="text-[12px] font-bold px-3 py-1 bg-white border border-gray-200 rounded-full">
                            ATACADO: <span class="text-slate-900 font-black"><?php echo e($brl($card['preco_a'] ?? 0)); ?></span>
                        </span>

                        <span class="text-[12px] font-bold px-3 py-1 bg-blue-100 text-blue-800 rounded-full border border-blue-200">
                            <?php echo e($card['grupo']); ?> / <?php echo e($card['subgrupo']); ?>

                        </span>

                        <span class="text-[13px] font-black px-3 py-1 bg-slate-800 text-white rounded-full">
                            RANK: #<?php echo e($loop->iteration + ($pagination->firstItem() - 1)); ?>

                        </span>
                    
                    </div>

                </div>

                <div class="p-6 space-y-6">

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                        
                        <div class="lg:col-span-2">

                            <div class="bg-white border border-gray-200 rounded-xl p-3 h-full flex items-center justify-center overflow-hidden">

                                <?php if($imgUrl): ?>
                                    <img src="<?php echo e($imgUrl); ?>" 
                                         alt="Foto Produto"
                                         class="w-full h-full object-contain max-h-[360px] rounded-lg"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    
                                    <div class="w-full h-[260px] rounded-lg bg-slate-100 flex items-center justify-center text-gray-400 font-bold" style="display: none;">
                                        Sem foto
                                    </div>
                                <?php else: ?>
                                    <div class="w-full h-[260px] rounded-lg bg-slate-100 flex items-center justify-center text-gray-400 font-bold">
                                        Sem foto
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        
                        <div class="lg:col-span-10">

                            <div class="border rounded-xl overflow-hidden bg-white">

                                <table class="w-full text-sm">

                                    <thead class="bg-slate-100 border-b">

                                        <tr class="text-slate-500 text-[10px] uppercase font-black">

                                            <th class="px-4 py-3 text-left">Variação (Cód/Desc)</th>
                                            <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="px-4 py-3 text-center"><?php echo e($loja); ?></th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <th class="px-4 py-3 text-center bg-blue-50 text-blue-700 font-black">Total Vendas</th>

                                        </tr>

                                    </thead>

                                    <tbody class="divide-y divide-gray-100">

                                        <?php $__currentLoopData = $card['matriz']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                            <tr class="hover:bg-blue-50/30 transition">

                                                <td class="px-4 py-3 font-bold text-slate-800">
                                                    <?php echo e($row['codigo']); ?>

                                                    <span class="text-slate-400 font-medium ml-1">
                                                        (<?php echo e(mb_convert_encoding($row['desc'] ?? '', 'UTF-8', 'ISO-8859-1')); ?>)
                                                    </span>
                                                </td>

                                                <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                    <td class="text-center px-4 py-3 font-black <?php echo e(($row['lojas'][$loja] ?? 0) > 0 ? 'text-slate-800' : 'text-slate-300'); ?>">
                                                        <?php echo e($row['lojas'][$loja] ?? 0); ?>

                                                    </td>

                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                <td class="px-4 py-3 text-center font-black bg-blue-50 text-blue-700">
                                                    <?php echo e($row['total_linha'] ?? 0); ?>

                                                </td>

                                            </tr>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </tbody>

                                    <tfoot class="bg-slate-800 text-white font-black text-xs">
                                        <tr>
                                            <td class="px-4 py-3">TOTAIS POR UNIDADE</td>
                                            <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <td class="text-center px-4 py-3">
                                                    <?php echo e(collect($card['matriz'])->sum(fn($r) => $r['lojas'][$loja] ?? 0)); ?>

                                                </td>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <td class="text-center px-4 py-3 bg-blue-600">
                                                <?php echo e($card['total_geral']); ?>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <div class="mt-8">
            <?php echo e($pagination->links()); ?>

        </div>

    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loader = document.getElementById('loader-overlay');
        const form = document.getElementById('formVendas');

        form.addEventListener('submit', () => {
            loader.style.display = 'flex';
        });

        // Aplicar loader na navegação de páginas
        document.querySelectorAll('nav[role="navigation"] a').forEach(link => {
            link.addEventListener('click', () => {
                loader.style.display = 'flex';
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/vendas.blade.php ENDPATH**/ ?>