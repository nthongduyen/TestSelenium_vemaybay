<?php

namespace Database\Factories;

use App\Models\Ve;
use App\Models\Booking;
use App\Models\ChuyenBay;
use Illuminate\Database\Eloquent\Factories\Factory;

class VeFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = Ve::class;

    /**
     * Định nghĩa trạng thái mặc định của model Ve.
     */
    public function definition(): array
    {
        // Đảm bảo các Model Booking và ChuyenBay được tạo trước
        $booking = Booking::factory();
        $chuyenBay = ChuyenBay::factory();

        return [
            'id_booking' => $booking,
            'id_chuyen_bay' => $chuyenBay,

            // Vì bạn chưa có Model Ghe, ta giả định số ghế là một string ngẫu nhiên.
            'so_ghe' => $this->faker->regexify('[A-Z]{1}[0-9]{2}'),

            // Gia_ve có thể được lấy từ chuyến bay, nhưng ta tạo giá trị ngẫu nhiên để đơn giản hóa.
            'gia_ve' => $this->faker->numberBetween(800000, 4500000),

            'trang_thai' => $this->faker->randomElement(['da_dat', 'da_thanh_toan', 'da_huy']),
        ];
    }
}
