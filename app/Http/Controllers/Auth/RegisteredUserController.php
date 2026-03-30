<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.NguoiDung::class],

            // Mật khẩu: yêu cầu, xác nhận, và mặc định 8 ký tự
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

            'so_dien_thoai' => ['required', 'string', 'digits:10', 'regex:/^0/'],

            // Cập nhật: Đổi 'nullable' thành 'required' để khớp với UT20
            'dia_chi' => ['required', 'string', 'max:255'],
        ],[
            // (Tùy chọn) Thêm thông báo lỗi Tiếng Việt
            'name.regex' => 'Họ tên không được chứa ký tự đặc biệt hoặc số.',
            'so_dien_thoai.required' => 'Số điện thoại không được để trống.',
            'dia_chi.required' => 'Địa chỉ không được để trống.',
            'so_dien_thoai.digits' => 'Số điện thoại phải đủ 10 chữ số.',
            'so_dien_thoai.regex' => 'Số điện thoại phải bắt đầu bằng số 0.',
        ]);

        // ---- LOGIC LƯU DỮ LIỆU ĐÃ ĐÚNG ----
        $user = NguoiDung::create([
            'ho_ten' => $request->name,
            'email' => $request->email,
            'mat_khau' => Hash::make($request->password),
            'so_dien_thoai' => $request->so_dien_thoai,
            'dia_chi' => $request->dia_chi,
            'vai_tro' => 'khach_hang',
            // FIX LỖI: Cột `trang_thai` trong DB (nguoi_dung) là kiểu số (INT/TINYINT)
            // Đã thay 'hoat_dong' bằng giá trị số 1 (Active)
            'trang_thai' => 1,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}


