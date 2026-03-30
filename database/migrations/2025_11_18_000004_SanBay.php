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
        Schema::create('san_bay', function (Blueprint $table) {
            $table->id();
            $table->string('ma_san_bay', 5)->unique()->comment('Mã sân bay (ví dụ: SGN, HAN)');
            $table->string('ten_san_bay', 150);

            // ĐÃ SỬA: Thêm ->nullable() để cho phép cột này không có giá trị
            $table->string('tinh_thanh', 50)->nullable();

            $table->string('quoc_gia', 50);
            $table->timestamp('ngay_tao')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_bay');
    }
};
