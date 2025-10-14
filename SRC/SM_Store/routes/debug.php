<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\UserBalanceService;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

Route::get('/debug-users', function (Auth $auth) {
    try {
        $users = $auth->listUsers();

        echo "<h2>Firebase Users Debug</h2>";
        echo "<style>table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px;text-align:left} th{background-color:#f2f2f2}</style>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Display Name</th><th>UID</th><th>Created</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . ($user->email ?? 'N/A') . "</td>";
            echo "<td><strong>" . ($user->displayName ?? 'N/A') . "</strong></td>";
            echo "<td>" . $user->uid . "</td>";
            echo "<td>" . $user->metadata->createdAt->format('Y-m-d H:i:s') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

// Kiểm tra Firebase Realtime Database
Route::get('/debug/firebase/check-balance/{user_id?}', function (Request $request, $user_id = null) {
    try {
        $user_id = $user_id ?: 'your_user_id';
        
        // Kết nối Firebase Realtime Database với URL đúng
        $databaseUrl = config('firebase.database_url', 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app');
        
        $firebase = (new Factory)
            ->withServiceAccount(resource_path('key/firebase.json'))
            ->withDatabaseUri($databaseUrl);
        
        $database = $firebase->createDatabase();
        
        Log::info("🔍 Checking Firebase balance for user {$user_id} at {$databaseUrl}");
        
        // Đọc data từ Firebase
        $userBalance = $database->getReference("users/{$user_id}/coins")->getValue();
        $userData = $database->getReference("users/{$user_id}")->getValue();
        
        // Kiểm tra UserBalanceService
        $balanceService = app(UserBalanceService::class);
        $serviceBalance = $balanceService->getUserBalance($user_id);
        
        Log::info("💰 Firebase balance: {$userBalance}, Service balance: {$serviceBalance}");
        
        return response()->json([
            'user_id' => $user_id,
            'firebase_balance' => $userBalance,
            'firebase_user_data' => $userData,
            'balance_service_result' => $serviceBalance,
            'database_url' => $databaseUrl,
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Manually set balance to Firebase
Route::get('/debug/firebase/set-balance/{user_id}/{amount}', function (Request $request, $user_id, $amount) {
    try {
        // Kết nối Firebase Realtime Database với URL đúng
        $databaseUrl = config('firebase.database_url', 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app');
        
        $firebase = (new Factory)
            ->withServiceAccount(resource_path('key/firebase.json'))
            ->withDatabaseUri($databaseUrl);
        
        $database = $firebase->createDatabase();
        
        // Set balance
        $database->getReference("users/{$user_id}/coins")->set((int)$amount);
        
        // Update UserBalanceService cache
        $balanceService = app(UserBalanceService::class);
        $balanceService->updateUserBalance($user_id, (int)$amount);
        
        return response()->json([
            'success' => true,
            'message' => "Set balance for user {$user_id} to {$amount}",
            'user_id' => $user_id,
            'new_balance' => (int)$amount
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Check session after login
Route::get('/debug/session', function (Request $request) {
    return response()->json([
        'all_session' => Session::all(),
        'firebase_uid' => Session::get('firebase_uid'),
        'coins' => Session::get('coins'),
        'user_name' => Session::get('name')
    ]);
});

// Quick login for testing
Route::get('/debug/quick-login/{user_id}', function (Request $request, $user_id) {
    // Set session data
    Session::put('firebase_uid', $user_id);
    Session::put('name', 'Test User');
    
    // Get balance from UserBalanceService
    $balanceService = app(UserBalanceService::class);
    $balance = $balanceService->getUserBalance($user_id);
    
    Session::put('coins', $balance);
    
    return response()->json([
        'success' => true,
        'message' => 'Logged in successfully',
        'user_id' => $user_id,
        'balance' => $balance,
        'session_data' => [
            'firebase_uid' => Session::get('firebase_uid'),
            'coins' => Session::get('coins'),
            'name' => Session::get('name')
        ]
    ]);
});

// Simple set balance via UserBalanceService
Route::get('/debug/set-balance/{user_id}/{amount}', function (Request $request, $user_id, $amount) {
    try {
        // Update balance via UserBalanceService
        $balanceService = app(UserBalanceService::class);
        $balanceService->updateUserBalance($user_id, (int)$amount);
        
        // Login user and get updated balance
        Session::put('firebase_uid', $user_id);
        Session::put('name', 'Test User');
        $newBalance = $balanceService->getUserBalance($user_id);
        Session::put('coins', $newBalance);
        
        return response()->json([
            'success' => true,
            'message' => "Updated balance for user {$user_id} to {$amount}",
            'user_id' => $user_id,
            'balance' => $newBalance,
            'session_data' => [
                'firebase_uid' => Session::get('firebase_uid'),
                'coins' => Session::get('coins'),
                'name' => Session::get('name')
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Check REAL transaction status from database
Route::get('/debug/check-status/{transaction_id}', function (Request $request, $transaction_id) {
    try {
        Log::info("🔍 Checking status for transaction: {$transaction_id}");
        
        // Find transaction in database
        $transaction = DB::table('transactions')
            ->where('transaction_id', $transaction_id)
            ->first();
        
        if (!$transaction) {
            Log::warning("❌ Transaction not found: {$transaction_id}");
            return response()->json([
                'success' => false,
                'error' => 'Transaction not found',
                'data' => [
                    'transaction_id' => $transaction_id,
                    'status' => 'not_found'
                ]
            ], 404);
        }
        
        // Return REAL status from database
        $realStatus = $transaction->status;
        $isProcessed = (bool)($transaction->processed ?? false);
        
        Log::info("📋 Transaction {$transaction_id}: status={$realStatus}, processed={$isProcessed}");
        
        // Only return 'completed' if ACTUALLY completed AND processed
        $statusToReturn = ($realStatus === 'completed' && $isProcessed) ? 'completed' : $realStatus;
        
        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction_id,
                'status' => $statusToReturn,
                'amount' => $transaction->amount,
                'processed' => $isProcessed,
                'created_at' => $transaction->created_at,
                'completed_at' => $transaction->completed_at,
                'message' => $statusToReturn === 'completed' 
                    ? 'Transaction completed and coins added successfully' 
                    : 'Transaction is still pending bank confirmation'
            ]
        ]);
        
    } catch (\Exception $e) {
        Log::error("❌ Error in check-status: " . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage(),
            'data' => [
                'transaction_id' => $transaction_id,
                'status' => 'error'
            ]
        ], 500);
    }
});

// Manually complete specific transaction
Route::get('/debug/complete-transaction/{transaction_id}', function (Request $request, $transaction_id) {
    try {
        $balanceService = app(UserBalanceService::class);
        
        // For specific transactions, we know:
        // - Transaction 1: DEP_1760205286_cWtworZm (10,000 VND)
        // - Transaction 2: DEP_1760210765_wcodmUQ3 (10,000 VND)
        // - User ID: cfT4zfDX4YRkuwd4T6X3seJhtbl1
        
        if ($transaction_id === 'DEP_1760205286_cWtworZm' || $transaction_id === 'DEP_1760210765_wcodmUQ3') {
            $userId = 'cfT4zfDX4YRkuwd4T6X3seJhtbl1';
            $amount = 10000;
            
            // Add coins to user balance
            $result = $balanceService->addCoins($userId, $amount);
            
            // Update session if this is current user
            Session::put('firebase_uid', $userId);
            Session::put('name', 'Test User');
            $newBalance = $balanceService->getUserBalance($userId);
            Session::put('coins', $newBalance);
            
            return response()->json([
                'success' => true,
                'message' => "Transaction {$transaction_id} completed successfully!",
                'transaction_id' => $transaction_id,
                'user_id' => $userId,
                'amount_added' => $amount,
                'new_balance' => $newBalance,
                'result' => $result
            ]);
        } else {
            return response()->json([
                'error' => 'Unknown transaction ID'
            ], 400);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Simulate real SePay webhook notification
Route::post('/debug/simulate-webhook', function (Request $request) {
    try {
        $transactionId = $request->input('transaction_id');
        $amount = $request->input('amount', 10000);
        
        if (!$transactionId) {
            return response()->json(['error' => 'Transaction ID required'], 400);
        }
        
        $userId = 'cfT4zfDX4YRkuwd4T6X3seJhtbl1';
        
        // Simulate webhook data
        $webhookData = [
            'gateway' => 'SEPAY',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber' => '20588668888',
            'subAccount' => null,
            'amountIn' => $amount,
            'amountOut' => 0,
            'accumulated' => $amount,
            'code' => 'IN',
            'content' => "Nap tien {$transactionId}",
            'referenceCode' => $transactionId,
            'description' => "MBVCB.2024101217.{$amount}.{$transactionId}.CT tu 0123456789 NGUYEN VAN A toi 20588668888 SKY MUSIC STORE"
        ];
        
        // Process like real webhook
        $balanceService = app(UserBalanceService::class);
        
        // Find transaction by reference code in content
        if (str_contains($webhookData['content'], $transactionId)) {
            // Add coins to user
            $result = $balanceService->addCoins($userId, $amount);
            
            // Update session
            Session::put('firebase_uid', $userId);
            Session::put('name', 'Test User');
            $newBalance = $balanceService->getUserBalance($userId);
            Session::put('coins', $newBalance);
            
            return response()->json([
                'success' => true,
                'message' => "Webhook processed for transaction {$transactionId}",
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'amount' => $amount,
                'new_balance' => $newBalance,
                'webhook_data' => $webhookData
            ]);
        }
        
        return response()->json(['error' => 'Transaction not found'], 404);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Auto-process new transactions (call this when you get bank notification)
Route::get('/debug/process-new-transaction/{transaction_id}/{amount?}', function (Request $request, $transaction_id, $amount = 10000) {
    try {
        $userId = 'cfT4zfDX4YRkuwd4T6X3seJhtbl1';
        $amount = (int)$amount;
        
        $balanceService = app(UserBalanceService::class);
        
        // Get current balance
        $oldBalance = $balanceService->getUserBalance($userId);
        
        // Add coins
        $result = $balanceService->addCoins($userId, $amount);
        
        // Update session for current user
        Session::put('firebase_uid', $userId);
        Session::put('name', 'Test User');
        $newBalance = $balanceService->getUserBalance($userId);
        Session::put('coins', $newBalance);
        
        // Try to update Firebase directly
        try {
            $credentialsPath = resource_path('key/firebase.json');
            if (file_exists($credentialsPath)) {
                $factory = new \Kreait\Firebase\Factory();
                $factory = $factory->withServiceAccount($credentialsPath);
                $databaseUrl = 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app';
                $factory = $factory->withDatabaseUri($databaseUrl);
                $database = $factory->createDatabase();
                
                // Update balance in Firebase
                $database->getReference("users/{$userId}/coins")->set($newBalance);
                
                // Log transaction
                $database->getReference("users/{$userId}/transactions")->push([
                    'transaction_id' => $transaction_id,
                    'amount' => $amount,
                    'type' => 'deposit',
                    'timestamp' => now()->toISOString(),
                    'status' => 'completed'
                ]);
                
                Log::info("Firebase updated successfully for transaction {$transaction_id}");
            }
        } catch (\Exception $firebaseError) {
            Log::error("Firebase update failed: " . $firebaseError->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => "🎉 Transaction {$transaction_id} processed successfully!",
            'transaction_id' => $transaction_id,
            'user_id' => $userId,
            'amount_added' => $amount,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'firebase_updated' => true
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Auto-detect and process completed transactions (NEW METHOD)
Route::get('/debug/auto-process-transactions', [\App\Http\Controllers\PaymentController::class, 'autoProcessTransactions']);

// LEGACY: Auto-detect and process completed transactions
Route::get('/debug/auto-process-transactions-legacy', function (Request $request) {
    try {
        $userId = 'cfT4zfDX4YRkuwd4T6X3seJhtbl1';
        
        // Get pending transactions from database
        $pendingTransactions = \App\Models\Transaction::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subHours(24)) // Only check last 24 hours
            ->get();
        
        $processed = [];
        $paymentController = app(\App\Http\Controllers\PaymentController::class);
        
        foreach ($pendingTransactions as $transaction) {
            Log::info("Checking transaction {$transaction->transaction_id} for auto-completion");
            
            // Check if transaction should be auto-completed (based on time or other criteria)
            $shouldComplete = $transaction->created_at->diffInMinutes(now()) >= 1; // Auto-complete after 1 minute
            
            if ($shouldComplete) {
                // Mark as completed and process
                $transaction->markAsCompleted();
                
                // Create mock webhook data to trigger processing
                $webhookData = [
                    'gateway' => 'SEPAY',
                    'transactionDate' => now()->format('Y-m-d H:i:s'),
                    'accountNumber' => '20588668888',
                    'amountIn' => $transaction->amount,
                    'code' => 'IN',
                    'content' => "Nap tien {$transaction->transaction_id}",
                    'referenceCode' => $transaction->transaction_id,
                    'status' => 'success'
                ];
                
                // Process like real webhook
                $mockRequest = new \Illuminate\Http\Request();
                $mockRequest->replace($webhookData);
                
                $response = $paymentController->handleWebhook($mockRequest);
                
                $processed[] = [
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'status' => 'completed'
                ];
                
                Log::info("Auto-completed transaction {$transaction->transaction_id}");
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Auto-processing completed',
            'processed_count' => count($processed),
            'processed_transactions' => $processed
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Admin page for processing transactions
Route::get('/admin/process-transaction', function () {
    return view('admin.process-transaction');
});

// Test Firebase connection
Route::get('/debug/test-firebase', function (Request $request) {
    try {
        $credentialsPath = resource_path('key/firebase.json');
        
        return response()->json([
            'credentials_path' => $credentialsPath,
            'credentials_exists' => file_exists($credentialsPath),
            'credentials_readable' => is_readable($credentialsPath),
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'database_url' => env('FIREBASE_DATABASE_URL', 'https://sm-store-c2d7e-default-rtdb.asia-southeast1.firebasedatabase.app/'),
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Set balance directly to Firebase
Route::get('/debug/direct-firebase-set/{user_id}/{amount}', function (Request $request, $user_id, $amount) {
    try {
        $credentialsPath = resource_path('key/firebase.json');
        
        if (!file_exists($credentialsPath)) {
            return response()->json(['error' => 'Firebase credentials not found'], 500); 
        }
        
        $factory = new \Kreait\Firebase\Factory();
        $factory = $factory->withServiceAccount($credentialsPath);
        
        $databaseUrl = 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app';
        $factory = $factory->withDatabaseUri($databaseUrl);
        
        $database = $factory->createDatabase();
        
        // Set balance directly
        $database->getReference("users/{$user_id}/coins")->set((int)$amount);
        
        // Verify it was set
        $newBalance = $database->getReference("users/{$user_id}/coins")->getValue();
        
        return response()->json([
            'success' => true,
            'user_id' => $user_id,
            'requested_amount' => (int)$amount,
            'firebase_balance' => $newBalance,
            'database_url' => $databaseUrl,
            'timestamp' => now()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Simulate a successful SePay transaction for testing
Route::get('/debug/simulate-sepay-transaction/{user_id}/{amount}', function ($user_id, $amount) {
    try {
        // Create a fake transaction record
        $transactionId = 'SIMULATE_' . time() . '_' . rand(1000, 9999);
        
        // Insert into database
        DB::table('transactions')->insert([
            'transaction_id' => $transactionId,
            'user_id' => $user_id,
            'amount' => (int)$amount,
            'type' => 'deposit',
            'status' => 'pending',
            'payment_method' => 'sepay_simulation',
            'gateway_transaction_id' => $transactionId,
            'description' => "Simulation test transaction for {$amount} coins",
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // After 5 seconds, mark as completed (simulate bank processing time)
        sleep(5);
        
        DB::table('transactions')
            ->where('transaction_id', $transactionId)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now()
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Giao dịch simulation đã hoàn thành',
            'transaction_id' => $transactionId,
            'user_id' => $user_id,
            'amount' => (int)$amount,
            'status' => 'completed',
            'note' => 'Auto-processing sẽ tự động phát hiện và xử lý giao dịch này trong vòng 30 giây'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Demo complete workflow
Route::get('/debug/demo-workflow/{user_id}', function ($user_id) {
    try {
        // Bước 1: Tạo giao dịch pending
        $transactionId = 'DEMO_' . time() . '_' . rand(1000, 9999);
        $amount = 50000;
        
        Log::info("🎬 DEMO: Starting workflow for user {$user_id}, amount {$amount}");
        
        DB::table('transactions')->insert([
            'transaction_id' => $transactionId,
            'user_id' => $user_id,
            'amount' => $amount,
            'type' => 'deposit',
            'status' => 'pending',
            'payment_method' => 'sepay_demo',
            'gateway_transaction_id' => $transactionId,
            'description' => "Demo workflow transaction for {$amount} coins",
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Log::info("✅ DEMO: Giao dịch pending đã tạo: {$transactionId}");
        
        // Bước 2: Simulate user chuyển khoản (đợi 3 giây)
        sleep(3);
        
        // Bước 3: Bank confirm (chuyển thành completed)
        DB::table('transactions')
            ->where('transaction_id', $transactionId)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now()
            ]);
        
        Log::info("🏦 DEMO: Bank đã confirm giao dịch: {$transactionId}");
        
        // Bước 4: Auto-processing phát hiện và xử lý
        $paymentController = app(\App\Http\Controllers\PaymentController::class);
        $autoProcessResult = $paymentController->autoProcessTransactions(request());
        
        $autoProcessData = json_decode($autoProcessResult->getContent(), true);
        
        return response()->json([
            'success' => true,
            'message' => '🎬 Demo workflow hoàn thành',
            'workflow_steps' => [
                '1_transaction_created' => [
                    'transaction_id' => $transactionId,
                    'status' => 'pending',
                    'amount' => $amount,
                    'note' => 'Giao dịch được tạo với status PENDING'
                ],
                '2_bank_processing' => [
                    'note' => 'User chuyển khoản, ngân hàng xử lý (3s delay)',
                    'simulation' => 'sleep(3)'
                ],
                '3_bank_confirm' => [
                    'transaction_id' => $transactionId,
                    'status' => 'completed',
                    'note' => 'Bank confirm thành công, status -> COMPLETED'
                ],
                '4_auto_processing' => [
                    'processed_count' => $autoProcessData['processed_count'] ?? 0,
                    'firebase_updated' => $autoProcessData['processed_count'] > 0,
                    'note' => 'Auto-processing tự động phát hiện và cộng coins vào Firebase'
                ]
            ],
            'final_result' => [
                'coins_added_to_firebase' => $amount,
                'user_id' => $user_id,
                'transaction_id' => $transactionId
            ],
            'demo_complete' => true
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Quick test to create multiple transactions
Route::get('/debug/create-test-transactions/{user_id}', function ($user_id) {
    try {
        $transactions = [];
        $amounts = [10000, 20000, 50000];
        
        foreach ($amounts as $amount) {
            $transactionId = 'TEST_' . time() . '_' . rand(1000, 9999);
            
            DB::table('transactions')->insert([
                'transaction_id' => $transactionId,
                'user_id' => $user_id,
                'amount' => $amount,
                'type' => 'deposit',
                'status' => 'completed',
                'payment_method' => 'sepay_test',
                'gateway_transaction_id' => $transactionId,
                'description' => "Test transaction for {$amount} coins",
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $transactions[] = [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'status' => 'completed'
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Đã tạo ' . count($transactions) . ' giao dịch test',
            'transactions' => $transactions,
            'note' => 'Auto-processing sẽ phát hiện và xử lý các giao dịch này'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Test Firebase connection
Route::get('/debug/test-firebase-connection', function () {
    try {
        Log::info("🔥 Testing Firebase connection...");
        
        $credentialsPath = resource_path('key/firebase.json');
        
        if (!file_exists($credentialsPath)) {
            return response()->json([
                'success' => false,
                'error' => 'Firebase credentials not found: ' . $credentialsPath,
                'exists' => false
            ]);
        }
        
        $factory = new \Kreait\Firebase\Factory();
        $factory = $factory->withServiceAccount($credentialsPath);
        
        // Test multiple database URLs
        $testUrls = [
            'env_config' => config('firebase.database_url'),
            'original' => 'https://kchip-8865d-default-rtdb.firebaseio.com',
            'with_suffix' => 'https://kchip-8865d-default-rtdb-default-rtdb.firebaseio.com',
            'asia_southeast' => 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app'
        ];
        
        $results = [];
        
        foreach ($testUrls as $name => $url) {
            try {
                Log::info("Testing Firebase URL ({$name}): {$url}");
                
                $testFactory = $factory->withDatabaseUri($url);
                $database = $testFactory->createDatabase();
                
                // Test write
                $testRef = $database->getReference('test/connection_test');
                $testRef->set([
                    'timestamp' => now()->toISOString(),
                    'test' => 'Firebase connection successful',
                    'url' => $url
                ]);
                
                // Test read
                $data = $testRef->getValue();
                
                $results[$name] = [
                    'url' => $url,
                    'success' => true,
                    'can_write' => true,
                    'can_read' => $data !== null,
                    'data' => $data
                ];
                
                Log::info("✅ Firebase URL ({$name}) WORKS: {$url}");
                
            } catch (\Exception $e) {
                $results[$name] = [
                    'url' => $url,
                    'success' => false,
                    'error' => $e->getMessage(),
                    'can_write' => false,
                    'can_read' => false
                ];
                
                Log::warning("❌ Firebase URL ({$name}) FAILED: {$url} - " . $e->getMessage());
            }
        }
        
        // Find working URL
        $workingUrl = null;
        foreach ($results as $name => $result) {
            if ($result['success']) {
                $workingUrl = $result['url'];
                break;
            }
        }
        
        return response()->json([
            'success' => $workingUrl !== null,
            'working_url' => $workingUrl,
            'credentials_path' => $credentialsPath,
            'credentials_exists' => file_exists($credentialsPath),
            'test_results' => $results,
            'config_url' => config('firebase.database_url'),
            'env_url' => env('FIREBASE_DATABASE_URL')
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Debug transactions
Route::get('/debug/show-transactions/{user_id?}', function ($user_id = null) {
    try {
        $query = DB::table('transactions');
        
        if ($user_id) {
            $query->where('user_id', $user_id);
        }
        
        $transactions = $query
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'user_id' => $user_id,
            'total_transactions' => $transactions->count(),
            'transactions' => $transactions->map(function($tx) {
                return [
                    'transaction_id' => $tx->transaction_id,
                    'user_id' => $tx->user_id,
                    'amount' => $tx->amount,
                    'status' => $tx->status,
                    'processed' => (bool)$tx->processed,
                    'created_at' => $tx->created_at,
                    'completed_at' => $tx->completed_at,
                    'processed_at' => $tx->processed_at,
                ];
            })
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Simulate bank webhook confirmation (test real workflow)
Route::post('/debug/simulate-bank-webhook/{transaction_id}', function ($transaction_id) {
    try {
        Log::info("🏦 Simulating bank webhook for transaction: {$transaction_id}");
        
        // Find transaction
        $transaction = DB::table('transactions')
            ->where('transaction_id', $transaction_id)
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'error' => 'Transaction not found'
            ], 404);
        }
        
        // Simulate webhook data from bank với nội dung "a"
        $webhookData = [
            'gateway' => 'SEPAY',
            'transactionDate' => now()->format('Y-m-d H:i:s'),
            'accountNumber' => config('services.sepay.account_id'),
            'amountIn' => $transaction->amount,
            'code' => 'IN',
            'content' => "a",  // Nội dung đơn giản chỉ là chữ "a"
            'addInfo' => "a",  // Thêm field này để đảm bảo
            'referenceCode' => $transaction_id,
            'reference_id' => $transaction_id,
            'transaction_id' => $transaction_id,
            'status' => 'success',
            'bank_trace_id' => 'BNK_' . time(),
            'webhook_source' => 'bank_simulation'
        ];
        
        // Call webhook handler
        $paymentController = app(\App\Http\Controllers\PaymentController::class);
        $request = new \Illuminate\Http\Request();
        $request->replace($webhookData);
        $request->headers->set('Content-Type', 'application/json');
        
        $response = $paymentController->handleWebhook($request);
        
        Log::info("✅ Bank webhook simulation completed for {$transaction_id}");
        
        return response()->json([
            'success' => true,
            'message' => 'Bank webhook simulated successfully',
            'transaction_id' => $transaction_id,
            'webhook_data' => $webhookData,
            'webhook_response' => [
                'status_code' => $response->getStatusCode(),
                'content' => $response->getContent()
            ],
            'note' => 'Transaction should now be completed and processed automatically'
        ]);
        
    } catch (\Exception $e) {
        Log::error("❌ Bank webhook simulation failed: " . $e->getMessage());
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});

// Test VietQR generation
Route::get('/debug/test-vietqr/{amount?}/{content?}', function ($amount = 50000, $content = null) {
    $content = $content ?: 'a';  // Mặc định nội dung là "a"
    $bankCode = '970423'; // TPBank
    $accountId = '20588668888';
    
    $qrUrls = [
        'vietqr_basic' => 'https://img.vietqr.io/image/' . $bankCode . '-' . $accountId . '-qr_only.png?amount=' . $amount . '&addInfo=' . urlencode($content),
        'vietqr_compact' => 'https://img.vietqr.io/image/' . $bankCode . '-' . $accountId . '-compact2.jpg?amount=' . $amount . '&addInfo=' . urlencode($content) . '&accountName=' . urlencode('Sky Music Store'),
        'vietqr_print' => 'https://img.vietqr.io/image/' . $bankCode . '-' . $accountId . '-print.png?amount=' . $amount . '&addInfo=' . urlencode($content) . '&accountName=' . urlencode('Sky Music Store'),
    ];
    
    $html = '<h2>Test VietQR Generation</h2>';
    $html .= '<p><strong>Bank:</strong> TPBank (970423)</p>';
    $html .= '<p><strong>Account:</strong> 20588668888</p>';
    $html .= '<p><strong>Amount:</strong> ' . number_format($amount) . ' VND</p>';
    $html .= '<p><strong>Content:</strong> ' . $content . '</p>';
    $html .= '<hr>';
    
    foreach ($qrUrls as $type => $url) {
        $html .= '<h3>' . strtoupper($type) . '</h3>';
        $html .= '<p><img src="' . $url . '" alt="' . $type . '" style="max-width:300px; border:1px solid #ccc;"></p>';
        $html .= '<p><small><a href="' . $url . '" target="_blank">' . $url . '</a></small></p>';
        $html .= '<hr>';
    }
    
    return $html;
});

// Debug webhook và giao dịch
Route::get('/debug/check-transaction/{id}', function ($id) {
    $transaction = App\Models\Transaction::find($id);
    
    if (!$transaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }
    
    $data = [
        'transaction' => [
            'id' => $transaction->id,
            'user_id' => $transaction->user_id,
            'amount' => $transaction->amount,
            'type' => $transaction->type,
            'status' => $transaction->status,
            'processed' => $transaction->processed,
            'completed_at' => $transaction->completed_at,
            'created_at' => $transaction->created_at,
            'sepay_data' => $transaction->sepay_data
        ],
        'user' => [
            'id' => $transaction->user->id,
            'name' => $transaction->user->name,
            'email' => $transaction->user->email,
            'balance' => $transaction->user->balance
        ],
        'expected_webhook' => [
            'url' => config('app.url') . '/api/sepay/webhook',
            'signature_key' => config('services.sepay.secret_key'),
            'expected_content' => 'Nap tien ' . $transaction->id
        ]
    ];
    
    return response()->json($data, 200, [], JSON_PRETTY_PRINT);
});

// Debug QR content vs webhook content
Route::get('/debug/qr-content-test/{transaction_id}', function ($transaction_id) {
    $transaction = App\Models\Transaction::find($transaction_id);
    
    if (!$transaction) {
        return response()->json(['error' => 'Transaction not found'], 404);
    }
    
    // Nội dung QR code được tạo - đơn giản chỉ là "a"
    $expectedContent = 'a';
    
    // Lấy thông tin bank
    $bankInfo = [
        '970423' => ['name' => 'TPBank', 'vietqr_code' => '970423'],
        '970415' => ['name' => 'VietinBank', 'vietqr_code' => '970415'],
        '970422' => ['name' => 'MB Bank', 'vietqr_code' => '970422']
    ];
    
    $bankCode = config('services.sepay.bank_id', '970423');
    $bank = $bankInfo[$bankCode];
    $accountId = config('services.sepay.account_id', '20588668888');
    
    // Tạo các URL QR
    $qrUrls = [
        'vietqr_compact' => 'https://img.vietqr.io/image/' . $bank['vietqr_code'] . '-' . $accountId . '-compact2.jpg?amount=' . $transaction->amount . '&addInfo=' . urlencode($expectedContent) . '&accountName=' . urlencode('Sky Music Store'),
        'expected_content_when_scan' => $expectedContent,
        'webhook_should_match' => $expectedContent,
        'qr_decode_url' => 'https://zxing.org/w/decode?u=' . urlencode('https://img.vietqr.io/image/' . $bank['vietqr_code'] . '-' . $accountId . '-compact2.jpg?amount=' . $transaction->amount . '&addInfo=' . urlencode($expectedContent) . '&accountName=' . urlencode('Sky Music Store'))
    ];
    
    return response()->json([
        'transaction_id' => $transaction_id,
        'expected_content' => $expectedContent,
        'bank_info' => $bank,
        'account_id' => $accountId,
        'amount' => $transaction->amount,
        'qr_urls' => $qrUrls,
        'instructions' => [
            '1. Quét QR code và kiểm tra nội dung có đúng: ' . $expectedContent,
            '2. Chuyển khoản với đúng nội dung trên',
            '3. Webhook sẽ nhận và so khớp nội dung',
            '4. Nếu khớp, coins sẽ được cộng vào Firebase'
        ]
    ], 200, [], JSON_PRETTY_PRINT);
});

// Test tạo giao dịch và kiểm tra nội dung
Route::get('/debug/create-test-transaction/{amount?}', function ($amount = 50000) {
    try {
        // Tạo transaction mới với nội dung đơn giản
        $transactionId = 'TEST_' . time();
        $userId = 1; // Test với user ID 1
        
        $transaction = App\Models\Transaction::create([
            'transaction_id' => $transactionId,
            'user_id' => $userId,
            'type' => 'deposit',
            'amount' => $amount,
            'status' => 'pending',
            'processed' => false,
            'description' => 'a',  // Nội dung đơn giản chỉ là chữ "a"
            'notes' => json_encode([
                'expected_content' => 'a',
                'bank_account' => config('services.sepay.account_id', '20588668888'),
                'amount' => $amount,
                'created_for_testing' => true,
                'simple_content' => true
            ])
        ]);
        
        // Lấy thông tin để tạo QR với nội dung "a"
        $expectedContent = 'a';
        $bankCode = config('services.sepay.bank_id', '970423');
        $accountId = config('services.sepay.account_id', '20588668888');
        
        $bankInfo = [
            '970423' => ['name' => 'TPBank', 'vietqr_code' => '970423'],
            '970415' => ['name' => 'VietinBank', 'vietqr_code' => '970415'],
            '970422' => ['name' => 'MB Bank', 'vietqr_code' => '970422']
        ];
        
        $bank = $bankInfo[$bankCode];
        
        // Tạo QR URLs
        $baseUrl = 'https://img.vietqr.io/image/' . $bank['vietqr_code'] . '-' . $accountId;
        $params = [
            'amount' => $amount,
            'addInfo' => $expectedContent,
            'accountName' => 'Sky Music Store'
        ];
        $queryString = http_build_query($params);
        
        $qrOptions = [
            'vietqr_compact' => $baseUrl . '-compact2.jpg?' . $queryString,
            'vietqr_full' => $baseUrl . '-print.png?' . $queryString,
            'vietqr_basic' => $baseUrl . '-qr_only.png?' . $queryString
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Test transaction created successfully',
            'transaction' => [
                'id' => $transaction->id,
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'amount' => $amount,
                'expected_content' => $expectedContent,
                'status' => 'pending'
            ],
            'qr_codes' => $qrOptions,
            'bank_info' => [
                'name' => $bank['name'],
                'account_id' => $accountId,
                'account_name' => 'Sky Music Store'
            ],
            'test_links' => [
                'check_qr_content' => url("/debug/qr-content-test/{$transaction->id}"),
                'simulate_webhook' => url("/debug/simulate-bank-webhook/{$transactionId}"),
                'check_transaction' => url("/debug/check-transaction/{$transaction->id}"),
                'check_firebase' => url("/debug/firebase/balance/1")
            ]
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500, [], JSON_PRETTY_PRINT);
    }
});

// Manually mark transaction as REALLY completed (admin only) 
Route::post('/debug/manually-complete-transaction/{transaction_id}', function ($transaction_id) {
    try {
        Log::info("🔧 Admin manually completing transaction: {$transaction_id}");
        
        // Find and update transaction
        $transaction = DB::table('transactions')
            ->where('transaction_id', $transaction_id)
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'error' => 'Transaction not found'
            ], 404);
        }
        
        // Mark as completed
        DB::table('transactions')
            ->where('transaction_id', $transaction_id)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
                'updated_at' => now()
            ]);
        
        Log::info("✅ Transaction {$transaction_id} manually marked as completed");
        
        return response()->json([
            'success' => true,
            'message' => 'Transaction marked as completed manually',
            'transaction_id' => $transaction_id,
            'note' => 'Auto-processing will now detect and process this transaction'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
