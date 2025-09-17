@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div id="forgot-password" class="page-content">
    <section class="relative z-10 py-20 px-6">
        <div class="max-w-md mx-auto">
            <div class="game-card rounded-xl p-8">
                <h2 class="orbitron text-3xl font-bold text-white text-center mb-8">🔑 Quên Mật Khẩu</h2>

                {{-- Thông báo thành công --}}
                @if (session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Thông báo lỗi --}}
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="inter text-white block mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email của bạn" required>
                    </div>
                    <button type="submit" class="glow-button w-full bg-gradient-to-r from-yellow-400 to-pink-500 text-white py-3 rounded-full font-semibold">Gửi liên kết đặt lại mật khẩu</button>
                </form>

                <div class="text-center mt-6">
                    <a href="{{ route('login') }}" class="inter text-blue-200 hover:text-white">Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
