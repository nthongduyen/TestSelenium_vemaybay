<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use App\Models\DanhMucBaiViet;
use App\Models\BaiViet;
// Sử dụng DatabaseMigrations để đảm bảo cấu trúc database được tạo lại
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Kiểm tra các mối quan hệ và nghiệp vụ của Bài Viết, Danh Mục và Tác Giả.
 */
class ContentManagementTest extends TestCase
{
    // Sử dụng DatabaseMigrations để tạo lại database cho mỗi test
    use DatabaseMigrations;

    protected $tacGia;
    protected $danhMucCha;
    protected $danhMucCon;
    protected $baiViet;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. TẠO DỮ LIỆU CƠ SỞ (Tương ứng với Điều kiện của ITG-CMS-01 & ITG-CMS-02)

        // Tác giả (Giả định NguoiDung có vai trò 'admin' để làm tác giả)
        $this->tacGia = NguoiDung::factory()->create([
            'vai_tro' => 'admin',
            // Thêm các trường cần thiết khác cho NguoiDung nếu cần, ví dụ:
            // 'email' => 'admin@test.com',
            // 'password' => bcrypt('password'),
        ]);

        // Danh Mục Cha (id_danh_muc_cha = null)
        $this->danhMucCha = DanhMucBaiViet::factory()->create([
            'ten_danh_muc' => 'Du lịch'
        ]);

        // Danh Mục Con (Phục vụ cho ITG-CMS-01)
        $this->danhMucCon = DanhMucBaiViet::factory()->create([
            'ten_danh_muc' => 'Kinh nghiệm săn vé',
            'id_danh_muc_cha' => $this->danhMucCha->id,
        ]);

        // Bài Viết (Phục vụ cho ITG-CMS-02 và ITG-CMS-03)
        $this->baiViet = BaiViet::factory()->create([
            'tieu_de' => '10 mẹo săn vé máy bay giá rẻ',
            'id_tac_gia' => $this->tacGia->id,
            'id_danh_muc' => $this->danhMucCon->id,
        ]);
    }

    /**
     * @test
     * @group ITG-CMS-01
     * Mô tả: Kiểm tra quan hệ đệ quy (DanhMucBaiViet Cha-Con).
     * Kết quả mong đợi: DanhMucCha->danhMucCon() trả về Collection chứa DM Con.
     * DanhMucCon->danhMucCha() trả về đúng đối tượng DM Cha.
     */
    public function test_recursive_category_relationship()
    {
        // 1. Truy vấn DanhMucCha->danhMucCon()
        // Giả định tên quan hệ là danhMucCon
        $dmCons = $this->danhMucCha->danhMucCon;

        // Kết quả mong đợi 1: DanhMucCha->danhMucCon() trả về Collection chứa DM Con.
        $this->assertCount(1, $dmCons, 'Lỗi ITG-CMS-01 (Cha->Con): Danh Mục Cha phải có 1 Danh Mục Con.');
        $this->assertEquals($this->danhMucCon->id, $dmCons->first()->id, 'Lỗi ITG-CMS-01 (Cha->Con): ID Danh Mục Con không khớp.');

        // 2. Truy vấn DanhMucCon->danhMucCha()
        // Giả định tên quan hệ là danhMucCha
        $dmChaTuCon = $this->danhMucCon->danhMucCha;

        // Kết quả mong đợi 2: DanhMucCon->danhMucCha() trả về đúng đối tượng DM Cha.
        $this->assertEquals($this->danhMucCha->id, $dmChaTuCon->id, 'Lỗi ITG-CMS-01 (Con->Cha): Danh Mục Con phải liên kết đúng với DM Cha.');
    }

    /**
     * @test
     * @group ITG-CMS-02
     * Mô tả: Kiểm tra liên kết giữa Bài Viết, Tác Giả (NguoiDung), và Danh Mục.
     * Kết quả mong đợi: BaiViet->tacGia() trả về đối tượng NguoiDung Admin.
     * BaiViet->danhMuc() trả về đối tượng Danh Mục Con.
     */
    public function test_article_belongs_to_relationships()
    {
        // 1. Truy vấn BaiViet->tacGia()
        // Giả định tên quan hệ là tacGia
        $tacGia = $this->baiViet->tacGia;

        // Kết quả mong đợi 1: BaiViet->tacGia() trả về đối tượng NguoiDung Admin.
        $this->assertEquals($this->tacGia->id, $tacGia->id, 'Lỗi ITG-CMS-02 (Tác Giả): Bài Viết không liên kết đúng Tác Giả.');
        $this->assertEquals('admin', $tacGia->vai_tro, 'Lỗi ITG-CMS-02 (Tác Giả): Tác Giả phải có vai trò là Admin.');

        // 2. Truy vấn BaiViet->danhMuc()
        // Giả định tên quan hệ là danhMuc
        $danhMuc = $this->baiViet->danhMuc;

        // Kết quả mong đợi 2: BaiViet->danhMuc() trả về đối tượng Danh Mục Con.
        $this->assertEquals($this->danhMucCon->id, $danhMuc->id, 'Lỗi ITG-CMS-02 (Danh Mục): Bài Viết không liên kết đúng Danh Mục.');

        // KIỂM TRA MỐI QUAN HỆ CHA CỦA DANH MỤC CON: Danh mục của bài viết phải có id_danh_muc_cha
        $this->assertEquals($this->danhMucCha->id, $danhMuc->id_danh_muc_cha, 'Lỗi ITG-CMS-02 (Quan hệ cha): Danh Mục của Bài Viết phải có ID Danh Mục Cha.');
    }

    /**
     * @test
     * @group ITG-CMS-03
     * Mô tả: Kiểm tra Tác Giả và Danh Mục có thể truy xuất các Bài Viết liên quan.
     * Kết quả mong đợi: NguoiDung->baiViets() trả về Collection chứa Bài Viết vừa tạo.
     * DanhMucCon->baiViets() trả về Collection chứa Bài Viết vừa tạo.
     */
    public function test_author_and_category_has_many_relationships()
    {
        // 1. Truy vấn NguoiDung(Admin)->baiViets()
        // Giả định tên quan hệ là baiViets
        $baiVietsCuaTacGia = $this->tacGia->baiViets;

        // Kết quả mong đợi 1: NguoiDung->baiViets() trả về Collection chứa Bài Viết.
        $this->assertCount(1, $baiVietsCuaTacGia, 'Lỗi ITG-CMS-03 (Tác Giả): Tác Giả phải có 1 Bài Viết.');
        $this->assertEquals($this->baiViet->id, $baiVietsCuaTacGia->first()->id, 'Lỗi ITG-CMS-03 (Tác Giả): ID Bài Viết không khớp.');

        // 2. Truy vấn DanhMucCon->baiViets()
        // Giả định tên quan hệ là baiViets
        $baiVietsCuaDanhMuc = $this->danhMucCon->baiViets;

        // Kết quả mong đợi 2: DanhMucCon->baiViets() trả về Collection chứa Bài Viết.
        $this->assertCount(1, $baiVietsCuaDanhMuc, 'Lỗi ITG-CMS-03 (Danh Mục): Danh Mục phải có 1 Bài Viết.');
        $this->assertEquals($this->baiViet->id, $baiVietsCuaDanhMuc->first()->id, 'Lỗi ITG-CMS-03 (Danh Mục): ID Bài Viết không khớp.');
    }
}


//DanhMucBaiVietFactory.php -- BaiVietFactory.php

//php artisan test --filter ContentManagementTest
