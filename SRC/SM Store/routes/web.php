<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

use function Laravel\Prompts\password;

Route::get('/', function () {
    return view('page.home.index');
});
Route::get('/community', function () {
    return view('page.community.index');
});
// Route chi tiết bài viết cộng đồng
use App\Http\Controllers\CommunityController;
Route::get('/community/post/{id}', [CommunityController::class, 'show'])->name('community.post-detail');
Route::get('/shop', function () {
    return view('page.shop.index');
});
Route::get('/support', function () {
    return view('page.support.index');
});
Route::get('/admin', function () {
    return view('layouts.admin');
});

Route::get('/login', function () {
    return view('auth.login');
});
Route::get('register', function(){
    return view('auth.register');
});
Route::get('/forgot-password',function(){
    return view('auth.forgot-password');
});
Route::get('/reset-password',function(){
    return view('auth.reset-password');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard.dashboard');
    })->name('admin.dashboard');

    Route::get('/products', function () {
        return view('admin.products.products');
    })->name('admin.products');

    Route::get('/products/edit', function () {
        return view('admin.products.edit');
    })->name('admin.products.edit');

    Route::get('/products/products', function () {
        return view('admin.products.products');
    })->name('admin.products.products');

    Route::get('/orders', function () {
        return view('admin.orders.orders');
    })->name('admin.orders');

    Route::get('/posts', function () {
        return view('admin.posts.posts');
    })->name('admin.posts');

    Route::get('/posts/edit', function () {
        return view('admin.posts.edit');
    })->name('admin.posts.edit');

    Route::get('/posts/create', function () {
        return view('admin.posts.create');
    })->name('admin.posts.create');

    Route::get('/posts/posts', function () {
        return view('admin.posts.posts');
    })->name('admin.posts.posts');


    Route::get('/users', function () {
        return view('admin.users.users');
    })->name('admin.users');

    Route::get('/analytics', function () {
        return view('admin.analytics.analytics');
    })->name('admin.analytics');

    Route::get('/settings', function () {
        return view('admin.settings.settings');
    })->name('admin.settings');
});

Route::get('/firebase/ping', function (FirebaseAuth $auth) {
    try {
        // Simple call that exercises credentials; listUser is lightweight and requires auth scope
        $list = $auth->listUsers(1);
        return response()->json([
            'ok' => true,
            'message' => 'Firebase connected',
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

