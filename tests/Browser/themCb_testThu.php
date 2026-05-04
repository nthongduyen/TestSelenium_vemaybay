<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class themCb_testThu extends DuskTestCase
{
    use \Tests\Browser\Traits\InteractsWithFilament;

    protected function setUpAdmin(Browser $browser)
    {
        $user = \App\Models\User::where('email', 'duyen@gmail.com')->first();

        $browser->loginAs($user)
                ->resize(1500, 800)
                ->visit('/admin'); // Nhảy thẳng vào trang admin
    }

     //TC_TCB_03: Thêm chuyến bay không thành công - Trống mã chuyến bay
    public function testAddFlightFailed_TC_TCB_03()
    {
        $this->browse(function (Browser $browser) {

            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Th', 'Thọ Xuân');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Bu', 'Buôn Ma Thuột');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]')
                ->pause(1000);
            $isValid = $browser->script("return document.getElementById('data.ma_chuyen_bay').checkValidity();")[0];
            $this->assertFalse($isValid, 'Lỗi: Mã chuyến bay trống nhưng form vẫn cho phép submit!');
        });
    }
    //TC_TCB_04: Thêm chuyến bay không thành công - Trống tên máy bay
    public function testAddFlightFailed_TC_TCB_04()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);


            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ675');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]');
            $browser->waitForText('Máy bay không được để trống.', 10)
                    ->assertSee('Máy bay không được để trống.');
        });
    }

    //TC_TCB_05: Thêm chuyến bay không thành công - Trống sân bay đi
    public function testAddFlightFailed_TC_TCB_05()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);


            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ674');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            $browser->waitForText('Sân bay đi không được để trống.', 10)
                    ->assertSee('Sân bay đi không được để trống.');
        });
    }

}
