
<div align="center">

# 📚 LMS Backend - Laravel API

### Backend API cho hệ thống Learning Management System

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Redis](https://img.shields.io/badge/Redis-Queue-DC382D?style=flat&logo=redis&logoColor=white)](https://redis.io/)
[![JWT](https://img.shields.io/badge/JWT-Auth-000000?style=flat&logo=json-web-tokens&logoColor=white)](https://jwt.io/)

</div>

---

## 📋 Mục lục

- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cài đặt Development](#-cài-đặt-development)
- [Deploy Production](#-deploy-production)
- [Các lệnh hữu ích](#-các-lệnh-hữu-ích)
- [Monitoring & Maintenance](#-monitoring--maintenance)
- [Troubleshooting](#-troubleshooting)
- [Tài liệu tham khảo](#-tài-liệu-tham-khảo)

---

## 💻 Yêu cầu hệ thống

| Thành phần | Phiên bản | Bắt buộc | Ghi chú |
|-----------|-----------|----------|---------|
| **PHP** | 8.0 | ✅ | Với extensions: mbstring, xml, bcmath, curl |
| **Composer** | Latest | ✅ | Package manager cho PHP |
| **MySQL** | ≥ 5.7 | ✅ | Hoặc PostgreSQL ≥ 10 |
| **Redis** | Latest | ❌ | Cho queue và cache (khuyến nghị) |
| **FFmpeg** | Latest | ✅ | Cho xử lý video |
| **Nginx/Apache** | Latest | ✅ | Web server (production) |

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
# 🗄️ DATABASE CONFIGURATION
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_database
DB_USERNAME=root
DB_PASSWORD=your_password
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

Đọc file [docs/DEPLOY_WITH_DOCKER.md](docs/DEPLOY_WITH_DOCKER.md)

## 🛠️ Các lệnh hữu ích

### Development Commands

```bash
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🗄️ DATABASE
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Chạy migration mới
php artisan migrate

# Rollback migration gần nhất
php artisan migrate:rollback

# Rollback tất cả migrations
php artisan migrate:reset

# Refresh database (drop all + migrate + seed)
php artisan migrate:fresh --seed

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🧹 CACHE
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔐 ROUTES & PERMISSIONS
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Scan routes to permissions
php artisan routes:scan

# List all routes
php artisan route:list

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🧪 TESTING
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Run all tests
php artisan test

# Run specific test
php artisan test --filter=UserTest

# Run tests with coverage
php artisan test --coverage
```

---

### Production Commands

```bash
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🚀 DEPLOYMENT
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# 1. Update code
git pull origin main

# 2. Update dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Clear và rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Restart queue workers
php artisan queue:restart
sudo supervisorctl restart lms-worker:*

# 6. Restart services
sudo systemctl reload nginx
sudo systemctl restart php8.1-fpm

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔄 QUEUE MANAGEMENT
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Monitor queue
php artisan queue:monitor

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush

# Restart workers
php artisan queue:restart
```

---

## 📊 Monitoring & Maintenance

### Kiểm tra Queue

```bash
# Xem số lượng jobs đang chờ
php artisan queue:monitor

# List failed jobs
php artisan queue:failed

# Retry specific failed job
php artisan queue:retry {job-id}

# Retry all failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

<details>
<summary>📊 Output mẫu</summary>

```
+--------------------------------------+--------------------+-----------+
| ID                                   | Connection         | Queue     |
+--------------------------------------+--------------------+-----------+
| 9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f | redis             | default   |
+--------------------------------------+--------------------+-----------+
```

</details>

---

### Kiểm tra Scheduler

```bash
# Xem danh sách scheduled tasks
php artisan schedule:list

# Test scheduler (chạy thử ngay)
php artisan schedule:run

# Chạy specific command
php artisan video:cleanup-chunks
```

<details>
<summary>📋 Scheduled tasks</summary>

| Command | Schedule | Description |
|---------|----------|-------------|
| `video:cleanup-chunks` | Every 3 hours | Xóa video chunks cũ hơn 24h |

</details>

---

### Logs

```bash
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 📝 APPLICATION LOGS
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Laravel log (realtime)
tail -f storage/logs/laravel.log

# Worker log (queue jobs)
tail -f storage/logs/worker.log

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🌐 SERVER LOGS
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Nginx access log
sudo tail -f /var/log/nginx/access.log

# Nginx error log
sudo tail -f /var/log/nginx/error.log

# PHP-FPM log
sudo tail -f /var/log/php8.1-fpm.log

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔍 SEARCH LOGS
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

# Tìm errors trong Laravel log
grep "ERROR" storage/logs/laravel.log

# Tìm failed jobs
grep "failed" storage/logs/worker.log

# Kiểm tra cron jobs
grep CRON /var/log/syslog
```

---

## 🔧 Troubleshooting

<details>
<summary><strong>❌ Queue không chạy</strong></summary>

**Triệu chứng:**
- Jobs không được xử lý
- Video uploads không được ghép

**Kiểm tra:**

```bash
<<<<<<< Updated upstream
# Kiểm tra quyền thư mục
ls -la storage/app/lessons/video-uploads/
=======
# 1. Kiểm tra supervisor
sudo supervisorctl status
>>>>>>> Stashed changes

# 2. Xem logs
tail -f storage/logs/worker.log

# 3. Test chạy thủ công
php artisan queue:work --once
```

**Giải pháp:**

```bash
# Restart workers
sudo supervisorctl restart lms-worker:*

# Nếu không được, reread config
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

</details>

<details>
<summary><strong>❌ Scheduler không chạy</strong></summary>

**Triệu chứng:**
- Chunks cũ không được xóa
- Scheduled tasks không chạy

**Kiểm tra:**

```bash
# 1. Kiểm tra cron job
crontab -l

# 2. Test schedule manually
php artisan schedule:run

# 3. Check logs
grep CRON /var/log/syslog
```

**Giải pháp:**

```bash
# Thêm lại cron job
crontab -e
# Thêm: * * * * * cd /var/www/lms-backend && php artisan schedule:run >> /dev/null 2>&1

# Verify
crontab -l
```

</details>

<details>
<summary><strong>❌ Lỗi upload video</strong></summary>

**Triệu chứng:**
- Upload fails với lỗi 500
- Chunks không được lưu

**Kiểm tra:**

```bash
# 1. Kiểm tra quyền thư mục
ls -la storage/app/temp/lesson_video_uploads/

# 2. Kiểm tra dung lượng disk
df -h

# 3. Kiểm tra PHP upload limits
php -i | grep -E 'upload_max_filesize|post_max_size|memory_limit'
```

**Giải pháp:**

```bash
# Fix permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/

# Clear old chunks
php artisan video:cleanup-chunks --hours=1
<<<<<<< Updated upstream
```
=======

# Kiểm tra logs
tail -f storage/logs/laravel.log
```

</details>

<details>
<summary><strong>❌ 502 Bad Gateway (Nginx)</strong></summary>

**Nguyên nhân:**
- PHP-FPM không chạy
- Socket không đúng
- Permissions

**Kiểm tra:**

```bash
# 1. Kiểm tra PHP-FPM
sudo systemctl status php8.1-fpm

# 2. Kiểm tra socket
ls -la /var/run/php/php8.1-fpm.sock

# 3. Check logs
sudo tail -f /var/log/nginx/error.log
```

**Giải pháp:**

```bash
# Restart PHP-FPM
sudo systemctl restart php8.1-fpm

# Restart Nginx
sudo systemctl restart nginx
```

</details>

<details>
<summary><strong>⚠️ Performance issues</strong></summary>

**Triệu chứng:**
- Response chậm
- Queue jobs xử lý lâu

**Optimization:**

```bash
# 1. Enable cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative

# 3. Enable OPcache (php.ini)
# opcache.enable=1
# opcache.memory_consumption=256
# opcache.interned_strings_buffer=16
# opcache.max_accelerated_files=10000

# 4. Use Redis for cache & sessions
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

</details>

---

## 📚 Tài liệu tham khảo

### Tài liệu API

- 📄 [API Upload Video Background](docs/PARALLEL_CHUNK_VIDEO_UPLOAD_API.md)
- ⚙️ [Cấu hình Server Upload](docs/PARALLEL_CHUNK_VIDEO_UPLOAD_CONFIG.md)
- 🔐 [Routes Scan to Permission](docs/ROUTES_SCAN_TO_PERMISSION.md)

### External Resources

- 🌐 [Laravel Documentation](https://laravel.com/docs/8.x)
- 🔑 [JWT Authentication](https://jwt-auth.readthedocs.io/)
- 🔄 [Laravel Queue](https://laravel.com/docs/8.x/queues)
- 📦 [Laravel Storage](https://laravel.com/docs/8.x/filesystem)
- 🐘 [PHP-FPM Configuration](https://www.php.net/manual/en/install.fpm.php)
- 🌐 [Nginx Documentation](https://nginx.org/en/docs/)

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📝 License

This project is licensed under the MIT License.

---

<div align="center">

### 🎉 Happy Coding!

Made with ❤️ by the Dnmanh

</div>
>>>>>>> Stashed changes
