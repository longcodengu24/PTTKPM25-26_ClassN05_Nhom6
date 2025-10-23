
<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-2xl shadow-2xl text-white border border-white/30">
        <h2 class="text-3xl font-bold mb-6 text-center text-white">Thanh toán đơn hàng</h2>

        <!-- Tóm tắt đơn hàng -->
        <div id="orderSummary" class="mb-6 border-b border-white/30 pb-4">
            <h3 class="text-lg font-semibold mb-2">Tóm tắt đơn hàng</h3>
            <p><strong>Số sản phẩm:</strong> <span id="itemCount">0</span></p>
            <p><strong>Tổng tiền:</strong> <span id="totalAmount">0đ</span></p>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">Chọn phương thức thanh toán</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3">
                    <input type="radio" name="paymentMethod" value="coins" checked class="text-blue-500">
                    <span>Thanh toán bằng Sky Coins</span>
                </label>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="flex justify-between items-center">
            <a href="<?php echo e(route('shop.cart')); ?>" class="text-blue-400 underline">← Quay lại giỏ hàng</a>
            <button id="confirmPaymentBtn" class="bg-gradient-to-r from-blue-500 to-cyan-400 px-6 py-3 rounded-lg font-bold text-white hover:from-blue-600 hover:to-cyan-500">
                XÁC NHẬN THANH TOÁN
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Lấy dữ liệu từ sessionStorage (được lưu ở cart.blade.php)
    const cart = JSON.parse(sessionStorage.getItem('checkout_cart') || '[]');

    // ✅ Tính tổng tiền
    const total = cart.reduce((sum, item) => {
        const price = parseInt(item.price || 0);
        return sum + price * (item.quantity || 1);
    }, 0);

    // ✅ Cập nhật giao diện
    document.getElementById('itemCount').innerText = cart.length;
    document.getElementById('totalAmount').innerText = total.toLocaleString('vi-VN') + 'đ';

    const confirmBtn = document.getElementById('confirmPaymentBtn');
    confirmBtn.addEventListener('click', async function() {
        if (cart.length === 0) {
            alert('Giỏ hàng trống!');
            return;
        }

        const method = document.querySelector('input[name="paymentMethod"]:checked').value;
        confirmBtn.disabled = true;
        confirmBtn.innerText = 'ĐANG XỬ LÝ...';

        if (method === 'coins') {
            try {
                const response = await fetch('<?php echo e(route("account.paycart.confirm")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({ cart_items: cart })
                });

                const data = await response.json();

                if (data.success) {
                    sessionStorage.removeItem('checkout_cart');
                    alert('✅ Thanh toán thành công! Số dư còn lại: ' + data.data.remaining_coins.toLocaleString('vi-VN') + ' xu.');
                    window.location.href = '<?php echo e(route("account.sheets")); ?>';
                } else {
                    alert('❌ ' + data.message);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = 'XÁC NHẬN THANH TOÁN';
                }
            } catch (err) {
                alert('Lỗi kết nối máy chủ!');
                confirmBtn.disabled = false;
                confirmBtn.innerText = 'XÁC NHẬN THANH TOÁN';
            }
        }
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/account/paycart.blade.php ENDPATH**/ ?>