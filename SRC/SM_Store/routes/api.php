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

<<<<<<< HEAD
// Payment API - Create deposit (no CSRF required)
Route::post('/payment/create', [\App\Http\Controllers\Account\PaymentController::class, 'createDeposit'])
    ->middleware('web'); // Cần web middleware để có session

// Payment API - Check transaction status
Route::get('/payment/status', [\App\Http\Controllers\Account\PaymentController::class, 'checkPaymentStatus'])
    ->middleware('web'); // Cần web middleware để có session

// Test payment endpoint without CSRF
Route::post('/test-payment', function(Request $request) {
    \Log::info('🚀 API Test payment endpoint called', ['data' => $request->all()]);
    
    try {
        $amount = $request->input('amount', 50000);
        $userId = $request->input('user_id', 'test_user');
        
        // Thông tin ngân hàng
        $bankInfo = [
            'bank_id' => '970423',  // TPBank
            'bank_name' => 'TPBank',
            'account_number' => '20588668888',
            'account_name' => 'Sky Music Store',
            'content' => $userId  // Nội dung là UID
        ];
        
        // Tạo QR Code với VietQR API
        $qrUrl = 'https://img.vietqr.io/image/' 
                . $bankInfo['bank_id'] . '-' 
                . $bankInfo['account_number'] . '-compact2.jpg'
                . '?amount=' . $amount
                . '&addInfo=' . urlencode($userId)
                . '&accountName=' . urlencode($bankInfo['account_name']);
        
        return response()->json([
            'success' => true,
            'message' => 'Test payment endpoint working!',
            'data' => [
                'transaction_id' => 'TEST_' . uniqid(),
                'amount' => $amount,
                'user_id' => $userId,
                'qr_code' => $qrUrl,
                'bank_info' => $bankInfo
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('❌ API Test payment error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// SePay Webhook - Override package route
Route::post('/sepay/webhook', [\App\Http\Controllers\Account\PaymentController::class, 'handleWebhook'])
    ->name('sepay.webhook.custom')
    ->withoutMiddleware(['throttle:api']);

=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
// Cart API routes
Route::prefix('cart')->group(function () {
    Route::post('/can-add', [App\Http\Controllers\CartController::class, 'canAddToCart']);
    Route::get('/purchased-products', [App\Http\Controllers\CartController::class, 'getPurchasedProducts']);
    Route::post('/validate', [App\Http\Controllers\CartController::class, 'validateCart']);
});
<<<<<<< HEAD

// Product API routes
Route::get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'getProductById'])
    ->middleware('web');

=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
