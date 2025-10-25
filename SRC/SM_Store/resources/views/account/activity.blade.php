
@extends('layouts.account')
@section('content')
            <div id="activity" class="tab-content active">
                <h3 class="orbitron text-xl font-bold text-white mb-6">Hoạt Động Gần Đây</h3>
                
                <div class="space-y-4">
                    <!-- Hiển thị hoạt động thực từ Firestore -->
                    @if(isset($activities) && count($activities) > 0)
                        @foreach($activities as $activity)
                            <div class="profile-card rounded-xl p-6 flex items-center space-x-4 hover:bg-white/5 transition-all duration-200">
                                <div class="text-2xl">
                                    @if(($activity['type'] ?? '') === 'purchase')
                                        🛒
                                    @elseif(($activity['type'] ?? '') === 'deposit')
                                        💳
                                    @elseif(($activity['type'] ?? '') === 'download')
                                        📥
                                    @else
                                        📋
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="inter font-semibold text-white">{{ $activity['title'] ?? 'Hoạt động' }}</h4>
                                    <p class="inter text-gray-300 text-sm">{{ $activity['description'] ?? '' }} • {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                                    @if(config('app.debug'))
                                        <div class="text-xs text-blue-300 mt-1">
                                            Type: {{ $activity['type'] ?? 'N/A' }} | 
                                            ID: {{ $activity['id'] ?? 'N/A' }}
                                            @if(isset($activity['transaction_id']))
                                                | TXN: {{ $activity['transaction_id'] }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if(($activity['type'] ?? '') === 'purchase' && isset($activity['total_amount']))
                                        <div class="text-red-400 font-bold">-{{ number_format(floatval($activity['total_amount'])) }} coins</div>
                                    @elseif(($activity['type'] ?? '') === 'deposit' && isset($activity['amount']))
                                        <div class="text-green-400 font-bold">+{{ number_format(floatval($activity['amount'])) }} coins</div>
                                    @elseif(($activity['type'] ?? '') === 'download')
                                        <div class="text-blue-400 font-bold">📥</div>
                                    @else
                                        <div class="text-gray-300 font-bold">-</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif



                    @if(!isset($activities) || count($activities) === 0)
                        <div class="profile-card rounded-xl p-6 text-center">
                            <div class="text-6xl mb-4">📋</div>
                            <h4 class="inter font-semibold text-white mb-2">Chưa có hoạt động nào</h4>
                            <p class="inter text-gray-300">Các hoạt động như mua sheet, nạp coins sẽ hiển thị ở đây</p>
                        </div>
                    @endif

                    <!-- Dữ liệu mẫu (chỉ hiển thị khi không có hoạt động thực) -->
                    @if(!isset($activities) || count($activities) === 0)
                    <!-- Nạp Sky Coins -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">💳</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Nạp Sky Coins</h4>
                            <p class="inter text-gray-300 text-sm">Nạp thành công 50.000 Sky Coins vào tài khoản qua Momo • 10 phút trước</p>
                        </div>
                        <div class="text-green-400 font-bold">+50.000 coins</div>
                    </div>
                    <!-- Mua sheet thành công -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">🛒</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Mua sheet thành công</h4>
                            <p class="inter text-gray-300 text-sm">Bạn đã mua thành công 2 sheet nhạc: River Flows In You, Dreams of Light • 1 giờ trước</p>
                        </div>
                        <div class="text-red-400 font-bold">-25.000 coins</div>
                    </div>
                    <!-- Bạn đã tải sheet nhạc về máy -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">📥</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Bạn đã tải sheet nhạc về máy</h4>
                            <p class="inter text-gray-300 text-sm">Bạn đã tải về sheet nhạc: River Flows In You • 2 giờ trước</p>
                        </div>
                        <div class="text-blue-400 font-bold">📥</div>
                    </div>
                    @endif
                    
                </div>
            </div>
@endsection