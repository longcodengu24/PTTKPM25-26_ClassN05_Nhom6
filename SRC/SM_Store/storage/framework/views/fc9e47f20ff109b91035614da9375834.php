<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Sky Music Store'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <script src="<?php echo e(asset('js/navbar.js')); ?>"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/animation.css')); ?>">
    
</head>
<body class="sky-gradient min-h-screen overflow-x-hidden">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="cloud cloud1"></div>
        <div class="cloud cloud2"></div>
        <div class="cloud cloud3"></div>
        <div class="cloud cloud4"></div>
        <div class="cloud cloud5"></div>
        <div class="star w-2 h-2" style="top: 15%; left: 20%; animation-delay: 0s;"></div>
        <div class="star w-1 h-1" style="top: 25%; left: 80%; animation-delay: 1s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 35%; left: 60%; animation-delay: 0.5s;"></div>
        <div class="star w-1 h-1" style="top: 45%; left: 30%; animation-delay: 1.5s;"></div>
        <div class="star w-2 h-2" style="top: 55%; left: 90%; animation-delay: 0.8s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 10%; left: 50%; animation-delay: 0.3s;"></div>
        <div class="star w-1 h-1" style="top: 60%; left: 10%; animation-delay: 1.2s;"></div>
        <div class="star w-2 h-2" style="top: 70%; left: 70%; animation-delay: 0.6s;"></div>
        <div class="star w-1 h-1" style="top: 80%; left: 40%; animation-delay: 1.7s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 20%; left: 75%; animation-delay: 0.9s;"></div>
        <div class="star w-2 h-2" style="top: 30%; left: 10%; animation-delay: 1.1s;"></div>
    </div>

    <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/layouts/app.blade.php ENDPATH**/ ?>