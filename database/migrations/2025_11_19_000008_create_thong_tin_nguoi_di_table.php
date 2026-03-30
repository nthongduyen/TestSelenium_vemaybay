<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_tin_nguoi_di', function (Blueprint $table) {
            $table->id();

            // FK tới Ve (UNIQUE vì Ve hasOne ThongTinNguoiDi) (Test ITG-DV-02)
            $table->foreignId('id_ve')
                  ->unique()
                  ->constrained('ve')
                  ->onDelete('cascade');

            $table->string('ho_ten', 100);
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('dia_chi', 255)->nullable();
            $table->text('ghi_chu')->nullable();

            // Không có timestamps (dựa trên ThongTinNguoiDi model)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_tin_nguoi_di');
    }
};
