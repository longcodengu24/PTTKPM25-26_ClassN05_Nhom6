@extends('layouts.account')
@section('content')
<div class="profile-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="orbitron text-xl font-bold text-white">
            Sheet Nhạc Đã Mua ({{ $totalPurchasedProducts ?? 0 }})
            @if(config('app.debug'))
                <small class="text-xs text-blue-200 block">
                    UID: {{ session('firebase_uid', 'NULL') }}
                </small>
            @endif
        </h3>
        <a href="{{ route('saler.products.create') }}" 
           class="glow-button bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inter font-semibold">
            + Tạo Product Mới
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left border-separate border-spacing-y-2">
            <thead>
                <tr class="bg-white/10 text-white/90 uppercase text-xs tracking-wider">
                    <th class="py-3 px-4 rounded-l-xl font-semibold">Tên Sheet</th>
                    <th class="py-3 px-4 font-semibold">Người Soạn</th>
                    <th class="py-3 px-4 font-semibold">Danh Mục</th>
                    <th class="py-3 px-4 font-semibold">Giá</th>
                    <th class="py-3 px-4 rounded-r-xl font-semibold text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($purchasedProducts) && $purchasedProducts->count() > 0)
                    @foreach($purchasedProducts as $product)
                    <tr class="bg-white/10 hover:bg-white/20 transition-all duration-300">
                        <td class="py-3 px-4 text-white">
                            <div class="orbitron font-bold leading-tight">{{ $product['title'] ?? 'Chưa có tên' }}</div>
                            <div class="inter text-xs text-blue-100 mt-1">{{ $product['description'] ?? '' }}</div>
                        </td>
                        <td class="py-3 px-4 text-white">
                            {{ $product['seller_name'] ?? 'Không rõ người soạn' }}
                        </td>
                        <td class="py-3 px-4 text-white">
                            {{ $product['category'] ?? 'Chưa phân loại' }}
                        </td>
                        <td class="py-3 px-4 text-white orbitron font-semibold">
                            {{ number_format($product['price'] ?? 0) }}đ
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ asset($product['file_url'] ?? '#') }}" 
                               target="_blank"
                               class="px-4 py-1 rounded bg-green-500 hover:bg-green-600 text-white font-semibold shadow inline-block text-center transition">
                                Tải về
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="py-5 text-center text-white/60">
                            Bạn chưa mua sheet nhạc nào.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
