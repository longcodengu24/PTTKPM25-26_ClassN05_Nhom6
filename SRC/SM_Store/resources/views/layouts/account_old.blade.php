<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài Khoản - Sky Music Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animation.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap');
        
        .sky-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        }
        
        .cloud {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50px;
            position: absolute;
            animation: float 20s infinite linear;
        }
        
        .cloud:before {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50px;
        }
        
        .cloud1 {
            width: 100px;
            height: 40px;
            top: 20%;
            left: -100px;
            animation-duration: 25s;
        }
        
        .cloud1:before {
            width: 50px;
            height: 50px;
            top: -25px;
            left: 10px;
        }
        
        .cloud2 {
            width: 80px;
            height: 30px;
            top: 40%;
            left: -80px;
            animation-duration: 30s;
            animation-delay: -10s;
        }
        
        .cloud2:before {
            width: 40px;
            height: 40px;
            top: -20px;
            left: 15px;
        }
        
        .cloud3 {
            width: 120px;
            height: 50px;
            top: 60%;
            left: -120px;
            animation-duration: 35s;
            animation-delay: -20s;
        }
        
        .cloud3:before {
            width: 60px;
            height: 60px;
            top: -30px;
            left: 20px;
        }
        
        @keyframes float {
            0% { transform: translateX(-100px); }
            100% { transform: translateX(calc(100vw + 100px)); }
        }
        
        .star {
            position: absolute;
            background: white;
            border-radius: 50%;
            animation: twinkle 2s infinite alternate;
        }
        
        @keyframes twinkle {
            0% { opacity: 0.3; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.2); }
        }
        
        .glow-button {
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
            transition: all 0.3s ease;
        }
        
        .glow-button:hover {
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.8);
            transform: translateY(-2px);
        }
        
        .profile-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .orbitron { font-family: 'Orbitron', monospace; }
        .inter { font-family: 'Inter', sans-serif; }

        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }

        .tab-button.active {
            background: rgba(255, 255, 255, 0.3);
            color: #fbbf24;
            font-weight: 600;
        }

        .music-note {
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }

        .coin-spin {
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            from { transform: rotateY(0deg); }
            to { transform: rotateY(360deg); }
        }

        .title-badge {
            background: linear-gradient(45deg, #fbbf24, #f59e0b);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .sheet-item {
            transition: all 0.3s ease;
        }

        .sheet-item:hover {
            transform: translateX(10px);
        }
    </style>
</head>
<body class="sky-gradient min-h-screen overflow-x-hidden">
    <!-- Animated Background Elements -->
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

    <!-- Header -->
    @include('components.navbar')

    <!-- Main Content -->
    <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <!-- Welcome Header -->
        <div class="text-center mb-8">
            <h1 class="orbitron text-4xl md:text-5xl font-extrabold text-white mb-2 drop-shadow-lg">
                🌟 Tài Khoản Của Tôi
            </h1>
            <p class="inter text-lg text-white/80">Quản lý thông tin và hoạt động của bạn</p>
        </div>

        <!-- Account Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Profile Card -->
            <div class="lg:col-span-2 profile-card rounded-3xl p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Avatar -->
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-3xl bg-gradient-to-br from-yellow-300 via-pink-400 to-purple-500 flex items-center justify-center shadow-2xl border-4 border-white/30 overflow-hidden transform transition-all duration-300 group-hover:scale-105">
                            <img src="/img/default-avatar.png" alt="Avatar" class="w-full h-full object-cover rounded-2xl" />
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white flex items-center justify-center">
                            <span class="text-white text-xs">✓</span>
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <div class="flex-1 text-center sm:text-left">
                        <h2 class="orbitron text-3xl md:text-4xl font-extrabold text-white mb-2 drop-shadow">
                            Nguyễn Văn A
                        </h2>
                        <p class="inter text-lg text-gray-200 mb-4">
                            🗓️ Thành viên từ tháng 8, 2023
                        </p>
                        
                        <!-- Coins Display -->
                        <div class="bg-gradient-to-r from-yellow-400/20 to-orange-400/20 backdrop-blur-sm rounded-2xl p-4 border border-yellow-300/30">
                            <div class="flex items-center justify-center sm:justify-start gap-3">
                                <span class="coin-spin text-5xl drop-shadow-lg">🪙</span>
                                <div>
                                    <span class="orbitron text-3xl font-extrabold text-yellow-300 drop-shadow">2,450</span>
                                    <p class="inter text-sm text-yellow-100 tracking-wide">Sky Coins</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="profile-card rounded-3xl p-6">
                <h3 class="orbitron text-xl font-bold text-white mb-6 text-center">
                    ⚡ Hành Động Nhanh
                </h3>
                
                <div class="space-y-4">
                    <!-- Seller Panel (if applicable) -->
                    @if(session('role') === 'saler')
                    <div class="relative group">
                        <a href="{{ route('saler.dashboard') }}" 
                           class="block w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white p-4 rounded-2xl inter font-bold shadow-xl transition-all duration-300 transform hover:scale-[1.02] hover:-translate-y-1">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🛒</span>
                                <div>
                                    <div class="font-bold">Seller Panel</div>
                                    <div class="text-xs text-blue-100">Quản lý cửa hàng</div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                    
                    <!-- Financial Actions -->
                    <a href="{{ route('account.deposit') }}" 
                       class="block w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white p-4 rounded-2xl inter font-bold shadow-xl transition-all duration-300 transform hover:scale-[1.02] hover:-translate-y-1">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">💰</span>
                            <div>
                                <div class="font-bold">Nạp Coins</div>
                                <div class="text-xs text-green-100">Tăng số dư tài khoản</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('account.withdraw') }}" 
                       class="block w-full bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white p-4 rounded-2xl inter font-bold shadow-xl transition-all duration-300 transform hover:scale-[1.02] hover:-translate-y-1">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">�</span>
                            <div>
                                <div class="font-bold">Rút Coins</div>
                                <div class="text-xs text-orange-100">Chuyển về ví cá nhân</div>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('account.settings') }}" 
                       class="block w-full bg-white/20 hover:bg-white/30 text-white p-4 rounded-2xl inter font-bold shadow-xl transition-all duration-300 transform hover:scale-[1.02] hover:-translate-y-1 border border-white/30">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">⚙️</span>
                            <div>
                                <div class="font-bold">Cài Đặt</div>
                                <div class="text-xs text-gray-200">Tùy chỉnh tài khoản</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Section -->
        <div class="profile-card rounded-3xl p-8">
            <!-- Navigation Tabs -->
            <div class="flex flex-wrap gap-2 mb-8 border-b border-white/20 pb-6">
                <a href="{{ route('account.sheets') }}" 
                   class="tab-button @if(request()->routeIs('account.sheets')) active @endif px-6 py-3 rounded-xl inter font-semibold text-white transition-all duration-300 hover:bg-white/20">
                    <span class="flex items-center gap-2">
                        <span>📜</span>
                        <span>Sheet Nhạc</span>
                    </span>
                </a>
                <a href="{{ route('account.activity') }}" 
                   class="tab-button @if(request()->routeIs('account.activity')) active @endif px-6 py-3 rounded-xl inter font-semibold text-white transition-all duration-300 hover:bg-white/20">
                    <span class="flex items-center gap-2">
                        <span>📊</span>
                        <span>Hoạt Động</span>
                    </span>
                </a>
            </div>
            
            <!-- Tab Content -->
            <div class="min-h-[400px]">
                @yield('content')
            </div>
        </div>
    </main>

<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97382cf535b75dfc',t:'MTc1NTkyNjU4My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
