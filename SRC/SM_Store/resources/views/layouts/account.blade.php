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
    <main class="relative z-10 max-w-7xl mx-auto px-6 py-8">
        <!-- Account Information Section -->
        <div class="profile-card rounded-2xl p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-6 flex-1">
                    <div class="relative">
                        <div class="w-28 h-28 rounded-full bg-gradient-to-br from-yellow-300 to-pink-400 flex items-center justify-center shadow-xl border-4 border-white overflow-hidden">
                            <img src="/img/default-avatar.png" alt="Avatar" class="w-full h-full object-cover rounded-full" />
                        </div>
                    </div>
                    <div>
                        <h2 class="orbitron text-4xl font-extrabold text-white mb-1 drop-shadow">Nguyễn Văn A</h2>
                        <p class="inter text-lg text-gray-200 mb-2">Thành viên từ 2023</p>
                        <div class="flex items-center gap-3 mt-2">
                            <span class="coin-spin text-4xl drop-shadow-lg">🪙</span>
                            <span class="orbitron text-3xl font-extrabold text-yellow-300 drop-shadow">2,450</span>
                            <span class="inter text-base text-yellow-100 tracking-wide ml-1">Sky Coins</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3 items-stretch w-full md:w-48">
                    <a href="{{ route('account.settings') }}" class="glow-button bg-white/40 hover:bg-white/60 text-gray-800 px-4 py-2.5 rounded-lg inter font-semibold shadow-md flex items-center gap-2 justify-center text-base border border-white/30 backdrop-blur-md transition-all">
                        <span class="text-lg">⚙️</span> Cài đặt
                    </a>
                    <a href="{{ route('account.deposit') }}" class="glow-button bg-white/40 hover:bg-white/60 text-gray-800 px-4 py-2.5 rounded-lg inter font-bold shadow-md flex items-center gap-2 justify-center text-base border border-white/30 backdrop-blur-md transition-all">
                        <span class="text-lg">➕</span> <span>Nạp Coins</span>
                    </a>
                    <a href="{{ route('account.withdraw') }}" class="glow-button bg-white/40 hover:bg-white/60 text-gray-800 px-4 py-2.5 rounded-lg inter font-bold shadow-md flex items-center gap-2 justify-center text-base border border-white/30 backdrop-blur-md transition-all">
                        <span class="text-lg">➖</span> <span>Rút Coins</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="profile-card rounded-2xl p-6">
            <ul class="flex flex-wrap gap-4 mb-6 border-b border-white border-opacity-20 pb-4">
                <li>
                    <a href="{{ route('account.sheets') }}" class="tab-button @if(request()->routeIs('account.sheets')) active @endif px-6 py-3 rounded-lg inter font-semibold text-white transition-all">
                        📜 Sheet Nhạc Của Tôi
                    </a>
                </li>
                <li>
                    <a href="{{ route('account.activity') }}" class="tab-button @if(request()->routeIs('account.activity')) active @endif px-6 py-3 rounded-lg inter font-semibold text-white transition-all">
                        📊 Hoạt Động
                    </a>
                </li>
            </ul>
            @yield('content')
        </div>
    </main>

<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'97382cf535b75dfc',t:'MTc1NTkyNjU4My4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
