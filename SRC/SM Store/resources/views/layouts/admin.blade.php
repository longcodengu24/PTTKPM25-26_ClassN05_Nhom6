<!-- filepath: resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap');
        
        .sky-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .admin-sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #334155 100%);
            backdrop-filter: blur(10px);
        }
        
        .admin-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .admin-card:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .orbitron { font-family: 'Orbitron', monospace; }
        .inter { font-family: 'Inter', sans-serif; }

        .admin-content {
            display: none;
        }
        
        .admin-content.active {
            display: block;
        }

        .sidebar-link {
            transition: all 0.3s ease;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar-link.active {
            background: rgba(102, 126, 234, 0.3);
            border-right: 3px solid #667eea;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .chart-placeholder {
            background: linear-gradient(45deg, #667eea, #764ba2);
            opacity: 0.8;
        }

        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
    </style>
</head>
<body class="sky-gradient min-h-screen">
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
                    <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📊 Dashboard</a></li>
                    <li><a href="{{ route('admin.products') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🎼 Quản Lý Sheet Nhạc</a></li>
                    <li><a href="{{ route('admin.orders') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">🛒 Đơn Hàng</a></li>
                    <li><a href="{{ route('admin.users') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">👥 Người Dùng</a></li>
                    <li><a href="{{ route('admin.analytics') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">📈 Thống Kê</a></li>
                    <li><a href="{{ route('admin.settings') }}" class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3">⚙️ Cài Đặt</a></li>
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
                        <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white inter">
                            Đăng Xuất
                        </button>
                    </div>
                </div>
            </header>
            @yield('content')
        </div>
    </div>
    
</body>
</html>