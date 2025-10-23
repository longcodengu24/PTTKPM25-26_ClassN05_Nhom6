<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TransactionService
{
    protected $firestore;
    protected $database;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials', resource_path('key/firebase.json'));
        $databaseUrl = config('firebase.database_url', 'https://kchip-8865d-default-rtdb.asia-southeast1.firebasedatabase.app');

        $factory = (new Factory)->withServiceAccount($credentialsPath);
        
        // Firestore cho lưu transactions
        $this->firestore = $factory->createFirestore();
        
        // Realtime Database cho balance (đã có trong UserBalanceService)
        $this->database = $factory->withDatabaseUri($databaseUrl)->createDatabase();
    }

    /**
     * Tạo transaction mới trong Firestore
     */
    public function createTransaction(array $data): array
    {
        try {
            $transactionData = [
                'transaction_id' => $data['transaction_id'],
                'user_id' => $data['user_id'],
                'type' => $data['type'] ?? 'deposit',
                'amount' => (int)$data['amount'],
                'currency' => $data['currency'] ?? 'VND',
                'status' => $data['status'] ?? 'pending',
                'payment_method' => $data['payment_method'] ?? 'sepay',
                'description' => $data['description'] ?? '',
                'reference_code' => $data['reference_code'] ?? null,
                'sepay_data' => $data['sepay_data'] ?? [],
                'processed' => false,
                'created_at' => Carbon::now()->toISOString(),
                'updated_at' => Carbon::now()->toISOString(),
                'completed_at' => null,
                'processed_at' => null
            ];

            // Lưu vào Firestore collection 'transactions'
            $docRef = $this->firestore->collection('transactions')->add($transactionData);
            
            // Cập nhật document với ID
            $docRef->update([
                ['path' => 'firestore_id', 'value' => $docRef->id()]
            ]);

            Log::info("💾 Transaction saved to Firestore", [
                'firestore_id' => $docRef->id(),
                'transaction_id' => $data['transaction_id'],
                'user_id' => $data['user_id'],
                'amount' => $data['amount']
            ]);

            return [
                'success' => true,
                'firestore_id' => $docRef->id(),
                'transaction_data' => $transactionData
            ];

        } catch (\Exception $e) {
            Log::error("❌ Error creating transaction in Firestore: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Tìm transaction theo transaction_id
     */
    public function getTransactionById(string $transactionId): ?array
    {
        try {
            $query = $this->firestore->collection('transactions')
                ->where('transaction_id', '=', $transactionId)
                ->limit(1);

            $documents = $query->documents();

            foreach ($documents as $document) {
                return [
                    'firestore_id' => $document->id(),
                    'data' => $document->data()
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error finding transaction: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm transaction pending của user với amount
     */
    public function findPendingTransaction(string $userId, int $amount): ?array
    {
        try {
            $query = $this->firestore->collection('transactions')
                ->where('user_id', '=', $userId)
                ->where('amount', '=', $amount)
                ->where('status', '=', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(1);

            $documents = $query->documents();

            foreach ($documents as $document) {
                return [
                    'firestore_id' => $document->id(),
                    'data' => $document->data()
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error("Error finding pending transaction: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Cập nhật trạng thái transaction thành completed
     */
    public function markTransactionCompleted(string $firestoreId): bool
    {
        try {
            $docRef = $this->firestore->collection('transactions')->document($firestoreId);
            
            $updateData = [
                ['path' => 'status', 'value' => 'completed'],
                ['path' => 'processed', 'value' => true],
                ['path' => 'completed_at', 'value' => Carbon::now()->toISOString()],
                ['path' => 'processed_at', 'value' => Carbon::now()->toISOString()],
                ['path' => 'updated_at', 'value' => Carbon::now()->toISOString()]
            ];

            $docRef->update($updateData);

            Log::info("✅ Transaction marked as completed in Firestore", [
                'firestore_id' => $firestoreId
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Error marking transaction completed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật transaction với webhook data
     */
    public function updateTransactionWithWebhook(string $firestoreId, array $webhookData): bool
    {
        try {
            $docRef = $this->firestore->collection('transactions')->document($firestoreId);
            
            $updateData = [
                ['path' => 'webhook_data', 'value' => $webhookData],
                ['path' => 'updated_at', 'value' => Carbon::now()->toISOString()]
            ];

            $docRef->update($updateData);

            return true;

        } catch (\Exception $e) {
            Log::error("Error updating transaction with webhook data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy lịch sử transactions của user
     */
    public function getUserTransactions(string $userId, int $limit = 50): array
    {
        try {
            $query = $this->firestore->collection('transactions')
                ->where('user_id', '=', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            $documents = $query->documents();
            $transactions = [];

            foreach ($documents as $document) {
                $transactions[] = [
                    'firestore_id' => $document->id(),
                    'data' => $document->data()
                ];
            }

            return $transactions;

        } catch (\Exception $e) {
            Log::error("Error getting user transactions: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Kiểm tra transaction đã được xử lý chưa
     */
    public function isTransactionCompleted(array $transaction): bool
    {
        return isset($transaction['data']['status']) && 
               $transaction['data']['status'] === 'completed' &&
               isset($transaction['data']['processed']) &&
               $transaction['data']['processed'] === true;
    }

    /**
     * Lưu activity log cho user (tùy chọn)
     */
    public function logActivity(string $userId, string $action, array $data = []): bool
    {
        try {
            $activityData = [
                'user_id' => $userId,
                'action' => $action,
                'data' => $data,
                'timestamp' => Carbon::now()->toISOString(),
                'ip' => request()->ip() ?? null
            ];

            $this->firestore->collection('user_activities')->add($activityData);

            return true;

        } catch (\Exception $e) {
            Log::error("Error logging activity: " . $e->getMessage());
            return false;
        }
    }
}