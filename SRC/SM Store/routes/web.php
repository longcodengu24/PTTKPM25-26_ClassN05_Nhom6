<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

Route::get('/', function () {
    return view('page.home.index');
});
Route::get('/community', function () {
    return view('page.community.index');
});
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

