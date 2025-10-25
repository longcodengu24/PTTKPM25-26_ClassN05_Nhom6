<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreSimple;
use App\Services\PurchaseService;
use App\Services\UserPurchaseService;
use App\Services\SheetActivityService;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private $firestoreService;
    private $productModel;
    private $purchaseService;
    private $userPurchaseService;
    private $sheetActivityService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
        $this->productModel = new Product();
        $this->purchaseService = new PurchaseService($this->firestoreService);
        $this->userPurchaseService = new UserPurchaseService($this->firestoreService);
        $this->sheetActivityService = new SheetActivityService($this->firestoreService);
    }

    /**
<<<<<<< HEAD
     * Hiển thị trang checkout
     */
    public function showCheckout(Request $request)
    {
        try {
            $buyerId = session('firebase_uid');
            if (!$buyerId) {
                return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thanh toán');
            }

            // Lấy thông tin user
            $userResult = $this->firestoreService->getDocument('users', $buyerId);
            $userData = $userResult['success'] ? $userResult['data'] : [];

            // Cart items sẽ được load từ sessionStorage bằng JavaScript
            return view('shop.checkout', [
                'userData' => $userData,
                'userCoins' => $userData['coins'] ?? 0
            ]);

        } catch (\Exception $e) {
            Log::error('Error showing checkout: ' . $e->getMessage());
            return redirect()->route('account.cart')->with('error', 'Có lỗi xảy ra');
        }
    }

    /**
=======
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
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
            $buyerResult = $this->firestoreService->getDocument('users', $buyerId);
            if (!$buyerResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin người dùng'
                ], 404);
            }

            $buyer = $buyerResult['data'];
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
                    $sellerResult = $this->firestoreService->getDocument('users', $sellerId);
                    $sellerData = $sellerResult['success'] ? $sellerResult['data'] : [];
                    $sellersUpdated[$sellerId] = [
                        'current_coins' => $sellerData['coins'] ?? 0,
                        'earned_coins' => 0
                    ];
                }

                // Cộng dồn xu cho seller này
                $sellersUpdated[$sellerId]['earned_coins'] += $itemPrice;

                // Lưu thông tin purchase cho buyer theo cấu trúc subcollection
                $purchaseData = [
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'seller_id' => $sellerId,
                    'buyer_id' => $buyerId,
                    'price' => $itemPrice,
                    'transaction_id' => $transactionId,
                    'purchased_at' => now()->toISOString(),
                    'file_path' => $product['file_path'] ?? '',
                    'image_path' => is_array($product['image_path'] ?? null) ? '' : ($product['image_path'] ?? ''),
                    'author' => $product['author'] ?? '',
                    'status' => 'completed',
                    'category' => $product['category'] ?? '',
                    'description' => $product['description'] ?? ''
                ];

                // Lưu vào subcollection sheets (theo cấu trúc purchases/{uid}/sheets)
                $result = $this->userPurchaseService->savePurchase($buyerId, $purchaseData);
                
                if ($result['success']) {
                    $purchasedItems[] = array_merge($purchaseData, ['id' => $result['sheet_id']]);
                    Log::info('✅ Sheet saved to purchases from checkout:', [
                        'product' => $item['name'], 
                        'sheet_id' => $result['sheet_id']
                    ]);
                } else {
                    Log::error("❌ Failed to save sheet to purchases from checkout: {$productId}", [
                        'error' => $result['error']
                    ]);
                }

                // Increment sold count cho product
                $this->productModel->incrementSoldCount($productId);

                // Tạo activity cho seller
                $activityService = new \App\Services\ActivityService();
                $activityService->createActivity(
                    $sellerId,
                    'sale',
                    'Bạn đã bán "' . $item['name'] . '" cho ' . ($buyer['displayName'] ?? 'khách hàng'),
                    [
                        'amount' => $itemPrice,
                        'transaction_id' => $transactionId,
                        'product_id' => $productId,
                        'buyer_id' => $buyerId,
                        'transaction_type' => 'sale'
                    ]
                );

                // Tạo activity cho buyer
                $activityService->createActivity(
                    $buyerId,
                    'purchase',
                    'Bạn đã mua "' . $item['name'] . '"',
                    [
                        'total_amount' => $itemPrice,
                        'transaction_id' => $transactionId,
                        'product_id' => $productId,
                        'seller_id' => $sellerId,
                        'transaction_type' => 'purchase'
                    ]
                );
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

}
