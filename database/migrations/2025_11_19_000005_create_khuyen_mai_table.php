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
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khuyen_mai', 20)->unique();
            $table->text('mo_ta')->nullable();

            // Khắc phục lỗi: loai_gia_tri phải là ENUM và chứa giá trị dài nhất.
            $table->enum('loai_gia_tri', ['phan_tram', 'gia_tri_co_dinh']);

            $table->decimal('gia_tri', 10, 2);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');

            // Khắc phục lỗi: trang_thai phải là ENUM và chứa giá trị dài nhất (active, inactive, expired)
            $table->enum('trang_thai', ['active', 'inactive', 'expired']);

            // Bảng này không dùng timestamp mặc định (dựa trên KhuyenMai.php)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
