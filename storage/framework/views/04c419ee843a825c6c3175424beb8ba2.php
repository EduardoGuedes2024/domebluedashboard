<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; }
        .card { border: 1px solid #ccc; margin-bottom: 20px; page-break-inside: avoid; }
        .card-header { background: #1e3a8a; color: white; padding: 8px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f3f4f6; padding: 6px; border-bottom: 1px solid #ccc; text-align: left; text-transform: uppercase; font-size: 9px; }
        td { padding: 6px; border-bottom: 1px solid #eee; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-black { font-weight: bold; }
        .entrada { color: #15803d; }
        .saida { color: #b91c1c; }
        .footer-total { background: #f9fafb; font-weight: bold; border-top: 2px solid #333; }
    </style>
</head>
<body>
    <div class="header">
        <h2> MOVIMENTAÇÃO PRODUTO - LOJA <?php echo e(strtoupper($nomeLoja)); ?></h2>
        <p>Codigo Pai: <strong><?php echo e($codigoPai); ?></strong> | Gerado em: <?php echo e(date('d/m/Y H:i')); ?></p>
    </div>

    <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tamanho => $historico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php 
        // Pegamos o primeiro registro do grupo para exibir os dados do produto
        $primeiro = $historico->first(); 
        ?>

        <div class="card">
            
            <div class="card-header">
                PRODUTO: <?php echo e($primeiro->cod_produto_pai); ?> -
                <?php echo e(mb_convert_encoding($primeiro->des1_produto, 'UTF-8', 'ISO-8859-1')); ?> 
                (<?php echo e($tamanho); ?>)
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Tipo</th>
                        <th>Documento</th>
                        <th class="text-center">Qtd</th>
                        <th>Observação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $historico; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(date('d/m/Y', strtotime($m->data_responsavel))); ?> <?php echo e($m->hora_responsavel); ?></td>
                        <td class="<?php echo e($m->tipo == 'E' ? 'entrada' : 'saida'); ?>">
                            <?php echo e($m->tipo == 'E' ? 'ENTRADA' : 'SAÍDA'); ?>

                        </td>
                        <td><?php echo e($m->documento_origem); ?></td>
                        <td class="text-center font-black <?php echo e($m->quantidade < 0 ? 'saida' : 'entrada'); ?>">
                            <?php echo e($m->quantidade > 0 ? '+' : ''); ?><?php echo e((int)$m->quantidade); ?>

                        </td>
                        <td style="font-size: 8px; font-style: italic;"><?php echo e($m->observacao); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot class="footer-total">
                    <tr>
                        <td colspan="3" class="text-right">SALDO LÍQUIDO DO PERÍODO:</td>
                        <td class="text-center" style="font-size: 14px;">
                            <?php echo e($historico->sum('quantidade')); ?>

                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


</body>
</html><?php /**PATH C:\domeblue-dash\resources\views/relatorios/movimentacao_pdf.blade.php ENDPATH**/ ?>