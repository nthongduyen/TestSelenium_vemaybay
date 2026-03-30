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
    'different'      => ':Attribute và :other không được trùng nhau.',
    'after_or_equal' => ':Attribute phải sau hoặc bằng :date.',
    //'required'       => 'Yêu cầu chọn :attribute.',
    'required'       => ':Attribute không được để trống',



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

    'attributes' => [

        // Thêm các dòng này để khớp với form tìm kiếm
        'id_san_bay_di'  => 'điểm xuất phát',
        'id_san_bay_den' => 'điểm đến',
        'ngay_di'        => 'ngày đi',
        'ngay_ve'        => 'ngày về',
    ],
];


