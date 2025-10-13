@extends('layouts.account')
@section('content')
            <div id="activity" class="tab-content active">
                <h3 class="orbitron text-xl font-bold text-white mb-6">Hoạt Động Gần Đây</h3>
                
                <div class="space-y-4">
                    <!-- Hiển thị hoạt động thực từ database -->
                    @if(isset($activities) && count($activities) > 0)
                        @foreach($activities as $activity)
                            <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                                <div class="text-2xl">
                                    @if($activity['type'] === 'purchase')
                                        🛒
                                    @elseif($activity['type'] === 'sale')
                                        💰
                                    @elseif($activity['type'] === 'upload')
                                        📤
                                    @elseif($activity['type'] === 'update')
                                        ✏️
                                    @elseif($activity['type'] === 'delete')
                                        🗑️
                                    @else
                                        📋
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="inter font-semibold text-white">{{ $activity['title'] ?? 'Hoạt động' }}</h4>
                                    <p class="inter text-gray-300 text-sm">{{ $activity['description'] ?? '' }} • {{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</p>
                                </div>
                                <div class="text-gray-300 font-bold">{{ $activity['amount'] ?? '+0' }} 🪙</div>
                            </div>
                        @endforeach
                    @endif



                    @if(!isset($activities) || count($activities) === 0)
                        <div class="profile-card rounded-xl p-6 text-center">
                            <p class="inter text-gray-300">Chưa có hoạt động nào gần đây</p>
                        </div>
                    @endif

                    <!-- Dữ liệu mẫu (giữ nguyên để demo các tính năng khác) -->
                    <!-- Tải lên sheet nhạc mới (+0) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">📤</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Tải lên sheet nhạc mới</h4>
                            <p class="inter text-gray-300 text-sm">"Nocturne Op.9 No.2" • 5 phút trước</p>
                        </div>
                        <div class="text-gray-300 font-bold">+0 🪙</div>
                    </div>
                    <!-- Yêu cầu rút thành công (+0) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">✅</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Yêu cầu rút thành công</h4>
                            <p class="inter text-gray-300 text-sm">Rút 10.000 Sky Coins về Momo • 10 phút trước</p>
                        </div>
                        <div class="text-gray-300 font-bold">+0 🪙</div>
                    </div>
                    <!-- Yêu cầu nạp thành công (+0) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">✅</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Yêu cầu nạp thành công</h4>
                            <p class="inter text-gray-300 text-sm">Nạp 20.000 Sky Coins qua ZaloPay • 20 phút trước</p>
                        </div>
                        <div class="text-gray-300 font-bold">+0 🪙</div>
                    </div>
                    <!-- Yêu cầu quyền đăng sheet (+0) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">📝</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Yêu cầu quyền đăng sheet</h4>
                            <p class="inter text-gray-300 text-sm">Đã gửi yêu cầu lên hệ thống • 30 phút trước</p>
                        </div>
                        <div class="text-gray-300 font-bold">+0 🪙</div>
                    </div>
                    <!-- Yêu cầu đăng sheet được chấp nhận (+0) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">✔️</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Yêu cầu đăng sheet được chấp nhận</h4>
                            <p class="inter text-gray-300 text-sm">Bạn đã có quyền đăng sheet nhạc • 40 phút trước</p>
                        </div>
                        <div class="text-gray-300 font-bold">+0 🪙</div>
                    </div>
                    <!-- Có người mua sheet của bạn (+coin bằng giá sheet) -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">💸</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Có người mua sheet của bạn</h4>
                            <p class="inter text-gray-300 text-sm">"Dreams of Light" đã được bán • 1 giờ trước</p>
                        </div>
                        <div class="text-green-400 font-bold">+10.000 🪙</div>
                    </div>
                    <!-- Nạp coin -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">💰</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Nạp Sky Coins</h4>
                            <p class="inter text-gray-300 text-sm">Nạp 50.000 Sky Coins qua Momo • 10 phút trước</p>
                        </div>
                        <div class="text-green-400 font-bold">+50.000 🪙</div>
                    </div>
                    <!-- Rút coin -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">🏧</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Rút Sky Coins</h4>
                            <p class="inter text-gray-300 text-sm">Rút 20.000 Sky Coins về tài khoản ATM • 1 giờ trước</p>
                        </div>
                        <div class="text-red-400 font-bold">-20.000 🪙</div>
                    </div>
                    <!-- Mua sheet nhạc -->
                    <div class="profile-card rounded-xl p-6 flex items-center space-x-4">
                        <div class="text-2xl">🎼</div>
                        <div class="flex-1">
                            <h4 class="inter font-semibold text-white">Mua sheet nhạc</h4>
                            <p class="inter text-gray-300 text-sm">Mua "River Flows In You" • Trừ 10.000 Sky Coins • 2 giờ trước</p>
                        </div>
                        <div class="text-red-400 font-bold">-10.000 🪙</div>
                    </div>
                    
                </div>
            </div>
@endsection