<?php

namespace Tests;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Tự động khởi động ChromeDriver trước khi chạy test.
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Tạo phiên trình duyệt.
     */
    protected function driver(): RemoteWebDriver
    {
        // Trong file tests/DuskTestCase.php
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--window-size=1500,800',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            // Vô hiệu hóa tính năng kiểm tra rò rỉ mật khẩu của Chrome
            '--disable-features=PasswordLeakDetection',
            '--disable-save-password-bubble',
        ]);

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    protected function baseUrl()
    {
        return env('APP_URL', 'http://127.0.0.1:8000');
    }
}
