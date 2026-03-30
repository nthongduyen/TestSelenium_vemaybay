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
        Schema::create('may_bay', function (Blueprint $table) {
            $table->id();
            $table->string('ma_may_bay', 10)->unique()->comment('Mã định danh máy bay');
            $table->string('ten_may_bay', 100)->comment('Tên hoặc số hiệu máy bay');
            $table->string('hang_san_xuat', 50)->comment('Hãng sản xuất (ví dụ: Boeing, Airbus)');
            $table->unsignedSmallInteger('so_ghe')->comment('Tổng số ghế');

            // ĐÃ SỬA: Thêm 'active' vào danh sách ENUM
            $table->enum('trang_thai', ['hoat_dong', 'bao_tri', 'ngung_su_dung', 'active'])->default('hoat_dong');

            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('may_bay');
    }
};
