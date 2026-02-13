

<?php $__env->startSection('title', 'Vendas E-commerce por UF'); ?>

<?php $__env->startSection('content'); ?>


<div id="loader-overlay" style="display: none; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 9999; align-items: center; justify-content: center;">
    <div class="flex flex-col items-center">
        <img src="<?php echo e(asset('imagens/loader.gif')); ?>" alt="Carregando..." class="w-12 h-12">
        <h3 class="text-blue-900 font-black mt-4 uppercase">Processando Vendas por Estado</h3>
    </div>
</div>


<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-lg border border-gray-200 ">
    
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ecommerce por UF</h1>
            <p class="text-gray-500 text-sm">Distribuição de faturamento com participação percentual</p>
        </div>

        <form method="GET" action="<?php echo e(route('ecommerce_Uf')); ?>" id="formFiltro" class="flex flex-col md:flex-row gap-4 items-end">

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Início</label>
                <input type="date" name="data_inicio" value="<?php echo e($data_inicio); ?>" class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Fim</label>
                <input type="date" name="data_fim" value="<?php echo e($data_fim); ?>" class="border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex gap-2">

                <button type="submit" class="self-end bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-slate-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrar
                </button>

                
                <a href="javascript:void(0)" id="btnPdf"
                    class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-download"></i> PDF
                </a>
                
            </div>
        </form>
    </div>
</header>

<div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
    <div class="flex items-center gap-2 mb-6 border-b pb-4">
        <div class="bg-blue-600 w-2 h-6 rounded-full"></div>
        <h3 class="font-black text-slate-800 uppercase text-sm tracking-widest">Ranking e Participação </h3>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        
        <div class="lg:col-span-7 border rounded-xl overflow-hidden">
            <?php $totalGeral = $vendasUf->sum('total_vendas'); ?>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-100 text-slate-500 font-black text-[10px] uppercase">
                    <tr>
                        <th class="px-4 py-4">Estado (UF)</th>
                        <th class="px-4 py-4 text-right">Faturamento</th>
                        <th class="px-4 py-4 text-center w-24">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $vendasUf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 

                        $percentual = $totalGeral > 0 ? ($venda->total_vendas / $totalGeral) * 100 : 0; 

                    ?>
                    <tr class="hover:bg-blue-50/50 transition">
                        <td class="px-4 py-4 font-bold text-slate-700">
                            <span class="bg-slate-800 text-white text-[10px] px-2 py-1 rounded mr-2"><?php echo e($venda->entrega_uf); ?></span>
                            Estado de <?php echo e($venda->entrega_uf); ?>

                        </td>
                        <td class="px-4 py-4 text-right font-black text-slate-900">
                            R$ <?php echo e(number_format($venda->total_vendas, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-4 text-center font-black text-blue-600 bg-blue-50/30">
                            <?php echo e(number_format($percentual, 1)); ?>%
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                
                <tfoot class="bg-slate-800 text-white font-black text-sm">
                    <tr>
                        <td class="px-4 py-4 uppercase tracking-wider">
                            Total Geral Acumulado
                        </td>
                        <td class="px-4 py-4 text-right text-base border-l border-slate-700">
                            R$ <?php echo e(number_format($totalGeral, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-4 text-center bg-blue-600">
                            100%
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        
        <div class="lg:col-span-5 flex flex-col bg-slate-50 rounded-xl p-6 border border-dashed border-slate-200">
            <h3 class="font-black text-slate-400 mb-6 uppercase text-[11px] text-center tracking-tighter">Visualização de Saídas</h3>
            
            <div class="relative w-full" style="height: 300px;">
                <canvas id="ufChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ufChart').getContext('2d');
        
        // Garantir que os dados sejam números decimais para o JS
        const dataValues = <?php echo json_encode($vendasUf->pluck('total_vendas')); ?>.map(v => parseFloat(v));
        const labels = <?php echo json_encode($vendasUf->pluck('entrega_uf')); ?>;
        const total = dataValues.reduce((a, b) => a + b, 0);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: ['#0f172a', '#1e3a8a', '#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe', '#f1f5f9', '#cbd5e1'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            plugins: [ChartDataLabels], // Ativa a porcentagem dentro da fatia
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 11 },
                        formatter: (value) => {
                            return ((value / total) * 100).toFixed(1) + "%";
                        },
                        display: (context) => (context.dataset.data[context.dataIndex] / total) > 0.04 // Esconde fatias minúsculas
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw;
                                const p = ((val / total) * 100).toFixed(1);
                                // Limpa o valor para R$ padrão brasileiro
                                const formatado = val.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                                return ` ${context.label}: ${formatado} (${p}%)`;
                            }
                        }
                    }
                }
            }
        });
    });

    document.getElementById('btnPdf').addEventListener('click', function() {
        // Pega o formulário de filtros
        const form = document.getElementById('formFiltro');
        
        // Cria um objeto com os dados atuais (Data Início e Fim)
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        
        // Monta a URL da rota que criamos no Controller
        const url = "<?php echo e(route('ecommerce_Uf.export.pdf')); ?>?" + params;
        
        // Abre o PDF em uma nova aba (Padrão DOME BLUE)
        window.open(url, '_blank');
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/ecommerce_uf.blade.php ENDPATH**/ ?>