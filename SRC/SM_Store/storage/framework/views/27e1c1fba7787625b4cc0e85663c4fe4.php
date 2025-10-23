<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Tài Khoản - Sky Music Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <style>
        
        .sky-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .orbitron { font-family: 'Orbitron', monospace; }
        .inter { font-family: 'Inter', sans-serif; }

        .profile-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tab-button.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fbbf24;
        }
    </style>
</head>
<body class="sky-gradient min-h-screen">
    <?php echo $__env->make('components.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Clean & Minimal Design -->
    <main class="max-w-6xl mx-auto px-6 py-8">
        <!-- Profile Header -->
        <div class="profile-card rounded-lg p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <!-- Avatar -->
                <img src="<?php echo e(isset($currentUser) ? $currentUser['avatar'] : session('avatar', '/img/default-avatar.png')); ?>" 
                     alt="Avatar" 
                     class="w-16 h-16 rounded-full border-2 border-white/30">
                
                <!-- User Info -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="orbitron text-xl font-bold text-white"><?php echo e(isset($currentUser) ? $currentUser['name'] : session('name', 'User')); ?></h1>
                    <p class="inter text-gray-300 text-sm"><?php echo e(isset($currentUser) ? $currentUser['email'] : session('email', '')); ?></p>
                </div>
                
                <!-- Coins -->
                <div class="bg-black/20 rounded-lg px-4 py-2">
                    <span class="orbitron text-lg font-bold text-yellow-300">
                        🪙 <?php echo e(number_format(isset($currentUser) ? $currentUser['coins'] : session('coins', 0))); ?>

                    </span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mb-6">
            <?php if(session('role') === 'saler'): ?>
            <a href="<?php echo e(route('saler.dashboard')); ?>" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inter font-medium transition-colors">
                🛒 Seller Panel
            </a>
            <?php endif; ?>
            
            <a href="<?php echo e(route('account.deposit')); ?>" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inter font-medium transition-colors">
                💰 Nạp Coins
            </a>
            
            <a href="<?php echo e(route('account.withdraw')); ?>" 
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg inter font-medium transition-colors">
                💸 Rút Coins
            </a>
            
            <a href="<?php echo e(route('account.settings')); ?>" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg inter font-medium transition-colors">
                ⚙️ Cài đặt
            </a>
        </div>

        <!-- Content -->
        <div class="profile-card rounded-lg p-6">
            <!-- Tabs -->
            <div class="flex gap-1 mb-6 bg-black/20 rounded-lg p-1">
                <a href="<?php echo e(route('account.sheets')); ?>" 
                   class="tab-button <?php if(request()->routeIs('account.sheets')): ?> active <?php endif; ?> px-4 py-2 rounded-md inter font-medium transition-all flex-1 text-center text-white">
                    📜 Sheet Nhạc
                </a>
                <a href="<?php echo e(route('account.activity')); ?>" 
                   class="tab-button <?php if(request()->routeIs('account.activity')): ?> active <?php endif; ?> px-4 py-2 rounded-md inter font-medium transition-all flex-1 text-center text-white">
                    📊 Hoạt Động
                </a>
            </div>
            
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>
</body>
</html><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/layouts/account.blade.php ENDPATH**/ ?>