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

// SePay Webhook để nhận thông báo từ ngân hàng
Route::post('/sepay/webhook', [\App\Http\Controllers\PaymentController::class, 'handleWebhook'])
    ->name('sepay.webhook');

// SePay webhook routes (no middleware required)
use App\Http\Controllers\PaymentController;
Route::post('/sepay/webhook', [PaymentController::class, 'handleWebhook']);

// SePay API routes
Route::prefix('sepay')->group(function () {
    Route::get('/transaction/{transactionId}/status', [PaymentController::class, 'getTransactionStatus']);
    Route::post('/test-webhook', [PaymentController::class, 'testWebhook']);
    Route::get('/balance/{userId}', [PaymentController::class, 'getUserBalance']);
});
