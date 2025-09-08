@extends('layouts.app')

@section('title', 'Đăng ký')

@section('content')
{{-- <div id="register" class="page-content">
    <section class="relative z-10 py-20 px-6">
        <div class="max-w-md mx-auto">
            <div class="game-card rounded-xl p-8">
                <h2 class="orbitron text-3xl font-bold text-white text-center mb-8">📝 Đăng Ký</h2>
                @if ($errors->any())
                    <div class="bg-red-500 text-white p-4 rounded-lg mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif                
                
                <form action="{{ route('register') }}" method="POST" class="space-y-6">
                    @csrf <!-- Bảo vệ chống tấn công CSRF -->
                    <div>
                        <label class="inter text-white block mb-2">Tên đăng nhập</label>
                        <input type="text" name="name" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập họ và tên" required>
                    </div>
                    <div>
                        <label class="inter text-white block mb-2">Email</label>
                        <input type="email" name="email" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email của bạn" required>
                    </div>
                    <div>
                        <label class="inter text-white block mb-2">Mật khẩu</label>
                        <input type="password" name="password" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Tạo mật khẩu" required>
                    </div>
                    <div>
                        <label class="inter text-white block mb-2">Nhập lại mật khẩu</label>
                        <input type="password" name="password_confirmation" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập lại mật khẩu" required>
                    </div>
                    <button type="submit" class="glow-button w-full bg-gradient-to-r from-green-400 to-blue-500 text-white py-3 rounded-full font-semibold">Đăng Ký</button>
                </form>
                <div class="text-center mt-6">
                    <p class="inter text-blue-200">Đã có tài khoản? <a href="{{ url('/login') }}" class="text-yellow-300 hover:text-yellow-200">Đăng nhập</a></p>
                </div>
            </div>
        </div>
    </section>
</div> --}}
@endsection