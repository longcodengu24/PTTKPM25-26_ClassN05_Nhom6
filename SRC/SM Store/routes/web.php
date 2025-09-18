<<<<<<< HEAD

<?php


=======



<?php
>>>>>>> 6a4cff30571c1cb4a7741bee36c2e6b149f00755
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
})->name('community.index');

// Community post detail
Route::get('/community/post/{id}', function ($id) {
    // In a real app, you would fetch post by $id
    return view('page.community.post-detail');
})->name('community.post-detail');



Route::get('/shop', function () {
    return view('page.shop.index');
});
// Shop index page (for cart link)
Route::get('/shop', function () {
    return view('page.shop.index');
})->name('shop.index');
// Shop Cart page (moved to page/shop/cart.blade.php)
Route::get('/shop/cart', function () {
    return view('page.shop.cart');
})->name('shop.cart');

Route::get('/support', function () {
    return view('page.support.index');
});
Route::get('/admin', function () {
    return view('layouts.admin');
});

Route::get('/account', function () {
    return view('layouts.account');
});
Route::get('/account/settings', function () {
    return view('account.settings');
})->name('account.settings');
Route::get('/app', function () {
    return view('layouts.app');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
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

    // Route trang sửa sheet nhạc
    Route::get('/products/edit/{id}', function ($id) {
        // Trong thực tế sẽ lấy dữ liệu theo $id
        return view('admin.products.edit');
    })->name('admin.products.edit');

    Route::get('/orders', function () {
        return view('admin.orders.orders');
    })->name('admin.orders');

    Route::get('/users', function () {
        return view('admin.users.users');
    })->name('admin.users');

    Route::get('/analytics', function () {
        return view('admin.analytics.analytics');
    })->name('admin.analytics');

    Route::get('/settings', function () {
        return view('admin.settings.settings');
    })->name('admin.settings');

    Route::get('/posts', function () {
        return view('admin.posts.posts');
    })->name('admin.posts');


    Route::get('/posts/create', function () {
        return view('admin.posts.create');
    })->name('admin.posts.create');
    Route::post('/posts/create', [\App\Http\Controllers\Admin\PostController::class, 'store'])->name('admin.posts.store');

    Route::get('/posts/edit/{id}', function ($id) {
        return view('admin.posts.edit');
    })->name('admin.posts.edit');
});

<<<<<<< HEAD
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
=======
// Account section



>>>>>>> 6a4cff30571c1cb4a7741bee36c2e6b149f00755


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

