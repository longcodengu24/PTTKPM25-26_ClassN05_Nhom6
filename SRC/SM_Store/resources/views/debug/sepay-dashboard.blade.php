<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SePay Webhook Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-success { @apply bg-green-100 text-green-800; }
        .status-pending { @apply bg-yellow-100 text-yellow-800; }
        .status-failed { @apply bg-red-100 text-red-800; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">🏪 SePay Webhook Automation Dashboard</h1>
            
            <!-- Status Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Webhook Endpoint</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ url('/api/sepay/webhook') }}</p>
                    <span id="webhookStatus" class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">✅ Active</span>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Auto Credit</h3>
                    <p class="text-sm text-gray-600 mb-2">Automatic coin crediting</p>
                    <span id="autoCreditStatus" class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">✅ Enabled</span>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Real-time Updates</h3>
                    <p class="text-sm text-gray-600 mb-2">Frontend auto-checking</p>
                    <span id="realtimeStatus" class="px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">✅ Working</span>
                </div>
            </div>

            <!-- Test Controls -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">🧪 Test Automation System</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Test Full Flow -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-700">Complete Flow Test</h3>
                        <p class="text-sm text-gray-600">Tests: Transaction creation → Webhook → Balance update</p>
                        
                        <div class="flex space-x-2">
                            <input type="text" id="testUserId" placeholder="User ID" value="test_user_123" class="flex-1 px-3 py-2 border rounded">
                            <input type="number" id="testAmount" placeholder="Amount" value="50000" min="10000" class="flex-1 px-3 py-2 border rounded">
                        </div>
                        
                        <button onclick="testFullFlow()" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                            Run Full Flow Test
                        </button>
                    </div>
                    
                    <!-- Test Individual Webhook -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-700">Webhook Test</h3>
                        <p class="text-sm text-gray-600">Test webhook with existing transaction ID</p>
                        
                        <input type="text" id="webhookTransactionId" placeholder="Transaction ID" class="w-full px-3 py-2 border rounded">
                        
                        <button onclick="testWebhook()" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                            Test Webhook
                        </button>
                    </div>
                </div>

                <!-- Test Results -->
                <div id="testResults" class="mt-6 hidden">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Test Results</h3>
                    <div id="testResultContent" class="bg-gray-50 p-4 rounded border text-sm font-mono overflow-auto max-h-96"></div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Recent Transactions</h2>
                <div id="recentTransactions">
                    <p class="text-gray-500 text-sm">Click "Load Transactions" to view recent transactions</p>
                </div>
                
                <button onclick="loadRecentTransactions()" class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
                    Load Transactions
                </button>
            </div>

            <!-- Configuration -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">⚙️ SePay Configuration</h2>
                <div id="configuration">
                    <p class="text-gray-500 text-sm">Loading configuration...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load configuration on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadConfiguration();
        });

        async function testFullFlow() {
            const userId = document.getElementById('testUserId').value;
            const amount = document.getElementById('testAmount').value;
            
            if (!userId || !amount) {
                alert('Please enter User ID and Amount');
                return;
            }

            try {
                showLoading('Running complete automation test...');
                
                const response = await fetch(`/test/webhook-automation?user_id=${userId}&amount=${amount}`);
                const result = await response.json();
                
                showTestResults(result);
                
                // Update status indicators based on result
                updateStatusIndicators(result);
                
            } catch (error) {
                console.error('Test failed:', error);
                showTestResults({ success: false, error: error.message });
            }
        }

        async function testWebhook() {
            const transactionId = document.getElementById('webhookTransactionId').value;
            
            if (!transactionId) {
                alert('Please enter Transaction ID');  
                return;
            }

            try {
                showLoading('Testing webhook...');
                
                const response = await fetch(`/api/sepay/test-webhook`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        transaction_id: transactionId,
                        amount: 50000
                    })
                });
                
                const result = await response.json();
                showTestResults(result);
                
            } catch (error) {
                console.error('Webhook test failed:', error);
                showTestResults({ success: false, error: error.message });
            }
        }

        async function loadConfiguration() {
            try {
                const response = await fetch('/debug/sepay-config');
                const config = await response.json();
                
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">';
                
                Object.entries(config.sepay_config).forEach(([key, value]) => {
                    html += `
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-medium">${key}:</span>
                            <span>${value}</span>
                        </div>
                    `;
                });
                
                html += `
                    <div class="flex justify-between py-2 border-b">
                        <span class="font-medium">SePay Class:</span>
                        <span class="${config.sepay_class_exists ? 'text-green-600' : 'text-red-600'}">
                            ${config.sepay_class_exists ? '✅ Available' : '❌ Not Found'}
                        </span>
                    </div>
                `;
                
                html += '</div>';
                
                document.getElementById('configuration').innerHTML = html;
                
            } catch (error) {
                document.getElementById('configuration').innerHTML = 
                    '<p class="text-red-500">Failed to load configuration</p>';
            }
        }

        async function loadRecentTransactions() {
            try {
                // This would need a proper endpoint
                document.getElementById('recentTransactions').innerHTML = 
                    '<p class="text-blue-500">Loading transactions... (endpoint needed)</p>';
                    
            } catch (error) {
                document.getElementById('recentTransactions').innerHTML = 
                    '<p class="text-red-500">Failed to load transactions</p>';
            }
        }

        function showLoading(message) {
            document.getElementById('testResults').classList.remove('hidden');
            document.getElementById('testResultContent').innerHTML = 
                `<div class="flex items-center"><div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>${message}</div>`;
        }

        function showTestResults(result) {
            document.getElementById('testResults').classList.remove('hidden');
            document.getElementById('testResultContent').innerHTML = 
                JSON.stringify(result, null, 2);
        }

        function updateStatusIndicators(result) {
            if (result.automation_working) {
                const indicators = ['webhookStatus', 'autoCreditStatus', 'realtimeStatus'];
                indicators.forEach(id => {
                    const element = document.getElementById(id);
                    if (result.automation_working.overall_success) {
                        element.className = 'px-3 py-1 rounded-full text-sm bg-green-100 text-green-800';
                        element.textContent = '✅ Working';
                    } else {
                        element.className = 'px-3 py-1 rounded-full text-sm bg-red-100 text-red-800';
                        element.textContent = '❌ Error';
                    }
                });
            }
        }
    </script>
</body>
</html>