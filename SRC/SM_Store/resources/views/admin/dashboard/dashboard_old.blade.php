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

    <!-- Quick Stats Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Tổng Doanh Thu</p>
                                <p class="orbitron text-2xl font-bold text-white">₫15.5M</p>
                                <p class="inter text-green-400 text-sm">+12% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">💰</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Đơn Hàng</p>
                                <p class="orbitron text-2xl font-bold text-white">1,234</p>
                                <p class="inter text-blue-400 text-sm">+8% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">🛒</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Người Dùng</p>
                                <p class="orbitron text-2xl font-bold text-white">10,567</p>
                                <p class="inter text-purple-400 text-sm">+15% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">👥</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Sheet Nhạc</p>
                                <p class="orbitron text-2xl font-bold text-white">567</p>
                                <p class="inter text-yellow-400 text-sm">+5 bài mới</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">🎼</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Doanh Thu Theo Tháng</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📊 Biểu đồ doanh thu</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Top Sheet Nhạc Bán Chạy</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎵</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Dreams of Light</p>
                                        <p class="text-gray-300 text-sm inter">Season of Dreams</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">234 lượt mua</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎶</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Aurora Concert</p>
                                        <p class="text-gray-300 text-sm inter">Season of Aurora</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">189 lượt mua</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎼</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Forest Theme</p>
                                        <p class="text-gray-300 text-sm inter">Hidden Forest</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">156 lượt mua</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-xl font-bold text-white mb-4">Đơn Hàng Gần Đây</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">ID</th>
                                    <th class="text-left py-3 text-gray-300 inter">Khách Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Sản Phẩm</th>
                                    <th class="text-left py-3 text-gray-300 inter">Giá</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1234</td>
                                    <td class="py-3 text-white inter">Nguyễn Văn A</td>
                                    <td class="py-3 text-white inter">Dreams of Light</td>
                                    <td class="py-3 text-white inter">50.000đ</td>
                                    <td class="py-3"><span class="status-badge status-active">Hoàn thành</span></td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1235</td>
                                    <td class="py-3 text-white inter">Trần Thị B</td>
                                    <td class="py-3 text-white inter">Aurora Concert</td>
                                    <td class="py-3 text-white inter">75.000đ</td>
                                    <td class="py-3"><span class="status-badge status-pending">Đang xử lý</span></td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1236</td>
                                    <td class="py-3 text-white inter">Lê Văn C</td>
                                    <td class="py-3 text-white inter">Forest Theme</td>
                                    <td class="py-3 text-white inter">30.000đ</td>
                                    <td class="py-3"><span class="status-badge status-active">Hoàn thành</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection