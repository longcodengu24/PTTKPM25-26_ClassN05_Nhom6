<!-- filepath: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
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
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🎵</span>
                </div>
                <div>
                    <h1 class="orbitron text-lg font-bold text-white">Sky Music</h1>
                    <p class="inter text-xs text-gray-300">Admin Panel</p>
                </div>
            </div>
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li><a href="{{ route('saler.dashboard') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📊 Dashboard</a></li>
                    <li><a href="{{ route('saler.products') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🎼 Sheet Nhạc</a></li>
                    <li><a href="{{ route('saler.orders') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🛒 Đơn Hàng</a></li>
                    <li><a href="{{ route('saler.users') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">👥 Người Dùng</a></li>
                    <li><a href="{{ route('saler.posts') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📝 Bài Viết</a></li>
                    <li><a href="{{ route('saler.analytics') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📈 Thống Kê</a></li>
                    <li><a href="{{ route('saler.settings') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">⚙️ Cài Đặt</a></li>



                </ul>
            </nav>
        </div>
        
        <!-- Main Content -->
        
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="admin-card m-6 rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="orbitron text-2xl font-bold text-white">Admin Dashboard</h2>
                        <p class="inter text-gray-300">Chào mừng trở lại! Quản lý cửa hàng của bạn.</p>
                    </div>
                    <div class="flex items-center space-x-4">
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