@extends('layouts.saler')

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
                    <p class="text-2xl font-bold text-white orbitron">1,234</p>
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
                    <p class="text-2xl font-bold text-white orbitron">45</p>
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
                    <p class="text-2xl font-bold text-white orbitron">987</p>
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
                    <p class="text-2xl font-bold text-white orbitron">12.5M</p>
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
                <input type="text" placeholder="Tìm kiếm mã đơn hàng, khách hàng..." 
                       class="w-full px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ xử lý</option>
                <option value="processing">Đang xử lý</option>
                <option value="shipped">Đã giao hàng</option>
                <option value="completed">Hoàn thành</option>
                <option value="cancelled">Đã hủy</option>
            </select>
            <input type="date" class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <!-- Orders Table -->
    <div class="admin-card rounded-xl p-6">
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
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <span class="text-white font-medium inter">#ORD001234</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">NV</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Nguyễn Văn A</p>
                                    <p class="text-gray-300 text-sm">nguyenvana@email.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div>
                                <p class="text-white">Canon in D + 2 sheet khác</p>
                                <p class="text-gray-300 text-sm">3 sản phẩm</p>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-white font-medium">150,000 VND</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Hoàn thành</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">25/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="In hóa đơn">🖨️</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <span class="text-white font-medium inter">#ORD001235</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">LT</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Lê Thị B</p>
                                    <p class="text-gray-300 text-sm">lethib@email.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div>
                                <p class="text-white">Für Elise</p>
                                <p class="text-gray-300 text-sm">1 sản phẩm</p>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-white font-medium">45,000 VND</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-yellow-500 bg-opacity-20 text-yellow-300 rounded-full text-sm">Chờ xử lý</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">26/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Xử lý">⚡</button>
                                <button class="text-red-400 hover:text-red-300 p-2" title="Hủy đơn">❌</button>
                            </div>
                        </td>
                    </tr>

                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <span class="text-white font-medium inter">#ORD001236</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">TV</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Trần Văn C</p>
                                    <p class="text-gray-300 text-sm">tranvanc@email.com</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div>
                                <p class="text-white">Shape of You + 4 sheet khác</p>
                                <p class="text-gray-300 text-sm">5 sản phẩm</p>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-white font-medium">280,000 VND</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-blue-500 bg-opacity-20 text-blue-300 rounded-full text-sm">Đang xử lý</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">27/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem chi tiết">👁️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Hoàn thành">✅</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6">
            <span class="text-gray-300 text-sm">Hiển thị 1-10 của 1,234 đơn hàng</span>
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