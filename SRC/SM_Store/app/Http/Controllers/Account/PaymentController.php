<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FirestoreRestService;
use App\Services\UserPurchaseService;
use App\Services\SheetActivityService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $firestore;
    protected $userPurchaseService;
    protected $sheetActivityService;

    public function __construct(FirestoreRestService $firestore, UserPurchaseService $userPurchaseService, SheetActivityService $sheetActivityService)
    {
        $this->firestore = $firestore;
        $this->userPurchaseService = $userPurchaseService;
        $this->sheetActivityService = $sheetActivityService;
    }

    public function showDepositForm()
    {
        return view('account.deposit');
    }

    public function createDeposit(Request $request)
    {
        Log::info('💳 createDeposit called', ['data' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:10000|max:50000000',
                'user_id' => 'nullable|string'
            ]);

            $userId = $validated['user_id'] ?? session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập'
                ], 401);
            }

            $amount = $validated['amount'];

            Log::info('✅ Deposit request created', [
                'user_id' => $userId,
                'amount' => $amount
            ]);

            // Tạo QR code bằng VietQR - Nội dung chuyển khoản là Firebase UID
            $qrUrl = "https://img.vietqr.io/image/970423-20588668888-print.jpg?amount={$amount}&addInfo=" . urlencode($userId) . "&accountName=" . urlencode("NGUYEN KHAC LONG");

            $this->logActivity($userId, 'deposit_requested', [
                'amount' => $amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo yêu cầu nạp tiền thành công',
                'data' => [
                    'amount' => $amount,
                    'qr_url' => $qrUrl,
                    'bank_info' => [
                        'bank_name' => 'TPBank',
                        'account_number' => '20588668888',
                        'account_name' => 'NGUYEN KHAC LONG',
                        'content' => $userId
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ createDeposit error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        Log::info('📨 SePay webhook received', ['data' => $request->all()]);

        try {
            $data = $request->all();

            if (!isset($data['content']) || !isset($data['transferAmount'])) {
                Log::warning('⚠️ Invalid webhook data');
                return response()->json(['success' => false], 400);
            }

            // Extract Firebase UID from content (28 chars alphanumeric)
            // Content có thể là: "104880237588 cfT4zfDX4YRkuwd4T6X3seJhtbl1 CHUYEN TIEN..."
            // Hoặc đơn giản: "cfT4zfDX4YRkuwd4T6X3seJhtbl1"
            $content = $data['content'];
            $userId = $content; // Default
            
            // Tìm Firebase UID pattern (28 ký tự alphanumeric)
            if (preg_match('/([a-zA-Z0-9]{28})/', $content, $matches)) {
                $userId = $matches[1];
                Log::info('🔍 Extracted UID from content', [
                    'raw_content' => $content,
                    'extracted_uid' => $userId
                ]);
            }

            $amount = $data['transferAmount'];
            $referenceCode = $data['referenceCode'] ?? '';
            $sepayId = $data['id'] ?? '';

            Log::info('📋 Processing webhook', [
                'user_id' => $userId,
                'amount' => $amount,
                'sepay_id' => $sepayId
            ]);

            // Cộng coins trực tiếp vào users
            $balanceResult = $this->addCoins($userId, $amount, [
                'source' => 'sepay_webhook',
                'sepay_id' => $sepayId,
                'reference_code' => $referenceCode
            ]);

            if ($balanceResult['success']) {
                // Tạo activity cho việc nạp coins thành công
                $activityService = new \App\Services\ActivityService();
                $activityService->createActivity(
                    $userId,
                    'deposit',
                    "Nạp thành công " . number_format($amount) . " Sky Coins vào tài khoản qua SePay",
                    [
                        'amount' => $amount,
                        'balance' => $balanceResult['new_balance'],
                        'sepay_id' => $sepayId,
                        'reference_code' => $referenceCode,
                        'source' => 'sepay_webhook',
                        'transaction_type' => 'deposit'
                    ]
                );
                
                Log::info('💰 Coins added successfully', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'new_balance' => $balanceResult['new_balance']
                ]);

                return response()->json(['success' => true]);
            }

            Log::error('❌ Failed to add coins');
            return response()->json(['success' => false], 500);
            Log::error('❌ Failed to add coins');
            return response()->json(['success' => false], 500);

        } catch (\Exception $e) {
            Log::error('❌ handleWebhook error: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    protected function addCoins($userId, $amount, $metadata = [])
    {
        try {
            // Đọc toàn bộ user document
            $userDoc = $this->firestore->getDocument('users', $userId);
            
            if (!$userDoc['success']) {
                Log::error('❌ User not found', ['user_id' => $userId]);
                return ['success' => false, 'error' => 'User not found'];
            }

            // Lấy tất cả data hiện tại
            $userData = $userDoc['data'];
            $currentCoins = isset($userData['coins']) ? $userData['coins'] : 0;
            $newCoins = $currentCoins + $amount;

            // Update coins nhưng GIỮ NGUYÊN tất cả field khác
            $userData['coins'] = $newCoins;

            // Update lại toàn bộ document (giữ nguyên avatar, email, name, role)
            $result = $this->firestore->updateDocument('users', $userId, $userData);

            if ($result['success']) {
                Log::info('✅ Updated coins in users collection', [
                    'user_id' => $userId,
                    'old_coins' => $currentCoins,
                    'added' => $amount,
                    'new_coins' => $newCoins
                ]);
                
                return [
                    'success' => true,
                    'new_balance' => $newCoins,
                    'added_amount' => $amount
                ];
            }

            return ['success' => false, 'error' => 'Failed to update coins'];

        } catch (\Exception $e) {
            Log::error('❌ addCoins error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected function logActivity($userId, $action, $metadata = [])
    {
        try {
            $activityId = 'ACT_' . time() . '_' . bin2hex(random_bytes(4));
            
            $this->firestore->createDocument('activities', $activityId, [
                'user_id' => $userId,
                'action' => $action,
                'metadata' => $metadata,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::warning(' Failed to log activity: ' . $e->getMessage());
        }
    }

    /**
     * Check payment status by comparing user coins
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $userId = session('firebase_uid');
            
            Log::info('🔍 checkPaymentStatus called', [
                'user_id' => $userId,
                'session_data' => session()->all()
            ]);
            
            if (!$userId) {
                Log::warning('⚠️ No user ID in session');
                return response()->json([
                    'success' => false, 
                    'message' => 'Chưa đăng nhập. Vui lòng đăng nhập lại.',
                    'session_exists' => session()->has('firebase_uid')
                ], 401);
            }

            // Lấy thông tin user để check coins
            $userDoc = $this->firestore->getDocument('users', $userId);
            
            if (!$userDoc['success']) {
                Log::error('❌ User not found in Firestore', ['user_id' => $userId]);
                return response()->json(['success' => false, 'message' => 'Không tìm thấy user'], 404);
            }

            $coins = $userDoc['data']['coins'] ?? 0;
            
            Log::info('✅ Got user coins', ['user_id' => $userId, 'coins' => $coins]);
            
            return response()->json([
                'success' => true, 
                'status' => 'completed',
                'coins' => $coins,
                'user_id' => $userId
            ]);

        } catch (\Exception $e) {
            Log::error('❌ checkPaymentStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }





public function confirmCartPayment(Request $request)
{
    try {
        $userId = session('firebase_uid');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập'], 401);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|string',
            'items.*.seller_id' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.name' => 'required|string',
            'total_amount' => 'required|numeric|min:0'
        ]);

        $items = $request->items;
        $totalAmount = floatval($request->total_amount);

        Log::info('💳 Payment request:', [
            'user_id' => $userId,
            'items_count' => count($items),
            'total_amount' => $totalAmount
        ]);

        // 1️⃣ Kiểm tra số dư COINS (không phải balance)
        $userDoc = $this->firestore->getDocument('users', $userId);
        if (!$userDoc['success']) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin người dùng'], 404);
        }

        $userData = $userDoc['data'];
        $currentCoins = floatval($userData['coins'] ?? 0); // ✅ ĐỔI THÀNH 'coins'

        Log::info('💰 Checking balance:', [
            'current_coins' => $currentCoins,
            'required_amount' => $totalAmount
        ]);

        if ($currentCoins < $totalAmount) {
            return response()->json([
                'success' => false, 
                'message' => 'Số dư không đủ! Hiện tại: ' . number_format($currentCoins) . 'đ, Cần: ' . number_format($totalAmount) . 'đ',
                'current_balance' => $currentCoins,
                'required_amount' => $totalAmount
            ], 400);
        }

        // 2️⃣ Trừ COINS người mua (GIỮ NGUYÊN các field khác)
        $newCoins = $currentCoins - $totalAmount;
        $userData['coins'] = $newCoins; // ✅ Update field 'coins'
        
        $updateBalanceResult = $this->firestore->updateDocument('users', $userId, $userData);

        if (!$updateBalanceResult['success']) {
            return response()->json(['success' => false, 'message' => 'Không thể trừ tiền từ tài khoản'], 500);
        }

        Log::info('✅ Coins deducted:', [
            'old_coins' => $currentCoins,
            'new_coins' => $newCoins
        ]);

        // 3️⃣ Tạo transaction_id duy nhất
        $transactionId = 'txn_' . time() . '_' . substr($userId, 0, 10);

        // 4️⃣ Lưu từng sản phẩm vào purchases theo cấu trúc subcollection
        $sheetIds = [];
        $sellerPayments = [];

        foreach ($items as $item) {
            $productDoc = $this->firestore->getDocument('products', $item['product_id']);
            
            if (!$productDoc['success']) {
                Log::warning("⚠️ Product not found: {$item['product_id']}");
                continue;
            }

            $product = $productDoc['data'];

            // ✅ Tạo purchase data theo cấu trúc subcollection
            $purchaseData = [
                'author' => $product['author'] ?? '',
                'buyer_id' => $userId,
                'file_path' => $product['file_path'] ?? '',
                'image_path' => is_array($product['image_path'] ?? null) ? '' : ($product['image_path'] ?? ''),
                'price' => floatval($item['price']),
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'purchased_at' => now()->toIso8601String(),
                'seller_id' => $item['seller_id'],
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'category' => $product['category'] ?? '',
                'description' => $product['description'] ?? ''
            ];

            // Lưu purchase vào subcollection sheets (theo cấu trúc purchases/{uid}/sheets)
            $result = $this->userPurchaseService->savePurchase($userId, $purchaseData);

            if ($result['success']) {
                $sheetIds[] = $result['sheet_id'];
                Log::info('✅ Sheet created in purchases:', ['product' => $item['name'], 'sheet_id' => $result['sheet_id']]);

                // Tính tiền cho seller
                $sellerId = $item['seller_id'];
                if (!isset($sellerPayments[$sellerId])) {
                    $sellerPayments[$sellerId] = 0;
                }
                $sellerPayments[$sellerId] += floatval($item['price']) * intval($item['quantity'] ?? 1);
            } else {
                Log::error("❌ Failed to create sheet in purchases for: {$item['product_id']}", ['error' => $result['error']]);
            }
        }

        // 5️⃣ Cộng COINS cho các seller (GIỮ NGUYÊN các field khác)
        foreach ($sellerPayments as $sellerId => $amount) {
            $sellerDoc = $this->firestore->getDocument('users', $sellerId);
            if ($sellerDoc['success']) {
                $sellerData = $sellerDoc['data'];
                $sellerCoins = floatval($sellerData['coins'] ?? 0);
                $newSellerCoins = $sellerCoins + $amount;
                
                $sellerData['coins'] = $newSellerCoins; // ✅ Update field 'coins'
                $this->firestore->updateDocument('users', $sellerId, $sellerData);

                Log::info('💰 Seller payment:', [
                    'seller_id' => $sellerId,
                    'amount' => $amount,
                    'new_coins' => $newSellerCoins
                ]);
            }
        }

        // 6️⃣ Xóa giỏ hàng
        $this->firestore->deleteDocument('carts', $userId);
        Log::info('🗑️ Cart cleared for user: ' . $userId);

        // 7️⃣ Lưu transaction log
        $this->firestore->createDocument('transactions', null, [
            'user_id' => $userId,
            'type' => 'purchase',
            'amount' => -$totalAmount,
            'description' => 'Mua ' . count($items) . ' sheet nhạc',
            'transaction_id' => $transactionId,
            'created_at' => now()->toIso8601String(),
            'status' => 'completed'
        ]);

        // 8️⃣ Tạo activity cho việc mua hàng thành công
        $activityService = new \App\Services\ActivityService();
        $activityService->createActivity(
            $userId,
            'purchase',
            "Bạn đã mua thành công " . count($items) . " sheet nhạc với tổng giá trị " . number_format($totalAmount) . " coins",
            [
                'total_amount' => $totalAmount,
                'items_count' => count($items),
                'transaction_id' => $transactionId,
                'sheet_ids_string' => implode(', ', $sheetIds) // Convert array to string
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công!',
            'transaction_id' => $transactionId,
            'new_balance' => $newCoins,
            'sheets' => $sheetIds
        ]);

    } catch (\Throwable $e) {
        Log::error('❌ Error confirmCartPayment: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false, 
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ], 500);
    }
}


public function getUserBalance()
{
    try {
        $userId = session('firebase_uid');
        
        Log::info('🔍 getUserBalance called', ['user_id' => $userId]);
        
        if (!$userId) {
            return response()->json([
                'success' => false, 
                'message' => 'Chưa đăng nhập'
            ], 401);
        }

        $userDoc = $this->firestore->getDocument('users', $userId);
        
        if (!$userDoc['success']) {
            Log::error('❌ User not found in getUserBalance', ['user_id' => $userId]);
            return response()->json([
                'success' => false, 
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $coins = floatval($userDoc['data']['coins'] ?? 0);
        
        Log::info('✅ getUserBalance success', [
            'user_id' => $userId,
            'coins' => $coins
        ]);

        return response()->json([
            'success' => true,
            'balance' => $coins,
            'coins' => $coins
        ]);
    } catch (\Throwable $e) {
        Log::error('❌ Error getUserBalance: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Lỗi server: ' . $e->getMessage()
        ], 500);
    }
}


}
