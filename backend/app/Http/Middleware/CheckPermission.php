<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Kiểm tra quyền truy cập của user dựa trên method và path của request.
     *
     * Lưu ý: Middleware này cần được sử dụng sau middleware 'auth:api'
     * để đảm bảo user đã được authenticate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Lấy user đã được authenticate (từ middleware auth:api)
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Người dùng không được xác thực'
            ], 401);
        }

        // Lấy method và path từ request
        $method = $request->method();
        $path = $request->path();

        // Loại bỏ prefix 'api' nếu có (vì trong database có thể lưu với hoặc không có prefix)
        $pathWithoutApi = preg_replace('/^api\//', '', $path);
        $pathWithApi = '/api/' . $pathWithoutApi;
        $pathWithSlash = '/' . $pathWithoutApi; 

        // Kiểm tra quyền với các format path khác nhau
        $hasPermission = $user->hasPermission($method, $pathWithApi)
                      || $user->hasPermission($method, $pathWithSlash)
                      || $user->hasPermission($method, $path);

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập tài nguyên này',
                'required_permission' => [
                    'method' => $method,
                    'path' => $pathWithApi
                ]
            ], 403);
        }

        return $next($request);
    }
}

