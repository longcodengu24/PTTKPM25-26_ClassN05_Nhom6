<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\UserBalanceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $balanceService;

    public function __construct(UserBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Trang nạp tiền (form nhập số tiền)
     */
    public function showDepositForm()
    {
        return view('payments.deposit');
    }

    /**
     * Tạo yêu cầu nạp tiền mới
     */
    public function createDeposit(Request $request)
    {
        try {
            $validated = $request->validate([
                'amount' => 'required|integer|min:10000|max:50000000',
                'user_id' => 'nullable|string'
            ]);

            $userId = $validated['user_id'] ?? session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập để nạp tiền'
                ], 401);
            }

            $amount = $validated['amount'];
            
            // Tạo transaction ID theo format: DEP_timestamp_random
            $transactionId = 'DEP_' . time() . '_' . bin2hex(random_bytes(6));

            // Tạo transaction trong database
            $transaction = Transaction::create([
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'type' => 'deposit',
                'amount' => $amount,
                'currency' => 'VND',
                'status' => 'pending',
                'payment_method' => 'sepay',
                'description' => $userId, // Nội dung chuyển khoản = UID
                'reference_code' => $transactionId,
                'sepay_data' => json_encode([
                    'created_via' => 'web_form',
                    'ip' => $request->ip(),
                    'bank_code' => '970423',
                    'account_number' => '20588668888'
                ])
            ]);

            Log::info("💳 Created deposit transaction", [
                'transaction_id' => $transactionId,
                'user_id' => $userId,
                'amount' => $amount
            ]);

            // Nội dung chuyển khoản = UID của user
            $transferContent = $userId;

            // Trả về thông tin thanh toán
            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transactionId,
                    'amount' => $amount,
                    'qr_code' => $this->generateQRData('970423', '20588668888', $amount, $transferContent),
                    'bank_info' => [
                        'bank_code' => '970423',
                        'bank_name' => 'TPBank',
                        'account_number' => '20588668888',
                        'account_name' => 'NGUYEN KHAC LONG',
                        'content' => $transferContent
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error("❌ Create deposit error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate QR code data for VietQR
     */
    private function generateQRData($bankCode, $accountNumber, $amount, $content)
    {
        // VietQR format: https://img.vietqr.io/image/{bank}-{account}-{template}.jpg?amount={amount}&addInfo={content}
        return "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-compact.jpg?amount={$amount}&addInfo=" . urlencode($content);
    }

    /**
     * Hàm xử lý khi giao dịch hoàn tất
     */
    private function processSuccessfulDeposit(Transaction $tx)
    {
        if ($tx->isCompleted()) {
            Log::info("⚠️ Transaction {$tx->id} already completed");
            return;
        }

        $tx->markAsCompleted();

        $userId = $tx->user_id;
        $amount = $tx->amount;

        $result = $this->balanceService->addCoins($userId, (int)$amount);

        if ($result['success']) {
            Log::info("✅ Deposit success for user {$userId}: +{$amount} coins", [
                'old_balance' => $result['old_balance'],
                'new_balance' => $result['new_balance']
            ]);
        } else {
            Log::error("❌ Failed to add coins for transaction {$tx->id}", [
                'error' => $result['error'] ?? 'Unknown error'
            ]);
        }
    }

    /**
     * Kiểm tra trạng thái giao dịch (cho popup)
     */
    public function checkTransactionStatus($transactionId)
    {
        try {
            $transaction = Transaction::where('transaction_id', $transactionId)->first();
            
            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'created_at' => $transaction->created_at->format('d/m/Y H:i:s'),
                    'completed' => $transaction->status === 'completed'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("Check transaction status error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kiểm tra trạng thái'
            ], 500);
        }
    }

    /**
     * API webhook (thật) từ SePay
     * Nhận thông tin chuyển khoản từ ngân hàng
     */
    public function handleWebhook(Request $request)
    {
        Log::info("📥 Webhook SePay received", $request->all());

        try {
            // Lấy thông tin từ webhook SePay
            $transferContent = $request->input('content'); // Nội dung CK = UID
            $amount = (int)$request->input('transferAmount'); // SePay dùng field "transferAmount"
            $transferId = $request->input('referenceCode'); // Mã tham chiếu từ SePay
            $gateway = $request->input('gateway'); // TPBank
            
            if (!$transferContent || !$amount) {
                Log::warning("⚠️ Webhook thiếu thông tin", $request->all());
                return response()->json(['success' => false, 'message' => 'Missing data']);
            }

            // Extract Firebase UID từ nội dung CK (28 ký tự alphanumeric)
            // Pattern: cfT4zfDX4YRkuwd4T6X3seJhtbl1 (Firebase UID format)
            preg_match('/[a-zA-Z0-9]{28}/', $transferContent, $matches);
            
            if (empty($matches)) {
                Log::warning("⚠️ Không tìm thấy UID trong nội dung CK", [
                    'content' => $transferContent
                ]);
                return response()->json(['success' => false, 'message' => 'UID not found in content']);
            }
            
            $userId = $matches[0]; // UID đầu tiên tìm được
            
            Log::info("🔍 Extracted UID from content", [
                'original_content' => $transferContent,
                'extracted_uid' => $userId
            ]);
            
            // Tìm transaction pending của user này với số tiền tương ứng
            $transaction = Transaction::where('user_id', $userId)
                ->where('amount', $amount)
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$transaction) {
                Log::warning("⚠️ Không tìm thấy transaction pending", [
                    'user_id' => $userId,
                    'amount' => $amount
                ]);
                
                // Tạo transaction mới nếu không tìm thấy
                $transaction = Transaction::create([
                    'transaction_id' => 'DEP_' . time() . '_' . bin2hex(random_bytes(4)),
                    'user_id' => $userId,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'currency' => 'VND',
                    'status' => 'pending',
                    'payment_method' => 'sepay',
                    'description' => $userId,
                    'reference_code' => $transferId,
                    'sepay_data' => json_encode([
                        'created_via' => 'webhook',
                        'transfer_id' => $transferId,
                        'webhook_data' => $request->all()
                    ])
                ]);
            }

            // Xử lý thanh toán thành công
            $this->processSuccessfulDeposit($transaction);

            Log::info("✅ Webhook processed successfully", [
                'transaction_id' => $transaction->transaction_id,
                'user_id' => $userId,
                'amount' => $amount
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error("❌ Webhook processing error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
