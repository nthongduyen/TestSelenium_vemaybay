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
        Schema::create('doanh_thu', function (Blueprint $table) {
            $table->id();

            // Các cột đã có (hoặc đã định nghĩa)
            $table->foreignId('id_hoa_don')
                ->constrained('hoa_don')
                ->onDelete('restrict')
                ->nullable();
            $table->decimal('tong_doanh_thu', 15, 2)->comment('Tổng doanh thu đã trừ giảm giá');
            $table->date('ngay_ghi_nhan');
            $table->string('loai_doanh_thu', 50)->comment('Phân loại doanh thu');

            // CÁC CỘT BỊ THIẾU CẦN PHẢI THÊM VÀO:
            $table->unsignedTinyInteger('thang')->nullable()->comment('Tháng thống kê');
            $table->year('nam')->nullable()->comment('Năm thống kê');
            $table->decimal('doanh_thu', 15, 2)->nullable()->comment('Giá trị doanh thu khác (Có thể là giá trị gốc)');

            // Cột ngày cập nhật (thay thế cho 'updated_at' nếu bạn không dùng timestamps Laravel)
            $table->timestamp('ngay_cap_nhat')->nullable();

            // Nếu bạn muốn có 'ngay_tao' thay cho 'created_at', hãy thêm:
            // $table->timestamp('ngay_tao')->useCurrent();
        });
    }
};
