<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\BaiViet;
use App\Models\DanhMucBaiViet; // Đảm bảo đúng Model này
use App\Models\NguoiDung; // Đảm bảo đúng Model này
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

/**
 * Lớp kiểm thử tích hợp (Feature Test) cho chức năng Bài Viết.
 * Sử dụng RefreshDatabase để đảm bảo CSDL sạch sau mỗi test.
 */
class BaiVietTest extends TestCase
{
    // Sử dụng trait này để đảm bảo CSDL được migrate và làm sạch sau mỗi test
    use RefreshDatabase;

    protected NguoiDung $tacGia;
    protected DanhMucBaiViet $danhMuc;


    /**
     * Thiết lập môi trường trước khi chạy mỗi test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Vô hiệu hóa kiểm tra khóa ngoại trước khi chạy migration test
        Schema::disableForeignKeyConstraints();

        // 💡 FIX LỖI: KHỞI TẠO DỮ LIỆU MẪU CHO THUỘC TÍNH
        // Đảm bảo Factory tồn tại và hoạt động cho DanhMucBaiViet và NguoiDung
        $this->danhMuc = DanhMucBaiViet::factory()->create();
        $this->tacGia = NguoiDung::factory()->create();
    }

    protected function tearDown(): void
    {
        // Bật lại kiểm tra khóa ngoại sau khi test hoàn tất
        Schema::enableForeignKeyConstraints();
        parent::tearDown();
    }

    // =========================================================================
    // TC_BV_01: Tạo mới (Validation) - Kiểm tra bắt buộc Tiêu đề và Nội dung.
    // =========================================================================
    /** @test */
    public function tc_bv_01_tao_moi_phai_co_tieu_de_va_noi_dung()
    {
        // 1. Không có Tiêu đề
        $response1 = $this->postJson('/api/bai-viet', [
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'noi_dung' => 'Nội dung test không có tiêu đề.',
            'trang_thai' => 0,
        ]);

        $response1->assertStatus(422)
                  ->assertJsonValidationErrors(['tieu_de']);

        // 2. Không có Nội dung
        $response2 = $this->postJson('/api/bai-viet', [
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'tieu_de' => 'Tiêu đề test không có nội dung.',
            'trang_thai' => 0,
            // Thiếu 'noi_dung'
        ]);

        $response2->assertStatus(422)
                  ->assertJsonValidationErrors(['noi_dung']);

        $this->assertDatabaseCount('bai_viet', 0);
    }

    // =========================================================================
    // TC_BV_02: Tạo mới (Thành công) - Kiểm tra Slug tự động.
    // =========================================================================
    /** @test */
    public function tc_bv_02_tao_moi_thanh_cong_va_kiem_tra_slug()
    {
        $tieuDe = 'Bài Viết Mới Cần Kiểm Tra Slug';
        $slugMongDoi = Str::slug($tieuDe);

        $response = $this->postJson('/api/bai-viet', [
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'tieu_de' => $tieuDe,
            'noi_dung' => 'Nội dung đầy đủ.',
            'trang_thai' => 0, // Nháp
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Tạo bài viết thành công!']);

        $this->assertDatabaseHas('bai_viet', [
            'tieu_de' => $tieuDe,
            'slug' => $slugMongDoi, // Kiểm tra Slug tự động và chuẩn hóa
            'trang_thai' => 0,
            'luot_xem' => 0, // Kiểm tra luot_xem mặc định
            'ngay_xuat_ban' => null, // Nháp thì ngày xuất bản phải null
        ]);
    }

    // =========================================================================
    // TC_BV_03: Cập nhật (Xuất bản) - Chuyển Nháp sang Công khai (Ngay lập tức).
    // =========================================================================
    /** @test */
    public function tc_bv_03_cap_nhat_chuyen_sang_cong_khai_ngay_lap_tuc()
    {
        // 1. Tạo bài viết ở trạng thái Nháp
        $baiViet = BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'trang_thai' => 0,
            'ngay_xuat_ban' => null,
        ]);

        // 2. Gửi request cập nhật sang Công khai (1)
        $response = $this->putJson("/api/bai-viet/{$baiViet->id}", [
            'trang_thai' => 1,
            // Không set 'ngay_xuat_ban'
        ]);

        $response->assertStatus(200);

        // 3. Kiểm tra CSDL
        $baiVietMoi = $baiViet->fresh();

        $this->assertEquals(1, $baiVietMoi->trang_thai);
        // Kiểm tra ngày xuất bản được set là hôm nay (gần thời điểm hiện tại)
        $this->assertTrue($baiVietMoi->ngay_xuat_ban->isToday());
    }

    // =========================================================================
    // TC_BV_04: Cập nhật (Lên lịch) - Lên lịch xuất bản ngày tương lai.
    // =========================================================================
    /** @test */
    public function tc_bv_04_cap_nhat_len_lich_xuat_ban_ngay_tuong_lai()
    {
        // 1. Tạo bài viết ở trạng thái Nháp
        $baiViet = BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'trang_thai' => 0,
            'ngay_xuat_ban' => null,
        ]);

        // Ngày mai (tương lai)
        $ngayMai = now()->addDay()->format('Y-m-d H:i:s');

        // 2. Gửi request cập nhật trạng thái Công khai (1) và Ngày xuất bản tương lai
        $response = $this->putJson("/api/bai-viet/{$baiViet->id}", [
            'trang_thai' => 1,
            'ngay_xuat_ban' => $ngayMai,
        ]);

        $response->assertStatus(200);

        // 3. Kiểm tra CSDL
        $baiVietMoi = $baiViet->fresh();

        $this->assertEquals(1, $baiVietMoi->trang_thai);
        // Kiểm tra ngày xuất bản phải là ngày tương lai đã định
        $this->assertEquals($ngayMai, $baiVietMoi->ngay_xuat_ban->format('Y-m-d H:i:s'));
        $this->assertTrue($baiVietMoi->ngay_xuat_ban->isFuture());
    }

    // =========================================================================
    // TC_BV_05: Xem & Tìm kiếm - Kiểm tra tìm kiếm theo Tiêu đề.
    // =========================================================================
    /** @test */
    public function tc_bv_05_tim_kiem_theo_tieu_de()
    {
        // 1. Chuẩn bị dữ liệu test
        BaiViet::factory()->count(5)->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
        ]);

        // Bài viết MONG ĐỢI tìm thấy
        $baiVietChinh = BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'tieu_de' => 'Tìm kiếm chính xác TỪ KHÓA này',
        ]);

        // Bài viết KHÔNG mong đợi tìm thấy
        BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'tieu_de' => 'Một tiêu đề khác hoàn toàn',
        ]);

        $tuKhoa = 'TỪ KHÓA';

        // 2. Gửi request tìm kiếm
        $response = $this->getJson('/api/bai-viet?keyword=' . $tuKhoa);

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data') // Chỉ tìm thấy 1 bài
                 ->assertJsonFragment(['tieu_de' => $baiVietChinh->tieu_de]); // Kết quả chính xác
    }

    // =========================================================================
    // TC_BV_06: Xóa Bài viết
    // =========================================================================
    /** @test */
    public function tc_bv_06_xoa_bai_viet_thanh_cong()
    {
        // 1. Tạo bài viết cần xóa
        $baiViet = BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
        ]);

        // 2. Gửi request DELETE
        $response = $this->deleteJson("/api/bai-viet/{$baiViet->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Xóa bài viết thành công!']);

        // 3. Kiểm tra CSDL: Bài viết đã bị xóa
        $this->assertDatabaseMissing('bai_viet', ['id' => $baiViet->id]);
    }

    // =========================================================================
    // TC_BV_07: Logic (Lượt xem) - Đảm bảo trường Lượt xem không thể chỉnh sửa thủ công.
    // =========================================================================
    /** @test */
    public function tc_bv_07_truong_luot_xem_la_chi_doc()
    {
        // 1. Tạo bài viết với lượt xem ban đầu
        $luotXemGoc = 50;
        $baiViet = BaiViet::factory()->create([
            'id_danh_muc' => $this->danhMuc->id,
            'id_tac_gia' => $this->tacGia->id,
            'luot_xem' => $luotXemGoc,
            'tieu_de' => 'Tiêu đề cũ',
        ]);

        // 2. Gửi request cập nhật, cố gắng thay đổi luot_xem
        $luotXemMoi = 999;
        $tieuDeMoi = 'Tiêu đề đã sửa';

        $response = $this->putJson("/api/bai-viet/{$baiViet->id}", [
            'tieu_de' => $tieuDeMoi, // Thay đổi trường hợp lệ
            'luot_xem' => $luotXemMoi, // Cố gắng thay đổi trường chỉ đọc
        ]);

        $response->assertStatus(200);

        // 3. Kiểm tra CSDL: Tiêu đề đã được cập nhật, nhưng Lượt xem phải giữ nguyên
        $baiVietMoi = $baiViet->fresh();

        $this->assertEquals($tieuDeMoi, $baiVietMoi->tieu_de);
        $this->assertEquals($luotXemGoc, $baiVietMoi->luot_xem); // **Lượt xem vẫn là giá trị gốc**

        $this->assertNotEquals($luotXemMoi, $baiVietMoi->luot_xem);
    }
}

//routes/api.php
//BaiVietController.php----  test/feature/BaiVietTest.php
//



// php artisan test --filter BaiVietTest
