<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title>Login - DomeBlue</title>

        <!-- Fonts -->
        <link rel="icon" type="image/png" href="<?php echo e(asset('imagens/favicondomeblueazul.png')); ?>">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>

    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex items-center justify-center bg-center bg-cover bg-no-repeat""
                style="
                    background-image: url('<?php echo e(asset('imagens/background.jpg')); ?>');
                    background-repeat: no-repeat;
                    background-size: cover;
                    background-position: center;
                    ">

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-blue-100 shadow-md overflow-hidden sm:rounded-lg">
                <?php echo e($slot); ?>

            </div>
        </div>
    </body>
</html>
<?php /**PATH C:\xampp\domebluedashboard\resources\views/layouts/guest.blade.php ENDPATH**/ ?>