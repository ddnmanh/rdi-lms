
<div align="center">

# 🚀 DEPLOY VỚI DOCKER

### Triển khai dự án trên Ubuntu Server bằng Docker 

</div>

---

## 📋 Mục lục

- [Tổng quan](#-tổng-quan)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cấu trúc file liên quan](#-cấu-trúc-file-liên-quan)
- [Các bước deploy thủ công](#-các-bước-deploy-thủ-công)
- [Các bước deploy tự động](#-các-bước-deploy-tự-động)
- [Cấu hình Nginx ngoài server](#-cấu-hình-nginx-ngoài-server)
- [Kiểm tra sau deploy](#-kiểm-tra-sau-deploy)
- [Các lệnh thường dùng](#-các-lệnh-thường-dùng)
- [Deploy lại khi có thay đổi](#deploy-lại-khi-có-thay-đổi)
- [Troubleshooting](#-troubleshooting) 

---

## 🎯 Tổng quan

Tài liệu này hướng dẫn triển khai dự án lên Ubuntu Server có cài đặt sẵn MySQL và Nginx Server:

- ✅ Áp dụng cấu hình PHP-FPM để upload file dung lượng lớn

---

## 💻 Yêu cầu hệ thống

| Thành phần | Phiên bản | Mục đích |
|-----------|-----------|----------|
| Ubuntu Server | 24.04+ | Hệ điều hành |
| MySQL | 8.0+ | Cơ sở dữ liệu |
| Docker | 28.5+ | Môi trường chạy dự án |
| Docker Compose | 1.29+ | Môi trường chạy dự án theo cụm |
| Nginx | 1.24+ | Web Server cho server ngoài |
| rdi.sotech.io.vn |  | Domain truy cập dự án (Đã trỏ IP về server) | 

## 📁 Cấu trúc files liên quan
```
  source/
  ├── docker/
  │   └── nginx/
  │       └── nginx.conf          # Nginx cho docker (khác với nginx ngoài server)
  ├── .env.example                # Biến môi trường mẫu
  ├── Dockerfile                  # PHP 8.0-FPM container
  ├── docker-compose.yml          # Orchestration cho Nginx và PHP-FPM
  ├── nginx-host.conf             # Cấu hình nginx cho server (Điều hướng truy cập bên ngoài trỏ vào docker)
  └── deploy.sh                   # Script deploy tự động
```

**Lưu ý:** MySQL sử dụng MySQL đã có sẵn trên Ubuntu Server, không chạy trong Docker.

## 🔧 Các bước deploy thủ công

### 1️⃣ Chuẩn bị trên Server

```bash
# Clone hoặc copy code lên server
cd /home/ducmanh
git clone <your-repo> rdi-lms
cd rdi-lms/backend

# Hoặc sử dụng scp/rsync để copy code
```

### 2️⃣ Cấu hình Domain

Đảm bảo domain `rdi.sotech.io.vn` đã trỏ về IP server của bạn.

### 3️⃣ Cấu hình Environment

```bash
# Copy file environment
cp .env.example .env

# Chỉnh sửa các thông tin quan trọng
vim .env
```

**Các biến cần sửa:**

| Biến | Giá trị | Mô tả |
|------|---------|-------|
| `DB_HOST` | `host.docker.internal` | Giữ nguyên để Docker container kết nối với MySQL trên host |
| `DB_DATABASE` | `lms_database` | Tên database đã tạo trên MySQL server |
| `DB_USERNAME` | `lms_user` | User MySQL |
| `DB_PASSWORD` | `your_secure_password` | Mật khẩu MySQL |
| `JWT_SECRET` | `base64:abcdef123456...` | Secret key cho JWT |
| `APP_KEY` | *Auto-generate* | Sẽ được tự động generate bằng `php artisan key:generate` |
| `APP_ENV` | `production` | Môi trường sản phẩm |
| `APP_DEBUG` | `false` | Tắt chế độ debug |
| `APP_URL` | `http://rdi.sotech.io.vn` | URL của ứng dụng |

**Chuẩn bị MySQL trên Ubuntu Server:**

```bash
# Đăng nhập MySQL
sudo mysql -u root -p

# Tạo database và user
CREATE DATABASE lms_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lms_user'@'%' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON lms_database.* TO 'lms_user'@'%';
FLUSH PRIVILEGES;
EXIT;

# Cho phép MySQL listen từ Docker (sửa bind-address)
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# Tìm dòng: bind-address = 127.0.0.1
# Đổi thành: bind-address = 0.0.0.0

# Restart MySQL
sudo systemctl restart mysql
```

### 4️⃣ Deploy Thủ Công (Từng Bước)

#### Bước 1: Stop containers cũ (nếu có)
```bash
docker-compose down
```

#### Bước 2: Build Docker images
```bash
# Build lại images từ Dockerfile
docker-compose build --no-cache
```

#### Bước 3: Khởi động các containers
```bash
# Khởi động tất cả services (nginx, app)
docker-compose up -d

# Kiểm tra trạng thái
docker-compose ps
```

#### Bước 4: Test kết nối MySQL
```bash
# Test kết nối từ container đến MySQL host
docker-compose exec app php -r "new PDO('mysql:host=host.docker.internal;dbname=lms_database', 'lms_user', 'your_password');"

# Nếu thành công sẽ không có lỗi
```

#### Bước 5: Cài đặt dependencies
```bash
# Vào container và cài đặt Composer packages
docker-compose exec app composer install --optimize-autoloader --no-dev
```

#### Bước 6: Generate application key
```bash
# Tạo APP_KEY trong file .env
docker-compose exec app php artisan key:generate
```

### Bước 7: Tạo symplink
Tạo symlink để ánh xạ thư mục `public/storage` -> `storage/app/public`
```bash
docker-compose exec app php artisan storage:link
```

#### Bước 8: Chạy database migrations
```bash
# Tạo tables trong database
docker-compose exec app php artisan migrate --force

# Nếu cần chạy seeders
docker-compose exec app php artisan db:seed --force
```

#### Bước 9: Cache configurations
```bash
# Cache config để tăng performance
docker-compose exec app php artisan config:cache

# Cache routes
docker-compose exec app php artisan route:cache

# Cache views
docker-compose exec app php artisan view:cache
```

#### Bước 10: Set permissions
```bash
# Cấp quyền cho thư mục storage và cache
docker-compose exec app chown -R www-data:www-data /home/ducmanh/rdi/storage
docker-compose exec app chown -R www-data:www-data /home/ducmanh/rdi/bootstrap/cache
docker-compose exec app chmod -R 775 /home/ducmanh/rdi/storage
docker-compose exec app chmod -R 775 /home/ducmanh/rdi/bootstrap/cache
```

#### Bước 11: Restart để áp dụng changes
```bash
docker-compose restart
```

## ⚡ Các bước deploy tự động

```bash
# Cấp quyền thực thi cho script
chmod +x deploy.sh

# Chạy deploy
./deploy.sh
```

Script `deploy.sh` sẽ tự động thực hiện tất cả 10 bước ở trên.

## 🌐 Cấu hình Nginx ngoài server

Đảm bảo domain `rdi.sotech.io.vn` đã trỏ về IP server của bạn.

### 1. Đăng ký ssl cho `rdi.sotech.io.vn`
Hành động này là bắt buộc, xem tài liệu bên ngoài

### 2. Tạo file cấu hình

Di chuyển vào thư mục `/etc/nginx/sites-available` và tạo file cấu hình sau:
```bash
vim /etc/nginx/sites-available/rdi.sotech.io.vn
```
Ghi nội dung sau vào file vừa tạo hoặc có thể lấy nội dung từ `nginx-host.conf`:
```nginx
# HTTP Server - Redirect to HTTPS
server {
    listen 80;
    server_name rdi.sotech.io.vn;
    
    # Redirect all HTTP requests to HTTPS
    return 301 https://$server_name$request_uri;
}

# HTTPS Server
server {
    listen 443 ssl;
    server_name rdi.sotech.io.vn;

    # SSL Certificate paths (update these paths after generating certificates)
    ssl_certificate /etc/letsencrypt/live/rdi.sotech.io.vn/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/rdi.sotech.io.vn/privkey.pem;
    
    # SSL Configuration - Modern and secure
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384';
    ssl_prefer_server_ciphers off;
    
    # SSL Session cache
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;
    
    # HSTS (optional but recommended)
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # QUAN TRỌNG: Tăng giới hạn upload size
    # Phải đặt ở đây vì Nginx host nhận request trước Docker
    client_max_body_size 256M;
    client_body_buffer_size 128k;
    client_body_timeout 600s;

    location / {
        # Proxy đến Docker Nginx container (port 8080)
        proxy_pass http://localhost:8080;
        
        # Headers để giữ thông tin client
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Port $server_port;
        
        # Timeout cho upload file lớn
        proxy_connect_timeout 600;
        proxy_send_timeout 600;
        proxy_read_timeout 600;
        send_timeout 600;
        
        # Disable buffering để upload nhanh hơn
        # Request sẽ được stream trực tiếp vào backend
        proxy_buffering off;
        proxy_request_buffering off;
    }
}
```

### 1. Kích hoạt cấu hình

```bash
# Link sang thư mục cấu hình chính
sudo ln -s /etc/nginx/sites-available/rdi.sotech.io.vn /etc/nginx/sites-enabled/

# Kiểm tra cấu hình
sudo nginx -t

# Khởi động lại nginx để kích hoạt cấu hình mới
sudo systemctl restart nginx
```


## ✅ Kiểm tra sau deploy

```bash
# Xem logs
docker-compose logs -f

# Kiểm tra containers đang chạy
docker-compose ps

# Test kết nối database từ container
docker-compose exec app php artisan migrate:status

# Test từ host
curl http://localhost:8080
# Hoặc
curl -I http://localhost:8080
```

Mở trình duyệt và truy cập: `http://rdi.sotech.io.vn`

## 🛠️ Các lệnh thường dùng

```bash
# Xem logs
docker-compose logs -f app      # Logs PHP
docker-compose logs -f nginx    # Logs Nginx

# Restart services
docker-compose restart

# Stop services
docker-compose stop

# Start services
docker-compose start

# Vào container để chạy commands
docker-compose exec app bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan cache:clear

# Rebuild sau khi thay đổi code
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 🔄 Deploy lại khi có sự thay đổi về code

Khi bạn cần update code mới lên server:

```bash
# 1. Pull code mới (nếu dùng git)
git pull origin main

# 2. Rebuild nếu có thay đổi Dockerfile, nginx.conf, hoặc dependencies
docker-compose build --no-cache

# 3. Restart containers
docker-compose down
docker-compose up -d

# 4. Clear và cache lại
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app composer install --optimize-autoloader --no-dev
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache

# 5. Chạy migrations mới (nếu có)
docker-compose exec app php artisan migrate --force

# 6. Restart lại
docker-compose restart
```

## 🔄 Deploy lại khi có sự thay đổi cấu hình Nginx trong Docker

**File:** `docker/nginx/nginx.conf`

Khi bạn sửa file `docker/nginx/nginx.conf` (ví dụ tăng `client_max_body_size`):

```bash
# 1. Pull code mới hoặc edit trực tiếp trên server
git pull origin main
# Hoặc: nano docker/nginx/nginx.conf

# 2. Restart nginx container để áp dụng config mới
docker-compose restart nginx

# 3. Verify config đã được load
docker-compose exec nginx nginx -t

# 4. Xem config hiện tại
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf | grep client_max_body_size
```

## 🔍 Troubleshooting

### ⚠️ Lỗi 413 Request Entity Too Large (Upload file lớn)

**Triệu chứng:**
- HTTP 413 Request Entity Too Large khi upload file
- Xảy ra ở endpoint upload chunks: `/api/lesson-video-uploads/{id}/chunks`

**Nguyên nhân:**
- **Nginx reverse proxy trên Ubuntu host** chưa có `client_max_body_size` (lỗi phổ biến nhất!)
- Hoặc Nginx trong Docker `client_max_body_size` quá nhỏ (mặc định 1MB)

**Giải pháp 1: Fix Nginx trên Ubuntu Host (Reverse Proxy)**

Thực hiện bước cấu hình theo hướng dẫn [Cấu hình Nginx ngoài server](#-cấu-hình-nginx-ngoài-server).

**Giải pháp 2: Fix Nginx trong Docker**

```bash
# 1. Kiểm tra config hiện tại
cd /home/ducmanh/rdi
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf | grep client_max_body_size

# 2. Nếu không thấy hoặc giá trị nhỏ, cần pull code mới
git pull origin main

# 3. Restart nginx để áp dụng config
docker-compose restart nginx

# 4. Verify lại
docker-compose exec nginx nginx -t
docker-compose exec nginx cat /etc/nginx/conf.d/default.conf | grep -A 2 client_max_body_size

# Nếu vẫn không thấy, kiểm tra volume mapping
docker-compose exec nginx ls -la /etc/nginx/conf.d/
```

**Lưu ý**: File `docker/nginx/nginx.conf` đã được cấu hình:
- `client_max_body_size 256M` - Cho phép upload file tối đa 256MB
- `fastcgi_read_timeout 600` - Timeout 10 phút cho PHP xử lý
- Các buffer size phù hợp cho upload lớn

### ⚠️ Lỗi 500 - Missing APP_KEY hoặc Invalid Signature

**Triệu chứng:**
- HTTP 500 Internal Server Error
- Log: `No application encryption key has been specified`
- Log: `InvalidSignatureException: Your serialized closure might have been modified`

**Nguyên nhân:**
- File `.env` chưa có `APP_KEY`
- Cache config/routes đã serialize với APP_KEY cũ

**Giải pháp:**
```bash
# Xóa tất cả cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app rm -rf bootstrap/cache/*.php

# Generate APP_KEY mới
docker-compose exec app php artisan key:generate --force

# Restart để áp dụng
docker-compose restart

# Verify APP_KEY đã được tạo
docker-compose exec app grep "^APP_KEY=" .env
```

### ⚠️ Lỗi permission storage
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### 🧹 Clear cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### ⚠️ Database connection failed
- Kiểm tra MySQL đang chạy trên host: `sudo systemctl status mysql`
- Kiểm tra credentials trong `.env`
- Kiểm tra MySQL bind-address: `sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf`
- Test kết nối: `docker-compose exec app php artisan tinker` -> `DB::connection()->getPdo();`
- Kiểm tra firewall: `sudo ufw status`

### ⚠️ Không kết nối được MySQL từ Docker
```bash
# Kiểm tra MySQL có listen 0.0.0.0 không
sudo netstat -tulpn | grep 3306

# Nếu chỉ thấy 127.0.0.1:3306, cần sửa bind-address
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
# bind-address = 0.0.0.0

sudo systemctl restart mysql

# Test từ container
docker-compose exec app ping host.docker.internal
docker-compose exec app nc -zv host.docker.internal 3306
```

### ⚠️ Container không start được
```bash
# Xem logs để tìm lỗi
docker-compose logs app
docker-compose logs nginx

# Kiểm tra port đã được sử dụng chưa
sudo netstat -tulpn | grep :80

# Nếu port 80 đã được dùng bởi Nginx host, stop nó
sudo systemctl stop nginx
```

### ⚠️ Composer install failed
```bash
# Xóa vendor và thử lại
docker-compose exec app rm -rf vendor
docker-compose exec app composer clear-cache
docker-compose exec app composer install --optimize-autoloader --no-dev
```

### ⚠️ Migration failed
```bash
# Rollback và migrate lại
docker-compose exec app php artisan migrate:rollback
docker-compose exec app php artisan migrate --force

# Hoặc fresh (XÓA HẾT DATA - cẩn thận!)
docker-compose exec app php artisan migrate:fresh --force
```
