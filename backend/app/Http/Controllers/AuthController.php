<?php

namespace App\Http\Controllers;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'fullname' => 'nullable|string|max:150',
            'birthday' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'fullname' => $request->fullname,
            'birthday' => $request->birthday,
        ]);

        // Tạo access token và refresh token
        $tokens = $this->generateTokens($user, $request);

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'user' => $user,
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60, // seconds
            ]
        ], 201);
    }

    /**
     * Đăng nhập
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // Tạo access token và refresh token
        $tokens = $this->generateTokens($user, $request);

        // Lưu token vào cookie
        $accessTokenExpires = config('jwt.ttl') * 60; // seconds
        $refreshTokenExpires = config('jwt.refresh_ttl') * 60; // seconds

        $response = response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'user' => $user->load('roles'),
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_type' => 'Bearer',
                'expires_in' => $accessTokenExpires,
            ]
        ]);

        // Set access token cookie (HttpOnly, Secure chỉ khi HTTPS, SameSite)
        $secure = $request->secure() || config('session.secure', false);
        $response->cookie('access_token', $tokens['access_token'], $accessTokenExpires / 60, '/', null, $secure, true);

        // Set refresh token cookie (HttpOnly, Secure chỉ khi HTTPS, SameSite)
        $response->cookie('refresh_token', $tokens['refresh_token'], $refreshTokenExpires / 60, '/', null, $secure, true);

        return $response;
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken();

            // Revoke refresh token từ cookie hoặc request
            $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');
            if ($refreshToken && $request->user()) {
                $refreshTokenModel = RefreshToken::where('token', $refreshToken)
                    ->where('user_id', $request->user()->id)
                    ->where('is_revoked', false)
                    ->first();

                if ($refreshTokenModel) {
                    $refreshTokenModel->revoke();
                }
            }

            // Invalidate access token
            if ($token) {
                JWTAuth::invalidate($token);
            }

            $response = response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công'
            ]);

            // Xóa cookies
            $secure = $request->secure() || config('session.secure', false);
            $response->cookie('access_token', '', -1, '/', null, $secure, true);
            $response->cookie('refresh_token', '', -1, '/', null, $secure, true);

            return $response;
        } catch (JWTException $e) {
            $response = response()->json([
                'success' => false,
                'message' => 'Không thể đăng xuất'
            ], 500);

            // Vẫn xóa cookies dù có lỗi
            $secure = $request->secure() || config('session.secure', false);
            $response->cookie('access_token', '', -1, '/', null, $secure, true);
            $response->cookie('refresh_token', '', -1, '/', null, $secure, true);

            return $response;
        }
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Refresh access token
     */
    public function refresh(Request $request)
    {
        // Lấy refresh token từ cookie hoặc request body
        $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token không được cung cấp'
            ], 422);
        }

        $refreshTokenModel = RefreshToken::where('token', $refreshToken)
            ->where('is_revoked', false)
            ->first();

        if (!$refreshTokenModel || !$refreshTokenModel->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token không hợp lệ hoặc đã hết hạn'
            ], 401);
        }

        $user = $refreshTokenModel->user;

        // Tạo access token mới
        try {
            $accessToken = JWTAuth::fromUser($user);
            $accessTokenExpires = config('jwt.ttl') * 60; // seconds

            $response = response()->json([
                'success' => true,
                'message' => 'Refresh token thành công',
                'data' => [
                    'token_type' => 'Bearer',
                    'expires_in' => $accessTokenExpires,
                ]
            ]);

            // Cập nhật access token cookie
            $secure = $request->secure() || config('session.secure', false);
            $response->cookie('access_token', $accessToken, $accessTokenExpires / 60, '/', null, $secure, true);

            return $response;
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo token mới'
            ], 500);
        }
    }

    /**
     * Tạo access token và refresh token
     */
    private function generateTokens(User $user, Request $request)
    {
        try {
            // Tạo access token
            $accessToken = JWTAuth::fromUser($user);

            // Tạo refresh token
            $refreshToken = bin2hex(random_bytes(32)); // 64 characters
            $refreshTokenExpiresAt = now()->addMinutes(config('jwt.refresh_ttl'));

            // Lưu refresh token vào database
            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => $refreshTokenExpiresAt,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ];
        } catch (JWTException $e) {
            throw $e;
        }
    }

}

