@extends('layouts.account')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex flex-col items-center mb-8">
        <span class="text-5xl coin-spin mb-2">🪙</span>
        <h2 class="orbitron text-3xl font-bold text-white mb-1">Nạp Sky Coins</h2>
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">{{ number_format($currentUser['coins'] ?? session('coins', 0)) }}</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form id="depositForm" class="space-y-6">
            @csrf
            <input type="hidden" id="user_id" name="user_id" value="{{ session('firebase_uid') }}">
            <div>
                <label class="block text-white font-semibold mb-2">Chọn gói nạp nhanh</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="10000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">10,000</span>
                        <span class="text-xs text-gray-700">10.000đ</span>
                    </button>
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="20000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">20,000</span>
                        <span class="text-xs text-gray-700">20.000đ</span>
                    </button>
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="25000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">25,000</span>
                        <span class="text-xs text-gray-700">25.000đ</span>
                    </button>
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="30000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">30,000</span>
                        <span class="text-xs text-gray-700">30.000đ</span>
                    </button>
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="50000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">50,000</span>
                        <span class="text-xs text-gray-700">50.000đ</span>
                    </button>
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="100000">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">100,000</span>
                        <span class="text-xs text-gray-700">100.000đ</span>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Hoặc nhập số coin muốn nạp</label>
                <input type="text" id="customAmount" name="amount" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số tiền (từ 10.000đ - 50.000.000đ)" />
                <div id="amountError" class="text-red-300 text-xs mt-1 hidden"></div>
                <p class="text-xs text-yellow-100 mt-1">1 coin = 1 VND (Tối thiểu 10,000đ - Tối đa 50,000,000đ)</p>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Phương thức thanh toán</label>
                <div class="flex gap-4 justify-center">
                    <label class="flex items-center gap-2 cursor-pointer bg-white/20 px-4 py-2 rounded-lg border border-white/30">
                        <input type="radio" name="payment" class="accent-blue-500" checked>
                        <span class="text-blue-400 font-semibold">SePay</span>
                        <span class="text-2xl">🏦</span>
                    </label>
                </div>
                <p class="text-xs text-center text-yellow-100 mt-2">Thanh toán qua chuyển khoản ngân hàng với SePay</p>
            </div>
            <button type="submit" id="submitBtn" class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Nạp Coins với SePay</span>
                <div id="loadingSpinner" class="hidden inline-block ml-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                </div>
            </button>
            <div class="text-xs text-gray-200 mt-2 text-center">* Thanh toán an toàn với SePay - Nạp ngay, nhận coins liền!</div>
        </form>
    </div>
</div>

<!-- Modal hiển thị thông tin thanh toán SePay -->
<div id="paymentModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="text-center">
                <div class="text-4xl mb-4">⏳</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Chờ thanh toán</h3>
                <p class="text-sm text-gray-600 mb-4">Vui lòng chuyển khoản để hoàn tất giao dịch</p>
                <div id="paymentInfo" class="space-y-4">
                    <!-- Thông tin sẽ được load bằng JavaScript -->
                </div>
                <div class="mt-6 space-y-2">
                    <button id="checkStatusBtn" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        Kiểm tra trạng thái thanh toán
                    </button>
                    <button id="closeModalBtn" 
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('depositForm');
    const customAmountInput = document.getElementById('customAmount');
    const amountBtns = document.querySelectorAll('.amount-btn');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const paymentModal = document.getElementById('paymentModal');
    const paymentInfo = document.getElementById('paymentInfo');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const checkStatusBtn = document.getElementById('checkStatusBtn');
    const amountError = document.getElementById('amountError');
    
    let currentTransactionId = null;
    let selectedAmount = null;

    // Function to show error
    function showAmountError(message) {
        amountError.textContent = message;
        amountError.classList.remove('hidden');
        customAmountInput.classList.add('border-red-400');
    }

    // Function to hide error
    function hideAmountError() {
        amountError.classList.add('hidden');
        customAmountInput.classList.remove('border-red-400');
    }

    // Xử lý click vào các nút số tiền gợi ý
    amountBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = parseInt(this.getAttribute('data-amount'));
            selectedAmount = amount;
            customAmountInput.value = amount.toLocaleString('vi-VN');
            hideAmountError(); // Hide any existing errors
            
            // Reset active state
            amountBtns.forEach(b => {
                b.classList.remove('bg-yellow-200', 'border-yellow-400', 'shadow-lg');
                b.classList.add('bg-white/60', 'border-yellow-200');
            });
            
            // Set active state
            this.classList.remove('bg-white/60', 'border-yellow-200');
            this.classList.add('bg-yellow-200', 'border-yellow-400', 'shadow-lg');
        });
    });

    // Format số tiền khi nhập
    customAmountInput.addEventListener('input', function() {
        hideAmountError(); // Hide error when user types
        
        const value = this.value.replace(/,/g, '');
        if (value && !isNaN(value) && value > 0) {
            const amount = parseInt(value);
            selectedAmount = amount;
            
            // Validate amount
            if (amount < 10000) {
                showAmountError('Số tiền tối thiểu là 10,000đ');
            } else if (amount > 50000000) {
                showAmountError('Số tiền tối đa là 50,000,000đ');
            }
            
            // Format display
            const formatted = amount.toLocaleString('vi-VN');
            if (this.value !== formatted) {
                this.value = formatted;
            }
        } else if (!value) {
            selectedAmount = null;
            this.value = '';
        } else {
            showAmountError('Vui lòng nhập số hợp lệ');
        }
        
        // Reset active state của buttons khi nhập manual
        amountBtns.forEach(b => {
            b.classList.remove('bg-yellow-200', 'border-yellow-400', 'shadow-lg');
            b.classList.add('bg-white/60', 'border-yellow-200');
        });
        
        console.log('Selected amount updated:', selectedAmount); // Debug log
    });

    // Xử lý submit form
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const userId = document.getElementById('user_id').value;
        
        if (!userId) {
            alert('Vui lòng đăng nhập để thực hiện giao dịch');
            return;
        }
        
        // Lấy giá trị từ input nếu selectedAmount không có
        let amount = selectedAmount;
        if (!amount) {
            const inputValue = customAmountInput.value.replace(/,/g, '');
            amount = parseInt(inputValue) || 0;
        }
        
        console.log('Amount to send:', amount); // Debug log
        
        if (!amount || amount < 10000 || amount > 50000000) {
            if (!amount) {
                showAmountError('Vui lòng nhập số tiền');
            } else if (amount < 10000) {
                showAmountError('Số tiền tối thiểu là 10,000đ');
            } else {
                showAmountError('Số tiền tối đa là 50,000,000đ');
            }
            customAmountInput.focus();
            return;
        }
        
        hideAmountError(); // Hide any errors before submitting

        // Show loading
        submitBtn.disabled = true;
        submitText.textContent = 'Đang tạo yêu cầu thanh toán...';
        loadingSpinner.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("payment.deposit.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    amount: amount,
                    user_id: userId
                })
            });

            const data = await response.json();

            if (data.success) {
                currentTransactionId = data.data.transaction_id;
                showPaymentModal(data.data);
                
                // Show clear status message that payment is NOT complete yet
                setTimeout(() => {
                    const statusDiv = document.createElement('div');
                    statusDiv.className = 'bg-orange-50 border border-orange-200 p-3 rounded-lg mt-4';
                    statusDiv.innerHTML = `
                        <div class="flex items-center gap-2">
                            <span class="text-orange-600 text-lg">⏳</span>
                            <div>
                                <p class="font-semibold text-orange-800">Đang chờ thanh toán</p>
                                <p class="text-sm text-orange-700">Vui lòng chuyển khoản để hoàn tất giao dịch</p>
                            </div>
                        </div>
                    `;
                    
                    const paymentInfo = document.getElementById('paymentInfo');
                    if (paymentInfo) {
                        paymentInfo.appendChild(statusDiv);
                    }
                }, 500);
                
            } else {
                // Handle validation errors
                if (data.errors && data.errors.amount) {
                    showAmountError(data.errors.amount[0]);
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể tạo yêu cầu thanh toán'));
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra, vui lòng thử lại');
        } finally {
            // Hide loading
            submitBtn.disabled = false;
            submitText.textContent = 'Nạp Coins với SePay';
            loadingSpinner.classList.add('hidden');
        }
    });

    function showPaymentModal(data) {
        let html = `
            <div class="text-left">
                <div class="bg-blue-50 p-4 rounded-lg mb-4">
                    <p class="text-sm text-gray-600 mb-1">Mã giao dịch:</p>
                    <p class="font-bold text-blue-600">${data.transaction_id}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg mb-4">
                    <p class="text-sm text-gray-600 mb-1">Số tiền nạp:</p>
                    <p class="font-bold text-green-600 text-lg">${parseInt(data.amount).toLocaleString('vi-VN')}đ</p>
                    <p class="text-xs text-gray-500">= ${parseInt(data.amount).toLocaleString('vi-VN')} coins</p>
                </div>
        `;
        
        // QR Code section with fallback options
        if (data.qr_code || data.qr_options) {
            html += `<div class="text-center mb-4">`;
            
            // Primary QR code
            if (data.qr_code) {
                html += `
                    <div class="qr-container">
                        <img id="primaryQR" src="${data.qr_code}" alt="QR Code" 
                             class="mx-auto max-w-48 border rounded-lg shadow"
                             onerror="handleQRError(this, '${data.transaction_id}')">
                        <p class="text-xs text-gray-500 mt-2">📱 Quét mã QR bằng app ngân hàng</p>
                    </div>
                `;
            }
            
            // Alternative QR options
            if (data.qr_options) {
                html += `
                    <div class="mt-4">
                        <p class="text-xs text-gray-600 mb-2">🔄 Chọn định dạng QR code khác:</p>
                        <div class="grid grid-cols-2 gap-2">
                `;
                
                if (data.qr_options.vietqr && data.qr_options.vietqr !== data.qr_code) {
                    html += `<button onclick="switchQR('${data.qr_options.vietqr}')" class="text-xs bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded transition">🔲 QR Cơ bản</button>`;
                }
                if (data.qr_options.vietqr_compact && data.qr_options.vietqr_compact !== data.qr_code) {
                    html += `<button onclick="switchQR('${data.qr_options.vietqr_compact}')" class="text-xs bg-purple-100 hover:bg-purple-200 px-2 py-1 rounded transition">📱 Compact</button>`;
                }
                if (data.qr_options.vietqr_full) {
                    html += `<button onclick="switchQR('${data.qr_options.vietqr_full}')" class="text-xs bg-green-100 hover:bg-green-200 px-2 py-1 rounded transition">📄 In ấn</button>`;
                }
                if (data.qr_options.vietqr_mini) {
                    html += `<button onclick="switchQR('${data.qr_options.vietqr_mini}')" class="text-xs bg-orange-100 hover:bg-orange-200 px-2 py-1 rounded transition">🏷️ Mini</button>`;
                }
                if (data.qr_options.manual) {
                    html += `<button onclick="switchQR('${data.qr_options.manual}')" class="text-xs bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded transition col-span-2">ℹ️ Thông tin TK</button>`;
                }
                
                html += `
                        </div>
                        <div class="mt-2 text-center">
                            <a href="/debug/test-vietqr/${data.amount}" target="_blank" class="text-xs text-blue-600 underline">🔗 Test VietQR trực tiếp</a>
                        </div>
                    </div>
                `;
            }
            
            html += `</div>`;
        }
        
        if (data.bank_info && (data.bank_info.bank_name || data.bank_info.account_number)) {
            html += `
                <div class="bg-yellow-50 p-4 rounded-lg text-sm border border-yellow-200">
                    <h4 class="font-semibold mb-2 text-yellow-800">🏦 Thông tin chuyển khoản:</h4>
                    ${data.bank_info.bank_name ? `<p>Ngân hàng: <strong>${data.bank_info.bank_name}</strong></p>` : ''}
                    ${data.bank_info.account_number ? `<p>Số tài khoản: <strong>${data.bank_info.account_number}</strong></p>` : ''}
                    ${data.bank_info.account_name ? `<p>Tên tài khoản: <strong>${data.bank_info.account_name}</strong></p>` : ''}
                    <p>Nội dung: <strong class="text-red-600">${data.bank_info.content}</strong></p>
                    <p class="text-xs text-yellow-700 mt-2">⚠️ Chuyển khoản ĐÚNG nội dung để tự động cộng coins</p>
                </div>
            `;
        }
        
        html += `
                <div class="bg-red-50 border-2 border-red-300 p-4 rounded-lg mt-4">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-red-600 text-2xl animate-pulse">🚫</span>
                        <p class="font-bold text-red-800 text-lg">CHƯA THÀNH CÔNG</p>
                    </div>
                    <div class="bg-white p-3 rounded border border-red-200 mb-3">
                        <p class="text-sm text-red-800 font-semibold mb-2">
                            ❌ Giao dịch chưa hoàn thành - Coins chưa được cộng
                        </p>
                        <p class="text-xs text-red-700">
                            Thông báo "Thành công" sẽ chỉ hiển thị KHI ngân hàng xác nhận chuyển khoản
                        </p>
                    </div>
                    <p class="text-sm text-red-700 mb-2 font-semibold">
                        Để hoàn tất giao dịch:
                    </p>
                    <ol class="text-sm text-red-600 ml-4 list-decimal space-y-2">
                        <li><strong>Chuyển khoản đúng số tiền</strong> (${parseInt(data.amount).toLocaleString('vi-VN')}đ)</li>
                        <li><strong>Ghi ĐÚNG nội dung:</strong> <span class="bg-yellow-200 px-1 font-mono">${data.bank_info ? data.bank_info.content : ''}</span></li>
                        <li><strong>Đợi xác nhận từ ngân hàng</strong> (2-10 phút)</li>
                        <li><strong>Coins sẽ TỰ ĐỘNG cộng</strong> khi ngân hàng xác nhận</li>
                    </ol>
                    <div class="mt-3 p-2 bg-yellow-100 border border-yellow-300 rounded">
                        <p class="text-xs text-yellow-800 font-semibold">
                            ⏰ Chỉ bấm "Kiểm tra trạng thái" SAU KHI đã chuyển khoản thành công!
                        </p>
                    </div>
                </div>
            </div>
        `;
        
        paymentInfo.innerHTML = html;
        paymentModal.classList.remove('hidden');
    }

    closeModalBtn.addEventListener('click', function() {
        paymentModal.classList.add('hidden');
    });

    checkStatusBtn.addEventListener('click', async function() {
        if (!currentTransactionId) return;

        const originalText = this.textContent;
        this.textContent = 'Đang kiểm tra...';
        this.disabled = true;

        try {
            const response = await fetch(`/payment/check-status/${currentTransactionId}`);
            const data = await response.json();

            if (data.success) {
                const status = data.data.status;
                let message = '';
                let shouldReload = false;
                
                switch (status) {
                    case 'completed':
                        message = '  
                                '🎉🎉🎉 COINS ĐÃ ĐƯỢC NẠP VÀO TÀI KHOẢN!\n\n' +
                                `💰 Số tiền: ${parseInt(data.data.amount).toLocaleString('vi-VN')} coins\n` +
                                `⏰ Thời gian: ${data.data.created_at}\n\n` +
                                'Cảm ơn bạn đã sử dụng dịch vụ! 🙏';
                        shouldReload = true;
                        break;
                    case 'processing':
                        message = '⏳ ĐANG XỬ LÝ\n\n' +
                                'Ngân hàng đang xác thực giao dịch của bạn.\n' +
                                'Vui lòng đợi thêm 1-2 phút...';
                        break;
                    case 'pending':
                        message = '⏰ CHƯA HOÀN THÀNH\n\n' +
                                '❌ Hệ thống chưa nhận được chuyển khoản từ ngân hàng.\n\n' +
                                '📌 Vui lòng kiểm tra:\n' +
                                '• Đã chuyển khoản đúng số tiền chưa?\n' +
                                '• Nội dung chuyển khoản có chính xác không?\n' +
                                '• Nếu vừa chuyển, đợi thêm 2-5 phút\n\n' +
                                '💡 Sau khi chuyển khoản thành công, bấm lại "Kiểm tra trạng thái"';
                        break;
                    case 'failed':
                        message = '❌ THẤT BẠI\n\n' +
                                'Thanh toán không thành công.\n' +
                                'Vui lòng tạo giao dịch mới hoặc liên hệ hỗ trợ.';
                        break;
                    default:
                        message = `📊 Trạng thái: ${status.toUpperCase()}\n\n` +
                                'Nếu bạn đã chuyển khoản, vui lòng đợi thêm vài phút.';
                }
                
                alert(message);
                
                if (shouldReload) {
                    paymentModal.classList.add('hidden');
                    location.reload();
                }
            }
        } catch (error) {
            console.error('Error checking status:', error);
            alert('Không thể kiểm tra trạng thái giao dịch');
        } finally {
            this.textContent = originalText;
            this.disabled = false;
        }
    });





    // Close modal when clicking outside
    paymentModal.addEventListener('click', function(e) {
        if (e.target === paymentModal) {
            paymentModal.classList.add('hidden');
        }
    });

    // Auto check status and process transactions
    let statusCheckInterval;
    let autoProcessInterval;
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
                if (paymentModal.classList.contains('hidden')) {
                    // Clear intervals when modal is closed
                    if (statusCheckInterval) {
                        clearInterval(statusCheckInterval);
                        statusCheckInterval = null;
                    }
                    if (autoProcessInterval) {
                        clearInterval(autoProcessInterval);
                        autoProcessInterval = null;
                    }
                } else if (currentTransactionId && !statusCheckInterval) {
                    console.log('🚀 Starting auto-processing for transaction:', currentTransactionId);
                    
                    // Check status every 15 seconds (less frequent to avoid false positives)
                    statusCheckInterval = setInterval(async () => {
                        try {
                            const response = await fetch(`/debug/check-status/${currentTransactionId}`);
                            const data = await response.json();
                            
                            console.log('Status check result:', data);
                            
                            // Only update UI if response is successful
                            if (data.success) {
                                // Update status indicator in real-time
                                updatePaymentStatusUI(data.data.status);
                                
                                // Only show success if REALLY completed AND processed
                                if (data.data.status === 'completed' && data.data.processed === true) {
                                    clearInterval(statusCheckInterval);
                                    
                                    console.log('🎉 Transaction truly completed!');
                                    
                                    // Show success notification
                                    showSuccessNotification();
                                    
                                    setTimeout(() => {
                                        location.reload();
                                    }, 3000);
                                }
                            } else {
                                // If error, show pending status
                                updatePaymentStatusUI('pending');
                                console.log('Status check failed, showing pending');
                            }
                        } catch (error) {
                            console.error('Auto check error:', error);
                            updatePaymentStatusUI('pending'); // Default to pending on error
                        }
                    }, 15000); // Check every 15 seconds
                    
                    // DISABLED AUTO-PROCESS: Only manual status check to prevent false success
                    // User must actually complete bank transfer before success notification
                }
            }
        });
    });
    
    observer.observe(paymentModal, { attributes: true });
});

// Global functions for QR handling
function handleQRError(img, transactionId) {
    console.error('QR Code failed to load:', img.src);
    
    // Generate fallback QR using different service
    const fallbackUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + 
                       encodeURIComponent(`TPBank\nSo TK: 20588668888\nTen TK: Sky Music Store\nNoi dung: Nap tien ${transactionId}`);
    
    img.src = fallbackUrl;
    img.onerror = function() {
        // If all QR services fail, show manual info only
        img.style.display = 'none';
        const container = img.closest('.qr-container');
        if (container) {
            container.innerHTML = `
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <p class="text-red-700 text-sm font-medium mb-2">⚠️ Không thể tạo mã QR</p>
                    <p class="text-red-600 text-xs">Vui lòng chuyển khoản thủ công theo thông tin bên dưới</p>
                </div>
            `;
        }
    };
}

function switchQR(newUrl) {
    const primaryQR = document.getElementById('primaryQR');
    if (primaryQR) {
        primaryQR.src = newUrl;
        primaryQR.onerror = function() {
            handleQRError(this, currentTransactionId);
        };
    }
}

// Update payment status UI in real-time
function updatePaymentStatusUI(status) {
    let statusHtml = '';
    let className = '';
    
    switch(status) {
        case 'completed':
            statusHtml = `
                <div class="flex items-center gap-2">
                    <span class="text-green-600 text-lg">✅</span>
                    <div>
                        <p class="font-semibold text-green-800">Thanh toán thành công!</p>
                        <p class="text-sm text-green-700">Coins đã được cộng vào tài khoản</p>
                    </div>
                </div>
            `;
            className = 'bg-green-50 border-green-200';
            break;
        case 'processing':
            statusHtml = `
                <div class="flex items-center gap-2">
                    <span class="text-blue-600 text-lg animate-spin">⚡</span>
                    <div>
                        <p class="font-semibold text-blue-800">Đang xử lý thanh toán</p>
                        <p class="text-sm text-blue-700">Ngân hàng đang xác thực giao dịch...</p>
                    </div>
                </div>
            `;
            className = 'bg-blue-50 border-blue-200';
            break;
        default:
            statusHtml = `
                <div class="flex items-center gap-2">
                    <span class="text-orange-600 text-lg">⏳</span>
                    <div>
                        <p class="font-semibold text-orange-800">Đang chờ thanh toán</p>
                        <p class="text-sm text-orange-700">Vui lòng chuyển khoản để hoàn tất giao dịch</p>
                    </div>
                </div>
            `;
            className = 'bg-orange-50 border-orange-200';
    }
    
    // Find and update status div
    const paymentInfo = document.getElementById('paymentInfo');
    if (paymentInfo) {
        let statusDiv = paymentInfo.querySelector('.payment-status');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.className = 'payment-status border p-3 rounded-lg mt-4';
            paymentInfo.appendChild(statusDiv);
        }
        statusDiv.className = `payment-status border p-3 rounded-lg mt-4 ${className}`;
        statusDiv.innerHTML = statusHtml;
    }
}

// Show success notification
function showSuccessNotification() {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 animate-bounce';
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <span class="text-2xl">🎉</span>
            <div>
                <p class="font-bold">Thanh toán thành công!</p>
                <p class="text-sm">Coins đã được cộng vào tài khoản</p>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
