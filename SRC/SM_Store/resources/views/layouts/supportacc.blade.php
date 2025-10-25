<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Support Account') - Sky Music Store</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .sky-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .orbitron { font-family: 'Orbitron', monospace; }
        .inter { font-family: 'Inter', sans-serif; }
        
        .admin-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .sidebar-link {
            transition: all 0.3s ease;
        }
        
        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .admin-sidebar {
            background: linear-gradient(180deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.6) 100%);
            backdrop-filter: blur(10px);
        }
        
        .tab-button.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fbbf24;
        }
    </style>
</head>
<body class="sky-gradient min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="admin-sidebar w-64 p-6 flex flex-col">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🛠️</span>
                </div>
                <div>
                    <h1 class="orbitron text-lg font-bold text-white">Support Center</h1>
                    <p class="inter text-xs text-gray-300">Quản lý hỗ trợ</p>
                </div>
            </div>
            
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📊 Dashboard</a></li>
                    <li><a href="{{ route('admin.anyf') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3 bg-purple-600">🎼 Yêu cầu Seller</a></li>
                    <li><a href="{{ route('admin.roles.index') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">👥 Phân quyền</a></li>
                    <li><a href="{{ route('admin.users') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">👤 Người dùng</a></li>
                    <li><a href="{{ route('admin.products') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📦 Sản phẩm</a></li>
                    <li><a href="{{ route('admin.orders') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🛒 Đơn hàng</a></li>
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
                        <h2 class="orbitron text-2xl font-bold text-white">@yield('page-title', 'Support Center')</h2>
                        <p class="inter text-gray-300">@yield('page-description', 'Quản lý hỗ trợ và yêu cầu người dùng')</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- User Info -->
                        <div class="flex items-center space-x-3">
                            <img src="{{ session('avatar', '/img/default-avatar.png') }}" 
                                 alt="Avatar" 
                                 class="w-8 h-8 rounded-full border-2 border-white/30">
                            <div class="text-right">
                                <div class="text-white font-semibold">{{ session('name', 'Admin') }}</div>
                                <div class="text-gray-300 text-sm">{{ session('email', '') }}</div>
                            </div>
                        </div>
                        
                        <!-- Quick Switch Button -->
                        <div class="relative group">
                            <a href="{{ route('home') }}" 
                               class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2 transition-all">
                                <span>🏠</span>
                                <span class="hidden md:inline">Giao Diện Khách</span>
                            </a>
                        </div>
                        
                        <a href="{{ route('logout') }}" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white inter">
                            Đăng Xuất
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="px-6 pb-6">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
