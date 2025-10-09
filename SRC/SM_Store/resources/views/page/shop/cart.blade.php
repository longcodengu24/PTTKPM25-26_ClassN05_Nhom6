@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto px-2 py-10">
	<div class="game-card rounded-2xl p-8">
		<h2 class="text-center text-3xl md:text-4xl font-bold mb-2 text-white">GIỎ HÀNG CỦA BẠN</h2>
		<div class="text-center text-white/80 mb-8">Giỏ hàng của bạn sẽ hiển thị ở đây</div>
		<div class="flex flex-col md:flex-row gap-8">
			<!-- Cart Table -->
			<div class="flex-1 bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 overflow-x-auto border border-white/30 text-white">
			<table class="w-full text-base table-fixed">
				<colgroup>
					<col style="width: 140px;">
					<col style="width: 50%;">
					<col style="width: 140px;">
					<col style="width: 60px;">
				</colgroup>
				<thead>
					<tr class="border-b border-white/30 text-white/80 text-sm">
						<th class="py-3 px-2 font-semibold text-left">Hình ảnh</th>
						<th class="py-3 px-2 font-semibold text-left">Thông tin</th>
						<th class="py-3 px-2 font-semibold text-right">Giá tiền</th>
						<th class="py-3 px-2 font-semibold text-center"></th>
					</tr>
				</thead>
				<tbody>
					<tr class="border-b border-gray-100 transition">
						<td class="py-4 px-2 align-top">
							<div class="aspect-w-16 aspect-h-9 w-full rounded-lg overflow-hidden border border-gray-200">
								<img src="/img/sheet1.jpg" alt="Dreams of Light" class="object-cover w-full h-full">
							</div>
						</td>
						<td class="py-4 px-2 align-top">
							<div class="font-semibold text-white text-lg">Dreams of Light</div>
							<div class="text-white/80 text-sm">Tác giả: SkyMusicLover</div>
							<div class="text-white/60 text-sm mb-1">Người soạn: AuroraVN</div>
						</td>
						<td class="py-4 px-2 align-top text-right">
							<span class="font-bold text-lg text-yellow-300">1,855,000đ</span>
						</td>
						<td class="py-4 px-2 align-top text-center">
							<button class="text-blue-400 hover:text-blue-600 text-2xl transition align-top" title="Xóa sản phẩm">&times;</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
			<!-- Order Summary -->
			<div class="w-full md:w-80 bg-white/10 backdrop-blur-2xl rounded-2xl shadow-2xl p-6 flex flex-col gap-4 border border-white/30 text-white">
			<div class="font-semibold text-lg text-white mb-2">Tóm tắt đơn hàng</div>
			<div class="flex justify-between text-white/80 text-base">
				<span>Tổng tiền hàng:</span>
				<span>1,855,000đ</span>
			</div>
			<div class="flex justify-between text-white/80 text-base">
				<span>Giảm giá:</span>
				<span>- 0đ</span>
			</div>
			<div class="flex justify-between text-white/80 text-base pb-2 border-b border-white/30">
				<span>Tạm tính:</span>
				<span>1,855,000đ</span>
			</div>
			<div class="flex justify-between items-center text-xl font-bold text-yellow-300 mt-2">
				<span>Tổng tiền:</span>
				<span>1,855,000đ</span>
			</div>
			<button class="w-full mt-4 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-400 text-white font-bold text-lg shadow hover:from-blue-600 hover:to-cyan-500 transition">TIẾN HÀNH ĐẶT HÀNG</button>
			<a href="{{ route('shop.index') }}" class="w-full py-3 rounded-lg border border-blue-200 text-blue-700 font-semibold text-lg text-center hover:bg-blue-50 transition">MUA THÊM SẢN PHẨM</a>
		</div>
	</div>
</div>
@endsection
