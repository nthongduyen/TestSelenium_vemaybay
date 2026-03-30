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
        Schema::create('hoa_don', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại liên kết với bảng 'booking'
            // Tên cột cần khớp với Factory và Model: id_booking
            $table->foreignId('id_booking')
                  ->constrained('booking') // Tên bảng booking
                  ->onDelete('restrict');

            $table->decimal('tong_tien', 15, 2)->comment('Tổng tiền phải trả sau giảm giá');

            // Cần có trạng thái cho Hóa Đơn (khớp với Factory)
            $table->enum('trang_thai', ['da_thanh_toan', 'chua_thanh_toan', 'da_huy'])->default('chua_thanh_toan');

            $table->string('phuong_thuc_tt', 50)->nullable();

            // Ngay_tao là CREATED_AT (tên cột là 'ngay_tao')
            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoa_don');
    }
};
