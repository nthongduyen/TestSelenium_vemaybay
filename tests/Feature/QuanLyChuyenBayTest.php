<?php

namespace Tests\Feature;

use App\Models\ChuyenBay;
use App\Models\NguoiDung;
use App\Models\SanBay;
use App\Models\MayBay;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuanLyChuyenBayTest extends TestCase
{

    use DatabaseTransactions;

    protected $admin;
    protected $sanBayDi;
    protected $sanBayDen;
    protected $mayBay;

    // =========================================================================
    // 1. BIẾN VÀ HÀM TIỆN ÍCH CHO VIỆC GHI KẾT QUẢ TEST
    // =========================================================================

    protected static array $testResults = [];

    /**
     * Hàm tiện ích để ghi lại kết quả của từng Test Case vào mảng tĩnh.
     */
    protected function recordResult(string $id, string $description, string $expected, bool $passed): void
    {
        self::$testResults[] = [
            'ID' => $id,
            'Mô tả' => $description,
            'Kết quả Mong đợi' => $expected,
            'Trạng thái' => $passed ? 'PASS' : 'FAIL',
        ];
    }

    // =========================================================================
    // HÀM SETUP VÀ TEST CASE CỦA BẠN
    // =========================================================================

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Tạo dữ liệu giả lập (Các dữ liệu này sẽ tự mất sau khi test xong)

        // Tạo Admin
        $this->admin = NguoiDung::create([
            'ho_ten' => 'Admin Test',
            'email' => 'admintest' . rand(1000,9999) . '@test.com', // Email ngẫu nhiên để tránh trùng
            'mat_khau' => Hash::make('123456'),
            'vai_tro' => 'admin', // QUAN TRỌNG: Đảm bảo vai trò là 'admin'
            'trang_thai' => 1,
        ]);

        // Kiểm tra xem Sân Bay đã có chưa, nếu chưa thì tạo mới để lấy ID
        $this->sanBayDi = SanBay::firstOrCreate(
            ['ma_san_bay' => 'SGN'],
            ['ten_san_bay' => 'Tan Son Nhat', 'dia_chi' => 'HCM', 'quoc_gia' => 'VN', 'tinh_thanh' => 'HCM']
        );

        $this->sanBayDen = SanBay::firstOrCreate(
            ['ma_san_bay' => 'HAN'],
            ['ten_san_bay' => 'Noi Bai', 'dia_chi' => 'Ha Noi', 'quoc_gia' => 'VN', 'tinh_thanh' => 'Ha Noi']
        );

        $this->mayBay = MayBay::firstOrCreate(
            ['ma_may_bay' => 'VN-TEST'],
            [
                'ten_may_bay' => 'Boeing 787',
                'so_ghe' => 100,
                'trang_thai' => 'hoat_dong', // HOẶC 'active'
                'hang_san_xuat' => 'Boeing'
            ]
        );
    }

    /**
     * Test Case IT01: Thêm chuyến bay mới
     */
    public function test_it01_admin_co_the_them_chuyen_bay()
    {
        $status = true;
        $maChuyenBay = 'VN' . rand(1000, 9999); // Mã ngẫu nhiên

        $duLieuChuyenBay = [
            'ma_chuyen_bay'  => $maChuyenBay,
            'id_may_bay'     => $this->mayBay->id,
            'id_san_bay_di'  => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di'   => '2025-12-20 08:00:00',
            'thoi_gian_den'  => '2025-12-20 10:00:00',
            'gia_ve'         => 1500000,
            'trang_thai'     => 'dang_ban',
            '_token' => csrf_token(),
        ];

        try {
            $response = $this->actingAs($this->admin)
                             ->post('/admin/chuyen-bay', $duLieuChuyenBay);

            $status = in_array($response->status(), [200, 201, 302]);

            // Nếu thành công, kiểm tra DB
            if ($status) {
                $this->assertDatabaseHas('chuyen_bay', [
                    'ma_chuyen_bay' => $maChuyenBay,
                    'gia_ve'        => 1500000,
                ]);
            }
            $expected = "Thêm chuyến bay thành công (HTTP Status: {$response->status()}). Dữ liệu đã được lưu.";
        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('IT01', 'Admin có thể thêm chuyến bay', $expected, $status);
        }
    }

    /**
     * Test Case IT02: Sửa thông tin chuyến bay
     */
    public function test_it02_admin_co_the_sua_chuyen_bay()
    {
        $status = true;

        $chuyenBayCu = ChuyenBay::create([
            'ma_chuyen_bay'  => 'VN-OLD-' . rand(100,999),
            'id_may_bay'     => $this->mayBay->id,
            'id_san_bay_di'  => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di'   => '2025-12-01 08:00:00',
            'thoi_gian_den'  => '2025-12-01 10:00:00',
            'gia_ve'         => 1000000,
            'trang_thai'     => 'dang_ban'
        ]);

        $maMoi = 'VN-NEW-' . rand(100,999);
        $duLieuMoi = [
            'ma_chuyen_bay'  => $maMoi,
            'id_may_bay'     => $this->mayBay->id,
            'id_san_bay_di'  => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di'   => '2025-12-01 08:00:00',
            'thoi_gian_den'  => '2025-12-01 10:00:00',
            'gia_ve'         => 2000000,
            'trang_thai'     => 'dang_ban',
            '_token' => csrf_token(),
            '_method' => 'PUT'
        ];

        try {
            $response = $this->actingAs($this->admin)
                             ->put("/admin/chuyen-bay/{$chuyenBayCu->id}", $duLieuMoi);

            $status = in_array($response->status(), [200, 201, 302]);

            // Nếu thành công, kiểm tra DB
            if ($status) {
                $this->assertDatabaseHas('chuyen_bay', [
                    'id' => $chuyenBayCu->id,
                    'ma_chuyen_bay' => $maMoi,
                    'gia_ve' => 2000000
                ]);
            }
            $expected = "Sửa chuyến bay thành công (HTTP Status: {$response->status()}). Giá vé đã thay đổi thành 2000000.";

        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('IT02', 'Admin có thể sửa chuyến bay', $expected, $status);
        }
    }

    /**
     * Test Case IT03: Xóa chuyến bay
     */
    public function test_it03_admin_co_the_xoa_chuyen_bay()
    {
        $status = true;

        $chuyenBay = ChuyenBay::create([
            'ma_chuyen_bay'  => 'VN-DEL-' . rand(100,999),
            'id_may_bay'     => $this->mayBay->id,
            'id_san_bay_di'  => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di'   => '2025-12-01 08:00:00',
            'thoi_gian_den'  => '2025-12-01 10:00:00',
            'gia_ve'         => 500000,
            'trang_thai'     => 'dang_ban'
        ]);
        $chuyenBayId = $chuyenBay->id;
        $maChuyenBay = $chuyenBay->ma_chuyen_bay;


        try {
            $response = $this->actingAs($this->admin)
                             ->delete("/admin/chuyen-bay/{$chuyenBayId}", [
                                 '_token' => csrf_token(),
                             ]);

            $status = in_array($response->status(), [200, 204, 302]);

            if ($status) {
                $this->assertDatabaseMissing('chuyen_bay', [
                    'id' => $chuyenBayId,
                ]);
            }
            $expected = "Xóa chuyến bay thành công (HTTP Status: {$response->status()}). Dữ liệu đã bị xóa khỏi DB.";
        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('IT03', 'Admin có thể xóa chuyến bay', $expected, $status);
        }
    }


    // =========================================================================
    // HÀM XUẤT KẾT QUẢ TEST CASE RA EXCEL (BASE_PATH)
    // =========================================================================

    /**
     * Hàm chạy 1 lần sau khi tất cả test trong class này kết thúc.
     * Dùng để xuất kết quả ra định dạng CSV/Excel.
     */
    public static function tearDownAfterClass(): void
    {
        if (empty(self::$testResults)) {
            return;
        }

        $results = self::$testResults;

        // 1. Chuẩn bị headers và nội dung CSV
        $headers = array_keys($results[0]);
        // Sử dụng temp stream để xử lý dữ liệu CSV
        $output = fopen('php://temp', 'r+');

        // Ghi tiêu đề (Headers)
        fputcsv($output, $headers);

        // Ghi dữ liệu
        foreach ($results as $row) {
            fputcsv($output, array_values($row));
        }

        // Đặt con trỏ về đầu stream để đọc nội dung
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        // 2. Thêm BOM và lưu file
        // Thêm BOM để Excel trên Windows đọc được tiếng Việt
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $csvContentWithBOM = $bom . $csvContent;

        // LƯU FILE TẠI base_path() theo yêu cầu
        $filePath = base_path('quanlychuyenbay_test_report.csv');

        // Sử dụng file_put_contents để lưu chuỗi CSV CÓ BOM vào file
        if (@file_put_contents($filePath, $csvContentWithBOM) !== false) {
            echo "\n\n========================================================================\n";
            echo "✅ BÁO CÁO KẾT QUẢ TEST ĐÃ LƯU (EXCEL-FRIENDLY)\n";
            echo "File đã được lưu thành công tại: " . $filePath . "\n";
            echo "========================================================================\n\n";
        } else {
            // Hiển thị lỗi rõ ràng hơn
            echo "\n\n[LỖI] KHÔNG THỂ XUẤT FILE CSV/Excel. Vui lòng kiểm tra quyền ghi file tại: " . $filePath . "\n";
            echo "========================================================================\n\n";
        }
    }
}
// routes/web.php // chuyenbaycontroller

// OK: quản lý chuyến bay
//php artisan test --filter=QuanLyChuyenBayTest
