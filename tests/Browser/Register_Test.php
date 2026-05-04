<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Register_Test extends DuskTestCase
{
    // TC_DK_01: Đăng ký thành công
    public function testRegisterSuccess_TC_DK_01()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->resize(1500,800)
                ->clickLink('Đăng ký')
                ->waitFor('#name',10)
                ->type('#name','Nguyen Thi Hong')
                //->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#email', 'newuser12@example.com')
                ->type('#password','Abc@1234')
                ->type('#password_confirmation','Abc@1234')
                ->type('#so_dien_thoai','0901234567')
                ->type('#dia_chi','Ha Noi')
                ->press('button[type=submit]')
                ->pause(2000)
                ->script("if(document.querySelector('.swal2-confirm')) { document.querySelector('.swal2-confirm').click(); }");
            $browser->pause(2000)
                ->waitForLocation('/dashboard', 10)
                ->assertPathIs('/dashboard')
                ->assertSee('Cảm ơn bạn đã tin tưởng')
                ->clickLink('Về Trang Chủ')
                ->pause(700)
                ->assertPathIs('/')
                ->click('.relative button')
                ->waitForText('Log Out')
                ->clickLink('Log Out')
                ->assertPathIs('/');
        });
    }

    //TC_DK_02 – Email đã tồn tại
    public function testRegisterEmailExists_TC_DK_02()
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('/')
                ->resize(1500,800)
                ->clickLink('Đăng ký')
                ->waitFor('#name',10)

                ->type('#name','Nguyen Thi Hong')
                ->type('#email','123@gmail.com')
                ->type('#password','Abc@1234')
                ->type('#password_confirmation','Abc@1234')
                ->type('#so_dien_thoai','0901234567')
                ->type('#dia_chi','Ha Noi')
                ->press('button[type=submit]')
                ->waitForText('Email đã được sử dụng', 10)
                ->assertSee('Email đã được sử dụng');
        });
    }
            /*$browser->visit('/')
                ->resize(1500,800)
                ->clickLink('Đăng ký')*/ // chạy từ đầu ở home


    // TC_DK_03 – Email bỏ trống
    public function testRegisterEmailEmpty_TC_DK_03()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->resize(1500, 800)
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email', '')
                ->type('#password', 'Abc@1234')
                ->type('#password_confirmation', 'Abc@1234')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Ha Noi')

                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");
            $browser->press('button[type=submit]')
                ->waitForText('Email không được để trống.', 10)
                ->assertSee('Email không được để trống.');
            //Kiểm tra xem trình duyệt có chặn lại và vẫn ở trang cũ không
            //->press('button[type=submit]');
            //$browser->assertPathIs('/register');
        });
    }

    // TC_ĐK_04: Đăng ký với Email sai định dạng
    public function testRegisterEmailInvalid_TC_DK_04()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email', '123gmail.com')
                ->type('#password', 'Abc@1234')
                ->type('#password_confirmation', 'Abc@1234')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Hà Nội')
                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");
            $browser->press('button[type=submit]')
                ->waitForText('Email phải là một địa chỉ email hợp lệ.', 10)
                ->assertSee('Email phải là một địa chỉ email hợp lệ.');
        });
    }

    // TC_ĐK_05: Mật khẩu không đủ ký tự tối thiểu
    public function testRegisterPasswordMinLength_TC_DK_05()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123sd')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Hà Nội')
                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");

            $browser->press('button[type=submit]')
                ->waitForText('Mật khẩu phải có ít nhất 8 ký tự.', 10)
                ->assertSee('Mật khẩu phải có ít nhất 8 ký tự.');
        });
    }

    // TC_ĐK_06: Mật khẩu bị bỏ trống
    public function testRegisterPasswordEmpty_TC_DK_06()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Hà Nội')
                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");
            $browser->press('button[type=submit]')
                ->waitForText('Mật khẩu không được để trống.', 10)
                ->assertSee('Mật khẩu không được để trống.');
        });
    }

    // TC_ĐK_07: Số điện thoại không bắt đầu bằng số 0
    public function testRegisterPhoneStartWithZero_TC_DK_07()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Van A')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123123123')
                ->type('#so_dien_thoai', '9012345672')
                ->type('#dia_chi', 'Hà Nội')
                ->press('button[type=submit]')
                ->waitForText('Số điện thoại phải bắt đầu bằng số 0.', 10)
                ->assertSee('Số điện thoại phải bắt đầu bằng số 0.');
        });
    }

    // TC_ĐK_08: Số điện thoại < 10 số
    public function testRegisterPhoneLength_TC_DK_08()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123123123')
                ->type('#so_dien_thoai', '090123456')
                ->type('#dia_chi', 'Hà Nội')
                ->press('button[type=submit]')
                ->waitForText('Số điện thoại phải đủ 10 chữ số.', 10)
                ->assertSee('Số điện thoại phải đủ 10 chữ số.');
        });
    }

    // TC_ĐK_09: Xác nhận lại mật khẩu không khớp
    public function testRegisterPasswordNotMatch_TC_DK_09()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyen Thi Hong')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123456123')
                ->type('#so_dien_thoai', '0956123456')
                ->type('#dia_chi', 'Hà Nội')
                ->press('button[type=submit]')
                ->waitForText('Mật khẩu xác nhận không khớp.', 10)
                ->assertSee('Mật khẩu xác nhận không khớp.');
        });
    }

    // TC_ĐK_10: Họ và tên bị bỏ trống
    public function testRegisterNameEmpty_TC_DK_10()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', '')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123123123')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Hà Nội')
                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");
            $browser->press('button[type=submit]')
                ->waitForText('Họ tên không được để trống.', 10)
                ->assertSee('Họ tên không được để trống.');
        });
    }

    // TC_ĐK_11: Họ và tên chứa ký tự đặc biệt và số
    public function testRegisterNameInvalid_TC_DK_11()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyễn Thị & An 123')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123123123')
                ->type('#so_dien_thoai', '0901234567')
                ->type('#dia_chi', 'Hà Nội')
                ->press('button[type=submit]')
                ->waitForText('Họ tên không được chứa ký tự đặc biệt hoặc số.', 10)
                ->assertSee('Họ tên không được chứa ký tự đặc biệt hoặc số.');
        });
    }

    // TC_ĐK_12: Địa chỉ bị bỏ trống
    public function testRegisterAddressEmpty_TC_DK_12()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->type('#name', 'Nguyễn Thị An')
                ->type('#email','newuser'.rand(1,9999).'@gmail.com')
                ->type('#password', '123123123')
                ->type('#password_confirmation', '123123123')
                ->type('#so_dien_thoai', '0901234568')
                ->type('#dia_chi', '')
                ->script("document.querySelector('form').setAttribute('novalidate', 'novalidate');");
            $browser->press('button[type=submit]')
                ->waitForText('Địa chỉ không được để trống.', 10)
                ->assertSee('Địa chỉ không được để trống.');
        });
    }
}
//php artisan dusk tests/Browser/Register_Test.php  ,   php artisan dusk --filter=tên_hàm
