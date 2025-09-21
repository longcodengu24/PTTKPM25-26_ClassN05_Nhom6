@extends('layouts.account')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex flex-col items-center mb-8">
        <span class="text-5xl coin-spin mb-2">🪙</span>
        <h2 class="orbitron text-3xl font-bold text-white mb-1">Rút Sky Coins</h2>
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">2,450</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2">Số coin muốn rút</label>
                <input type="number" min="5000" step="5000" max="50000" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số coin (bội số 5.000)..." />
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Phương thức nhận tiền</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="withdraw_method" class="accent-pink-500" checked>
                        <span class="text-pink-500 font-semibold">Momo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="withdraw_method" class="accent-blue-500">
                        <span class="text-blue-500 font-semibold">ZaloPay</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="withdraw_method" class="accent-green-500">
                        <span class="text-green-500 font-semibold">ATM</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Thông tin tài khoản nhận</label>
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số điện thoại/số tài khoản/email..." />
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition">Rút Coins</button>
            <div class="text-xs text-gray-200 mt-2 text-center">* Lưu ý: Số coin rút phải là bội số 5.000. Thời gian xử lý 1-2 ngày làm việc.</div>
        </form>
    </div>
</div>
@endsection
