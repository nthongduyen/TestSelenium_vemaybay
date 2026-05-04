<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FlightManagement_Test extends DuskTestCase
{
    use \Tests\Browser\Traits\InteractsWithFilament;

    protected function setUpAdmin(Browser $browser)
    {
        $user = \App\Models\User::where('email', 'duyen@gmail.com')->first();

        $browser->loginAs($user)
                ->resize(1500, 800)
                ->visit('/admin'); // Nhảy thẳng vào trang admin
    }

    public function testAddFlightSuccess_TC_TCB_01()
    {
        $this->browse(function (Browser $browser) {
            $maChuyenBay = 'VN456';

            $this->setUpAdmin($browser); // Gọi ở đây

            $browser->clickLink('Quản lý Chuyến bay')
                ->waitForText('Tạo chuyen bay mới', 10)
                ->clickLink('Tạo chuyen bay mới')
                ->waitFor('[id="data.ma_chuyen_bay"]');
            $browser->type('[id="data.ma_chuyen_bay"]', 'VN456');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A321');
            $browser->select('[id="data.trang_thai"]', 'dang_ban');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                ->type('[id="data.gia_ve"]', '5000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-15 08:00:00');
            $browser->pause(500);
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-15 10:00:00');
            $browser->pause(1000);
            $browser->click('[dusk="filament.admin.action.create"]')
                ->waitForText('Đã tạo', 30);
            $browser->visit('/admin/chuyen-bays')
                ->waitForLocation('/admin/chuyen-bays', 15)
                ->waitFor('input[type="search"]', 10)
                ->type('input[type="search"]', $maChuyenBay)
                ->pause(2000)
                ->assertSee($maChuyenBay);
        });
    }

    public function testAddFlightSuccess_TC_TCB_02()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/chuyen-bays/create')
                ->resize(1500, 800)
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);
            $maChuyenBayMoi = 'VJ' . rand(100, 999);
            $giaVeNgauNhien = rand(10, 90) . '00000'; // Ví dụ: 5000000
            $browser->type('[id="data.ma_chuyen_bay"]', $maChuyenBayMoi);
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $browser->select('[id="data.trang_thai"]', 'dang_ban');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Tâ', 'Tân Sơn Nhất');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', $giaVeNgauNhien);
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-11-15 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-11-15 10:30:00');
            $browser->pause(1000);
            $browser->press('Tạo & tiếp tục tạo mới');
            $browser->waitForText('Đã tạo', 15);
            $browser->assertPathIs('/admin/chuyen-bays/create');
            $browser->waitUntilMissingText($maChuyenBayMoi, 10);
            $this->assertEmpty($browser->inputValue('[id="data.ma_chuyen_bay"]'), 'Lỗi: Form không tự động reset sau khi nhấn Tạo & tiếp tục!');
        });
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
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======
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
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

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

    //TC_TCB_06: Thêm chuyến bay không thành công - Trống sân bay đến
    public function testAddFlightFailed_TC_TCB_06()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ673');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            $browser->waitForText('Sân bay đến không được để trống.', 10)
                    ->assertSee('Sân bay đến không được để trống.');
        });
    }

    //TC_TCB_07: Thêm chuyến bay không thành công - Trống trạng thái
    public function testAddFlightFailed_TC_TCB_07()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======
            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ842');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->select('[id="data.trang_thai"]', '');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            // Vì trạng thái thường là HTML Select, trình duyệt sẽ hiện bong bóng "Please select..."
            $isValid = $browser->script("return document.getElementById('data.trang_thai').checkValidity();")[0];

            if ($isValid) {
                // Nếu trình duyệt không chặn, mới kiểm tra class lỗi Filament
                $browser->assertPresent('.fi-fo-field-wrp-error-message');
            } else {
                $this->assertFalse($isValid);
            }
        });
    }

    //TC_TCB_08: Thêm chuyến bay không thành công - Trống thời gian đi
    public function testAddFlightFailed_TC_TCB_08()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======
            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ672');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            $isValid = $browser->script("return document.getElementById('data.thoi_gian_di').checkValidity();")[0];
            $browser->waitForText('Thời gian đi không được để trống.', 10)
                    ->assertSee('Thời gian đi không được để trống.');
        });
    }

    //TC_TCB_09: Thêm chuyến bay không thành công - Trống thời gian đến
    public function testAddFlightFailed_TC_TCB_09()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ671');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            $browser->waitForText('Thời gian đến không được để trống.', 10)
                    ->assertSee('Thời gian đến không được để trống.');
        });
    }

    //TC_TCB_10: Thêm chuyến bay không thành công - Trống giá vé
    public function testAddFlightFailed_TC_TCB_10()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======
            $browser->type('[id="data.ma_chuyen_bay"]', 'VJ670');
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}');
            $browser->click('[dusk="filament.admin.action.create"]')->pause(1000);
            $isValid = $browser->script("return document.getElementById('data.gia_ve').checkValidity();")[0];
            $this->assertFalse($isValid);
        });
    }

    //TC_TCB_11: Thêm chuyến bay không thành công - Mã chuyến bay trùng
    public function testAddFlightFailed_TC_TCB_11()
    {
        $this->browse(function (Browser $browser) {
            $maTrung = 'VN123';

            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            $browser->type('[id="data.ma_chuyen_bay"]', $maTrung);
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $browser->select('[id="data.trang_thai"]', 'dang_ban');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            $browser->click('[dusk="filament.admin.action.create"]');
            $browser->waitForText('Mã chuyến bay đã được sử dụng.', 15)
                    ->assertSee('Mã chuyến bay đã được sử dụng.');
        });
    }

    //TC_TCB_12: Thêm chuyến bay không thành công - Mã sai định dạng (Ký tự đặc biệt)
    public function testAddFlightFailed_TC_TCB_12()
    {
        $this->browse(function (Browser $browser) {
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            // 1. Nhập mã có ký tự đặc biệt
            $browser->type('[id="data.ma_chuyen_bay"]', '@@@@@');

            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');
            // 2. Nhấn nút "Tạo"
            $browser->click('[dusk="filament.admin.action.create"]');

            // 3. Đợi chữ xuất hiện trực tiếp trên màn hình thay vì đợi selector class
            $browser->waitForText('Định dạng mã chuyến bay không hợp lệ hoặc chứa ký tự đặc biệt.', 10)
                    ->assertSee('Định dạng mã chuyến bay không hợp lệ hoặc chứa ký tự đặc biệt.');
        });
    }

    //TC_TCB_13: Thêm chuyến bay không thành công - Giá vé bằng 0
    public function testAddFlightFailed_PriceZero_TC_TCB_13()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            /* === chạy file===
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);*/ //=====

                // ====chạy từng test 1
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);    //======

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '0');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');
            $browser->waitForText('Giá vé (VND) phải lớn hơn 0.', 10)
                    ->assertSee('Giá vé (VND) phải lớn hơn 0.');
        });
    }

    //TC_TCB_14: Thêm chuyến bay không thành công - Giá vé âm
    public function testAddFlightFailed_PriceNegative_TC_TCB_14()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create');

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '-3000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');

            // Hệ thống phải chặn và hiện lỗi "ít nhất 1"
            $browser->waitForText('Giá vé (VND) phải lớn hơn 0.', 10)
                    ->assertSee('Giá vé (VND) phải lớn hơn 0.');
        });
    }

    //TC_TCB_15: Giá vé là chữ
    public function testAddFlightFailed_PriceIsString_TC_TCB_15()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create');

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', 'ba triệu đồng');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');
            $isValid = $browser->script("return document.getElementById('data.gia_ve').checkValidity();")[0];
            $this->assertFalse($isValid, "Trình duyệt lẽ ra phải chặn vì giá vé không phải là số.");
        });
    }

    //TC_TCB_16: Sân bay đi trùng sân bay đến
    public function testAddFlightFailed_DuplicateAirport_TC_TCB_16()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create');

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Nộ', 'Nội Bài');
            $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 10:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');

            $browser->waitForText('Sân bay đi và sân bay đến không được trùng nhau.', 10)
                    ->assertSee('Sân bay đi và sân bay đến không được trùng nhau.');
        });
    }

    //TC_TCB_17: Thêm chuyến bay không thành công - Thời gian đến nhỏ hơn thời gian đi
    public function testAddFlightFailed_ArrivalBeforeDeparture_TC_TCB_17()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                ->waitFor('[id="data.ma_chuyen_bay"]', 10);

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
             $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2026-10-25 10:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2026-10-25 08:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');
            $browser->waitForText('Thời gian đến phải lớn hơn thời gian đi.', 10)
                    ->assertSee('Thời gian đến phải lớn hơn thời gian đi.');
        });
    }

    //TC_TCB_18: Giờ đến = giờ đi
    public function testAddFlightFailed_ArrivalEqualDeparture_TC_TCB_18()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create');

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
             $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');

            $time = '2026-10-25 09:00:00';
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', $time);
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', $time);

            $browser->click('[dusk="filament.admin.action.create"]');

            $browser->waitForText('Thời gian đến phải lớn hơn thời gian đi', 10)
                    ->assertSee('Thời gian đến phải lớn hơn thời gian đi');
        });
    }

    //TC_TCB_19: Ngày quá khứ
    public function testAddFlightFailed_PastDate_TC_TCB_19()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create');

            $browser->type('[id="data.ma_chuyen_bay"]', 'VN' . rand(100, 999));
            $this->selectFilamentChoice($browser, 'data.id_may_bay', 'Ai', 'Airbus A320');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_di', 'Nộ', 'Nội Bài');
            $this->selectFilamentChoice($browser, 'data.id_san_bay_den', 'Ph', 'Phú Quốc');
             $browser->keys('[id="data.gia_ve"]', ['{control}', 'a'], '{backspace}')
                    ->type('[id="data.gia_ve"]', '6000000');
            // Chọn ngày 01/01/2020
            $this->setFilamentDateTime($browser, 'data.thoi_gian_di', '2020-01-01 08:00:00');
            $this->setFilamentDateTime($browser, 'data.thoi_gian_den', '2020-01-01 10:00:00');

            $browser->click('[dusk="filament.admin.action.create"]');

            $browser->waitForText('Không được chọn thời gian trong quá khứ', 10)
                    ->assertSee('Không được chọn thời gian trong quá khứ');
        });
    }
    //TC_TCB_20: Kiểm tra chức năng nút Quay lại
    public function testCancelCreateFlight_TC_TCB_20()
    {
        $this->browse(function (Browser $browser) {
            $this->setUpAdmin($browser);
            $browser->visit('/admin/chuyen-bays/create')
                    ->waitFor('[id="data.ma_chuyen_bay"]');
            $browser->click('a[href$="/admin/chuyen-bays"]')
                    ->pause(1000);
            // Kiểm tra xem đã về trang danh sách chưa
            $browser->assertPathIs('/admin/chuyen-bays');
        });
    }
}
