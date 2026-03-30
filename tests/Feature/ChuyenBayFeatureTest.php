<?php

namespace Tests\Feature;

use App\Models\ChuyenBay;
use App\Models\NguoiDung;
use App\Models\MayBay;
use App\Models\SanBay;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChuyenBayFeatureTest extends TestCase
{
    // Sử dụng RefreshDatabase để đảm bảo database sạch cho mỗi test
    use RefreshDatabase;

    protected NguoiDung $adminUser;
    protected MayBay $mayBay;
    protected SanBay $sanBayDi;
    protected SanBay $sanBayDen;

    protected function createFlightDependencies(): void
    {
        // 1. Tạo người dùng Admin (Dùng Factory helper admin())
        // Đảm bảo Factory NguoiDung có vai_tro là 'admin'
        $this->adminUser = NguoiDung::factory()->admin()->create();

        // 2. Tạo các dữ liệu liên quan cần thiết cho các khoá ngoại
        $this->mayBay = MayBay::factory()->create();
        $this->sanBayDi = SanBay::factory()->create();
        $this->sanBayDen = SanBay::factory()->create();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createFlightDependencies();
    }

    // ==========================================================
    // IT05: THÊM CHUYẾN BAY MỚI (CRUD: CREATE)
    // * KHẮC PHỤC LỖI ROUTE: Dùng POST /chuyen-bay thay vì route('chuyen-bay.store')
    // ==========================================================

    /** * @test
     * IT05: Admin có thể tạo chuyến bay mới thành công với dữ liệu hợp lệ.
     */
    public function admin_can_create_a_new_flight_successfully(): void
    {
        // Sử dụng URL trực tiếp để tránh lỗi 'Route not defined'
        $url = '/chuyen-bay';

        $payload = [
            'ma_chuyen_bay' => 'IT05_NEW',
            'id_may_bay' => $this->mayBay->id,
            'id_san_bay_di' => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            // Đảm bảo thời gian trong tương lai
            'thoi_gian_di' => Carbon::now()->addDays(5)->setTime(8, 0)->toDateTimeString(),
            'thoi_gian_den' => Carbon::now()->addDays(5)->setTime(10, 0)->toDateTimeString(),
            'gia_ve' => 1500000.00,
            'trang_thai' => 'dang_ban',
        ];

        $response = $this->actingAs($this->adminUser)->postJson($url, $payload);

        // IT05: Kiểm tra kết quả mong đợi
        $response->assertStatus(201)
                 ->assertJsonFragment(['ma_chuyen_bay' => 'IT05_NEW'])
                 ->assertJson(['message' => 'Thêm chuyến bay thành công!']);

        // Kiểm tra trong cơ sở dữ liệu
        $this->assertDatabaseHas('chuyen_bay', ['ma_chuyen_bay' => 'IT05_NEW']);
    }

    /** * @test
     * IT05a: Tạo chuyến bay thất bại khi mã chuyến bay bị trùng (unique validation).
     */
    public function creation_fails_with_duplicate_ma_chuyen_bay(): void
    {
        $url = '/chuyen-bay';

        // Tạo một chuyến bay đã tồn tại
        ChuyenBay::factory()->create(['ma_chuyen_bay' => 'DUPLICATE']);

        $payload = [
            'ma_chuyen_bay' => 'DUPLICATE', // Mã trùng
            'id_may_bay' => $this->mayBay->id,
            'id_san_bay_di' => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di' => Carbon::now()->addDay()->toDateTimeString(),
            'thoi_gian_den' => Carbon::now()->addDays(2)->toDateTimeString(),
            'gia_ve' => 1000000,
            'trang_thai' => 'dang_ban',
        ];

        $response = $this->actingAs($this->adminUser)->postJson($url, $payload);

        // Kiểm tra lỗi Validation (422) và trường bị lỗi
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('ma_chuyen_bay');
    }

    /** * @test
     * IT05b: Tạo chuyến bay thất bại khi thiếu các trường bắt buộc (required validation).
     */
    public function creation_fails_with_missing_required_fields(): void
    {
        $url = '/chuyen-bay';

        // Payload thiếu 'ma_chuyen_bay', 'gia_ve', 'id_san_bay_den', 'thoi_gian_den', v.v.
        $payload = [
            'id_may_bay' => $this->mayBay->id,
            'thoi_gian_di' => Carbon::now()->addDay()->toDateTimeString(),
        ];

        $response = $this->actingAs($this->adminUser)->postJson($url, $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['ma_chuyen_bay', 'gia_ve', 'id_san_bay_di', 'id_san_bay_den', 'thoi_gian_den']);
    }

    /** * @test
     * IT05c: Tạo chuyến bay thất bại khi thời gian đến trước hoặc bằng thời gian đi ('after' validation).
     */
    public function creation_fails_when_thoi_gian_den_is_before_thoi_gian_di(): void
    {
        $url = '/chuyen-bay';
        $thoi_gian_di = Carbon::now()->addDays(5)->setTime(10, 0);

        $payload = [
            'ma_chuyen_bay' => 'TIME_FAIL',
            'id_may_bay' => $this->mayBay->id,
            'id_san_bay_di' => $this->sanBayDi->id,
            'id_san_bay_den' => $this->sanBayDen->id,
            'thoi_gian_di' => $thoi_gian_di->toDateTimeString(),
            // Tạo thời gian đến sớm hơn thời gian đi 1 giờ
            'thoi_gian_den' => $thoi_gian_di->clone()->subHours(1)->toDateTimeString(),
            'gia_ve' => 1500000,
            'trang_thai' => 'dang_ban',
        ];

        $response = $this->actingAs($this->adminUser)->postJson($url, $payload);

        // Giả định bạn có rule 'after:thoi_gian_di' cho trường 'thoi_gian_den'
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('thoi_gian_den');
    }

    // ==========================================================
    // IT06: SỬA THÔNG TIN CHUYẾN BAY (CRUD: UPDATE)
    // * KHẮC PHỤC LỖI ROUTE: Dùng PUT /chuyen-bay/{id} thay vì route('chuyen-bay.update', ...)
    // ==========================================================

    /** * @test
     * IT06: Admin có thể sửa thông tin chuyến bay thành công (cập nhật giá vé và trạng thái).
     */
    public function admin_can_update_a_flight_successfully(): void
    {
        // Tạo một chuyến bay để sửa
        $flight = ChuyenBay::factory()->create(['gia_ve' => 500000]);
        // Sử dụng URL trực tiếp: PUT /chuyen-bay/{id}
        $url = "/chuyen-bay/{$flight->id}";

        $payload = [
            'ma_chuyen_bay' => $flight->ma_chuyen_bay,
            'id_may_bay' => $flight->id_may_bay,
            'id_san_bay_di' => $flight->id_san_bay_di,
            'id_san_bay_den' => $flight->id_san_bay_den,
            'thoi_gian_di' => $flight->thoi_gian_di,
            'thoi_gian_den' => $flight->thoi_gian_den,
            'gia_ve' => 2500000.00, // Thay đổi giá vé
            'trang_thai' => 'da_huy', // Thay đổi trạng thái
        ];

        $response = $this->actingAs($this->adminUser)->putJson($url, $payload);

        // IT06: Kiểm tra kết quả mong đợi
        $response->assertStatus(200) // 200 OK
                 ->assertJson(['message' => 'Thông tin chuyến bay được cập nhật thành công!']);

        // Kiểm tra database đã được cập nhật
        $this->assertDatabaseHas('chuyen_bay', [
            'id' => $flight->id,
            'gia_ve' => 2500000.00,
            'trang_thai' => 'da_huy',
        ]);
    }

    /** * @test
     * IT06a: Sửa chuyến bay thất bại khi mã chuyến bay mới trùng với chuyến bay khác.
     */
    public function update_fails_when_ma_chuyen_bay_duplicates_another_flight(): void
    {
        // Tạo chuyến bay A (sẽ sửa) và B (mã trùng)
        $flightA = ChuyenBay::factory()->create(['ma_chuyen_bay' => 'FLIGHT_A']);
        $flightB = ChuyenBay::factory()->create(['ma_chuyen_bay' => 'FLIGHT_B']);
        $url = "/chuyen-bay/{$flightA->id}";

        $payload = [
            // Cố gắng sửa mã A thành mã B (đã tồn tại)
            'ma_chuyen_bay' => 'FLIGHT_B',
            'id_may_bay' => $flightA->id_may_bay,
            'id_san_bay_di' => $flightA->id_san_bay_di,
            'id_san_bay_den' => $flightA->id_san_bay_den,
            'thoi_gian_di' => $flightA->thoi_gian_di,
            'thoi_gian_den' => $flightA->thoi_gian_den,
            'gia_ve' => 1200000.00,
        ];

        $response = $this->actingAs($this->adminUser)->putJson($url, $payload);

        // Kiểm tra lỗi Validation (422)
        $response->assertStatus(422)
                 ->assertJsonValidationErrors('ma_chuyen_bay');
    }

    /** * @test
     * Sửa: Cập nhật thành công khi mã chuyến bay không đổi (Kiểm tra rule Unique Ignore)
     */
    public function update_succeeds_when_ma_chuyen_bay_is_unchanged(): void
    {
        $flight = ChuyenBay::factory()->create(['ma_chuyen_bay' => 'FLIGHT_C']);
        $url = "/chuyen-bay/{$flight->id}";

        $payload = [
            'ma_chuyen_bay' => 'FLIGHT_C', // Mã không đổi
            'id_may_bay' => $flight->id_may_bay,
            'id_san_bay_di' => $flight->id_san_bay_di,
            'id_san_bay_den' => $flight->id_san_bay_den,
            'thoi_gian_di' => $flight->thoi_gian_di,
            'thoi_gian_den' => $flight->thoi_gian_den,
            'gia_ve' => 3000000.00, // Thay đổi trường khác
            'trang_thai' => 'dang_ban',
        ];

        $response = $this->actingAs($this->adminUser)->putJson($url, $payload);

        // Phải thành công
        $response->assertStatus(200);
        $this->assertDatabaseHas('chuyen_bay', ['id' => $flight->id, 'gia_ve' => 3000000.00]);
    }


    // ==========================================================
    // IT07: XOÁ CHUYẾN BAY (CRUD: DELETE)
    // * KHẮC PHỤC LỖI ROUTE: Dùng DELETE /chuyen-bay/{id} thay vì route('chuyen-bay.destroy', ...)
    // ==========================================================

    /** * @test
     * IT07: Admin có thể xóa một chuyến bay thành công.
     */
    public function admin_can_delete_a_flight_successfully(): void
    {
        // Tạo một chuyến bay để xóa
        $flight = ChuyenBay::factory()->create(['ma_chuyen_bay' => 'TO_BE_DELETED']);
        // Sử dụng URL trực tiếp: DELETE /chuyen-bay/{id}
        $url = "/chuyen-bay/{$flight->id}";

        $response = $this->actingAs($this->adminUser)->deleteJson($url);

        // IT07: Kiểm tra kết quả mong đợi
        $response->assertStatus(200) // 200 OK (hoặc 204 No Content)
                 ->assertJson(['message' => 'Xóa chuyến bay thành công!']);

        // Kiểm tra trong cơ sở dữ liệu
        $this->assertDatabaseMissing('chuyen_bay', ['ma_chuyen_bay' => 'TO_BE_DELETED']);
    }

    /**
     * @test
     * Kiểm tra truy cập: User không phải admin không thể thao tác
     * * KHẮC PHỤC LỖI ROUTE & LỖI FACTORY
     */
    public function unauthorized_user_cannot_manage_flights(): void
    {
        // Tạo người dùng thường - Đảm bảo sử dụng helper user() để có vai_tro = 'khach_hang'
        // Đây là điểm sửa lỗi lỗi Integrity constraint violation: 19 CHECK constraint failed: vai_tro
        $regularUser = NguoiDung::factory()->user()->create();

        // Tạo chuyến bay để thử xóa/sửa
        $flight = ChuyenBay::factory()->create();

        // 1. Thử tạo mới (POST /chuyen-bay)
        $responsePost = $this->actingAs($regularUser)->postJson('/chuyen-bay', []);
        $responsePost->assertStatus(403); // 403 Forbidden

        // 2. Thử sửa (PUT /chuyen-bay/{id})
        $responsePut = $this->actingAs($regularUser)->putJson("/chuyen-bay/{$flight->id}", []);
        $responsePut->assertStatus(403); // 403 Forbidden

        // 3. Thử xóa (DELETE /chuyen-bay/{id})
        $responseDelete = $this->actingAs($regularUser)->deleteJson("/chuyen-bay/{$flight->id}");
        $responseDelete->assertStatus(403); // 403 Forbidden
    }
}
// lệnh chạy: php artisan test --filter ChuyenBayFeatureTest


// các file liên quan
// 1. ChuyenBayFactory.php 2.MayBayFactory.php 3.SanBayFactory.php 4. NguoiDungFactory.php 5.User.php
// các file trong migrations: create_user_table, maybay,sanbay,chuyenbay
// ChuyenBayController.php


// sửa phpunit.xml
 //<env name="DB_CONNECTION" value="sqlite"/>
  //<env name="DB_DATABASE" value=":memory:"/> để dùng 2 dòng này
