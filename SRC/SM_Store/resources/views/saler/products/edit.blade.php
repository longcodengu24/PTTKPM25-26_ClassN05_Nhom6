@extends('layouts.seller')

@section('title', 'Chỉnh Sửa Sheet Nhạc')

@section('content')

<div id="products-edit" class="admin-content active px-6 pb-6">
    <div class="admin-card rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="orbitron text-2xl font-bold text-white">Chỉnh Sửa Sheet Nhạc</h3>
            <a href="{{ route('saler.products.index') }}" class="bg-gray-500 hover:bg-gray-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                ← Quay Lại
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4 mb-6">
                <p class="text-green-300 font-semibold">✅ {{ session('success') }}</p>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-500 bg-opacity-20 border border-red-500 rounded-lg p-4 mb-6">
                <h4 class="text-red-300 font-semibold mb-2">Có lỗi xảy ra:</h4>
                <ul class="text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Edit Form -->
        <form action="{{ route('saler.products.update', $product['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Thông tin hiện tại -->
            <div class="bg-white bg-opacity-5 rounded-lg p-6">
                <h4 class="text-white text-lg font-semibold mb-4">📄 Thông Tin Hiện Tại</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-blue-200 mb-4">
                    <div>
                        <strong>File hiện tại:</strong> {{ !empty($product['file_path']) ? basename($product['file_path']) : 'Chưa có file' }}
                    </div>
                    <div>
                        <strong>Tạo lúc:</strong> {{ $product['created_at'] ?? 'Chưa xác định' }}
                    </div>
                    <div>
                        <strong>Cập nhật lần cuối:</strong> {{ $product['updated_at'] ?? 'Chưa xác định' }}
                    </div>
                    <div>
                        <strong>Lượt tải:</strong> {{ $product['downloads_count'] ?? 0 }}
                    </div>
                </div>

                @if(!empty($product['image_path']))
                <div class="mb-4">
                    <strong class="text-blue-200">Ảnh cover hiện tại:</strong>
                    <div class="mt-2">
                        <img src="{{ asset($product['image_path']) }}" alt="Current cover" class="h-24 w-auto object-cover rounded-lg border border-white border-opacity-30">
                    </div>
                </div>
                @endif
            </div>

            <!-- Upload file mới (tùy chọn) -->
            <div class="bg-white bg-opacity-10 rounded-lg p-6 border-2 border-dashed border-yellow-500 border-opacity-50">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl">🔄</span>
                    </div>
                    <h4 class="text-white text-lg font-semibold mb-2">Thay Đổi File Sheet Nhạc (Tùy Chọn)</h4>
                    <p class="text-yellow-200 mb-4">Chỉ upload nếu muốn thay thế file hiện tại</p>
                    
                    <input type="file" name="music_file" id="music_file" accept=".txt,.json" 
                           class="hidden" onchange="showFileName(this)">
                    
                    <label for="music_file" class="bg-yellow-500 hover:bg-yellow-600 px-6 py-3 rounded-lg text-white inter font-semibold cursor-pointer inline-block">
                        Chọn File Mới
                    </label>
                    
                    <div id="file-info" class="mt-4 hidden">
                        <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-3">
                            <p class="text-green-300">
                                <span class="font-semibold">File mới đã chọn:</span>
                                <span id="file-name"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form chỉnh sửa thông tin -->
            <div class="bg-white bg-opacity-10 rounded-lg p-6 border border-white border-opacity-20">
                <h4 class="text-white text-lg font-semibold mb-6">✏️ Chỉnh Sửa Thông Tin Sản Phẩm</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tên bài -->
                    <div>
                        <label for="name" class="block text-blue-200 font-semibold mb-2">
                            🎵 Tên Bài Hát <span class="text-red-400">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               required
                               value="{{ old('name', $product['name'] ?? '') }}"
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập tên bài hát...">
                    </div>

                    <!-- Tác giả -->
                    <div>
                        <label for="author" class="block text-blue-200 font-semibold mb-2">
                            👤 Tác Giả
                        </label>
                        <input type="text" 
                               name="author" 
                               id="author"
                               value="{{ old('author', $product['author'] ?? '') }}"
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập tên tác giả...">
                    </div>

                    <!-- Người transcribe -->
                    <div>
                        <label for="transcribed_by" class="block text-blue-200 font-semibold mb-2">
                            🎹 Người Soạn/Transcribe
                        </label>
                        <input type="text" 
                               name="transcribed_by" 
                               id="transcribed_by"
                               value="{{ old('transcribed_by', $product['transcribed_by'] ?? '') }}"
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nhập tên người transcribe...">
                    </div>

                    <!-- Quốc gia/Vùng miền -->
                    <div>
                        <label class="block text-blue-200 font-semibold mb-2">
                            🌍 Quốc Gia/Vùng Miền
                        </label>
                        <div class="relative" x-data="{ 
                            open: false, 
                            selected: getCountryWithFlag('{{ old('country_region', $product['country_region'] ?? 'Việt Nam') }}'), 
                            options: ['🇻🇳 Việt Nam', '🇰🇷 Hàn Quốc', '🇯🇵 Nhật Bản', '🇨🇳 Trung Quốc', '🇺🇸 Âu Mỹ', '🌏 Khác'],
                            get value() {
                                return this.selected.replace(/🇻🇳 |🇰🇷 |🇯🇵 |🇨🇳 |🇺🇸 |🌏 /g, '');
                            }
                        }">
                            <button type="button" @click="open = !open" class="w-full px-4 py-3 bg-white bg-opacity-20 text-white rounded-lg border border-white border-opacity-30 flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <span x-text="selected"></span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-full bg-slate-800 text-white rounded-lg shadow-lg z-20">
                                <template x-for="option in options" :key="option">
                                    <div @click="selected = option; open = false" class="px-4 py-3 hover:bg-blue-600 cursor-pointer" x-text="option"></div>
                                </template>
                            </div>
                            <!-- Hidden input để gửi giá trị -->
                            <input type="hidden" name="country_region" id="country_region" x-model="value">
                        </div>
                    </div>

                    <!-- Giá -->
                    <div>
                        <label for="price" class="block text-blue-200 font-semibold mb-2">
                            💰 Giá Bán (VNĐ)
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               min="0" 
                               step="1000"
                               value="{{ old('price', $product['price'] ?? 0) }}"
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="30000">
                    </div>

                    <!-- YouTube Demo URL -->
                    <div>
                        <label for="youtube_url" class="block text-blue-200 font-semibold mb-2">
                            📺 YouTube Demo URL (tùy chọn)
                        </label>
                        <input type="url" 
                               name="youtube_url" 
                               id="youtube_url"
                               value="{{ old('youtube_url', $product['youtube_demo_url'] ?? '') }}"
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <!-- Cover Image Upload -->
                    <div>
                        <label for="cover_image" class="block text-blue-200 font-semibold mb-2">
                            🖼️ Ảnh Cover Mới (tùy chọn)
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   name="cover_image" 
                                   id="cover_image"
                                   accept="image/*"
                                   class="hidden" 
                                   onchange="previewImage(this)">
                            
                            <label for="cover_image" class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent cursor-pointer inline-flex items-center justify-center border-2 border-dashed hover:bg-white hover:bg-opacity-10 transition-colors">
                                <span class="text-blue-200">
                                    📁 Chọn ảnh cover mới
                                </span>
                            </label>
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="image-preview" class="hidden mt-3">
                            <div class="relative inline-block">
                                <img id="preview-img" src="" alt="Preview" class="max-w-full h-32 object-cover rounded-lg border border-white border-opacity-30">
                                <button type="button" onclick="removeImage()" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">
                                    ✕
                                </button>
                            </div>
                            <p class="text-green-300 text-sm mt-1">
                                ✅ Ảnh mới đã được chọn: <span id="image-name"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Trạng thái sản phẩm -->
                    <div>
                        <!-- Spacer để align với upload area của cover image -->
                        <div class="mb-8"></div>
                        <!-- Hidden input để đảm bảo luôn có giá trị khi checkbox không được check -->
                        <input type="hidden" name="is_active" value="0">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   id="is_active"
                                   value="1"
                                   {{ old('is_active', $product['is_active'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-blue-200">
                                Kích hoạt sản phẩm (hiển thị trên shop)
                            </span>
                        </label>
                        <p class="text-blue-300 text-sm mt-1">Nếu không check, sản phẩm sẽ ở trạng thái ẩn</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center space-x-4">
                <a href="{{ route('saler.products.index') }}" class="bg-gray-500 hover:bg-gray-600 px-8 py-3 rounded-lg text-white inter font-semibold text-lg">
                    ← Hủy bỏ
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 px-8 py-3 rounded-lg text-white inter font-semibold text-lg">
                    💾 Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showFileName(input) {
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    
    if (input.files && input.files[0]) {
        fileName.textContent = input.files[0].name;
        fileInfo.classList.remove('hidden');
    } else {
        fileInfo.classList.add('hidden');
    }
}

function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const imageName = document.getElementById('image-name');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Vui lòng chọn file ảnh!');
            input.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước ảnh quá lớn! Vui lòng chọn ảnh dưới 2MB.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imageName.textContent = file.name;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}

function removeImage() {
    const input = document.getElementById('cover_image');
    const preview = document.getElementById('image-preview');
    
    input.value = '';
    preview.classList.add('hidden');
}

function getCountryWithFlag(country) {
    const countryMap = {
        'Việt Nam': '🇻🇳 Việt Nam',
        'Hàn Quốc': '🇰🇷 Hàn Quốc', 
        'Nhật Bản': '🇯🇵 Nhật Bản',
        'Trung Quốc': '🇨🇳 Trung Quốc',
        'Âu Mỹ': '🇺🇸 Âu Mỹ',
        'Khác': '🌏 Khác'
    };
    return countryMap[country] || '🇻🇳 Việt Nam';
}
</script>

<!-- Thêm Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@endsection
