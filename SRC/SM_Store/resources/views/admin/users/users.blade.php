<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Người Dùng')

@section('content')
<!-- Users Management -->
            <div id="users" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Người Dùng</h3>
                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">Người Dùng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Email</th>
                                    <th class="text-left py-3 text-gray-300 inter">Vai trò</th>
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
                                        <td class="py-4">
                                            <span class="text-sm font-semibold text-white">Member</span>
                                        </td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                        <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
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
                                        <td class="py-4">
                                            <span class="text-sm font-semibold text-white">Seller</span>
                                        </td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                            <div class="flex space-x-2">
                                        <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
                                                <button class="bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-white text-sm" style="display:none;">Làm người bán</button>
                                            </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    <!-- Role Change Requests Table -->
                    <div class="mt-10">
                        <h4 class="text-lg font-bold text-white mb-4">Yêu cầu đăng sheet</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-white border-opacity-20">
                                        <th class="text-left py-3 text-gray-300 inter">Người Dùng</th>
                                        <th class="text-left py-3 text-gray-300 inter">Email</th>
                                        <th class="text-left py-3 text-gray-300 inter">Ngày Yêu Cầu</th>
                                        <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-row">
                                        <td class="py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                    <span class="text-white font-bold">M</span>
                                                </div>
                                                <div>
                                                    <p class="text-white font-semibold inter">Mai Văn C</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 text-white inter">maivanc@email.com</td>
                                        <td class="py-4 text-white inter">10/09/2025</td>
                                        <td class="py-4">
                                            <div class="flex space-x-2">
                                                <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Duyệt</button>
                                                <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Từ chối</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<!-- No JS needed for static role and action buttons -->
@endsection