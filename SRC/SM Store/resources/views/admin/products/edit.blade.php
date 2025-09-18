
@extends('layouts.admin')

@section('title', 'Sửa Sheet Nhạc')

@section('content')
<div class="admin-content active px-6 pb-6 flex justify-center ">
    <div class="admin-card rounded-xl p-8 w-full max-w-2xl">
        <h2 class="orbitron text-2xl font-bold text-white mb-6">Sửa Sheet Nhạc</h2>
        <form>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Tên Sheet Nhạc</label>
                <input type="text" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" value="Dreams of Light">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Tác giả</label>
                <input type="text" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Nhập tên tác giả...">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Người Soạn</label>
                <input type="text" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" value="SkyMusicLover">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Danh Mục</label>
                <div class="relative" x-data="{ open: false, selected: 'Tất Cả', options: ['Tất Cả', 'Việt Nam', 'Nhật Bản', 'Hàn Quốc', 'Trung Quốc', 'US-UK'] }">
                    <input type="hidden" name="category" :value="selected">
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
                <label class="block text-gray-300 font-semibold mb-2">Giá</label>
                <input type="text" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" value="50.000đ">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Ảnh</label>
                <input type="file" accept="image/*" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full focus:outline-none focus:ring-2 focus:ring-blue-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Link Video</label>
                <input type="text" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg border border-white border-opacity-30 w-full placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-300" placeholder="Nhập đường dẫn video YouTube...">
            </div>
            <div class="mb-5">
                <label class="block text-gray-300 font-semibold mb-2">Trạng Thái</label>
                <div class="relative" x-data="{ open: false, selected: 'Đang bán', options: ['Đang bán', 'Ngừng bán'] }">
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
            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('admin.products') }}" class="px-6 py-2 rounded-full font-semibold shadow transition bg-gradient-to-r from-gray-200 to-gray-400 text-gray-800 hover:from-gray-300 hover:to-gray-500 border border-gray-300">Hủy</a>
                <button type="submit" class="px-6 py-2 rounded-full font-semibold shadow transition bg-gradient-to-r from-blue-500 to-blue-700 text-white hover:from-blue-600 hover:to-blue-800 border border-blue-600">Lưu thay đổi</button>
            </div>
        </form>
    </div>
<!-- Thêm Alpine.js nếu chưa có -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</div>
@endsection
