<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <style>
        /* Configuração de Margens para o Header e Footer fixos */
        @page { margin: 100px 25px 60px 25px; }

        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }

        /* CABEÇALHO FIXO (Identidade igual à Movimentação) */
        .header { position: fixed; top: -80px; left: 0; right: 0; text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; height: 70px; }
        .header h2 { margin: 0; font-size: 18px; color: #333; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 5px 0 0 0; font-size: 11px; color: #444; }

        /* RODAPÉ FIXO (Paginação) */
        .footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 30px; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }

        /* CARD DO PRODUTO */
        .card { border: 1px solid #ddd; margin-bottom: 25px; page-break-inside: avoid; border-radius: 4px; overflow: hidden; }
        
        /* LINHA DE TÍTULO AZUL (Igual ao da Movimentação) */
        .card-header { background-color: #1e3a8a; color: white; padding: 8px 12px; font-weight: bold; font-size: 11px; }

        .row { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; padding: 10px; }
        .col-img { width: 130px; border-right: 1px solid #eee; text-align: center; background-color: #fafafa; }
        
        .imgbox img { max-width: 120px; max-height: 160px; object-fit: contain; }

        /* INFORMAÇÕES E TABELA */
        .info-header { margin-bottom: 8px; }
        .precos { margin-top: 4px; font-size: 10px; color: #555; }
        .precos strong { color: #000; }

        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background-color: #f3f4f6; color: #444; font-size: 8px; text-transform: uppercase; padding: 5px; border: 1px solid #ccc; }
        td { padding: 5px; border: 1px solid #eee; text-align: center; font-size: 10px; }
        
        /* Alinhamento da Variação */
        .td-variacao { text-align: left; font-weight: bold; width: 35%; background-color: #fcfcfc; }
        .td-total { font-weight: 900; background-color: #f1f5f9; color: #1e3a8a; }

    </style>
</head>
<body>

    
    <div class="header">
        <h2>RELATÓRIO ESTOQUE - LOJA <?php echo e(strtoupper($localLabel)); ?></h2>
        <p>Gerado em: <?php echo e(date('d/m/Y H:i')); ?> | Sistema <strong>DOME BLUE</strong></p>
    </div>

    
    <div class="footer">
        DomeBlue Dashboard - Relatório Geral de Disponibilidade
    </div>

    
    <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $p = $c['produto']; ?>
        <div class="card">
            
            <div class="card-header">
                REF: <?php echo e($p->cod_produto_pai ?? '-'); ?> — <?php echo e(mb_convert_encoding($p->des_produto, 'UTF-8', 'ISO-8859-1')); ?>

            </div>

            <div class="row">
                
                <div class="col col-img">
                    <div class="imgbox">
                        <?php if(!empty($c['imgLocal'])): ?>
                            <img src="<?php echo e($c['imgLocal']); ?>" alt="foto">
                        <?php else: ?>
                            <div style="padding-top: 70px; color:#ccc; font-size: 8px;">SEM FOTO</div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="col">
                    <div class="precos">
                        <strong>Varejo:</strong> R$ <?php echo e(number_format((float)($p->preco_01 ?? 0), 2, ',', '.')); ?>

                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <strong>Atacado:</strong> R$ <?php echo e(number_format((float)($p->preco_02 ?? 0), 2, ',', '.')); ?>

                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th>Variação</th>
                                <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                    <th><?php echo e($loja); ?></th> 
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $c['matriz']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="td-variacao"><?php echo e($m['variacao']); ?></td>
                                    <?php $__currentLoopData = $lojasFixas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loja): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td><?php echo e($m['lojas'][$loja] ?? 0); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td class="td-total"><?php echo e($m['total'] ?? 0); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $y = $pdf->get_height() - 35;
            $x = $pdf->get_width() - 100;
            $pdf->page_text($x, $y, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size, array(0,0,0));
        }
    </script>

</body>
</html><?php /**PATH C:\domeblue-dash\resources\views/relatorios/estoque_lojas_pdf.blade.php ENDPATH**/ ?>