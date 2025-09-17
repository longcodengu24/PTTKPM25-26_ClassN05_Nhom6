@extends('layouts.admin')

@section('title', 'Đăng Bài Viết Mới')

@section('content')
<div class="admin-content active px-6 pb-6 flex justify-center">
    <div class="admin-card rounded-xl p-8 w-full max-w-2xl">
        <h2 class="orbitron text-2xl font-bold text-white mb-6">Đăng Bài Viết Mới</h2>
        <form enctype="multipart/form-data" method="POST" action="{{ route('admin.posts.store') }}">
            @csrf
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Tiêu đề</label>
                <input type="text" name="title" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Nhập tiêu đề bài viết">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Tóm tắt</label>
                <textarea name="summary" rows="3" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Nhập tóm tắt ngắn gọn về bài viết..."></textarea>
            </div>
                        <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Trạng thái bài viết</label>
                <div class="relative" x-data="{ open: false, selected: 'Hiển thị', options: ['Hiển thị', 'Ẩn'] }">
                    <input type="hidden" name="status" :value="selected === 'Hiển thị' ? 'visible' : 'hidden'">
                    <button type="button" @click="open = !open" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 flex items-center w-full justify-between">
                        <span x-text="selected"></span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-full bg-slate-800 text-white rounded-lg shadow-lg z-20">
                        <template x-for="option in options" :key="option">
                            <div @click="selected = option; open = false" class="px-4 py-2 hover:bg-blue-600 cursor-pointer" x-text="option"></div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Nội dung bài viết</label>
                <textarea id="tinymce-content" name="content" rows="12" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300 indent-8" placeholder="Nhập nội dung bài viết..."></textarea>
            </div>

            <script src="https://cdn.tiny.cloud/1/d6cmgegn6w1tkui2ug7wwk6rssuojl4wuyilfdeoxloq9t2f/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
            <script>
                tinymce.init({
                    selector: '#tinymce-content',
                    plugins: 'image link media code lists table',
                    toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code',
                    menubar: false,
                    height: 400,
                    images_upload_url: '/admin/posts/upload-image', // Nếu muốn upload ảnh lên server
                    automatic_uploads: true,
                    file_picker_types: 'image',
                    image_caption: true,
                    media_live_embeds: true,
                    // Xử lý upload ảnh đơn giản (chỉ demo, cần backend nếu muốn upload thật)
                    images_upload_handler: function (blobInfo, success, failure) {
                        // Mặc định: chỉ upload base64, muốn upload thật cần viết backend
                        success('data:' + blobInfo.blob().type + ';base64,' + blobInfo.base64());
                    }
                });
            </script>
            
            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('admin.posts') }}" class="px-6 py-2 rounded-full font-semibold shadow transition bg-gradient-to-r from-gray-200 to-gray-400 text-gray-800 hover:from-gray-300 hover:to-gray-500 border border-gray-300">Hủy</a>
                <button type="submit" class="px-6 py-2 rounded-full font-semibold shadow transition bg-gradient-to-r from-blue-500 to-blue-700 text-white hover:from-blue-600 hover:to-blue-800 border border-blue-600">Đăng bài</button>
            </div>
        </form>
    </div>
</div>
@endsection
