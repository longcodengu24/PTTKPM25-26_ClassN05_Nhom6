<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Auth;

// Controllers
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\UserRoleController;
use App\Http\Controllers\Saler\SalerController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\PaymentController;
use App\Services\UserBalanceService;

/*
|--------------------------------------------------------------------------
| Trang chủ & Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('page.home.index'))->name('home')->middleware('load.user');
Route::get('/community', fn() => view('page.community.index'))->name('community.index')->middleware('load.user');
Route::get('/community/post/{id}', fn($id) => view('page.community.post-detail'))->name('community.post-detail')->middleware('load.user');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index')->middleware('load.user');
Route::post('/shop/filter', [ShopController::class, 'filter'])->name('shop.filter');
Route::get('/shop/cart', fn() => view('page.shop.cart'))->name('shop.cart')->middleware('load.user');
Route::get('/support', fn() => view('page.support.index'))->name('support.index')->middleware('load.user');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

/*
|--------------------------------------------------------------------------
| Saler (Người bán)
|--------------------------------------------------------------------------
*/
Route::prefix('saler')
    ->middleware(['firebase.auth', 'role:saler', 'load.user'])
    ->group(function () {
        Route::get('/dashboard', fn() => view('saler.dashboard'))->name('saler.dashboard');
        Route::get('/orders', fn() => view('saler.orders.index'))->name('saler.orders');
        Route::get('/orders/{id}', fn($id) => view('saler.orders.detail'))->name('saler.orders.detail');
        Route::get('/users', fn() => view('saler.users.index'))->name('saler.users');
        Route::get('/posts', fn() => view('saler.posts.index'))->name('saler.posts');
        Route::get('/posts/create', fn() => view('saler.posts.create'))->name('saler.posts.create');
        Route::get('/analytics', fn() => view('saler.analytics.index'))->name('saler.analytics');
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

        Route::post('/products/preview-file', [\App\Http\Controllers\Seller\ProductController::class, 'previewFile'])->name('saler.products.preview-file');
        Route::patch('/products/{id}/toggle-status', [\App\Http\Controllers\Seller\ProductController::class, 'toggleStatus'])->name('saler.products.toggle-status');
    });

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['firebase.auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard.dashboard'))->name('admin.dashboard');
        Route::get('/roles', [UserRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles/{uid}', [UserRoleController::class, 'updateRole'])->name('admin.roles.update');

        Route::view('/products', 'admin.products.products')->name('admin.products');
        Route::view('/orders', 'admin.orders.orders')->name('admin.orders');
        Route::view('/users', 'admin.users.users')->name('admin.users');
        Route::view('/analytics', 'admin.analytics.analytics')->name('admin.analytics');
        Route::view('/settings', 'admin.settings.settings')->name('admin.settings');
        Route::view('/posts', 'admin.posts.posts')->name('admin.posts');
        Route::view('/posts/create', 'admin.posts.create')->name('admin.posts.create');
        Route::view('/posts/edit/{id}', 'admin.posts.edit')->name('admin.posts.edit');
    });

/*
|--------------------------------------------------------------------------
| Account (Người dùng)
|--------------------------------------------------------------------------
*/
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
        Route::get('/download/{id}', [AccountController::class, 'downloadSheet'])->name('account.download');
    });

/*
|--------------------------------------------------------------------------
| Payment & SePay
|--------------------------------------------------------------------------
*/
Route::prefix('payment')
    ->middleware(['firebase.auth', 'load.user'])
    ->group(function () {
        Route::get('/deposit', [PaymentController::class, 'showDepositForm'])->name('payment.deposit');
        Route::post('/deposit/create', [PaymentController::class, 'createDeposit'])->name('payment.deposit.create');
        Route::get('/check-status/{transaction_id}', [PaymentController::class, 'checkTransactionStatus'])->name('payment.check.status');
    });

// Webhook SePay (API không cần auth)
Route::post('/api/sepay/webhook', [PaymentController::class, 'handleWebhook']);
Route::post('/sepay/manual-webhook', [PaymentController::class, 'manualWebhook']);

/*
|--------------------------------------------------------------------------
| Debug & Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/debug-users', function (Auth $auth) {
    try {
        $users = $auth->listUsers();
        echo "<h2>Firebase Users</h2><table border='1' cellpadding='5'><tr><th>Email</th><th>Name</th><th>UID</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr><td>{$user->email}</td><td>{$user->displayName}</td><td>{$user->uid}</td><td>{$user->metadata->createdAt->format('Y-m-d H:i:s')}</td></tr>";
        }
        echo "</table>";
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Firebase connection test
Route::get('/test/firebase', function () {
    try {
        $credentialsPath = config('firebase.credentials');
        if (!$credentialsPath || !file_exists($credentialsPath)) {
            throw new \Exception("Firebase credentials not found at: {$credentialsPath}");
        }

        $factory = (new \Kreait\Firebase\Factory())
            ->withServiceAccount($credentialsPath)
            ->withDatabaseUri('https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app');

        $database = $factory->createDatabase();
        $ref = $database->getReference('test/connection');
        $ref->set([
            'timestamp' => now()->toISOString(),
            'message' => 'Firebase connection OK'
        ]);

        return response()->json([
            'success' => true,
            'path' => $credentialsPath,
            'data' => $ref->getValue()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});


