# LMS Backend - Laravel API

Backend API cho hệ thống Learning Management System (LMS) được xây dựng bằng Laravel.

## Yêu cầu hệ thống

- PHP >= 8.0
- Composer
- MySQL >= 5.7 hoặc PostgreSQL >= 10
- Redis (optional, cho queue và cache)
- FFmpeg (cho xử lý video)

## Cài đặt môi trường Development

### 1. Clone repository và cài đặt dependencies

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

### 2. Cấu hình Database

Chỉnh sửa file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_database
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Chạy migration và seeder

```bash
# Chạy migration
php artisan migrate

# Chạy seeder (nếu có)
php artisan db:seed
```

### 4. Tạo symbolic link cho storage

```bash
php artisan storage:link
```

### 5. Cấu hình Queue (cho xử lý video background)

Chỉnh sửa file `.env`:

```env
QUEUE_CONNECTION=database
# hoặc redis nếu bạn sử dụng Redis
# QUEUE_CONNECTION=redis
```

Chạy migration cho queue table:

```bash
php artisan queue:table
php artisan migrate
```

### 6. Khởi động các services

Mở **3 terminal** riêng biệt và chạy:

**Terminal 1 - Web Server:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
# Server sẽ chạy tại: http://localhost:8000
```

**Terminal 2 - Queue Worker (xử lý upload video):**
```bash
php artisan queue:work --tries=3
```

**Terminal 3 - Scheduler (chạy cleanup chunks mỗi 3 giờ):**
```bash
php artisan schedule:work
```

### 7. Test command cleanup chunks thủ công

```bash
# Xóa các chunk cũ hơn 24 giờ
php artisan video:cleanup-chunks

# Xóa các chunk cũ hơn 48 giờ
php artisan video:cleanup-chunks --hours=48
```

## Deploy lên Production

### 1. Chuẩn bị server

```bash
# Update server
sudo apt update && sudo apt upgrade -y

# Cài đặt PHP, Composer, MySQL, Nginx/Apache, Redis
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl composer mysql-server redis-server nginx -y

# Cài đặt FFmpeg (cho xử lý video)
sudo apt install ffmpeg -y
```

### 2. Clone code và cài đặt

```bash
# Clone project
cd /var/www
git clone <repository-url> lms-backend
cd lms-backend

# Cài đặt dependencies (production mode)
composer install --optimize-autoloader --no-dev

# Copy và cấu hình .env
cp .env.example .env
nano .env  # Chỉnh sửa cấu hình production

# Generate keys
php artisan key:generate
php artisan jwt:secret
```

### 3. Cấu hình .env cho Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lms_production
DB_USERNAME=lms_user
DB_PASSWORD=secure_password

QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cấu hình file storage
FILESYSTEM_DISK=public
```

### 4. Setup Database và Storage

```bash
# Tạo database
mysql -u root -p
CREATE DATABASE lms_production;
CREATE USER 'lms_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON lms_production.* TO 'lms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Chạy migration
php artisan migrate --force

# Tạo symbolic link
php artisan storage:link

# Phân quyền thư mục
sudo chown -R www-data:www-data /var/www/lms-backend
sudo chmod -R 775 /var/www/lms-backend/storage
sudo chmod -R 775 /var/www/lms-backend/bootstrap/cache
```

### 5. Optimize cho Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 6. Setup Queue Worker với Supervisor

Tạo file `/etc/supervisor/conf.d/lms-worker.conf`:

```ini
[program:lms-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/lms-backend/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/lms-backend/storage/logs/worker.log
stopwaitsecs=3600
```

Khởi động supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lms-worker:*
```

### 7. Setup Cron Job cho Scheduler

```bash
# Mở crontab
crontab -e

# Thêm dòng này (thay đường dẫn phù hợp)
* * * * * cd /var/www/lms-backend && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler sẽ tự động chạy:
- Command `video:cleanup-chunks` mỗi 3 giờ để xóa các chunk cũ

### 8. Cấu hình Nginx

Tạo file `/etc/nginx/sites-available/lms-backend`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/lms-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Tăng giới hạn upload file (cho video upload)
    client_max_body_size 2G;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Kích hoạt site:

```bash
sudo ln -s /etc/nginx/sites-available/lms-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. Setup SSL với Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com
```

### 10. Kiểm tra services

```bash
# Kiểm tra queue worker
sudo supervisorctl status lms-worker:*

# Kiểm tra cron job
crontab -l

# Kiểm tra logs
tail -f /var/www/lms-backend/storage/logs/laravel.log

# Test cleanup command
cd /var/www/lms-backend
php artisan video:cleanup-chunks
```

## Các lệnh hữu ích

### Development

```bash
# Chạy migration mới
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Refresh database
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Chạy tests
php artisan test
```

### Production

```bash
# Update code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migration
php artisan migrate --force

# Clear và rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart
sudo supervisorctl restart lms-worker:*

# Restart services
sudo systemctl reload nginx
sudo systemctl restart php8.1-fpm
```

## Monitoring và Maintenance

### Kiểm tra Queue

```bash
# Xem số lượng jobs đang chờ
php artisan queue:monitor

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Kiểm tra Scheduler

```bash
# Xem danh sách scheduled tasks
php artisan schedule:list

# Test scheduler
php artisan schedule:run
```

### Logs

```bash
# Laravel log
tail -f storage/logs/laravel.log

# Nginx access log
sudo tail -f /var/log/nginx/access.log

# Nginx error log
sudo tail -f /var/log/nginx/error.log

# Queue worker log
tail -f storage/logs/worker.log
```

## Troubleshooting

### Queue không chạy

```bash
# Kiểm tra supervisor status
sudo supervisorctl status

# Restart workers
sudo supervisorctl restart lms-worker:*

# Check queue table
php artisan queue:work --once
```

### Scheduler không chạy

```bash
# Kiểm tra cron job
crontab -l

# Test schedule manually
php artisan schedule:run

# Check logs
grep CRON /var/log/syslog
```

### Lỗi upload video

```bash
# Kiểm tra quyền thư mục
ls -la storage/app/lesson-video-uploads/

# Kiểm tra dung lượng disk
df -h

# Clear old chunks manually
php artisan video:cleanup-chunks --hours=1
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
