<?php

namespace Database\Factories;

use App\Models\BaiViet;
use App\Models\NguoiDung;
use App\Models\DanhMucBaiViet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BaiVietFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = BaiViet::class;

    /**
     * Định nghĩa trạng thái mặc định của model.
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);

        return [
            'tieu_de' => $title,
            'slug' => Str::slug($title),
            // Tự động tạo Tác giả và Danh mục nếu chưa được cung cấp
            'id_tac_gia' => NguoiDung::factory(),
            'id_danh_muc' => DanhMucBaiViet::factory(),
            'noi_dung' => $this->faker->paragraphs(5, true),
            'trang_thai' => $this->faker->randomElement(['draft', 'published']),
            'ngay_dang' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
