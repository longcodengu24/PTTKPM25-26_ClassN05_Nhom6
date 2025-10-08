<nav class="relative z-10 p-6">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <!-- Logo Section -->
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-white/30 to-white/10 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 shadow-lg">
                <span class="text-2xl music-note filter drop-shadow-lg">🎵</span>
            </div>
            <h1 class="orbitron text-2xl font-bold text-white drop-shadow-lg tracking-wide">Sky Music Store</h1>
        </div>
        
        <!-- Navigation Links -->
        <div class="hidden md:flex space-x-8 inter">
            <a href="{{ url('/') }}" class="nav-link text-white/90 hover:text-yellow-300 transition-all duration-300 font-medium {{ request()->is('/') ? 'text-yellow-300 font-semibold' : '' }} relative group">
                Trang Chủ
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-300 transition-all duration-300 group-hover:w-full {{ request()->is('/') ? 'w-full' : '' }}"></span>
            </a>
            <a href="{{ url('/shop') }}" class="nav-link text-white/90 hover:text-yellow-300 transition-all duration-300 font-medium {{ request()->is('shop') ? 'text-yellow-300 font-semibold' : '' }} relative group">
                Shop
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-300 transition-all duration-300 group-hover:w-full {{ request()->is('shop') ? 'w-full' : '' }}"></span>
            </a>
            <a href="{{ url('/community') }}" class="nav-link text-white/90 hover:text-yellow-300 transition-all duration-300 font-medium {{ request()->is('community') ? 'text-yellow-300 font-semibold' : '' }} relative group">
                Cộng Đồng
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-300 transition-all duration-300 group-hover:w-full {{ request()->is('community') ? 'w-full' : '' }}"></span>
            </a>
            <a href="{{ url('/support') }}" class="nav-link text-white/90 hover:text-yellow-300 transition-all duration-300 font-medium {{ request()->is('support') ? 'text-yellow-300 font-semibold' : '' }} relative group">
                Hỗ Trợ
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-yellow-300 transition-all duration-300 group-hover:w-full {{ request()->is('support') ? 'w-full' : '' }}"></span>
            </a>
        </div>
        
        {{-- Kiểm tra trạng thái đăng nhập --}}
        @if(session()->has('firebase_uid'))
            <!-- Compact User Menu -->
            <div class="flex items-center space-x-2">
                <!-- Shop Features (only on shop page) -->
                @if(request()->is('shop'))
                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 px-2 py-1 rounded-lg font-bold text-xs shadow-md">
                        🪙 {{ number_format(session('coins', 0)) }}
                    </div>
                    <a href="{{ url('/cart') }}" 
                       class="bg-white/10 backdrop-blur-sm text-white p-2 rounded-lg hover:bg-white/20 transition-all duration-300 border border-white/20" 
                       title="Giỏ hàng">
                        🛒
                    </a>
                @endif
                
                <!-- User Profile -->
                <a href="{{ url('/account') }}" 
                   class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-lg px-3 py-2 hover:bg-white/20 transition-all duration-300 border border-white/20"
                   title="Tài khoản của tôi">
                    <img src="{{ isset($currentUser) ? $currentUser['avatar'] : session('avatar', '/img/default-avatar.png') }}" 
                         alt="Avatar" 
                         class="w-6 h-6 rounded-full border border-white/50">
                    <span class="text-white font-medium text-sm hidden lg:block max-w-[80px] truncate">{{ isset($currentUser) ? $currentUser['name'] : session('name', 'User') }}</span>
                </a>
                
                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="GET" class="inline">
                    <button type="submit" 
                            class="bg-red-500/80 backdrop-blur-sm text-white p-2 rounded-lg hover:bg-red-600 transition-all duration-300 border border-red-400/50 shadow-md" 
                            title="Đăng xuất">
                        Đăng xuất
                    </button>
                </form>
            </div>
        @else
            <!-- Login Button -->
            <a href="{{ url('/login') }}" 
               class="bg-gradient-to-r from-white/20 to-white/10 text-white px-4 py-2 rounded-lg backdrop-blur-sm hover:from-white/30 hover:to-white/20 transition-all duration-300 inter font-semibold border border-white/30 shadow-lg">
                <span class="flex items-center space-x-2">
                    <span>👤</span>
                    <span class="hidden sm:inline">Đăng Nhập</span>
                </span>
            </a>
        @endif
    </div>
</nav>
