<!-- filepath: resources/views/layouts/seller.blade.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Seller Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animation.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    

</head>
<body class="sky-gradient min-h-screen">
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="cloud cloud1"></div>
        <div class="cloud cloud2"></div>
        <div class="cloud cloud3"></div>
        <div class="cloud cloud4"></div>
        <div class="cloud cloud5"></div>
        <div class="star w-2 h-2" style="top: 15%; left: 20%; animation-delay: 0s;"></div>
        <div class="star w-1 h-1" style="top: 25%; left: 80%; animation-delay: 1s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 35%; left: 60%; animation-delay: 0.5s;"></div>
        <div class="star w-1 h-1" style="top: 45%; left: 30%; animation-delay: 1.5s;"></div>
        <div class="star w-2 h-2" style="top: 55%; left: 90%; animation-delay: 0.8s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 10%; left: 50%; animation-delay: 0.3s;"></div>
        <div class="star w-1 h-1" style="top: 60%; left: 10%; animation-delay: 1.2s;"></div>
        <div class="star w-2 h-2" style="top: 70%; left: 70%; animation-delay: 0.6s;"></div>
        <div class="star w-1 h-1" style="top: 80%; left: 40%; animation-delay: 1.7s;"></div>
        <div class="star w-1.5 h-1.5" style="top: 20%; left: 75%; animation-delay: 0.9s;"></div>
        <div class="star w-2 h-2" style="top: 30%; left: 10%; animation-delay: 1.1s;"></div>
    </div>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="admin-sidebar w-64 p-6 flex flex-col">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🎵</span>
                </div>
                <div>
                    <h1 class="orbitron text-lg font-bold text-white">Sky Music</h1>
                    <p class="inter text-xs text-gray-300">Seller Panel</p>
                </div>
            </div>
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li><a href="{{ route('saler.dashboard') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📊 Dashboard</a></li>
                    <li><a href="{{ route('saler.products.index') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🎼 Sheet Nhạc</a></li>
                    <li><a href="{{ route('saler.orders') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🛒 Đơn Hàng</a></li>
                    <li><a href="{{ route('saler.profile') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">👤 Hồ Sơ</a></li>
                </ul>
                
                <!-- Interface Switch -->
                <div class="mt-6 pt-6 border-t border-white border-opacity-20">
                    <h4 class="text-gray-300 text-sm mb-3">🔄 Chuyển Giao Diện</h4>
                    <a href="{{ route('home') }}" 
                       class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3 bg-blue-600 hover:bg-blue-700">
                        🏠 Giao Diện Khách Hàng
                    </a>
                </div>
            </nav>
        </div>
        
        <!-- Main Content -->
        
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="admin-card m-6 rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="orbitron text-2xl font-bold text-white">Seller Dashboard</h2>
                        <p class="inter text-gray-300">Chào mừng! Quản lý shop nhạc của bạn.</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Quick Switch Button with Tooltip -->
                        <div class="relative group">
                            <a href="{{ route('home') }}" 
                               class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2 transition-all">
                                <span>🏠</span>
                                <span class="hidden md:inline">Giao Diện Khách</span>
                            </a>
                            
                            <!-- Tooltip -->
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none z-50 whitespace-nowrap">
                                <div class="text-center">
                                    <div class="font-semibold">🔄 Chuyển sang giao diện khách hàng</div>
                                    <div class="text-xs text-gray-300">Xem cửa hàng như khách hàng thấy</div>
                                </div>
                                <!-- Arrow -->
                                <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-l-transparent border-r-transparent border-t-gray-800"></div>
                            </div>
                        </div>
                        
                        <button class="admin-card px-4 py-2 rounded-lg text-white inter hover:bg-white hover:bg-opacity-20">
                            🔔 Thông báo
                        </button>
                        <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white inter">
                            Đăng Xuất
                        </a>

                    </div>
                </div>
            </header>
            @yield('content')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>