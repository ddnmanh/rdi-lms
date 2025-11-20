<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class, // Xử lý proxy, IP client.
        \Fruitcake\Cors\HandleCors::class, // Xử lý CORS.
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Chặn request khi bảo trì.
        \App\Http\Middleware\HandlePutFormData::class, // Xử lý PUT/PATCH request với multipart/form-data (phải đặt trước ValidatePostSize)
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Kiểm tra kích thước POST.
        \App\Http\Middleware\TrimStrings::class, // Loại bỏ khoảng trắng thừa của input.
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Chuyển chuỗi rỗng thành null.
        \App\Http\Middleware\LogUserActivity::class, // Ghi lại hoạt động người dùng.
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class, // Mã hóa cookie trước khi gửi về client.
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class, // Thêm các cookie đã được "xếp hàng" vào response.
            \Illuminate\Session\Middleware\StartSession::class, // Bắt đầu session cho mỗi request.
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, // Chia sẻ lỗi từ session sang view (thường dùng cho form).
            \App\Http\Middleware\VerifyCsrfToken::class, // Xác thực token CSRF.
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Tự động gán model vào route dựa trên tham số.
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\AddTokenFromCookie::class, // Tự động lấy JWT access token từ cookie access_token và thêm vào header Authorization nếu header này chưa có → Giúp các middleware xác thực JWT hoạt động với token lưu trong cookie.
            'throttle:api', // Giới hạn tốc độ request.
            \Illuminate\Routing\Middleware\SubstituteBindings::class, // Tự động gán model vào route dựa trên tham số.
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class, // Xác thực người dùng (phải đăng nhập).
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // Xác thực HTTP Basic.
        'auth.cookie' => \App\Http\Middleware\AuthenticateCookie::class, // Xác thực qua cookie (middleware tự định nghĩa).
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Thiết lập header cache cho response.
        'can' => \Illuminate\Auth\Middleware\Authorize::class, // Kiểm tra quyền truy cập (authorization).
        'check.permission' => \App\Http\Middleware\CheckPermission::class, // Kiểm tra quyền cụ thể của người dùng (middleware tự định nghĩa)
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Chuyển hướng nếu đã đăng nhập.
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // Yêu cầu xác nhận lại mật khẩu.
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // Kiểm tra tính hợp lệ của URL có chữ ký.
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Giới hạn số lượng request.
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Đảm bảo email đã được xác minh.
    ];
}
