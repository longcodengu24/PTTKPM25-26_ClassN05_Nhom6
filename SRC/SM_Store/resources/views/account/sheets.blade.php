@extends('layouts.account')
@section('content')
<div class="profile-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="orbitron text-xl font-bold text-white">
            Sheet Nhạc Đã Mua ({{ $purchasedProducts->count() ?? 0 }})
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
                    <th class="py-3 px-4 font-semibold">Trạng Thái</th>
                    <th class="py-3 px-4 font-semibold">Giá</th>
                    <th class="py-3 px-4 font-semibold">Đánh Giá</th>
                    <th class="py-3 px-4 rounded-r-xl font-semibold text-center">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($purchasedProducts) && $purchasedProducts->count() > 0)
                    @foreach($purchasedProducts as $product)
                    <tr class="bg-white/10 hover:bg-white/20 transition-all duration-300">
                        <td class="py-3 px-4 text-white">
                            <div class="orbitron font-bold leading-tight">{{ $product['name'] ?? 'Chưa có tên' }}</div>
                            <div class="inter text-xs text-blue-100 mt-1">ID: {{ $product['product_id'] ?? 'N/A' }}</div>
                            @if(!empty($product['category']))
                                <div class="inter text-xs text-purple-200 mt-1">Thể loại: {{ $product['category'] }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-white">
                            {{ $product['author'] ?? 'Không rõ người soạn' }}
                        </td>
                        <td class="py-3 px-4 text-white">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                @if(($product['status'] ?? '') === 'active') bg-green-500 text-white
                                @elseif(($product['status'] ?? '') === 'completed') bg-green-500 text-white
                                @elseif(($product['status'] ?? '') === 'pending') bg-yellow-500 text-white
                                @else bg-gray-500 text-white @endif">
                                {{ ucfirst($product['status'] ?? 'Chưa xác định') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-white orbitron font-semibold">
                            {{ number_format($product['price'] ?? 0) }}đ
                        </td>
                        <td class="py-3 px-4 text-white text-center">
                            @if(($product['rating'] ?? 0) > 0)
                                <div class="flex items-center justify-center space-x-1">
                                    <span class="text-yellow-300">⭐</span>
                                    <span class="font-semibold">{{ $product['rating'] }}/5</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">Chưa đánh giá</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex flex-col space-y-2">
                                @if(!empty($product['file_path']))
                                    <button onclick="downloadSheet('{{ $product['id'] }}', '{{ basename($product['file_path']) }}')" 
                                            class="px-3 py-1 rounded bg-green-500 hover:bg-green-600 text-white font-semibold shadow text-sm transition-all duration-200 hover:shadow-lg hover:scale-105"
                                            title="Tải về file: {{ basename($product['file_path']) }}"
                                            id="download-btn-{{ $product['id'] }}">
                                        <span class="download-text">📥 Tải về</span>
                                        <span class="download-loading hidden">⏳ Đang tải...</span>
                                    </button>
                                    @if(config('app.debug'))
                                        <div class="text-xs text-gray-400 mt-1">
                                            ID: {{ $product['id'] }}<br>
                                            Path: {{ $product['file_path'] }}<br>
                                            Author: {{ $product['author'] }}<br>
                                            Status: {{ $product['status'] }}<br>
                                            <span class="text-green-300">✅ File có sẵn để download</span>
                                        </div>
                                    @endif
                                @else
                                    <span class="px-3 py-1 rounded bg-gray-500 text-white font-semibold shadow text-sm">
                                        ❌ Không có file
                                    </span>
                                    @if(config('app.debug'))
                                        <div class="text-xs text-red-300 mt-1">
                                            File path trống hoặc không tồn tại
                                        </div>
                                    @endif
                                @endif
                                
                                
                                <div class="text-xs text-gray-300">
                                    Mua: {{ \Carbon\Carbon::parse($product['purchased_at'])->format('d/m/Y H:i') ?? 'N/A' }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="py-5 text-center text-white/60">
                            Bạn chưa mua sheet nhạc nào.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

<script>
function downloadSheet(sheetId, fileName) {
    const button = document.getElementById('download-btn-' + sheetId);
    const textSpan = button.querySelector('.download-text');
    const loadingSpan = button.querySelector('.download-loading');
    
    // Show loading state
    textSpan.classList.add('hidden');
    loadingSpan.classList.remove('hidden');
    button.disabled = true;
    
    // Check if user is logged in
    const firebaseUid = '{{ session("firebase_uid") }}';
    
    if (!firebaseUid || firebaseUid === '') {
        // User not logged in
        alert('Vui lòng đăng nhập để tải file!');
        resetButton();
        return;
    }
    
    // Create download link
    const downloadUrl = '{{ route("account.download", ":id") }}'.replace(':id', sheetId);
    
    // Create hidden link and trigger download
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = fileName;
    link.style.display = 'none';
    document.body.appendChild(link);
    
    // Handle download success/error
    link.onclick = function(e) {
        // Let the browser handle the download
        setTimeout(() => {
            resetButton();
            document.body.removeChild(link);
        }, 1000);
    };
    
    // Handle download error
    link.onerror = function() {
        alert('Có lỗi khi tải file. Vui lòng thử lại!');
        resetButton();
        document.body.removeChild(link);
    };
    
    // Trigger download
    link.click();
    
    function resetButton() {
        textSpan.classList.remove('hidden');
        loadingSpan.classList.add('hidden');
        button.disabled = false;
    }
}
</script>