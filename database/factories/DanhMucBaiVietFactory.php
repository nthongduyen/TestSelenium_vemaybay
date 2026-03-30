<?php

namespace Database\Factories;

use App\Models\DanhMucBaiViet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DanhMucBaiVietFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = DanhMucBaiViet::class;

    /**
     * Định nghĩa trạng thái mặc định của model.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'ten_danh_muc' => $name,
            'slug' => Str::slug($name),
            // id_danh_muc_cha mặc định là null (danh mục cha)
            'id_danh_muc_cha' => null,
            'trang_thai' => $this->faker->randomElement(['active', 'inactive']),
            // ĐÃ BỎ: Giả định Model DanhMucBaiViet sử dụng timestamps mặc định hoặc không có timestamps.
            // Nếu bạn có cột 'ngay_tao' trong Migration, bạn cần thêm lại 'ngay_tao' => now(),
        ];
    }
}
