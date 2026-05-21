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
                    ->select('id_san_bay_di', '1')
                    ->select('id_san_bay_den', '2')
                    ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");
            $browser->press('TÌM KIẾM VÉ')
                ->waitUntil('window.location.pathname != "/"', 15)
                ->assertPathIsNot('/')
                ->pause(2000);
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
            //echo PHP_EOL . "[PASS] TC_TK_02: Tìm kiếm vé khứ hồi thành công!" . PHP_EOL;
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
            $browser->press('TÌM KIẾM VÉ')
                ->waitUntil('window.location.pathname != "/"', 15)
                ->assertPathIsNot('/')
                ->pause(2000);
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
                ->select('id_san_bay_den', '2')
                ->script("document.querySelector('input[name=\"ngay_di\"]').value = '2026-10-01'");
            $browser->press('TÌM KIẾM VÉ');
            $browser->pause(1000)
                ->assertPathIs('/')
                ->pause(1000);
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
            $browser->press('TÌM KIẾM VÉ');
            $browser->pause(1000)
                ->assertPathIs('/')
                ->pause(1000);
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
            ->pause(1500)
            ->assertPathIs('/');
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
        });
    }
}







// php artisan dusk tests/Browser/SearchFlight_Test.php --testdox
