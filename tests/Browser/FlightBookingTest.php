<?php

namespace Tests\Browser;

use App\Models\NguoiDung as User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FlightBookingTest extends DuskTestCase
{
    // Sử dụng DatabaseMigrations để đảm bảo test chạy độc lập và dữ liệu giả được dọn dẹp.
    use DatabaseMigrations;

    /**
     * Helper function để thực hiện thao tác đăng nhập.
     *
     * @param Browser $browser
     * @param string $email
     * @param string $password
     * @return void
     */
    protected function attemptLogin(Browser $browser, $email, $password)
    {
        $browser->visit('/login')
                ->waitFor('#email', 10)
                ->type('#email', $email)
                ->type('password', $password)
                // Sử dụng selector ổn định nhất cho nút submit trong form.
                ->press('button[type="submit"]');
    }

    /**
     * Test T.C 1: Đăng nhập thành công với người dùng hợp lệ.
     *
     * @return void
     */
    public function testSuccessfulLogin()
    {
        // 1. Chuẩn bị dữ liệu (Factory đã được sửa để chỉ dùng 'mat_khau')
        $user = User::factory()->create([
            'email' => '123@gmail.com',
            'mat_khau' => bcrypt('123123123'), // Chỉ định rõ cột mat_khau
        ]);

        // 2. Thực hiện test
        $this->browse(function (Browser $browser) use ($user) {
            $this->attemptLogin($browser, '123@gmail.com', '123123123');

            // 3. Khẳng định kết quả: Chờ chuyển hướng về trang chủ và chờ text chính xác
            $browser->waitForLocation('/dashboard', 10)
                   ->assertSee('Dashboard');
                    // Chờ text chính xác xuất hiện trên trang chủ
                    //->waitForText('Tìm kiếm chuyến bay', 10);
        });
    }



    /**
     * Test T.C 4: Tìm kiếm chuyến bay thành công.
     * Cập nhật: Sửa lỗi Timeout bằng cách chờ text.
     *
     * @return void
     */
    public function testFlightSearchSuccess()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/trang-chu')
                    // Chờ text chính xác xuất hiện trên trang chủ
                    ->waitFor('#flight-search-form', 10);

            // 1. Điền thông tin tìm kiếm
            $browser->select('noi_di', 'Hà Nội')
                    ->select('noi_den', 'TP. Hồ Chí Minh')
                    ->type('ngay_di', '2025-12-30')
                    ->press('Tìm kiếm');

            // 2. Khẳng định kết quả
            $browser->waitForPath('/ket-qua', 10)
                    ->assertSee('Danh sách chuyến bay'); // Kiểm tra text xác nhận đã đến trang kết quả
        });
    }

    /**
     * Test T.C 5: Quy trình đặt vé máy bay thành công (Bao gồm cả Đăng nhập).
     *
     * @return void
     */
    public function testFullFlightBookingSuccess()
    {
        // 1. Chuẩn bị dữ liệu (Factory đã được cập nhật)
        $user = User::factory()->create([
            'email' => 'test_booking@example.com',
            'mat_khau' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Bước 1: Đăng nhập
            $this->attemptLogin($browser, 'test_booking@example.com', 'password');
            $browser->assertPathIs('/trang-chu')
                    ->waitForText('Tìm kiếm chuyến bay', 10); // Chờ text

            // Bước 2: Tìm kiếm chuyến bay (Tái sử dụng code tìm kiếm)
            $browser->select('noi_di', 'Hà Nội')
                    ->select('noi_den', 'TP. Hồ Chí Minh')
                    ->type('ngay_di', '2025-12-30')
                    ->press('Tìm kiếm');

            // Chờ đến trang kết quả tìm kiếm
            $browser->waitForPath('/ket-qua', 10)
                    ->assertSee('Danh sách chuyến bay');

            // Bước 3: Chọn chuyến bay (Giả sử chuyến bay đầu tiên có nút "Đặt vé")
            $browser->click('@book-flight-button-1') // Dùng Dusk Selector cho nút đặt vé
                    ->waitForPath('/dat-ve', 10); // Chờ chuyển sang trang đặt vé

            // Bước 4: Điền thông tin khách hàng (KIỂM TRA LẠI CÁC TÊN TRƯỜNG: ho_ten, so_dien_thoai)
            $browser->type('ho_ten', 'Nguyễn Văn A')
                    ->type('so_dien_thoai', '0987654321')
                    ->press('Xác nhận đặt vé');

            // Bước 5: Khẳng định hoàn tất
            $browser->waitForPath('/thanh-cong', 10)
                    ->assertSee('Đặt vé thành công');
        });
    }
}
//Quá trình Đặt Vé Máy Bay (Flight Booking Flow)

//tetminal2 : php artisan dusk
//telminal1: # Trong Terminal 1 (Nếu Terminal là PowerShell)
//.\vendor\laravel\dusk\bin\chromedriver-win64\chromedriver.exe  ===. (Đảm bảo nó báo cổng 63430 hoặc cổng tương tự)



//tests/DuskTestCase.php
//database\migrations/2025_11_20_150035_update_chuyen_bay_trang_thai_column.php
