@extends('layouts.account')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex flex-col items-center mb-8">
        <span class="text-5xl coin-spin mb-2">🪙</span>
        <h2 class="orbitron text-3xl font-bold text-white mb-1">Nạp Sky Coins</h2>
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">2,450</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2">Chọn gói nạp nhanh</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">10,000</span>
                        <span class="text-xs text-gray-700">10.000đ</span>
                    </button>
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">20,000</span>
                        <span class="text-xs text-gray-700">20.000đ</span>
                    </button>
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">25,000</span>
                        <span class="text-xs text-gray-700">25.000đ</span>
                    </button>
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">30,000</span>
                        <span class="text-xs text-gray-700">30.000đ</span>
                    </button>
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">50,000</span>
                        <span class="text-xs text-gray-700">50.000đ</span>
                    </button>
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">100,000</span>
                        <span class="text-xs text-gray-700">100.000đ</span>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Hoặc nhập số coin muốn nạp</label>
                <input type="number" min="5000" step="5000" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số coin (bội số 5.000)..." />
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Phương thức thanh toán</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment" class="accent-pink-500" checked>
                        <span class="text-pink-500 font-semibold">Momo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment" class="accent-blue-500">
                        <span class="text-blue-500 font-semibold">ZaloPay</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="payment" class="accent-green-500">
                        <span class="text-green-500 font-semibold">ATM</span>
                    </label>
                </div>
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition">Nạp Coins</button>
            <div class="text-xs text-gray-200 mt-2 text-center">* Nạp càng nhiều, ưu đãi càng lớn!</div>
        </form>
    </div>
</div>
@endsection
