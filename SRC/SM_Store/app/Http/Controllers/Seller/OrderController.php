<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\FirestoreSimple;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị danh sách đơn hàng của seller
     * GET /seller/orders
     */
    public function index(Request $request)
    {
        try {
            $sellerId = session('firebase_uid');

            if (!$sellerId) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }

            // Lấy search query từ request
            $searchQuery = $request->get('search', '');

            // Lấy tất cả purchases từ collection purchases
            $purchasesResponse = $this->firestoreService->listDocuments('purchases');
            $orders = collect();
            $totalRevenue = 0;

            if (isset($purchasesResponse['documents'])) {
                foreach ($purchasesResponse['documents'] as $doc) {
                    // Parse document fields
                    $fields = $doc['fields'] ?? [];
                    $data = [];
                    foreach ($fields as $key => $value) {
                        if (isset($value['stringValue'])) {
                            $data[$key] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $data[$key] = (int)$value['integerValue'];
                        } elseif (isset($value['doubleValue'])) {
                            $data[$key] = (float)$value['doubleValue'];
                        } elseif (isset($value['booleanValue'])) {
                            $data[$key] = $value['booleanValue'];
                        }
                    }

                    // Chỉ lấy đơn hàng của seller hiện tại
                    if (isset($data['seller_id']) && $data['seller_id'] === $sellerId) {
                        $orderStatus = $data['status'] ?? 'completed';

                        // Parse document ID
                        $docId = '';
                        if (isset($doc['name'])) {
                            $parts = explode('/', $doc['name']);
                            $docId = end($parts);
                        }

                        $orderData = [
                            'id' => $docId,
                            'product_id' => $data['product_id'] ?? '',
                            'product_name' => $data['product_name'] ?? 'N/A',
                            'buyer_id' => $data['buyer_id'] ?? '',
                            'price' => $data['price'] ?? 0,
                            'status' => $orderStatus,
                            'transaction_id' => $data['transaction_id'] ?? '',
                            'purchased_at' => $data['purchased_at'] ?? now()->toISOString(),
                            'image_path' => $data['image_path'] ?? '',
                        ];

                        // Lấy thông tin buyer
                        if (!empty($orderData['buyer_id'])) {
                            $buyer = $this->firestoreService->getDocument('users', $orderData['buyer_id']);
                            $orderData['buyer_name'] = $buyer['name'] ?? 'Unknown';
                            $orderData['buyer_email'] = $buyer['email'] ?? 'N/A';
                        } else {
                            $orderData['buyer_name'] = 'Unknown';
                            $orderData['buyer_email'] = 'N/A';
                        }

                        $orders->push($orderData);

                        // Tính tổng doanh thu (chỉ đơn completed)
                        if ($orderStatus === 'completed') {
                            $totalRevenue += $orderData['price'];
                        }
                    }
                }
            }

            // Áp dụng tìm kiếm nếu có
            if (!empty($searchQuery)) {
                $orders = $orders->filter(function ($order) use ($searchQuery) {
                    $searchLower = mb_strtolower($searchQuery, 'UTF-8');

                    // Tìm kiếm theo mã đơn
                    if (str_contains(mb_strtolower($order['id'], 'UTF-8'), $searchLower)) {
                        return true;
                    }

                    // Tìm kiếm theo tên khách hàng
                    if (str_contains(mb_strtolower($order['buyer_name'], 'UTF-8'), $searchLower)) {
                        return true;
                    }

                    // Tìm kiếm theo email khách hàng
                    if (str_contains(mb_strtolower($order['buyer_email'], 'UTF-8'), $searchLower)) {
                        return true;
                    }

                    // Tìm kiếm theo tên sản phẩm
                    if (str_contains(mb_strtolower($order['product_name'], 'UTF-8'), $searchLower)) {
                        return true;
                    }

                    return false;
                });
            }

            // Sắp xếp theo thời gian mới nhất
            $orders = $orders->sortByDesc('purchased_at')->values();

            // Thống kê
            $stats = [
                'total_orders' => $orders->count(),
                'total_revenue' => $totalRevenue,
                'completed_orders' => $orders->where('status', 'completed')->count(),
                'pending_orders' => $orders->where('status', 'pending')->count(),
                'cancelled_orders' => $orders->where('status', 'cancelled')->count(),
            ];

            Log::info('Seller orders loaded', [
                'seller_id' => $sellerId,
                'total_orders' => $stats['total_orders'],
                'search_query' => $searchQuery
            ]);

            return view('saler.orders.index', [
                'orders' => $orders,
                'stats' => $stats,
                'searchQuery' => $searchQuery
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading seller orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('saler.orders.index', [
                'orders' => collect(),
                'stats' => [
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'completed_orders' => 0,
                    'pending_orders' => 0,
                    'cancelled_orders' => 0,
                ],
                'searchQuery' => '',
                'error' => 'Có lỗi xảy ra khi tải danh sách đơn hàng'
            ]);
        }
    }

    /**
     * Hiển thị chi tiết một đơn hàng
     * GET /seller/orders/{id}
     */
    public function show($id)
    {
        try {
            $sellerId = session('firebase_uid');

            if (!$sellerId) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }

            // Lấy đơn hàng từ collection purchases
            $order = $this->firestoreService->getDocument('purchases', $id);

            if (!$order) {
                return redirect()->route('seller.orders.index')
                    ->with('error', 'Không tìm thấy đơn hàng');
            }

            // Kiểm tra quyền sở hữu
            if (($order['seller_id'] ?? '') !== $sellerId) {
                return redirect()->route('seller.orders.index')
                    ->with('error', 'Bạn không có quyền xem đơn hàng này');
            }

            // Lấy thông tin buyer
            $buyer = null;
            if (!empty($order['buyer_id'])) {
                $buyer = $this->firestoreService->getDocument('users', $order['buyer_id']);
            }

            // Lấy thông tin product
            $product = null;
            if (!empty($order['product_id'])) {
                $product = $this->firestoreService->getDocument('products', $order['product_id']);
            }

            return view('saler.orders.detail', [
                'order' => $order,
                'buyer' => $buyer,
                'product' => $product,
                'orderId' => $id
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading order details', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('seller.orders.index')
                ->with('error', 'Có lỗi xảy ra khi tải chi tiết đơn hàng');
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng (nếu cần)
     * PUT /seller/orders/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,completed,cancelled,refunded'
            ]);

            $sellerId = session('firebase_uid');

            if (!$sellerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng đăng nhập'
                ], 401);
            }

            // Lấy đơn hàng từ collection purchases
            $order = $this->firestoreService->getDocument('purchases', $id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Kiểm tra quyền
            if (($order['seller_id'] ?? '') !== $sellerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật đơn hàng này'
                ], 403);
            }

            // Cập nhật status
            $updateData = [
                'status' => $request->status,
                'updated_at' => now()->toISOString()
            ];

            $this->firestoreService->updateDocument('purchases', $id, $updateData);

            Log::info('Order status updated', [
                'order_id' => $id,
                'seller_id' => $sellerId,
                'new_status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái đơn hàng',
                'status' => $request->status
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ], 500);
        }
    }
}
