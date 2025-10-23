<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserRoleController;
// SalerController removed - using Seller\ProductController instead
use App\Http\Controllers\Account\AccountController;
<<<<<<< HEAD
use App\Http\Controllers\Account\CartController;
=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\DashboardController;
use Kreait\Firebase\Contract\Auth;

<<<<<<< HEAD
use App\Http\Controllers\Account\PaymentController;
=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
Route::get('/', fn() => view('page.home.index'))->name('home')->middleware('load.user');

// Debug route để xem Firebase users
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

Route::get('/community', fn() => view('page.community.index'))->name('community.index')->middleware('load.user');
Route::get('/community/post/{id}', fn($id) => view('page.community.post-detail'))->name('community.post-detail')->middleware('load.user');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index')->middleware('load.user');
Route::post('/shop/filter', [ShopController::class, 'filter'])->name('shop.filter');
<<<<<<< HEAD
// Redirect old cart route to new account cart
Route::get('/shop/cart', fn() => redirect()->route('account.cart'))->name('shop.cart')->middleware('load.user');
Route::get('/shop/checkout', [CheckoutController::class, 'showCheckout'])->name('shop.checkout')->middleware(['firebase.auth', 'load.user']);
Route::post('/shop/checkout/process', [CheckoutController::class, 'processCheckout'])->name('shop.checkout.process')->middleware(['firebase.auth', 'load.user']);
=======
Route::get('/shop/cart', fn() => view('page.shop.cart'))->name('shop.cart')->middleware('load.user');
Route::post('/shop/checkout', [CheckoutController::class, 'processCheckout'])->name('shop.checkout')->middleware(['firebase.auth', 'load.user']);
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836

Route::get('/support', fn() => view('page.support.index'))->name('support.index')->middleware('load.user');

// Test route for purchase validation
Route::get('/test/purchase-validation', fn() => view('test.purchase-validation'))->name('test.purchase-validation')->middleware('load.user');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::prefix('saler')
    ->middleware(['firebase.auth', 'role:saler', 'load.user'])
    ->group(function () {

        // Dashboard với thống kê
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('saler.dashboard');

        // Các route khác:
        Route::get('/orders', [OrderController::class, 'index'])->name('saler.orders');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('saler.orders.detail');
        Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('saler.orders.update-status');
        Route::get('/users', fn() => view('saler.users.index'))->name('saler.users');
        Route::get('/posts', fn() => view('saler.posts.index'))->name('saler.posts');
        Route::get('/posts/create', fn() => view('saler.posts.create'))->name('saler.posts.create');
        Route::get('/profile', fn() => view('saler.profile.index'))->name('saler.profile');
        Route::get('/settings', fn() => view('saler.settings.index'))->name('saler.settings');

        // Quản lý sản phẩm
        Route::resource('products', \App\Http\Controllers\Seller\ProductController::class)->names([
            'index' => 'saler.products.index',
            'create' => 'saler.products.create',
            'store' => 'saler.products.store',
            'show' => 'saler.products.show',
            'edit' => 'saler.products.edit',
            'update' => 'saler.products.update',
            'destroy' => 'saler.products.destroy'
        ]);

        // API routes cho product management
        Route::post('/products/preview-file', [\App\Http\Controllers\Seller\ProductController::class, 'previewFile'])->name('saler.products.preview-file');
        Route::patch('/products/{id}/toggle-status', [\App\Http\Controllers\Seller\ProductController::class, 'toggleStatus'])->name('seller.products.toggle-status');
    });

Route::prefix('admin')
    ->middleware(['firebase.auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/roles', [UserRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles/{uid}', [UserRoleController::class, 'updateRole'])->name('admin.roles.update');


        Route::get('/products', fn() => view('admin.products.products'))->name('admin.products');
        Route::get('/products/edit/{id}', fn($id) => view('admin.products.edit'))->name('admin.products.edit');
        Route::get('/orders', fn() => view('admin.orders.orders'))->name('admin.orders');
        Route::get('/users', fn() => view('admin.users.users'))->name('admin.users');
        Route::get('/analytics', fn() => view('admin.analytics.analytics'))->name('admin.analytics');
        Route::get('/settings', fn() => view('admin.settings.settings'))->name('admin.settings');
        Route::get('/posts', fn() => view('admin.posts.posts'))->name('admin.posts');
        Route::get('/posts/create', fn() => view('admin.posts.create'))->name('admin.posts.create');
        // Route::post('/posts/create', [PostController::class, 'store'])->name('admin.posts.store');
        Route::get('/posts/edit/{id}', fn($id) => view('admin.posts.edit'))->name('admin.posts.edit');
    });

Route::prefix('account')
    ->middleware(['firebase.auth', 'load.user'])
    ->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('account.index');
        Route::get('/sheets', [AccountController::class, 'sheets'])->name('account.sheets');
        Route::get('/activity', [AccountController::class, 'activity'])->name('account.activity');
        Route::get('/settings', [AccountController::class, 'settings'])->name('account.settings');
        Route::put('/update', [AccountController::class, 'updateProfile'])->name('account.update');
        Route::get('/deposit', [AccountController::class, 'deposit'])->name('account.deposit');
        Route::get('/withdraw', [AccountController::class, 'withdraw'])->name('account.withdraw');
<<<<<<< HEAD
        Route::post('/withdraw/process', [AccountController::class, 'processWithdraw'])->name('account.withdraw.process');
        Route::get('/download/{id}', [AccountController::class, 'downloadSheet'])->name('account.download');
        
        // Cart routes
        Route::get('/cart', [CartController::class, 'index'])->name('account.cart');
    });




// Trang thanh toán paycart
Route::get('/account/paycart', fn() => view('account.paycart'))->name('account.paycart')->middleware(['firebase.auth', 'load.user']);
Route::post('/paycart/confirm', [App\Http\Controllers\Account\PaymentController::class, 'confirmCartPayment'])
    ->name('account.paycart.confirm');

//show 
Route::get('/account/sheets', [App\Http\Controllers\Account\AccountController::class, 'showMySheets'])
    ->name('account.sheets');



Route::prefix('payment')
    ->middleware(['firebase.auth', 'load.user'])
    ->group(function () {
        Route::get('/deposit', [PaymentController::class, 'showDepositForm'])->name('payment.deposit');
        Route::post('/deposit/create', [PaymentController::class, 'createDeposit'])->name('payment.deposit.create');
        Route::get('/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check.status');
    });

// Webhook SePay (API không cần auth)
Route::post('/api/sepay/webhook', [PaymentController::class, 'handleWebhook']);

// Cart API routes (cần auth)
Route::prefix('api/cart')
    ->middleware(['firebase.auth'])
    ->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/remove', [CartController::class, 'removeFromCart']);
        Route::post('/update', [CartController::class, 'updateQuantity']);
        Route::post('/clear', [CartController::class, 'clearCart']);
    });

// Debug routes cho test SePay
Route::prefix('debug')->group(function() {
    // Test add to cart
    Route::get('/test-cart', function() {
        $userId = session('firebase_uid');
        if (!$userId) {
            return response()->json(['error' => 'Not logged in. User ID: ' . session('firebase_uid')]);
        }
        
        $cartController = app(\App\Http\Controllers\Account\CartController::class);
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'product_id' => 'test_product_' . time(),
            'name' => 'Test Product',
            'price' => 100000,
            'image' => 'test.jpg',
            'quantity' => 1
        ]);
        
        return $cartController->addToCart($request);
    })->middleware(['firebase.auth'])->name('debug.test-cart');
    
    Route::get('/transactions/{userId?}', function($userId = null) {
        $transactionService = app(\App\Services\TransactionService::class);
        $userId = $userId ?? session('firebase_uid');
        
        if (!$userId) {
            return response()->json(['error' => 'No user ID provided']);
        }
        
        $transactions = $transactionService->getUserTransactions($userId);
        
        return response()->json([
            'user_id' => $userId,
            'transactions' => $transactions
        ]);
    })->name('debug.transactions');
    
    Route::get('/check-status/{transactionId}', function($transactionId) {
        $transactionService = app(\App\Services\TransactionService::class);
        $transaction = $transactionService->getTransactionById($transactionId);
        
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found']);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction['data']['transaction_id'],
                'status' => $transaction['data']['status'],
                'amount' => $transaction['data']['amount'],
                'processed' => $transaction['data']['processed'] ?? false,
                'created_at' => $transaction['data']['created_at'] ?? null,
                'completed_at' => $transaction['data']['completed_at'] ?? null
            ]
        ]);
    })->name('debug.check.status');
    
    // Test payment endpoint không cần auth
    Route::post('/test-payment', function(Request $request) {
        Log::info('🚀 Test payment endpoint called', ['data' => $request->all()]);
        
        try {
            $amount = $request->input('amount', 50000);
            $userId = $request->input('user_id', 'test_user');
            
            return response()->json([
                'success' => true,
                'message' => 'Test payment endpoint working!',
                'data' => [
                    'transaction_id' => 'TEST_' . uniqid(),
                    'amount' => $amount,
                    'user_id' => $userId,
                    'qr_code' => 'https://via.placeholder.com/300x300.png?text=TEST+QR',
                    'bank_info' => [
                        'bank_name' => 'TPBank',
                        'account_number' => '20588668888',
                        'account_name' => 'Sky Music Store',
                        'content' => 'Nap tien ' . $userId
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Test payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    })->withoutMiddleware(['csrf']);
    
    Route::post('/manual-webhook', function(Request $request) {
        $paymentController = app(\App\Http\Controllers\Account\PaymentController::class);
        return $paymentController->handleWebhook($request);
    })->name('debug.manual.webhook');
    
    Route::get('/test-webhook/{amount?}/{uid?}', function($amount = 25000, $uid = null) {
        $uid = $uid ?? session('firebase_uid') ?? 'test_user_123';
        
        // Simulate SePay webhook data
        $webhookData = [
            'transferAmount' => (int)$amount,
            'content' => "Nap tien {$uid} cho tai khoan",
            'referenceCode' => 'SEPAY_' . time(),
            'gateway' => 'TPBank',
            'transferTime' => now()->toISOString(),
            'bankCode' => '970423',
            'accountNumber' => '20588668888'
        ];
        
        $paymentController = app(\App\Http\Controllers\Account\PaymentController::class);
        $result = $paymentController->handleWebhook(new Request($webhookData));
        
        return response()->json([
            'webhook_data' => $webhookData,
            'result' => $result->getData()
        ]);
    })->name('debug.test.webhook');
});
=======
        Route::get('/download/{id}', [AccountController::class, 'downloadSheet'])->name('account.download');
    });
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
