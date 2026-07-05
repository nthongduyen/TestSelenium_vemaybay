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

=== chạy bài test: mở 2 terminal "=== chrome==
    T1: .\vendor\laravel\dusk\bin\chromedriver-win32\chromedriver.exe --port=9515
        ( .\vendor\laravel\dusk\bin\chromedriver-win32\chromedriver.exe --port=9515 )

    T2: chạy file test:  php artisan dusk tests/Browser/TC_DN_Test.php
        // lệnh chạy từng test 1: php artisan dusk --filter=tên_hàm => VD: php artisan dusk --filter=testLoginSuccess_TC_DN_01

===================

==================

/// cài Edge
b1: vào edge link : "Microsoft Edge Driver" tải phiên bản cùng vs edge là 149. x64
b2: cài xong , giải lén, tim file =="msedgedriver" copy vào =="E:\Xampp\htdocs\vemaybay-main\vendor\laravel\dusk\bin\" cùng hàng vs file chrome
b3: vào file .en thêm dòng "DUSK_BROWSER=edge" vào cuối cùng file ( nếu muốn chạy chrome thì xóa edge thành "DUSK_BROWSER=chrome")
b4: sửa code file =="DuskTestCase.php" có lưu trên driver
=================
 cách chạy trên ========edge========
t1:  .\vendor\laravel\dusk\bin\msedgedriver.exe --port=9515
t2:   file test

==== ĐỂ CHẠY TRÊN chrome hay edge cần làm như sau===
vào file  .env ở cuối file có Dòng " DUSK_BROWSER=edge " đây là chạy trên Edge
vào file  .env ở cuối file có Dòng " DUSK_BROWSER=chrome " đây là chạy trên chrome
-- Sau mỗi lần đổi trình duyệt cần crtl + c để cập nhật phiên bản cho mỗi trình duyệt (tức là teminal chạy  // cùng với lệnh chạy test case)


=== tk đăng nhập ===
Admin:
    Mk: lsdz11021
T   TK: an2392k4@gmail.com

    MK:duyen@gmail.com
    MK: 123123123
User
    TK: 123@gmail.com
    MK: 123123123










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
