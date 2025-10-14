<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SePay Transaction Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">💰 SePay Transaction Manager</h1>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">🔄 Process Existing Payments</h3>
                    <input type="text" id="userId" placeholder="User ID" class="w-full px-3 py-2 border rounded mb-3">
                    <button onclick="processExistingPayments()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                        Process SePay Transactions
                    </button>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">🎯 Manual Webhook</h3>
                    <input type="text" id="webhookRef" placeholder="Transaction Reference" class="w-full px-3 py-2 border rounded mb-2">
                    <input type="number" id="webhookAmount" placeholder="Amount" class="w-full px-3 py-2 border rounded mb-3">
                    <button onclick="sendManualWebhook()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                        Send Manual Webhook
                    </button>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">📊 Check Balance</h3>
                    <input type="text" id="balanceUserId" placeholder="User ID" class="w-full px-3 py-2 border rounded mb-3">
                    <button onclick="checkBalance()" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded">
                        Check Balance
                    </button>
                </div>
            </div>

            <!-- Results -->
            <div id="results" class="bg-white rounded-lg shadow p-6 hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📋 Results</h2>
                <div id="resultContent" class="bg-gray-50 p-4 rounded border text-sm font-mono overflow-auto max-h-96"></div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 rounded-lg p-6 mt-6">
                <h2 class="text-xl font-bold text-blue-800 mb-4">📚 Instructions</h2>
                <div class="text-blue-700 space-y-2">
                    <p><strong>Process Existing Payments:</strong> Nhập User ID để xử lý các giao dịch SePay đã có nhưng chưa được cộng coins</p>
                    <p><strong>Manual Webhook:</strong> Gửi webhook thủ công với transaction reference và amount</p>
                    <p><strong>Check Balance:</strong> Kiểm tra số coins hiện tại của user</p>
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-green-50 rounded-lg p-6 mt-6">
                <h2 class="text-xl font-bold text-green-800 mb-4">✅ System Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-green-700">
                    <div>
                        <p><strong>Webhook URL:</strong> https://a8d91bed3644.ngrok-free.app/api/sepay/webhook</p>
                        <p><strong>Auto Processing:</strong> ✅ Enabled</p>
                    </div>
                    <div>
                        <p><strong>Balance Storage:</strong> Session + Firebase Backup</p>
                        <p><strong>Real-time Updates:</strong> ✅ Working</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showResults(data) {
            document.getElementById('results').classList.remove('hidden');
            document.getElementById('resultContent').innerHTML = JSON.stringify(data, null, 2);
        }

        async function processExistingPayments() {
            const userId = document.getElementById('userId').value;
            if (!userId) {
                alert('Please enter User ID');
                return;
            }

            try {
                const response = await fetch(`/sepay/process-existing/${userId}`);
                const data = await response.json();
                showResults(data);
                
                if (data.success) {
                    alert(`✅ Success! Added ${data.total_coins_added} coins to user ${userId}`);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error processing payments');
            }
        }

        async function sendManualWebhook() {
            const ref = document.getElementById('webhookRef').value;
            const amount = document.getElementById('webhookAmount').value;
            
            if (!ref || !amount) {
                alert('Please enter both Transaction Reference and Amount');
                return;
            }

            try {
                const response = await fetch('/sepay/manual-webhook', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        reference_id: ref,
                        status: 'success',
                        amount: amount,
                        message: 'Manual webhook test'
                    })
                });
                
                const data = await response.json();
                showResults(data);
                
                if (data.success) {
                    alert('✅ Manual webhook sent successfully');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error sending webhook');
            }
        }

        async function checkBalance() {
            const userId = document.getElementById('balanceUserId').value;
            if (!userId) {
                alert('Please enter User ID');
                return;
            }

            try {
                const response = await fetch(`/api/sepay/balance/${userId}`);
                const data = await response.json();
                showResults(data);
                
                if (data.success) {
                    alert(`💰 User ${userId} has ${data.data.formatted_balance}`);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error checking balance');
            }
        }
    </script>
</body>
</html>