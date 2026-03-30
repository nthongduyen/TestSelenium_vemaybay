<?php
//================ test đăng nhập =================

namespace Tests\Feature\Auth;

use App\Models\NguoiDung;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
// Không cần Symfony Console nữa, chúng ta sẽ dùng hàm PHP thuần

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;


    protected static array $testResults = [];

    /**
     * Hàm tiện ích để ghi lại kết quả của từng Test Case vào mảng tĩnh.
     */
    protected function recordResult(string $id, string $description, string $email, string $password, string $expected, bool $passed): void
    {
        self::$testResults[] = [
            'ID' => $id,
            'Mô tả' => $description,
            'Email Input' => $email,
            'Password Input' => $password,
            'Kết quả Mong đợi' => $expected,
            'Trạng thái' => $passed ? '<fg=green>PASS</>' : '<fg=red>FAIL</>',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        NguoiDung::factory()->create([
            'email' => '123@gmail.com',
            'mat_khau' => Hash::make('123123123'),
        ]);
    }

    /**
     * UT01: Người dùng đăng nhập đúng với thông tin hợp lệ.
     */
    public function test_ut01_valid_login_is_successful(): void
    {
        $email = '123@gmail.com';
        $password = '123123123';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $this->assertAuthenticated();
            $response->assertRedirect(RouteServiceProvider::HOME);
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT01', 'Người dùng đăng nhập đúng', $email, $password, 'Đăng nhập thành công, chuyển hướng đúng', $status);
        }
    }

    /**
     * UT02: Kiểm tra đăng nhập với Email đúng, nhưng Mật khẩu sai.
     */
    public function test_ut02_invalid_password_shows_error_message(): void
    {
        $email = '123@gmail.com';
        $password = 'sai_mat_khau';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT02', 'Mật khẩu sai', $email, $password, 'Hệ thống báo lỗi đăng nhập', $status);
        }
    }

    /**
     * UT03: Kiểm tra đăng nhập với Email KHÔNG tồn tại trong hệ thống.
     */
    public function test_ut03_non_existent_email_shows_error_message(): void
    {
        $email = 'khachmoi@abc.com';
        $password = '123123123';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT03', 'Email không tồn tại', $email, $password, 'Hệ thống báo lỗi đăng nhập', $status);
        }
    }

    /**
     * UT04: Kiểm tra đăng nhập khi Email bị bỏ trống.
     */
    public function test_ut04_empty_email_shows_validation_error(): void
    {
        $email = '';
        $password = '123123123';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT04', 'Email bị bỏ trống', $email, $password, 'Hệ thống báo lỗi Validation cho Email', $status);
        }
    }

    /**
     * UT05: Kiểm tra đăng nhập khi Mật khẩu bị bỏ trống.
     */
    public function test_ut05_empty_password_shows_validation_error(): void
    {
        $email = '123@gmail.com';
        $password = '';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $response->assertSessionHasErrors(['password']);
            $this->assertGuest();
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT05', 'Mật khẩu bị bỏ trống', $email, $password, 'Hệ thống báo lỗi Validation cho Mật khẩu', $status);
        }
    }

    /**
     * UT06: Kiểm tra đăng nhập với Email không hợp lệ (không đúng định dạng).
     */
    public function test_ut06_invalid_email_format_shows_validation_error(): void
    {
        $email = 'abc.com';
        $password = '123123123';
        $status = true;

        try {
            $response = $this->post('/login', ['email' => $email, 'password' => $password]);
            $response->assertSessionHasErrors(['email']);
            $this->assertGuest();
        } catch (\Throwable $e) {
            $status = false;
        } finally {
             $this->recordResult('UT06', 'Email sai định dạng', $email, $password, 'Hệ thống báo lỗi Validation cho Email', $status);
        }
    }

    /**
     * Hàm chạy 1 lần sau khi tất cả test trong class này kết thúc.
     * Dùng để xuất kết quả ra định dạng CSV (In ra Console VÀ Lưu file).
     */
    public static function tearDownAfterClass(): void
    {
        if (empty(self::$testResults)) {
            return;
        }

        // 1. Loại bỏ thẻ màu sắc (<fg=green>PASS</>) trước khi xuất CSV
        $results = array_map(function($row) {
            // strip_tags loại bỏ các thẻ HTML/Console, giữ lại giá trị "PASS" hoặc "FAIL" thuần túy.
            $row['Trạng thái'] = strip_tags($row['Trạng thái']);
            return $row;
        }, self::$testResults);

        // Lấy headers
        $headers = array_keys($results[0]);

        // 2. Tạo chuỗi CSV trong bộ nhớ tạm
        // Sử dụng 'r+' cho phép đọc/ghi
        $output = fopen('php://temp', 'r+');

        // Ghi tiêu đề (Headers)
        fputcsv($output, $headers);

        // Ghi dữ liệu
        foreach ($results as $row) {
            // fputcsv mặc định sử dụng dấu phẩy (,) và mã hóa UTF-8
            fputcsv($output, array_values($row));
        }

        // Đặt con trỏ về đầu stream để đọc nội dung
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $csvContentWithBOM = $bom . $csvContent;

        // 4. IN NỘI DUNG CSV RA CONSOLE (STDOUT)
        echo "\n\n========================================================================\n";
        echo "BÁO CÁO KẾT QUẢ TEST CASE ĐĂNG NHẬP (ĐỊNH DẠNG CSV IN RA CONSOLE)\n";
        echo "========================================================================\n\n";
        // In nội dung KHÔNG CÓ BOM ra Console để tránh hiển thị ký tự lạ
        echo $csvContent;
        echo "\n";


        // 5. LƯU CHUỖI CSV CÓ BOM VÀO FILE VẬT LÝ
        $filePath = base_path('auth_test_report_DN.csv');

        // Sử dụng file_put_contents để lưu chuỗi CSV CÓ BOM vào file
        if (file_put_contents($filePath, $csvContentWithBOM) !== false) {
             // In thông báo thành công ra Console
            echo "========================================================================\n";
            echo "File CSV đã được lưu thành công tại: " . $filePath . "\n";
            echo "Đã thêm UTF-8 BOM để đảm bảo hiển thị đúng Tiếng Việt trong Excel/WPS.\n";
            echo "========================================================================\n\n";
        } else {
            echo "\n\n[LỖI] KHÔNG THỂ XUẤT FILE CSV. Vui lòng kiểm tra quyền ghi file tại: " . $filePath . "\n";
            echo "========================================================================\n\n";
        }
    }
}

// các file liên quan - test/feature/testcase.php - database/migrations/2014_10_12_000000_create_users_table.php
//test ĐĂNG NHẬP


// lệnh chạy: php artisan test tests/Feature/Auth/AuthenticationTest.php
///oke ====
