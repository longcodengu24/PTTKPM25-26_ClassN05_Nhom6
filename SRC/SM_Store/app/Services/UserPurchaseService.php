<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class UserPurchaseService
{
    protected $firestoreService;

    public function __construct(FirestoreSimple $firestoreService = null)
    {
        $this->firestoreService = $firestoreService ?: new FirestoreSimple();
    }

    /**
     * Lưu purchase mới theo cấu trúc subcollection
     * Cấu trúc: purchases/{uid}/sheets/{sheet_id}
     */
    public function savePurchase(string $userId, array $purchaseData): array
    {
        try {
            // 1. Kiểm tra xem user purchase document đã tồn tại chưa
            $userPurchaseDoc = $this->firestoreService->getDocument('purchases', $userId);
            
            if ($userPurchaseDoc === null) {
                // Tạo user purchase document mới
                $userPurchaseData = [
                    'user_id' => $userId,
                    'total_purchases' => 1,
                    'last_updated' => now()->toIso8601String(),
                    'created_at' => now()->toIso8601String()
                ];
                
                $createResult = $this->firestoreService->createDocumentWithId('purchases', $userId, $userPurchaseData);
                if (!$createResult) {
                    throw new \Exception('Failed to create user purchase document');
                }
                
                Log::info('✅ Created new user purchase document', ['user_id' => $userId]);
            } else {
                // Cập nhật total_purchases và last_updated
                $currentData = $userPurchaseDoc;
                $currentData['total_purchases'] = ($currentData['total_purchases'] ?? 0) + 1;
                $currentData['last_updated'] = now()->toIso8601String();
                
                $updateResult = $this->firestoreService->updateDocument('purchases', $userId, $currentData);
                if (!$updateResult) {
                    throw new \Exception('Failed to update user purchase document');
                }
                
                Log::info('✅ Updated user purchase document', ['user_id' => $userId, 'total_purchases' => $currentData['total_purchases']]);
            }

            // 2. Tạo sheet document trong subcollection
            $sheetId = 'sheet_' . time() . '_' . substr(md5($purchaseData['product_id']), 0, 8);
            
            $sheetData = [
                'category' => $purchaseData['category'] ?? '',
                'description' => $purchaseData['description'] ?? '',
                'file_url' => $purchaseData['file_path'] ?? '',
                'price' => floatval($purchaseData['price']),
                'purchased_at' => $purchaseData['purchased_at'] ?? now()->toIso8601String(),
                'rating' => 0,
                'seller_name' => $purchaseData['author'] ?? '',
                'seller_uid' => $purchaseData['seller_id'] ?? '',
                'status' => $purchaseData['status'] ?? 'active',
                'title' => $purchaseData['product_name'] ?? '',
                'product_id' => $purchaseData['product_id'] ?? '',
                'transaction_id' => $purchaseData['transaction_id'] ?? '',
                'image_path' => $purchaseData['image_path'] ?? '',
                'buyer_id' => $purchaseData['buyer_id'] ?? $userId
            ];

            // Lưu vào subcollection sheets
            $sheetResult = $this->firestoreService->createDocumentWithId("purchases/{$userId}/sheets", $sheetId, $sheetData);
            
            if (!$sheetResult) {
                throw new \Exception('Failed to create sheet document');
            }

            Log::info('✅ Created sheet document', [
                'user_id' => $userId,
                'sheet_id' => $sheetId,
                'product_name' => $purchaseData['product_name'] ?? 'N/A'
            ]);

            return [
                'success' => true,
                'sheet_id' => $sheetId,
                'user_id' => $userId,
                'data' => $sheetData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error saving purchase', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'purchase_data' => $purchaseData
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách sheets đã mua của user
     */
    public function getUserSheets(string $userId): array
    {
        try {
            $sheets = $this->firestoreService->listDocuments("purchases/{$userId}/sheets");
            
            if (!isset($sheets['documents'])) {
                return [];
            }
            
            return array_map(function ($sheet) {
                $data = [];
                if (isset($sheet['fields'])) {
                    foreach ($sheet['fields'] as $key => $field) {
                        if (isset($field['stringValue'])) {
                            $data[$key] = $field['stringValue'];
                        } elseif (isset($field['doubleValue'])) {
                            $data[$key] = $field['doubleValue'];
                        } elseif (isset($field['integerValue'])) {
                            $data[$key] = $field['integerValue'];
                        } elseif (isset($field['booleanValue'])) {
                            $data[$key] = $field['booleanValue'];
                        }
                    }
                }
                return [
                    'id' => basename($sheet['name'] ?? ''),
                    'data' => $data
                ];
            }, $sheets['documents']);
            
        } catch (\Exception $e) {
            Log::error('❌ Error getting user sheets', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Kiểm tra xem user đã mua sản phẩm này chưa
     */
    public function hasPurchasedProduct(string $userId, string $productId): bool
    {
        try {
            $sheets = $this->getUserSheets($userId);
            
            foreach ($sheets as $sheet) {
                if (($sheet['data']['product_id'] ?? '') === $productId) {
                    return true;
                }
            }

            return false;
            
        } catch (\Exception $e) {
            Log::error('❌ Error checking purchase history', [
                'user_id' => $userId,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Lấy thông tin user purchase document
     */
    public function getUserPurchaseInfo(string $userId): ?array
    {
        try {
            $result = $this->firestoreService->getDocument('purchases', $userId);
            
            if ($result !== null) {
                return $result;
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('❌ Error getting user purchase info', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Xóa một sheet khỏi user purchases
     */
    public function deleteSheet(string $userId, string $sheetId): bool
    {
        try {
            $result = $this->firestoreService->deleteDocument("purchases/{$userId}/sheets", $sheetId);
            
            if ($result) {
                // Cập nhật total_purchases
                $userPurchaseInfo = $this->getUserPurchaseInfo($userId);
                if ($userPurchaseInfo) {
                    $userPurchaseInfo['total_purchases'] = max(0, ($userPurchaseInfo['total_purchases'] ?? 1) - 1);
                    $userPurchaseInfo['last_updated'] = now()->toIso8601String();
                    
                    $this->firestoreService->updateDocument('purchases', $userId, $userPurchaseInfo);
                }
                
                Log::info('✅ Deleted sheet', ['user_id' => $userId, 'sheet_id' => $sheetId]);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('❌ Error deleting sheet', [
                'user_id' => $userId,
                'sheet_id' => $sheetId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
