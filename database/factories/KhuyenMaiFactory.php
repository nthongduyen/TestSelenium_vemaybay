<?php

namespace Database\Factories;

use App\Models\KhuyenMai;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class KhuyenMaiFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     *
     * @var string
     */
    protected $model = KhuyenMai::class;

    /**
     * Định nghĩa trạng thái mặc định của model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Sử dụng mã ngẫu nhiên cho khuyến mãi
        $maKhuyenMai = $this->faker->unique()->bothify('KM-#####');
        $loaiGiaTri = $this->faker->randomElement(['phan_tram', 'gia_tri_co_dinh']);

        $giaTri = ($loaiGiaTri === 'phan_tram')
            ? $this->faker->numberBetween(5, 50) // 5% đến 50%
            : $this->faker->numberBetween(50000, 500000); // 50,000 VND đến 500,000 VND

        return [
            'ma_khuyen_mai' => $maKhuyenMai,
            'mo_ta' => $this->faker->sentence(5),
            'gia_tri' => $giaTri,
            'loai_gia_tri' => $loaiGiaTri,
            // Đảm bảo ngày khuyến mãi hợp lệ (bắt đầu từ hôm qua, kết thúc sau vài ngày)
            'ngay_bat_dau' => Carbon::yesterday(),
            'ngay_ket_thuc' => Carbon::now()->addDays($this->faker->numberBetween(7, 30)),
            'trang_thai' => $this->faker->randomElement(['active', 'inactive', 'expired']),
        ];
    }
}
