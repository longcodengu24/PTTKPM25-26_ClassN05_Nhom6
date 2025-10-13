<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PurchaseService;
use App\Services\FirestoreSimple;

class CartController extends Controller
{
    private $purchaseService;

    public function __construct()
    {
        $this->purchaseService = new PurchaseService();
    }

    /**
     * Kiểm tra xem sản phẩm có thể thêm vào giỏ hàng không
     */
    public function canAddToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string'
            ]);

            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập'
                ], 401);
            }

            $productId = $request->product_id;
            $hasPurchased = $this->purchaseService->hasPurchasedProduct($userId, $productId);

            if ($hasPurchased) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã mua sản phẩm này rồi',
                    'can_add' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'can_add' => true,
                'message' => 'Có thể thêm vào giỏ hàng'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kiểm tra giỏ hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách sản phẩm đã mua của user
     */
    public function getPurchasedProducts(Request $request)
    {
        try {
            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập'
                ], 401);
            }

            $purchasedProductIds = $this->purchaseService->getUserPurchasedProducts($userId);

            return response()->json([
                'success' => true,
                'purchased_products' => $purchasedProductIds
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi lấy danh sách sản phẩm đã mua: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate toàn bộ giỏ hàng trước khi checkout
     */
    public function validateCart(Request $request)
    {
        try {
            $request->validate([
                'cart_items' => 'required|array',
                'cart_items.*.product_id' => 'required|string',
                'cart_items.*.name' => 'required|string'
            ]);

            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập'
                ], 401);
            }

            $cartItems = $request->cart_items;
            $purchaseErrors = $this->purchaseService->validateCartItems($userId, $cartItems);

            if (!empty($purchaseErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng có sản phẩm đã mua',
                    'errors' => $purchaseErrors,
                    'valid' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Giỏ hàng hợp lệ',
                'valid' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi validate giỏ hàng: ' . $e->getMessage()
            ], 500);
        }
    }
}
