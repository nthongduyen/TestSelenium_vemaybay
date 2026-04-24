<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FlightManagement_Test extends DuskTestCase
{
    use \Tests\Browser\Traits\InteractsWithFilament;
    public function testAddFlightSuccess_TC_TCB_01()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VN1234';
            $browser->visit('/')
                ->resize(1500, 1000)
                ->clickLink('Đăng nhập')
                ->waitFor('#email', 10)
                ->type('#email', 'duyen@gmail.com')
                ->type('#password', '123123123')
                ->click('button[type="submit"]')
                ->pause(2000);

            if ($browser->element('.swal2-confirm')) {
                $browser->click('.swal2-confirm');
            }
            $browser->waitForLocation('/dashboard', 10)
                ->clickLink('Truy cập Trang Quản Trị')
                ->waitForLocation('/admin', 15)
                ->clickLink('Quản lý Chuyến bay')
                ->waitForText('Tạo chuyen bay mới', 10)
                ->clickLink('Tạo chuyen bay mới')

                ->waitFor('[id="data.ma_chuyen_bay"]');
            $browser->type('[id="data.ma_chuyen_bay"]', 'VN1234');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $browser->select('[id="data.trang_thai"]', 'dang_ban');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                ->type('[id="data.gia_ve"]', '5000000');
            $browser->click('[dusk="filament.forms.data.thoi_gian_di.open"]')
                ->pause(500)
                ->keys('#data\.thoi_gian_di', '13102026', '{tab}', '080000')
                ->keys('#data\.thoi_gian_di', '{enter}')
                ->pause(500);
            $browser->click('[dusk="filament.forms.data.thoi_gian_den.open"]')
                ->pause(500)
                ->keys('#data\.thoi_gian_den', '13102026', '{tab}', '100000')
                ->keys('#data\.thoi_gian_den', '{enter}')
                ->pause(1000);
            $browser->click('[dusk="filament.admin.action.create"]')
                ->waitForText('Đã tạo', 10);

            $browser->clickLink('Chuyến Bay')
                ->waitForLocation('/admin/chuyen-bays', 10);

            $browser->waitForText($maChuyenBay, 15)
                 ->assertSee($maChuyenBay);
        });
    }

    /*protected function selectFilamentChoice(Browser $browser, $fieldId, $searchKeyword, $exactMatch)
    {
        // Bước 1: Mở dropdown bằng JavaScript để tránh lỗi ElementState
        $browser->script("
            let select = document.getElementById('$fieldId');
            if (select) {
                let container = select.closest('.choices');
                container.dispatchEvent(new MouseEvent('mousedown', {bubbles: true}));
                container.click();
            }
        ");

        $browser->pause(1000);
        // Bước 2: Gõ từ khóa tìm kiếm bằng JavaScript để an toàn tuyệt đối
        // Thay vì dùng ->type(), ta gõ trực tiếp vào value và dispatch event input
        $browser->script("
            let select = document.getElementById('$fieldId');
            let container = select.closest('.choices');
            let input = container.querySelector('.choices__input--cloned');
            if (input) {
                input.value = '$searchKeyword';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new KeyboardEvent('keyup', { bubbles: true }));
            }
        ");

        $browser->pause(2500); // Đợi gợi ý (Phú Quốc, Phú Bài) hiện ra

        // Bước 3: Đợi và Click vào item chứa text chính xác
        $browser->script("
            let container = document.getElementById('$fieldId').closest('.choices');
            let items = container.querySelectorAll('.choices__item--selectable');
            let found = false;
            for (let item of items) {
                if (item.innerText.toLowerCase().includes('$exactMatch'.toLowerCase())) {
                    item.dispatchEvent(new MouseEvent('mousedown', {bubbles: true}));
                    item.click();
                    found = true;
                    break;
                }
            }
        ");

        $browser->pause(500);
    }*/
}
