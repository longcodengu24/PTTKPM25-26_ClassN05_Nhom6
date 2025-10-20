<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
// use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserRoleController;
// SalerController removed - using Seller\ProductController instead
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\DashboardController;
use Kreait\Firebase\Contract\Auth;

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
Route::get('/shop/cart', fn() => view('page.shop.cart'))->name('shop.cart')->middleware('load.user');
Route::post('/shop/checkout', [CheckoutController::class, 'processCheckout'])->name('shop.checkout')->middleware(['firebase.auth', 'load.user']);

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
        Route::get('/download/{id}', [AccountController::class, 'downloadSheet'])->name('account.download');
    });
