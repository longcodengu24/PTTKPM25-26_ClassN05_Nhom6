@extends('layouts.seller')

@section('title', 'Quản lý Đơn Hàng - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">🛒 Quản lý Đơn Hàng</h2>
        <div class="flex items-center space-x-3">
            <button class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
                <span>📤</span>
                <span>Xuất Excel</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_orders'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📦</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Chờ xử lý</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['pending_orders'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">⏳</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Đã hoàn thành</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['completed_orders'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">✅</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Doanh thu</p>
                    <p class="text-2xl font-bold text-white orbitron">{{ number_format($stats['total_revenue'] ?? 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">💰</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="admin-card rounded-xl p-6 mb-6">
        <form action="{{ route('saler.orders') }}" method="GET">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ $searchQuery ?? '' }}" 
                           placeholder="Tìm kiếm theo mã đơn, khách hàng hoặc sản phẩm..." 
                           class="w-full px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg text-white inter flex items-center space-x-2">
                    <span>🔍</span>
                    <span>Tìm kiếm</span>
                </button>
                @if(!empty($searchQuery))
                    <a href="{{ route('saler.orders') }}" class="px-6 py-2 bg-gray-500 hover:bg-gray-600 rounded-lg text-white inter flex items-center space-x-2">
                        <span>✕</span>
                        <span>Xóa</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="admin-card rounded-xl p-6">
        @if(isset($error))
            <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-4">
                {{ $error }}
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-300 text-lg">📦 Chưa có đơn hàng nào</p>
                <p class="text-gray-400 text-sm mt-2">Đơn hàng của bạn sẽ hiển thị tại đây</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-600">
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Mã đơn</th>
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Khách hàng</th>
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Sản phẩm</th>
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Tổng tiền</th>
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Trạng thái</th>
                            <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Ngày đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                            <td class="py-4 px-4">
                                <span class="text-white font-medium inter">#{{ substr($order['id'], 0, 8) }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">
                                            {{ strtoupper(substr($order['buyer_name'] ?? 'U', 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-white font-medium inter">{{ $order['buyer_name'] ?? 'Unknown' }}</p>
                                        <p class="text-gray-300 text-sm">{{ $order['buyer_email'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center space-x-3">
                                    @if(!empty($order['image_path']))
                                        <img src="{{ $order['image_path'] }}" alt="{{ $order['product_name'] }}" 
                                             class="w-10 h-10 rounded object-cover">
                                    @endif
                                    <div>
                                        <p class="text-white">{{ $order['product_name'] }}</p>
                                        <p class="text-gray-300 text-sm">1 sản phẩm</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-white font-medium">{{ number_format($order['price']) }} VND</td>
                            <td class="py-4 px-4">
                                @php
                                    $statusColors = [
                                        'completed' => 'bg-green-500 bg-opacity-20 text-green-300',
                                        'pending' => 'bg-yellow-500 bg-opacity-20 text-yellow-300',
                                        'processing' => 'bg-blue-500 bg-opacity-20 text-blue-300',
                                        'cancelled' => 'bg-red-500 bg-opacity-20 text-red-300',
                                    ];
                                    $statusLabels = [
                                        'completed' => 'Hoàn thành',
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                    $status = $order['status'] ?? 'completed';
                                @endphp
                                <span class="px-3 py-1 {{ $statusColors[$status] ?? 'bg-gray-500 bg-opacity-20 text-gray-300' }} rounded-full text-sm">
                                    {{ $statusLabels[$status] ?? $status }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-gray-300">
                                {{ date('d/m/Y', strtotime($order['purchased_at'] ?? now())) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="flex items-center justify-between mt-6">
                <span class="text-gray-300 text-sm">Hiển thị {{ $orders->count() }} đơn hàng</span>
            </div>
        @endif
    </div>
</div>
@endsection