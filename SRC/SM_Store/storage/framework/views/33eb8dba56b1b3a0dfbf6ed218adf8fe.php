<?php $__env->startSection('title', 'Trang chủ'); ?>

<?php $__env->startSection('content'); ?>
     <div id="home" class="page-content active">
        <!-- Hero Section -->
        <section class="relative z-10 text-center py-20 px-6">
            <div class="max-w-4xl mx-auto">
                <h2 class="orbitron text-6xl md:text-8xl font-black text-white mb-6 leading-tight">
                    SKY MUSIC
                </h2>
                <p class="inter text-xl md:text-2xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Khám phá bộ sưu tập sheet nhạc Sky đầy đủ nhất. Từ những giai điệu quen thuộc đến những bản nhạc độc quyền mới nhất.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="<?php echo e(url('/shop')); ?>" class="glow-button bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-full text-lg font-semibold inter" onclick="showPage('shop')">
                        🎼 Khám Phá Shop
                    </a>
                    <a href="https://www.youtube.com/@kchip2858" class="bg-white bg-opacity-20 text-white px-8 py-4 rounded-full text-lg font-semibold backdrop-blur-sm hover:bg-opacity-30 transition-all inter">
                        🎵 Nghe Demo
                    </a>
                </div>
                
                <!-- Game Preview -->
                <div class="game-card rounded-2xl p-8 max-w-3xl mx-auto">
                    <!-- Video hoặc ảnh -->
                    <div class="bg-gradient-to-br from-blue-400 to-purple-500 rounded-xl flex items-center justify-center mb-6">
                        <div class="relative w-full" style="padding-bottom: 56.25%; /* Tỷ lệ 16:9 */">
                            <iframe 
                                class="absolute top-0 left-0 w-full h-full rounded-xl" 
                                src="https://www.youtube.com/embed/dHB-VM7iQUI" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>

                    <!-- Tiêu đề và mô tả -->
                    <h3 class="orbitron text-2xl font-bold text-white mb-3">
                        <a href="<?php echo e(url('/new-page')); ?>" class="hover:text-yellow-300 transition-colors">Trải Nghiệm Bay Lượn Tự Do</a>
                    </h3>
                    <p class="inter text-blue-100">
                        Điều khiển phi cơ của bạn qua những đám mây, khám phá các hòn đảo bay và thu thập năng lượng sao để nâng cấp.
                    </p>
                </div>
        </section>

        <!-- Features Section -->
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-6xl mx-auto">
                <h3 class="orbitron text-4xl font-bold text-white text-center mb-16">Tại Sao Chọn Chúng Tôi</h3>
                
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="game-card rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🎵</span>
                        </div>
                        <h4 class="orbitron text-xl font-bold text-white mb-3">Sheet Chất Lượng Cao</h4>
                        <p class="inter text-blue-100">Tất cả sheet nhạc được chuyển đổi chính xác từ game Sky</p>
                    </div>
                    
                    <div class="game-card rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">⚡</span>
                        </div>
                        <h4 class="orbitron text-xl font-bold text-white mb-3">Cập Nhật Liên Tục</h4>
                        <p class="inter text-blue-100">Bổ sung sheet nhạc mới từ các season và event mới nhất</p>
                    </div>
                    
                    <div class="game-card rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🏆</span>
                        </div>
                        <h4 class="orbitron text-xl font-bold text-white mb-3">Cộng Đồng Sôi Động</h4>
                        <p class="inter text-blue-100">Chia sẻ và học hỏi từ cộng đồng yêu nhạc Sky</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="relative z-10 py-16 px-6">
            <div class="max-w-4xl mx-auto">
                <div class="game-card rounded-xl p-8">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                        <div>
                            <div class="orbitron text-3xl font-bold text-white mb-2">500+</div>
                            <div class="inter text-blue-200">Sheet Nhạc</div>
                        </div>
                        <div>
                            <div class="orbitron text-3xl font-bold text-white mb-2">10K+</div>
                            <div class="inter text-blue-200">Người Dùng</div>
                        </div>
                        <div>
                            <div class="orbitron text-3xl font-bold text-white mb-2">50+</div>
                            <div class="inter text-blue-200">Bài Mới/Tháng</div>
                        </div>
                        <div>
                            <div class="orbitron text-3xl font-bold text-white mb-2">4.9★</div>
                            <div class="inter text-blue-200">Đánh Giá</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/page/home/index.blade.php ENDPATH**/ ?>