@extends('layouts.admin')

@section('title', 'Bài Đăng')

@section('content')
<div id="posts" class="admin-content active px-6 pb-6">
    <div class="admin-card rounded-xl p-6">
        <h3 class="orbitron text-2xl font-bold text-white mb-6">Quản Lý Bài Đăng</h3>
        <div class="flex justify-end mb-6">
            <a href="{{ route('admin.posts.create') }}" class="bg-blue-500 hover:bg-blue-600 px-6 py-2 rounded-lg text-white inter font-semibold">+ Đăng bài mới</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white border-opacity-20">
                        <th class="text-left py-3 text-gray-300 inter">Tiêu đề</th>
                        <th class="text-left py-3 text-gray-300 inter">Trạng thái</th>
                        <th class="text-left py-3 text-gray-300 inter">Ngày đăng</th>
                        <th class="text-left py-3 text-gray-300 inter">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-row">
                        <td class="py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                                    <span class="text-xl">📝</span>
                                </div>
                                <div>
                                    <p class="text-white font-semibold inter">Bài viết: Cảm nhận về âm nhạc</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4"><span class="status-badge status-active">Hiển thị</span></td>
                        <td class="py-4 text-white inter">2025-09-09</td>
                        <td class="py-4 flex gap-2">
                            <a href="{{ route('community.post-detail', 1) }}" class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded text-white text-sm" target="_blank">Xem</a>
                            <a href="{{ route('admin.posts.edit', 1) }}" class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-white text-sm">Sửa</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
function showTab(tab) {
    const approvalTab = document.getElementById('tab-content-approval');
    const allTab = document.getElementById('tab-content-all');
    const btnApproval = document.getElementById('tab-approval');
    const btnAll = document.getElementById('tab-all');
    if (tab === 'approval') {
        approvalTab.classList.remove('hidden');
        allTab.classList.add('hidden');
        btnApproval.classList.add('border-blue-500');
        btnApproval.classList.remove('border-transparent');
        btnAll.classList.add('border-transparent');
        btnAll.classList.remove('border-blue-500');
    } else {
        approvalTab.classList.add('hidden');
        allTab.classList.remove('hidden');
        btnApproval.classList.add('border-transparent');
        btnApproval.classList.remove('border-blue-500');
        btnAll.classList.add('border-blue-500');
        btnAll.classList.remove('border-transparent');
    }
}
</script>
@endsection