<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Tên model tương ứng với factory này.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Định nghĩa trạng thái mặc định của model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // QUAN TRỌNG: Đã loại bỏ cột 'password' mặc định
        // vì cơ sở dữ liệu của bạn chỉ có cột 'mat_khau'.

        return [
            // Giả định cột Họ Tên của bạn là 'ho_ten'
            'ho_ten' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            // CHỈ DÙNG cột 'mat_khau'
            'mat_khau' => static::$password ?? Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    // Giữ nguyên phần xử lý remember_token
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
