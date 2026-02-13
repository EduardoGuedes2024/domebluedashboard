<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; color: #1e293b; }
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .periodo { font-size: 10px; color: #64748b; font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f8fafc; color: #475569; text-align: left; padding: 10px; font-size: 9px; text-transform: uppercase; border: 1px solid #e2e8f0; }
        td { padding: 10px; border: 1px solid #e2e8f0; font-size: 11px; font-weight: bold; }
        
        .total-row { background: #1e3a8a; color: white; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uf-badge { background: #0f172a; color: white; padding: 2px 5px; border-radius: 3px; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">DOME BLUE - Vendas E-commerce por Estado</div>
        <div class="periodo">Período Analisado: <?php echo e(date('d/m/Y', strtotime($data_inicio))); ?> até <?php echo e(date('d/m/Y', strtotime($data_fim))); ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Estado de Entrega</th>
                <th class="text-right">Faturamento Total</th>
                <th class="text-center">Participação %</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $vendasUf; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $venda): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $percentual = $totalGeral > 0 ? ($venda->total_vendas / $totalGeral) * 100 : 0; ?>
            <tr>
                <td><span class="uf-badge"><?php echo e($venda->entrega_uf); ?></span> Estado de <?php echo e($venda->entrega_uf); ?></td>
                <td class="text-right">R$ <?php echo e(number_format($venda->total_vendas, 2, ',', '.')); ?></td>
                <td class="text-center"><?php echo e(number_format($percentual, 1, ',', '.')); ?>%</td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTAL GERAL ACUMULADO</td>
                <td class="text-right">R$ <?php echo e(number_format($totalGeral, 2, ',', '.')); ?></td>
                <td class="text-center"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer mt-5">
        Relatório gerado em <?php echo e(date('d/m/Y H:i')); ?> | Sistema DOME BLUE Dashboard
    </div>
    
</body>
</html><?php /**PATH C:\domeblue-dash\resources\views/relatorios/ecommerce_Uf_pdf.blade.php ENDPATH**/ ?>