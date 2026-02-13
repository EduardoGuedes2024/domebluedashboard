

<?php $__env->startSection('title', 'Home - DomeBlue'); ?>

<?php $__env->startSection('content'); ?>

<div class="flex items-center justify-center min-h-screen">

    <div class="text-center">

        <img src="<?php echo e(asset('imagens/logodomeblueazul.png')); ?>"
             class="mx-auto h-24 mb-4">

        <h1 class="text-5xl font-black text-blue-700">
            DomeBlue
        </h1>

        <p class="text-gray-500 mt-3">
            Sistema de Gestão
        </p>

    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\domeblue-dash\resources\views/home.blade.php ENDPATH**/ ?>