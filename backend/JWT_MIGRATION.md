# Hướng dẫn Migration từ Sanctum sang JWT

## Tổng quan

Hệ thống đã được chuyển từ Laravel Sanctum sang JWT Authentication với access-token và refresh-token.

## Thay đổi chính

### 1. Package
- **Cũ:** `laravel/sanctum`
- **Mới:** `tymon/jwt-auth`

### 2. Authentication Flow

#### Sanctum (Cũ)
- Sử dụng personal access tokens
- Token không có thời gian hết hạn mặc định
- Token được lưu trong bảng `personal_access_tokens`

#### JWT (Mới)
- Sử dụng JWT tokens với access-token và refresh-token
- Access token: 15 phút (có thể cấu hình)
- Refresh token: 2 tuần (có thể cấu hình)
- Refresh token được lưu trong bảng `refresh_tokens`

### 3. API Response Changes

#### Đăng ký / Đăng nhập (Cũ)
```json
{
  "success": true,
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxx"
  }
}
```

#### Đăng ký / Đăng nhập (Mới)
```json
{
  "success": true,
  "data": {
    "user": {...},
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "a1b2c3d4e5f6...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

### 4. API Endpoints

#### Endpoints mới
- `POST /api/auth/refresh` - Refresh access token (public)

#### Endpoints thay đổi
- Tất cả các endpoints protected sử dụng middleware `auth:api` thay vì `auth:sanctum`

### 5. Header yêu cầu

#### Cũ
```
Authorization: Bearer {sanctum_token}
```

#### Mới
```
Authorization: Bearer {jwt_access_token}
```

## Cách sử dụng

### 1. Đăng nhập
```bash
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "password123"
}

Response:
{
  "success": true,
  "data": {
    "user": {...},
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "refresh_token": "a1b2c3d4e5f6...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

### 2. Sử dụng Access Token
```bash
GET /api/auth/me
Headers:
  Authorization: Bearer {access_token}
```

### 3. Refresh Access Token
Khi access token hết hạn (sau 15 phút), sử dụng refresh token để lấy access token mới:

```bash
POST /api/auth/refresh
{
  "refresh_token": "a1b2c3d4e5f6..."
}

Response:
{
  "success": true,
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer",
    "expires_in": 900
  }
}
```

### 4. Đăng xuất
```bash
POST /api/auth/logout
Headers:
  Authorization: Bearer {access_token}
Body (optional):
{
  "refresh_token": "a1b2c3d4e5f6..."
}
```

## Cấu hình

### File: `.env`
```env
JWT_SECRET=your-secret-key
JWT_TTL=15          # Access token TTL (minutes)
JWT_REFRESH_TTL=20160  # Refresh token TTL (minutes) - 2 tuần
```

### File: `config/jwt.php`
- `ttl`: Thời gian sống của access token (mặc định: 15 phút)
- `refresh_ttl`: Thời gian sống của refresh token (mặc định: 2 tuần)

## Database Changes

### Bảng mới: `refresh_tokens`
- `id`: Primary key
- `user_id`: Foreign key đến users
- `token`: Refresh token (unique)
- `expires_at`: Thời gian hết hạn
- `ip_address`: IP address khi tạo token
- `user_agent`: User agent khi tạo token
- `is_revoked`: Trạng thái revoked
- `created_at`, `updated_at`: Timestamps

### Bảng cũ: `personal_access_tokens`
- Có thể giữ lại hoặc xóa (không còn sử dụng)

## Migration Steps

1. **Cài đặt package:**
   ```bash
   composer require tymon/jwt-auth:^1.0
   ```

2. **Publish config:**
   ```bash
   php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   ```

3. **Generate secret key:**
   ```bash
   php artisan jwt:secret
   ```

4. **Chạy migrations:**
   ```bash
   php artisan migrate
   ```

5. **Cập nhật `.env`:**
   ```env
   JWT_SECRET=your-secret-key
   JWT_TTL=15
   JWT_REFRESH_TTL=20160
   ```

## Lưu ý

1. **Access Token:**
   - Hết hạn sau 15 phút (mặc định)
   - Phải refresh để lấy token mới
   - Được sử dụng trong header `Authorization: Bearer {token}`

2. **Refresh Token:**
   - Hết hạn sau 2 tuần (mặc định)
   - Có thể sử dụng nhiều lần cho đến khi hết hạn
   - Nên lưu trữ an toàn (không lưu trong localStorage nếu có thể)

3. **Security:**
   - Refresh token được lưu trong database với hash
   - Có thể revoke refresh token khi đăng xuất
   - Access token được blacklist khi logout

4. **Error Handling:**
   - Access token hết hạn: Trả về 401 Unauthorized
   - Refresh token không hợp lệ: Trả về 401 Unauthorized
   - Cần xử lý refresh token tự động trong client

## Best Practices

1. **Client-side:**
   - Lưu access token và refresh token an toàn
   - Tự động refresh access token khi hết hạn
   - Xử lý lỗi 401 để refresh token

2. **Server-side:**
   - Validate refresh token trước khi tạo access token mới
   - Revoke refresh token khi cần thiết
   - Clean up expired refresh tokens định kỳ

3. **Security:**
   - Sử dụng HTTPS cho tất cả API calls
   - Không log access token hoặc refresh token
   - Implement rate limiting cho refresh endpoint

