<!-- filepath: resources/views/saler/products/products.blade.php -->


<?php $__env->startSection('title', 'Quản Lý Sheet Nhạc'); ?>

<?php $__env->startSection('content'); ?>

<!-- Products Management -->
            <div id="products" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="orbitron text-2xl font-bold text-white">Bản Nhạc Của Tôi</h3>
                        <a href="<?php echo e(route('saler.products.create')); ?>" class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                            + Thêm Sheet Mới
                        </a>
                    </div>

                    <!-- Success Message -->
                    <?php if(session('success')): ?>
                        <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4 mb-6">
                            <p class="text-green-300 font-semibold">✅ <?php echo e(session('success')); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if(session('error')): ?>
                        <div class="bg-red-500 bg-opacity-20 border border-red-500 rounded-lg p-4 mb-6">
                            <p class="text-red-300 font-semibold">❌ <?php echo e(session('error')); ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <!-- Custom Dropdown Danh Mục -->
                        <div class="relative" x-data="{ open: false, selected: 'Tất Cả', options: ['Tất Cả', 'Việt Nam', 'Nhật Bản', 'Hàn Quốc', 'Trung Quốc', 'US-UK'] }">
                            <button type="button" @click="open = !open" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 flex items-center min-w-[180px] justify-between">
                                <span x-text="selected"></span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-full bg-slate-800 text-white rounded-lg shadow-lg z-20">
                                <template x-for="option in options" :key="option">
                                    <div @click="selected = option; open = false" class="px-4 py-2 hover:bg-blue-600 cursor-pointer" x-text="option"></div>
                                </template>
                            </div>
                        </div>
                        <input type="text" placeholder="Tìm kiếm sheet nhạc..." class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 placeholder-gray-300">
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">Tên</th>
                                    <th class="text-left py-3 text-gray-300 inter">Người Soạn</th>
                                    <th class="text-left py-3 text-gray-300 inter">Danh Mục</th>
                                    <th class="text-left py-3 text-gray-300 inter">Giá</th>
                                    <th class="text-left py-3 text-gray-300 inter">Lượt Mua</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <span class="text-xl">🎵</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter"><?php echo e($product['name'] ?? 'Chưa có tên'); ?></p>
                                                <p class="text-blue-200 text-sm inter"><?php echo e($product['author'] ?? 'Chưa rõ tác giả'); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter"><?php echo e($product['transcribed_by'] ?? 'Seller'); ?></td>
                                    <td class="py-4 text-white inter"><?php echo e($product['country_region'] ?? 'Việt Nam'); ?></td>
                                    <td class="py-4 text-white inter"><?php echo e(number_format($product['price'] ?? 0)); ?>đ</td>
                                    <td class="py-4 text-white inter"><?php echo e($product['downloads_count'] ?? 0); ?></td>
                                    <td class="py-4">
                                        <span class="status-badge <?php echo e(($product['is_active'] ?? false) ? 'status-active' : 'status-inactive'); ?>">
                                            <?php echo e(($product['is_active'] ?? false) ? 'Đang bán' : 'Ngừng bán'); ?>

                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <a href="<?php echo e(route('saler.products.edit', $product['id'] ?? '')); ?>" class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-white text-sm">Sửa</a>
                                            <button onclick="confirmDelete('<?php echo e($product['id'] ?? ''); ?>', '<?php echo e(addslashes($product['name'] ?? 'sản phẩm')); ?>')" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Xóa</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-gray-400">
                                        Chưa có bản nhạc nào
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<!-- Modal xác nhận xóa -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-white border-opacity-20">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-red-500 rounded-full flex items-center justify-center mb-4">
                <span class="text-2xl">⚠️</span>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">Xác nhận xóa</h3>
            <p class="text-gray-300 mb-6">
                Bạn có chắc chắn muốn xóa bản nhạc 
                <strong id="deleteSongName" class="text-blue-300"></strong>?
            </p>
            <p class="text-red-300 text-sm mb-6">
                ⚠️ Hành động này không thể hoàn tác!
            </p>
            
            <div class="flex space-x-4">
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded-lg text-white font-semibold">
                    Hủy bỏ
                </button>
                <button onclick="executeDelete()" class="flex-1 bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white font-semibold">
                    Xóa ngay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form ẩn để gửi DELETE request -->
<form id="deleteForm" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<script>
let deleteProductId = null;

function confirmDelete(productId, productName) {
    deleteProductId = productId;
    document.getElementById('deleteSongName').textContent = productName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteProductId = null;
}

function executeDelete() {
    if (deleteProductId) {
        const form = document.getElementById('deleteForm');
        form.action = `/saler/products/${deleteProductId}`;
        form.submit();
    }
}

// Đóng modal khi click bên ngoài
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>

<?php $__env->stopSection(); ?>

<!-- Thêm Alpine.js nếu chưa có -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/saler/products/products.blade.php ENDPATH**/ ?>