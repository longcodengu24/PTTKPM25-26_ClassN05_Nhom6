<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class SheetActivityService
{
    protected $firestoreService;

    public function __construct(FirestoreSimple $firestoreService = null)
    {
        $this->firestoreService = $firestoreService ?: new FirestoreSimple();
    }

    /**
     * Lưu sheet nhạc vào collection activities
     */
    public function saveSheetToActivities(string $userId, array $productData, string $transactionId): array
    {
        try {
            // Tạo sheet data theo format bạn muốn
            $sheetData = [
                'category' => $productData['category'] ?? '',
                'created_at' => now()->toIso8601String(),
                'description' => $productData['description'] ?? '',
                'downloads' => 0,
                'file_url' => $productData['file_path'] ?? '',
                'price' => floatval($productData['price']),
                'rating' => 0,
                'seller_name' => $productData['author'] ?? '',
                'seller_uid' => $productData['seller_id'] ?? '',
                'status' => 'active',
                'title' => $productData['product_name'] ?? '',
                'updated_at' => now()->toIso8601String(),
                'buyer_uid' => $userId,
                'product_id' => $productData['product_id'] ?? '',
                'transaction_id' => $transactionId,
                'purchased_at' => now()->toIso8601String(),
                'image_path' => is_array($productData['image_path'] ?? '') ? '' : ($productData['image_path'] ?? '')
            ];

            // Tạo document ID duy nhất
            $documentId = 'sheet_' . time() . '_' . substr(md5($userId . $productData['product_id']), 0, 8);

            // Lưu vào collection activities
            $result = $this->firestoreService->createDocumentWithId('activities', $documentId, $sheetData);
            
            if (!$result) {
                throw new \Exception('Failed to create sheet document in activities');
            }

            Log::info('✅ Sheet saved to activities collection', [
                'user_id' => $userId,
                'document_id' => $documentId,
                'product_name' => $productData['product_name'] ?? 'N/A'
            ]);

            return [
                'success' => true,
                'document_id' => $documentId,
                'user_id' => $userId,
                'data' => $sheetData
            ];

        } catch (\Exception $e) {
            Log::error('❌ Error saving sheet to activities', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'product_data' => $productData
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Lấy danh sách sheets của user từ activities collection
     */
    public function getUserSheetsFromActivities(string $userId): array
    {
        try {
            // Query activities collection để tìm sheets của user
            $activities = $this->firestoreService->queryDocuments('activities', [
                'where' => [
                    ['buyer_uid', '==', $userId]
                ],
                'orderBy' => [
                    ['created_at', 'desc']
                ]
            ]);

            return array_map(function ($activity) {
                return [
                    'id' => $activity['id'],
                    'data' => $activity['data']
                ];
            }, $activities);
            
        } catch (\Exception $e) {
            Log::error('❌ Error getting user sheets from activities', [
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
            $activities = $this->firestoreService->queryDocuments('activities', [
                'where' => [
                    ['buyer_uid', '==', $userId],
                    ['product_id', '==', $productId]
                ]
            ]);

            return count($activities) > 0;
            
        } catch (\Exception $e) {
            Log::error('❌ Error checking purchase history in activities', [
                'user_id' => $userId,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}
