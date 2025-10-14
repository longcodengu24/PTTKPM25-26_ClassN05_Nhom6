<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xử lý giao dịch - Sky Music Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-pink-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-white text-center mb-8">🏦 Xử lý giao dịch SePay</h1>
            
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-white mb-4">📝 Hướng dẫn</h2>
                <div class="text-gray-200 text-sm space-y-2">
                    <p>1. Khi nhận được thông báo chuyển khoản thành công từ ngân hàng</p>
                    <p>2. Copy mã giao dịch từ nội dung chuyển khoản (VD: DEP_1760210765_wcodmUQ3)</p>
                    <p>3. Nhập vào form bên dưới và bấm "Xử lý giao dịch"</p>
                    <p>4. Hệ thống sẽ tự động cộng coins vào tài khoản và cập nhật Firebase</p>
                </div>
            </div>
            
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6">
                <form id="processForm" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Mã giao dịch:</label>
                        <input type="text" id="transactionId" 
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none"
                               placeholder="VD: DEP_1760210765_wcodmUQ3" />
                        <div id="transactionError" class="text-red-300 text-xs mt-1 hidden"></div>
                    </div>
                    
                    <div>
                        <label class="block text-white font-semibold mb-2">Số tiền (VND):</label>
                        <input type="number" id="amount" value="10000"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none"
                               placeholder="10000" />
                        <p class="text-xs text-gray-300 mt-1">1 VND = 1 Coin</p>
                    </div>
                    
                    <button type="submit" id="processBtn" 
                            class="w-full py-3 rounded-lg bg-gradient-to-r from-green-500 to-blue-500 text-white font-bold text-lg shadow-lg hover:from-green-600 hover:to-blue-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="processText">🚀 Xử lý giao dịch</span>
                        <div id="loadingSpinner" class="hidden inline-block ml-2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                        </div>
                    </button>
                </form>
                
                <div id="result" class="mt-6 hidden">
                    <!-- Result will be shown here -->
                </div>
            </div>
            
            <!-- Recent Transactions -->
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-white mb-4">📋 Giao dịch gần đây</h3>
                <div class="text-gray-200 text-sm space-y-1">
                    <p>• DEP_1760205286_cWtworZm - 10,000 VND ✅</p>
                    <p>• DEP_1760210765_wcodmUQ3 - 10,000 VND ✅</p>
                    <p id="newTransactionLog" class="text-green-300"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('processForm');
        const transactionIdInput = document.getElementById('transactionId');
        const amountInput = document.getElementById('amount');
        const processBtn = document.getElementById('processBtn');
        const processText = document.getElementById('processText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const transactionError = document.getElementById('transactionError');
        const result = document.getElementById('result');
        const newTransactionLog = document.getElementById('newTransactionLog');

        function showError(message) {
            transactionError.textContent = message;
            transactionError.classList.remove('hidden');
            transactionIdInput.classList.add('border-red-400');
        }

        function hideError() {
            transactionError.classList.add('hidden');
            transactionIdInput.classList.remove('border-red-400');
        }

        function showResult(data, isSuccess = true) {
            const bgColor = isSuccess ? 'bg-green-500/20 border-green-400' : 'bg-red-500/20 border-red-400';
            const textColor = isSuccess ? 'text-green-300' : 'text-red-300';
            const icon = isSuccess ? '🎉' : '❌';
            
            result.innerHTML = `
                <div class="${bgColor} border rounded-lg p-4">
                    <h4 class="${textColor} font-semibold mb-2">${icon} ${data.message}</h4>
                    ${isSuccess ? `
                        <div class="text-gray-300 text-sm space-y-1">
                            <p>Transaction ID: <strong>${data.transaction_id}</strong></p>
                            <p>User ID: <strong>${data.user_id}</strong></p>
                            <p>Amount Added: <strong>+${data.amount_added?.toLocaleString('vi-VN')} coins</strong></p>
                            <p>Old Balance: <strong>${data.old_balance?.toLocaleString('vi-VN')} coins</strong></p>
                            <p>New Balance: <strong>${data.new_balance?.toLocaleString('vi-VN')} coins</strong></p>
                            <p>Firebase: <strong>${data.firebase_updated ? '✅ Updated' : '❌ Failed'}</strong></p>
                        </div>
                    ` : ''}
                </div>
            `;
            result.classList.remove('hidden');
            
            if (isSuccess) {
                // Add to transaction log
                newTransactionLog.textContent = `• ${data.transaction_id} - ${data.amount_added?.toLocaleString('vi-VN')} VND ✅ (${new Date().toLocaleTimeString()})`;
                
                // Clear form
                transactionIdInput.value = '';
                amountInput.value = '10000';
            }
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            hideError();
            
            const transactionId = transactionIdInput.value.trim();
            const amount = parseInt(amountInput.value) || 10000;
            
            if (!transactionId) {
                showError('Vui lòng nhập mã giao dịch');
                transactionIdInput.focus();
                return;
            }
            
            if (amount < 1000 || amount > 50000000) {
                showError('Số tiền phải từ 1,000 đến 50,000,000 VND');
                amountInput.focus();
                return;
            }

            // Show loading
            processBtn.disabled = true;
            processText.textContent = 'Đang xử lý...';
            loadingSpinner.classList.remove('hidden');
            result.classList.add('hidden');

            try {
                const response = await fetch(`/debug/process-new-transaction/${transactionId}/${amount}`, {
                    method: 'GET'
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showResult(data, true);
                } else {
                    showResult(data, false);
                }
                
            } catch (error) {
                console.error('Error:', error);
                showResult({ message: 'Có lỗi xảy ra khi xử lý giao dịch: ' + error.message }, false);
            } finally {
                // Hide loading
                processBtn.disabled = false;
                processText.textContent = '🚀 Xử lý giao dịch';
                loadingSpinner.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>