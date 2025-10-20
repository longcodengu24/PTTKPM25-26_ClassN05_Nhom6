@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="p-6">
    @if(isset($error))
        <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
            {{ $error }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">🎯 Admin Dashboard</h2>
        <div class="flex items-center space-x-3">
            <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
                <span>📊</span>
                <span>Xuất báo cáo</span>
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_orders'] ?? 0) }}</p>
                    <p class="text-gray-400 text-sm">Đơn</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🛒</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Người dùng</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_users'] ?? 0) }}</p>
                    <p class="text-gray-400 text-sm">Tài khoản</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">👥</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Sản phẩm</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_products'] ?? 0) }}</p>
                    <p class="text-gray-400 text-sm">Sheet nhạc</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🎼</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tỷ lệ hoàn thành</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ $stats['conversion_rate'] ?? 0 }}%</p>
                    <p class="text-gray-400 text-sm">{{ $stats['completed_orders'] ?? 0 }}/{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Revenue (Commission) -->
    <div class="mb-6">
        <div class="admin-card rounded-xl p-6 bg-gradient-to-br from-green-500 bg-opacity-10 border-green-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center">
                        <span class="text-3xl">💰</span>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 mb-2">
                            <p class="text-gray-300 text-sm">Tổng doanh thu (Hoa hồng)</p>
                            <div class="bg-green-500 bg-opacity-20 px-2 py-1 rounded">
                                <span class="text-green-400 text-xs font-bold">HOA HỒNG</span>
                            </div>
                        </div>
                        <p class="text-4xl font-bold text-white orbitron">{{ number_format($stats['admin_revenue'] ?? 0) }} <span class="text-xl text-gray-400">VND</span></p>
                        <p class="text-gray-400 text-xs mt-2">
                            <span class="text-yellow-400">⚠️</span> Logic tính toán: Lấy % hoa hồng từ mỗi giao dịch (chưa implement)
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                        <p class="text-gray-400 text-xs mb-2">Tỷ lệ hoa hồng dự kiến</p>
                        <p class="text-2xl font-bold text-green-400">5-10%</p>
                        <p class="text-gray-500 text-xs mt-1">từ mỗi giao dịch</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Orders Chart -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">📈 Đơn hàng 7 ngày gần nhất</h3>
            
            <!-- Vertical Bar Chart -->
            <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                <div class="h-56 flex items-end justify-around gap-2">
                    @if(!empty($stats['chart_data']['values']))
                        @foreach($stats['chart_data']['values'] as $index => $value)
                            @php
                                $percentage = $stats['chart_data']['max'] > 0 ? ($value / $stats['chart_data']['max']) * 100 : 0;
                                $height = max(5, $percentage); // Minimum 5%
                                
                                $colors = [
                                    '#a855f7', // purple
                                    '#3b82f6', // blue
                                    '#10b981', // green
                                    '#f59e0b', // amber
                                    '#ef4444', // red
                                    '#6366f1', // indigo
                                    '#14b8a6'  // teal
                                ];
                                $color = $colors[$index % 7];
                            @endphp
                            
                            <div class="flex-1 flex flex-col items-center group">
                                <!-- Bar Container -->
                                <div class="w-full h-48 flex items-end justify-center mb-2 relative">
                                    <!-- Bar -->
                                    <div class="w-full rounded-t-lg transition-all duration-500 hover:brightness-125 cursor-pointer chart-bar relative"
                                         style="height: {{ $height }}%; background: linear-gradient(to top, {{ $color }}, {{ $color }}dd); box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                        
                                        <!-- Tooltip on hover -->
                                        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10 pointer-events-none">
                                            <div class="font-bold text-center">{{ number_format($value) }} đơn</div>
                                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-full">
                                                <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Day Label -->
                                <div class="text-white text-sm font-bold">
                                    {{ $stats['chart_data']['labels'][$index] ?? '' }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="w-full text-center py-8">
                            <p class="text-gray-400">Chưa có dữ liệu đơn hàng</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Summary -->
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-blue-500 bg-opacity-20 rounded-lg p-3 border border-blue-500 border-opacity-30">
                    <div class="text-gray-300 text-xs mb-1">Tổng đơn hàng</div>
                    <div class="text-white font-bold text-sm">{{ number_format($stats['total_orders']) }} đơn</div>
                </div>
                @if($stats['chart_data']['max'] > 0)
                    <div class="bg-green-500 bg-opacity-20 rounded-lg p-3 border border-green-500 border-opacity-30">
                        <div class="text-gray-300 text-xs mb-1">Ngày cao nhất</div>
                        <div class="text-white font-bold text-sm">{{ number_format($stats['chart_data']['max']) }} đơn</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Products -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🏆 Sheet nhạc bán chạy</h3>
            @if(empty($stats['top_products']))
                <div class="text-center py-8">
                    <p class="text-gray-300">Chưa có sản phẩm nào được bán</p>
                </div>
            @else
                <div class="space-y-4">
                    @php
                        $badgeColors = ['bg-yellow-500', 'bg-gray-400', 'bg-orange-500', 'bg-blue-500', 'bg-purple-500'];
                    @endphp
                    @foreach($stats['top_products'] as $index => $product)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 {{ $badgeColors[$index] ?? 'bg-gray-500' }} rounded-lg flex items-center justify-center text-white font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $product['name'] }}</p>
                                <p class="text-gray-300 text-sm">Số lượng: {{ $product['count'] }}</p>
                            </div>
                        </div>
                        <span class="text-white font-medium">{{ $product['count'] }} bán</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">📋 Trạng thái đơn hàng</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tổng đơn hàng</span>
                    <span class="text-white font-medium">{{ number_format($stats['total_orders'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Đã hoàn thành</span>
                    <span class="text-green-400 font-medium">{{ number_format($stats['completed_orders'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Đang xử lý</span>
                    <span class="text-yellow-400 font-medium">{{ number_format($stats['pending_orders'] ?? 0) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300">Hôm nay</span>
                        <span class="text-blue-400 font-medium">{{ number_format($stats['today_orders'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-300">Tuần này</span>
                        <span class="text-blue-400 font-medium">{{ number_format($stats['week_orders'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-gray-300">Tháng này</span>
                        <span class="text-blue-400 font-medium">{{ number_format($stats['month_orders'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">👥 Thống kê người dùng</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tổng người dùng</span>
                    <span class="text-white font-medium">{{ number_format($stats['total_users'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Người bán</span>
                    <span class="text-purple-400 font-medium">{{ number_format($stats['total_sellers'] ?? 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Khách hàng</span>
                    <span class="text-blue-400 font-medium">{{ number_format($stats['total_customers'] ?? 0) }}</span>
                </div>
                <div class="pt-3 border-t border-gray-700">
                    <div class="bg-purple-500 bg-opacity-20 rounded-lg p-3 border border-purple-500 border-opacity-30">
                        <div class="text-gray-300 text-xs mb-1">Tỷ lệ Seller/Khách</div>
                        <div class="text-white font-bold">
                            @php
                                $sellerRatio = $stats['total_users'] > 0 ? round(($stats['total_sellers'] / $stats['total_users']) * 100, 1) : 0;
                            @endphp
                            {{ $sellerRatio }}% / {{ 100 - $sellerRatio }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">⚙️ Tổng quan hệ thống</h3>
            <div class="space-y-3">
                <!-- Products Stats -->
                <div class="bg-green-500 bg-opacity-10 rounded-lg p-3 border border-green-500 border-opacity-30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">🎼</span>
                            <div>
                                <p class="text-gray-300 text-xs">Sheet nhạc</p>
                                <p class="text-white font-bold text-lg">{{ number_format($stats['total_products'] ?? 0) }}</p>
                            </div>
                        </div>
                        <span class="text-green-400 text-xs">Đang bán</span>
                    </div>
                </div>

                <!-- Orders Growth -->
                <div class="bg-blue-500 bg-opacity-10 rounded-lg p-3 border border-blue-500 border-opacity-30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">📈</span>
                            <div>
                                <p class="text-gray-300 text-xs">Tăng trưởng tuần</p>
                                <p class="text-white font-bold text-lg">{{ number_format($stats['week_orders'] ?? 0) }}</p>
                            </div>
                        </div>
                        <span class="text-blue-400 text-xs">đơn</span>
                    </div>
                </div>

                <!-- Success Rate -->
                <div class="bg-purple-500 bg-opacity-10 rounded-lg p-3 border border-purple-500 border-opacity-30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">✅</span>
                            <div>
                                <p class="text-gray-300 text-xs">Tỷ lệ thành công</p>
                                <p class="text-white font-bold text-lg">{{ $stats['conversion_rate'] ?? 0 }}%</p>
                            </div>
                        </div>
                        <span class="text-purple-400 text-xs">hoàn thành</span>
                    </div>
                </div>

                <!-- Active Sellers -->
                <div class="bg-yellow-500 bg-opacity-10 rounded-lg p-3 border border-yellow-500 border-opacity-30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-2xl">💼</span>
                            <div>
                                <p class="text-gray-300 text-xs">Người bán</p>
                                <p class="text-white font-bold text-lg">{{ number_format($stats['total_sellers'] ?? 0) }}</p>
                            </div>
                        </div>
                        <span class="text-yellow-400 text-xs">đang hoạt động</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chart-bar {
    animation: growUp 0.8s ease-out forwards;
    transform-origin: bottom;
}

@keyframes growUp {
    from {
        height: 0 !important;
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.admin-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.admin-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}
</style>
@endsection
