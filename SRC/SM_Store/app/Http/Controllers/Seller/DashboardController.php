<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $firestoreService;

    public function __construct()
    {
        $this->firestoreService = new FirestoreSimple();
    }

    /**
     * Hiển thị dashboard với thống kê
     */
    public function index()
    {
        try {
            $sellerId = session('firebase_uid');

            if (!$sellerId) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }

            // Lấy tất cả purchases của seller
            $purchasesResponse = $this->firestoreService->listDocuments('purchases');

            $totalRevenue = 0;
            $totalOrders = 0;
            $completedOrders = 0;
            $productSales = []; // Để tính sản phẩm bán chạy
            $dailyRevenue = []; // Doanh thu theo ngày

            if (isset($purchasesResponse['documents'])) {
                foreach ($purchasesResponse['documents'] as $doc) {
                    $fields = $doc['fields'] ?? [];
                    $data = [];

                    foreach ($fields as $key => $value) {
                        if (isset($value['stringValue'])) {
                            $data[$key] = $value['stringValue'];
                        } elseif (isset($value['integerValue'])) {
                            $data[$key] = (int)$value['integerValue'];
                        } elseif (isset($value['doubleValue'])) {
                            $data[$key] = (float)$value['doubleValue'];
                        }
                    }

                    // Chỉ tính đơn hàng của seller hiện tại
                    if (isset($data['seller_id']) && $data['seller_id'] === $sellerId) {
                        $totalOrders++;
                        $price = $data['price'] ?? 0;
                        $status = $data['status'] ?? 'completed';
                        $productName = $data['product_name'] ?? 'Unknown';
                        $purchasedAt = $data['purchased_at'] ?? now()->toISOString();

                        if ($status === 'completed') {
                            $totalRevenue += $price;
                            $completedOrders++;

                            // Tính doanh thu theo ngày trong tuần (7 ngày gần nhất)
                            try {
                                $date = new \DateTime($purchasedAt);
                                $dayOfWeek = $date->format('N'); // 1 (Monday) to 7 (Sunday)
                                $dateKey = $date->format('Y-m-d');

                                if (!isset($dailyRevenue[$dateKey])) {
                                    $dailyRevenue[$dateKey] = [
                                        'date' => $dateKey,
                                        'day' => $dayOfWeek,
                                        'revenue' => 0,
                                        'orders' => 0
                                    ];
                                }
                                $dailyRevenue[$dateKey]['revenue'] += $price;
                                $dailyRevenue[$dateKey]['orders']++;
                            } catch (\Exception $e) {
                                // Skip invalid dates
                            }
                        }

                        // Đếm số lượng bán của từng sản phẩm
                        if (!isset($productSales[$productName])) {
                            $productSales[$productName] = [
                                'name' => $productName,
                                'count' => 0,
                                'revenue' => 0
                            ];
                        }
                        $productSales[$productName]['count']++;
                        $productSales[$productName]['revenue'] += $price;
                    }
                }
            }

            // Chuẩn bị dữ liệu biểu đồ cho 7 ngày gần nhất
            $chartData = $this->prepareChartData($dailyRevenue);

            Log::info('Chart data prepared', [
                'daily_revenue_count' => count($dailyRevenue),
                'chart_data' => $chartData
            ]);

            // Sắp xếp sản phẩm theo số lượng bán
            usort($productSales, function ($a, $b) {
                return $b['count'] - $a['count'];
            });

            // Lấy top 5 sản phẩm bán chạy
            $topProducts = array_slice($productSales, 0, 5);

            // Lấy số lượng sản phẩm của seller
            $productsResponse = $this->firestoreService->listDocuments('products');
            $totalProducts = 0;

            if (isset($productsResponse['documents'])) {
                foreach ($productsResponse['documents'] as $doc) {
                    $fields = $doc['fields'] ?? [];
                    $sellerId_field = $fields['seller_id']['stringValue'] ?? null;

                    if ($sellerId_field === $sellerId) {
                        $totalProducts++;
                    }
                }
            }

            $stats = [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'pending_orders' => $totalOrders - $completedOrders,
                'total_products' => $totalProducts,
                'top_products' => $topProducts,
                'conversion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                'chart_data' => $chartData
            ];

            Log::info('Dashboard loaded', [
                'seller_id' => $sellerId,
                'stats' => $stats
            ]);

            return view('saler.dashboard', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error loading dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('saler.dashboard', [
                'stats' => [
                    'total_revenue' => 0,
                    'total_orders' => 0,
                    'completed_orders' => 0,
                    'pending_orders' => 0,
                    'total_products' => 0,
                    'top_products' => [],
                    'conversion_rate' => 0,
                    'chart_data' => [
                        'labels' => [],
                        'values' => [],
                        'max' => 0
                    ]
                ],
                'error' => 'Có lỗi xảy ra khi tải dữ liệu dashboard'
            ]);
        }
    }

    /**
     * Chuẩn bị dữ liệu biểu đồ cho 7 ngày gần nhất
     */
    private function prepareChartData($dailyRevenue)
    {
        $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        $labels = [];
        $values = [];

        // Tạo mảng cho 7 ngày gần nhất
        $dateRevenue = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayIndex = (int)$date->format('N') % 7; // 0 = CN, 1 = T2, ..., 6 = T7

            // Kiểm tra xem ngày này có dữ liệu không
            $revenue = 0;
            if (isset($dailyRevenue[$dateKey])) {
                $revenue = $dailyRevenue[$dateKey]['revenue'];
            }

            $labels[] = $dayNames[$dayIndex];
            $values[] = $revenue;
        }

        $maxRevenue = !empty($values) ? max($values) : 1000000; // Default 1 triệu nếu không có dữ liệu

        return [
            'labels' => $labels,
            'values' => $values,
            'max' => $maxRevenue
        ];
    }
}
