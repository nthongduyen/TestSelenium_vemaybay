<?php

namespace Database\Factories;

use App\Models\SanBay;
use Illuminate\Database\Eloquent\Factories\Factory;

class SanBayFactory extends Factory
{
    protected $model = SanBay::class;

    public function definition(): array
    {
        return [
            //  Sửa thành 5 ký tự: 'S?B??'
            //'ma_san_bay' => $this->faker->unique()->lexify('S?N???'),
            'ma_san_bay' => $this->faker->unique()->lexify('???'),
            'ten_san_bay' => $this->faker->city() . ' International Airport',
            'tinh_thanh' => $this->faker->city(), // **SỬA LỖI:** Thay 'thanh_pho' bằng 'tinh_thanh'
            'quoc_gia' => $this->faker->country(),
            'dia_chi' => $this->faker->address(),
        ];
    }
}
