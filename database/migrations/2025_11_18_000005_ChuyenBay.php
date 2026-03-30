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
        Schema::create('chuyen_bay', function (Blueprint $table) {
            $table->id();
            $table->string('ma_chuyen_bay', 10)->unique();

            // Khóa ngoại tới Máy Bay
            $table->foreignId('id_may_bay')
                  ->constrained('may_bay')
                  ->onDelete('restrict');

            // Khóa ngoại tới Sân Bay Đi
            $table->foreignId('id_san_bay_di')
                  ->constrained('san_bay')
                  ->onDelete('restrict');

            // Khóa ngoại tới Sân Bay Đến
            $table->foreignId('id_san_bay_den')
                  ->constrained('san_bay')
                  ->onDelete('restrict');

            $table->dateTime('thoi_gian_di');
            $table->dateTime('thoi_gian_den');
            $table->decimal('gia_ve', 10, 2);
            $table->enum('trang_thai', ['sap_bay', 'dang_ban', 'hoan_tat', 'da_huy'])->default('dang_ban');

            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chuyen_bay');
    }
};
