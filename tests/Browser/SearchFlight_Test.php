<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchFlight_Test extends DuskTestCase
{
    //TC_TK_01: Tìm kiếm vé MỘT CHIỀU thành công (One Way)
    public function testSearchFlightOneWay_TC_TK_01()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('input[name="flight_type"]', 10)
                    // 1. Chọn vé một chiều
                    ->radio('flight_type', 'oneway')
                    // 2. Chọn địa điểm (Lưu ý: ID phải tồn tại trong DB)
                    ->select('id_san_bay_di', '1')
                    ->select('id_san_bay_den', '2')
                    // 3. Nhập ngày đi (Dùng JS cho chuẩn)
                    ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");
            $browser->press('TÌM KIẾM VÉ')
                ->waitUntil('window.location.pathname != "/"', 15)
                ->assertPathIsNot('/')
                ->pause(2000);

            echo PHP_EOL . "[PASS] TC_TK_01: Tìm kiếm vé 1 chiều thành công!" . PHP_EOL;
        });
    }

    // TC_TK_02: Tìm kiếm vé KHỨ HỒI (Round Trip)
    public function testSearchFlightRoundTrip_TC_TK_02()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('input[name="flight_type"]', 10)
                    ->radio('flight_type', 'roundtrip')
                    ->pause(500) // Đợi UI cập nhật hiển thị ô ngày về
                    ->select('id_san_bay_di', '1')
                    ->select('id_san_bay_den', '2');

            // SỬA LỖI TẠI ĐÂY: Gộp chung script hoặc gọi riêng biệt đúng cú pháp
            $browser->script([
                "document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'",
                "document.querySelector('input[name=\"ngay_ve\"]').value = '2026-10-01'"
            ]);

            $browser->press('TÌM KIẾM VÉ')
                ->waitUntil('window.location.search.includes("ngay_ve") || window.location.pathname != "/"', 15)
                ->assertPathIsNot('/')
                ->pause(2000);

            echo PHP_EOL . "[PASS] TC_TK_02: Tìm kiếm vé khứ hồi thành công!" . PHP_EOL;
        });
    }

    //TC_TK_03: Tìm kiếm với nhiều loại hành khách (Người lớn, trẻ em, em bé)
    public function testMultiplePassengers_TC_TK_03()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('input[name="flight_type"]', 10)
                ->radio('flight_type', 'roundtrip')
                ->pause(500)
                ->select('id_san_bay_di', '1') // Nội Bài
                ->select('id_san_bay_den', '2'); // Phú Quốc (PQC)

            $browser->script([
                "document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'",
                "document.querySelector('input[name=\"ngay_ve\"]').value = '2026-10-01'",
                "document.querySelector('select[name=\"nguoi_lon\"]').value = '4'",
                "document.querySelector('select[name=\"tre_em\"]').value = '2'",
                "document.querySelector('select[name=\"em_be\"]').value = '2'"
            ]);

            /*$browser->select('nguoi_lon', '4')
                ->select('tre_em', '2')
                ->select('em_be', '2');*/

            $browser->press('TÌM KIẾM VÉ') // Chữ in hoa giống trong ảnh nút đỏ
                ->waitUntil('window.location.pathname != "/"', 15)
                ->assertPathIsNot('/')
                ->pause(2000); // Giữ lại 2s để xem thành quả

            echo PHP_EOL . "[PASS] TC_TK_03: Tìm kiếm với nhiều hành khách thành công!" . PHP_EOL;
        });
    }

    //TC_TK_04: không chọn điểm xuất phát
    public function testMissingOrigin_TC_TK_04()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('input[name="flight_type"]', 10)
                ->radio('flight_type', 'roundtrip')
                ->pause(500)
                // 1. Không chọn id_san_bay_di
                // 2. Chỉ chọn điểm đến (Lưu ý: name trong HTML của bạn là id_san_bay_den)
                ->select('id_san_bay_den', '2')
                ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");

            $browser->press('TÌM KIẾM VÉ');

            // 3. Logic kiểm tra lỗi:
            // Vì thiếu điểm đi, URL phải VẪN LÀ trang chủ '/'
            $browser->pause(1000)
                ->assertPathIs('/')
                ->pause(1000);

            echo PHP_EOL . "[PASS] TC_TK_04: Hệ thống đã chặn khi thiếu điểm xuất phát!" . PHP_EOL;
        });
    }

    //TC_TK_05: không chọn điểm đến
    public function testMissingDestination_TC_TK_05()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('input[name="flight_type"]', 10)
                    ->select('id_san_bay_di', '1')
                    ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");
            $browser->press('TÌM KIẾM VÉ');

            $browser->pause(1000)
                ->assertPathIs('/')
                ->pause(1000);
            echo PHP_EOL . "[PASS] TC_TK_05: Hệ thống chặn thành công khi thiếu thông tin!" . PHP_EOL;
        });
    }

    //TC_TK_06: Điểm đi trùng điểm đến
    public function testSameOriginAndDestination_TC_TK_06()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('id_san_bay_di', '1')
                ->select('id_san_bay_den', '1') // Chọn cùng ID 1
                ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");
            $browser->press('TÌM KIẾM VÉ');

            $browser->pause(1000)
                ->assertPathIs('/')
                ->pause(1000);

            echo "\n [PASS] TC_TK_06: Hệ thống chặn khi địa điểm trùng nhau!";
        });
    }

    //TC_TK_07: Lỗi khi Ngày về nhỏ hơn ngày đi
    public function testReturnDateBeforeDeparture_TC_TK_07()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitFor('input[name="flight_type"]', 10)
                ->radio('flight_type', 'roundtrip')
                ->pause(500)
                ->select('id_san_bay_di', '1')
                ->select('id_san_bay_den', '2')
                ->script([
                    "document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'",
                    "document.querySelector('input[name=\"ngay_ve\"]').value = '2026-09-30'" // Ngày về < Ngày đi
                ]);

            $browser->press('TÌM KIẾM VÉ')
                ->pause(1000);

            // Kiểm tra: Phải bị chặn lại ở trang chủ
            $browser->assertPathIs('/')
                // Chỉ dùng assertSee nếu bạn chắc chắn có chữ này hiện trên màn hình (không phải pop-up)
                // ->assertSee('ngày về phải sau ngày đi')
                ->pause(2000);

            echo PHP_EOL . "[PASS] TC_TK_07: Hệ thống chặn thành công khi ngày về nhỏ hơn ngày đi!";
        });
    }

    //TC_TK_08: không chọn ngày đi và ngày về
    public function testMissingAllDates_TC_TK_08()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('id_san_bay_di', '1')
                ->select('id_san_bay_den', '2')
                ->script([
                    "document.querySelector('input[name=\"ngay_di\"]').value = ''",
                    "document.querySelector('input[name=\"ngay_ve\"]').value = ''"
                ]);

        $browser->press('TÌM KIẾM VÉ')
            ->pause(1500) // Đợi Laravel xử lý và trả về lỗi
            ->assertPathIs('/');

            echo PHP_EOL . "[PASS] TC_TK_08: Hệ thống chặn thành công khi bỏ trống toàn bộ ngày.";
        });
    }

    //TC_TK_09: Có ngày về nhưng thiếu ngày đi
    public function testMissingDepartureDateOnly_TC_TK_09()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('id_san_bay_di', '1')
                ->select('id_san_bay_den', '2')
                ->script([
                    "document.querySelector('input[name=\"ngay_di\"]').value = ''",
                    "document.querySelector('input[name=\"ngay_ve\"]').value = '2026-10-15'"
                ]);

            $browser->press('TÌM KIẾM VÉ')
                ->pause(1500)
                ->assertPathIs('/');
                //->assertSee('Ngày đi không được để trống');

            echo PHP_EOL . "[PASS] TC_TK_09: Chặn thành công khi thiếu ngày đi.";
        });
    }

    //TC_TK_10: Tìm kiếm không có kết quả
    public function testNoFlightsFound_TC_TK_10()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->select('id_san_bay_di', '1')
                ->select('id_san_bay_den', '2')
                ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-12-31'");

            $browser->press('TÌM KIẾM VÉ')
                ->pause(2000)
                // Đợi cho đến khi không còn ở trang chủ nữa
                ->waitUntil('window.location.pathname != "/"', 10)
                ->assertSee('Không tìm thấy chuyến bay');

            echo PHP_EOL . "[PASS] TC_TK_10: Hiển thị thông báo không tìm thấy chuyến.";
        });
    }
}







//php artisan dusk tests/Browser/SearchFlightTest.php --testdox
