@php
    $d = $d ?? []; // nếu chưa có $d thì tạo mảng rỗng
@endphp



<div class="admin-card rounded-xl p-6">
  <label class="block text-gray-300 text-sm mb-2">Tên bài hát *</label>
  <input type="text" name="title" 
         value="{{ old('title', $d['title'] ?? '') }}" 
         required 
         class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white placeholder-gray-400">
</div>

{{-- Tác giả / Nhạc sĩ --}}
<div class="admin-card rounded-xl p-6">
  <label class="block text-gray-300 text-sm mb-2">Tác giả/Nhạc sĩ *</label>
  <input type="text" name="composer" 
         value="{{ old('composer', $d['composer'] ?? '') }}" 
         required 
         class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white placeholder-gray-400">
</div>

{{-- Thể loại & Độ khó --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="admin-card rounded-xl p-6">
    <label class="block text-gray-300 text-sm mb-2">Thể loại *</label>
    <select name="genre" required class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
      <option value="">Chọn thể loại</option>
      @foreach (['Classical','Pop','Rock','Jazz','Blues','Folk','Country'] as $genre)
        <option value="{{ $genre }}" {{ old('genre', $d['genre'] ?? '') == $genre ? 'selected' : '' }}>{{ $genre }}</option>
      @endforeach
    </select>
  </div>

  <div class="admin-card rounded-xl p-6">
    <label class="block text-gray-300 text-sm mb-2">Độ khó *</label>
    <select name="difficulty" required class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
      <option value="">Chọn độ khó</option>
      @foreach (['Beginner','Intermediate','Advanced','Expert'] as $level)
        <option value="{{ $level }}" {{ old('difficulty', $d['difficulty'] ?? '') == $level ? 'selected' : '' }}>{{ $level }}</option>
      @endforeach
    </select>
  </div>
</div>

{{-- Mô tả --}}
<div class="admin-card rounded-xl p-6">
  <label class="block text-gray-300 text-sm mb-2">Mô tả</label>
  <textarea name="description" rows="4" 
            class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white placeholder-gray-400"
            placeholder="Mô tả về bài hát, phong cách chơi...">{{ old('description', $d['description'] ?? '') }}</textarea>
</div>

{{-- Giá bán & Giảm giá --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="admin-card rounded-xl p-6">
    <label class="block text-gray-300 text-sm mb-2">Giá bán (VNĐ) *</label>
    <input type="number" name="price" 
           value="{{ old('price', $d['price'] ?? '') }}" 
           required min="0" step="1000"
           class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white placeholder-gray-400">
  </div>

  <div class="admin-card rounded-xl p-6">
    <div class="flex items-center mb-4">
      <input type="checkbox" id="allow_discount" name="allow_discount" value="1" 
             {{ old('allow_discount', $d['allow_discount'] ?? false) ? 'checked' : '' }}
             class="mr-2">
      <label for="allow_discount" class="text-gray-300">Cho phép giảm giá</label>
    </div>
    
    <div id="discount-section" class="{{ old('allow_discount', $d['allow_discount'] ?? false) ? '' : 'hidden' }}">
      <label class="block text-gray-300 text-sm mb-2">Giá giảm (VNĐ)</label>
      <input type="number" name="discount_price" 
             value="{{ old('discount_price', $d['discount_price'] ?? '') }}" 
             min="0" step="1000"
             class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white placeholder-gray-400">
    </div>
  </div>
</div>

{{-- Trạng thái & tuỳ chọn khác --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="admin-card rounded-xl p-6">
    <label class="block text-gray-300 text-sm mb-2">Trạng thái *</label>
    <select name="status" required class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
      @foreach (['draft'=>'Nháp','published'=>'Xuất bản','scheduled'=>'Hẹn giờ'] as $key => $label)
        <option value="{{ $key }}" {{ old('status', $d['status'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
      @endforeach
    </select>
  </div>

  <div class="admin-card rounded-xl p-6">
    <div class="space-y-3">
      <div class="flex items-center">
        <input type="checkbox" id="featured" name="featured" value="1" 
               {{ old('featured', $d['featured'] ?? false) ? 'checked' : '' }}
               class="mr-2">
        <label for="featured" class="text-gray-300">Sản phẩm nổi bật</label>
      </div>
      
      <div class="flex items-center">
        <input type="checkbox" id="allow_comments" name="allow_comments" value="1" 
               {{ old('allow_comments', $d['allow_comments'] ?? true) ? 'checked' : '' }}
               class="mr-2">
        <label for="allow_comments" class="text-gray-300">Cho phép bình luận</label>
      </div>
    </div>
  </div>
</div>

{{-- Ảnh xem trước --}}
<div class="admin-card rounded-xl p-6">
  <h3 class="text-lg font-bold text-white mb-4">Ảnh xem trước {{ empty($d) ? '*' : '' }}</h3>
  @if(!empty($d['preview_image_url']))
    <div class="mb-4">
      <img src="{{ $d['preview_image_url'] }}" class="w-32 h-32 object-cover rounded-lg border">
      <p class="text-gray-400 text-sm mt-2">Ảnh hiện tại</p>
    </div>
  @endif
  <input type="file" name="preview_image" accept="image/*" {{ empty($d) ? 'required' : '' }}
         class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
  <p class="text-gray-400 text-sm mt-2">Chấp nhận: JPG, PNG. Tối đa 2MB.</p>
</div>

{{-- File sheet nhạc --}}
<div class="admin-card rounded-xl p-6">
  <h3 class="text-lg font-bold text-white mb-4">Tệp sheet nhạc {{ empty($d) ? '*' : '' }}</h3>
  @if(!empty($d['sheet_file_url']))
    <div class="mb-4">
      <a href="{{ $d['sheet_file_url'] }}" target="_blank" 
         class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
        📄 Xem file hiện tại
      </a>
    </div>
  @endif
  <input type="file" name="sheet_file" accept=".pdf,.png,.jpg,.jpeg" {{ empty($d) ? 'required' : '' }}
         class="w-full px-4 py-3 bg-white bg-opacity-10 border rounded-lg text-white">
  <p class="text-gray-400 text-sm mt-2">Chấp nhận: PDF, PNG, JPG. Tối đa 10MB.</p>
</div>

{{-- Script bật/tắt phần giảm giá --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const allowDiscountCheckbox = document.getElementById('allow_discount');
  const discountSection = document.getElementById('discount-section');
  
  if (allowDiscountCheckbox) {
    allowDiscountCheckbox.addEventListener('change', function() {
      if (this.checked) {
        discountSection.classList.remove('hidden');
      } else {
        discountSection.classList.add('hidden');
      }
    });
  }
});
</script>
