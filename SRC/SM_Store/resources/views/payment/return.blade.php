@extends('layouts.app')

@section('title', 'Kết quả thanh toán')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-6 text-center">
        @if($transaction->isCompleted())
            <!-- Thanh toán thành công -->
            <div class="text-green-600 mb-6">
                <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clips-rule="evenodd"></path>
                </svg>
                <h1 class="text-2xl font-bold">Nạp tiền thành công!</h1>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <p class="text-green-800 text-sm mb-2">
                    Số tiền <strong class="text-lg">{{ number_format($transaction->amount, 0, ',', '.') }}đ</strong> đã được nạp vào tài khoản của bạn.
                </p>
                <p class="text-green-600 text-xs">
                    Thời gian xử lý: {{ $transaction->processed_at ? $transaction->processed_at->format('d/m/Y H:i:s') : 'N/A' }}
                </p>
            </div>
        @else
            <!-- Trạng thái khác -->
            <div class="text-yellow-600 mb-6">
                <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <h1 class="text-2xl font-bold">
                    @switch($transaction->status)
                        @case('processing')
                            Đang xử lý giao dịch
                            @break
                        @case('pending')
                            Chờ thanh toán
                            @break
                        @case('failed')
                            Giao dịch thất bại
                            @break
                        @default
                            Trạng thái: {{ ucfirst($transaction->status) }}
                    @endswitch
                </h1>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                @if($transaction->status === 'processing')
                    <p class="text-yellow-800 text-sm">
                        Giao dịch của bạn đang được xử lý. Vui lòng đợi trong vài phút.
                    </p>
                @elseif($transaction->status === 'pending')
                    <p class="text-yellow-800 text-sm">
                        Giao dịch chưa được hoàn thành. Vui lòng kiểm tra lại việc chuyển khoản.
                    </p>
                @else
                    <p class="text-yellow-800 text-sm">
                        Có vấn đề với giao dịch của bạn. Vui lòng liên hệ hỗ trợ.
                    </p>
                @endif
            </div>
        @endif

        <!-- Thông tin giao dịch -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <h3 class="font-semibold text-gray-800 mb-3">Thông tin giao dịch:</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Mã giao dịch:</span>
                    <span class="font-medium">{{ $transaction->transaction_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Số tiền:</span>
                    <span class="font-medium text-green-600">{{ number_format($transaction->amount, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span>Trạng thái:</span>
                    <span class="font-medium 
                        @if($transaction->isCompleted()) text-green-600
                        @elseif($transaction->status === 'failed') text-red-600
                        @else text-yellow-600 @endif">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Thời gian tạo:</span>
                    <span class="font-medium">{{ $transaction->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                @if($transaction->processed_at)
                <div class="flex justify-between">
                    <span>Thời gian xử lý:</span>
                    <span class="font-medium">{{ $transaction->processed_at->format('d/m/Y H:i:s') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Các hành động -->
        <div class="space-y-3">
            @if(!$transaction->isCompleted())
                <button id="checkStatusBtn" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md transition">
                    Kiểm tra lại trạng thái
                </button>
            @endif
            
            <div class="flex space-x-3">
                <a href="{{ route('payment.deposit') }}" 
                   class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition text-center">
                    Nạp tiền mới
                </a>
                <a href="{{ route('payment.transactions') }}?user_id={{ $transaction->user_id }}" 
                   class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md transition text-center">
                    Lịch sử giao dịch
                </a>
            </div>
            
            <a href="{{ route('account.index') }}" 
               class="block w-full bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition text-center">
                Về trang tài khoản
            </a>
        </div>
    </div>
</div>

@if(!$transaction->isCompleted())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkStatusBtn = document.getElementById('checkStatusBtn');
    
    if (checkStatusBtn) {
        checkStatusBtn.addEventListener('click', async function() {
            this.disabled = true;
            this.textContent = 'Đang kiểm tra...';
            
            try {
                const response = await fetch(`{{ route("payment.status") }}?transaction_id={{ $transaction->transaction_id }}`);
                const data = await response.json();
                
                if (data.success) {
                    const status = data.data.status;
                    
                    if (status === 'completed') {
                        alert('Giao dịch đã hoàn thành! Trang sẽ được tải lại.');
                        location.reload();
                    } else {
                        let message = '';
                        switch (status) {
                            case 'processing':
                                message = 'Giao dịch đang được xử lý. Vui lòng đợi thêm.';
                                break;
                            case 'pending':
                                message = 'Giao dịch vẫn đang chờ thanh toán.';
                                break;
                            case 'failed':
                                message = 'Giao dịch đã thất bại.';
                                break;
                            default:
                                message = `Trạng thái hiện tại: ${status}`;
                        }
                        alert(message);
                    }
                } else {
                    alert('Không thể kiểm tra trạng thái giao dịch.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kiểm tra trạng thái.');
            } finally {
                this.disabled = false;
                this.textContent = 'Kiểm tra lại trạng thái';
            }
        });
    }
    
    // Auto refresh every 30 seconds if not completed
    @if(!$transaction->isCompleted())
    setInterval(() => {
        fetch(`{{ route("payment.status") }}?transaction_id={{ $transaction->transaction_id }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.status === 'completed') {
                    location.reload();
                }
            })
            .catch(console.error);
    }, 30000);
    @endif
});
</script>
@endif
@endsection