<?php

namespace Database\Factories;

use App\Models\ChuyenBay;
use App\Models\MayBay;
use App\Models\SanBay;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChuyenBayFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = ChuyenBay::class;

    /**
     * Định nghĩa trạng thái mặc định của model ChuyenBay.
     */
    public function definition(): array
    {
        // 1. Lấy hoặc tạo Máy Bay và Sân Bay
        // (Nếu chưa có, Laravel sẽ tự động gọi Factory tương ứng)

        // Bạn nên tạo MayBayFactory và SanBayFactory nếu chúng chưa tồn tại.
        $mayBay = MayBay::factory();
        $sanBayDi = SanBay::factory();
        $sanBayDen = SanBay::factory();

        $departureTime = $this->faker->dateTimeBetween('+1 week', '+1 month');
        $arrivalTime = (clone $departureTime)->modify('+3 hours');

        return [
            'ma_chuyen_bay' => $this->faker->unique()->bothify('VN-####'),

            // Liên kết các khóa ngoại
            'id_may_bay' => $mayBay,
            'id_san_bay_di' => $sanBayDi,
            'id_san_bay_den' => $sanBayDen,

            'thoi_gian_di' => $departureTime,
            'thoi_gian_den' => $arrivalTime,
            'gia_ve' => $this->faker->numberBetween(1000000, 5000000),
            'trang_thai' => 'dang_ban',
        ];
    }
}
