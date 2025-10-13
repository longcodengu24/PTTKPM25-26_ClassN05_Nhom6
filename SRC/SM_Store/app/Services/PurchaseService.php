<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class PurchaseService
{
    protected $firestoreService;

    public function __construct(FirestoreSimple $firestoreService = null)
    {
        $this->firestoreService = $firestoreService ?: new FirestoreSimple();
    }

    /**
     * Kiểm tra xem user đã mua sản phẩm này chưa
     */
    public function hasPurchasedProduct(string $userId, string $productId): bool
    {
        try {
            // Query purchases collection để tìm purchase record
            $purchases = $this->firestoreService->queryDocuments('purchases', [
                'where' => [
                    ['buyer_id', '==', $userId],
                    ['product_id', '==', $productId]
                ]
            ]);

            return count($purchases) > 0;
        } catch (\Exception $e) {
            Log::error('Error checking purchase history', [
                'user_id' => $userId,
                'product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách sản phẩm đã mua của user
     */
    public function getUserPurchasedProducts(string $userId): array
    {
        try {
            $purchases = $this->firestoreService->queryDocuments('purchases', [
                'where' => [
                    ['buyer_id', '==', $userId]
                ]
            ]);

            return array_map(function ($purchase) {
                return $purchase['data']['product_id'] ?? null;
            }, $purchases);
        } catch (\Exception $e) {
            Log::error('Error getting user purchased products', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Kiểm tra danh sách sản phẩm trong giỏ hàng xem có sản phẩm nào đã mua chưa
     */
    public function validateCartItems(string $userId, array $cartItems): array
    {
        $errors = [];
        $purchasedProductIds = $this->getUserPurchasedProducts($userId);

        foreach ($cartItems as $index => $item) {
            $productId = $item['product_id'] ?? null;
            $productName = $item['name'] ?? 'Sản phẩm không xác định';

            if ($productId && in_array($productId, $purchasedProductIds)) {
                $errors[] = [
                    'index' => $index,
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'message' => "Bạn đã mua sản phẩm '{$productName}' rồi"
                ];
            }
        }

        return $errors;
    }

    /**
     * Lọc bỏ những sản phẩm đã mua khỏi giỏ hàng
     */
    public function filterPurchasedItems(string $userId, array $cartItems): array
    {
        $purchasedProductIds = $this->getUserPurchasedProducts($userId);

        return array_filter($cartItems, function ($item) use ($purchasedProductIds) {
            $productId = $item['product_id'] ?? null;
            return $productId && !in_array($productId, $purchasedProductIds);
        });
    }
}
