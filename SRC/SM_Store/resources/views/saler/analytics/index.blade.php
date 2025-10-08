@extends('layouts.seller')

@section('title', 'Thống kê - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">📈 Thống kê & Phân tích</h2>
        <div class="flex items-center space-x-3">
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="7">7 ngày qua</option>
                <option value="30">30 ngày qua</option>
                <option value="90">3 tháng qua</option>
                <option value="365">1 năm qua</option>
            </select>
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
                    <p class="text-gray-300 text-sm inter">Doanh thu</p>
                    <p class="text-2xl font-bold text-white orbitron">45.2M</p>
                    <p class="text-green-400 text-sm flex items-center space-x-1">
                        <span>↗️</span>
                        <span>+12.5%</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">💰</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Đơn hàng</p>
                    <p class="text-2xl font-bold text-white orbitron">1,234</p>
                    <p class="text-green-400 text-sm flex items-center space-x-1">
                        <span>↗️</span>
                        <span>+8.2%</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🛒</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Lượt truy cập</p>
                    <p class="text-2xl font-bold text-white orbitron">24.8K</p>
                    <p class="text-red-400 text-sm flex items-center space-x-1">
                        <span>↘️</span>
                        <span>-3.1%</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">👥</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tỷ lệ chuyển đổi</p>
                    <p class="text-2xl font-bold text-white orbitron">4.97%</p>
                    <p class="text-green-400 text-sm flex items-center space-x-1">
                        <span>↗️</span>
                        <span>+1.2%</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">📈 Doanh thu theo thời gian</h3>
            <div class="h-64 flex items-end justify-between space-x-2 bg-white bg-opacity-5 rounded-lg p-4">
                <div class="bg-blue-500 rounded-t flex-1" style="height: 60%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 80%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 45%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 90%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 70%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 100%"></div>
                <div class="bg-blue-500 rounded-t flex-1" style="height: 85%"></div>
            </div>
            <div class="flex justify-between text-gray-300 text-sm mt-2">
                <span>T2</span>
                <span>T3</span>
                <span>T4</span>
                <span>T5</span>
                <span>T6</span>
                <span>T7</span>
                <span>CN</span>
            </div>
        </div>

        <!-- Top Products -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🏆 Sheet nhạc bán chạy</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center text-white font-bold">1</div>
                        <div>
                            <p class="text-white font-medium">Canon in D</p>
                            <p class="text-gray-300 text-sm">Pachelbel</p>
                        </div>
                    </div>
                    <span class="text-white font-medium">156 bán</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gray-400 rounded-lg flex items-center justify-center text-white font-bold">2</div>
                        <div>
                            <p class="text-white font-medium">Für Elise</p>
                            <p class="text-gray-300 text-sm">Beethoven</p>
                        </div>
                    </div>
                    <span class="text-white font-medium">134 bán</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold">3</div>
                        <div>
                            <p class="text-white font-medium">Shape of You</p>
                            <p class="text-gray-300 text-sm">Ed Sheeran</p>
                        </div>
                    </div>
                    <span class="text-white font-medium">98 bán</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">4</div>
                        <div>
                            <p class="text-white font-medium">Moonlight Sonata</p>
                            <p class="text-gray-300 text-sm">Beethoven</p>
                        </div>
                    </div>
                    <span class="text-white font-medium">87 bán</span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white font-bold">5</div>
                        <div>
                            <p class="text-white font-medium">Perfect</p>
                            <p class="text-gray-300 text-sm">Ed Sheeran</p>
                        </div>
                    </div>
                    <span class="text-white font-medium">76 bán</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Analytics -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">👥 Phân tích khách hàng</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Khách hàng mới</span>
                    <span class="text-white font-medium">+245</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Khách hàng quay lại</span>
                    <span class="text-white font-medium">1,234</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Khách VIP</span>
                    <span class="text-white font-medium">128</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tỷ lệ giữ chân</span>
                    <span class="text-green-400 font-medium">87.5%</span>
                </div>
            </div>
        </div>

        <!-- Geographic Analytics -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🌍 Phân bố địa lý</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Hà Nội</span>
                    <span class="text-white font-medium">35%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">TP.HCM</span>
                    <span class="text-white font-medium">28%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Đà Nẵng</span>
                    <span class="text-white font-medium">12%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Khác</span>
                    <span class="text-white font-medium">25%</span>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">⚡ Hiệu suất</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Thời gian tải trung bình</span>
                    <span class="text-green-400 font-medium">1.2s</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tỷ lệ thoát</span>
                    <span class="text-yellow-400 font-medium">24.5%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Thời gian ở lại</span>
                    <span class="text-white font-medium">4m 32s</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Trang/Phiên</span>
                    <span class="text-white font-medium">3.7</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection