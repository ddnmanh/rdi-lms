
<div align="center">

# 📚 LMS Backend - Laravel API

### Backend API cho hệ thống Learning Management System

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)](https://www.docker.com/)
[![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=flat&logo=json-web-tokens&logoColor=white)](https://jwt.io/)
[![HLS](https://img.shields.io/badge/HLS-Video-FF6B6B?style=flat&logo=html5&logoColor=white)](https://developer.apple.com/streaming/)

</div>

---

## 📋 Mục lục

- [Giới thiệu](#-giới-thiệu)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cài đặt nhanh](#-cài-đặt-nhanh)
- [Tài liệu chi tiết](#-tài-liệu-chi-tiết)
- [Scripts tiện ích](#-scripts-tiện-ích)
- [Tài liệu tham khảo](#-tài-liệu-tham-khảo)

---

## 🎯 Giới thiệu

LMS Backend là hệ thống API RESTful được xây dựng trên Laravel, cung cấp:

- ✅ **Authentication & Authorization**: JWT-based với role & permission system
- ✅ **Video Management**: Upload parallel chunks, HLS/MP4 streaming với signed URL
- ✅ **Course Management**: Quản lý khóa học, bài giảng, video lessons
- ✅ **Queue System**: Background processing cho video encoding
- ✅ **Docker Ready**: Deploy dễ dàng với Docker Compose

---

## 💻 Yêu cầu hệ thống

### Development
- **PHP**: 8.0.x với extensions: mbstring, xml, bcmath, curl, gd, zip
- **Composer**: Latest
- **MySQL**: 8.0+
- **FFmpeg**: Latest (cho xử lý video)

### Production (Docker)
- **Docker**: 20.10+
- **Docker Compose**: 1.29+
- **Nginx**: Reverse proxy (cấu hình HTTPS)

---

## 🚀 Cài đặt Development

### Bước 1: Clone repository và cài đặt dependencies

```bash
# Clone project
git clone <repository-url>
cd backend

# Cài đặt PHP dependencies
composer install

# Copy file môi trường
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret key
php artisan jwt:secret
```

<details>
<summary>✅ Output mong đợi</summary>

```
Application key set successfully.
jwt-auth secret [xxxxxxxxxxxxxx] set successfully.
```

</details>

---

### Bước 2: Cấu hình Database

Chỉnh sửa file `.env`:

```env
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🗄️ DATABASE CONFIGURATION (Development)
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_database
DB_USERNAME=root
DB_PASSWORD=your_password

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🎬 FFMPEG CONFIGURATION (Development - MacOS)
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Trên MacOS với Homebrew:
FFMPEG_PATH=/opt/homebrew/bin/ffmpeg

# Trên Linux:
# FFMPEG_PATH=/usr/bin/ffmpeg

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔐 HLS VIDEO SIGNED URL (Development)
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

STATIC_SOURCE_SECRET_KEY=your-super-secret-key-must-match-nginx
STATIC_SOURCE_DEFAULT_EXPIRY=3600
STATIC_SOURCE_BASE_URL=http://localhost:8000
HLS_RESTRICT_BY_IP=false
HLS_STORAGE_PATH=hls
```

---

### Bước 3: Chạy migration và seeder

```bash
# Tạo database (nếu chưa có)
mysql -u root -p -e "CREATE DATABASE lms_database;"

# Chạy migration
php artisan migrate

# Chạy seeder (nếu có)
php artisan db:seed

# Hoặc chạy cả hai cùng lúc
php artisan migrate:fresh --seed
```

> **💡 Tip:** Sử dụng `migrate:fresh --seed` để reset toàn bộ database với data mẫu.

---

### Bước 4: Tạo symbolic link cho storage

```bash
php artisan storage:link
```

**Kết quả:**
```
The [public/storage] link has been connected to [storage/app/public].
```

> **📝 Ghi chú:** Link này cho phép truy cập public files như video, images từ URL.

---

### Bước 5: Cấu hình Queue (cho xử lý video background)

Chỉnh sửa file `.env`:

```env
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔄 QUEUE CONFIGURATION
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

QUEUE_CONNECTION=database
# Hoặc sử dụng Redis cho hiệu năng tốt hơn:
# QUEUE_CONNECTION=redis
```

Chạy migration cho queue table:

```bash
php artisan queue:table
php artisan migrate
```

---

### Bước 6: Khởi động các services

Mở **3 terminal** riêng biệt:

#### Terminal 1️⃣ - Web Server

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Output:**
```
Laravel development server started: http://0.0.0.0:8000
[Ctrl+C to quit]
```

> 🌐 Server chạy tại: **http://localhost:8000**

---

#### Terminal 2️⃣ - Queue Worker (xử lý upload video)

```bash
php artisan queue:work --tries=3
```

**Output:**
```
[2025-11-22 10:30:00][1] Processing: App\Jobs\ProcessLessonVideoUpload
[2025-11-22 10:30:15][1] Processed:  App\Jobs\ProcessLessonVideoUpload
```

> 🔄 Worker xử lý background jobs như ghép video chunks

---

#### Terminal 3️⃣ - Scheduler (cleanup chunks)

```bash
php artisan schedule:work
```

**Output:**
```
[2025-11-22 10:00:00] Running scheduled command: Artisan::call('video:cleanup-chunks')
```

> ⏰ Scheduler chạy cleanup chunks mỗi 3 giờ

---

### Bước 7: Test command cleanup chunks

```bash
# Xóa các chunk cũ hơn 24 giờ (mặc định)
php artisan video:cleanup-chunks

# Xóa các chunk cũ hơn 48 giờ
php artisan video:cleanup-chunks --hours=48
```

<details>
<summary>📊 Output mẫu</summary>

```
Đang quét các chunk uploads cũ hơn 24 giờ...
Tìm thấy 5 upload sessions cũ:
  - Xóa: temp/lesson_video_uploads/9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f
  - Xóa: temp/lesson_video_uploads/8d7e6f5c-4a3b-2c1d-0e9f-8a7b6c5d4e3f
  ...
Đã xóa 5 sessions (320.5 MB)
```

</details>

---

## 🌐 Deploy Production

### 📚 Tài liệu Deploy

Dự án cung cấp hướng dẫn deploy chi tiết với Docker:

| Tài liệu | Mô tả |
|----------|-------|
| **[docs/DEPLOY_WITH_DOCKER.md](docs/DEPLOY_WITH_DOCKER.md)** | 🐳 Chi tiết deploy với Docker trên Ubuntu |
| **[VIDEO_SIGNED_URL_FLOW.md](VIDEO_SIGNED_URL_FLOW.md)** | 🎬 Luồng xử lý xem Video với signed URL |