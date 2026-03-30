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
        // Tên bảng phải là 'nguoi_dung'
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->id();

            $table->string('ho_ten');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();

            // Tên cột mật khẩu phải là 'mat_khau'
            $table->string('mat_khau');

            $table->string('so_dien_thoai')->nullable();
            $table->string('dia_chi')->nullable();

            // Dùng enum cho vai trò (Admin, Nhân viên, Khách hàng)
            $table->enum('vai_tro', ['admin', 'nhan_vien', 'khach_hang'])->default('khach_hang');

            // Dùng boolean cho trạng thái (1: Kích hoạt, 0: Khóa)
            $table->boolean('trang_thai')->default(true)->comment('1: Kích hoạt, 0: Khóa');

            $table->rememberToken();

            // Chỉ dùng 'ngay_tao'
            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Đảm bảo tên bảng là 'nguoi_dung'
        Schema::dropIfExists('nguoi_dung');
    }
};
