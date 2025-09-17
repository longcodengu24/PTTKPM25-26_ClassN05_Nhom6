@extends('layouts.app')

@section('title', 'Cộng đồng - Sky Music Store')

@section('content')
<div id="community" class="page-content">
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="orbitron text-5xl font-bold text-white text-center mb-16">🌟 Cộng Đồng Sky Music</h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Discord -->
                    <div class="game-card rounded-xl p-8 text-center">
                        <div class="w-20 h-20 bg-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-4xl">💬</span>
                        </div>
                        <h3 class="orbitron text-2xl font-bold text-white mb-4">Discord Server</h3>
                        <p class="inter text-blue-100 mb-6">Tham gia server Discord với hơn 5000 thành viên yêu nhạc Sky</p>
                        <button class="glow-button bg-indigo-600 text-white px-6 py-3 rounded-full font-semibold">Tham Gia Discord</button>
                    </div>

                    <!-- Facebook Group -->
                    <div class="game-card rounded-xl p-8 text-center">
                        <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-4xl">👥</span>
                        </div>
                        <h3 class="orbitron text-2xl font-bold text-white mb-4">Facebook Group</h3>
                        <p class="inter text-blue-100 mb-6">Chia sẻ video cover, thảo luận về sheet nhạc mới</p>
                        <button class="glow-button bg-blue-600 text-white px-6 py-3 rounded-full font-semibold">Tham Gia Group</button>
                    </div>
                </div>

                <!-- Recent Posts -->
                <div class="mt-16">
                    <h3 class="orbitron text-3xl font-bold text-white text-center mb-8">Bài Viết Mới Nhất</h3>
                    <div class="space-y-6">
                        <div class="game-card rounded-xl p-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                    <span class="text-xl">🎵</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="orbitron font-bold text-white mb-2">Hướng dẫn chơi "Dreams of Light" trên đàn piano</h4>
                                    <p class="inter text-blue-200 text-sm mb-2">Bởi SkyMusicLover • 2 giờ trước</p>
                                    <p class="inter text-blue-100">Mình vừa làm video hướng dẫn chi tiết cách chơi bài này, các bạn xem và góp ý nhé!</p>
                                </div>
                            </div>
                        </div>

                        <div class="game-card rounded-xl p-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                    <span class="text-xl">🎶</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="orbitron font-bold text-white mb-2">Sheet nhạc mới từ Season of Passage đã có!</h4>
                                    <p class="inter text-blue-200 text-sm mb-2">Bởi Admin • 5 giờ trước</p>
                                    <p class="inter text-blue-100">Chúng mình vừa cập nhật 3 bài nhạc mới từ season mới nhất, mọi người check shop nhé!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection