<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Thống Kê')

@section('content')
<!-- Analytics -->
            <div id="analytics" class="admin-content active px-6 pb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Doanh Thu Theo Ngày</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📊 Biểu đồ doanh thu theo ngày</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Người Dùng Mới</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📈 Biểu đồ người dùng mới</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Top Danh Mục Bán Chạy</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">🥧 Biểu đồ tròn danh mục</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Thống Kê Truy Cập</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Lượt truy cập hôm nay</span>
                                <span class="text-white font-bold inter">2,456</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Trang được xem nhiều nhất</span>
                                <span class="text-white font-bold inter">Shop</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Thời gian truy cập trung bình</span>
                                <span class="text-white font-bold inter">4m 32s</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Tỷ lệ thoát</span>
                                <span class="text-white font-bold inter">23%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection