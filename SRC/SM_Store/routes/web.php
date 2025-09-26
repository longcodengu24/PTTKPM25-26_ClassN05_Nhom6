<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserRoleController;

Route::get('/', fn () => view('page.home.index'))->name('home');

Route::get('/community', fn () => view('page.community.index'))->name('community.index');
Route::get('/community/post/{id}', fn ($id) => view('page.community.post-detail'))->name('community.post-detail');

Route::get('/shop', fn () => view('page.shop.index'))->name('shop.index');
Route::get('/shop/cart', fn () => view('page.shop.cart'))->name('shop.cart');

Route::get('/support', fn () => view('page.support.index'))->name('support.index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

Route::get('/saler/dashboard', fn () => view('saler.dashboard'))
    ->name('saler.dashboard')
    ->middleware(['firebase.auth', 'role:saler']);

Route::prefix('admin')
    ->middleware(['firebase.auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', fn () => view('admin.dashboard.dashboard'))->name('admin.dashboard');

        Route::get('/roles', [UserRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles/{uid}', [UserRoleController::class, 'updateRole'])->name('admin.roles.update');


        Route::get('/products', fn () => view('admin.products.products'))->name('admin.products');
        Route::get('/products/edit/{id}', fn ($id) => view('admin.products.edit'))->name('admin.products.edit');
        Route::get('/orders', fn () => view('admin.orders.orders'))->name('admin.orders');
        Route::get('/users', fn () => view('admin.users.users'))->name('admin.users');
        Route::get('/analytics', fn () => view('admin.analytics.analytics'))->name('admin.analytics');
        Route::get('/settings', fn () => view('admin.settings.settings'))->name('admin.settings');
        Route::get('/posts', fn () => view('admin.posts.posts'))->name('admin.posts');
        Route::get('/posts/create', fn () => view('admin.posts.create'))->name('admin.posts.create');
        Route::post('/posts/create', [PostController::class, 'store'])->name('admin.posts.store');
        Route::get('/posts/edit/{id}', fn ($id) => view('admin.posts.edit'))->name('admin.posts.edit');
    });

Route::prefix('account')->group(function () {
    Route::get('/', function () {
        return view('account.index');
    })->name('account.index');
    Route::get('/profile', function () {
        return view('account.profile');
    })->name('account.profile');
    Route::get('/sheets', function () {
        return view('account.sheets');
    })->name('account.sheets');
    Route::get('/posts', function () {
        return view('account.posts');
    })->name('account.posts');
    Route::get('/activity', function () {
        return view('account.activity');
    })->name('account.activity');
    Route::get('/settings', function () {
        return view('account.settings');
    })->name('account.settings');
    // Nạp coin (Deposit) page
    Route::get('/account/deposit', function () {
        return view('account.deposit');
    })->name('account.deposit');
    // Rút coin (Withdraw) page
    Route::get('/account/withdraw', function () {
        return view('account.withdraw');
    })->name('account.withdraw');


});

 