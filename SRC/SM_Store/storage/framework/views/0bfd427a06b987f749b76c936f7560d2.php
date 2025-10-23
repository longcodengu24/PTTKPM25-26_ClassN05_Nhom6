<?php $__env->startSection('content'); ?>
<div class="profile-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="orbitron text-xl font-bold text-white">
            Sheet Nhạc Đã Mua (<?php echo e($totalPurchasedProducts ?? 0); ?>)
            <?php if(config('app.debug')): ?>
                <small class="text-xs text-blue-200 block">
                    UID: <?php echo e(session('firebase_uid', 'NULL')); ?>

                </small>
            <?php endif; ?>
        </h3>
        <a href="<?php echo e(route('saler.products.create')); ?>" 
           class="glow-button bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inter font-semibold">
            + Tạo Product Mới
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="bg-white/10 text-white/90 uppercase text-xs tracking-wider">
                    <th class="py-3 px-4 rounded-l-xl font-semibold">Tên Sheet</th>
                    <th class="py-3 px-4 font-semibold">Người Soạn</th>
                    <th class="py-3 px-4 font-semibold">Danh Mục</th>
                    <th class="py-3 px-4 font-semibold">Giá</th>
                    <th class="py-3 px-4 rounded-r-xl font-semibold text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($purchasedProducts) && $purchasedProducts->count() > 0): ?>
                    <?php $__currentLoopData = $purchasedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="bg-white/10 hover:bg-white/20 transition-all duration-300">
                        <td class="py-3 px-4 text-white">
                            <div class="orbitron font-bold leading-tight"><?php echo e($product['title'] ?? 'Chưa có tên'); ?></div>
                            <div class="inter text-xs text-blue-100 mt-1"><?php echo e($product['description'] ?? ''); ?></div>
                        </td>
                        <td class="py-3 px-4 text-white">
                            <?php echo e($product['seller_name'] ?? 'Không rõ người soạn'); ?>

                        </td>
                        <td class="py-3 px-4 text-white">
                            <?php echo e($product['category'] ?? 'Chưa phân loại'); ?>

                        </td>
                        <td class="py-3 px-4 text-white orbitron font-semibold">
                            <?php echo e(number_format($product['price'] ?? 0)); ?>đ
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="<?php echo e(asset($product['file_url'] ?? '#')); ?>" 
                               target="_blank"
                               class="px-4 py-1 rounded bg-green-500 hover:bg-green-600 text-white font-semibold shadow inline-block text-center transition">
                                Tải về
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-5 text-center text-white/60">
                            Bạn chưa mua sheet nhạc nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.account', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/account/sheets.blade.php ENDPATH**/ ?>