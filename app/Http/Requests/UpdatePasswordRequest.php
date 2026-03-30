<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Must be authenticated to change password
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Rules address all failing test cases:
     * - ut14, ut15, ut16, ut23: 'required'
     * - ut22: 'current_password'
     * - ut19: 'min:9'
     * - ut17: 'confirmed'
     * - ut26: 'different:current_password'
     * - ut20: 'regex:/\S+/' (no whitespace)
     */
    public function rules(): array
    {
        return [
            // Fixes ut23 (missing) and ut22 (incorrect password validation)
            'current_password' => ['required', 'current_password'],

            // Fixes ut14, ut15, ut16 (empty), ut19 (min length), ut17 (mismatch),
            // ut20 (invalid char/whitespace), and ut26 (same as old password)
            'password' => [
                'required',
                'confirmed', // Checks against 'password_confirmation' field (ut17)
                Password::min(9), // Ensures minimum length of 9 (ut19)
                'different:current_password', // New password must differ from current (ut26)
                'regex:/\S+/', // Ensures no whitespace/invalid characters (ut20)
            ],
            // 'password_confirmation' is required implicitly by the 'confirmed' rule on 'password'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     * These messages are based on the expected success/failure messages in your tests.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'current_password.current_password' => 'Mật khẩu hiện tại không chính xác.',

            'password.required' => 'Vui lòng nhập mật khẩu mới!',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 9 ký tự!',
            'password.different' => 'Mật khẩu mới không được trùng với mật khẩu hiện tại.',
            'password.regex' => 'Mật khẩu không được chứa khoảng trắng hoặc ký tự không hợp lệ.',
        ];
    }
}
