@extends('layouts.account')
@section('content')
<div class="max-w-lg mx-auto">
    <div class="flex flex-col items-center mb-8">
        <span class="text-5xl coin-spin mb-2">🪙</span>
        <h2 class="orbitron text-3xl font-bold text-white mb-1">Rút Sky Coins</h2>
<<<<<<< HEAD
        <div class="text-yellow-200 text-lg mb-2">
            Số dư hiện tại: <span class="font-bold" id="currentBalance">{{ number_format($userData['coins'] ?? 0) }}</span> coins
        </div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form id="withdrawForm" class="space-y-6">
            @csrf
            <div>
                <label class="block text-white font-semibold mb-2">Số coin muốn rút</label>
                <input type="number" id="withdrawAmount" name="amount" min="5000" step="5000" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" 
                    placeholder="Nhập số coin (bội số 5.000)..." required />
=======
        <div class="text-yellow-200 text-lg mb-2">Số dư hiện tại: <span class="font-bold">2,450</span> coins</div>
    </div>
    <div class="profile-card rounded-3xl p-8 shadow-2xl bg-white/30 border border-white/20 backdrop-blur-lg">
        <form class="space-y-6">
            <div>
                <label class="block text-white font-semibold mb-2">Số coin muốn rút</label>
                <input type="number" min="5000" step="5000" max="50000" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số coin (bội số 5.000)..." />
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Phương thức nhận tiền</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
<<<<<<< HEAD
                        <input type="radio" name="method" value="momo" class="accent-pink-500" checked>
                        <span class="text-pink-500 font-semibold">Momo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="method" value="zalopay" class="accent-blue-500">
                        <span class="text-blue-500 font-semibold">ZaloPay</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="method" value="bank" class="accent-green-500">
=======
                        <input type="radio" name="withdraw_method" class="accent-pink-500" checked>
                        <span class="text-pink-500 font-semibold">Momo</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="withdraw_method" class="accent-blue-500">
                        <span class="text-blue-500 font-semibold">ZaloPay</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="withdraw_method" class="accent-green-500">
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
                        <span class="text-green-500 font-semibold">ATM</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-white font-semibold mb-2">Thông tin tài khoản nhận</label>
<<<<<<< HEAD
                <input type="text" id="accountInfo" name="account_info" 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" 
                    placeholder="Nhập số điện thoại/số tài khoản..." required />
            </div>
            <button type="submit" id="withdrawBtn" 
                class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition">
                Rút Coins
            </button>
            <div class="text-xs text-gray-200 mt-2 text-center">
                * Lưu ý: Số coin rút phải là bội số 5.000. Xử lý tự động.
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('withdrawForm');
    var submitBtn = document.getElementById('withdrawBtn');
    var currentBalance = {{ $userData['coins'] ?? 0 }};

    form.onsubmit = function(e) {
        e.preventDefault();

        var amount = parseInt(document.getElementById('withdrawAmount').value);
        var method = document.querySelector('input[name="method"]:checked').value;
        var accountInfo = document.getElementById('accountInfo').value;

        // Validation
        if (!amount || amount < 5000) {
            alert('Số tiền rút tối thiểu là 5,000 coins');
            return;
        }

        if (amount % 5000 !== 0) {
            alert('Số tiền rút phải là bội số của 5,000');
            return;
        }

        if (amount > currentBalance) {
            alert('Số dư không đủ! Bạn có ' + currentBalance.toLocaleString('vi-VN') + ' coins');
            return;
        }

        if (!accountInfo || accountInfo.length < 9) {
            alert('Vui lòng nhập thông tin tài khoản hợp lệ');
            return;
        }

        // Confirm
        var methodName = method === 'momo' ? 'Momo' : (method === 'zalopay' ? 'ZaloPay' : 'ATM');
        if (!confirm('Xác nhận rút ' + amount.toLocaleString('vi-VN') + ' coins về ' + methodName + ' (' + accountInfo + ')?')) {
            return;
        }

        // Disable button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';

        // Call API
        fetch('{{ route("account.withdraw.process") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount: amount,
                method: method,
                account_info: accountInfo
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Rút Coins';

            if (data.success) {
                alert('✅ Rút tiền thành công!\n\n' +
                    '💰 Số coins đã rút: ' + amount.toLocaleString('vi-VN') + '\n' +
                    '💳 Phương thức: ' + methodName + '\n' +
                    '📱 Tài khoản: ' + accountInfo + '\n' +
                    '💵 Số dư còn lại: ' + data.data.new_coins.toLocaleString('vi-VN') + ' coins\n\n' +
                    'Trang sẽ được tải lại.');
                
                // Reload page
                window.location.reload();
            } else {
                alert('❌ Lỗi: ' + data.message);
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.textContent = 'Rút Coins';
            alert('Có lỗi xảy ra: ' + error.message);
        });
    };
});
</script>
=======
                <input type="text" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-400 outline-none" placeholder="Nhập số điện thoại/số tài khoản/email..." />
            </div>
            <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400 to-pink-400 text-white font-bold text-lg shadow-lg hover:from-yellow-500 hover:to-pink-500 transition">Rút Coins</button>
            <div class="text-xs text-gray-200 mt-2 text-center">* Lưu ý: Số coin rút phải là bội số 5.000. Thời gian xử lý 1-2 ngày làm việc.</div>
        </form>
    </div>
</div>
>>>>>>> 4e0fcd0d9d0af40ad9cee5488658eb3cda4b9836
@endsection
