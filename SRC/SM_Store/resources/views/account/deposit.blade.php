@extends('layouts.account')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex flex-col items-center mb-8">
        <span class="text-5xl coin-spin mb-2">🪙</span>
        <h2 class="orbitron text-3xl font-bold text-white mb-1">Nạp Sky Coins</h2>
<<<<<<< HEAD
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">{{ number_format($currentUser['coins'] ?? session('coins', 0)) }}</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form id="depositForm" class="space-y-6" onsubmit="return false;">
            @csrf
            <input type="hidden" id="user_id" name="user_id" value="{{ session('firebase_uid') }}">
            <div>
                <label class="block text-white font-semibold mb-2">Chọn gói nạp nhanh</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="10000">
=======
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">2,450</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2">Chọn gói nạp nhanh</label>
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">10,000</span>
                        <span class="text-xs text-gray-700">10.000đ</span>
                    </button>
<<<<<<< HEAD
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="20000">
=======
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">20,000</span>
                        <span class="text-xs text-gray-700">20.000đ</span>
                    </button>
<<<<<<< HEAD
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="25000">
=======
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">25,000</span>
                        <span class="text-xs text-gray-700">25.000đ</span>
                    </button>
<<<<<<< HEAD
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="30000">
=======
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">30,000</span>
                        <span class="text-xs text-gray-700">30.000đ</span>
                    </button>
<<<<<<< HEAD
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="50000">
=======
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">50,000</span>
                        <span class="text-xs text-gray-700">50.000đ</span>
                    </button>
<<<<<<< HEAD
                    <button type="button" class="amount-btn bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200 transition-all" data-amount="100000">
=======
                    <button type="button" class="bg-white/60 hover:bg-yellow-100 rounded-xl p-4 flex flex-col items-center border border-yellow-200">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-2xl">🪙</span>
                        <span class="font-bold text-yellow-600">100,000</span>
                        <span class="text-xs text-gray-700">100.000đ</span>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Hoặc nhập số coin muốn nạp</label>
<<<<<<< HEAD
                <input type="text" id="customAmount" name="amount" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số tiền (từ 10.000đ - 50.000.000đ)" />
                <div id="amountError" class="text-red-300 text-xs mt-1 hidden"></div>
                <p class="text-xs text-yellow-100 mt-1">1 coin = 1 VND (Tối thiểu 10,000đ - Tối đa 50,000,000đ)</p>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Phương thức thanh toán</label>
                <div class="flex gap-4 justify-center">
                    <label class="flex items-center gap-2 cursor-pointer bg-white/20 px-4 py-2 rounded-lg border border-white/30">
                        <input type="radio" name="payment" class="accent-blue-500" checked>
                        <span class="text-blue-400 font-semibold">SePay</span>
                        <span class="text-2xl">🏦</span>
                    </label>
                </div>
                <p class="text-xs text-center text-yellow-100 mt-2">Thanh toán qua chuyển khoản ngân hàng với SePay</p>
            </div>
            <button type="button" id="submitBtn" class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="submitText">Nạp Coins với SePay</span>
                <div id="loadingSpinner" class="hidden inline-block ml-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                </div>
            </button>
            <div class="text-xs text-gray-200 mt-2 text-center">* Thanh toán an toàn với SePay - Nạp ngay, nhận coins liền!</div>
        </form>
    </div>
</div>

<!-- Modal hiển thị thông tin thanh toán SePay -->
<div id="paymentModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <div class="text-center">
                <div class="text-4xl mb-4">⏳</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Chờ thanh toán</h3>
                <p class="text-sm text-gray-600 mb-4">Vui lòng chuyển khoản để hoàn tất giao dịch</p>
                <div id="paymentInfo" class="space-y-4">
                    <!-- Thông tin sẽ được load bằng JavaScript -->
                </div>
                <div class="mt-6 space-y-2">
                    <button id="checkPaymentBtn" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition">
                        🔄 Kiểm tra thanh toán
                    </button>
                    <button id="closeModalBtn" 
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg transition">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/deposit-simple.js"></script>
@endsection
=======
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
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
