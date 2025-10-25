<?php

namespace App\Services;

use App\Services\FirestoreSimple;
use Illuminate\Support\Facades\Log;

class AdminRevenueService
{
    protected $firestoreService;

    public function __construct(FirestoreSimple $firestoreService = null)
    {
        $this->firestoreService = $firestoreService ?: new FirestoreSimple();
    }

    /**
     * Tính toán doanh thu admin từ tất cả purchases
     * Doanh thu admin = tổng giá trị purchases * 0.3 (30%)
     */
    public function calculateAdminRevenue(): array
    {
        try {
            $totalRevenue = 0;
            $totalSales = 0;
            $completedPurchases = 0;
            $revenueByDay = [];
            $revenueByMonth = [];
            $revenueBySeller = [];
            $recentTransactions = [];

            // Lấy tất cả purchases từ collection purchases
            $purchasesResponse = $this->firestoreService->listDocuments('purchases', 1000);
            $allPurchases = $purchasesResponse['documents'] ?? [];

            Log::info('AdminRevenueService: Processing purchases', ['count' => count($allPurchases)]);

            foreach ($allPurchases as $doc) {
                $fields = $doc['fields'] ?? [];
                $data = $this->parseDocument($fields);

                // Chỉ tính doanh thu từ purchases đã hoàn thành
                if (($data['status'] ?? 'completed') === 'completed') {
                    $price = floatval($data['price'] ?? 0);
                    $purchasedAt = $data['purchased_at'] ?? now()->toISOString();
                    $sellerId = $data['seller_id'] ?? '';
                    $sellerName = $data['seller_name'] ?? 'Unknown Seller';
                    $productName = $data['title'] ?? $data['product_name'] ?? 'Unknown Product';

                    if ($price > 0) {
                        $completedPurchases++;
                        $totalSales += $price;
                        $adminCommission = $price * 0.3; // 30% commission
                        $totalRevenue += $adminCommission;

                        // Thống kê theo ngày
                        $date = date('Y-m-d', strtotime($purchasedAt));
                        if (!isset($revenueByDay[$date])) {
                            $revenueByDay[$date] = [
                                'date' => $date,
                                'revenue' => 0,
                                'sales' => 0,
                                'transactions' => 0
                            ];
                        }
                        $revenueByDay[$date]['revenue'] += $adminCommission;
                        $revenueByDay[$date]['sales'] += $price;
                        $revenueByDay[$date]['transactions']++;

                        // Thống kê theo tháng
                        $month = date('Y-m', strtotime($purchasedAt));
                        if (!isset($revenueByMonth[$month])) {
                            $revenueByMonth[$month] = [
                                'month' => $month,
                                'revenue' => 0,
                                'sales' => 0,
                                'transactions' => 0
                            ];
                        }
                        $revenueByMonth[$month]['revenue'] += $adminCommission;
                        $revenueByMonth[$month]['sales'] += $price;
                        $revenueByMonth[$month]['transactions']++;

                        // Thống kê theo seller
                        if ($sellerId) {
                            if (!isset($revenueBySeller[$sellerId])) {
                                $revenueBySeller[$sellerId] = [
                                    'seller_id' => $sellerId,
                                    'seller_name' => $sellerName,
                                    'revenue' => 0,
                                    'sales' => 0,
                                    'transactions' => 0
                                ];
                            }
                            $revenueBySeller[$sellerId]['revenue'] += $adminCommission;
                            $revenueBySeller[$sellerId]['sales'] += $price;
                            $revenueBySeller[$sellerId]['transactions']++;
                        }

                        // Giao dịch gần nhất (top 10)
                        if (count($recentTransactions) < 10) {
                            $recentTransactions[] = [
                                'id' => basename($doc['name']),
                                'product_name' => $productName,
                                'seller_name' => $sellerName,
                                'price' => $price,
                                'admin_commission' => $adminCommission,
                                'purchased_at' => $purchasedAt,
                                'date' => date('d/m/Y H:i', strtotime($purchasedAt))
                            ];
                        }
                    }
                }
            }

            // Sắp xếp dữ liệu
            usort($recentTransactions, function($a, $b) {
                return strtotime($b['purchased_at']) - strtotime($a['purchased_at']);
            });

            // Sắp xếp sellers theo doanh thu
            usort($revenueBySeller, function($a, $b) {
                return $b['revenue'] - $a['revenue'];
            });

            // Chuẩn bị dữ liệu biểu đồ doanh thu 7 ngày gần nhất
            $chartData = $this->prepareRevenueChartData($revenueByDay);

            $result = [
                'total_revenue' => round($totalRevenue, 2),
                'total_sales' => round($totalSales, 2),
                'completed_purchases' => $completedPurchases,
                'commission_rate' => 30, // 30%
                'revenue_by_day' => array_values($revenueByDay),
                'revenue_by_month' => array_values($revenueByMonth),
                'revenue_by_seller' => array_slice($revenueBySeller, 0, 10), // Top 10 sellers
                'recent_transactions' => $recentTransactions,
                'chart_data' => $chartData,
                'today_revenue' => $this->getTodayRevenue($revenueByDay),
                'week_revenue' => $this->getWeekRevenue($revenueByDay),
                'month_revenue' => $this->getMonthRevenue($revenueByMonth)
            ];

            Log::info('AdminRevenueService: Calculation completed', [
                'total_revenue' => $result['total_revenue'],
                'total_sales' => $result['total_sales'],
                'completed_purchases' => $result['completed_purchases']
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('AdminRevenueService: Error calculating revenue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'total_revenue' => 0,
                'total_sales' => 0,
                'completed_purchases' => 0,
                'commission_rate' => 30,
                'revenue_by_day' => [],
                'revenue_by_month' => [],
                'revenue_by_seller' => [],
                'recent_transactions' => [],
                'chart_data' => [
                    'labels' => [],
                    'values' => [],
                    'max' => 0
                ],
                'today_revenue' => 0,
                'week_revenue' => 0,
                'month_revenue' => 0
            ];
        }
    }

    /**
     * Parse document fields từ Firestore
     */
    private function parseDocument($fields): array
    {
        $data = [];
        foreach ($fields as $key => $value) {
            if (isset($value['stringValue'])) {
                $data[$key] = $value['stringValue'];
            } elseif (isset($value['integerValue'])) {
                $data[$key] = (int)$value['integerValue'];
            } elseif (isset($value['doubleValue'])) {
                $data[$key] = (float)$value['doubleValue'];
            } elseif (isset($value['booleanValue'])) {
                $data[$key] = (bool)$value['booleanValue'];
            }
        }
        return $data;
    }

    /**
     * Chuẩn bị dữ liệu biểu đồ doanh thu 7 ngày gần nhất
     */
    private function prepareRevenueChartData($revenueByDay): array
    {
        $dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $dayIndex = (int)$date->format('N') % 7;

            $revenue = $revenueByDay[$dateKey]['revenue'] ?? 0;

            $labels[] = $dayNames[$dayIndex];
            $values[] = round($revenue, 2);
        }

        $maxRevenue = !empty($values) ? max($values) : 1000;

        return [
            'labels' => $labels,
            'values' => $values,
            'max' => $maxRevenue
        ];
    }

    /**
     * Lấy doanh thu hôm nay
     */
    private function getTodayRevenue($revenueByDay): float
    {
        $today = date('Y-m-d');
        return round($revenueByDay[$today]['revenue'] ?? 0, 2);
    }

    /**
     * Lấy doanh thu tuần này
     */
    private function getWeekRevenue($revenueByDay): float
    {
        $weekAgo = date('Y-m-d', strtotime('-7 days'));
        $total = 0;

        foreach ($revenueByDay as $day => $data) {
            if ($day >= $weekAgo) {
                $total += $data['revenue'];
            }
        }

        return round($total, 2);
    }

    /**
     * Lấy doanh thu tháng này
     */
    private function getMonthRevenue($revenueByMonth): float
    {
        $currentMonth = date('Y-m');
        return round($revenueByMonth[$currentMonth]['revenue'] ?? 0, 2);
    }

    /**
     * Lấy thống kê doanh thu theo khoảng thời gian
     */
    public function getRevenueByPeriod(string $period = 'week'): array
    {
        $revenueData = $this->calculateAdminRevenue();
        
        switch ($period) {
            case 'day':
                return $revenueData['revenue_by_day'];
            case 'month':
                return $revenueData['revenue_by_month'];
            case 'seller':
                return $revenueData['revenue_by_seller'];
            default:
                return $revenueData['revenue_by_day'];
        }
    }
}
