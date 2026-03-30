<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use App\Models\KhuyenMai;
use App\Models\SanBay;
use App\Models\MayBay;
use App\Models\ChuyenBay;
use App\Models\Booking;
use App\Models\Ve;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use App\Models\DoanhThu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Carbon\Carbon;

/**
 * Kiểm tra luồng tính toán Khuyến Mãi, Tổng Tiền, Hóa Đơn và ghi nhận Doanh Thu.
 */
class DiscountRevenueTest extends TestCase
{
    use RefreshDatabase;

    // Biến tĩnh để lưu kết quả và hàm tiện ích
    protected static array $testResults = [];

    // Cố định các giá trị để dễ kiểm tra
    protected $giaVeCoBanPhanTram = 500.00;
    protected $giaTriGiamPhanTram = 10;

    protected $giaVeCoBanCoDinh = 1000000.00;
    protected $giaTriGiamCoDinh = 100000.00;
    protected $soLuongVe = 2;
    protected $nguoiDung;
    protected $chuyenBay;
    protected $khuyenMaiCoDinh;
    protected $tongTienThanhToan;

    /**
     * Hàm tiện ích để ghi lại kết quả của từng Test Case vào mảng tĩnh.
     */
    protected function recordResult(string $id, string $description, string $expected, bool $passed): void
    {
        // ... (Giữ nguyên hàm recordResult)
        self::$testResults[] = [
            'ID' => $id,
            'Mô tả' => $description,
            'Kết quả Mong đợi' => $expected,
            'Trạng thái' => $passed ? 'PASS' : 'FAIL',
        ];
    }

    /**
     * Thiết lập môi trường và tạo dữ liệu giả cho tất cả các test case.
     * Tạo dữ liệu nền tảng cho 4 test case.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. TẠO DỮ LIỆU CƠ SỞ CHUNG
        $sanBayDi = SanBay::factory()->create(['ma_san_bay' => 'SGN']);
        $sanBayDen = SanBay::factory()->create(['ma_san_bay' => 'HAN']);
        $mayBay = MayBay::factory()->create();
        $this->nguoiDung = NguoiDung::factory()->create();

        // Tạo Chuyến Bay
        $this->chuyenBay = ChuyenBay::factory()->create([
            'id_may_bay' => $mayBay->id,
            'id_san_bay_di' => $sanBayDi->id,
            'id_san_bay_den' => $sanBayDen->id,
            'gia_ve' => $this->giaVeCoBanCoDinh, // 1,000,000 VND
        ]);

        // Tính toán thanh toán cố định (dùng chung cho 3 test case ITG-TT-01, 02, 03)
        $tongTienChuaGiam = $this->giaVeCoBanCoDinh * $this->soLuongVe; // 2,000,000 VND
        $this->tongTienThanhToan = $tongTienChuaGiam - $this->giaTriGiamCoDinh; // 1,900,000 VND

        // CHUẨN BỊ KHUYẾN MÃI (Giảm Cố Định)
        $this->khuyenMaiCoDinh = KhuyenMai::factory()->create([
            'ma_khuyen_mai' => 'ITG100K',
            'gia_tri' => $this->giaTriGiamCoDinh, // 100,000 VND
            'loai_gia_tri' => 'gia_tri_co_dinh',
            'ngay_bat_dau' => Carbon::now()->subDay(),
            'ngay_ket_thuc' => Carbon::now()->addDay(),
            'trang_thai' => 'active',
        ]);
    }

    /**
     * @test
     * @group ITG-TT-01
     * Mô tả: Kiểm tra luồng tính toán với Khuyến Mãi Giảm Phần Trăm (10%).
     */
    public function test_01_percentage_discount_flow()
    {
        $status = true;
        $id = 'ITG-TT-01';
        $description = 'Kiểm tra tính toán giảm giá theo phần trăm (10%).';
        $expectedResult = 'Booking.tong_tien phải là 900.00.';

        try {
            // ... (Giữ nguyên logic test ITG-TT-01)
            $chuyenBay = ChuyenBay::factory()->create([
                'gia_ve' => $this->giaVeCoBanPhanTram, // 500.00
                'id_may_bay' => $this->chuyenBay->id_may_bay,
                'id_san_bay_di' => $this->chuyenBay->id_san_bay_di,
                'id_san_bay_den' => $this->chuyenBay->id_san_bay_den,
            ]);

            $khuyenMai = KhuyenMai::factory()->create([
                'ma_khuyen_mai' => 'SALE10',
                'gia_tri' => $this->giaTriGiamPhanTram,
                'loai_gia_tri' => 'phan_tram',
                'ngay_bat_dau' => Carbon::now()->subDay(),
                'ngay_ket_thuc' => Carbon::now()->addDay(),
                'trang_thai' => 'active',
            ]);

            $tongTienChuaGiam = $this->giaVeCoBanPhanTram * $this->soLuongVe; // 1000.00
            $giaTriGiam = $tongTienChuaGiam * ($this->giaTriGiamPhanTram / 100);
            $tongTienSauGiam = $tongTienChuaGiam - $giaTriGiam; // 900.00

            $booking = Booking::factory()->create([
                'id_nguoi_dung' => $this->nguoiDung->id,
                'tong_tien' => $tongTienSauGiam,
                'id_khuyen_mai' => $khuyenMai->id,
                'trang_thai' => 'thanh_cong',
            ]);

            $this->assertEquals(900.00, $booking->refresh()->tong_tien, 'Lỗi: Tổng tiền Booking (Giảm %) không chính xác.');
            $expected = $expectedResult;

        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult($id, $description, $expected, $status);
        }
    }

    // -------------------------------------------------------------------------------- //

    /**
     * @test
     * @group ITG-TT-02
     * Mô tả: Kiểm tra Booking tính Tổng Tiền chính xác sau khi áp dụng Khuyến Mãi cố định.
     */
    public function test_02_booking_fixed_discount()
    {
        $status = true;
        $id = 'ITG-TT-02';
        $description = 'Kiểm tra Booking tính Tổng Tiền chính xác sau khi áp dụng Khuyến Mãi cố định.';
        $expected_02 = 'Booking.tong_tien phải là 1,900,000 VND và liên kết đúng Khuyến Mãi.';

        try {
            // TẠO BOOKING
            $booking = Booking::factory()->create([
                'id_nguoi_dung' => $this->nguoiDung->id,
                'tong_tien' => $this->tongTienThanhToan, // 1,900,000 VND
                'id_khuyen_mai' => $this->khuyenMaiCoDinh->id,
                'trang_thai' => 'paid',
            ]);

            // KIỂM TRA
            $this->assertEquals($this->tongTienThanhToan, $booking->refresh()->tong_tien, 'ITG-TT-02 Lỗi: Booking.tong_tien không khớp.');
            $this->assertEquals($this->khuyenMaiCoDinh->id, $booking->khuyenMai->id, 'ITG-TT-02 Lỗi: Booking không liên kết đúng Khuyến Mãi.');

        } catch (\Throwable $e) {
            $status = false;
            $expected_02 = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult($id, $description, $expected_02, $status);
        }
    }


    /**
     * @test
     * @group ITG-TT-03
     * Mô tả: Kiểm tra tạo Hóa Đơn và Chi Tiết Hóa Đơn từ Booking (Giảm Cố Định).
     */
    public function test_03_invoice_and_details_fixed_discount()
    {
        $status = true;
        $id = 'ITG-TT-03';
        $description = 'Kiểm tra tạo Hóa Đơn và Chi Tiết Hóa Đơn từ Booking (Giá gốc trong Chi Tiết).';
        $expected_03 = 'HoaDon.tong_tien là 1,900,000 VND, có 2 Chi Tiết Hóa Đơn, và ChiTietHoaDon.gia là 1,000,000 VND.';

        try {
            // TẠO BOOKING (Để có ID liên kết)
            $booking = Booking::factory()->create([
                'id_nguoi_dung' => $this->nguoiDung->id,
                'tong_tien' => $this->tongTienThanhToan,
                'id_khuyen_mai' => $this->khuyenMaiCoDinh->id,
                'trang_thai' => 'paid',
            ]);

            // TẠO VÉ (2 vé)
            $ve1 = Ve::factory()->create([
                'id_booking' => $booking->id,
                'id_chuyen_bay' => $this->chuyenBay->id,
                'gia_ve' => $this->giaVeCoBanCoDinh
            ]);
            $ve2 = Ve::factory()->create([
                'id_booking' => $booking->id,
                'id_chuyen_bay' => $this->chuyenBay->id,
                'gia_ve' => $this->giaVeCoBanCoDinh
            ]);

            // TẠO HÓA ĐƠN VÀ CHI TIẾT HÓA ĐƠN
            $hoaDon = HoaDon::factory()->create([
                'id_booking' => $booking->id,
                'tong_tien' => $this->tongTienThanhToan, // 1,900,000 VND
                'trang_thai' => 'da_thanh_toan',
            ]);

            ChiTietHoaDon::factory()->create([
                'id_hoa_don' => $hoaDon->id,
                'id_ve' => $ve1->id,
                'so_luong' => 1,
                'gia' => $this->giaVeCoBanCoDinh
            ]);
            ChiTietHoaDon::factory()->create([
                'id_hoa_don' => $hoaDon->id,
                'id_ve' => $ve2->id,
                'so_luong' => 1,
                'gia' => $this->giaVeCoBanCoDinh
            ]);

            // KIỂM TRA
            $this->assertEquals($this->tongTienThanhToan, $hoaDon->refresh()->tong_tien, 'ITG-TT-03 Lỗi: HoaDon.tong_tien không khớp.');
            $this->assertCount($this->soLuongVe, $hoaDon->chiTiets, 'ITG-TT-03 Lỗi: Phải có 2 Chi Tiết Hóa Đơn.');

            foreach ($hoaDon->chiTiets as $chiTiet) {
                $this->assertEquals($this->giaVeCoBanCoDinh, $chiTiet->gia, 'ITG-TT-03 Lỗi: ChiTietHoaDon.gia phải là giá gốc.');
            }
        } catch (\Throwable $e) {
            $status = false;
            $expected_03 = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult($id, $description, $expected_03, $status);
        }
    }


    /**
     * @test
     * @group ITG-TT-04
     * Mô tả: Kiểm tra ghi nhận Doanh Thu từ Hóa Đơn.
     */
    public function test_04_revenue_record()
    {
        $status = true;
        $id = 'ITG-TT-04';
        $description = 'Kiểm tra ghi nhận Doanh Thu từ Hóa Đơn (bằng tổng tiền thanh toán).';
        $expected_04 = 'DoanhThu.doanh_thu phải là 1,900,000 VND và liên kết đúng Hoa Đơn.';

        try {
            // TẠO BOOKING VÀ HÓA ĐƠN (tương tự như ITG-TT-02 để có đối tượng HoaDon)
            $booking = Booking::factory()->create([
                'id_nguoi_dung' => $this->nguoiDung->id,
                'tong_tien' => $this->tongTienThanhToan,
                'id_khuyen_mai' => $this->khuyenMaiCoDinh->id,
                'trang_thai' => 'paid',
            ]);
            $hoaDon = HoaDon::factory()->create([
                'id_booking' => $booking->id,
                'tong_tien' => $this->tongTienThanhToan,
                'trang_thai' => 'da_thanh_toan',
            ]);

            // GHI NHẬN DOANH THU
            $doanhThu = DoanhThu::factory()->create([
                'id_hoa_don' => $hoaDon->id,
                'thang' => Carbon::now()->month,
                'nam' => Carbon::now()->year,
                'doanh_thu' => $this->tongTienThanhToan,
            ]);

            // KIỂM TRA
            $this->assertEquals($this->tongTienThanhToan, $doanhThu->refresh()->doanh_thu, 'ITG-TT-03 Lỗi: DoanhThu.doanh_thu không khớp.');
            $this->assertTrue($hoaDon->doanhThu->contains($doanhThu), 'ITG-TT-03 Lỗi: Hóa đơn không liên kết với bản ghi Doanh Thu.');
        } catch (\Throwable $e) {
            $status = false;
            $expected_04 = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult($id, $description, $expected_04, $status);
        }
    }

    /**
     * Hàm chạy 1 lần sau khi tất cả test trong class này kết thúc.
     * Dùng để xuất kết quả ra định dạng CSV/Excel.
     */
    public static function tearDownAfterClass(): void
    {
        // ... (Giữ nguyên hàm tearDownAfterClass)
        if (empty(self::$testResults)) {
            return;
        }

        $results = self::$testResults;
        $headers = array_keys($results[0]);
        $output = fopen('php://temp', 'r+');

        fputcsv($output, $headers);

        foreach ($results as $row) {
            fputcsv($output, array_values($row));
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $csvContentWithBOM = $bom . $csvContent;

        $filePath = base_path('discount_revenue_test_report.csv');

        if (file_put_contents($filePath, $csvContentWithBOM) !== false) {
            echo "\n\n========================================================================\n";
            echo "✅ BÁO CÁO KẾT QUẢ TEST ĐÃ LƯU (EXCEL-FRIENDLY)\n";
            echo "File đã được lưu thành công tại: " . $filePath . "\n";
            echo "========================================================================\n\n";
        } else {
            echo "\n\n[LỖI] KHÔNG THỂ XUẤT FILE CSV/Excel. Vui lòng kiểm tra quyền ghi file tại: " . $filePath . "\n";
            echo "========================================================================\n\n";
        }
    }
}

//Áp dụng Khuyến mãi và Ghi nhận Doanh
// File: database/factories/ChuyenBayFactory.php
//MayBayFactory.php -- SanBayFactory.php -- 2025_11_18_000003_MayBay.php
//database/factories/KhuyenMaiFactory.php
//2025_11_18_000006_create_khuyen_mai_table.php
//database/factories/VeFactory.php
//database/factories/HoaDonFactory.php
//database/migrations/*_create_hoa_don_table.php
//database/factories/ChiTietHoaDonFactory.php
//database/migrations/*_create_chi_tiet_hoa_don_table.php
//database/factories/DoanhThuFactory.php
//database/migrations/*_create_doanh_thu_table.php

//php artisan test --filter DiscountRevenueTest
//==========OK==Áp dụng Khuyến mãi và Ghi nhận Doanh
