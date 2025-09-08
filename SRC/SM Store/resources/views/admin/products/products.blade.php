<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Quản Lý Sheet Nhạc')

@section('content')

<!-- Products Management -->
            <div id="products" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="orbitron text-2xl font-bold text-white">Quản Lý Sheet Nhạc</h3>
                        <button class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                            + Thêm Sheet Mới
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-4 mb-6">
                        <select class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30">
                            <option>Tất cả danh mục</option>
                            <option>Season</option>
                            <option>Event</option>
                            <option>Cổ điển</option>
                        </select>
                        <input type="text" placeholder="Tìm kiếm sheet nhạc..." class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 placeholder-gray-300">
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">Tên</th>
                                    <th class="text-left py-3 text-gray-300 inter">Danh Mục</th>
                                    <th class="text-left py-3 text-gray-300 inter">Giá</th>
                                    <th class="text-left py-3 text-gray-300 inter">Lượt Mua</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                                <span class="text-xl">🎵</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Dreams of Light</p>
                                                <p class="text-gray-300 text-sm inter">Season of Dreams</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Season</td>
                                    <td class="py-4 text-white inter">50.000đ</td>
                                    <td class="py-4 text-white inter">234</td>
                                    <td class="py-4"><span class="status-badge status-active">Đang bán</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-white text-sm">Sửa</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Xóa</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                                                <span class="text-xl">🎶</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Aurora Concert</p>
                                                <p class="text-gray-300 text-sm inter">Season of Aurora</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Season</td>
                                    <td class="py-4 text-white inter">75.000đ</td>
                                    <td class="py-4 text-white inter">189</td>
                                    <td class="py-4"><span class="status-badge status-active">Đang bán</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-white text-sm">Sửa</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Xóa</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

@endsection