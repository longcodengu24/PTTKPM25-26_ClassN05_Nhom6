@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-2 py-10">
	<div class="game-card rounded-2xl p-8">
		<h2 class="text-center text-3xl md:text-4xl font-bold mb-2 text-white">XÁC NHẬN THANH TOÁN</h2>
		<div class="text-center text-white/80 mb-8">Vui lòng kiểm tra lại thông tin đơn hàng trước khi thanh toán</div>
		
		<div class="flex flex-col lg:flex-row gap-8">
			<!-- Left: Thông tin người mua -->
			<div class="flex-1 space-y-6">
				<!-- Thông tin tài khoản -->
				<div class="bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 border border-white/30 text-white">
					<h3 class="text-xl font-bold mb-4 flex items-center gap-2">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
						</svg>
						Thông tin người mua
					</h3>
					<div class="space-y-3">
						<div class="flex justify-between">
							<span class="text-white/70">Tên tài khoản:</span>
							<span class="font-semibold">{{ $userData['name'] ?? 'N/A' }}</span>
						</div>
						<div class="flex justify-between">
							<span class="text-white/70">Email:</span>
							<span class="font-semibold">{{ $userData['email'] ?? 'N/A' }}</span>
						</div>
						<div class="flex justify-between items-center pt-3 border-t border-white/20">
							<span class="text-white/70">Số dư hiện tại:</span>
							<span class="font-bold text-yellow-300 text-xl">{{ number_format($userCoins) }} coins</span>
						</div>
					</div>
				</div>

				<!-- Danh sách sản phẩm -->
				<div class="bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 border border-white/30 text-white">
					<h3 class="text-xl font-bold mb-4 flex items-center gap-2">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
						</svg>
						Sản phẩm trong đơn hàng
					</h3>
					<div id="checkout-items" class="space-y-4">
						<!-- Will be filled by JavaScript -->
						<div class="text-center py-8 text-white/60">
							<svg class="w-12 h-12 mx-auto mb-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							Đang tải...
						</div>
					</div>
				</div>
			</div>

			<!-- Right: Tóm tắt đơn hàng -->
			<div class="lg:w-96">
				<div class="bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 border border-white/30 text-white sticky top-4">
					<h3 class="text-xl font-bold mb-4">Tóm tắt đơn hàng</h3>
					
					<div class="space-y-3 mb-4">
						<div class="flex justify-between text-white/80">
							<span>Số lượng sản phẩm:</span>
							<span id="summary-items" class="font-semibold">0</span>
						</div>
						<div class="flex justify-between text-white/80">
							<span>Tổng tiền hàng:</span>
							<span id="summary-subtotal" class="font-semibold">0đ</span>
						</div>
						<div class="flex justify-between text-white/80 pb-3 border-b border-white/20">
							<span>Phí xử lý:</span>
							<span class="font-semibold">0đ</span>
						</div>
						<div class="flex justify-between items-center text-xl font-bold text-yellow-300 py-3 border-b border-white/20">
							<span>Tổng thanh toán:</span>
							<span id="summary-total">0đ</span>
						</div>
						<div class="flex justify-between items-center text-white/80">
							<span>Số dư sau thanh toán:</span>
							<span id="summary-remaining" class="font-semibold text-green-300">0 coins</span>
						</div>
					</div>

					<!-- Thông báo cảnh báo nếu không đủ xu -->
					<div id="insufficient-warning" class="hidden mb-4 p-3 bg-red-500/20 border border-red-500/50 rounded-lg text-red-300 text-sm">
						<svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
						</svg>
						Không đủ xu để thanh toán!
					</div>

					<!-- Buttons -->
					<button onclick="confirmCheckout()" id="checkout-btn" class="w-full py-3 rounded-lg bg-gradient-to-r from-green-500 to-emerald-400 text-white font-bold text-lg shadow hover:from-green-600 hover:to-emerald-500 transition mb-3">
						XÁC NHẬN THANH TOÁN
					</button>
					<a href="{{ route('account.cart') }}" class="w-full py-3 rounded-lg border border-white/30 text-white font-semibold text-lg text-center hover:bg-white/10 transition block">
						← Quay lại giỏ hàng
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	let cartItems = [];
	let totalAmount = 0;
	let totalItems = 0;
	let userCoins = {{ $userCoins }};

	// Load cart từ sessionStorage
	function loadCheckoutCart() {
		const cartDataStr = sessionStorage.getItem('checkout_cart');
		if (!cartDataStr) {
			showToast('Không tìm thấy giỏ hàng. Vui lòng thử lại.', 'error');
			setTimeout(() => {
				window.location.href = '{{ route("account.cart") }}';
			}, 2000);
			return;
		}

		try {
			cartItems = JSON.parse(cartDataStr);
			if (!cartItems || cartItems.length === 0) {
				throw new Error('Giỏ hàng trống');
			}
			renderCheckoutItems();
			updateSummary();
		} catch (e) {
			console.error('Error parsing cart data:', e);
			showToast('Dữ liệu giỏ hàng không hợp lệ', 'error');
			setTimeout(() => {
				window.location.href = '{{ route("account.cart") }}';
			}, 2000);
		}
	}

	// Render danh sách sản phẩm
	function renderCheckoutItems() {
		const container = document.getElementById('checkout-items');
		container.innerHTML = '';

		cartItems.forEach((item, index) => {
			const itemDiv = document.createElement('div');
			itemDiv.className = 'flex gap-4 p-4 bg-white/5 rounded-lg border border-white/20 hover:border-white/40 transition';
			itemDiv.innerHTML = `
				<div class="w-24 h-24 flex-shrink-0">
					<img src="${item.image || '/img/default-product.jpg'}" alt="${item.name}" class="w-full h-full object-cover rounded-lg border border-white/20">
				</div>
				<div class="flex-1 min-w-0">
					<h4 class="font-semibold text-white mb-1 truncate">${item.name}</h4>
					${item.author ? `<p class="text-sm text-white/60 mb-1">Tác giả: ${item.author}</p>` : ''}
					${item.transcribed_by ? `<p class="text-sm text-white/60">Soạn: ${item.transcribed_by}</p>` : ''}
				</div>
				<div class="text-right flex-shrink-0">
					<div class="text-sm text-white/60 mb-1">SL: ${item.quantity || 1}</div>
					<div class="font-bold text-yellow-300">${((item.price || 0) * (item.quantity || 1)).toLocaleString('vi-VN')}đ</div>
				</div>
			`;
			container.appendChild(itemDiv);
		});
	}

	// Cập nhật tóm tắt
	function updateSummary() {
		totalAmount = 0;
		totalItems = 0;

		cartItems.forEach(item => {
			const qty = item.quantity || 1;
			totalItems += qty;
			totalAmount += (item.price || 0) * qty;
		});

		document.getElementById('summary-items').textContent = totalItems;
		document.getElementById('summary-subtotal').textContent = totalAmount.toLocaleString('vi-VN') + 'đ';
		document.getElementById('summary-total').textContent = totalAmount.toLocaleString('vi-VN') + 'đ';
		
		const remaining = userCoins - totalAmount;
		document.getElementById('summary-remaining').textContent = remaining.toLocaleString('vi-VN') + ' coins';

		// Hiển thị cảnh báo nếu không đủ xu
		const warningEl = document.getElementById('insufficient-warning');
		const checkoutBtn = document.getElementById('checkout-btn');
		
		if (remaining < 0) {
			warningEl.classList.remove('hidden');
			checkoutBtn.disabled = true;
			checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
			document.getElementById('summary-remaining').classList.remove('text-green-300');
			document.getElementById('summary-remaining').classList.add('text-red-300');
		} else {
			warningEl.classList.add('hidden');
			checkoutBtn.disabled = false;
			checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
			document.getElementById('summary-remaining').classList.add('text-green-300');
			document.getElementById('summary-remaining').classList.remove('text-red-300');
		}
	}

	// Xác nhận thanh toán
	window.confirmCheckout = function() {
		if (userCoins < totalAmount) {
			showToast('Số dư không đủ để thanh toán!', 'error');
			return;
		}

		if (!confirm(`Xác nhận thanh toán ${totalItems} sản phẩm với tổng ${totalAmount.toLocaleString('vi-VN')}đ?`)) {
			return;
		}

		const checkoutBtn = document.getElementById('checkout-btn');
		const originalText = checkoutBtn.innerHTML;
		checkoutBtn.innerHTML = '<svg class="w-5 h-5 inline animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ĐANG XỬ LÝ...';
		checkoutBtn.disabled = true;

		// Gọi API thanh toán
		fetch('{{ route("shop.checkout.process") }}', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			body: JSON.stringify({
				cart_items: cartItems
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				// Clear cart
				fetch('/api/cart/clear', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				});

				// Xóa sessionStorage
				sessionStorage.removeItem('checkout_cart');

				showToast('✅ Thanh toán thành công! Số xu còn lại: ' + data.data.remaining_coins.toLocaleString('vi-VN'), 'success');
				
				// Redirect
				setTimeout(() => {
					window.location.href = '{{ route("account.sheets") }}';
				}, 2000);
			} else {
				showToast('❌ ' + data.message, 'error');
				checkoutBtn.innerHTML = originalText;
				checkoutBtn.disabled = false;
			}
		})
		.catch(error => {
			console.error('Checkout error:', error);
			showToast('❌ Có lỗi xảy ra trong quá trình thanh toán', 'error');
			checkoutBtn.innerHTML = originalText;
			checkoutBtn.disabled = false;
		});
	}

	// Toast notification
	function showToast(message, type = 'info') {
		const toast = document.createElement('div');
		toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-semibold shadow-lg z-50 ${
			type === 'success' ? 'bg-green-500' : 
			type === 'error' ? 'bg-red-500' : 'bg-blue-500'
		}`;
		toast.textContent = message;
		document.body.appendChild(toast);

		setTimeout(() => {
			toast.style.opacity = '0';
			toast.style.transition = 'opacity 0.4s';
			setTimeout(() => document.body.removeChild(toast), 400);
		}, 4000);
	}

	// Load cart khi trang load
	loadCheckoutCart();
});
</script>
@endsection
