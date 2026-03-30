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
        Schema::create('chi_tiet_hoa_don', function (Blueprint $table) {
            // Không cần $table->id() nếu đây là bảng pivot hoặc dùng khóa kép
            // Nhưng để đơn giản, ta vẫn dùng ID chính
            $table->id();

            // Khóa ngoại liên kết với bảng 'hoa_don'
            $table->foreignId('id_hoa_don')
                  ->constrained('hoa_don')
                  ->onDelete('cascade');

            // Khóa ngoại liên kết với bảng 've'
            $table->foreignId('id_ve')
                  ->constrained('ve')
                  ->onDelete('restrict');

            $table->unsignedSmallInteger('so_luong')->default(1);
            $table->decimal('gia', 10, 2)->comment('Giá gốc của vé/dịch vụ tại thời điểm thanh toán');

            // Đặt khóa kép (tùy chọn, nếu bạn muốn đảm bảo 1 vé chỉ có 1 chi tiết HĐ)
            // $table->unique(['id_hoa_don', 'id_ve']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_hoa_don');
    }
};
