@extends('layouts.app')

@section('title', 'Hỗ trợ - Sky Music Store')

@section('content')
<div id="support" class="page-content">
        <section class="relative z-10 py-20 px-6">
            <div class="max-w-4xl mx-auto">
                <h2 class="orbitron text-5xl font-bold text-white text-center mb-16">🛠️ Hỗ Trợ Khách Hàng</h2>
                
                <!-- FAQ -->
                <div class="game-card rounded-xl p-8 mb-8">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Câu Hỏi Thường Gặp</h3>
                    <div class="space-y-4">
                        <div class="border-b border-white border-opacity-20 pb-4">
                            <h4 class="inter font-semibold text-white mb-2">Làm sao để tải sheet nhạc sau khi mua?</h4>
                            <p class="inter text-blue-200">Sau khi thanh toán thành công, bạn sẽ nhận được link tải về email đã đăng ký.</p>
                        </div>
                        <div class="border-b border-white border-opacity-20 pb-4">
                            <h4 class="inter font-semibold text-white mb-2">Sheet nhạc có định dạng gì?</h4>
                            <p class="inter text-blue-200">Chúng tôi cung cấp sheet ở định dạng PDF và MIDI cho Sky Studio.</p>
                        </div>
                        <div class="border-b border-white border-opacity-20 pb-4">
                            <h4 class="inter font-semibold text-white mb-2">Có thể hoàn tiền không?</h4>
                            <p class="inter text-blue-200">Chúng tôi hỗ trợ hoàn tiền trong vòng 7 ngày nếu có vấn đề về chất lượng.</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="game-card rounded-xl p-8">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Liên Hệ Với Chúng Tôi</h3>
                    <form class="space-y-4">
                        <div>
                            <label class="inter text-white block mb-2">Họ và tên</label>
                            <input type="text" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập họ và tên">
                        </div>
                        <div>
                            <label class="inter text-white block mb-2">Email</label>
                            <input type="email" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Nhập email">
                        </div>
                        <div>
                            <label class="inter text-white block mb-2">Vấn đề</label>
                            <select class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                <option>Vấn đề thanh toán</option>
                                <option>Vấn đề tải file</option>
                                <option>Chất lượng sheet nhạc</option>
                                <option>Khác</option>
                            </select>
                        </div>
                        <div>
                            <label class="inter text-white block mb-2">Nội dung</label>
                            <textarea rows="4" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white placeholder-blue-200 border border-white border-opacity-30" placeholder="Mô tả chi tiết vấn đề của bạn"></textarea>
                        </div>
                        <button type="submit" class="glow-button bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-full font-semibold">Gửi Yêu Cầu</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection