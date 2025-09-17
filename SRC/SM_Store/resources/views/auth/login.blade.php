@extends('layouts.app')

@section('title', 'Đăng nhập')

@section('content')
<div id="login" class="page-content">
    <section class="relative z-10 py-20 px-6">
        <div class="max-w-md mx-auto">
            <div class="game-card rounded-xl p-8">
                <h2 class="orbitron text-3xl font-bold text-white text-center mb-8">🔐 Đăng Nhập</h2>
                

                <!-- //hien thi loi -->
                @if ($errors->any())
    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

                <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">

                    @csrf 
                    <div>
                        <label class="inter text-white block mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email của bạn" required>
                    </div>
                    <div>
                        <label class="inter text-white block mb-2">Mật khẩu</label>
                        <input type="password" name="password" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập mật khẩu" required>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-blue-200">
                            <input type="checkbox" name="remember" class="mr-2">
                            <span class="inter text-sm">Ghi nhớ đăng nhập</span>
                        </label>
                        <a href="{{ url('/forgot-password') }}" class="inter text-sm text-yellow-300 hover:text-yellow-200">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="glow-button w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-full font-semibold">Đăng Nhập</button>
                </form>
                
                <div class="text-center mt-6">
                    <p class="inter text-blue-200">Chưa có tài khoản? <a href="{{ url('/register') }}" class="text-yellow-300 hover:text-yellow-200">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection