<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_ghe_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ghe', function (Blueprint $table) {
            $table->id();
            // Khóa ngoại đến bảng may_bay
            $table->foreignId('id_may_bay')
                  ->constrained('may_bay') // Tên bảng là 'may_bay'
                  ->onDelete('cascade');

            $table->string('so_ghe', 5);
            $table->enum('loai_ghe', ['Business', 'Economy']);
            $table->enum('trang_thai', ['available', 'booked', 'sold']);
            // Bảng Ghe không cần timestamps
            // $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ghe');
    }

};
