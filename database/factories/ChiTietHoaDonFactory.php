<?php

namespace Database\Factories;

use App\Models\ChiTietHoaDon;
use App\Models\HoaDon;
use App\Models\Ve;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChiTietHoaDonFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = ChiTietHoaDon::class;

    /**
     * Định nghĩa trạng thái mặc định của model ChiTietHoaDon.
     */
    public function definition(): array
    {
        // 1. Tạo một đối tượng Ve (Ticket) làm dependency
        // Giả sử VeFactory đã tồn tại
        $ve = Ve::factory()->create();

        return [
            // 2. Liên kết với HoaDon (sẽ được tạo tự động nếu chưa có)
            'id_hoa_don' => HoaDon::factory(),

            // 3. Liên kết với Vé đã tạo
            'id_ve' => $ve->id,

            // 4. Số lượng vé trong chi tiết này (thường là 1)
            'so_luong' => 1,

            // 5. Giá của vé (lấy từ giá vé ban đầu)
            'gia' => $ve->gia_ve,
        ];
    }
}
