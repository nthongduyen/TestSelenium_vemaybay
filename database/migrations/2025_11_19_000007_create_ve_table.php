<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ve', function (Blueprint $table) {
            $table->id();

            // FK tới Booking (Test ITG-DV-01)
            $table->foreignId('id_booking')
                  ->constrained('booking')
                  ->onDelete('cascade');

            // FK tới ChuyenBay
            $table->foreignId('id_chuyen_bay')
                  ->constrained('chuyen_bay')
                  ->onDelete('restrict');

            $table->string('so_ghe', 10)->nullable()->comment('Số ghế');
            $table->decimal('gia_ve', 10, 2);

            // CẬP NHẬT: Thêm các trạng thái bị thiếu và cần thiết
            $table->enum('trang_thai', [
                // Các trạng thái gốc:
                'da_dat',
                'da_thanh_toan',
                'da_huy',
                // Trạng thái gây lỗi trong test của bạn:
                'da_xuat',
                // Các trạng thái tiếng Anh phổ biến trong Factory/Test:
                'booked',
                'checked_in',
                'used',
                'cancelled'
            ])->default('da_dat');

            // Không có timestamps (dựa trên Ve model)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ve');
    }
};
