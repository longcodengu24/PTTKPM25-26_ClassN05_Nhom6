<?php $__env->startSection('title', 'Đăng nhập'); ?>

<?php $__env->startSection('content'); ?>
<div id="login" class="page-content">
    <section class="relative z-10 py-20 px-6">
        <div class="max-w-md mx-auto">
            <div class="game-card rounded-xl p-8">
                <h2 class="orbitron text-3xl font-bold text-white text-center mb-8">🔐 Đăng Nhập</h2>
                

                <!-- //hien thi loi -->
                <?php if($errors->any()): ?>
    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
        <?php echo e(session('success')); ?>

    </div>
<?php endif; ?>

                <form action="<?php echo e(route('login.submit')); ?>" method="POST" class="space-y-6">

                    <?php echo csrf_field(); ?> 
                    <div>
                        <label class="inter text-white block mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email của bạn" required>
                    </div>
                    <div>
                        <label class="inter text-white block mb-2">Mật khẩu</label>
                        <input type="password" name="password" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập mật khẩu" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-blue-200">
                            <input type="checkbox" name="remember" class="mr-2">
                            <span class="inter text-sm">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="<?php echo e(url('/forgot-password')); ?>" class="inter text-sm text-yellow-300 hover:text-yellow-200">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="glow-button w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-full font-semibold">Đăng Nhập</button>
                </form>
                
                <div class="text-center mt-6">
                    <p class="inter text-blue-200">Chưa có tài khoản? <a href="<?php echo e(url('/register')); ?>" class="text-yellow-300 hover:text-yellow-200">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/auth/login.blade.php ENDPATH**/ ?>