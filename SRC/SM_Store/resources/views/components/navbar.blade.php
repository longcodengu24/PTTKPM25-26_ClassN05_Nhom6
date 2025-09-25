<nav class="relative z-10 p-6">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                <span class="text-2xl music-note">🎵</span>
            </div>
            <h1 class="orbitron text-2xl font-bold text-white">Sky Music Store</h1>
        </div>
        
        <div class="hidden md:flex space-x-8 inter">
            <a href="{{ url('/') }}" class="nav-link text-white hover:text-yellow-300 transition-colors {{ request()->is('/') ? 'active' : '' }}">Trang Chủ</a>
            <a href="{{ url('/shop') }}" class="nav-link text-white hover:text-yellow-300 transition-colors {{ request()->is('shop') ? 'active' : '' }}">Shop</a>
            <a href="{{ url('/community') }}" class="nav-link text-white hover:text-yellow-300 transition-colors {{ request()->is('community') ? 'active' : '' }}">Cộng Đồng</a>
            <a href="{{ url('/support') }}" class="nav-link text-white hover:text-yellow-300 transition-colors {{ request()->is('support') ? 'active' : '' }}">Hỗ Trợ</a>
        </div>
        
        {{-- Kiểm tra trạng thái đăng nhập --}}
        @if(session()->has('firebase_uid'))
            <!-- Hiển thị khi đã đăng nhập -->
            <div class="flex items-center space-x-4">
                <!-- Hiển thị ảnh đại diện -->
                <a href="{{ url('/account') }}" class="flex items-center space-x-2">
                    <img src="{{ session('avatar', '/img/default-avatar.png') }}" 
                         alt="Avatar" 
                         class="w-10 h-10 rounded-full border-2 border-white">
                    <span class="text-white">{{ session('name', 'Người dùng') }}</span>
                </a>

                <!-- Hiển thị số xu và giỏ hàng trên trang Shop -->
                @if(request()->is('shop'))
                    <span class="text-yellow-300 font-bold">
                        Xu: {{ session('coins', 0) }}
                    </span>
                    <a href="{{ url('/cart') }}" 
                       class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg hover:bg-opacity-30 transition-all">
                        🛒 Giỏ Hàng
                    </a>
                @endif

                <!-- Nút Đăng Xuất -->
                <form action="{{ route('logout') }}" method="GET" class="inline">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-all">
                        Đăng Xuất
                    </button>
                </form>
            </div>
        @else
            <!-- Hiển thị khi chưa đăng nhập -->
            <a href="{{ url('/login') }}" 
               class="bg-white bg-opacity-20 text-white px-6 py-2 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">
                Đăng Nhập
            </a>
        @endif
    </div>
</nav>
