@extends('layouts.app')

@section('title', 'Chi tiết bài viết - Sky Music Store')

@section('content')
<main class="mx-auto w-4/5 rounded-xl shadow p-8 mt-10 mb-10 bg-white/30 backdrop-blur-md">
	<!-- Tiêu đề -->
	<h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-2">Hướng dẫn chơi "Dreams of Light" trên đàn piano</h1>

	<!-- Người viết & Ngày viết -->
	<div class="flex flex-col md:flex-row md:items-center md:justify-between text-gray-500 text-base mb-8">
		<div><span class="font-semibold text-blue-700">SkyMusicLover</span></div>
		<div>12/09/2025</div>
	</div>
	<!-- Nút tim (like) ở cuối bài viết -->
	<div class="mt-10 mb-4">
		<button class="flex items-center px-4 py-2 rounded-full bg-pink-100 hover:bg-pink-200 text-pink-600 font-semibold shadow transition">
			<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" class="w-5 h-5 mr-2"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>
			<span>12</span>
		</button>
	</div>

	<!-- Đoạn văn đầu tiên (lùi đầu dòng) -->
	<p class="text-lg text-gray-800 mb-6 indent-8">Mình vừa làm video hướng dẫn chi tiết cách chơi bài này, các bạn xem và góp ý nhé! Trong bài viết này, mình sẽ chia sẻ các bước luyện tập, các đoạn khó và mẹo để chơi "Dreams of Light" mượt mà hơn.</p>

	<!-- Ảnh minh họa (1/3 chiều rộng) -->
	<div class="flex justify-center mb-8">
		<img src="https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?auto=format&fit=crop&w=800&q=80" alt="Piano Cover" class="w-1/3 rounded shadow object-cover">
	</div>

	<!-- Đoạn văn tiếp theo (lùi đầu dòng) -->
	<p class="text-lg text-gray-800 mb-6 indent-8">Nếu bạn có thắc mắc hoặc muốn chia sẻ kinh nghiệm, hãy để lại bình luận ở trang cộng đồng nhé! Dưới đây là video hướng dẫn chi tiết từng bước.</p>

	<!-- Video nhúng (1/3 chiều rộng) -->
	<div class="flex justify-center mb-8">
		<div class="w-1/3 aspect-video">
			<iframe class="rounded-lg w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video hướng dẫn" allowfullscreen></iframe>
		</div>
	</div>

	<!-- Đoạn văn tiếp theo (lùi đầu dòng) -->
	<p class="text-lg text-gray-800 mb-6 indent-8">Chúc các bạn luyện tập vui vẻ và sớm chơi thành thạo bản nhạc này!</p>

	<!-- Bình luận -->
	<section class="mt-12">
		<h2 class="text-xl font-bold text-gray-900 mb-4">Bình luận</h2>
		<!-- Danh sách bình luận mẫu -->
		<div class="space-y-6 mb-8">
			<div class="flex items-start gap-3">
				<img src="https://i.pravatar.cc/40?img=1" class="w-10 h-10 rounded-full object-cover" alt="avatar">
				<div class="bg-gray-100 rounded-lg px-4 py-2 flex-1">
					<div class="font-semibold text-sm text-blue-700">Minh Anh <span class="text-gray-400 font-normal text-xs ml-2">2 phút trước</span></div>
					<div class="text-gray-800 text-base">Cảm ơn bạn đã chia sẻ, mình sẽ thử tập bài này!</div>
				</div>
			</div>
			<div class="flex items-start gap-3">
				<img src="https://i.pravatar.cc/40?img=2" class="w-10 h-10 rounded-full object-cover" alt="avatar">
				<div class="bg-gray-100 rounded-lg px-4 py-2 flex-1">
					<div class="font-semibold text-sm text-blue-700">Hải Đăng <span class="text-gray-400 font-normal text-xs ml-2">5 phút trước</span></div>
					<div class="text-gray-800 text-base">Video hướng dẫn rất dễ hiểu, cảm ơn bạn!</div>
				</div>
			</div>
		</div>
		<!-- Form nhập bình luận -->
		<form class="flex items-start gap-3">
			<img src="https://i.pravatar.cc/40?img=3" class="w-10 h-10 rounded-full object-cover" alt="avatar">
			<textarea class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-300 resize-none" rows="2" placeholder="Viết bình luận..."></textarea>
			<button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg transition">Gửi</button>
		</form>
	</section>

	<div class="text-right mt-8">
		<a href="{{ route('community.index') }}" class="text-blue-500 hover:underline">← Quay lại danh sách bài viết</a>
	</div>
</main>
@endsection
