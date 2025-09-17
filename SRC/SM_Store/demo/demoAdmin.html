<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sky Music Store - Admin Dashboard</title>
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
            <!-- Logo -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🎵</span>
                </div>
                <div>
                    <h1 class="orbitron text-lg font-bold text-white">Sky Music</h1>
                    <p class="inter text-xs text-gray-300">Admin Panel</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li>
                        <button class="sidebar-link active w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('dashboard')">
                            <span>📊</span>
                            <span>Dashboard</span>
                        </button>
                    </li>
                    <li>
                        <button class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('products')">
                            <span>🎼</span>
                            <span>Quản Lý Sheet Nhạc</span>
                        </button>
                    </li>
                    <li>
                        <button class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('orders')">
                            <span>🛒</span>
                            <span>Đơn Hàng</span>
                        </button>
                    </li>
                    <li>
                        <button class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('users')">
                            <span>👥</span>
                            <span>Người Dùng</span>
                        </button>
                    </li>
                    <li>
                        <button class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('analytics')">
                            <span>📈</span>
                            <span>Thống Kê</span>
                        </button>
                    </li>
                    <li>
                        <button class="sidebar-link w-full text-left px-4 py-3 rounded-lg text-white inter flex items-center space-x-3" onclick="showAdminContent('settings')">
                            <span>⚙️</span>
                            <span>Cài Đặt</span>
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- User Info -->
            <div class="admin-card rounded-lg p-4 mt-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">A</span>
                    </div>
                    <div>
                        <p class="text-white font-semibold inter">Admin</p>
                        <p class="text-gray-300 text-xs inter">admin@skymusic.com</p>
                    </div>
                </div>
            </div>
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

            <!-- Dashboard Content -->
            <div id="dashboard" class="admin-content active px-6 pb-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Tổng Doanh Thu</p>
                                <p class="orbitron text-2xl font-bold text-white">₫15.5M</p>
                                <p class="inter text-green-400 text-sm">+12% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">💰</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Đơn Hàng</p>
                                <p class="orbitron text-2xl font-bold text-white">1,234</p>
                                <p class="inter text-blue-400 text-sm">+8% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">🛒</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Người Dùng</p>
                                <p class="orbitron text-2xl font-bold text-white">10,567</p>
                                <p class="inter text-purple-400 text-sm">+15% tháng này</p>
                            </div>
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">👥</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="inter text-gray-300 text-sm">Sheet Nhạc</p>
                                <p class="orbitron text-2xl font-bold text-white">567</p>
                                <p class="inter text-yellow-400 text-sm">+5 bài mới</p>
                            </div>
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                <span class="text-xl">🎼</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Doanh Thu Theo Tháng</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📊 Biểu đồ doanh thu</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Top Sheet Nhạc Bán Chạy</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎵</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Dreams of Light</p>
                                        <p class="text-gray-300 text-sm inter">Season of Dreams</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">234 lượt mua</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎶</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Aurora Concert</p>
                                        <p class="text-gray-300 text-sm inter">Season of Aurora</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">189 lượt mua</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-white bg-opacity-5 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">🎼</span>
                                    <div>
                                        <p class="text-white font-semibold inter">Forest Theme</p>
                                        <p class="text-gray-300 text-sm inter">Hidden Forest</p>
                                    </div>
                                </div>
                                <span class="text-green-400 font-bold inter">156 lượt mua</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-xl font-bold text-white mb-4">Đơn Hàng Gần Đây</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">ID</th>
                                    <th class="text-left py-3 text-gray-300 inter">Khách Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Sản Phẩm</th>
                                    <th class="text-left py-3 text-gray-300 inter">Giá</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1234</td>
                                    <td class="py-3 text-white inter">Nguyễn Văn A</td>
                                    <td class="py-3 text-white inter">Dreams of Light</td>
                                    <td class="py-3 text-white inter">50.000đ</td>
                                    <td class="py-3"><span class="status-badge status-active">Hoàn thành</span></td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1235</td>
                                    <td class="py-3 text-white inter">Trần Thị B</td>
                                    <td class="py-3 text-white inter">Aurora Concert</td>
                                    <td class="py-3 text-white inter">75.000đ</td>
                                    <td class="py-3"><span class="status-badge status-pending">Đang xử lý</span></td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-3 text-white inter">#1236</td>
                                    <td class="py-3 text-white inter">Lê Văn C</td>
                                    <td class="py-3 text-white inter">Forest Theme</td>
                                    <td class="py-3 text-white inter">30.000đ</td>
                                    <td class="py-3"><span class="status-badge status-active">Hoàn thành</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Products Management -->
            <div id="products" class="admin-content px-6 pb-6">
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

            <!-- Orders Management -->
            <div id="orders" class="admin-content px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Đơn Hàng</h3>

                    <!-- Order Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">156</p>
                            <p class="text-blue-200 inter text-sm">Tổng đơn hàng</p>
                        </div>
                        <div class="bg-green-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">134</p>
                            <p class="text-green-200 inter text-sm">Hoàn thành</p>
                        </div>
                        <div class="bg-yellow-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">18</p>
                            <p class="text-yellow-200 inter text-sm">Đang xử lý</p>
                        </div>
                        <div class="bg-red-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">4</p>
                            <p class="text-red-200 inter text-sm">Đã hủy</p>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">ID Đơn</th>
                                    <th class="text-left py-3 text-gray-300 inter">Khách Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Sản Phẩm</th>
                                    <th class="text-left py-3 text-gray-300 inter">Tổng Tiền</th>
                                    <th class="text-left py-3 text-gray-300 inter">Ngày Đặt</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-4 text-white inter font-mono">#ORD-1234</td>
                                    <td class="py-4">
                                        <div>
                                            <p class="text-white inter">Nguyễn Văn A</p>
                                            <p class="text-gray-300 text-sm inter">nguyenvana@email.com</p>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Dreams of Light</td>
                                    <td class="py-4 text-white inter font-semibold">50.000đ</td>
                                    <td class="py-4 text-white inter">15/12/2024</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoàn thành</span></td>
                                    <td class="py-4">
                                        <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Chi tiết</button>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-4 text-white inter font-mono">#ORD-1235</td>
                                    <td class="py-4">
                                        <div>
                                            <p class="text-white inter">Trần Thị B</p>
                                            <p class="text-gray-300 text-sm inter">tranthib@email.com</p>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">Aurora Concert</td>
                                    <td class="py-4 text-white inter font-semibold">75.000đ</td>
                                    <td class="py-4 text-white inter">15/12/2024</td>
                                    <td class="py-4"><span class="status-badge status-pending">Đang xử lý</span></td>
                                    <td class="py-4">
                                        <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Chi tiết</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Management -->
            <div id="users" class="admin-content px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Người Dùng</h3>

                    <!-- User Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-purple-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">10,567</p>
                            <p class="text-purple-200 inter text-sm">Tổng người dùng</p>
                        </div>
                        <div class="bg-green-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">8,234</p>
                            <p class="text-green-200 inter text-sm">Đang hoạt động</p>
                        </div>
                        <div class="bg-blue-500 bg-opacity-20 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-white orbitron">156</p>
                            <p class="text-blue-200 inter text-sm">Mới tháng này</p>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white border-opacity-20">
                                    <th class="text-left py-3 text-gray-300 inter">Người Dùng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Email</th>
                                    <th class="text-left py-3 text-gray-300 inter">Ngày Đăng Ký</th>
                                    <th class="text-left py-3 text-gray-300 inter">Đơn Hàng</th>
                                    <th class="text-left py-3 text-gray-300 inter">Trạng Thái</th>
                                    <th class="text-left py-3 text-gray-300 inter">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">N</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Nguyễn Văn A</p>
                                                <p class="text-gray-300 text-sm inter">Khách hàng VIP</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">nguyenvana@email.com</td>
                                    <td class="py-4 text-white inter">01/10/2024</td>
                                    <td class="py-4 text-white inter">15 đơn</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Xem</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="table-row">
                                    <td class="py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center">
                                                <span class="text-white font-bold">T</span>
                                            </div>
                                            <div>
                                                <p class="text-white font-semibold inter">Trần Thị B</p>
                                                <p class="text-gray-300 text-sm inter">Khách hàng thường</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-white inter">tranthib@email.com</td>
                                    <td class="py-4 text-white inter">15/11/2024</td>
                                    <td class="py-4 text-white inter">3 đơn</td>
                                    <td class="py-4"><span class="status-badge status-active">Hoạt động</span></td>
                                    <td class="py-4">
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm">Xem</button>
                                            <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded text-white text-sm">Khóa</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Analytics -->
            <div id="analytics" class="admin-content px-6 pb-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Doanh Thu Theo Ngày</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📊 Biểu đồ doanh thu theo ngày</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Người Dùng Mới</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">📈 Biểu đồ người dùng mới</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Top Danh Mục Bán Chạy</h3>
                        <div class="chart-placeholder h-64 rounded-lg flex items-center justify-center">
                            <p class="text-white inter">🥧 Biểu đồ tròn danh mục</p>
                        </div>
                    </div>

                    <div class="admin-card rounded-xl p-6">
                        <h3 class="orbitron text-xl font-bold text-white mb-4">Thống Kê Truy Cập</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Lượt truy cập hôm nay</span>
                                <span class="text-white font-bold inter">2,456</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Trang được xem nhiều nhất</span>
                                <span class="text-white font-bold inter">Shop</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Thời gian truy cập trung bình</span>
                                <span class="text-white font-bold inter">4m 32s</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-300 inter">Tỷ lệ thoát</span>
                                <span class="text-white font-bold inter">23%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div id="settings" class="admin-content px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Cài Đặt Hệ Thống</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- General Settings -->
                        <div>
                            <h4 class="orbitron text-lg font-bold text-white mb-4">Cài Đặt Chung</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="inter text-white block mb-2">Tên Website</label>
                                    <input type="text" value="Sky Music Store" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                                <div>
                                    <label class="inter text-white block mb-2">Email Liên Hệ</label>
                                    <input type="email" value="admin@skymusic.com" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                                <div>
                                    <label class="inter text-white block mb-2">Số Điện Thoại</label>
                                    <input type="tel" value="0123456789" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div>
                            <h4 class="orbitron text-lg font-bold text-white mb-4">Cài Đặt Thanh Toán</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">Momo</p>
                                        <p class="text-gray-300 text-sm inter">Thanh toán qua ví Momo</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">ZaloPay</p>
                                        <p class="text-gray-300 text-sm inter">Thanh toán qua ZaloPay</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">Chuyển Khoản</p>
                                        <p class="text-gray-300 text-sm inter">Chuyển khoản ngân hàng</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button class="bg-blue-500 hover:bg-blue-600 px-8 py-3 rounded-lg text-white inter font-semibold">
                            Lưu Cài Đặt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showAdminContent(contentId) {
            // Hide all content
            const contents = document.querySelectorAll('.admin-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Show selected content
            document.getElementById(contentId).classList.add('active');
            
            // Update sidebar navigation
            const sidebarLinks = document.querySelectorAll('.sidebar-link');
            sidebarLinks.forEach(link => link.classList.remove('active'));
            
            // Find and activate the corresponding sidebar link
            const activeLink = document.querySelector(`[onclick="showAdminContent('${contentId}')"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'972e8b181623cb25',t:'MTc1NTgyNTU4MS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
