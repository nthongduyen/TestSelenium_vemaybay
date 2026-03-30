<?php

namespace Database\Factories;

use App\Models\DoanhThu;
use App\Models\HoaDon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoanhThuFactory extends Factory
{
    /**
     * Tên model tương ứng với factory.
     */
    protected $model = DoanhThu::class;

    /**
     * Định nghĩa trạng thái mặc định của model DoanhThu.
     */
    public function definition(): array
    {
        // Tạo một Hóa Đơn đã thanh toán (nếu chưa tồn tại)
        $hoaDon = HoaDon::factory()->daThanhToan()->create();

        // Giả sử DoanhThu được ghi nhận là tổng tiền từ Hóa Đơn
        $tongTien = $hoaDon->tong_tien;

        return [
            'id_hoa_don' => $hoaDon->id,
            'tong_doanh_thu' => $tongTien,
            'ngay_ghi_nhan' => $hoaDon->ngay_tao->format('Y-m-d'),

            // Loại doanh thu (ví dụ: 'vé máy bay', 'dịch vụ')
            'loai_doanh_thu' => 've_may_bay',
        ];
    }
}
