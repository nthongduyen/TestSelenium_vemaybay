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
        Schema::table('chuyen_bay', function (Blueprint $table) {
            // Thay đổi kiểu dữ liệu của cột 'trang_thai' thành string với độ dài 20
            // Điều này đảm bảo nó có thể chứa các trạng thái như 'active', 'cancelled', v.v.
            $table->string('trang_thai', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chuyen_bay', function (Blueprint $table) {
            // Nếu muốn rollback, đổi lại về kiểu dữ liệu cũ (giả sử là string 10,
            // bạn có thể điều chỉnh lại nếu nhớ kiểu dữ liệu ban đầu).
            $table->string('trang_thai', 10)->change();
        });
    }
};
