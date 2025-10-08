@extends('layouts.seller')

@section('title', 'Chi tiết Đơn Hàng - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('saler.orders') }}" class="text-gray-300 hover:text-white">
                <span class="text-xl">←</span>
            </a>
            <h2 class="orbitron text-2xl font-bold text-white">📋 Chi tiết Đơn Hàng #ORD001234</h2>
        </div>
        <div class="flex items-center space-x-3">
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="pending">Chờ xử lý</option>
                <option value="processing">Đang xử lý</option>
                <option value="completed">Hoàn thành</option>
                <option value="cancelled">Đã hủy</option>
            </select>
            <button class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg text-white inter">
                Cập nhật trạng thái
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Items -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 orbitron">Sản phẩm đã đặt</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center">
                                <span class="text-2xl">🎵</span>
                            </div>
                            <div>
                                <h4 class="text-white font-medium">Canon in D</h4>
                                <p class="text-gray-300 text-sm">Pachelbel - Classical</p>
                                <p class="text-gray-300 text-xs">SKU: SHEET001</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">50,000 VND</p>
                            <p class="text-gray-300 text-sm">Số lượng: 1</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center">
                                <span class="text-2xl">🎶</span>
                            </div>
                            <div>
                                <h4 class="text-white font-medium">Für Elise</h4>
                                <p class="text-gray-300 text-sm">Beethoven - Classical</p>
                                <p class="text-gray-300 text-xs">SKU: SHEET002</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">45,000 VND</p>
                            <p class="text-gray-300 text-sm">Số lượng: 1</p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="mt-6 pt-4 border-t border-gray-600">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-300">Tạm tính:</span>
                            <span class="text-white">95,000 VND</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Phí xử lý:</span>
                            <span class="text-white">5,000 VND</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-300">Giảm giá:</span>
                            <span class="text-green-400">-10,000 VND</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold border-t border-gray-600 pt-2">
                            <span class="text-white">Tổng cộng:</span>
                            <span class="text-white">90,000 VND</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 orbitron">Lịch sử đơn hàng</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-white font-medium">Đơn hàng đã được tạo</p>
                            <p class="text-gray-300 text-sm">25/09/2025 - 14:30</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-white font-medium">Thanh toán thành công</p>
                            <p class="text-gray-300 text-sm">25/09/2025 - 14:32</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-white font-medium">Đang xử lý đơn hàng</p>
                            <p class="text-gray-300 text-sm">25/09/2025 - 15:00</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 bg-gray-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-gray-300">Gửi file cho khách hàng</p>
                            <p class="text-gray-400 text-sm">Chờ xử lý</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 orbitron">Thông tin khách hàng</h3>
                
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium">NV</span>
                    </div>
                    <div>
                        <p class="text-white font-medium">Nguyễn Văn A</p>
                        <p class="text-gray-300 text-sm">Khách VIP</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-300">📧</span>
                        <span class="text-white text-sm">nguyenvana@email.com</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-300">📱</span>
                        <span class="text-white text-sm">0123 456 789</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-300">🛒</span>
                        <span class="text-white text-sm">45 đơn hàng trước đó</span>
                    </div>
                </div>
                
                <button class="w-full mt-4 bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter">
                    Xem hồ sơ khách hàng
                </button>
            </div>

            <!-- Payment Info -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 orbitron">Thông tin thanh toán</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Phương thức:</span>
                        <span class="text-white">Chuyển khoản</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Trạng thái:</span>
                        <span class="text-green-400">Đã thanh toán</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Ngày TT:</span>
                        <span class="text-white">25/09/2025</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Mã GD:</span>
                        <span class="text-white">TXN123456</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-lg font-bold text-white mb-4 orbitron">Thao tác nhanh</h3>
                
                <div class="space-y-3">
                    <button class="w-full bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg text-white inter text-sm">
                        📤 Gửi file sheet nhạc
                    </button>
                    <button class="w-full bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter text-sm">
                        📧 Gửi email khách hàng
                    </button>
                    <button class="w-full bg-purple-500 hover:bg-purple-600 px-4 py-2 rounded-lg text-white inter text-sm">
                        🖨️ In hóa đơn
                    </button>
                    <button class="w-full bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white inter text-sm">
                        ❌ Hủy đơn hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection