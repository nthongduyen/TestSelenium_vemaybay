<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Login_Test extends DuskTestCase
{
    // Tc_DN_01: Đăng nhập thành công
    public function testLoginSuccess_TC_DN_01()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(1500, 800)
                ->clickLink('Đăng nhập')
                ->waitFor('#email', 10)
                ->type('#email', '123@gmail.com')
                ->type('#password', '123123123')
                ->click('button[type="submit"]')
                ->pause(3000)
                ->script("if(document.querySelector('.swal2-confirm')) { document.querySelector('.swal2-confirm').click(); }");
            $browser->pause(2000)
                ->waitForLocation('/dashboard', 10)
                ->assertPathIs('/dashboard')
                ->assertSee('Cảm ơn bạn đã tin tưởng')
                ->clickLink('Về Trang Chủ')
                ->pause(2000)
                ->assertPathIs('/');
            });
    }

    //TC_DN_02: Sai email
    public function testLoginInvalidEmail_TC_DN_02()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->visit('/')
                ->waitForText('Đăng nhập', 10)
                ->clickLink('Đăng nhập')
                ->waitFor('#email', 10)
                ->type('#email', '123@gmailcom')
                ->type('#password', '123123123')
                ->click('.px-4')
                ->pause(2000)
                ->assertSee('Email hoặc mật khẩu không chính xác.')
                ->assertPathIsNot('/dashboard');
        });
    }


    //TC_DN_03: Sai mật khẩu
    public function testLoginWrongPassword_TC_DN_03()
    {
        $this->browse(function (Browser $browser) {
            //$browser->logout();//Lệnh này sẽ xóa toàn bộ session/cookie hiện tại
            $browser->visit('/')
                ->clickLink('Đăng nhập')
                ->waitFor('#email')
                ->type('#email', '123@gmail.com')
                ->type('#password', '123456789') // Mật khẩu sai
                ->click('.px-4')
                ->pause(2000)
                ->assertSee('Email hoặc mật khẩu không chính xác.')
                ->assertPathIsNot('/dashboard');
        });
    }

    //TC_DN_04: Email không tồn tại
    public function testLoginNonExistentEmail_TC_DN_04()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Đăng nhập')
                ->waitFor('#email')
                ->type('#email', '4543@gmail.com')
                ->type('#password', '12345678')
                ->press('ĐĂNG NHẬP') // Dùng press('TEXT') chuẩn hơn click class
                ->pause(2000)

                // ĐÃ SỬA: Khớp với nội dung tiếng Việt thực tế
                ->assertSee('Email hoặc mật khẩu không chính xác.')
                ->assertPathIs('/login');
        });
    }

    //TC_DN_05: Để trống Email
    public function testLoginEmptyEmail_TC_DN_05()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login') // Đi thẳng vào trang login để tăng tốc
                ->waitFor('#email')

                // 1. Xóa thuộc tính chặn của trình duyệt
                ->script("document.querySelectorAll('input').forEach(i => i.removeAttribute('required'));");

            $browser->type('email', '')
                ->type('password', '123123123')
                ->press('ĐĂNG NHẬP');

            // 2. Chờ đợi thông minh: Tiếp tục ngay khi thấy text, tối đa chờ 10s
            $browser->waitForText('Email không được để trống.', 30)
                ->assertSee('Email không được để trống.')
                ->assertPathIs('/login');
        });
    }

    //TC_DN_06: Để trống Mật khẩu
    public function testLoginEmptyPassword_TC_DN_06()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitFor('#email')
                ->script("document.querySelectorAll('input').forEach(i => i.removeAttribute('required'));");

            $browser->type('email', '123@gmail.com')
                ->type('password', '')
                ->press('ĐĂNG NHẬP');
            $browser->waitForText('Mật khẩu không được để trống.', 30)
                ->assertSee('Mật khẩu không được để trống.')
                ->assertPathIs('/login');
        });
    }
}


















//T1:chrome   .\vendor\laravel\dusk\bin\chromedriver-win32\chromedriver.exe --port=9515
//edge         .\vendor\laravel\dusk\bin\msedgedriver.exe --port=9515


//lẹnh chạy: php artisan dusk --filter=Login_Test
// php artisan dusk --filter=tên_hàm => VD: php artisan dusk --filter=testLoginSuccess_TC_DN_01

//T2: chạy file test:  php artisan dusk tests/Browser/Login_Test.php
