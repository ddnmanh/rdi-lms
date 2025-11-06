# Hướng dẫn sử dụng Command quét Routes

## Tổng quan

Command `routes:scan` được sử dụng để tự động quét tất cả các routes trong ứng dụng và thêm vào bảng `permissions`. Điều này giúp tự động hóa việc quản lý permissions khi có routes mới được thêm vào.

## Cách sử dụng

### 1. Quét routes cơ bản

Quét tất cả routes có prefix `api` (mặc định):

```bash
php artisan routes:scan
```

### 2. Quét routes với prefix tùy chỉnh

Quét routes với prefix khác:

```bash
php artisan routes:scan --prefix=admin
```

### 3. Quét routes và xóa permissions cũ

Sử dụng option `--force` để xóa tất cả permissions cũ trước khi quét:

```bash
php artisan routes:scan --force
```

### 4. Hiển thị chi tiết

Sử dụng option `-v` hoặc `--verbose` để hiển thị tất cả routes (kể cả đã tồn tại):

```bash
php artisan routes:scan -v
```

## Các tùy chọn

| Option | Mô tả | Mặc định |
|--------|-------|----------|
| `--prefix` | Chỉ quét routes có prefix này | `api` |
| `--force` | Xóa tất cả permissions cũ trước khi quét | `false` |
| `-v, --verbose` | Hiển thị chi tiết tất cả routes | `false` |

## Cách hoạt động

1. **Quét routes**: Command sẽ quét tất cả routes đã đăng ký trong ứng dụng
2. **Lọc theo prefix**: Chỉ lấy các routes có prefix được chỉ định (mặc định là `api`)
3. **Chuẩn hóa path**: 
   - Tất cả route parameters (`{id}`, `{courseId}`, `{userId}`, etc.) sẽ được chuẩn hóa thành `{id}`
   - Ví dụ: `/api/courses/{courseId}` → `/api/courses/{id}`
4. **Lưu vào database**: 
   - Nếu permission chưa tồn tại → Tạo mới
   - Nếu permission đã tồn tại → Bỏ qua (hoặc restore nếu bị soft delete)
   - Nếu permission bị soft delete → Restore

## Ví dụ

### Quét routes API

```bash
php artisan routes:scan
```

Output:
```
Bắt đầu quét routes với prefix: /api
  + Added: POST /api/auth/register
  + Added: POST /api/auth/login
  + Added: POST /api/auth/refresh
  + Added: POST /api/auth/logout
  + Added: GET /api/auth/me
  + Added: GET /api/users
  + Added: POST /api/users
  + Added: GET /api/users/{id}
  + Added: PUT /api/users/{id}
  + Added: DELETE /api/users/{id}
  ...

Hoàn thành quét routes!
┌─────────────┬──────────┐
│ Thao tác    │ Số lượng │
├─────────────┼──────────┤
│ Đã thêm     │ 36       │
│ Đã cập nhật │ 0        │
│ Đã bỏ qua   │ 0        │
│ Tổng cộng   │ 36       │
└─────────────┴──────────┘
Tổng số permissions trong database: 36
```

### Quét lại routes (đã tồn tại)

```bash
php artisan routes:scan
```

Output:
```
Bắt đầu quét routes với prefix: /api
  - Skipped: POST /api/auth/register (đã tồn tại)
  - Skipped: POST /api/auth/login (đã tồn tại)
  ...

Hoàn thành quét routes!
┌─────────────┬──────────┐
│ Thao tác    │ Số lượng │
├─────────────┼──────────┤
│ Đã thêm     │ 0        │
│ Đã cập nhật │ 0        │
│ Đã bỏ qua   │ 36       │
│ Tổng cộng   │ 36       │
└─────────────┴──────────┘
Tổng số permissions trong database: 36
```

### Xóa và quét lại

```bash
php artisan routes:scan --force
```

Output:
```
Bắt đầu quét routes với prefix: /api
Đang xóa tất cả permissions cũ...
Đã xóa tất cả permissions cũ.
  + Added: POST /api/auth/register
  + Added: POST /api/auth/login
  ...

Hoàn thành quét routes!
┌─────────────┬──────────┐
│ Thao tác    │ Số lượng │
├─────────────┼──────────┤
│ Đã thêm     │ 36       │
│ Đã cập nhật │ 0        │
│ Đã bỏ qua   │ 0        │
│ Tổng cộng   │ 36       │
└─────────────┴──────────┘
Tổng số permissions trong database: 36
```

## Lưu ý

1. **Route Parameters**: Tất cả route parameters sẽ được chuẩn hóa thành `{id}`:
   - `/api/users/{id}` → `/api/users/{id}`
   - `/api/courses/{courseId}` → `/api/courses/{id}`
   - `/api/student/courses/{courseId}` → `/api/student/courses/{id}`

2. **HTTP Methods**: Chỉ lấy các methods: GET, POST, PUT, DELETE, PATCH. Bỏ qua OPTIONS và HEAD.

3. **Soft Delete**: Nếu permission bị soft delete, command sẽ tự động restore nó.

4. **Unique Constraint**: Bảng `permissions` có unique constraint trên `(method, path)`, nên không thể có duplicate.

5. **Prefix**: Mặc định chỉ quét routes có prefix `api`. Nếu bạn có routes khác, cần chỉ định prefix tương ứng.

## Tích hợp vào Workflow

### 1. Sau khi thêm routes mới

Sau khi thêm routes mới vào `routes/api.php`, chạy command để cập nhật permissions:

```bash
php artisan routes:scan
```

### 2. Trong CI/CD Pipeline

Có thể thêm vào CI/CD pipeline để tự động cập nhật permissions:

```yaml
# .github/workflows/deploy.yml
- name: Scan routes
  run: php artisan routes:scan
```

### 3. Trong Deployment Script

Thêm vào deployment script:

```bash
#!/bin/bash
php artisan migrate
php artisan routes:scan
php artisan config:cache
php artisan route:cache
```

## Troubleshooting

### Không quét được routes

- Kiểm tra xem routes đã được đăng ký chưa: `php artisan route:list`
- Kiểm tra prefix có đúng không
- Kiểm tra xem RouteServiceProvider có đăng ký routes chưa

### Permissions không được tạo

- Kiểm tra xem bảng `permissions` có tồn tại không: `php artisan migrate:status`
- Kiểm tra xem có lỗi database không
- Kiểm tra logs: `storage/logs/laravel.log`

### Duplicate permissions

- Bảng `permissions` có unique constraint, nên không thể có duplicate
- Nếu gặp lỗi duplicate, có thể do data cũ. Sử dụng `--force` để xóa và quét lại

## Best Practices

1. **Chạy sau khi thêm routes mới**: Luôn chạy command sau khi thêm routes mới
2. **Sử dụng --force cẩn thận**: Chỉ sử dụng `--force` khi cần xóa và quét lại toàn bộ
3. **Kiểm tra kết quả**: Luôn kiểm tra output để đảm bảo routes được quét đúng
4. **Backup trước khi --force**: Nếu sử dụng `--force`, nên backup database trước

