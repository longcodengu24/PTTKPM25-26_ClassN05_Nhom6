<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->get('/ping', function (Request $request) {
    return response()->json(['message' => 'pong']);
});

// Cart API routes
Route::prefix('cart')->group(function () {
    Route::post('/can-add', [App\Http\Controllers\CartController::class, 'canAddToCart']);
    Route::get('/purchased-products', [App\Http\Controllers\CartController::class, 'getPurchasedProducts']);
    Route::post('/validate', [App\Http\Controllers\CartController::class, 'validateCart']);
});
