@extends('layouts.seller')

@section('title', 'Quản lý Người Dùng - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">👥 Quản lý Khách Hàng</h2>
        <div class="flex items-center space-x-3">
            <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
                <span>📊</span>
                <span>Xuất báo cáo</span>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng khách hàng</p>
                    <p class="text-2xl font-bold text-white orbitron">2,456</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">👥</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Khách VIP</p>
                    <p class="text-2xl font-bold text-white orbitron">128</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">⭐</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Hoạt động hôm nay</p>
                    <p class="text-2xl font-bold text-white orbitron">87</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🟢</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng chi tiêu</p>
                    <p class="text-2xl font-bold text-white orbitron">45.2M</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">💰</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="admin-card rounded-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Tìm kiếm tên, email khách hàng..." 
                       class="w-full px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả loại KH</option>
                <option value="vip">Khách VIP</option>
                <option value="regular">Khách thường</option>
                <option value="new">Khách mới</option>
            </select>
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="inactive">Không hoạt động</option>
                <option value="blocked">Bị khóa</option>
            </select>
        </div>
    </div>

    <!-- Users Table -->
    <div class="admin-card rounded-xl p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Khách hàng</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Loại KH</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Tổng đơn hàng</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Tổng chi tiêu</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Hoạt động cuối</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Trạng thái</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">NV</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Nguyễn Văn An</p>
                                    <p class="text-gray-300 text-sm">nguyenvanan@email.com</p>
                                    <p class="text-gray-300 text-xs">ID: #USR001234</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-yellow-500 bg-opacity-20 text-yellow-300 rounded-full text-sm flex items-center space-x-1">
                                <span>⭐</span>
                                <span>VIP</span>
                            </span>
                        </td>
                        <td class="py-4 px-4 text-white">45 đơn</td>
                        <td class="py-4 px-4 text-white font-medium">2,850,000 VND</td>
                        <td class="py-4 px-4 text-gray-300">2 giờ trước</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Hoạt động</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Gửi tin nhắn">💬</button>
                                <button class="text-purple-400 hover:text-purple-300 p-2" title="Lịch sử mua hàng">📋</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">LT</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Lê Thị Bình</p>
                                    <p class="text-gray-300 text-sm">lethibinh@email.com</p>
                                    <p class="text-gray-300 text-xs">ID: #USR001235</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-blue-500 bg-opacity-20 text-blue-300 rounded-full text-sm">Thường</span>
                        </td>
                        <td class="py-4 px-4 text-white">12 đơn</td>
                        <td class="py-4 px-4 text-white font-medium">680,000 VND</td>
                        <td class="py-4 px-4 text-gray-300">1 ngày trước</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Hoạt động</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Gửi tin nhắn">💬</button>
                                <button class="text-purple-400 hover:text-purple-300 p-2" title="Lịch sử mua hàng">📋</button>
                            </div>
                        </td>
                    </tr>

                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">TV</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Trần Văn Cường</p>
                                    <p class="text-gray-300 text-sm">tranvancuong@email.com</p>
                                    <p class="text-gray-300 text-xs">ID: #USR001236</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-gray-500 bg-opacity-20 text-gray-300 rounded-full text-sm">Mới</span>
                        </td>
                        <td class="py-4 px-4 text-white">3 đơn</td>
                        <td class="py-4 px-4 text-white font-medium">195,000 VND</td>
                        <td class="py-4 px-4 text-gray-300">3 ngày trước</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-gray-500 bg-opacity-20 text-gray-300 rounded-full text-sm">Không hoạt động</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Gửi email khuyến khích">📧</button>
                                <button class="text-purple-400 hover:text-purple-300 p-2" title="Lịch sử mua hàng">📋</button>
                            </div>
                        </td>
                    </tr>

                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium">PH</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Phạm Thị Hoa</p>
                                    <p class="text-gray-300 text-sm">phamthihoa@email.com</p>
                                    <p class="text-gray-300 text-xs">ID: #USR001237</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-yellow-500 bg-opacity-20 text-yellow-300 rounded-full text-sm flex items-center space-x-1">
                                <span>⭐</span>
                                <span>VIP</span>
                            </span>
                        </td>
                        <td class="py-4 px-4 text-white">78 đơn</td>
                        <td class="py-4 px-4 text-white font-medium">5,240,000 VND</td>
                        <td class="py-4 px-4 text-gray-300">30 phút trước</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Hoạt động</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Gửi tin nhắn">💬</button>
                                <button class="text-purple-400 hover:text-purple-300 p-2" title="Lịch sử mua hàng">📋</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6">
            <span class="text-gray-300 text-sm">Hiển thị 1-10 của 2,456 khách hàng</span>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-2 bg-white bg-opacity-10 text-gray-300 rounded-lg hover:bg-opacity-20">Trước</button>
                <button class="px-3 py-2 bg-blue-500 text-white rounded-lg">1</button>
                <button class="px-3 py-2 bg-white bg-opacity-10 text-gray-300 rounded-lg hover:bg-opacity-20">2</button>
                <button class="px-3 py-2 bg-white bg-opacity-10 text-gray-300 rounded-lg hover:bg-opacity-20">3</button>
                <button class="px-3 py-2 bg-white bg-opacity-10 text-gray-300 rounded-lg hover:bg-opacity-20">Sau</button>
            </div>
        </div>
    </div>
</div>
@endsection