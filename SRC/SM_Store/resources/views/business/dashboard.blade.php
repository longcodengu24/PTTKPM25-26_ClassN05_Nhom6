@extends('layouts.admin') {{-- hoặc tạo layout riêng cho business nếu bạn muốn --}}

@section('title', 'Business Dashboard')

@section('content')
<div class="admin-card m-6 rounded-xl p-6">
    <h2 class="orbitron text-2xl font-bold text-white">🏢 Business Dashboard</h2>
    <p class="inter text-gray-300 mt-2">
        Xin chào {{ session('name') ?? 'Business' }} — bạn đang đăng nhập với vai trò <b>{{ session('role') }}</b>.
    </p>
</div>
@endsection
