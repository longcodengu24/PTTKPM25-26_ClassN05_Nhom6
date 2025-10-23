@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-2 py-10">
	<div class="game-card rounded-2xl p-8">
		<h2 class="text-center text-3xl md:text-4xl font-bold mb-2 text-white">GIỎ HÀNG CỦA BẠN</h2>
		<div class="text-center text-white/80 mb-8">Giỏ hàng của bạn sẽ hiển thị ở đây</div>
		<div class="flex flex-col md:flex-row gap-8">
			<!-- Cart Table -->
			<div class="flex-1 bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 overflow-x-auto border border-white/30 text-white">
			<table class="w-full text-base table-fixed">
				<colgroup>
					<col style="width: 140px;">
					<col style="width: 50%;">
					<col style="width: 140px;">
					<col style="width: 60px;">
				</colgroup>
				<thead>
					<tr class="border-b border-white/30 text-white/80 text-sm">
						<th class="py-3 px-2 font-semibold text-left">Hình ảnh</th>
						<th class="py-3 px-2 font-semibold text-left">Thông tin</th>
						<th class="py-3 px-2 font-semibold text-right">Giá tiền</th>
						<th class="py-3 px-2 font-semibold text-center"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="border-b border-gray-100 transition">
						<td class="py-4 px-2 align-top">
							<div class="aspect-w-16 aspect-h-9 w-full rounded-lg overflow-hidden border border-gray-200">
								<img src="/img/sheet1.jpg" alt="Dreams of Light" class="object-cover w-full h-full">
							</div>
						</td>
						<td class="py-4 px-2 align-top">
							<div class="font-semibold text-white text-lg">Dreams of Light</div>
							<div class="text-white/80 text-sm">Tác giả: SkyMusicLover</div>
							<div class="text-white/60 text-sm mb-1">Người soạn: AuroraVN</div>
						</td>
						<td class="py-4 px-2 align-top text-right">
							<span class="font-bold text-lg text-yellow-300">1,855,000đ</span>
						</td>
						<td class="py-4 px-2 align-top text-center">
							<button class="text-blue-400 hover:text-blue-600 text-2xl transition align-top" title="Xóa sản phẩm">&times;</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
			<!-- Order Summary -->
			<div class="w-full md:w-80 bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 flex flex-col gap-4 border border-white/30 text-white">
			<div class="font-semibold text-lg text-white mb-2">Tóm tắt đơn hàng</div>
			<div class="flex justify-between text-white/80 text-base">
				<span>Tổng tiền hàng:</span>
				<span class="summary-total">0đ</span>
			</div>
			<div class="flex justify-between text-white/80 text-base">
				<span>Giảm giá:</span>
				<span class="summary-discount">- 0đ</span>
			</div>
			<div class="flex justify-between text-white/80 text-base pb-2 border-b border-white/30">
				<span>Tạm tính:</span>
				<span class="summary-subtotal">0đ</span>
			</div>
			<div class="flex justify-between items-center text-xl font-bold text-yellow-300 mt-2">
				<span>Tổng tiền:</span>
				<span class="summary-final">0đ</span>
			</div>
			<button onclick="processCheckout()" class="w-full mt-4 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-bold text-lg shadow hover:from-blue-600 hover:to-cyan-500 transition">TIẾN HÀNH ĐẶT HÀNG</button>
			<a href="{{ route('shop.index') }}" class="w-full py-3 rounded-lg border border-blue-200 text-blue-700 font-semibold text-lg text-center hover:bg-blue-50 transition">MUA THÊM SẢN PHẨM</a>
		</div>
	</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {


	
	let cartData = { items: [], total_items: 0, total_amount: 0 };

	// Load cart từ Firestore API
	function loadCart() {
		fetch('/api/cart', {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			}
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				cartData = data.cart;
				renderCart();
			} else {
				console.error('Error loading cart:', data.message);
				renderCart(); // Render empty cart
			}
		})
		.catch(error => {
			console.error('Error loading cart:', error);
			renderCart(); // Render empty cart
		});
	}

	function renderCart() {
		const tbody = document.querySelector('table tbody');
		tbody.innerHTML = '';
		
		if (!cartData.items || cartData.items.length === 0) {
			tbody.innerHTML = `<tr><td colspan='4' class='text-center py-8 text-white/80'>Giỏ hàng trống.</td></tr>`;
		} else {
			cartData.items.forEach((item, idx) => {
				let price = item.price || 0;
				let tr = document.createElement('tr');
				tr.className = 'border-b border-gray-100 transition';
				tr.innerHTML = `
					<td class='py-4 px-2 align-top'>
						<div class='aspect-w-16 aspect-h-9 w-full rounded-lg overflow-hidden border border-gray-200'>
							<img src='${item.image || '/img/default-product.jpg'}' alt='${item.name}' class='object-cover w-full h-full'>
						</div>
					</td>
					<td class='py-4 px-2 align-top'>
						<div class='font-semibold text-white text-lg'>${item.name}</div>
						<div class='text-white/80 text-sm'>Số lượng: ${item.quantity || 1}</div>
					</td>
					<td class='py-4 px-2 align-top text-right'>
						<span class='font-bold text-lg text-yellow-300'>${(price * (item.quantity || 1)).toLocaleString('vi-VN')}đ</span>
					</td>
					<td class='py-4 px-2 align-top text-center'>
						<button onclick='removeItem("${item.product_id}")' class='text-blue-400 hover:text-blue-600 text-2xl transition align-top' title='Xóa sản phẩm'>&times;</button>
					</td>
				`;
				tbody.appendChild(tr);
			});
		}

		// Update summary
		let total = cartData.total_amount || 0;
		let discount = 0;
		let subtotal = total - discount;
		
		document.querySelector('.summary-total').innerText = total.toLocaleString('vi-VN') + 'đ';
		document.querySelector('.summary-discount').innerText = '- ' + discount.toLocaleString('vi-VN') + 'đ';
		document.querySelector('.summary-subtotal').innerText = subtotal.toLocaleString('vi-VN') + 'đ';
		document.querySelector('.summary-final').innerText = subtotal.toLocaleString('vi-VN') + 'đ';
	}

window.removeItem = function(productId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        return;
    }

    fetch('/api/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ✅ Cập nhật cartData từ response
            cartData = data.cart || { items: [], total_items: 0, total_amount: 0 };
            renderCart();
            showToast(data.message || 'Đã xóa sản phẩm khỏi giỏ hàng', 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
        showToast('Có lỗi xảy ra', 'error');
    });
}

	// Load cart khi trang load
	loadCart();

	// Xử lý checkout - Chuyển đến trang thanh toán
	window.processCheckout = function() {
		if (!cartData.items || cartData.items.length === 0) {
			showToast('Giỏ hàng trống!', 'error');
			return;
		}

		let totalAmount = cartData.total_amount || 0;

		// Show loading
		const checkoutBtn = document.querySelector('button[onclick="processCheckout()"]');
		const originalText = checkoutBtn.innerHTML;
		checkoutBtn.innerHTML = 'ĐANG XỬ LÝ...';
		checkoutBtn.disabled = true;

		// Step 1: Get full product details with seller_id
		let productPromises = cartData.items.map(item => 
			fetch('/api/products/' + item.product_id)
				.then(res => res.json())
		);

		Promise.all(productPromises)
			.then(productResults => {
				// Prepare cart items with seller_id
				let cartItems = cartData.items.map((item, index) => {
					let productDetail = productResults[index];
					
					if (!productDetail.success || !productDetail.data) {
						throw new Error('Không tìm thấy thông tin sản phẩm: ' + item.name);
					}

					let product = productDetail.data;
					
					if (!product.seller_id) {
						throw new Error('Sản phẩm thiếu thông tin người bán: ' + item.name);
					}

					return {
						product_id: item.product_id,
						price: item.price,
						name: item.name,
						image: item.image,
						seller_id: product.seller_id,
						quantity: item.quantity || 1,
						author: product.author || '',
						transcribed_by: product.transcribed_by || ''
					};
				});

				   // Lưu cart items vào sessionStorage để dùng ở trang paycart
				   sessionStorage.setItem('checkout_cart', JSON.stringify(cartItems));
				   // Chuyển đến trang thanh toán mới
				   window.location.href = '{{ route("account.paycart") }}';
			})
			.catch(error => {
				console.error('Checkout error:', error);
				showToast(error.message || 'Có lỗi xảy ra khi chuẩn bị thanh toán', 'error');
				checkoutBtn.innerHTML = originalText;
				checkoutBtn.disabled = false;
			});
	}

	// Toast notification function
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
});
</script>
@endsection
