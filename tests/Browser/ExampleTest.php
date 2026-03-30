<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Kiểm tra trang chủ có tải thành công không.
     *
     * @return void
     */
    public function testBasicExample()
    {
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                // Vấn đề thường xảy ra ở đây.
                // Đảm bảo assertSee chỉ nhận chuỗi, không phải biến $browser
                ->assertSee('Tìm kiếm chuyến bay');
        });
    }
}
