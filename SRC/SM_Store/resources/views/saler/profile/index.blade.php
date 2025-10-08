@extends('layouts.seller')

@section('title', 'Hồ Sơ Seller')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">👤 Hồ Sơ Của Tôi</h2>
    </div>

    <!-- Quick Interface Switch -->
    <div class="admin-card rounded-xl p-6 mb-6">
        <h3 class="text-xl font-semibold text-white mb-4">🔄 Chuyển Đổi Giao Diện</h3>
        <p class="text-blue-200 mb-4">Bạn có thể dễ dàng chuyển đổi giữa giao diện quản lý seller và giao diện khách hàng.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Current Interface -->
            <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4">
                <div class="text-center">
                    <div class="text-3xl mb-2">🛒</div>
                    <h4 class="text-white font-semibold mb-2">Seller Panel</h4>
                    <p class="text-green-200 text-sm mb-3">Đang sử dụng</p>
                    <button disabled class="bg-gray-500 px-4 py-2 rounded-lg text-white cursor-not-allowed">
                        Giao diện hiện tại
                    </button>
                </div>
            </div>
            
            <!-- Switch to Customer Interface -->
            <div class="bg-blue-500 bg-opacity-20 border border-blue-500 rounded-lg p-4">
                <div class="text-center">
                    <div class="text-3xl mb-2">🏠</div>
                    <h4 class="text-white font-semibold mb-2">Giao Diện Khách Hàng</h4>
                    <p class="text-blue-200 text-sm mb-3">Xem như khách hàng</p>
                    <a href="{{ route('home') }}" 
                       class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inline-block transition-all">
                        Chuyển đổi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Navigation -->
        <div class="mt-6 pt-4 border-t border-white border-opacity-20">
            <h4 class="text-white font-semibold mb-3">🚀 Truy Cập Nhanh</h4>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('home') }}" 
                   class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2">
                    <span>🏠</span>
                    <span>Trang Chủ</span>
                </a>
                <a href="{{ route('shop.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2">
                    <span>🛍️</span>
                    <span>Cửa Hàng</span>
                </a>
                <a href="{{ route('community.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2">
                    <span>👥</span>
                    <span>Cộng Đồng</span>
                </a>
                <a href="{{ route('support.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg text-white text-sm flex items-center space-x-2">
                    <span>🆘</span>
                    <span>Hỗ Trợ</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Info Card -->
    <div class="admin-card rounded-xl p-6 mb-6">
        <h3 class="text-xl font-semibold text-white mb-4">Thông Tin Cá Nhân</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-blue-200 font-semibold mb-2">
                    📧 Email
                </label>
                <input type="email" 
                       value="{{ session('firebase_email', 'seller@example.com') }}" 
                       readonly
                       class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white">
            </div>
            
            <div>
                <label class="block text-blue-200 font-semibold mb-2">
                    🆔 Seller ID
                </label>
                <input type="text" 
                       value="{{ session('firebase_uid', 'N/A') }}" 
                       readonly
                       class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white">
            </div>
            
            <div>
                <label class="block text-blue-200 font-semibold mb-2">
                    👥 Vai Trò
                </label>
                <input type="text" 
                       value="{{ session('role', 'saler') }}" 
                       readonly
                       class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white">
            </div>
            
            <div>
                <label class="block text-blue-200 font-semibold mb-2">
                    ⏰ Đăng nhập lần cuối
                </label>
                <input type="text" 
                       value="{{ now()->format('d/m/Y H:i') }}" 
                       readonly
                       class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white">
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">🎵</div>
            <div class="text-2xl font-bold text-white mb-1">-</div>
            <div class="text-blue-200">Tổng Sản Phẩm</div>
        </div>
        
        <div class="admin-card rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">📥</div>
            <div class="text-2xl font-bold text-white mb-1">-</div>
            <div class="text-blue-200">Lượt Tải</div>
        </div>
        
        <div class="admin-card rounded-xl p-6 text-center">
            <div class="text-3xl mb-2">💰</div>
            <div class="text-2xl font-bold text-white mb-1">-</div>
            <div class="text-blue-200">Doanh Thu</div>
        </div>
    </div>

    <!-- Actions -->
    <div class="admin-card rounded-xl p-6">
        <h3 class="text-xl font-semibold text-white mb-4">Hành Động</h3>
        
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('saler.products.index') }}" 
               class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                🎼 Quản Lý Sản Phẩm
            </a>
            
            <a href="{{ route('saler.products.create') }}" 
               class="bg-green-500 hover:bg-green-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                ➕ Thêm Sản Phẩm Mới
            </a>
            
            <a href="{{ route('saler.analytics') }}" 
               class="bg-purple-500 hover:bg-purple-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                📈 Xem Thống Kê
            </a>
        </div>
    </div>
</div>
@endsection