<?php

namespace Database\Factories;

use App\Models\HoaDon;
use App\Models\Booking; // Hóa Đơn phụ thuộc vào Booking
use Illuminate\Database\Eloquent\Factories\Factory;

class HoaDonFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = HoaDon::class;

    /**
     * Định nghĩa trạng thái mặc định của model HoaDon.
     */
    public function definition(): array
    {
        // Đảm bảo có một Booking được tạo trước
        // (Booking Factory cần đảm bảo có NguoiDung và KhuyenMai)
        $booking = Booking::factory();

        return [
            'id_booking' => $booking,
            // Giá trị ngẫu nhiên trong khoảng có lý
            'tong_tien' => $this->faker->numberBetween(1000000, 5000000),
            // Đảm bảo các giá trị ENUM khớp với Migration của bảng 'hoa_don'
            'trang_thai' => $this->faker->randomElement(['da_thanh_toan', 'chua_thanh_toan', 'da_huy']),
            'phuong_thuc_tt' => $this->faker->randomElement(['Chuyển khoản', 'Tiền mặt', 'Ví điện tử']),
        ];
    }

    /**
     * State: Hóa đơn đã được thanh toán (rất hay dùng trong test).
     */
    public function daThanhToan()
    {
        return $this->state(function (array $attributes) {
            return [
                'trang_thai' => 'da_thanh_toan',
            ];
        });
    }
}
