@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-3xl font-bold text-white">📊 Dashboard Quản Trị</h2>
        <div class="text-gray-300 text-sm">
            <span>Cập nhật:</span>
            <span class="text-white font-semibold">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-500 bg-opacity-20 border border-red-500 rounded-lg p-4 mb-6">
            <p class="text-red-200">{{ $error }}</p>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-300 text-sm mb-1">💰 Tổng Doanh Thu</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_revenue'], 0, ',', '.') }} đ</p>
                    <p class="text-green-400 text-sm mt-1">Tháng này: {{ number_format($stats['month_revenue'], 0, ',', '.') }} đ</p>
                </div>
            </div>
        </div>

        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-300 text-sm mb-1">📦 Tổng Đơn Hàng</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_orders']) }}</p>
                    <p class="text-blue-400 text-sm mt-1">Hôm nay: {{ $stats['today_orders'] }} đơn</p>
                </div>
            </div>
        </div>

        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-300 text-sm mb-1">👥 Người Dùng</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_users']) }}</p>
                    <p class="text-purple-400 text-sm mt-1">Seller: {{ $stats['total_sellers'] }} | Khách: {{ $stats['total_customers'] }}</p>
                </div>
            </div>
        </div>

        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-gray-300 text-sm mb-1">🎼 Sheet Nhạc</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_products']) }}</p>
                    <p class="text-yellow-400 text-sm mt-1">Hoàn thành: {{ $stats['conversion_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Today Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 mb-1">Doanh thu hôm nay</p>
                    <p class="text-3xl font-bold orbitron">{{ number_format($stats['today_revenue'], 0, ',', '.') }} đ</p>
                </div>
                <div class="text-4xl opacity-80">💵</div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 mb-1">Đơn hoàn thành</p>
                    <p class="text-3xl font-bold orbitron">{{ number_format($stats['completed_orders']) }}</p>
                </div>
                <div class="text-4xl opacity-80">✅</div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90 mb-1">Đơn chờ xử lý</p>
                    <p class="text-3xl font-bold orbitron">{{ number_format($stats['pending_orders']) }}</p>
                </div>
                <div class="text-4xl opacity-80">⏳</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">📈 Doanh thu 7 ngày</h3>
            
            <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                <div class="h-56 flex items-end justify-around gap-2">
                    @if(!empty($stats['chart_data']['values']))
                        @foreach($stats['chart_data']['values'] as $index => $value)
                            @php
                                $percentage = $stats['chart_data']['max'] > 0 ? ($value / $stats['chart_data']['max']) * 100 : 0;
                                $height = max(5, $percentage);
                                $colors = ['#a855f7', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#14b8a6'];
                                $color = $colors[$index % 7];
                            @endphp
                            
                            <div class="flex-1 flex flex-col items-center group">
                                <div class="w-full h-48 flex items-end justify-center mb-2 relative">
                                    <div class="w-full rounded-t-lg transition-all duration-500 hover:brightness-125 cursor-pointer relative"
                                         style="height: {{ $height }}%; background: linear-gradient(to top, {{ $color }}, {{ $color }}dd); box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                        
                                        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                            <div class="font-bold">{{ number_format($value, 0, ',', '.') }} đ</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-white text-sm font-bold">{{ $stats['chart_data']['labels'][$index] ?? '' }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="w-full text-center py-8"><p class="text-gray-400">Chưa có dữ liệu</p></div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🏆 Top Sheet Bán Chạy</h3>
            
            @if(!empty($stats['top_products']))
                <div class="space-y-3">
                    @foreach($stats['top_products'] as $index => $product)
                        <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 flex items-center justify-center font-bold text-white">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-white font-semibold truncate">{{ $product['name'] }}</p>
                                        <p class="text-gray-300 text-sm">{{ number_format($product['revenue'], 0, ',', '.') }} đ</p>
                                    </div>
                                </div>
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold">{{ $product['count'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 py-8">Chưa có dữ liệu</p>
            @endif
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Top Sellers -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">⭐ Top Seller</h3>
            
            @if(!empty($stats['top_sellers']))
                <div class="space-y-3">
                    @foreach($stats['top_sellers'] as $index => $seller)
                        <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-400 to-pink-500 flex items-center justify-center font-bold text-white">{{ $index + 1 }}</div>
                                    <div>
                                        <p class="text-white font-semibold">Seller #{{ substr($seller['seller_id'], 0, 8) }}</p>
                                        <p class="text-gray-300 text-sm">{{ $seller['orders'] }} đơn</p>
                                    </div>
                                </div>
                                <p class="text-green-400 font-bold">{{ number_format($seller['revenue'], 0, ',', '.') }} đ</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 py-8">Chưa có dữ liệu</p>
            @endif
        </div>

        <!-- Recent Orders -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🕒 Đơn Hàng Gần Đây</h3>
            
            @if(!empty($stats['recent_orders']))
                <div class="space-y-3">
                    @foreach($stats['recent_orders'] as $order)
                        <div class="bg-gray-800 bg-opacity-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-semibold truncate">{{ $order['product_name'] }}</p>
                                    <p class="text-gray-300 text-xs">{{ \Carbon\Carbon::parse($order['purchased_at'])->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="text-right ml-3">
                                    <p class="text-white font-bold text-sm">{{ number_format($order['price'], 0, ',', '.') }} đ</p>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $order['status'] === 'completed' ? 'bg-green-500' : 'bg-yellow-500' }}">
                                        {{ $order['status'] === 'completed' ? 'Hoàn thành' : 'Chờ' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 py-8">Chưa có đơn hàng</p>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card rounded-xl p-6">
        <h3 class="text-xl font-bold text-white mb-4 orbitron">⚡ Thao Tác Nhanh</h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.roles.index') }}" class="bg-blue-500 hover:bg-blue-600 rounded-lg p-4 text-center transition-all hover:scale-105">
                <div class="text-3xl mb-2">👥</div>
                <p class="text-white font-semibold">Quản lý User</p>
            </a>
            <a href="{{ route('admin.products') }}" class="bg-purple-500 hover:bg-purple-600 rounded-lg p-4 text-center transition-all hover:scale-105">
                <div class="text-3xl mb-2">🎼</div>
                <p class="text-white font-semibold">Quản lý Sheet</p>
            </a>
            <a href="{{ route('admin.orders') }}" class="bg-green-500 hover:bg-green-600 rounded-lg p-4 text-center transition-all hover:scale-105">
                <div class="text-3xl mb-2">📦</div>
                <p class="text-white font-semibold">Xem Đơn Hàng</p>
            </a>
            <a href="{{ route('admin.settings') }}" class="bg-orange-500 hover:bg-orange-600 rounded-lg p-4 text-center transition-all hover:scale-105">
                <div class="text-3xl mb-2">⚙️</div>
                <p class="text-white font-semibold">Cài Đặt</p>
            </a>
        </div>
    </div>
</div>
@endsection
