<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Đơn Hàng')

@section('content')
<!-- Orders Management -->
            <div id="orders" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Đơn Hàng</h3>

                    <!-- Order Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">156</p>
                            <p class="text-blue-200 inter text-sm">Tổng đơn hàng</p>
                        </div>
                        <div class="bg-green-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">134</p>
                            <p class="text-green-200 inter text-sm">Hoàn thành</p>
                        </div>
                        <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">18</p>
                            <p class="text-yellow-200 inter text-sm">Đang xử lý</p>
                        </div>
                        <div class="bg-red-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">4</p>
                            <p class="text-red-200 inter text-sm">Đã hủy</p>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">ID Đơn</th>
                                    <th class="text-left py-3 text-gray-300 inter">Khách Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Sản Phẩm</th>
                                    <th class="text-left py-3 text-gray-300 inter">Tổng Tiền</th>
                                    <th class="text-left py-3 text-gray-300 inter">Ngày Đặt</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-4 text-white inter font-mono">#ORD-1234</td>
                                    <td class="py-4">
                                        <div>
                                            <p class="text-white inter">Nguyễn Văn A</p>
                                            <p class="text-gray-300 text-sm inter">nguyenvana@email.com</p>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Dreams of Light</td>
                                    <td class="py-4 text-white inter font-semibold">50.000đ</td>
                                    <td class="py-4 text-white inter">15/12/2024</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoàn thành</span></td>
                                    <td class="py-4">
                                        <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-4 text-white inter font-mono">#ORD-1235</td>
                                    <td class="py-4">
                                        <div>
                                            <p class="text-white inter">Trần Thị B</p>
                                            <p class="text-gray-300 text-sm inter">tranthib@email.com</p>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Aurora Concert</td>
                                    <td class="py-4 text-white inter font-semibold">75.000đ</td>
                                    <td class="py-4 text-white inter">15/12/2024</td>
                                    <td class="py-4"><span class="status-badge status-pending">Đang xử lý</span></td>
                                    <td class="py-4">
                                        <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Chi tiết</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
@endsection