<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    // BẮT BUỘC: Sử dụng RefreshDatabase để reset database sau mỗi test
    use RefreshDatabase;

    /**
     * Thiết lập các cấu hình cần thiết trước khi chạy mỗi bài test.
     */
    protected function setUp(): void
    {
        // Phải gọi parent::setUp() trước
        parent::setUp();

        // FIX LOCALE: Buộc môi trường test sử dụng Locale 'vi' (Tiếng Việt).
        App::setLocale('vi');
    }
}
