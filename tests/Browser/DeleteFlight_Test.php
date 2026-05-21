<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeleteFlight_Test extends DuskTestCase
{
    use \Tests\Browser\Traits\InteractsWithFilament;

    protected function setUpAdmin(Browser $browser)
    {
        $user = \App\Models\User::where('email', 'duyen@gmail.com')->first();

        $browser->loginAs($user)
                ->resize(1500, 800)
                ->visit('/admin');
    }


    //TC_XCB_01: Xóa một chuyến bay đơn lẻ từ trang danh sách
    public function testDeleteFlightSuccess_TC_XCB_01()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VN33333';
            $this->setUpAdmin($browser);
            $browser->clickLink('Quản lý Chuyến bay')
                ->waitFor('input[type="search"]', 10)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000);
            $browser->waitFor('[dusk="filament.tables.action.delete"]')
                ->click('[dusk="filament.tables.action.delete"]');
            $browser->waitForText('Xóa chuyen bay', 50)
                ->press('Xác nhận');
            $browser->waitForText('Đã xóa', 50)
                ->assertSee('Đã xóa');
            $browser->pause(1000)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(3000)
                ->assertDontSee($maChuyenBay);
        });
    }

    //TC_XCB_02: Xóa chuyến bay từ giao diện Chỉnh sửa (Edit Page)
    public function testDeleteFlightFromEditPage_TC_XCB_02()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VJ4242';
            $this->setUpAdmin($browser);
            $browser->clickLink('Quản lý Chuyến bay')
                ->waitFor('input[type="search"]', 15)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000);
            $browser->waitFor('a[href*="/edit"]', 10)
                ->click('a[href*="/edit"]');
            $browser->waitFor('[dusk="filament.admin.action.delete"]', 15)
                ->assertSee('Xóa');
            $browser->click('[dusk="filament.admin.action.delete"]');
            $browser->waitForText('Xác nhận', 50)
                ->press('Xác nhận');
            $browser->waitForText('Đã xóa', 50)
                ->assertSee('Đã xóa');
            $browser->visit('/admin/chuyen-bays')
                ->waitFor('input[type="search"]', 100)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000)
                ->assertDontSee($maChuyenBay);
        });
    }

    //TC_XCB_03: Kiểm tra chức năng Hủy khi thực hiện xóa chuyến bay
    public function testCancelDeleteFlight_TC_XCB_03()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VJ1310';
            $this->setUpAdmin($browser);
            $browser->clickLink('Quản lý Chuyến bay')
                ->waitFor('input[type="search"]', 10)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000);
            $browser->waitFor('[dusk="filament.tables.action.delete"]')
                ->click('[dusk="filament.tables.action.delete"]');
            $browser->waitForText('Xóa chuyen bay', 20);
            $browser->script("
                let buttons = Array.from(document.querySelectorAll('button'));
                let cancelButton = buttons.find(btn => btn.innerText.includes('Huỷ'));
                if (cancelButton) cancelButton.click();
            ");
            $browser->waitUntilMissingText('Xóa chuyen bay', 30);
            $browser->assertDontSee('Đã xóa');
            $browser->assertSee($maChuyenBay);
            $browser->refresh()
                ->waitFor('input[type="search"]', 30)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000)
                ->assertSee($maChuyenBay);
        });
    }
}
//php artisan dusk --filter=testLoginAndShowAllFlights
