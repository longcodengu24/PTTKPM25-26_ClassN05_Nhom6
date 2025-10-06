@extends('layouts.saler')

@section('title', 'Quản lý Sheet Nhạc - Saler Dashboard')

@section('content')
<div class="p-6">
  <div class="flex items-center justify-between mb-6">
    <h2 class="orbitron text-2xl font-bold text-white">🎼 Quản lý Sheet Nhạc</h2>
    <a href="{{ route('saler.products.create') }}" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white">
      ➕ Thêm Sheet Nhạc
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 text-green-300">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 text-red-300">{{ session('error') }}</div>
  @endif

  <div class="admin-card rounded-xl p-6">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="border-b border-gray-600">
            <th class="text-left py-3 px-4 text-gray-300">Tên</th>
            <th class="text-left py-3 px-4 text-gray-300">Thể loại</th>
            <th class="text-left py-3 px-4 text-gray-300">Giá</th>
            <th class="text-left py-3 px-4 text-gray-300">Trạng thái</th>
            <th class="text-left py-3 px-4 text-gray-300">Ảnh</th>
            <th class="text-left py-3 px-4 text-gray-300">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sheets as $sheet)
            <tr class="border-b border-gray-700 hover:bg-white hover:bg-opacity-5">
              <td class="py-4 px-4 text-white">{{ $sheet['title'] ?? '---' }}</td>
              <td class="py-4 px-4 text-white">{{ $sheet['genre'] ?? '---' }}</td>
              <td class="py-4 px-4 text-white">{{ isset($sheet['price']) ? number_format($sheet['price']) : 0 }} VND</td>
              <td class="py-4 px-4 text-white">{{ $sheet['status'] ?? '---' }}</td>
              <td class="py-4 px-4">
                @if(!empty($sheet['preview_image_url']))
                  <img src="{{ $sheet['preview_image_url'] }}" class="w-12 h-12 object-cover rounded">
                @endif
              </td>
              <td class="py-4 px-4">
                <div class="flex items-center gap-2">
                  <a href="{{ route('saler.products.edit', $sheet['id']) }}" class="text-yellow-400 hover:text-yellow-300 p-2">✏️</a>
                  <form action="{{ route('saler.products.destroy', $sheet['id']) }}" method="POST" onsubmit="return confirm('Xác nhận xoá?')">
                    @csrf @method('DELETE')
                    <button class="text-red-400 hover:text-red-300 p-2">🗑️</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-gray-400 py-6">Chưa có sheet nhạc</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
