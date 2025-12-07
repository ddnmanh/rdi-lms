<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cấu hình URL tĩnh với chữ ký (signed URL)
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho việc tạo signed URL cho việc lấy các tài nguyên tĩnh.
    | Secret key phải giống với cấu hình trong nginx (secure_link_secret).
    |
    */

    /**
     * Secret key dùng để ký URL
     * QUAN TRỌNG: Phải giống với nginx secure_link_secret
     */
    'secret_key' => env('STATIC_SOURCE_SECRET_KEY', 'TQULUXsEuwX1bjwfZJW5EhFkjxuScYCZ'),

    /**
     * Thời gian hết hạn mặc định (giây)
     * Mặc định: 3600 giây = 1 giờ
     */
    'default_expiry' => env('STATIC_SOURCE_DEFAULT_EXPIRY', 3600),

    /**
     * Base URL của nginx phục vụ xem các tài nguyên tĩnh
     * Ví dụ: https://video.example.com hoặc https://example.com
     */
    'base_url' => env('STATIC_SOURCE_BASE_URL', env('APP_URL', 'http://localhost')),
];
