<?php
// File: Database/Factories/MayBayFactory.php

namespace Database\Factories;

use App\Models\MayBay;
use Illuminate\Database\Eloquent\Factories\Factory;

class MayBayFactory extends Factory
{
    protected $model = MayBay::class;

    public function definition(): array
    {
        return [
            // Cột 'ma_may_bay' (Không phải 'ma_san_bay')
            'ma_may_bay' => $this->faker->unique()->bothify('MB-####'),

            // Cột 'ten_may_bay' (Không phải 'ten_san_bay')
            'ten_may_bay' => $this->faker->company() . ' Air',

            // Cột 'hang_san_xuat' (Không phải 'quoc_gia' hay 'thanh_pho')
            'hang_san_xuat' => $this->faker->randomElement(['Boeing', 'Airbus', 'Embraer']),

            // Cột 'so_ghe' (Cột này chỉ có trong bảng may_bay)
            'so_ghe' => $this->faker->numberBetween(100, 300),

            'trang_thai' => $this->faker->randomElement(['hoat_dong', 'bao_tri']),
        ];
    }
}
