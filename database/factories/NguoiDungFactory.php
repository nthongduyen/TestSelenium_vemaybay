<?php

namespace Database\Factories;

use App\Models\NguoiDung;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NguoiDung>
 */
class NguoiDungFactory extends Factory
{
    /**
     * Tên Model tương ứng với Factory này.
     *
     * @var string
     */
    protected $model = NguoiDung::class;

    /**
     * Định nghĩa các thuộc tính mặc định của Model NguoiDung.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ho_ten' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            //'mat_khau' => Hash::make('password'),
            'mat_khau' => bcrypt('password'),
            'so_dien_thoai' => $this->faker->phoneNumber,
            'dia_chi' => $this->faker->address,

            // Đặt vai trò mặc định là 'khach_hang' theo yêu cầu mới
            'vai_tro' => 'khach_hang',

           // 'trang_thai' => 1, // Kích hoạt
           'trang_thai' => true,
            'ngay_tao' => now(),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Chỉ định vai_tro là 'admin'.
     */
    public function admin(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'vai_tro' => 'admin',
        ]);
    }

    /**
     * Chỉ định vai_tro là 'user' (người dùng thường).
     * Do database không cho phép 'user', ta dùng 'khach_hang' thay thế
     */
    public function user(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'vai_tro' => 'khach_hang', // <-- ĐÃ SỬA TỪ 'user' THÀNH 'khach_hang'
        ]);
    }
}
