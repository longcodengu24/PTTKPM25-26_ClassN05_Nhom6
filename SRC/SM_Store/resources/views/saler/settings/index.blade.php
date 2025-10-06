@extends('layouts.saler')

@section('title', 'Cài đặt - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">⚙️ Cài đặt Shop</h2>
        <button class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
            <span>💾</span>
            <span>Lưu tất cả</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Menu -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-4 orbitron">Menu cài đặt</h3>
            <nav class="space-y-2">
                <button class="w-full text-left px-4 py-3 bg-blue-500 bg-opacity-20 text-blue-300 rounded-lg inter flex items-center space-x-3">
                    <span>🏪</span>
                    <span>Thông tin Shop</span>
                </button>
                <button class="w-full text-left px-4 py-3 text-gray-300 hover:bg-white hover:bg-opacity-10 rounded-lg inter flex items-center space-x-3">
                    <span>💳</span>
                    <span>Thanh toán</span>
                </button>
                <button class="w-full text-left px-4 py-3 text-gray-300 hover:bg-white hover:bg-opacity-10 rounded-lg inter flex items-center space-x-3">
                    <span>🚚</span>
                    <span>Vận chuyển</span>
                </button>
                <button class="w-full text-left px-4 py-3 text-gray-300 hover:bg-white hover:bg-opacity-10 rounded-lg inter flex items-center space-x-3">
                    <span>📧</span>
                    <span>Email & Thông báo</span>
                </button>
                <button class="w-full text-left px-4 py-3 text-gray-300 hover:bg-white hover:bg-opacity-10 rounded-lg inter flex items-center space-x-3">
                    <span>🔐</span>
                    <span>Bảo mật</span>
                </button>
                <button class="w-full text-left px-4 py-3 text-gray-300 hover:bg-white hover:bg-opacity-10 rounded-lg inter flex items-center space-x-3">
                    <span>🎨</span>
                    <span>Giao diện</span>
                </button>
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Shop Information -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 orbitron flex items-center space-x-2">
                    <span>🏪</span>
                    <span>Thông tin Shop</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Tên Shop</label>
                        <input type="text" value="Sky Music Store" 
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Email liên hệ</label>
                        <input type="email" value="contact@skymusic.com" 
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Số điện thoại</label>
                        <input type="tel" value="+84 123 456 789" 
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Website</label>
                        <input type="url" value="https://skymusic.com" 
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Mô tả Shop</label>
                    <textarea rows="4" 
                              class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Mô tả về shop của bạn...">Sky Music Store - Nơi cung cấp sheet nhạc chất lượng cao cho mọi cấp độ từ cơ bản đến chuyên nghiệp. Chúng tôi có hơn 1000+ sheet nhạc đa dạng thể loại.</textarea>
                </div>
                
                <div class="mt-6">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Logo Shop</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 bg-blue-500 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">🎵</span>
                        </div>
                        <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter">
                            Thay đổi Logo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Business Settings -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 orbitron flex items-center space-x-2">
                    <span>💼</span>
                    <span>Cài đặt kinh doanh</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Đơn vị tiền tệ</label>
                        <select class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="VND" selected>Việt Nam Đồng (VND)</option>
                            <option value="USD">US Dollar (USD)</option>
                            <option value="EUR">Euro (EUR)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Múi giờ</label>
                        <select class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Asia/Ho_Chi_Minh" selected>Việt Nam (GMT+7)</option>
                            <option value="Asia/Bangkok">Thailand (GMT+7)</option>
                            <option value="Asia/Singapore">Singapore (GMT+8)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Phí xử lý (%)</label>
                        <input type="number" value="2.5" step="0.1" min="0" max="100"
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Giá tối thiểu</label>
                        <input type="number" value="10000" min="0"
                               class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Toggle Settings -->
                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Tự động duyệt đơn hàng</p>
                            <p class="text-gray-300 text-sm">Đơn hàng sẽ được duyệt tự động</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-blue-500">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-6"></span>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Cho phép đánh giá</p>
                            <p class="text-gray-300 text-sm">Khách hàng có thể đánh giá sản phẩm</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-blue-500">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-6"></span>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Hiển thị số lượng tồn kho</p>
                            <p class="text-gray-300 text-sm">Hiển thị số lượng còn lại cho khách</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-600">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-1"></span>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Thông báo email</p>
                            <p class="text-gray-300 text-sm">Gửi email thông báo đơn hàng mới</p>
                        </div>
                        <button class="relative inline-flex h-6 w-11 items-center rounded-full bg-blue-500">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-6"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="admin-card rounded-xl p-6">
                <h3 class="text-xl font-bold text-white mb-4 orbitron flex items-center space-x-2">
                    <span>🔐</span>
                    <span>Bảo mật</span>
                </h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 text-sm font-medium mb-2">Đổi mật khẩu</label>
                        <div class="space-y-3">
                            <input type="password" placeholder="Mật khẩu hiện tại" 
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="password" placeholder="Mật khẩu mới" 
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <input type="password" placeholder="Xác nhận mật khẩu mới" 
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button class="mt-3 bg-yellow-500 hover:bg-yellow-600 px-4 py-2 rounded-lg text-white inter">
                            Cập nhật mật khẩu
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Xác thực 2 lớp (2FA)</p>
                            <p class="text-gray-300 text-sm">Tăng cường bảo mật tài khoản</p>
                        </div>
                        <button class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg text-white inter">
                            Kích hoạt
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-white font-medium">Lịch sử đăng nhập</p>
                            <p class="text-gray-300 text-sm">Xem các lần đăng nhập gần đây</p>
                        </div>
                        <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter">
                            Xem chi tiết
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection