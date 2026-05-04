<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dòng Ngôn ngữ Validation
    |--------------------------------------------------------------------------
    |
    | Các dòng ngôn ngữ sau đây chứa các thông báo lỗi mặc định được sử dụng
    | bởi class validator. Các quy tắc này có thể được điều chỉnh lại
    | theo ý muốn của bạn.
    |
    */

    'accepted'             => ':Attribute phải được chấp nhận.',
    'active_url'           => ':Attribute không phải là một URL hợp lệ.',
    'after'                => ':Attribute phải lớn hơn :date.',
    'after_or_equal'       => 'Không được chọn thời gian trong quá khứ.',
    'alpha'                => ':Attribute chỉ có thể chứa các chữ cái.',
    'numeric' => ':Attribute phải là số.',
    'regex'   => 'Định dạng :attribute không hợp lệ hoặc chứa ký tự đặc biệt.',
    'different' => 'Sân bay đi và sân bay đến không được trùng nhau.',

    // ... (Các quy tắc khác nếu cần)

    'email'                => ':Attribute phải là một địa chỉ email hợp lệ.',
    'max'                  => [
        'numeric' => ':Attribute không được lớn hơn :max.',
        'file'    => ':Attribute không được lớn hơn :max kilobytes.',
        'string'  => ':Attribute không được lớn hơn :max ký tự.',
        'array'   => ':Attribute không được có nhiều hơn :max mục.',
    ],
    'min'                  => [
        'numeric' => ':Attribute phải lớn hơn 0.',
        'file'    => ':Attribute phải có ít nhất :min kilobytes.',
        'string'  => ':Attribute phải có ít nhất :min ký tự.',
        'array'   => ':Attribute phải có ít nhất :min mục.',
    ],
    'required'             => ':Attribute không được để trống.',
    'unique'               => ':Attribute đã được sử dụng.',
    'confirmed'            => ':Attribute xác nhận không khớp.',


    /*
    |--------------------------------------------------------------------------
    | Tên các thuộc tính (Attributes)
    |--------------------------------------------------------------------------
    |
    | Các dòng ngôn ngữ sau đây được sử dụng để hoán đổi placeholder
    | :attribute thành tên thuộc tính thân thiện hơn. Ví dụ: "email"
    | sẽ được đổi thành "địa chỉ email".
    |
    */

    // Cuối file validation.php
    'attributes' => [
        'ten' => 'tên',
        'name' => 'họ tên',
        'email' => 'email',
        'password' => 'mật khẩu',
        'dia_chi' => 'địa chỉ',
        'ma_chuyen_bay' => 'mã chuyến bay',
        'gia_ve' => 'giá vé',
        'id_san_bay_den' => 'sân bay đến',
    ],

    'custom' => [
        'ma_chuyen_bay' => [
            'regex' => 'Mã chuyến bay không được chứa ký tự đặc biệt.',
        ],
        'gia_ve' => [
            'min' => 'Giá vé phải lớn hơn 0.',
        ],
        'id_san_bay_den' => [
            'different' => 'Sân bay đi và sân bay đến không được trùng nhau.',
        ],
    ],
];

