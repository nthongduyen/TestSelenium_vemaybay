1. Unit test

    === đăng nhập: AuthenticationTest
    // php artisan test tests/Feature/Auth/AuthenticationTest.php

    === đăng ký: RegistrationTest
    // php artisan test --filter=RegistrationTest

    === đổi mật khẩu: PasswordChangeTest
    //  php artisan test --filter=PasswordChangeTest

2. Integration Test

    === Quản lý chuyến bay và tồn kho ghế: FlightInventoryTest
    //  php artisan test --filter=FlightInventoryTest

    === Luồng đặt vé và thông tin khách hàng: BookingRelationshipTest
    //  php artisan test --filter BookingRelationshipTest

    === Áp dụng khuyến mãi và ghi nhận doanh thu: DiscountRevenueTest
    //  php artisan test --filter DiscountRevenueTest

    === Quản lý chuyến bay: QuanLyChuyenBayTest
    //  php artisan test --filter=QuanLyChuyenBayTest

3. System Test

=============== chạy auke ==========
=== mở web: mở 2 terminal để chạy web
    Terminal 1. npm run dev
    Terminal 2. php artisan serve

=== chạy bài test: mở 2 terminal
    T1: .\vendor\laravel\dusk\bin\chromedriver-win32\chromedriver.exe --port=9515
        ( .\vendor\laravel\dusk\bin\chromedriver-win32\chromedriver.exe --port=9515 )

    T2: chạy file test:  php artisan dusk tests/Browser/TC_DN_Test.php
        // lệnh chạy từng test 1: php artisan dusk --filter=tên_hàm => VD: php artisan dusk --filter=testLoginSuccess_TC_DN_01

===================

====================== dưới cũ =====================
    === Quá trình đặt vé máy bay: test/Brower/FightBookingTest
    //lệnh chạy tetminal2 : php artisan dusk
    //lệnh chạy telminal1: # Trong Terminal 1 (Nếu Terminal là PowerShell)
    //.\vendor\laravel\dusk\bin\chromedriver-win64\chromedriver.exe  ===. (Đảm bảo nó báo cổng 63430 hoặc cổng tương tự)-- chạy 2 telminal song song
    // telminal 1 hiện port là bn mở test/Unit/DuskTestCase.php tìm và thay giống số port

    ==== cách chạy drive chrome mở 3 telminal ko đc tắt  khi dow code về
    telminal 1: php artisan dusk:chrome - driver (khi dow code về cần chạy lệnh này để phù hợp hợp chromedriver)
    telminal 2: ./chromedriver -- port=9515
    telminal 3 chạy file bài: php artisan dusk test/Brower/FightBookingTest.php

    == chạy 1 test case: php artisan dusk tests/Brower/FightBookingTest.php --filter=tên function
    php artisan dusk tests/Browser/TC_DN_01Test

================
//lệnh chạy tetminal2 : php artisan dusk
//lệnh chạy telminal1: # Trong Terminal 1 (Nếu Terminal là PowerShell)
//.\vendor\laravel\dusk\bin\chromedriver-win64\chromedriver.exe  ===. (Đảm bảo nó báo cổng 63430 hoặc cổng tương tự)-- chạy 2 telminal song song
// telminal 1 hiện port là bn mở test/Unit/DuskTestCase.php tìm và thay giống số port
