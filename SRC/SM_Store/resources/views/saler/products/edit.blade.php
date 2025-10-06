@extends('layouts.saler')
@section('title', 'Sửa Sheet Nhạc - Saler Dashboard')

@section('content')
<div class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-4">
      <a href="{{ route('saler.products') }}" class="text-gray-300 hover:text-white">←</a>
      <h2 class="orbitron text-2xl font-bold text-white">✏️ Sửa: {{ $sheet['title'] ?? '' }}</h2>
    </div>
    <button type="submit" form="edit-form" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white">
      Lưu thay đổi
    </button>
  </div>

  @if ($errors->any())
    <div class="mb-4 text-red-300">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form id="edit-form" action="{{ route('saler.products.update', $sheet['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    {{-- Các input giống create nhưng set value mặc định từ $d --}}
    {{-- Ví dụ: --}}
    <div class="admin-card rounded-xl p-6">
      <label class="block text-gray-300 text-sm mb-2">Tên bài hát</label>
      <input type="text" name="title" value="{{ old('title', $sheet['title'] ?? '') }}" required class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
    </div>

    {{-- ... (các field khác) ... --}}

    <div class="admin-card rounded-xl p-6">
      <h3 class="text-lg font-bold text-white mb-4">Ảnh xem trước</h3>
      @if(!empty($sheet['preview_image_url']))
        <img src="{{ $sheet['preview_image_url'] }}" class="w-24 h-24 object-cover rounded mb-3">
      @endif
      <input type="file" name="preview_image" accept="image/*" class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
    </div>

    <div class="admin-card rounded-xl p-6">
      <h3 class="text-lg font-bold text-white mb-4">Tệp sheet nhạc</h3>
      @if(!empty($sheet['sheet_file_url']))
        <a href="{{ $sheet['sheet_file_url'] }}" target="_blank" class="text-blue-300 underline text-sm">File hiện tại</a>
      @endif
      <input type="file" name="sheet_file" accept=".pdf,.png,.jpg,.jpeg" class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white mt-2">
    </div>

    {{-- Checkbox/Select giữ nguyên, dùng old(...) với default từ $d --}}
  </form>
</div>

<script>
document.getElementById('allow_discount')?.addEventListener('change', function() {
  const s = document.getElementById('discount-section');
  this.checked ? s.classList.remove('hidden') : s.classList.add('hidden');
});
</script>
@endsection
