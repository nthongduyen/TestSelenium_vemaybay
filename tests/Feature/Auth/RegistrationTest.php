<?php

namespace Tests\Feature\Auth;

use App\Models\NguoiDung;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected static array $testResults = [];

    protected function recordResult(string $id, string $description, string $email, string $expected, bool $passed): void
    {
        // Ghi nhận kết quả vào mảng tĩnh
        self::$testResults[] = [
            'ID' => $id,
            'Mô tả' => $description,
            'Email Input' => $email,
            'Kết quả Mong đợi' => $expected,
            'Trạng thái' => $passed ? '<fg=green>PASS</>' : '<fg=red>FAIL</>',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        NguoiDung::factory()->create([
            'email' => 'user1@example.com',
            'ho_ten' => 'Người Dùng Tồn Tại',
            'mat_khau' => Hash::make('password123'),
        ]);

        // Giả lập việc không gửi email để tránh lỗi khi chạy test
        Notification::fake();
    }

    // --- CÁC TEST CASE ĐĂNG KÝ (UT07 đến UT21) ---

    /**
     * UT07: Đăng ký thành công với dữ liệu hợp lệ.
     */
    public function test_ut07_valid_registration_is_successful(): void
    {
        $data = [
            'name' => 'Nguyễn Văn A',
            'email' => 'newuser@example.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'Hà Nội',
        ];

        // 1. Thực hiện Request
        $response = $this->post('/register', $data);

        // 2. Khẳng định (Assertions)
        // A. KIỂM TRA CƠ SỞ DỮ LIỆU VÀ CHUYỂN HƯỚNG
        $this->assertDatabaseHas('nguoi_dung', [
            'email' => $data['email'],
            'ho_ten' => $data['name'],
        ]);
        $response->assertRedirect(RouteServiceProvider::HOME);
        $response->assertSessionDoesntHaveErrors();

        // 3. Ghi lại kết quả sau khi tất cả assertion đã PASS
        $this->recordResult('UT07', 'Đăng ký thành công (Dữ liệu hợp lệ)', $data['email'], 'Lưu thông tin thành công và chuyển hướng', true);
    }

    /**
     * UT08: Đăng ký thất bại do Email đã tồn tại.
     */
    public function test_ut08_duplicate_email_registration_fails(): void
    {
        // A. LẤY SỐ LƯỢNG NGƯỜI DÙNG BAN ĐẦU
        $initialUserCount = NguoiDung::count();

        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'user1@example.com', // Email đã tồn tại (đã tạo trong setUp)
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234568',
            'dia_chi' => 'TP HCM',
        ];

        // 1. Thực hiện Request
        $response = $this->post('/register', $data);

        // 2. Khẳng định (Assertions)
        // A. KIỂM TRA LỖI VALIDATION VÀ CHUYỂN HƯỚNG
        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302); // Quay lại form

        // B. KIỂM TRA SỐ LƯỢNG NGƯỜI DÙNG KHÔNG TĂNG
        $this->assertDatabaseCount('nguoi_dung', $initialUserCount);

        // 3. Ghi lại kết quả sau khi tất cả assertion đã PASS
        $this->recordResult('UT08', 'Email đã tồn tại', $data['email'], 'Lỗi: Email đã tồn tại trong hệ thống', true);
    }

    /**
     * UT09: Đăng ký với Email bị bỏ trống.
     */
    public function test_ut09_empty_email_shows_validation_error(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => '',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234568',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['email']);

        $this->recordResult('UT09', 'Email bị bỏ trống', $data['email'], 'Lỗi: Email không được để trống', true);
    }

    /**
     * UT10: Đăng ký với Email sai định dạng.
     */
    public function test_ut10_invalid_email_format_shows_validation_error(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'abc.com', // Sai định dạng
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234568',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['email']);

        $this->recordResult('UT10', 'Email sai định dạng', $data['email'], 'Lỗi: Địa chỉ Email không hợp lệ', true);
    }

    /**
     * UT11: Đăng ký với Mật khẩu ít hơn 8 ký tự.
     */
    public function test_ut11_short_password_shows_validation_error(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@shortpass.com',
            'password' => 'Abc@123', // 7 ký tự
            'password_confirmation' => 'Abc@123',
            'so_dien_thoai' => '0901234568',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['password']);

        $this->recordResult('UT11', 'Mật khẩu ít hơn 8 ký tự', $data['email'], 'Lỗi: Mật khẩu phải từ 8 ký tự trở lên', true);
    }

    /**
     * UT12: Đăng ký với Mật khẩu bị bỏ trống.
     */
    public function test_ut12_empty_password_shows_validation_error(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@emptypass.com',
            'password' => '',
            'password_confirmation' => '',
            'so_dien_thoai' => '0901234568',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['password']);

        $this->recordResult('UT12', 'Mật khẩu bị bỏ trống', $data['email'], 'Lỗi: Mật khẩu không được để trống', true);
    }

    /**
     * UT13: Đăng ký với SĐT không bắt đầu bằng 0 (Giả định rule 'regex:/^0/').
     */
    public function test_ut13_sdt_not_starting_with_zero_fails(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@wrongsdt.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '8901234567', // Không bắt đầu bằng 0
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['so_dien_thoai']);

        $this->recordResult('UT13', 'SĐT không bắt đầu bằng 0', $data['email'], 'Lỗi: Số điện thoại phải bắt đầu bằng 0', true);
    }

    /**
     * UT14: Đăng ký với SĐT không đủ 10 số (Giả định rule 'digits:10').
     */
    public function test_ut14_sdt_not_ten_digits_fails(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@shortdigits.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '090123456', // 9 số
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['so_dien_thoai']);

        $this->recordResult('UT14', 'SĐT không đủ 10 số', $data['email'], 'Lỗi: Số điện thoại phải đủ 10 số', true);
    }

    /**
     * UT15: Đăng ký khi Xác nhận lại mật khẩu không khớp (Giả định rule 'confirmed').
     */
    public function test_ut15_password_confirmation_mismatch_fails(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@mismatch.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1235', // Không khớp
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['password']);

        $this->recordResult('UT15', 'Xác nhận mật khẩu không khớp', $data['email'], 'Lỗi: Xác nhận lại mật khẩu không khớp', true);
    }

    /**
     * UT16: Đăng ký với Họ tên bị bỏ trống.
     */
    public function test_ut16_empty_ho_ten_shows_validation_error(): void
    {
        $data = [
            'name' => '', // Tên trường input phải là 'name'
            'email' => 'test@emptyname.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['name']);

        $this->recordResult('UT16', 'Họ tên bị bỏ trống', $data['email'], 'Lỗi: Họ tên không được để trống', true);
    }

    /**
     * UT17: Đăng ký với Họ tên chứa ký tự đặc biệt (Giả định rule nghiêm ngặt).
     */
    public function test_ut17_ho_ten_with_special_chars_fails(): void
    {
        $data = [
            'name' => 'Nguyễn Văn *A*', // Ký tự đặc biệt
            'email' => 'test@namespecial.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['name']);

        $this->recordResult('UT17', 'Họ tên chứa ký tự đặc biệt', $data['email'], 'Lỗi: Họ tên không được chứa ký tự đặc biệt', true);
    }

    /**
     * UT18: Đăng ký với Họ tên chứa số (Giả định rule nghiêm ngặt).
     */
    public function test_ut18_ho_ten_with_numbers_fails(): void
    {
        $data = [
            'name' => 'Nguyễn Văn 123', // Chứa số
            'email' => 'test@namenumber.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => 'TP HCM',
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['name']);

        $this->recordResult('UT18', 'Họ tên chứa số', $data['email'], 'Lỗi: Họ tên không được chứa số', true);
    }

    /**
     * UT19: Đăng ký với Địa chỉ bị bỏ trống.
     */
    public function test_ut19_empty_dia_chi_shows_validation_error(): void
    {
        $data = [
            'name' => 'Nguyễn Văn B',
            'email' => 'test@emptyaddress.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => '', // Bị bỏ trống
        ];

        $response = $this->post('/register', $data);
        $response->assertSessionHasErrors(['dia_chi']);

        $this->recordResult('UT19', 'Địa chỉ bị bỏ trống', $data['email'], 'Lỗi: Địa chỉ không được để trống', true);
    }

    /**
     * UT20: Đăng ký với Địa chỉ chứa ký tự đặc biệt (Giả định thành công).
     */
    public function test_ut20_dia_chi_with_special_chars_is_successful(): void
    {
        $data = [
            'name' => 'Nguyễn Văn C',
            'email' => 'test@addressspecial.com',
            'password' => 'Abc@1234',
            'password_confirmation' => 'Abc@1234',
            'so_dien_thoai' => '0901234567',
            'dia_chi' => '123, đường A, quận 1, *Hồ Chí Minh*', // Chứa ký tự đặc biệt/số
        ];

        // 1. Thực hiện Request
        $response = $this->post('/register', $data);

        // 2. Khẳng định (Assertions)
        $this->assertDatabaseHas('nguoi_dung', ['email' => $data['email']]);
        $response->assertRedirect(RouteServiceProvider::HOME);
        $response->assertSessionDoesntHaveErrors();

        // 3. Ghi lại kết quả sau khi tất cả assertion đã PASS
        $this->recordResult('UT20', 'Địa chỉ chứa ký tự đặc biệt (Hợp lệ)', $data['email'], 'Lưu thông tin thành công và chuyển hướng', true);
    }

    //* Dùng để xuất kết quả ra định dạng CSV (In ra Console VÀ Lưu file có BOM).

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
        $output = fopen('php://temp', 'r+');

        // Ghi tiêu đề (Headers)
        fputcsv($output, $headers);

        // Ghi dữ liệu
        foreach ($results as $row) {
            fputcsv($output, array_values($row));
        }

        // Đặt con trỏ về đầu stream để đọc nội dung
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        // 3. FIX LỖI FONT TIẾNG VIỆT TRÊN EXCEL/WPS: Thêm Byte Order Mark (BOM) cho UTF-8
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        $csvContentWithBOM = $bom . $csvContent;

        // 4. IN NỘI DUNG CSV RA CONSOLE (STDOUT)
        echo "\n\n========================================================================\n";
        echo "BÁO CÁO KẾT QUẢ TEST CASE ĐĂNG KÝ (ĐỊNH DẠNG CSV IN RA CONSOLE)\n";
        echo "========================================================================\n\n";
        // In nội dung KHÔNG CÓ BOM ra Console
        echo $csvContent;
        echo "\n";


        // 5. LƯU CHUỖI CSV CÓ BOM VÀO FILE VẬT LÝ
        // Đã sửa tên file để tránh ghi đè lên file cũ, nhưng vẫn giữ đuôi _DK.csv để nhận dạng
        $filePath = base_path('registration_test_report_DK.csv');

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

//lệnh test: php artisan test --filter=RegistrationTest
// lệnh chạy UT07: php artisan test --filter=UT07

// test ĐĂNG KÝ =======ok
// các file liên quan - app/Http/Requests/RegisterRequest.php - app/Http/Controllers/Auth/RegisteredUserController.php
