<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\NguoiDung;
use App\Models\KhuyenMai;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Định nghĩa trạng thái mặc định của model Booking.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Liên kết tới NguoiDungFactory vừa tạo
            'id_nguoi_dung' => NguoiDung::factory(),

            'ma_booking' => 'BOOK-' . Str::upper(Str::random(6)),
            // Tổng tiền ngẫu nhiên
            'tong_tien' => $this->faker->numberBetween(1000000, 10000000),

            // Trạng thái (thường dùng 'confirmed' hoặc 'pending' cho Booking)
            'trang_thai' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'paid']),

            'phuong_thuc_tt' => $this->faker->randomElement(['Chuyển khoản', 'Credit/Debit Card']),

            // Mặc định không áp dụng khuyến mãi
            'id_khuyen_mai' => null,
        ];
    }

    /**
     * State: Tạo một Booking ở trạng thái 'paid' (Đã thanh toán)
     */
    public function paid(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'trang_thai' => 'paid',
        ]);
    }
}
