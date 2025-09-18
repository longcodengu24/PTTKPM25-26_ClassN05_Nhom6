@extends('layouts.account')
@section('content')
<div class="profile-card rounded-2xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="orbitron text-xl font-bold text-white">Sheet Nhạc Của Tôi (2)</h3>
        <button class="glow-button bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg inter font-semibold">
            + Tải Lên Sheet Mới
        </button>
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
                <!-- Sheet đang bán -->
                <tr class="bg-white/10 hover:bg-white/20 transition rounded-xl">
                    <td class="py-4 px-4">
                        <div>
                            <div class="orbitron font-bold text-white leading-tight">Dreams of Light</div>
                            <div class="inter text-xs text-blue-100">Nguyễn Văn A</div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-white">SkyMusicLover</td>
                    <td class="py-4 px-4 text-white">Season</td>
                    <td class="py-4 px-4 text-white">50.000đ</td>
                    <td class="py-4 px-4 text-white">234</td>
                    <td class="py-4 px-4"><span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Đang bán</span></td>
                    <td class="py-4 px-4 flex gap-2">
                        <button class="px-4 py-1 rounded bg-yellow-400 hover:bg-yellow-500 text-white font-semibold shadow">Sửa</button>
                        <button class="px-4 py-1 rounded bg-red-500 hover:bg-red-600 text-white font-semibold shadow">Xóa</button>
                    </td>
                </tr>
                <!-- Sheet đã mua -->
                <tr class="bg-white/5 hover:bg-white/20 transition rounded-xl">
                    <td class="py-4 px-4">
                        <div>
                            <div class="orbitron font-bold text-white leading-tight">River Flows In You</div>
                            <div class="inter text-xs text-blue-100">Yiruma</div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-white">Yiruma</td>
                    <td class="py-4 px-4 text-white">Ballad</td>
                    <td class="py-4 px-4 text-white">40.000đ</td>
                    <td class="py-4 px-4 text-white">1</td>
                    <td class="py-4 px-4"><span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">Đã mua</span></td>
                    <td class="py-4 px-4 flex gap-2">
                        <button class="px-4 py-1 rounded bg-blue-500 hover:bg-blue-600 text-white font-semibold shadow">Tải</button>
                    </td>
                </tr>
       
            </tbody>
        </table>
    </div>
</div>
@endsection

