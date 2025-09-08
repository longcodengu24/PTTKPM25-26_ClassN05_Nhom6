<!-- filepath: resources/views/admin/products.blade.php -->
@extends('layouts.admin')

@section('title', 'Cài Đặt')

@section('content')
 <!-- Settings -->
            <div id="settings" class="admin-content active px-6 pb-6">
                <div class="admin-card rounded-xl p-6">
                    <h3 class="orbitron text-2xl font-bold text-white mb-6">Cài Đặt Hệ Thống</h3>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- General Settings -->
                        <div>
                            <h4 class="orbitron text-lg font-bold text-white mb-4">Cài Đặt Chung</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="inter text-white block mb-2">Tên Website</label>
                                    <input type="text" value="Sky Music Store" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                                <div>
                                    <label class="inter text-white block mb-2">Email Liên Hệ</label>
                                    <input type="email" value="admin@skymusic.com" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                                <div>
                                    <label class="inter text-white block mb-2">Số Điện Thoại</label>
                                    <input type="tel" value="0123456789" class="w-full p-3 rounded-lg bg-white bg-opacity-20 text-white border border-white border-opacity-30">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Settings -->
                        <div>
                            <h4 class="orbitron text-lg font-bold text-white mb-4">Cài Đặt Thanh Toán</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">Momo</p>
                                        <p class="text-gray-300 text-sm inter">Thanh toán qua ví Momo</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">ZaloPay</p>
                                        <p class="text-gray-300 text-sm inter">Thanh toán qua ZaloPay</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between p-4 bg-white bg-opacity-5 rounded-lg">
                                    <div>
                                        <p class="text-white font-semibold inter">Chuyển Khoản</p>
                                        <p class="text-gray-300 text-sm inter">Chuyển khoản ngân hàng</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button class="bg-blue-500 hover:bg-blue-600 px-8 py-3 rounded-lg text-white inter font-semibold">
                            Lưu Cài Đặt
                        </button>
                    </div>
                </div>
            </div>
@endsection