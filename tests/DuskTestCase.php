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
     * Tạo phiên trình duyệt (Hỗ trợ chuyển đổi linh hoạt Chrome / Edge).
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--window-size=1500,800',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-features=PasswordLeakDetection',
            '--disable-save-password-bubble',
        ]);

        // 1. Đọc cấu hình từ file .env (Mặc định nếu không ghi là chrome)
        $browser = env('DUSK_BROWSER', 'chrome');

        // 2. Định nghĩa Capabilities tương ứng với trình duyệt
        // 2. Định nghĩa Capabilities tương ứng với trình duyệt
        if ($browser === 'edge') {
            // Ép Driver gọi đến file thực thi của Microsoft Edge trên Windows
            $options->setBinary('C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe');

            // THAY THẾ DÒNG BỊ LỖI ĐỎ BẰNG DÒNG DƯỚI ĐÂY:
            $capabilities = DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            );
            $capabilities->setCapability('browserName', 'MicrosoftEdge');

        } else {
            // Ép Driver gọi đến file thực thi của Google Chrome trên Windows
            $options->setBinary('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
            $capabilities = DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            );
        }

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            $capabilities
        );
    }

    protected function baseUrl()
    {
        return env('APP_URL', 'http://127.0.0.1:8000');
    }
}
