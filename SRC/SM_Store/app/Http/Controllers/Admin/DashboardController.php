<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FirestoreSimple;
use App\Services\AdminRevenueService;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $firestore;
    protected $revenueService;

    public function __construct()
    {
        $this->firestore = new FirestoreSimple();
        $this->revenueService = new AdminRevenueService();
    }

    public function index()
    {
        try {
            // Lấy tất cả purchases từ collection purchases
            $purchasesResponse = $this->firestore->listDocuments('purchases', 1000);
            $allPurchases = $purchasesResponse['documents'] ?? [];

            // Lấy tất cả users
            $usersResponse = $this->firestore->listDocuments('users', 1000);

            // Lấy tất cả products
            $productsResponse = $this->firestore->listDocuments('products', 1000);

            // Khởi tạo biến thống kê (Admin chỉ quản lý, không có doanh thu)
            $totalOrders = 0;
            $completedOrders = 0;
            $pendingOrders = 0;
            $totalProducts = 0;
            $totalUsers = 0;
            $totalSellers = 0;
            $totalCustomers = 0;
            $todayOrders = 0;
            $weekOrders = 0;
            $monthOrders = 0;

            $productSales = [];
            $dailyOrders = [];
            $recentOrders = [];

            // Xử lý purchases
            foreach ($allPurchases as $doc) {
                    $fields = $doc['fields'] ?? [];
                    $data = $this->parseDocument($fields);

                    $totalOrders++;
                    $status = $data['status'] ?? 'completed';
                    $productName = $data['product_name'] ?? 'Unknown';
                    $purchasedAt = $data['purchased_at'] ?? now()->toISOString();

                    if ($status === 'completed') {
                        $completedOrders++;

                        // Đếm đơn hàng theo thời gian
                        $purchaseDate = date('Y-m-d', strtotime($purchasedAt));
                        $today = date('Y-m-d');
                        $weekAgo = date('Y-m-d', strtotime('-7 days'));
                        $monthAgo = date('Y-m-d', strtotime('-30 days'));

                        if ($purchaseDate === $today) {
                            $todayOrders++;
                        }
                        if ($purchaseDate >= $weekAgo) {
                            $weekOrders++;
                        }
                        if ($purchaseDate >= $monthAgo) {
                            $monthOrders++;
                        }

                        // Sản phẩm bán chạy (đếm số lượng bán)
                        if (!isset($productSales[$productName])) {
                            $productSales[$productName] = [
                                'name' => $productName,
                                'count' => 0
                            ];
                        }
                        $productSales[$productName]['count']++;

                        // Đơn hàng theo ngày (7 ngày gần nhất)
                        if (!isset($dailyOrders[$purchaseDate])) {
                            $dailyOrders[$purchaseDate] = 0;
                        }
                        $dailyOrders[$purchaseDate]++;
                    } else {
                        $pendingOrders++;
                    }

                    // Đơn hàng gần nhất (top 10)
                    if (count($recentOrders) < 10) {
                        $recentOrders[] = [
                            'id' => basename($doc['name']),
                            'product_name' => $productName,
                            'customer_email' => $customerEmail ?? 'N/A',
                            'status' => $status,
                            'purchased_at' => $purchasedAt,
                            'date' => date('d/m/Y H:i', strtotime($purchasedAt))
                        ];
                    }
            }

            // Xử lý users
            if (isset($usersResponse['documents'])) {
                foreach ($usersResponse['documents'] as $doc) {
                    $fields = $doc['fields'] ?? [];
                    $role = $fields['role']['stringValue'] ?? 'user';

                    $totalUsers++;
                    if ($role === 'saler') {
                        $totalSellers++;
                    } else {
                        $totalCustomers++;
                    }
                }
            }

            // Đếm products
            if (isset($productsResponse['documents'])) {
                $totalProducts = count($productsResponse['documents']);
            }

            // Sắp xếp top products
            usort($productSales, function ($a, $b) {
                return $b['count'] - $a['count'];
            });
            $topProducts = array_slice($productSales, 0, 5);

            // Chuẩn bị dữ liệu biểu đồ (7 ngày gần nhất - số đơn hàng)
            $chartData = $this->prepareChartData($dailyOrders);

            // Tính toán doanh thu admin từ hoa hồng (30% từ mỗi giao dịch)
            $revenueData = $this->revenueService->calculateAdminRevenue();
            $adminRevenue = $revenueData['total_revenue'];
            $totalSales = $revenueData['total_sales'];
            $todayRevenue = $revenueData['today_revenue'];
            $weekRevenue = $revenueData['week_revenue'];
            $monthRevenue = $revenueData['month_revenue'];
            $topSellers = $revenueData['revenue_by_seller'];
            $recentTransactions = $revenueData['recent_transactions'];
            $revenueChartData = $revenueData['chart_data'];

            $stats = [
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'pending_orders' => $pendingOrders,
                'total_products' => $totalProducts,
                'total_users' => $totalUsers,
                'total_sellers' => $totalSellers,
                'total_customers' => $totalCustomers,
                'today_orders' => $todayOrders,
                'week_orders' => $weekOrders,
                'month_orders' => $monthOrders,
                'top_products' => $topProducts,
                'recent_orders' => $recentOrders,
                'chart_data' => $chartData,
                'conversion_rate' => $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 2) : 0,
                // Admin Revenue (Commission based - 30%)
                'admin_revenue' => $adminRevenue,
                'total_sales' => $totalSales,
                'today_revenue' => $todayRevenue,
                'week_revenue' => $weekRevenue,
                'month_revenue' => $monthRevenue,
                'top_sellers' => $topSellers,
                'recent_transactions' => $recentTransactions,
                'revenue_chart_data' => $revenueChartData,
                'commission_rate' => 30
            ];

            Log::info('Admin dashboard loaded', ['stats' => $stats]);

            return view('admin.dashboard.dashboard', compact('stats'));
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('admin.dashboard.dashboard', [
                'stats' => $this->getEmptyStats(),
                'error' => 'Có lỗi xảy ra khi tải dữ liệu dashboard'
            ]);
        }
    }

    private function parseDocument($fields)
    {
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
        return $data;
    }

    private function prepareChartData($dailyOrders)
    {
        $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayIndex = (int)$date->format('N') % 7;

            $orderCount = $dailyOrders[$dateKey] ?? 0;

            $labels[] = $dayNames[$dayIndex];
            $values[] = $orderCount;
        }

        $maxOrders = !empty($values) ? max($values) : 10;

        return [
            'labels' => $labels,
            'values' => $values,
            'max' => $maxOrders
        ];
    }

    private function getEmptyStats()
    {
        return [
            'total_orders' => 0,
            'completed_orders' => 0,
            'pending_orders' => 0,
            'total_products' => 0,
            'total_users' => 0,
            'total_sellers' => 0,
            'total_customers' => 0,
            'today_orders' => 0,
            'week_orders' => 0,
            'month_orders' => 0,
            'top_products' => [],
            'recent_orders' => [],
            'chart_data' => [
                'labels' => [],
                'values' => [],
                'max' => 0
            ],
            'conversion_rate' => 0,
            // Admin Revenue (Commission - 30%)
            'admin_revenue' => 0,
            'total_sales' => 0,
            'today_revenue' => 0,
            'week_revenue' => 0,
            'month_revenue' => 0,
            'top_sellers' => [],
            'recent_transactions' => [],
            'revenue_chart_data' => [
                'labels' => [],
                'values' => [],
                'max' => 0
            ],
            'commission_rate' => 30
        ];
    }
}
