@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - Sky Music Store')

@section('content')
<div id="reset-password" class="page-content">
    <section class="relative z-10 py-20 px-6">
        <div class="max-w-md mx-auto">
            <div class="game-card rounded-xl p-8">
                <h2 class="orbitron text-3xl font-bold text-white text-center mb-8">🔒 Đặt Lại Mật Khẩu</h2>
                <form method="POST" action="" class="space-y-6">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token ?? '' }}">
                    <div>
                        <label class="inter text-white block mb-2" for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email của bạn">
                    </div>
                    <div>
                        <label class="inter text-white block mb-2" for="password">Mật khẩu mới</label>
                        <input id="password" type="password" name="password" required class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập mật khẩu mới">
                    </div>
                    <div>
                        <label class="inter text-white block mb-2" for="password_confirmation">Xác nhận mật khẩu</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập lại mật khẩu">
                    </div>
                    <button type="submit" class="glow-button w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 rounded-full font-semibold">Đặt lại mật khẩu</button>
                </form>
                <div class="text-center mt-6">
                    <a href="{{ url('/login') }}" class="inter text-blue-200 hover:text-white">Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
