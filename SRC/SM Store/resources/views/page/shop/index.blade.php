
@extends('layouts.app')
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>[x-cloak] { display: none !important; }</style>
@endpush

@section('title', 'Cửa hàng - Sky Music Store')

@section('content')
 <div id="shop" class="page-content" x-data="{ showDetail: false, product: {} }">
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="orbitron text-5xl font-bold text-white text-center mb-16">🎼 Cửa Hàng Sheet Nhạc</h2>
                
                <!-- Categories -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">Tất Cả</button>
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">Việt Nam</button>
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">Nhật Bản</button>
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">Hàn Quốc</button>
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">Trung Quốc</button>
                    <button class="bg-white bg-opacity-20 text-white px-6 py-3 rounded-full backdrop-blur-sm hover:bg-opacity-30 transition-all inter">US-UK</button>
                </div>

                <!-- Products Grid -->
                <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <!-- Product 1 -->
                    <div class="game-card rounded-xl p-4">
                        <div class="bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                            <span class="text-3xl">🎵</span>
                        </div>
                        <h4 class="orbitron font-bold text-white mb-2">Dreams of Light</h4>
                        <p class="inter text-blue-200 text-sm mb-1">Tác giả: Nguyễn Văn A</p>
                        <p class="inter text-blue-200 text-sm mb-1">Người soạn: SkyMusicLover</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="orbitron text-yellow-300 font-bold">50.000đ</span>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors"
                                @click="product = { name: 'Dreams of Light', author: 'Nguyễn Văn A', composer: 'SkyMusicLover', price: '50.000đ', img: 'https://via.placeholder.com/320x180?text=Dreams+of+Light', video: 'https://www.youtube.com/embed/dQw4w9WgXcQ' }; showDetail = true;">
                                Xem
                            </button>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="game-card rounded-xl p-4">
                        <div class="bg-gradient-to-br from-pink-400 to-red-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                            <span class="text-3xl">🎶</span>
                        </div>
                        <h4 class="orbitron font-bold text-white mb-2">Aurora Concert</h4>
                        <p class="inter text-blue-200 text-sm mb-1">Tác giả: Yamada Taro</p>
                        <p class="inter text-blue-200 text-sm mb-1">Người soạn: AuroraVN</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="orbitron text-yellow-300 font-bold">75.000đ</span>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors"
                                @click="product = { name: 'Aurora Concert', author: 'Yamada Taro', composer: 'AuroraVN', price: '75.000đ', img: 'https://via.placeholder.com/320x180?text=Aurora+Concert', video: 'https://www.youtube.com/embed/vd2WseQnruc' }; showDetail = true;">
                                Xem
                            </button>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="game-card rounded-xl p-4">
                        <div class="bg-gradient-to-br from-green-400 to-blue-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                            <span class="text-3xl">🎼</span>
                        </div>
                        <h4 class="orbitron font-bold text-white mb-2">Forest Theme</h4>
                        <p class="inter text-blue-200 text-sm mb-1">Tác giả: Lê Văn B</p>
                        <p class="inter text-blue-200 text-sm mb-1">Người soạn: ForestMaster</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="orbitron text-yellow-300 font-bold">30.000đ</span>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors"
                                @click="product = { name: 'Forest Theme', author: 'Lê Văn B', composer: 'ForestMaster', price: '30.000đ', img: 'https://via.placeholder.com/320x180?text=Forest+Theme', video: 'https://www.youtube.com/embed/dQw4w9WgXcQ' }; showDetail = true;">
                                Xem
                            </button>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="game-card rounded-xl p-4">
                        <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-lg mb-4 flex items-center justify-center" style="aspect-ratio: 16/9; width: 100%;">
                            <span class="text-3xl">🎹</span>
                        </div>
                        <h4 class="orbitron font-bold text-white mb-2">Valley Race</h4>
                        <p class="inter text-blue-200 text-sm mb-1">Tác giả: Trần Văn C</p>
                        <p class="inter text-blue-200 text-sm mb-1">Người soạn: ValleyKing</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="orbitron text-yellow-300 font-bold">40.000đ</span>
                            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-600 transition-colors"
                                @click="product = { name: 'Valley Race', author: 'Trần Văn C', composer: 'ValleyKing', price: '40.000đ', img: 'https://via.placeholder.com/320x180?text=Valley+Race', video: 'https://youtu.be/483FtCW_3po?si=zfDmr03D2ny2L-qE' }; showDetail = true;">
                                Xem
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- Popup chi tiết sản phẩm -->
    <div x-show="showDetail" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 relative flex flex-col gap-4">
                <button @click="showDetail=false" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>
                <div class="flex flex-col md:flex-row gap-6 items-center">
                    <div class="w-full md:w-1/3">
                        <div class="rounded-lg overflow-hidden" style="aspect-ratio: 16/9;">
                            <img :src="product.img" alt="Ảnh đại diện" class="object-cover w-full h-full" />
                        </div>
                    </div>
                    <div class="flex-1 flex flex-col gap-2">
                        <h3 class="orbitron text-2xl font-bold text-gray-900" x-text="product.name"></h3>
                        <p class="inter text-gray-700 text-base">Tác giả: <span class="font-semibold" x-text="product.author"></span></p>
                        <p class="inter text-gray-700 text-base">Người soạn: <span class="font-semibold" x-text="product.composer"></span></p>
                        <p class="orbitron text-blue-600 text-xl font-bold">Giá: <span x-text="product.price"></span></p>
                        <button class="bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-blue-600 transition w-fit mt-2">Thêm vào giỏ hàng</button>
                    </div>
                </div>
                <div class="mt-4">
                    <div style="position:relative;width:100%;aspect-ratio:16/9;">
                        <iframe :src="product.video" style="position:absolute;top:0;left:0;width:100%;height:100%;" class="rounded-lg shadow" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection