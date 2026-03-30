<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy các migrations.
     */
    public function up(): void
    {
        // Tên bảng: danh_muc_bai_viet (theo lỗi SQL của bạn)
        Schema::create('danh_muc_bai_viet', function (Blueprint $table) {
            $table->id();

            $table->string('ten_danh_muc', 255);
            $table->string('slug', 255)->unique();
            $table->enum('trang_thai', ['active', 'inactive'])->default('active');

            // Cột ngay_tao: Được suy ra từ lỗi SQL của bạn
            $table->timestamp('ngay_tao')->useCurrent();

            // Khóa ngoại đệ quy: id_danh_muc_cha (nullable cho Danh mục Cha)
            // Liên kết tới chính id của bảng này
            $table->foreignId('id_danh_muc_cha')
                  ->nullable()
                  ->constrained('danh_muc_bai_viet') // Tự tham chiếu
                  ->onDelete('cascade'); // Xóa danh mục cha sẽ xóa các danh mục con

            // Nếu bạn có các cột timestamp mặc định (created_at, updated_at) thì thay thế
            // $table->timestamp('ngay_tao')->useCurrent(); bằng $table->timestamps();
            // Nhưng hiện tại tôi giữ theo tên cột 'ngay_tao' trong lỗi của bạn.
        });
    }

    /**
     * Hoàn tác các migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_bai_viet');
    }
};
