<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FirestoreRestService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $firestore;

    public function __construct(FirestoreRestService $firestore)
    {
        $this->firestore = $firestore;
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
                Log::info('💰 Coins added successfully', [
                    'user_id' => $userId,
                    'amount' => $amount,
                    'new_balance' => $balanceResult['new_balance']
                ]);

                $this->logActivity($userId, 'deposit_completed', [
                    'amount' => $amount,
                    'balance' => $balanceResult['new_balance'],
                    'sepay_id' => $sepayId
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

        $cartItems = $request->input('cart_items', []);
        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng trống!'], 400);
        }

        // 1️⃣ Lấy thông tin người mua
        $buyerDoc = $this->firestore->getDocument('users', $userId);
        if (!$buyerDoc['success']) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy người mua'], 404);
        }
        $buyer = $buyerDoc['data'];
        $buyerCoins = $buyer['coins'] ?? 0;

        // 2️⃣ Tính tổng tiền
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }

        // 3️⃣ Kiểm tra số dư
        if ($buyerCoins < $totalAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Số dư không đủ. Bạn cần thêm ' . number_format($totalAmount - $buyerCoins) . ' xu để thanh toán.'
            ]);
        }

        // 4️⃣ Trừ xu người mua
        $buyer['coins'] = $buyerCoins - $totalAmount;
        $this->firestore->updateDocument('users', $userId, $buyer);

        // 5️⃣ Xử lý từng sản phẩm
        foreach ($cartItems as $item) {
            $sellerId = $item['seller_id'] ?? null;
            $price = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);

            // Cộng xu cho người bán
            if ($sellerId) {
                $sellerDoc = $this->firestore->getDocument('users', $sellerId);
                if ($sellerDoc['success']) {
                    $seller = $sellerDoc['data'];
                    $seller['coins'] = ($seller['coins'] ?? 0) + $price;
                    $this->firestore->updateDocument('users', $sellerId, $seller);

                    $this->logActivity($sellerId, 'receive_payment', [
                        'from_user' => $userId,
                        'product' => $item['name'],
                        'amount' => $price
                    ]);
                }
            }

            // Lưu vào listsheets
            $userDoc = $this->firestore->getDocument('users', $userId);
            $userData = $userDoc['data'] ?? [];
            $listsheets = $userData['listsheets'] ?? [];

            $listsheets[$item['product_id']] = [
                'title' => $item['name'] ?? '',
                'category' => $item['category'] ?? '',
                'price' => $item['price'] ?? 0,
                'file_url' => $item['file_url'] ?? '',
                'seller_name' => $item['seller_name'] ?? '',
                'seller_uid' => $item['seller_id'] ?? '',
                'description' => $item['description'] ?? '',
                'rating' => $item['rating'] ?? 0,
                'status' => 'active',
                'purchased_at' => now()->toIso8601String()
            ];

            $userData['listsheets'] = $listsheets;
            $this->firestore->updateDocument('users', $userId, $userData);
        }

        // 6️⃣ Log người mua
        $this->logActivity($userId, 'purchase_completed', [
            'total_amount' => $totalAmount,
            'items' => $cartItems
        ]);

        // ✅ 7️⃣ XÓA HOÀN TOÀN GIỎ HÀNG khỏi Firestore
        $deleteResult = $this->firestore->deleteDocument('carts', $userId);
        
        if ($deleteResult['success']) {
            Log::info("🗑️ Cart deleted after successful payment for user: {$userId}");
        } else {
            Log::warning("⚠️ Could not delete cart after payment", ['user_id' => $userId]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công!',
            'data' => [
                'remaining_coins' => $buyer['coins'],
                'total_spent' => $totalAmount
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('❌ confirmCartPayment error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


}
