<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PurchaseService;
use App\Services\FirestoreRestService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    private $purchaseService;
    private $firestore;

    public function __construct(FirestoreRestService $firestore)
    {
        $this->purchaseService = new PurchaseService();
        $this->firestore = $firestore;
    }

    public function index()
    {
        return view('account.cart');
    }

    /**
     * 🔹 Lấy giỏ hàng người dùng
     */
    public function getCart(Request $request)
    {
        try {
            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập'], 401);
            }

            $cart = $this->firestore->getDocument('carts', $userId);
            if (!$cart['success']) {
                return response()->json([
                    'success' => true,
                    'cart' => [
                        'items' => [],
                        'total_items' => 0,
                        'total_amount' => 0
                    ]
                ]);
            }

            return response()->json(['success' => true, 'cart' => $cart['data']]);
        } catch (\Throwable $e) {
            Log::error('❌ Error getCart: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi lấy giỏ hàng: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 Thêm sản phẩm vào giỏ hàng
     */
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|string',
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
                'image' => 'nullable|string',
                'quantity' => 'nullable|integer|min:1',
                'composer' => 'nullable',
                'category' => 'nullable'
            ]);

            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập'], 401);
            }

            $cartResult = $this->firestore->getDocument('carts', $userId);
            $cartData = [
                'items' => [],
                'total_items' => 0,
                'total_amount' => 0,
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String()
            ];

            $isNew = !$cartResult['success'];
            if (!$isNew) {
                $cartData = $cartResult['data'];
            }

            $items = $cartData['items'] ?? [];
            if (!is_array($items)) $items = [];

            $exists = false;
            foreach ($items as &$it) {
                if ($it['product_id'] === $request->product_id) {
                    $it['quantity'] = ($it['quantity'] ?? 1) + ($request->quantity ?? 1);
                    $exists = true;
                    break;
                }
            }
            unset($it);

            if (!$exists) {
                $items[] = [
                    'product_id' => $request->product_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'image' => $request->image ?? '',
                    'quantity' => $request->quantity ?? 1,
                    'composer' => $request->composer ?? '',
                    'category' => $request->category ?? ''
                ];
            }

            $totalItems = 0;
            $totalAmount = 0;
            foreach ($items as $i) {
                $qty = intval($i['quantity'] ?? 1);
                $price = intval($i['price'] ?? 0);
                $totalItems += $qty;
                $totalAmount += $qty * $price;
            }

            $cartData['items'] = $items;
            $cartData['total_items'] = $totalItems;
            $cartData['total_amount'] = $totalAmount;
            $cartData['updated_at'] = now()->toIso8601String();

            $res = $isNew
                ? $this->firestore->createDocument('carts', $userId, $cartData)
                : $this->firestore->updateDocument('carts', $userId, $cartData);

            return $res['success']
                ? response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'cart' => $cartData])
                : response()->json(['success' => false, 'message' => 'Không thể thêm vào giỏ hàng', 'error' => $res['error'] ?? null], 500);
        } catch (\Throwable $e) {
            Log::error('❌ Error addToCart: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 🔹 Xóa sản phẩm khỏi giỏ hàng
     */
public function removeFromCart(Request $request)
{
    try {
        $request->validate(['product_id' => 'required|string']);
        $userId = session('firebase_uid');
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập'], 401);
        }

        $cart = $this->firestore->getDocument('carts', $userId);
        if (!$cart['success']) {
            return response()->json(['success' => false, 'message' => 'Giỏ hàng không tồn tại'], 404);
        }

        $cartData = $cart['data'] ?? [];
        $items = $cartData['items'] ?? [];
        if (!is_array($items)) $items = [];

        $items = array_values(array_filter($items, fn($it) => $it['product_id'] !== $request->product_id));

        // Nếu giỏ hàng trống => xóa document luôn khỏi Firestore
        if (empty($items)) {
            $deleteUrl = "https://firestore.googleapis.com/v1/projects/" . config('firebase.project_id') . "/databases/(default)/documents/carts/{$userId}";
            $deleteResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->firestore->accessToken,
            ])->delete($deleteUrl);

            Log::info("🗑️ Cart deleted for user: {$userId}", ['status' => $deleteResponse->status()]);
            
            // ✅ Trả về cấu trúc giống getCart() khi cart trống
            return response()->json([
                'success' => true, 
                'message' => 'Đã xóa sản phẩm cuối cùng khỏi giỏ hàng',
                'cart' => [
                    'items' => [],
                    'total_items' => 0,
                    'total_amount' => 0
                ]
            ]);
        }

        // Nếu vẫn còn sản phẩm
        $totalItems = 0;
        $totalAmount = 0;
        foreach ($items as $i) {
            $qty = intval($i['quantity'] ?? 1);
            $price = intval($i['price'] ?? 0);
            $totalItems += $qty;
            $totalAmount += $qty * $price;
        }

        $cartData['items'] = $items;
        $cartData['total_items'] = $totalItems;
        $cartData['total_amount'] = $totalAmount;
        $cartData['updated_at'] = now()->toIso8601String();

        $res = $this->firestore->updateDocument('carts', $userId, $cartData);

        return $res['success']
            ? response()->json(['success' => true, 'message' => 'Đã xóa khỏi giỏ hàng', 'cart' => $cartData])
            : response()->json(['success' => false, 'message' => 'Không thể xóa khỏi giỏ hàng', 'error' => $res['error'] ?? null], 500);
    } catch (\Throwable $e) {
        Log::error('❌ Error removeFromCart: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
    }
}
    /**
     * 🔹 Xóa toàn bộ giỏ hàng
     */
    public function clearCart()
    {
        try {
            $userId = session('firebase_uid');
            if (!$userId) {
                return response()->json(['success' => false, 'message' => 'Bạn cần đăng nhập'], 401);
            }

            // Xóa luôn document carts/{userId}
            $deleteUrl = "https://firestore.googleapis.com/v1/projects/" . config('firebase.project_id') . "/databases/(default)/documents/carts/{$userId}";
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->firestore->accessToken,
            ])->delete($deleteUrl);

            Log::info("🗑️ Clear cart deleted for user: {$userId}");
            return response()->json(['success' => true, 'message' => 'Đã xóa toàn bộ giỏ hàng']);
        } catch (\Throwable $e) {
            Log::error('❌ Error clearCart: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()], 500);
        }
    }
}
