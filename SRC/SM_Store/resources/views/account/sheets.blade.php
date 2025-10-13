@extends('layouts.account')
@section('content')
<div class="profile-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="orbitron text-xl font-bold text-white">
            Sheet Nhạc Của Tôi ({{ (isset($totalUserProducts) ? $totalUserProducts : 0) + (isset($totalPurchasedProducts) ? $totalPurchasedProducts : 0) }})
            @if(config('app.debug'))
                <small class="text-xs text-blue-200 block">
                    Debug: UID = {{ session('firebase_uid', 'NULL') }}, 
                    Own Products = {{ isset($userProducts) ? $userProducts->count() : 'NULL' }},
                    Purchased = {{ isset($purchasedProducts) ? $purchasedProducts->count() : 'NULL' }}
                </small>
            @endif
        </h3>
        <a href="{{ route('saler.products.create') }}" class="glow-button bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inter font-semibold">
            + Tạo Product Mới
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead>
                <tr class="text-white/80 border-b border-white/20">
                    <th class="py-3 px-4 font-semibold">Tên</th>
                    <th class="py-3 px-4 font-semibold">Người Soạn</th>
                    <th class="py-3 px-4 font-semibold">Danh Mục</th>
                    <th class="py-3 px-4 font-semibold">Giá</th>
                    <th class="py-3 px-4 font-semibold">Lượt Mua</th>
                    <th class="py-3 px-4 font-semibold">Trạng Thái</th>
                    <th class="py-3 px-4 font-semibold">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <!-- User's own products -->
                @if(isset($userProducts) && $userProducts->count() > 0)
                    @foreach($userProducts as $product)
                    <tr class="bg-white/10 hover:bg-white/20 transition rounded-xl">
                        <td class="py-4 px-4">
                            <div>
                                <div class="orbitron font-bold text-white leading-tight">{{ $product['name'] ?? 'Chưa có tên' }}</div>
                                <div class="inter text-xs text-blue-100">{{ $product['author'] ?? 'Chưa xác định' }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-white">{{ $product['transcribed_by'] ?? 'Chưa xác định' }}</td>
                        <td class="py-4 px-4 text-white">{{ $product['country_region'] ?? 'Chưa phân loại' }}</td>
                        <td class="py-4 px-4 text-white orbitron font-semibold">{{ number_format($product['price'] ?? 0) }}đ</td>
                        <td class="py-4 px-4 text-white">{{ $product['sold_count'] ?? 0 }}</td>
                        <td class="py-4 px-4">
                            <span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Sản phẩm của tôi</span>
                        </td>
                        <td class="py-4 px-4 flex gap-2">
                            <a href="{{ route('account.download', $product['id']) }}" 
                               class="px-4 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white font-semibold shadow inline-block text-center">
                                Tải
                            </a>
                            <a href="{{ route('saler.products.edit', $product['id']) }}" 
                               class="px-4 py-1 rounded bg-yellow-500 hover:bg-yellow-600 text-white font-semibold shadow inline-block text-center">
                                Sửa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endif

                <!-- Purchased products -->
                @if(isset($purchasedProducts) && $purchasedProducts->count() > 0)
                    @foreach($purchasedProducts as $product)
                    <tr class="bg-white/5 hover:bg-white/20 transition rounded-xl">
                        <td class="py-4 px-4">
                            <div>
                                <div class="orbitron font-bold text-white leading-tight">{{ $product['product_name'] ?? 'Chưa có tên' }}</div>
                                <div class="inter text-xs text-blue-100">{{ $product['author'] ?? 'Chưa xác định' }}</div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-white">Đã mua</td>
                        <td class="py-4 px-4 text-white">Music</td>
                        <td class="py-4 px-4 text-white orbitron font-semibold">{{ number_format($product['price'] ?? 0) }}đ</td>
                        <td class="py-4 px-4 text-white">-</td>
                        <td class="py-4 px-4">
                            <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Đã mua</span>
                        </td>
                        <td class="py-4 px-4 flex gap-2">
                            <a href="{{ route('account.download', $product['product_id']) }}" 
                               class="px-4 py-1 rounded bg-green-500 hover:bg-green-600 text-white font-semibold shadow inline-block text-center">
                                Tải về
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <!-- Debug message when no products found -->
                    <tr class="bg-white/5">
                        <td colspan="7" class="py-4 px-4 text-center text-white/60">
                            @if(isset($userProducts))
                                <div class="text-sm">
                                    Không tìm thấy sheet nhạc nào của bạn
                                    @if(config('app.debug'))
                                        <br><small>Debug: UID = {{ session('firebase_uid', 'NULL') }}</small>
                                        <br><small>Total products in DB: {{ isset($userProducts) ? 'Loaded' : 'Not loaded' }}</small>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm">Chưa load được dữ liệu sản phẩm</div>
                            @endif
                        </td>
                    </tr>
                @endif
                
                @if(config('app.debug'))
                <!-- Temporary debug section to show all products structure -->
                <tr class="bg-red-900/20">
                    <td colspan="7" class="py-4 px-4">
                        <details class="text-white text-xs">
                            <summary class="cursor-pointer">🐛 Debug: Xem tất cả sản phẩm (click để mở)</summary>
                            <div class="mt-2 max-h-40 overflow-y-auto bg-black/30 p-2 rounded">
                                <pre>{{ print_r(isset($userProducts) ? $userProducts->take(3)->toArray() : 'No products', true) }}</pre>
                            </div>
                        </details>
                    </td>
                </tr>
                @endif
       
            </tbody>
        </table>
    </div>
</div>
@endsection

