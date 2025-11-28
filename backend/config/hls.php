<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HLS Secure Link Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho việc tạo signed URL cho video HLS.
    | Secret key phải giống với cấu hình trong nginx (secure_link_secret).
    |
    */

    /**
     * Secret key dùng để ký URL
     * QUAN TRỌNG: Phải giống với nginx secure_link_secret
     */
    'secret_key' => env('HLS_SECRET_KEY', 'your-super-secret-key-change-in-production'),

    /**
     * Thời gian hết hạn mặc định (giây)
     * Mặc định: 3600 giây = 1 giờ
     */
    'default_expiry' => env('HLS_DEFAULT_EXPIRY', 3600),

    /**
     * Base URL của nginx phục vụ video HLS
     * Ví dụ: https://video.example.com hoặc https://example.com/hls
     */
    'base_url' => env('HLS_BASE_URL', env('APP_URL', 'http://localhost') . '/hls'),

    /**
     * Có giới hạn theo IP hay không
     * Nếu true, chữ ký sẽ bao gồm IP client, giúp ngăn chặn chia sẻ URL
     */
    'restrict_by_ip' => env('HLS_RESTRICT_BY_IP', false),

    /**
     * Đường dẫn thư mục chứa video HLS (relative to storage/app/public)
     */
    'storage_path' => env('HLS_STORAGE_PATH', 'hls'),
];
