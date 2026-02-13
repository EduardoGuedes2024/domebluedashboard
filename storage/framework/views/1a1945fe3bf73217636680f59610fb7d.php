

<?php $__env->startSection('title', 'Vendas - DomeBlue'); ?>

<?php $__env->startSection('content'); ?>

        
            
            <header class="mb-8 bg-blue-50 p-6 rounded-xl shadow-sm border border-gray-200">

                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">

                    
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Resumo de Performance</h1>
                        <p class="text-gray-500 text-sm">
                            Olá <span class="font-bold text-gray-800"><?php echo e(auth()->user()->nome ?? 'Usuário'); ?></span> , visualize os dados e Performance de cada Loja
                        </p>
                    </div>

                    
                    <div class="flex flex-col items-end gap-3">


                        
                        <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="flex flex-wrap items-end justify-end gap-3">

                            
                            <div>
                                <label class="block text-xs font-bold text-black-800 uppercase mb-1">Atual Início</label>
                                <input type="date"
                                    name="atual_inicio"
                                    value="<?php echo e(request('atual_inicio', $atual_inicio)); ?>"
                                    class="border rounded-lg p-2 text-sm text-black-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-black-800 uppercase mb-1">Atual Fim</label>
                                <input type="date"
                                    name="atual_fim"
                                    value="<?php echo e(request('atual_fim', $atual_fim)); ?>"
                                    class="border rounded-lg p-2 text-sm text-black-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            

                            
                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Anterior Início</label>
                                <input type="date"
                                    name="anterior_inicio"
                                    value="<?php echo e(request('anterior_inicio', $anterior_inicio)); ?>"
                                    class="border rounded-lg p-2 text-sm text-gray-600 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Anterior Fim</label>
                                <input type="date"
                                    name="anterior_fim"
                                    value="<?php echo e(request('anterior_fim', $anterior_fim)); ?>"
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
            <?php if(isset($conexaoStatus)): ?>
            <div style="padding:10px; border:1px solid #ccc; margin-bottom:12px;">
                <strong>Status:</strong> <?php echo e($conexaoStatus); ?>

            </div>
            <?php endif; ?> --->


            <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-10">
                <?php $__currentLoopData = $lojas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if (isset($component)) { $__componentOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3 = $attributes; } ?>
<?php $component = App\View\Components\CardIndicador::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card-indicador'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\CardIndicador::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['titulo' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['nome']),'valor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('R$ ' . number_format($loja['atual'], 2, ',', '.')),'anterior' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('R$ ' . number_format($loja['anterior'], 2, ',', '.')),'cor' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['cor']),'icone' => 'store','porcentagem' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($loja['atual'] > $loja['anterior'] ? '+' : '-') ),'pedidosAtual' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['pedidos_atual']),'clientesAtual' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['clientes_atual']),'pedidosAnterior' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['pedidos_anterior']),'clientesAnterior' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($loja['clientes_anterior'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3)): ?>
<?php $attributes = $__attributesOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3; ?>
<?php unset($__attributesOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3)): ?>
<?php $component = $__componentOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3; ?>
<?php unset($__componentOriginal8a5bdb4cad5b20bb98bbbe409f9fe3f3); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
                                <?php $__currentLoopData = $financeiro; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-3 font-medium"><?php echo e($item['forma']); ?></td>
                                    <td class="py-3 text-center"><?php echo e($item['qtd']); ?></td>
                                    <td class="py-3 text-right font-bold">R$ <?php echo e(number_format($item['valor'], 2, ',', '.')); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-slate-50">
                                    <td colspan="2" class="py-3 px-2 font-bold text-gray-800">TOTAL GERAL</td>
                                    <td class="py-3 px-2 text-right font-black text-blue-600 text-lg">
                                        R$ <?php echo e(number_format(array_sum(array_column($financeiro, 'valor')), 2, ',', '.')); ?>

                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const graficoVendasAtual = <?php echo json_encode($graficoVendasAtual, 15, 512) ?>;

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

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/dashboard.blade.php ENDPATH**/ ?>