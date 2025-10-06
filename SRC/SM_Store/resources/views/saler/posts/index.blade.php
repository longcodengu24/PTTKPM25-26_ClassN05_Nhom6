@extends('layouts.saler')

@section('title', 'Quản lý Bài Viết - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">📝 Quản lý Bài Viết</h2>
        <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
            <span>➕</span>
            <span>Tạo bài viết mới</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng bài viết</p>
                    <p class="text-2xl font-bold text-white orbitron">156</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📄</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Đã đăng</p>
                    <p class="text-2xl font-bold text-white orbitron">132</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">✅</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Chờ duyệt</p>
                    <p class="text-2xl font-bold text-white orbitron">18</p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">⏳</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Lượt xem</p>
                    <p class="text-2xl font-bold text-white orbitron">24.5K</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">👁️</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="admin-card rounded-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="Tìm kiếm tiêu đề bài viết..." 
                       class="w-full px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả danh mục</option>
                <option value="tutorial">Hướng dẫn</option>
                <option value="review">Review nhạc</option>
                <option value="news">Tin tức</option>
                <option value="tips">Mẹo hay</option>
            </select>
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Trạng thái</option>
                <option value="published">Đã đăng</option>
                <option value="draft">Nháp</option>
                <option value="pending">Chờ duyệt</option>
                <option value="archived">Lưu trữ</option>
            </select>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="admin-card rounded-xl p-6">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Bài viết</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Danh mục</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Lượt xem</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Bình luận</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Trạng thái</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Ngày đăng</th>
                        <th class="text-left py-3 px-4 text-gray-300 font-medium inter">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">🎵</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Cách chơi piano cho người mới bắt đầu</p>
                                    <p class="text-gray-300 text-sm">Hướng dẫn từ cơ bản đến nâng cao...</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-blue-500 bg-opacity-20 text-blue-300 rounded-full text-sm">Hướng dẫn</span>
                        </td>
                        <td class="py-4 px-4 text-white">1,245</td>
                        <td class="py-4 px-4 text-white">23</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Đã đăng</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">22/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem bài viết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Chỉnh sửa">✏️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Sao chép">📋</button>
                                <button class="text-red-400 hover:text-red-300 p-2" title="Xóa">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    
                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">⭐</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Top 10 sheet nhạc được yêu thích nhất</p>
                                    <p class="text-gray-300 text-sm">Bảng xếp hạng sheet nhạc hot...</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-purple-500 bg-opacity-20 text-purple-300 rounded-full text-sm">Review nhạc</span>
                        </td>
                        <td class="py-4 px-4 text-white">2,156</td>
                        <td class="py-4 px-4 text-white">87</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-yellow-500 bg-opacity-20 text-yellow-300 rounded-full text-sm">Chờ duyệt</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">25/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem bài viết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Chỉnh sửa">✏️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Duyệt bài">✅</button>
                                <button class="text-red-400 hover:text-red-300 p-2" title="Từ chối">❌</button>
                            </div>
                        </td>
                    </tr>

                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-12 bg-gradient-to-r from-yellow-500 to-red-500 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">📰</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">Xu hướng âm nhạc 2025</p>
                                    <p class="text-gray-300 text-sm">Những xu hướng mới trong năm...</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-orange-500 bg-opacity-20 text-orange-300 rounded-full text-sm">Tin tức</span>
                        </td>
                        <td class="py-4 px-4 text-white">890</td>
                        <td class="py-4 px-4 text-white">12</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Đã đăng</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">20/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem bài viết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Chỉnh sửa">✏️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Sao chép">📋</button>
                                <button class="text-purple-400 hover:text-purple-300 p-2" title="Lưu trữ">📦</button>
                            </div>
                        </td>
                    </tr>

                    <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-16 h-12 bg-gradient-to-r from-pink-500 to-purple-500 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">💡</span>
                                </div>
                                <div>
                                    <p class="text-white font-medium inter">5 mẹo luyện tập hiệu quả</p>
                                    <p class="text-gray-300 text-sm">Cách tối ưu hóa thời gian luyện...</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Mẹo hay</span>
                        </td>
                        <td class="py-4 px-4 text-white">1,567</td>
                        <td class="py-4 px-4 text-white">45</td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 bg-green-500 bg-opacity-20 text-green-300 rounded-full text-sm">Đã đăng</span>
                        </td>
                        <td class="py-4 px-4 text-gray-300">18/09/2025</td>
                        <td class="py-4 px-4">
                            <div class="flex items-center space-x-2">
                                <button class="text-blue-400 hover:text-blue-300 p-2" title="Xem bài viết">👁️</button>
                                <button class="text-yellow-400 hover:text-yellow-300 p-2" title="Chỉnh sửa">✏️</button>
                                <button class="text-green-400 hover:text-green-300 p-2" title="Sao chép">📋</button>
                                <button class="text-red-400 hover:text-red-300 p-2" title="Xóa">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center justify-between mt-6">
            <span class="text-gray-300 text-sm">Hiển thị 1-10 của 156 bài viết</span>
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