<?php $__env->startSection('title', 'Thêm Sheet Nhạc Mới'); ?>

<?php $__env->startSection('content'); ?>

<div id="products-create" class="admin-content active px-6 pb-6">
    <div class="admin-card rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="orbitron text-2xl font-bold text-white">Thêm Sheet Nhạc Mới</h3>
            <a href="<?php echo e(route('saler.products.index')); ?>" class="bg-gray-500 hover:bg-gray-600 px-6 py-3 rounded-lg text-white inter font-semibold">
                ← Quay Lại
            </a>
        </div>

        <!-- Upload Form -->
        <form action="<?php echo e(route('saler.products.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php echo csrf_field(); ?>
            <!-- File Upload Section -->
            <div class="bg-white bg-opacity-10 rounded-lg p-6 border-2 border-dashed border-white border-opacity-30">
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mb-4">
                        <span class="text-2xl">📁</span>
                    </div>
                    <h4 class="text-white text-lg font-semibold mb-2">Upload File Sheet Nhạc</h4>
                    <p class="text-blue-200 mb-4">Chọn file .txt hoặc .json chứa thông tin bản nhạc</p>
                    
                    <input type="file" name="music_file" id="music_file" accept=".txt,.json" 
                           class="hidden" onchange="showFileName(this)">
                    
                    <label for="music_file" class="bg-blue-500 hover:bg-blue-600 px-6 py-3 rounded-lg text-white inter font-semibold cursor-pointer inline-block">
                        Chọn File
                    </label>
                    
                    <div id="file-info" class="mt-4 hidden">
                        <div class="bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-3">
                            <p class="text-green-300">
                                <span class="font-semibold">File đã chọn:</span>
                                <span id="file-name"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin mặc định -->
            <div class="bg-white bg-opacity-5 rounded-lg p-6">
                <h4 class="text-white text-lg font-semibold mb-4">ℹ️ Thông Tin Sẽ Được Tự Động Tạo</h4>
                
                <!-- Preview thông tin từ file -->
                <div id="file-preview" class="hidden bg-green-500 bg-opacity-20 border border-green-500 rounded-lg p-4 mb-4">
                    <h5 class="text-green-300 font-semibold mb-3">🎵 Thông tin từ file đã upload:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-green-200">
                        <div>
                            <strong>Tên bài:</strong> <span id="preview-name">-</span>
                        </div>
                        <div>
                            <strong>Tác giả:</strong> <span id="preview-author">-</span>
                        </div>
                        <div>
                            <strong>Người soạn:</strong> <span id="preview-transcriber">-</span>
                        </div>
                        <div>
                            <strong>Tên file:</strong> <span id="preview-filename">-</span>
                        </div>
                        <div>
                            <strong>Kích thước:</strong> <span id="preview-filesize">-</span>
                        </div>
                        <div>
                            <strong>Trạng thái:</strong> <span class="text-green-400">✅ Phân tích thành công</span>
                        </div>
                    </div>
                </div>

                <!-- Loading state -->
                <div id="file-loading" class="hidden bg-blue-500 bg-opacity-20 border border-blue-500 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-300 mr-3"></div>
                        <p class="text-blue-300">Đang phân tích file...</p>
                    </div>
                </div>

                <!-- Error state -->
                <div id="file-error" class="hidden bg-red-500 bg-opacity-20 border border-red-500 rounded-lg p-4 mb-4">
                    <h5 class="text-red-300 font-semibold mb-2">❌ Lỗi phân tích file:</h5>
                    <p class="text-red-200" id="error-message">-</p>
                </div>
                
                <!-- JSON Format Support -->
                <div class="bg-blue-500 bg-opacity-20 border border-blue-500 rounded-lg p-4 mb-4">
                    <h5 class="text-blue-300 font-semibold mb-2">📄 Hỗ trợ định dạng JSON:</h5>
                    <code class="text-blue-200 text-sm block">
                        [{"name":"Thần thoại","author":"Unknown","transcribedBy":"Piano Master",...}]
                    </code>
                    <p class="text-blue-300 text-sm mt-2">Tự động lấy: name, author, transcribedBy từ JSON</p>
                </div>

            </div>

            <!-- Form chỉnh sửa thông tin -->
            <div id="edit-form" class="hidden bg-white bg-opacity-10 rounded-lg p-6 border border-white border-opacity-20">
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
                            selected: '🇻🇳 Việt Nam', 
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
                               value="30000"
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
                               class="w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <!-- Cover Image Upload -->
                    <div>
                        <label for="cover_image" class="block text-blue-200 font-semibold mb-2">
                            🖼️ Ảnh Cover (tùy chọn)
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
                                    📁 Chọn ảnh cover (JPG, PNG, GIF, WebP)
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
                                ✅ Ảnh đã được chọn: <span id="image-name"></span>
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
                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-blue-200">
                                Kích hoạt sản phẩm ngay (hiển thị trên shop)
                            </span>
                        </label>
                        <p class="text-blue-300 text-sm mt-1">Nếu không check, sản phẩm sẽ ở trạng thái ẩn</p>
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            <?php if($errors->any()): ?>
                <div class="bg-red-500 bg-opacity-20 border border-red-500 rounded-lg p-4">
                    <h4 class="text-red-300 font-semibold mb-2">Có lỗi xảy ra:</h4>
                    <ul class="text-red-200">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>• <?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" class="bg-green-500 hover:bg-green-600 px-8 py-3 rounded-lg text-white inter font-semibold text-lg">
                    🎵 Tạo Sheet Nhạc
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
        
        // Gọi API để preview file
        previewFile(input.files[0]);
    } else {
        fileInfo.classList.add('hidden');
        hideAllPreview();
    }
}

function previewFile(file) {
    // Hiển thị loading state
    showLoading();
    
    const formData = new FormData();
    formData.append('file', file);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');
    
    fetch('<?php echo e(route("saler.products.preview-file")); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            showPreview(data.data);
        } else {
            showError(data.error || 'Có lỗi xảy ra khi phân tích file');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showError('Không thể kết nối đến server. Vui lòng thử lại.');
    });
}

function showLoading() {
    hideAllPreview();
    document.getElementById('file-loading').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('file-loading').classList.add('hidden');
}

function showPreview(data) {
    hideAllPreview();
    
    document.getElementById('preview-name').textContent = data.name || 'Không xác định';
    document.getElementById('preview-author').textContent = data.author || 'Chưa xác định';
    document.getElementById('preview-transcriber').textContent = data.transcribed_by || 'Admin';
    document.getElementById('preview-filename').textContent = data.file_name || '-';
    document.getElementById('preview-filesize').textContent = data.file_size || '-';
    
    // Hiển thị preview section
    document.getElementById('file-preview').classList.remove('hidden');
    
    // Auto-fill form với thông tin đã parse được
    autoFillForm(data);
    
    // Hiển thị form chỉnh sửa
    document.getElementById('edit-form').classList.remove('hidden');
    
    // Scroll xuống form một cách mượt mà
    setTimeout(() => {
        document.getElementById('edit-form').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }, 300);
}

function autoFillForm(data) {
    // Fill các field với dữ liệu đã parse
    document.getElementById('name').value = data.name || '';
    document.getElementById('author').value = data.author || '';
    document.getElementById('transcribed_by').value = data.transcribed_by || '';
    
    // Detect quốc gia từ tên bài hát hoặc tác giả
    const detectedCountry = detectCountry(data);
    if (detectedCountry) {
        // Tìm element có Alpine.js và cập nhật giá trị
        const countryDropdown = document.querySelector('[x-data*="selected"]');
        if (countryDropdown && countryDropdown._x_dataStack) {
            // Cập nhật selected value trong Alpine.js
            countryDropdown._x_dataStack[0].selected = detectedCountry;
        }
    }
    
    // Thêm hiệu ứng highlight cho các field đã được fill
    highlightFilledFields();
}

function detectCountry(data) {
    const name = (data.name || '').toLowerCase();
    const author = (data.author || '').toLowerCase();
    const text = name + ' ' + author;
    
    
    // Detect Korean
    if (/[ㄱ-ㅎ가-힣]/.test(text) || 
        /\b(korea|korean|한국|kpop|k-pop)\b/.test(text)) {
        return '🇰🇷 Hàn Quốc';
    }
    
    // Detect Japanese  
    if (/[ひらがなカタカナ一-龯]/.test(text) ||
        /\b(japan|japanese|日本|jpop|j-pop)\b/.test(text)) {
        return '🇯🇵 Nhật Bản';
    }
    
    // Detect Chinese
    if (/[一-龯]/.test(text) && !/[ひらがなカタカナ]/.test(text) ||
        /\b(china|chinese|中国|cpop|c-pop)\b/.test(text)) {
        return '🇨🇳 Trung Quốc';
    }

        // Detect Vietnamese
    if (/[àáạảãăắằẳẵặâấầẩẫậđèéẹẻẽêếềểễệìíịỉĩòóọỏõôốồổỗộơớờởỡợùúụủũưứừửữự]/.test(text) ||
        /\b(việt nam|vietnam|tiếng việt)\b/.test(text)) {
        return '🇻🇳 Việt Nam';
    }
    
    return null; // Giữ nguyên default (Việt Nam)
}

function highlightFilledFields() {
    const fields = ['name', 'author', 'transcribed_by'];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.value.trim()) {
            // Thêm hiệu ứng glow tạm thời
            field.style.boxShadow = '0 0 10px rgba(34, 197, 94, 0.5)';
            field.style.borderColor = 'rgba(34, 197, 94, 0.8)';
            
            // Xóa hiệu ứng sau 2 giây
            setTimeout(() => {
                field.style.boxShadow = '';
                field.style.borderColor = '';
            }, 2000);
        }
    });
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

function showError(message) {
    hideAllPreview();
    document.getElementById('error-message').textContent = message;
    document.getElementById('file-error').classList.remove('hidden');
}

function hideAllPreview() {
    document.getElementById('file-preview').classList.add('hidden');
    document.getElementById('file-loading').classList.add('hidden');
    document.getElementById('file-error').classList.add('hidden');
    document.getElementById('edit-form').classList.add('hidden');
}
</script>

<!-- Thêm Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/saler/products/create.blade.php ENDPATH**/ ?>