<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddTokenFromCookie
{
    /**
     * Handle an incoming request.
     * Tự động thêm token từ cookie vào header Authorization
     * để JWT middleware có thể sử dụng
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu chưa có header Authorization, thử lấy từ cookie
        if (!$request->header('Authorization')) {
            $token = $request->cookie('access_token');
            
            if ($token) {
                // Thêm token vào header Authorization
                $request->headers->set('Authorization', 'Bearer ' . $token);
            }
        }

        return $next($request);
    }
}

