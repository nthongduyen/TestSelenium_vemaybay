<?php

namespace Tests\Feature;

use App\Models\SanBay;
use App\Models\MayBay;
use App\Models\Ghe;
use App\Models\ChuyenBay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Kiểm tra các mối quan hệ và tính toàn vẹn dữ liệu
 * của Chuyến Bay, Máy Bay, Ghế và Sân Bay.
 */
class FlightInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected $sanBayDi;
    protected $sanBayDen;
    protected $mayBay;
    protected $chuyenBay;
    protected $soLuongGhe = 5;

    // Biến tĩnh để lưu kết quả và hàm tiện ích
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
            'Trạng thái' => $passed ? 'PASS' : 'FAIL', // Loại bỏ thẻ màu console
        ];
    }


    protected function setUp(): void
    {
        parent::setUp();

        // 1. TẠO SÂN BAY
        $this->sanBayDi = SanBay::create([
            'ma_san_bay' => 'SGN',
            'ten_san_bay' => 'Sân bay Tân Sơn Nhất',
            'quoc_gia' => 'Việt Nam',
            'thanh_pho' => 'TP. Hồ Chí Minh',
            'dia_chi' => 'Đường Trường Sơn'
        ]);

        $this->sanBayDen = SanBay::create([
            'ma_san_bay' => 'HAN',
            'ten_san_bay' => 'Sân bay Nội Bài',
            'quoc_gia' => 'Việt Nam',
            'thanh_pho' => 'Hà Nội',
            'dia_chi' => 'Huyện Sóc Sơn'
        ]);

        // 2. TẠO MÁY BAY
        $this->mayBay = MayBay::create([
            'ma_may_bay' => 'A320-VN',
            'ten_may_bay' => 'Airbus A320',
            'hang_san_xuat' => 'Airbus',
            'so_ghe' => $this->soLuongGhe,
            'trang_thai' => 'active',
        ]);

        // 3. TẠO CHUYẾN BAY
        $this->chuyenBay = ChuyenBay::create([
            'ma_chuyen_bay' => 'VN123',
            'id_may_bay' => $this->mayBay->id,
            'id_san_bay_di' => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di' => now()->addDay(),
            'thoi_gian_den' => now()->addDay()->addHours(2),
            'gia_ve' => 150.00,
            'trang_thai' => 'dang_ban',
        ]);

        // 4. TẠO GHẾ (5 GHẾ)
        $seatData = [
            ['A1', 'Business'], ['A2', 'Business'],
            ['B1', 'Economy'], ['B2', 'Economy'], ['C1', 'Economy']
        ];

        foreach ($seatData as $seat) {
            Ghe::create([
                'id_may_bay' => $this->mayBay->id,
                'so_ghe' => $seat[0],
                'loai_ghe' => $seat[1],
                'trang_thai' => 'available'
            ]);
        }
    }


    /**
     * @test
     * ID: ITG-CB-01
     * Mô tả: Kiểm tra ChuyenBay liên kết đúng Máy Bay, Sân Bay Đi, Sân Bay Đến (belongsTo).
     */
    public function testChuyenBayBelongsToRelationships()
    {
        $status = true;
        try {
            $this->assertNotNull($this->chuyenBay);
            $mayBayCuaCB = $this->chuyenBay->mayBay;
            $this->assertEquals($this->mayBay->id, $mayBayCuaCB->id);
            $this->assertEquals('A320-VN', $mayBayCuaCB->ma_may_bay);
            $this->assertEquals('SGN', $this->chuyenBay->sanBayDi->ma_san_bay);
            $this->assertEquals('HAN', $this->chuyenBay->sanBayDen->ma_san_bay);
            $expected = 'Quan hệ belongsTo (Máy Bay, Sân Bay Đi/Đến) thành công.';
        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('ITG-CB-01', 'Kiểm tra quan hệ BelongsTo của Chuyến Bay.', $expected, $status);
        }
    }


    /**
     * @test
     * ID: ITG-CB-02
     * Mô tả: Kiểm tra mối quan hệ 1-N giữa Máy Bay và Ghế.
     */
    public function testMayBayGheHasManyRelationship()
    {
        $status = true;
        try {
            $ghesCuaMayBay = $this->mayBay->ghes;
            $this->assertCount($this->soLuongGhe, $ghesCuaMayBay);
            $countBusiness = $ghesCuaMayBay->where('loai_ghe', 'Business')->count();
            $this->assertEquals(2, $countBusiness);
            $gheDauTien = $ghesCuaMayBay->first();
            $this->assertEquals($this->mayBay->id, $gheDauTien->mayBay->id);
            $expected = 'Quan hệ HasMany (Ghế) và số lượng ghế khớp.';
        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('ITG-CB-02', 'Kiểm tra quan hệ HasMany giữa Máy Bay và Ghế.', $expected, $status);
        }
    }

    /**
     * ID: ITG-CB-03
     * Mô tả: Kiểm tra Sân Bay có thể truy xuất các Chuyến Bay đi/đến (hasMany).
     */
    public function testSanBayChuyenBayHasManyRelationship()
    {
        $status = true;
        try {
            $chuyenBayDiSGN = $this->sanBayDi->chuyenBayDi;
            $this->assertCount(1, $chuyenBayDiSGN);
            $chuyenBayDenHAN = $this->sanBayDen->chuyenBayDen;
            $this->assertCount(1, $chuyenBayDenHAN);
            $expected = 'Quan hệ HasMany (Chuyến Bay Đi/Đến) từ Sân Bay thành công.';
        } catch (\Throwable $e) {
            $status = false;
            $expected = 'Lỗi Assertion: ' . $e->getMessage();
        } finally {
            $this->recordResult('ITG-CB-03', 'Kiểm tra quan hệ HasMany giữa Sân Bay và Chuyến Bay.', $expected, $status);
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
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $csvContentWithBOM = $bom . $csvContent;

        // LƯU FILE TẠI base_path() theo yêu cầu
        $filePath = base_path('flight_test_report.csv');

        // Sử dụng file_put_contents để lưu chuỗi CSV CÓ BOM vào file
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
// lệnh chạy: php artisan test --filter=FlightInventoryTest
//==============oke Kịch bản: Quản lý Chuyến Bay & Tồn kho Ghế
//2025_11_19_091736_create_ghe_table.php
