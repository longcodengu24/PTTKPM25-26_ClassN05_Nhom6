@extends('layouts.saler')
@section('title', 'Thêm Sheet Nhạc Mới - Saler Dashboard')

@section('content')
<div class="p-6">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center space-x-4">
      <a href="{{ route('saler.products') }}" class="text-gray-300 hover:text-white">←</a>
      <h2 class="orbitron text-2xl font-bold text-white">🎵 Thêm Sheet Nhạc Mới</h2>
    </div>
    <button type="submit" form="product-form" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white">
      Đăng bán
    </button>
  </div>

  @if ($errors->any())
    <div class="mb-4 text-red-300">
      <ul class="list-disc ml-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form id="product-form" action="{{ route('saler.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
  
    @include('saler.products.form') 
  </form>
</div>

<script>
document.getElementById('allow_discount')?.addEventListener('change', function() {
  const s = document.getElementById('discount-section');
  this.checked ? s.classList.remove('hidden') : s.classList.add('hidden');
});
</script>
@endsection
