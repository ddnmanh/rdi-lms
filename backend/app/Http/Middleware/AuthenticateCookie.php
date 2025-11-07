<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthenticateCookie
{
    /**
     * Handle an incoming request.
     * Xác thực user qua cookie (access_token hoặc refresh_token)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\JsonResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Lấy token từ cookie hoặc header Authorization
        $token = $request->cookie('access_token') ?? $this->getTokenFromHeader($request);

        if (!$token) {
            // Nếu không có access token, thử refresh token
            $refreshToken = $request->cookie('refresh_token');
            if ($refreshToken) {
                // Có refresh token, nhưng không có access token
                // Có thể redirect về trang login hoặc tự động refresh
                // Ở đây ta sẽ để middleware auth:api xử lý
                return $next($request);
            }

            // Không có token nào, redirect về trang login
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa đăng nhập'
                ], 401);
            }

            return redirect()->route('admin.login');
        }

        try {
            // Set token vào request để JWT middleware có thể sử dụng
            $request->headers->set('Authorization', 'Bearer ' . $token);

            // Xác thực token
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                throw new TokenInvalidException('Token không hợp lệ');
            }

            // Set user vào request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

        } catch (TokenExpiredException $e) {
            // Token hết hạn, thử refresh
            $refreshToken = $request->cookie('refresh_token');
            if ($refreshToken) {
                // Có refresh token, có thể tự động refresh hoặc redirect
                // Ở đây ta sẽ để middleware auth:api xử lý
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token đã hết hạn'
                ], 401);
            }

            return redirect()->route('admin.login');

        } catch (TokenInvalidException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không hợp lệ'
                ], 401);
            }

            return redirect()->route('admin.login');

        } catch (JWTException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác thực token'
                ], 401);
            }

            return redirect()->route('admin.login');
        }

        return $next($request);
    }

    /**
     * Lấy token từ header Authorization
     */
    private function getTokenFromHeader(Request $request)
    {
        $header = $request->header('Authorization');

        if (!$header) {
            return null;
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

