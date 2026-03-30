<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();

            // FK tới NguoiDung (Test ITG-DV-03)
            $table->foreignId('id_nguoi_dung')
                  ->constrained('nguoi_dung')
                  ->onDelete('cascade');

            $table->string('ma_booking', 20)->unique()->comment('Mã Booking');
            $table->decimal('tong_tien', 10, 2);
            //$table->enum('trang_thai', ['dat_thanh_cong', 'da_huy', 'cho_thanh_toan'])->default('cho_thanh_toan');
            $table->enum('trang_thai', ['pending', 'confirmed', 'cancelled', 'paid', 'thanh_cong', 'da_huy','dat_thanh_cong'])->default('pending');
            $table->string('phuong_thuc_tt', 50)->nullable();

            // FK tới KhuyenMai (nullable)
            $table->foreignId('id_khuyen_mai')
                  ->nullable()
                  ->constrained('khuyen_mai')
                  ->onDelete('set null');

            // CREATED_AT: ngay_dat (dựa trên Booking model)
            $table->timestamp('ngay_dat')->useCurrent();
            // Không có UPDATED_AT (dựa trên Booking model: const UPDATED_AT = null;)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
