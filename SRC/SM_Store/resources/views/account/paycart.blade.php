@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white/10 backdrop-blur-2xl p-8 rounded-2xl shadow-2xl text-white border border-white/30">
        <h2 class="text-3xl font-bold mb-6 text-center text-white">Thanh toán đơn hàng</h2>

        <!-- Danh sách sản phẩm -->
        <div id="productList" class="mb-6 border-b border-white/30 pb-4">
            <h3 class="text-lg font-semibold mb-3">Sản phẩm trong đơn hàng</h3>
            <div id="itemsContainer" class="space-y-3">
                <!-- Items will be loaded here -->
            </div>
        </div>

        <!-- Tóm tắt đơn hàng -->
        <div id="orderSummary" class="mb-6 border-b border-white/30 pb-4">
            <h3 class="text-lg font-semibold mb-2">Tóm tắt đơn hàng</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Số sản phẩm:</span>
                    <span id="itemCount" class="font-bold">0</span>
                </div>
                <div class="flex justify-between">
                    <span>Tổng tiền:</span>
                    <span id="totalAmount" class="font-bold text-yellow-300 text-xl">0đ</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-white/20">
                    <span>Số dư hiện tại:</span>
                    <span id="currentBalance" class="font-bold text-green-300">0đ</span>
                </div>
            </div>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-3">Phương thức thanh toán</h3>
            <div class="space-y-3">
                <label class="flex items-center space-x-3 cursor-pointer">
                    <input type="radio" name="paymentMethod" value="coins" checked class="text-blue-500 w-5 h-5">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">💰</span>
                        <span>Thanh toán bằng Sky Coins</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="flex justify-between items-center">
            <a href="{{ route('account.cart') }}" class="text-blue-400 hover:text-blue-300 underline transition">
                ← Quay lại giỏ hàng
            </a>
            <button id="confirmPaymentBtn" 
                    class="bg-gradient-to-r from-blue-500 to-cyan-400 px-6 py-3 rounded-lg font-bold text-white hover:from-blue-600 hover:to-cyan-500 transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                XÁC NHẬN THANH TOÁN
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    // ✅ Lấy dữ liệu từ sessionStorage
    const cartItems = JSON.parse(sessionStorage.getItem('checkout_cart') || '[]');
    
    console.log('🛒 Cart items from sessionStorage:', cartItems);

    // ✅ Kiểm tra giỏ hàng rỗng
    if (cartItems.length === 0) {
        showToast('Giỏ hàng trống! Đang chuyển về trang giỏ hàng...', 'error');
        setTimeout(() => {
            window.location.href = '{{ route("account.cart") }}';
        }, 2000);
        return;
    }

    // ✅ Render danh sách sản phẩm
    renderCartItems(cartItems);

    // ✅ Tính tổng tiền
    const totalAmount = cartItems.reduce((sum, item) => {
        const price = parseFloat(item.price || 0);
        const quantity = parseInt(item.quantity || 1);
        return sum + (price * quantity);
    }, 0);

    // ✅ Cập nhật giao diện
    document.getElementById('itemCount').textContent = cartItems.length;
    document.getElementById('totalAmount').textContent = totalAmount.toLocaleString('vi-VN') + 'đ';

    // ✅ Lấy số dư hiện tại
    await fetchCurrentBalance();

    // ✅ Xử lý nút thanh toán
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    confirmBtn.addEventListener('click', handlePayment);
});

/**
 * Render danh sách sản phẩm
 */
function renderCartItems(items) {
    const container = document.getElementById('itemsContainer');
    
    if (items.length === 0) {
        container.innerHTML = '<p class="text-white/70 text-center">Không có sản phẩm nào</p>';
        return;
    }

    container.innerHTML = items.map(item => {
        const price = parseFloat(item.price || 0);
        const quantity = parseInt(item.quantity || 1);
        const total = price * quantity;
        
        return `
            <div class="flex items-center gap-4 bg-white/5 p-3 rounded-lg">
                <img src="${item.image || '/img/default-product.jpg'}" 
                     alt="${item.name}" 
                     class="w-16 h-16 object-cover rounded">
                <div class="flex-1">
                    <h4 class="font-semibold text-white">${item.name}</h4>
                    <p class="text-sm text-white/70">Tác giả: ${item.author || 'Chưa rõ'}</p>
                    <p class="text-sm text-white/70">Số lượng: ${quantity}</p>
                </div>
                <div class="text-right">
                    <p class="text-yellow-300 font-bold">${total.toLocaleString('vi-VN')}đ</p>
                </div>
            </div>
        `;
    }).join('');
}

/**
 * Lấy số dư hiện tại
 */
async function fetchCurrentBalance() {
    try {
        console.log('🔍 Fetching current balance...');
        
        const response = await fetch('/api/user/balance', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            credentials: 'same-origin' // ✅ Quan trọng: Gửi cookies/session
        });
        
        console.log('📡 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('💰 Balance data:', data);
        
        if (data.success) {
            const balance = parseFloat(data.balance || 0);
            document.getElementById('currentBalance').textContent = formatVietnameseNumber(balance) + 'đ';
            
            console.log('✅ Balance updated:', balance);
        } else {
            console.error('❌ API returned error:', data.message);
            document.getElementById('currentBalance').textContent = 'Lỗi: ' + data.message;
        }
    } catch (error) {
        console.error('❌ Error fetching balance:', error);
        document.getElementById('currentBalance').textContent = 'Không thể tải';
    }
}
/**
 * Xử lý thanh toán
 */
async function handlePayment() {
    const cartItems = JSON.parse(sessionStorage.getItem('checkout_cart') || '[]');
    
    if (cartItems.length === 0) {
        showToast('Giỏ hàng trống!', 'error');
        return;
    }

    // ✅ Tính tổng tiền
    const totalAmount = cartItems.reduce((sum, item) => {
        const price = parseFloat(item.price || 0);
        const quantity = parseInt(item.quantity || 1);
        return sum + (price * quantity);
    }, 0);

    // ✅ Xác nhận thanh toán
    if (!confirm(`Bạn có chắc muốn thanh toán ${totalAmount.toLocaleString('vi-VN')}đ cho ${cartItems.length} sản phẩm?`)) {
        return;
    }

    const confirmBtn = document.getElementById('confirmPaymentBtn');
    const originalText = confirmBtn.innerHTML;
    
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '⏳ ĐANG XỬ LÝ...';

    try {
        const response = await fetch('{{ route("account.paycart.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                items: cartItems,
                total_amount: totalAmount
            })
        });

        const data = await response.json();
        console.log('💳 Payment response:', data);

        if (data.success) {
            // ✅ Xóa sessionStorage
            sessionStorage.removeItem('checkout_cart');
            
            showToast('✅ Thanh toán thành công! Số dư còn lại: ' + formatVietnameseNumber(data.new_balance) + 'đ', 'success');
            
            // ✅ Chuyển đến trang sheets sau 1.5s
            setTimeout(() => {
                window.location.href = '{{ route("account.sheets") }}';
            }, 1500);
        } else {
            showToast('❌ ' + data.message, 'error');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Payment error:', error);
        showToast('❌ Lỗi kết nối máy chủ!', 'error');
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = originalText;
    }
}

/**
 * Format số theo kiểu Việt Nam (dấu phẩy phân cách hàng nghìn)
 */
function formatVietnameseNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 
                    type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.className = `fixed top-6 right-6 z-[9999] px-6 py-3 rounded-lg shadow-lg text-white font-semibold ${bgColor} transition-all duration-300`;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            if (document.body.contains(toast)) {
                document.body.removeChild(toast);
            }
        }, 400);
    }, 3000);
}
</script>
@endsection