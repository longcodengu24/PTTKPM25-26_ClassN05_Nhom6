<?php

use Illuminate\Support\Facades\Route;

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


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
