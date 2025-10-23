<?php $__env->startSection('title', 'Dashboard - Thống kê'); ?>

<?php $__env->startSection('content'); ?>
<div class="p-6">
    <?php if(isset($error)): ?>
        <div class="bg-red-500 bg-opacity-20 border border-red-500 text-red-200 px-4 py-3 rounded-lg mb-6">
            <?php echo e($error); ?>

        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="orbitron text-2xl font-bold text-white">📈 Dashboard & Thống kê</h2>
        <div class="flex items-center space-x-3">
            <select class="px-4 py-2 bg-white bg-opacity-10 border border-gray-300 border-opacity-30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="7">7 ngày qua</option>
                <option value="30">30 ngày qua</option>
                <option value="90">3 tháng qua</option>
                <option value="365">1 năm qua</option>
            </select>
            <button class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg text-white inter flex items-center space-x-2">
                <span>📊</span>
                <span>Xuất báo cáo</span>
            </button>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Doanh thu</p>
                    <p class="text-2xl font-bold text-white orbitron"><?php echo e(number_format($stats['total_revenue'] ?? 0)); ?></p>
                    <p class="text-gray-400 text-sm">VND</p>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">💰</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tổng đơn hàng</p>
                    <p class="text-2xl font-bold text-white orbitron"><?php echo e(number_format($stats['total_orders'] ?? 0)); ?></p>
                    <p class="text-gray-400 text-sm">Đơn</p>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🛒</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Sản phẩm</p>
                    <p class="text-2xl font-bold text-white orbitron"><?php echo e(number_format($stats['total_products'] ?? 0)); ?></p>
                    <p class="text-gray-400 text-sm">Sheet nhạc</p>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">🎼</span>
                </div>
            </div>
        </div>
        
        <div class="admin-card rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-300 text-sm inter">Tỷ lệ hoàn thành</p>
                    <p class="text-2xl font-bold text-white orbitron"><?php echo e($stats['conversion_rate'] ?? 0); ?>%</p>
                    <p class="text-gray-400 text-sm"><?php echo e($stats['completed_orders'] ?? 0); ?>/<?php echo e($stats['total_orders'] ?? 0); ?></p>
                </div>
                <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                    <span class="text-xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue Chart -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">📈 Doanh thu 7 ngày gần nhất</h3>
            
            <!-- Vertical Bar Chart -->
            <div class="bg-gray-800 bg-opacity-50 rounded-lg p-4">
                <div class="h-56 flex items-end justify-around gap-2">
                    <?php if(!empty($stats['chart_data']['values'])): ?>
                        <?php $__currentLoopData = $stats['chart_data']['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $percentage = $stats['chart_data']['max'] > 0 ? ($value / $stats['chart_data']['max']) * 100 : 0;
                                $height = max(5, $percentage); // Minimum 5%
                                
                                $colors = [
                                    '#a855f7', // purple
                                    '#3b82f6', // blue
                                    '#10b981', // green
                                    '#f59e0b', // amber
                                    '#ef4444', // red
                                    '#6366f1', // indigo
                                    '#14b8a6'  // teal
                                ];
                                $color = $colors[$index % 7];
                            ?>
                            
                            <div class="flex-1 flex flex-col items-center group">
                                <!-- Bar Container -->
                                <div class="w-full h-48 flex items-end justify-center mb-2 relative">
                                    <!-- Bar -->
                                    <div class="w-full rounded-t-lg transition-all duration-500 hover:brightness-125 cursor-pointer chart-bar relative"
                                         style="height: <?php echo e($height); ?>%; background: linear-gradient(to top, <?php echo e($color); ?>, <?php echo e($color); ?>dd); box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                                        
                                        <!-- Tooltip on hover -->
                                        <div class="absolute -top-16 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs rounded-lg py-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap z-10 pointer-events-none">
                                            <div class="font-bold text-center"><?php echo e(number_format($value, 0, ',', '.')); ?> đ</div>
                                            <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-full">
                                                <div class="w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Day Label -->
                                <div class="text-white text-sm font-bold">
                                    <?php echo e($stats['chart_data']['labels'][$index] ?? ''); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="w-full text-center py-8">
                            <p class="text-gray-400">Chưa có dữ liệu doanh thu</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Summary -->
            <div class="mt-4 grid grid-cols-2 gap-3">
                <div class="bg-blue-500 bg-opacity-20 rounded-lg p-3 border border-blue-500 border-opacity-30">
                    <div class="text-gray-300 text-xs mb-1">Tổng doanh thu</div>
                    <div class="text-white font-bold text-sm"><?php echo e(number_format($stats['total_revenue'], 0, ',', '.')); ?> đ</div>
                </div>
                <?php if($stats['chart_data']['max'] > 0): ?>
                    <div class="bg-green-500 bg-opacity-20 rounded-lg p-3 border border-green-500 border-opacity-30">
                        <div class="text-gray-300 text-xs mb-1">Ngày cao nhất</div>
                        <div class="text-white font-bold text-sm"><?php echo e(number_format($stats['chart_data']['max'], 0, ',', '.')); ?> đ</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Products -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">🏆 Sheet nhạc bán chạy</h3>
            <?php if(empty($stats['top_products'])): ?>
                <div class="text-center py-8">
                    <p class="text-gray-300">Chưa có sản phẩm nào được bán</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php
                        $badgeColors = ['bg-yellow-500', 'bg-gray-400', 'bg-orange-500', 'bg-blue-500', 'bg-purple-500'];
                    ?>
                    <?php $__currentLoopData = $stats['top_products']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 <?php echo e($badgeColors[$index] ?? 'bg-gray-500'); ?> rounded-lg flex items-center justify-center text-white font-bold">
                                <?php echo e($index + 1); ?>

                            </div>
                            <div>
                                <p class="text-white font-medium"><?php echo e($product['name']); ?></p>
                                <p class="text-gray-300 text-sm"><?php echo e(number_format($product['revenue'])); ?> VND</p>
                            </div>
                        </div>
                        <span class="text-white font-medium"><?php echo e($product['count']); ?> bán</span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Additional Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">� Trạng thái đơn hàng</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tổng đơn hàng</span>
                    <span class="text-white font-medium"><?php echo e(number_format($stats['total_orders'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Đã hoàn thành</span>
                    <span class="text-green-400 font-medium"><?php echo e(number_format($stats['completed_orders'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Đang xử lý</span>
                    <span class="text-yellow-400 font-medium"><?php echo e(number_format($stats['pending_orders'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tỷ lệ hoàn thành</span>
                    <span class="text-white font-medium"><?php echo e($stats['conversion_rate'] ?? 0); ?>%</span>
                </div>
            </div>
        </div>

        <!-- Revenue Summary -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">💰 Tổng quan doanh thu</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tổng doanh thu</span>
                    <span class="text-white font-medium"><?php echo e(number_format($stats['total_revenue'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Trung bình/đơn</span>
                    <span class="text-white font-medium">
                        <?php echo e($stats['total_orders'] > 0 ? number_format($stats['total_revenue'] / $stats['total_orders']) : 0); ?> VND
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Sản phẩm đang bán</span>
                    <span class="text-white font-medium"><?php echo e(number_format($stats['total_products'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Trung bình/sản phẩm</span>
                    <span class="text-white font-medium">
                        <?php echo e($stats['total_products'] > 0 ? number_format($stats['total_revenue'] / $stats['total_products']) : 0); ?> VND
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="admin-card rounded-xl p-6">
            <h3 class="text-xl font-bold text-white mb-4 orbitron">⚡ Thống kê nhanh</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Sản phẩm bán chạy nhất</span>
                    <span class="text-white font-medium">
                        <?php echo e(!empty($stats['top_products']) ? $stats['top_products'][0]['count'] : 0); ?> bán
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tổng lượt bán</span>
                    <span class="text-white font-medium"><?php echo e(number_format($stats['total_orders'] ?? 0)); ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Tỷ lệ thành công</span>
                    <span class="text-green-400 font-medium"><?php echo e($stats['conversion_rate'] ?? 0); ?>%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Sheet nhạc khác nhau</span>
                    <span class="text-white font-medium"><?php echo e(count($stats['top_products'] ?? [])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes growUp {
    from {
        height: 0%;
    }
}

.chart-bar {
    animation: growUp 1s ease-out forwards;
}

.chart-bar:nth-child(1) { animation-delay: 0.1s; }
.chart-bar:nth-child(2) { animation-delay: 0.2s; }
.chart-bar:nth-child(3) { animation-delay: 0.3s; }
.chart-bar:nth-child(4) { animation-delay: 0.4s; }
.chart-bar:nth-child(5) { animation-delay: 0.5s; }
.chart-bar:nth-child(6) { animation-delay: 0.6s; }
.chart-bar:nth-child(7) { animation-delay: 0.7s; }
</style>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/saler/dashboard.blade.php ENDPATH**/ ?>