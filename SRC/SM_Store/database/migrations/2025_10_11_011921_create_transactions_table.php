<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // Firebase UID
            $table->string('transaction_id')->unique(); // SePay transaction ID
            $table->decimal('amount', 15, 2); // Số tiền giao dịch
            $table->string('currency', 3)->default('VND'); // Loại tiền tệ
            $table->enum('type', ['deposit', 'withdrawal', 'transfer'])->default('deposit'); // Loại giao dịch
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending'); // Trạng thái
            $table->string('payment_method')->default('sepay'); // Phương thức thanh toán
            $table->json('sepay_data')->nullable(); // Dữ liệu từ SePay
            $table->text('description')->nullable(); // Mô tả giao dịch
            $table->string('reference_code')->nullable(); // Mã tham chiếu
            $table->timestamp('processed_at')->nullable(); // Thời gian xử lý
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
