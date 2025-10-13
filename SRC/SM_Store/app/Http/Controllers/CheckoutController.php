<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use App\Services\PurchaseService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private $firestoreService;
    private $productModel;
    private $purchaseService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
        $this->productModel = new Product();
        $this->purchaseService = new PurchaseService($this->firestoreService);
    }

    /**
     * Xử lý checkout giỏ hàng
     */
    public function processCheckout(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'cart_items' => 'required|array|min:1',
                'cart_items.*.product_id' => 'required|string',
                'cart_items.*.seller_id' => 'required|string',
                'cart_items.*.price' => 'required|integer|min:0',
                'cart_items.*.name' => 'required|string'
            ]);

            $buyerId = session('firebase_uid');
            if (!$buyerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để mua hàng'
                ], 401);
            }

            $cartItems = $request->cart_items;

            // ✅ KIỂM TRA SAN PHẨM ĐÃ MUA
            $purchaseErrors = $this->purchaseService->validateCartItems($buyerId, $cartItems);
            if (!empty($purchaseErrors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng có sản phẩm đã mua',
                    'purchased_items' => $purchaseErrors
                ], 400);
            }

            // Tính tổng tiền
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['price'];
            }

            // Lấy thông tin người mua
            $buyer = $this->firestoreService->getDocument('users', $buyerId);
            if (!$buyer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }

            $buyerCoins = $buyer['coins'] ?? 0;

            // Kiểm tra đủ xu
            if ($buyerCoins < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không đủ xu để mua hàng. Bạn có ' . number_format($buyerCoins) . ' xu, cần ' . number_format($totalAmount) . ' xu'
                ], 400);
            }

            // Bắt đầu transaction
            $transactionId = 'txn_' . time() . '_' . substr($buyerId, 0, 8);

            // Xử lý từng item trong giỏ hàng
            $purchasedItems = [];
            $sellersUpdated = [];

            foreach ($cartItems as $item) {
                $productId = $item['product_id'];
                $sellerId = $item['seller_id'];
                $itemPrice = $item['price'];

                // Validate sản phẩm tồn tại
                $product = $this->getProductById($productId);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sản phẩm ' . $item['name'] . ' không tồn tại'
                    ], 404);
                }

                // Kiểm tra không mua sản phẩm của chính mình
                if ($sellerId === $buyerId) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể mua sản phẩm của chính mình'
                    ], 400);
                }

                // Cộng xu cho seller (fix: accumulate all products from same seller)
                if (!isset($sellersUpdated[$sellerId])) {
                    $seller = $this->firestoreService->getDocument('users', $sellerId);
                    $sellersUpdated[$sellerId] = [
                        'current_coins' => $seller['coins'] ?? 0,
                        'earned_coins' => 0
                    ];
                }

                // Cộng dồn xu cho seller này
                $sellersUpdated[$sellerId]['earned_coins'] += $itemPrice;

                // Lưu thông tin purchase cho buyer
                $purchaseData = [
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'seller_id' => $sellerId,
                    'buyer_id' => $buyerId,
                    'price' => $itemPrice,
                    'transaction_id' => $transactionId,
                    'purchased_at' => now()->toISOString(),
                    'file_path' => $product['file_path'] ?? '',
                    'image_path' => $product['image_path'] ?? '',
                    'author' => $product['author'] ?? '',
                    'status' => 'completed'
                ];

                // Lưu vào collection purchases
                $purchaseId = $this->firestoreService->createDocument('purchases', $purchaseData);
                $purchasedItems[] = array_merge($purchaseData, ['id' => $purchaseId]);

                // Increment sold count cho product
                $this->productModel->incrementSoldCount($productId);

                // Tạo activity cho seller
                $this->createActivity([
                    'user_id' => $sellerId,
                    'type' => 'sale',
                    'title' => 'Bán sheet thành công',
                    'description' => 'Bạn đã bán "' . $item['name'] . '" cho ' . ($buyer['displayName'] ?? 'khách hàng'),
                    'amount' => '+' . number_format($itemPrice) . ' xu',
                    'transaction_id' => $transactionId,
                    'product_id' => $productId,
                    'related_user' => $buyerId
                ]);

                // Tạo activity cho buyer
                $this->createActivity([
                    'user_id' => $buyerId,
                    'type' => 'purchase',
                    'title' => 'Mua sheet thành công',
                    'description' => 'Bạn đã mua "' . $item['name'] . '"',
                    'amount' => '-' . number_format($itemPrice) . ' xu',
                    'transaction_id' => $transactionId,
                    'product_id' => $productId,
                    'related_user' => $sellerId
                ]);
            }

            // Cập nhật xu cho tất cả sellers (fix: update all accumulated coins)
            foreach ($sellersUpdated as $sellerId => $sellerData) {
                $finalCoins = $sellerData['current_coins'] + $sellerData['earned_coins'];
                $this->firestoreService->updateCoinsOnly($sellerId, $finalCoins);

                Log::info('Seller coins updated', [
                    'seller_id' => $sellerId,
                    'coins_before' => $sellerData['current_coins'],
                    'coins_earned' => $sellerData['earned_coins'],
                    'coins_after' => $finalCoins
                ]);
            }

            // Trừ xu người mua
            $newBuyerCoins = $buyerCoins - $totalAmount;
            $this->firestoreService->updateCoinsOnly($buyerId, $newBuyerCoins);

            // Log transaction
            Log::info('Checkout completed', [
                'transaction_id' => $transactionId,
                'buyer_id' => $buyerId,
                'total_amount' => $totalAmount,
                'items_count' => count($cartItems),
                'buyer_coins_before' => $buyerCoins,
                'buyer_coins_after' => $newBuyerCoins
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mua hàng thành công!',
                'data' => [
                    'transaction_id' => $transactionId,
                    'total_amount' => $totalAmount,
                    'items_purchased' => count($cartItems),
                    'remaining_coins' => $newBuyerCoins,
                    'purchased_items' => $purchasedItems
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout error: ' . $e->getMessage(), [
                'buyer_id' => session('firebase_uid'),
                'cart_items' => $request->cart_items ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra trong quá trình mua hàng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin sản phẩm theo ID
     */
    protected function getProductById($productId)
    {
        try {
            $allProducts = $this->productModel->getAllActive();
            $productsArray = is_array($allProducts) ? $allProducts : $allProducts->toArray();

            foreach ($productsArray as $product) {
                if (($product['id'] ?? '') === $productId) {
                    return $product;
                }
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting product by ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo activity record
     */
    private function createActivity($data)
    {
        try {
            $activityData = array_merge($data, [
                'created_at' => now()->toISOString(),
                'read' => false
            ]);

            return $this->firestoreService->createDocument('activities', $activityData);
        } catch (\Exception $e) {
            Log::error('Error creating activity: ' . $e->getMessage());
            return null;
        }
    }
}
