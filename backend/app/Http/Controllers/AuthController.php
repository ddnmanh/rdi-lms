<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Lấy dữ liệu đã được validate
        $data = $request->validated();

        // Chuẩn hóa email (lowercase)
        $data['email'] = Str::lower($data['email']);

        try {
            // Có thể dùng transaction nếu sau này có thêm các insert liên quan
            $user = DB::transaction(function () use ($data, $request) {
                $user = User::create([
                    'email'    => $data['email'],
                    'password' => Hash::make($data['password']),
                    'fullname' => $data['fullname'] ?? null,
                    'birthday' => $data['birthday'] ?? null,
                ]);

                // Bắn event để listener có thể gửi email verify, log, v.v.
                event(new Registered($user));

                return $user;
            });

            // Sinh access token & refresh token (hàm của bạn)
            // $tokens = $this->generateTokens($user, $request);

            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data'    => [
                    'user' => new UserResource($user)
                ],
            ], 201);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Đăng nhập
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Lấy dữ liệu đã được validate
        $body = $request->validated();

        try {

            $user = User::where('email', $body['email'])->first();

            if (!$user || !Hash::check($body['password'], $user->password)) {
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
                    'access_token_expires_in' => $accessTokenExpires,
                    'refresh_token' => $tokens['refresh_token'],
                    'refresh_token_expires_in' => $refreshTokenExpires
                ]
            ]);

            // Set access token cookie (HttpOnly, Secure chỉ khi HTTPS, SameSite)
            $secure = $request->secure() || config('session.secure', false);
            $response->cookie('access_token', $tokens['access_token'], $accessTokenExpires / 60, '/', null, $secure, true);

            // Set refresh token cookie (HttpOnly, Secure chỉ khi HTTPS, SameSite)
            $response->cookie('refresh_token', $tokens['refresh_token'], $refreshTokenExpires / 60, '/', null, $secure, true);

            return $response;

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại.',
            ], 500);
        }
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
        try {
            $user = $request->user()->load('roles');

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng hiện tại.'
            ], 500);
        }
    }

    /**
     * Refresh access token
     */
    public function refresh(Request $request)
    {
        try {

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
            $accessToken = JWTAuth::fromUser($user);
            $accessTokenExpires = config('jwt.ttl') * 60; // seconds

            $response = response()->json([
                'success' => true,
                'message' => 'Refresh token thành công',
                'data' => [
                    'token_type' => 'Bearer',
                    'access_token' => $accessToken,
                    'access_token_expires_in' => $accessTokenExpires,
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
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = $request->user();
            $body = $request->validated();

            if ($request->hasFile('avatar')) {
                // Xóa avatar cũ nếu có
                if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                    Storage::disk('public')->delete($user->avatar_path);
                }

                // Lưu avatar mới 
                $extension = $body['avatar']->getClientOriginalExtension();
                $randomString = Str::random(30);
                $customFileName = 'user_' . $randomString . '_' . time() . '.' . $extension;
                $storedPath = $body['avatar']->storeAs('avatars', $customFileName, 'public');
                $publicUrl = Storage::url($storedPath); // ví dụ: /storage/avatars/user__1697059200.jpg

                $body['avatar_path'] = $publicUrl;
            }

            // Cập nhật thông tin (chỉ update những trường có trong data)
            // Loại bỏ avatar khỏi data nếu không có file upload để tránh lỗi, 
            // nhưng validated() chỉ trả về các trường đã validate.
            // Nếu avatar không có trong request thì validated() cũng không có key avatar (nếu nullable).
            // Tuy nhiên, nếu avatar gửi lên là null (text) thì sao? 
            // Rule là 'nullable|image', nếu gửi null thì validated có thể có key avatar => null.
            // Nếu key avatar => null, update sẽ set avatar_path = null (xóa avatar).
            // Nếu user muốn giữ avatar cũ thì không nên gửi key avatar hoặc gửi file.
            // Logic ở trên: if hasFile('avatar') thì xử lý upload và set avatar_path.
            // Nếu không hasFile, ta nên unset avatar khỏi data để tránh set null nếu user không gửi gì.
            
            if (!$request->hasFile('avatar')) {
                unset($body['avatar']);
            }

            $user->update($body);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => new UserResource($user)
            ]);

        } catch (Throwable $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật thông tin.'
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

