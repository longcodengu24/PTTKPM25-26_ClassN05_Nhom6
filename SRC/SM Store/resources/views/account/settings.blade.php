@extends('layouts.account')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="orbitron text-3xl font-bold text-white mb-8 text-center drop-shadow">Cài Đặt Tài Khoản</h1>
    <form class="bg-white/30 backdrop-blur-lg rounded-2xl shadow-xl p-8 space-y-6 border border-white/20">
        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="name">Tên đăng nhập</label>
            <input type="text" id="name" name="name" value="Nguyễn Văn A" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none" />
        </div>
        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="avatar">Ảnh đại diện</label>
            <input type="file" id="avatar" name="avatar" class="w-full text-gray-700" />
        </div>
        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="password">Mật khẩu mới</label>
            <input type="password" id="password" name="password" placeholder="••••••••" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none" />
        </div>
        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="password_confirmation">Nhập lại mật khẩu mới</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none" />
        </div>
        <div class="flex justify-end gap-4 pt-4">
            <button type="reset" class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition">Hủy</button>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow">Lưu thay đổi</button>
        </div>
    </form>
</div>
@endsection
