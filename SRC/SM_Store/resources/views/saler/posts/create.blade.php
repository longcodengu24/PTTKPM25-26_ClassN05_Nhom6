@extends('layouts.seller')

@section('title', 'Tạo Bài Viết Mới - Saler Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('saler.posts') }}" class="text-gray-300 hover:text-white">
                <span class="text-xl">←</span>
            </a>
            <h2 class="orbitron text-2xl font-bold text-white">✍️ Tạo Bài Viết Mới</h2>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" class="bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded-lg text-white inter">
                Lưu nháp
            </button>
            <button type="submit" form="post-form" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter">
                Đăng bài
            </button>
        </div>
    </div>

    <form id="post-form" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 orbitron">Nội dung bài viết</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Tiêu đề bài viết</label>
                            <input type="text" name="title" required
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nhập tiêu đề hấp dẫn cho bài viết...">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Mô tả ngắn</label>
                            <textarea name="excerpt" rows="3"
                                      class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Tóm tắt ngắn gọn về nội dung bài viết..."></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Nội dung chi tiết</label>
                            <div class="bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg">
                                <!-- Toolbar -->
                                <div class="flex items-center space-x-2 p-3 border-b border-gray-600">
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        <strong>B</strong>
                                    </button>
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        <em>I</em>
                                    </button>
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        <u>U</u>
                                    </button>
                                    <div class="w-px h-6 bg-gray-600"></div>
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        📷
                                    </button>
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        🔗
                                    </button>
                                    <button type="button" class="p-2 text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10 rounded">
                                        📋
                                    </button>
                                </div>
                                
                                <!-- Editor -->
                                <textarea name="content" rows="12"
                                          class="w-full px-4 py-3 bg-transparent text-white focus:outline-none resize-none"
                                          placeholder="Viết nội dung bài viết của bạn tại đây...

Một số gợi ý:
• Chia sẻ kinh nghiệm chơi nhạc
• Hướng dẫn kỹ thuật
• Review sheet nhạc mới
• Tin tức âm nhạc
• Mẹo luyện tập hiệu quả"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 orbitron">Tối ưu SEO</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Từ khóa chính</label>
                            <input type="text" name="main_keyword"
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ví dụ: học piano, sheet nhạc classical">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Tags</label>
                            <input type="text" name="tags"
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="piano, classical, beginner, tutorial (phân cách bằng dấu phẩy)">
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">URL thân thiện SEO</label>
                            <div class="flex">
                                <span class="px-3 py-3 bg-gray-600 text-gray-300 rounded-l-lg text-sm">skymusic.com/posts/</span>
                                <input type="text" name="slug"
                                       class="flex-1 px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-r-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="cach-choi-piano-co-ban">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publishing Options -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 orbitron">Tùy chọn đăng</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Danh mục</label>
                            <select name="category" required
                                    class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Chọn danh mục</option>
                                <option value="tutorial">Hướng dẫn</option>
                                <option value="review">Review nhạc</option>
                                <option value="news">Tin tức</option>
                                <option value="tips">Mẹo hay</option>
                                <option value="interview">Phỏng vấn</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-gray-300 text-sm font-medium mb-2">Trạng thái</label>
                            <select name="status"
                                    class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="draft">Nháp</option>
                                <option value="published">Đăng ngay</option>
                                <option value="scheduled">Đăng theo lịch</option>
                            </select>
                        </div>
                        
                        <div class="hidden" id="schedule-section">
                            <label class="block text-gray-300 text-sm font-medium mb-2">Thời gian đăng</label>
                            <input type="datetime-local" name="scheduled_at"
                                   class="w-full px-4 py-3 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="featured" id="featured" class="rounded">
                                <label for="featured" class="text-gray-300 text-sm">Bài viết nổi bật</label>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="allow_comments" id="allow_comments" class="rounded" checked>
                                <label for="allow_comments" class="text-gray-300 text-sm">Cho phép bình luận</label>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="send_notification" id="send_notification" class="rounded">
                                <label for="send_notification" class="text-gray-300 text-sm">Gửi thông báo đến followers</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 orbitron">Ảnh đại diện</h3>
                    
                    <div class="border-2 border-dashed border-gray-600 rounded-lg p-6 text-center">
                        <div class="text-4xl mb-3">🖼️</div>
                        <h4 class="text-white font-medium mb-2">Ảnh bìa bài viết</h4>
                        <p class="text-gray-300 text-sm mb-4">Kích thước khuyến nghị: 1200x630px</p>
                        <input type="file" name="featured_image" accept="image/*" class="hidden" id="featured-upload">
                        <label for="featured-upload" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter cursor-pointer">
                            Chọn ảnh
                        </label>
                    </div>
                </div>

                <!-- Related Products -->
                <div class="admin-card rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 orbitron">Sheet nhạc liên quan</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="related_products[]" value="sheet1" id="sheet1" class="rounded">
                            <label for="sheet1" class="text-gray-300 text-sm">Canon in D</label>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="related_products[]" value="sheet2" id="sheet2" class="rounded">
                            <label for="sheet2" class="text-gray-300 text-sm">Für Elise</label>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" name="related_products[]" value="sheet3" id="sheet3" class="rounded">
                            <label for="sheet3" class="text-gray-300 text-sm">Moonlight Sonata</label>
                        </div>
                    </div>
                    
                    <button type="button" class="w-full mt-3 bg-gray-500 hover:bg-gray-600 px-4 py-2 rounded-lg text-white inter text-sm">
                        Chọn thêm sheet
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const scheduleSection = document.getElementById('schedule-section');
    if (this.value === 'scheduled') {
        scheduleSection.classList.remove('hidden');
    } else {
        scheduleSection.classList.add('hidden');
    }
});

// Auto-generate slug from title
document.querySelector('input[name="title"]').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    document.querySelector('input[name="slug"]').value = slug;
});
</script>
@endsection