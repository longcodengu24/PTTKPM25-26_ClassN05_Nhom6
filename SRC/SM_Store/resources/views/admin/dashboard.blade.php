<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Auto-Processing - Sky Music Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-pink-900 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-white text-center mb-8">🚀 Auto-Processing Dashboard</h1>
            
            <!-- Status Cards -->
            <div class="grid md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-2">🔄 Auto-Processing Status</h3>
                    <div id="autoStatus" class="text-green-300">Đang chạy...</div>
                    <button id="toggleAuto" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Bật/Tắt Auto
                    </button>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-2">📊 Thống kê</h3>
                    <div id="stats" class="text-gray-200 text-sm">
                        <p>Đã xử lý: <span id="processedCount">0</span></p>
                        <p>Lần kiểm tra cuối: <span id="lastCheck">-</span></p>
                        <p>🔥 Firebase: <span id="firebaseStatus" class="text-green-300">Connected</span></p>
                    </div>
                </div>
                
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-2">⚡ Actions</h3>
                    <div class="space-y-2">
                        <button id="runNow" class="w-full px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition text-sm">
                            Chạy ngay
                        </button>
                        <button id="checkBalance" class="w-full px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition text-sm">
                            Kiểm tra Balance
                        </button>
                        <button id="createTest" class="w-full px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition text-sm">
                            Tạo test transactions
                        </button>
                        <button id="demoWorkflow" class="w-full px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition text-sm">
                            🎬 Demo Workflow
                        </button>
                        <button id="showTransactions" class="w-full px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition text-sm">
                            📋 Xem Transactions
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Log Display -->
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-white mb-4">📋 Activity Log</h3>
                <div id="logContainer" class="bg-black/30 rounded-lg p-4 h-96 overflow-y-auto">
                    <div class="text-gray-300 text-sm" id="logContent">
                        <p class="text-green-300">[SYSTEM] Dashboard khởi tạo...</p>
                    </div>
                </div>
                <button id="clearLog" class="mt-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                    Xóa Log
                </button>
            </div>
        </div>
    </div>

    <script>
    class AutoProcessDashboard {
        constructor() {
            this.isRunning = false;
            this.interval = null;
            this.processedCount = 0;
            
            this.initElements();
            this.initEvents();
            this.start();
        }
        
        initElements() {
            this.autoStatus = document.getElementById('autoStatus');
            this.toggleBtn = document.getElementById('toggleAuto');
            this.runNowBtn = document.getElementById('runNow');
            this.checkBalanceBtn = document.getElementById('checkBalance');
            this.createTestBtn = document.getElementById('createTest');
            this.demoWorkflowBtn = document.getElementById('demoWorkflow');
            this.showTransactionsBtn = document.getElementById('showTransactions');
            this.logContent = document.getElementById('logContent');
            this.clearLogBtn = document.getElementById('clearLog');
            this.statsProcessed = document.getElementById('processedCount');
            this.statsLastCheck = document.getElementById('lastCheck');
        }
        
        initEvents() {
            this.toggleBtn.addEventListener('click', () => this.toggle());
            this.runNowBtn.addEventListener('click', () => this.runNow());
            this.checkBalanceBtn.addEventListener('click', () => this.checkBalance());
            this.createTestBtn.addEventListener('click', () => this.createTestTransactions());
            this.demoWorkflowBtn.addEventListener('click', () => this.demoWorkflow());
            this.showTransactionsBtn.addEventListener('click', () => this.showTransactions());
            this.clearLogBtn.addEventListener('click', () => this.clearLog());
        }
        
        log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const colors = {
                info: 'text-gray-300',
                success: 'text-green-300',
                error: 'text-red-300',
                warning: 'text-yellow-300'
            };
            
            const logEntry = document.createElement('p');
            logEntry.className = colors[type] || colors.info;
            logEntry.innerHTML = `[${timestamp}] ${message}`;
            
            this.logContent.appendChild(logEntry);
            this.logContent.parentElement.scrollTop = this.logContent.parentElement.scrollHeight;
            
            // Keep only last 50 log entries
            while (this.logContent.children.length > 50) {
                this.logContent.removeChild(this.logContent.firstChild);
            }
        }
        
        updateStatus() {
            this.autoStatus.textContent = this.isRunning ? '🟢 Đang chạy' : '🔴 Đã tắt';
            this.autoStatus.className = this.isRunning ? 'text-green-300' : 'text-red-300';
            this.toggleBtn.textContent = this.isRunning ? '⏹️ Tắt Auto' : '▶️ Bật Auto';
            this.statsLastCheck.textContent = new Date().toLocaleTimeString();
        }
        
        async runAutoProcess() {
            try {
                this.log('🔄 Đang chạy auto-process...', 'info');
                
                const response = await fetch('/debug/auto-process-transactions');
                const data = await response.json();
                
                if (data.success) {
                    if (data.processed_count > 0) {
                        this.processedCount += data.processed_count;
                        this.statsProcessed.textContent = this.processedCount;
                        this.log(`✅ Đã xử lý ${data.processed_count} giao dịch`, 'success');
                        
                        data.processed_transactions.forEach(tx => {
                            this.log(`💰 ${tx.transaction_id}: +${tx.amount.toLocaleString('vi-VN')} coins`, 'success');
                        });
                    } else {
                        this.log('ℹ️ Không có giao dịch nào cần xử lý', 'info');
                    }
                } else {
                    this.log(`❌ Lỗi: ${data.message || 'Unknown error'}`, 'error');
                }
                
            } catch (error) {
                this.log(`❌ Network error: ${error.message}`, 'error');
            }
            
            this.updateStatus();
        }
        
        async checkBalance() {
            try {
                this.log('💰 Đang kiểm tra balance...', 'info');
                
                const response = await fetch('/debug/set-balance/cfT4zfDX4YRkuwd4T6X3seJhtbl1/0');
                const data = await response.json();
                
                if (data.success) {
                    this.log(`💰 Balance hiện tại: ${data.balance.toLocaleString('vi-VN')} coins`, 'success');
                    this.log(`👤 User ID: ${data.user_id}`, 'info');
                } else {
                    this.log('❌ Không thể kiểm tra balance', 'error');
                }
                
            } catch (error) {
                this.log(`❌ Balance check error: ${error.message}`, 'error');
            }
        }
        
        start() {
            if (this.isRunning) return;
            
            this.isRunning = true;
            this.log('🚀 Bắt đầu auto-processing...', 'success');
            
            // Run immediately
            this.runAutoProcess();
            
            // Then run every 30 seconds
            this.interval = setInterval(() => {
                this.runAutoProcess();
            }, 30000);
            
            this.updateStatus();
        }
        
        stop() {
            if (!this.isRunning) return;
            
            this.isRunning = false;
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
            
            this.log('⏹️ Đã tắt auto-processing', 'warning');
            this.updateStatus();
        }
        
        toggle() {
            this.isRunning ? this.stop() : this.start();
        }
        
        runNow() {
            this.log('⚡ Chạy thủ công...', 'info');
            this.runAutoProcess();
        }
        
        async createTestTransactions() {
            try {
                this.log('🧪 Đang tạo test transactions...', 'info');
                
                const response = await fetch('/debug/create-test-transactions/cfT4zfDX4YRkuwd4T6X3seJhtbl1');
                const data = await response.json();
                
                if (data.success) {
                    this.log(`✅ Đã tạo ${data.transactions.length} test transactions`, 'success');
                    
                    data.transactions.forEach(tx => {
                        this.log(`📝 ${tx.transaction_id}: ${tx.amount.toLocaleString('vi-VN')} coins`, 'info');
                    });
                    
                    this.log('ℹ️ Auto-processing sẽ xử lý trong vòng 30 giây', 'warning');
                } else {
                    this.log('❌ Không thể tạo test transactions', 'error');
                }
                
            } catch (error) {
                this.log(`❌ Create test error: ${error.message}`, 'error');
            }
        }
        
        async demoWorkflow() {
            try {
                this.log('🎬 Bắt đầu Demo Workflow hoàn chỉnh...', 'info');
                this.log('📝 Bước 1: Tạo giao dịch PENDING...', 'info');
                
                const response = await fetch('/debug/demo-workflow/cfT4zfDX4YRkuwd4T6X3seJhtbl1');
                const data = await response.json();
                
                if (data.success && data.demo_complete) {
                    const steps = data.workflow_steps;
                    
                    this.log(`✅ Bước 1: Tạo giao dịch ${steps['1_transaction_created'].transaction_id} (${steps['1_transaction_created'].amount.toLocaleString('vi-VN')} VND)`, 'success');
                    this.log('💳 Bước 2: User chuyển khoản...', 'info');
                    this.log('🏦 Bước 3: Bank confirm thành công!', 'success');
                    
                    if (steps['4_auto_processing'].processed_count > 0) {
                        this.processedCount += steps['4_auto_processing'].processed_count;
                        this.statsProcessed.textContent = this.processedCount;
                        this.log(`🔥 Bước 4: Auto-processing đã cộng ${data.final_result.coins_added_to_firebase.toLocaleString('vi-VN')} coins vào Firebase!`, 'success');
                        this.log(`🎯 HOÀN THÀNH: "khi giao dịch thành công thì coins tự động cộng vào firebase" ✅`, 'success');
                    } else {
                        this.log('⚠️ Bước 4: Auto-processing không phát hiện giao dịch mới', 'warning');
                    }
                    
                } else {
                    this.log('❌ Demo workflow thất bại', 'error');
                    console.error('Demo error:', data);
                }
                
            } catch (error) {
                this.log(`❌ Demo workflow error: ${error.message}`, 'error');
            }
        }
        
        async showTransactions() {
            try {
                this.log('📋 Đang lấy danh sách transactions...', 'info');
                
                const response = await fetch('/debug/show-transactions/cfT4zfDX4YRkuwd4T6X3seJhtbl1');
                const data = await response.json();
                
                if (data.success) {
                    this.log(`📊 Tìm thấy ${data.total_transactions} transactions:`, 'success');
                    
                    data.transactions.forEach(tx => {
                        const statusEmoji = {
                            'completed': '✅',
                            'pending': '⏳', 
                            'processing': '🔄',
                            'failed': '❌'
                        }[tx.status] || '❓';
                        
                        const processedEmoji = tx.processed ? '✅' : '⏸️';
                        
                        this.log(`${statusEmoji} ${tx.transaction_id}: ${tx.amount.toLocaleString('vi-VN')}đ [${tx.status.toUpperCase()}] [Processed: ${processedEmoji}]`, 
                                tx.status === 'completed' ? 'success' : tx.status === 'failed' ? 'error' : 'info');
                    });
                    
                    if (data.total_transactions === 0) {
                        this.log('📝 Chưa có transactions nào', 'warning');
                    }
                    
                } else {
                    this.log('❌ Không thể lấy danh sách transactions', 'error');
                }
                
            } catch (error) {
                this.log(`❌ Show transactions error: ${error.message}`, 'error');
            }
        }
        
        clearLog() {
            this.logContent.innerHTML = '<p class="text-green-300">[SYSTEM] Log đã được xóa</p>';
        }
    }

    // Initialize dashboard when page loads
    document.addEventListener('DOMContentLoaded', () => {
        window.dashboard = new AutoProcessDashboard();
    });
    </script>
</body>
</html>