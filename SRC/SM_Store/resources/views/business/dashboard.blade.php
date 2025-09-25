@extends('layouts.business')

@section('title', 'Business Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">📊 Business Dashboard</h2>
        <span class="text-sm text-gray-300">
            Xin chào, {{ session('name') }} ({{ session('email') }})
        </span>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-gray-600 text-sm">Doanh thu hôm nay</h3>
            <p class="text-2xl font-bold text-blue-600">1.200.000đ</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-gray-600 text-sm">Sản phẩm đang bán</h3>
            <p class="text-2xl font-bold text-green-600">45</p>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-gray-600 text-sm">Đơn hàng mới</h3>
            <p class="text-2xl font-bold text-purple-600">7</p>
        </div>
    </div>

    <!-- Tables -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Danh sách sản phẩm -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">📦 Sản phẩm mới nhất</h3>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Tên</th>
                        <th class="px-4 py-2 text-left">Giá</th>
                        <th class="px-4 py-2 text-left">Tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-2">Tai nghe Sony</td>
                        <td class="px-4 py-2">2.500.000đ</td>
                        <td class="px-4 py-2">20</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2">Loa JBL</td>
                        <td class="px-4 py-2">3.200.000đ</td>
                        <td class="px-4 py-2">12</td>
                    </tr>
                </tbody>
            </table>
            <a href="#" class="text-blue-500 text-sm mt-2 inline-block">Xem tất cả →</a>
        </div>

        <!-- Đơn hàng gần đây -->
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">🛒 Đơn hàng gần đây</h3>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left">Khách hàng</th>
                        <th class="px-4 py-2 text-left">Sản phẩm</th>
                        <th class="px-4 py-2 text-left">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b">
                        <td class="px-4 py-2">Nguyễn Văn A</td>
                        <td class="px-4 py-2">Tai nghe Sony</td>
                        <td class="px-4 py-2">2.500.000đ</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-4 py-2">Trần Thị B</td>
                        <td class="px-4 py-2">Loa JBL</td>
                        <td class="px-4 py-2">3.200.000đ</td>
                    </tr>
                </tbody>
            </table>
            <a href="#" class="text-blue-500 text-sm mt-2 inline-block">Xem tất cả →</a>
        </div>
    </div>
</div>
@endsection
