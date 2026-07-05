<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UpdateFlight_Test extends DuskTestCase
{
    use \Tests\Browser\Traits\InteractsWithFilament;
    protected function setUpAdmin(Browser $browser)
    {
        $user = \App\Models\User::where('email', 'duyen@gmail.com')->first();
        $browser->loginAs($user)
                ->resize(1500, 800)
                ->visit('/admin');
    }
    protected function navigateToEditPage(Browser $browser, $maCB)
    {
        $browser->visit('/admin/chuyen-bays')
            ->waitFor('input[type="search"]', 10)
            ->type('input[type="search"]', $maCB)
            ->pause(2000)
            ->click('a[href*="/edit"]')
            ->waitFor('.choices__inner', 15);
    }

    //TC_SCB_01: Sửa giá vé thành công
    public function testUpdateFlightSuccess_TC_SCB_01()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VJ903';
            $giaVeMoi = '2000000';
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, $maChuyenBay);
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                ->type('[id="data.gia_ve"]', $giaVeMoi)
                ->pause(500)
                ->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Đã lưu', 10);
            $browser->visit('/admin/chuyen-bays')
                ->waitFor('input[type="search"]')
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000)
                ->assertSee('2.000.000');
        });
    }
    //TC_SCB_02: Sửa nhiều trường cùng lúc (Sân bay, Giờ đi và giờ đến)
    public function testUpdateMultipleFields_TC_SCB_02()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'QH221';
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, $maChuyenBay);
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ta', 'Tân Sơn Nhất');
            $browser->pause(700);
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-12-10 11:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-12-10 12:00:00');
            $browser->click('[dusk="filament.admin.action.save"]')
                    ->waitForText('Đã lưu');
            $browser->visit('/admin/chuyen-bays')
                    ->waitFor('input[type="search"]')
                    ->type('input[type="search"]', $maChuyenBay)
                    ->pause(2000);
            $browser->assertSee('SGN');
        });
    }

    //TC_SCB_03: Xóa mã chuyến bay
    public function testUpdateFlightFail_EmptyCode_TC_SCB_03()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VJ903';
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, $maChuyenBay);
            // Xóa mã chuyến bay bằng cách chọn tất cả và nhấn Backspace
            $browser->keys('[id="data.ma_chuyen_bay"]', ['{control}', 'a'], '{backspace}')
                ->pause(500)
                ->click('[dusk="filament.admin.action.save"]');
            $isValid = $browser->script("return document.getElementById('data.ma_chuyen_bay').checkValidity();")[0];
            $this->assertFalse($isValid, 'Lỗi: Mã chuyến bay trống nhưng form vẫn cho phép submit!');
        });
    }
    //TC_SCB_04: Không chọn máy bay (Nhấn nút x để xóa)
    public function testUpdateFlightFail_EmptyAirplane_TC_SCB_04()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VJ903';
            $this->setUpAdmin($browser);
            $browser->clickLink('Quản lý Chuyến bay')
                ->waitFor('input[type="search"]')
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000)
                ->click('a[href*="/edit"]')
                // Chờ thẻ div bao bọc dropdown máy bay hiển thị thay vì chờ ID trực tiếp
                ->waitFor('.choices__list--single', 10);
            // 1. Nhấn nút 'x' để xóa máy bay (Dựa trên HTML button.choices__button)
            $browser->click('.choices__button')
                ->pause(500);
            // 2. Nhấn nút Lưu
            $browser->click('[dusk="filament.admin.action.save"]');
            // 3. Kiểm tra thông báo lỗi hiển thị ngay dưới trường máy bay
            $browser->waitForText('Máy bay không được để trống.', 50)
                ->assertSee('Máy bay không được để trống.');
        });
    }

    public function testUpdateFlightFail_EmptyDeparture_TC_SCB_05()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            // THAY THẾ XPATH CŨ BẰNG LỆNH GỌI HÀM DÙNG ID NÀY:
            $this->clearFilamentInput($browser, 'data.id_san_bay_di');

            $browser->click('[dusk="filament.admin.action.save"]');

            $browser->waitForText('sân bay đi không được để trống.', 10)
                ->assertSee('sân bay đi không được để trống.');
        });
    }
    public function testUpdateFlightFail_EmptyArrival_TC_SCB_06()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            // THAY THẾ XPATH CŨ BẰNG LỆNH GỌI HÀM DÙNG ID NÀY:
            $this->clearFilamentInput($browser, 'data.id_san_bay_den');

            $browser->click('[dusk="filament.admin.action.save"]');

            $browser->waitForText('sân bay đến không được để trống.', 10)
                ->assertSee('sân bay đến không được để trống.');
        });
    }

    //TC_SCB_07: Sửa không thành công - Xóa thời gian đi
    public function testUpdateFlightFailed_EmptyDepartureTime_TC_SCB_07()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $this->clearFilamentInput($browser, 'data.thoi_gian_di');

            $browser->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Thời gian đi không được để trống.', 10)
                ->assertSee('Thời gian đi không được để trống.');
        });
    }
    //TC_SCB_08: Sửa không thành công - Xóa thời gian đến
    public function testUpdateFlightFailed_EmptyArrivalTime_TC_SCB_08()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $this->clearFilamentInput($browser, 'data.thoi_gian_den');

            $browser->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Thời gian đến không được để trống.', 10)
                ->assertSee('Thời gian đến không được để trống.');
        });
    }
    //TC_SCB_09: Sửa không thành công - Xóa giá vé
    public function testUpdateFlightFailed_EmptyPrice_TC_SCB_09()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $this->clearFilamentInput($browser, 'data.gia_ve');

            $browser->click('[dusk="filament.admin.action.save"]')->pause(1000);
            $isValid = $browser->script("return document.getElementById('data.gia_ve').checkValidity();")[0];
            $this->assertFalse($isValid);
        });
    }
    //TC_SCB_10: Sửa không thành công - Giá vé âm
    public function testUpdateFlightFailed_NegativePrice_TC_SC_10()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $browser->type('input[id="data.gia_ve"]', '-1000000')
                ->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Giá vé phải lớn hơn 0.', 30)
                ->assertSee('Giá vé phải lớn hơn 0.');
        });
    }
    //TC_SCB_11: Sửa không thành công - Giá vé bằng 0
    public function testUpdateFlightFailed_ZeroPrice_TC_SCB_11()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $browser->type('input[id="data.gia_ve"]', '0')
                ->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Giá vé phải lớn hơn 0.', 30)
                ->assertSee('Giá vé phải lớn hơn 0.');
        });
    }
    //TC_SCB_12: Sửa không thành công - Giá vé là chữ
    public function testUpdateFlightFailed_StringPrice_TC_SCB_12()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            $browser->keys('input[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                ->type('input[id="data.gia_ve"]', 'abc')
                ->pause(1000) // Tạm dừng để thấy rõ giá trị nhập vào
                ->click('[dusk="filament.admin.action.save"]');
            $browser->waitForText('Giá vé phải là số.', 30)
                    ->assertSee('Giá vé phải là số.');
        });
    }
    //TC_SCB_13: Sửa không thành công - Sân bay đi trùng sân bay đến
    public function testUpdateFlightFailed_SameAirports_TC_SCB_13()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            // Chọn sân bay đi và sân bay đến giống nhau (ví dụ: cùng là Tân Sơn Nhất)
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Ta', 'Tân Sơn Nhất');
            $browser->pause(500);
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ta', 'Tân Sơn Nhất');

            $browser->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Sân bay đi và sân bay đến không được trùng nhau.', 300)
                ->assertSee('Sân bay đi và sân bay đến không được trùng nhau.');
        });
    }
    //TC_SCB_14: Sửa không thành công - Thời gian đến trước thời gian đi (cùng ngày)
    public function testUpdateFlightFailed_ArrivalBeforeDeparture_TC_SCB_14()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');

            // Thiết lập thời gian đến sớm hơn thời gian đi
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-01 10:00:00');
            $browser->pause(500);
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-01 08:00:00');

            $browser->click('[dusk="filament.admin.action.save"]')
                ->waitForText('Thời gian đến phải lớn hơn thời gian đi.', 30)
                ->assertSee('Thời gian đến phải lớn hơn thời gian đi.');
        });
    }
    //TC_SCB_15: Chức năng quay lại
    public function testNavigationBack_TC_SCB_15()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $this->navigateToEditPage($browser, 'VJ903');
            $browser->clickLink('Quay lại')
                ->assertPathIs('/admin/chuyen-bays');
        });
    }

}
