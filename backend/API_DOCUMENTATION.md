# Tài liệu API - Hệ thống Quản lý Học trực tuyến

## Tổng quan

Hệ thống API được xây dựng bằng Laravel 8 với Sanctum authentication. Tất cả các API trả về dữ liệu dưới dạng JSON.

## Base URL

```
http://your-domain.com/api
```

## Authentication

Hầu hết các API yêu cầu authentication thông qua Bearer Token. Token được trả về khi đăng nhập hoặc đăng ký.

### Header yêu cầu cho các API protected:
```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

## API Endpoints

### 1. Authentication APIs

#### 1.1. Đăng ký
- **URL:** `POST /api/auth/register`
- **Auth:** Không cần
- **Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "fullname": "Nguyễn Văn A",
  "birthday": "2000-01-01"
}
```
- **Response:**
```json
{
  "success": true,
  "message": "Đăng ký thành công",
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxx"
  }
}
```

#### 1.2. Đăng nhập
- **URL:** `POST /api/auth/login`
- **Auth:** Không cần
- **Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
- **Response:**
```json
{
  "success": true,
  "message": "Đăng nhập thành công",
  "data": {
    "user": {...},
    "token": "1|xxxxxxxxxxxx"
  }
}
```

#### 1.3. Đăng xuất
- **URL:** `POST /api/auth/logout`
- **Auth:** Cần

#### 1.4. Lấy thông tin user hiện tại
- **URL:** `GET /api/auth/me`
- **Auth:** Cần

---

### 2. User Management APIs (Admin)

#### 2.1. Danh sách người dùng
- **URL:** `GET /api/users`
- **Auth:** Cần
- **Query params:**
  - `search`: Tìm kiếm theo email hoặc fullname
  - `role_id`: Lọc theo role
  - `per_page`: Số lượng mỗi trang (mặc định: 15)
  - `page`: Số trang

#### 2.2. Chi tiết người dùng
- **URL:** `GET /api/users/{id}`
- **Auth:** Cần

#### 2.3. Tạo người dùng mới
- **URL:** `POST /api/users`
- **Auth:** Cần
- **Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "fullname": "Nguyễn Văn A",
  "birthday": "2000-01-01",
  "path_avatar": "path/to/avatar.jpg",
  "role_ids": [1, 2]
}
```

#### 2.4. Cập nhật người dùng
- **URL:** `PUT /api/users/{id}`
- **Auth:** Cần
- **Body:** Tương tự như tạo mới

#### 2.5. Xóa người dùng
- **URL:** `DELETE /api/users/{id}`
- **Auth:** Cần

#### 2.6. Phân quyền cho người dùng
- **URL:** `POST /api/users/{id}/roles`
- **Auth:** Cần
- **Body:**
```json
{
  "role_ids": [1, 2]
}
```

---

### 3. Course Management APIs (Admin)

#### 3.1. Danh sách khóa học
- **URL:** `GET /api/courses`
- **Auth:** Cần
- **Query params:**
  - `search`: Tìm kiếm theo title hoặc description
  - `per_page`: Số lượng mỗi trang
  - `page`: Số trang

#### 3.2. Chi tiết khóa học
- **URL:** `GET /api/courses/{id}`
- **Auth:** Cần

#### 3.3. Tạo khóa học mới
- **URL:** `POST /api/courses`
- **Auth:** Cần
- **Body:**
```json
{
  "title": "Lập trình Laravel",
  "description": "Khóa học về Laravel framework",
  "start_date": "2024-01-01 00:00:00",
  "end_date": "2024-12-31 23:59:59"
}
```

#### 3.4. Cập nhật khóa học
- **URL:** `PUT /api/courses/{id}`
- **Auth:** Cần

#### 3.5. Xóa khóa học
- **URL:** `DELETE /api/courses/{id}`
- **Auth:** Cần

#### 3.6. Phân quyền sinh viên vào khóa học (sync - thay thế toàn bộ)
- **URL:** `POST /api/courses/{id}/users`
- **Auth:** Cần
- **Body:**
```json
{
  "user_ids": [1, 2, 3]
}
```

#### 3.7. Thêm sinh viên vào khóa học
- **URL:** `POST /api/courses/{id}/users/add`
- **Auth:** Cần
- **Body:**
```json
{
  "user_id": 1
}
```

#### 3.8. Xóa sinh viên khỏi khóa học
- **URL:** `POST /api/courses/{id}/users/remove`
- **Auth:** Cần
- **Body:**
```json
{
  "user_id": 1
}
```

---

### 4. Lesson Management APIs (Admin)

#### 4.1. Danh sách bài học
- **URL:** `GET /api/lessons`
- **Auth:** Cần
- **Query params:**
  - `course_id`: Lọc theo khóa học
  - `search`: Tìm kiếm
  - `per_page`: Số lượng mỗi trang
  - `page`: Số trang

#### 4.2. Chi tiết bài học
- **URL:** `GET /api/lessons/{id}`
- **Auth:** Cần

#### 4.3. Tạo bài học mới
- **URL:** `POST /api/lessons`
- **Auth:** Cần
- **Body:**
```json
{
  "course_id": 1,
  "title": "Giới thiệu Laravel",
  "description": "Bài học đầu tiên",
  "duration": 3600,
  "video_url": "https://example.com/video.mp4",
  "display_order": 1
}
```
- **Lưu ý:** `duration` tính bằng giây

#### 4.4. Cập nhật bài học
- **URL:** `PUT /api/lessons/{id}`
- **Auth:** Cần

#### 4.5. Xóa bài học
- **URL:** `DELETE /api/lessons/{id}`
- **Auth:** Cần

---

### 5. Student APIs

#### 5.1. Danh sách khóa học của sinh viên
- **URL:** `GET /api/student/courses`
- **Auth:** Cần
- **Response:** Trả về danh sách khóa học mà sinh viên được phép truy cập

#### 5.2. Chi tiết khóa học của sinh viên
- **URL:** `GET /api/student/courses/{courseId}`
- **Auth:** Cần
- **Response:** Bao gồm thông tin khóa học, danh sách bài học và tiến độ học của từng bài

#### 5.3. Chi tiết bài học của sinh viên
- **URL:** `GET /api/student/lessons/{lessonId}`
- **Auth:** Cần
- **Response:** Bao gồm thông tin bài học và tiến độ học

#### 5.4. Cập nhật tiến độ xem bài học
- **URL:** `POST /api/student/progress`
- **Auth:** Cần
- **Body:**
```json
{
  "lesson_id": 1,
  "watched_duration": 1800,
  "last_position": 1800
}
```
- **Lưu ý:** 
  - `watched_duration`: Tổng thời gian đã xem (giây)
  - `last_position`: Vị trí dừng lại để resume (giây)

#### 5.5. Tiến độ học của sinh viên trong một khóa học
- **URL:** `GET /api/student/courses/{courseId}/progress`
- **Auth:** Cần
- **Response:**
```json
{
  "success": true,
  "data": {
    "course_id": 1,
    "course_title": "Lập trình Laravel",
    "total_duration": 7200,
    "total_watched_duration": 5760,
    "overall_percentage": 80.0,
    "is_completed": true
  }
}
```

---

### 6. Report APIs (Admin)

#### 6.1. Tổng quan thống kê
- **URL:** `GET /api/reports/overview`
- **Auth:** Cần
- **Response:** Thống kê tổng quan về số lượng khóa học, sinh viên, bài học

#### 6.2. Thống kê khóa học
- **URL:** `GET /api/reports/courses/{courseId}/statistics`
- **Auth:** Cần
- **Response:** Số lượng sinh viên, bài học, tổng thời lượng

#### 6.3. Danh sách sinh viên trong khóa học với tiến độ
- **URL:** `GET /api/reports/courses/{courseId}/students`
- **Auth:** Cần
- **Query params:**
  - `per_page`: Số lượng mỗi trang
  - `page`: Số trang
- **Response:** Danh sách sinh viên với thông tin tiến độ học và đánh giá đạt/không đạt (>= 80%)

#### 6.4. Chi tiết tiến độ học của một sinh viên trong khóa học
- **URL:** `GET /api/reports/courses/{courseId}/students/{userId}/progress`
- **Auth:** Cần
- **Response:** Chi tiết tiến độ học của từng bài học và tổng quan

#### 6.5. Lịch sử đăng nhập của sinh viên
- **URL:** `GET /api/reports/students/{userId}/login-history`
- **Auth:** Cần
- **Response:** Lịch sử đăng nhập với thông tin IP, user agent, thời gian

---

## Response Format

### Success Response
```json
{
  "success": true,
  "message": "Thông báo thành công",
  "data": {...}
}
```

### Error Response
```json
{
  "success": false,
  "message": "Thông báo lỗi",
  "errors": {
    "field": ["Lỗi validation"]
  }
}
```

### HTTP Status Codes
- `200`: Success
- `201`: Created
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

---

## Lưu ý

1. Tất cả các API yêu cầu authentication (trừ register và login) cần gửi Bearer Token trong header
2. Thời gian được lưu dưới dạng timestamp hoặc datetime string
3. `duration` trong bài học tính bằng giây
4. Sinh viên được đánh giá đạt yêu cầu nếu tổng thời lượng xem video >= 80% tổng thời lượng khóa học
5. Soft delete được sử dụng cho users, courses, lessons (có thể khôi phục)
6. Pagination mặc định là 15 items mỗi trang

---

## Ví dụ sử dụng với cURL

### Đăng nhập
```bash
curl -X POST http://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

### Lấy danh sách khóa học (sau khi đăng nhập)
```bash
curl -X GET http://your-domain.com/api/courses \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept: application/json"
```

### Cập nhật tiến độ học
```bash
curl -X POST http://your-domain.com/api/student/progress \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "lesson_id": 1,
    "watched_duration": 1800,
    "last_position": 1800
  }'
```

