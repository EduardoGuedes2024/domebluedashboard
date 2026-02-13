

<?php $__env->startSection('title', 'Movimentação de Estoque - DomeBlue'); ?>

<?php $__env->startSection('content'); ?>


<div id="loader-overlay" 
     style="display: none; 
            position: fixed; 
            inset: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(255,255,255,0.9); 
            z-index: 999999; 
            align-items: center; 
            justify-content: center; 
            cursor: wait;">
    
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
        <img src="<?php echo e(asset('imagens/loader.gif')); ?>" alt="Carregando..." style="width: 80px; height: 80px;">
        
        <div style="text-align: center; margin-top: 20px;">
            <h3 style="font-family: sans-serif; color: #1e3a8a; font-weight: 900; margin: 0;">
                CONSULTANDO MOVIMENTAÇÕES
            </h3>
            <p style="font-family: sans-serif; color: #64748b; font-size: 14px; margin-top: 5px;">
                Aguarde, estamos organizando o histórico do produto...
            </p>
        </div>
    </div>
</div>


<header class="mb-6 bg-blue-50 p-6 rounded-xl shadow-lg border border-gray-200">

    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Movimentação de Produtos</h1>
            <p class="text-gray-500 text-sm">Histórico detalhado de entradas e saídas</p>
        </div>

        <form method="GET" action="<?php echo e(route('movimentacao_estoque')); ?>" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 min-w-[250px]">
                <label class="text-gray-400 text-xs font-bold uppercase">Código Pai</label>
                <input type="text" name="cod_produto" value="<?php echo e(request('cod_produto')); ?>" 
                       class="w-full p-2 rounded-lg border focus:ring-2 focus:ring-blue-500 outline-none" 
                       placeholder="Ex: A4731.07 ou SY0129">
            </div>

            <div class="w-full md:w-48">
                <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Loja</label>
                <select name="local" class="w-full p-2 rounded-lg border outline-none focus:ring-2 focus:ring-blue-500">
                    <?php $__currentLoopData = $locais; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php if(request('local') == $val): echo 'selected'; endif; ?>><?php echo e($nome); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="flex gap-2">
                
                <button type="submit" id="btnBuscar" class="bg-blue-600 text-white px-8 py-2 rounded-lg font-black hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                
                <a href="<?php echo e(route('movimentacao_estoque')); ?>"
                   class="bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-bold hover:bg-slate-300 transition text-center">
                    Limpar
                </a>

                
                <a href="javascript:void(0)" id="btnPdf"
                    class="bg-blue-900 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-download"></i> PDF
                </a>
            </div>
        </form>
    </div>
</header>



<?php if($gruposPorTamanho === null): ?>
    
    <div class="bg-white p-10 rounded-xl shadow-sm border border-blue-200 text-center mt-6">
        <i class="fa-solid fa-filter text-blue-300 text-5xl mb-4"></i>
        <h3 class="text-gray-700 font-bold text-xl">Pronto para pesquisar!</h3>
        <p class="text-blue-800 font-bold mt-2">Digite um código de produto para ver a movimentação por tamanhos.</p>
    </div>

<?php elseif($gruposPorTamanho->isEmpty()): ?>
    
    <section class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-8 text-center mt-6">
        <i class="fa-solid fa-circle-exclamation text-amber-500 text-3xl mb-2"></i>
        <p class="text-amber-800 font-bold">Nenhuma movimentação encontrada para o código "<?php echo e(request('cod_produto')); ?>".</p>
        <p class="text-amber-600 text-sm">Verifique o código.</p>
    </section>

<?php else: ?>
    
    <div class="grid grid-cols-1 gap-8 mt-6">
        <?php $__currentLoopData = $gruposPorTamanho; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tamanhoCodigo => $historico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                // Pegamos os dados do produto através do primeiro registro do grupo
                $produto = $historico->first(); 
            ?>

            <section class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden mb-8">
                
                <div class="bg-slate-800 px-6 py-4 flex justify-between items-center">
                    <div>
                        <span class="text-blue-400 text-[10px] font-bold uppercase tracking-widest">
                            Ref: <?php echo e($produto->cod_produto_pai); ?>

                        </span>
                        <h2 class="text-white font-black text-lg leading-tight uppercase">
                            
                            <?php echo e(mb_convert_encoding($produto->des1_produto, 'UTF-8', 'ISO-8859-1')); ?>

                            <span class="text-slate-400 text-sm ml-2">(<?php echo e($tamanhoCodigo); ?>)</span>
                        </h2>
                    </div>
                    
                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold">
                        <?php echo e(count($historico)); ?> Movimentações
                    </span>
                </div>

                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="text-slate-500 border-b">
                                <tr class="uppercase text-[11px] font-bold">
                                    <th class="text-left pb-2">Data/Hora</th>
                                    <th class="text-left pb-2">Tipo</th>
                                    <th class="text-left pb-2">Origem</th>
                                    <th class="text-left pb-2">Documento</th>
                                    
                                    <th class="text-center pb-2">Qtd</th>
                                    <th class="text-left pb-2">Observação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php $__currentLoopData = $historico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3">
                                            <span class="font-bold text-slate-700"><?php echo e(date('d/m/Y', strtotime($m->data_responsavel))); ?></span>
                                            <span class="text-slate-400 text-xs block"><?php echo e($m->hora_responsavel); ?></span>
                                        </td>
                                        <td>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black <?php echo e($m->tipo == 'E' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'); ?>">
                                                <?php echo e($m->tipo == 'E' ? 'ENTRADA' : 'SAÍDA'); ?>

                                            </span>
                                        </td>
                                        <td class="text-slate-500 text-xs font-bold"><?php echo e($m->origem); ?></td>
                                        <td class="font-medium text-slate-800"><?php echo e($m->documento_origem); ?></td>
                                        
                                        
                                        <td class="text-center font-black <?php echo e($m->quantidade < 0 ? 'text-red-600' : 'text-green-600'); ?>">
                                            <?php echo e($m->quantidade > 0 ? '+' : ''); ?><?php echo e(number_format($m->quantidade, 0)); ?>

                                            <?php echo $m->quantidade > 0 ? '<i class="fa-solid fa-arrow-up text-[10px]"></i>' : '<i class="fa-solid fa-arrow-down text-[10px]"></i>'; ?>

                                        </td>
                                        
                                        <td class="text-xs text-slate-500 italic"><?php echo e($m->observacao); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>

                            
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                                <tr>
                                    
                                    <td colspan="4" class="text-right py-4 px-4 text-[10px] font-black uppercase text-slate-500 tracking-tighter">
                                        Saldo Líquido do Período:
                                    </td>
                                    
                                    
                                    <td class="text-center py-4">
                                        <span class="text-xl font-black  <?php echo e($historico->sum('quantidade') < 1 ? 'text-red-600' : 'text-green-600'); ?>">
                                            <?php echo e($historico->sum('quantidade')); ?>

                                        </span>
                                    </td>
                                    
                                    
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>


            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loader = document.getElementById('loader-overlay');
            const btnBuscar = document.getElementById('btnBuscar');
            const btnPdf = document.getElementById('btnPdf');

            // 1. Lógica para o Botão Buscar
            if (btnBuscar) {
                btnBuscar.addEventListener('click', function() {
                    loader.style.display = 'flex'; // Ativa o loader centralizado
                });
            }

            // 2. Lógica para o Botão PDF
            if (btnPdf) {
                btnPdf.addEventListener('click', function(e) {
                    e.preventDefault(); // Evita qualquer comportamento padrão do link

                    const cod = document.querySelector('input[name="cod_produto"]').value;
                    const local = document.querySelector('select[name="local"]').value;

                    if(!cod) { 
                        alert('Digite um código antes de gerar o PDF!'); 
                        return; 
                    }

                    // Monta a URL com os filtros atuais de código e loja
                    const url = `<?php echo e(route('movimentacao.pdf')); ?>?cod_produto=${cod}&local=${local}`;
                    
                    // Ativa o loader e dispara o download
                    loader.style.display = 'flex';
                    window.location.href = url;

                    // Timer de 8 segundos para desativar o loader (tempo para o PDF ser gerado)
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 3000);
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/movimentacao_estoque.blade.php ENDPATH**/ ?>