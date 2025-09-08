<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Người Dùng')

@section('content')
<!-- Users Management -->
            <div id="users" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Người Dùng</h3>

                    <!-- User Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-purple-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">10,567</p>
                            <p class="text-purple-200 inter text-sm">Tổng người dùng</p>
                        </div>
                        <div class="bg-green-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">8,234</p>
                            <p class="text-green-200 inter text-sm">Đang hoạt động</p>
                        </div>
                        <div class="bg-blue-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">156</p>
                            <p class="text-blue-200 inter text-sm">Mới tháng này</p>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">Người Dùng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Email</th>
                                    <th class="text-left py-3 text-gray-300 inter">Ngày Đăng Ký</th>
                                    <th class="text-left py-3 text-gray-300 inter">Đơn Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">N</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Nguyễn Văn A</p>
                                                <p class="text-gray-300 text-sm inter">Khách hàng VIP</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">nguyenvana@email.com</td>
                                    <td class="py-4 text-white inter">01/10/2024</td>
                                    <td class="py-4 text-white inter">15 đơn</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Xem</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">T</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Trần Thị B</p>
                                                <p class="text-gray-300 text-sm inter">Khách hàng thường</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">tranthib@email.com</td>
                                    <td class="py-4 text-white inter">15/11/2024</td>
                                    <td class="py-4 text-white inter">3 đơn</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Xem</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

@endsection