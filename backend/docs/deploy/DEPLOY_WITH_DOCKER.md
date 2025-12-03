
<div align="center">

# 🚀 DEPLOY VỚI DOCKER

### Triển khai dự án trên Ubuntu Server bằng Docker 

</div>

---

## 📋 Mục lục

- [Tổng quan](#-tổng-quan)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cấu trúc file liên quan](#-cấu-trúc-files-liên-quan)
- [Các bước deploy thủ công](#-các-bước-deploy-thủ-công)
- [Cấu hình Nginx ngoài server](#️⃣-cấu-hình-nginx-ngoài-server)
- [Kiểm tra sau deploy](#️⃣-kiểm-tra-sau-deploy)
- [Các lệnh thường dùng](#️-các-lệnh-thường-dùng)
- [Deploy lại khi có sự thay đổi về code](#-deploy-lại-khi-có-sự-thay-đổi-về-code)
- [Deploy lại khi có sự thay đổi cấu hình Nginx trong Docker](#-deploy-lại-khi-có-sự-thay-đổi-cấu-hình-nginx-trong-docker)
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
| Docker-Compose | 1.29+ | Môi trường chạy dự án theo cụm |
| Nginx | 1.24+ | Web Server cho server ngoài |
| lms.domain.vn |  | Domain truy cập dự án (Đã trỏ IP về server) | 
| FFmpeg | latest | Dùng trong container PHP để tạo HLS |

## 📁 Cấu trúc files liên quan
```
    source/
    ├── ⚙️ .env.example                         # Biến môi trường mẫu
    ├── 🐳 Dockerfile                           # PHP 8.0-FPM container
    ├── 🐳 docker-compose.yml                   # Orchestration cho Nginx và PHP-FPM
    ├── 🌐 nginx-host.conf                      # Cấu hình nginx cho server (Điều hướng truy cập bên ngoài trỏ vào docker)
    ├── 🔧 verify-deployment-with-docker.sh     # Script tự động kiểm tra deploy đã thành công hay chưa
    └── docker/
        ├── nginx/
        │   └── nginx.conf                      # Nginx config trong Docker
        └── php-fpm/
            └── www.conf                        # PHP-FPM config
```

**Lưu ý:** MySQL sử dụng MySQL đã có sẵn trên Ubuntu Server, không chạy trong Docker.

## 🔧 Các bước deploy thủ công

### Quy ước các giá trị làm mẫu
-   Đặt tên thư mục chưa source là `lms`
-   Chọn thư mục `/home/ducmanh` làm nơi đặt lưu source ở Ubuntu Server
-   Chọn domain `lms.domain.vn` làm domain truy cập app

### 1️⃣ Chuẩn bị trên Server

Đặt source backend vào `/home/ducmanh`, di chuyển vào source
``` bash
cd lms
```

### 2️⃣ Cấu hình Domain

Đảm bảo domain `lms.domain.vn` đã trỏ về IP server.

### 3️⃣ Cấu hình Environment

Đổi tên `.evn.example` thành `.env` và sửa lại các giá trị quan trọng sau
```bash
cp .env.example .env
```

**Các biến cần sửa:**

| Biến | Giá trị | Mô tả |
|------|---------|-------|
| `APP_KEY` | *Auto-generate* | Sẽ được tự động generate bằng `php artisan key:generate` |
| `APP_ENV` | `production` | Môi trường sản phẩm |
| `APP_DEBUG` | `false` | Tắt chế độ debug |
| `APP_URL` | `http://lms.domain.vn` | URL của ứng dụng |
| `DB_HOST` | `host.docker.internal` | Giữ nguyên để Docker container kết nối với MySQL trên host |
| `DB_DATABASE` | `lms_database` | Tên database đã tạo trên MySQL server |
| `DB_USERNAME` | `lms_user` | User MySQL |
| `DB_PASSWORD` | `your_secure_password` | Mật khẩu MySQL |
| `JWT_SECRET` | `base64:abcdef123456...` | Secret key cho JWT |
| `FFMPEG_PATH` | `ffmpeg` | Binary ffmpeg bên trong container |
| `STATIC_SOURCE_SECRET_KEY` | `...` | Secret dùng ký URL HLS (phải trùng với Nginx HLS `/docker/nginx/nginx.conf`) |
| `STATIC_SOURCE_DEFAULT_EXPIRY` | `3600` | Thời gian hết hạn URL HLS (giây) |
| `STATIC_SOURCE_BASE_URL` | `https://lms.domain.vn` | Base URL cho HLS playlist/segment |

**Lưu ý cho FFmpeg/HLS khi chạy trong Docker:**

- Image PHP (xem `Dockerfile`) đã cài sẵn ffmpeg bằng `apt-get`, nên bên trong container lệnh `ffmpeg` đã khả dụng.
- Trong `.env` trên server bạn nên để:

```env
FFMPEG_PATH=ffmpeg
```

- Các biến `HLS_*` cần được set đúng domain thật (`APP_URL`) để signed URL khi trả về cho frontend có thể play được.

### 4️⃣ Sửa cấu hình Nginx container
Mở file cấu hình tại `docker/nginx/nginx.conf` và sửa lại giá trị của biến `secure_link_secret` phải giống với giá trị của biến `STATIC_SOURCE_SECRET_KEY` trong `.env`.
```bash
vim docker/nginx/nginx.conf
```

### 5️⃣ Deploy Docker chính thức qua các bước

#### Bước 1: Đảm bảo đang đứng ở thư mục gốc của source
Bước này là bắt buộc vì các lệnh deploy được viết ở trạng thái đứng ở thư mục gốc của source để Docker tham chiếu đến các file cấu hình tự động mà không cần phải chỉ định tên rõ ràng.

#### Bước 2: Stop containers cũ (nếu có)
```bash
docker-compose down
```

#### Bước 3: Build Docker images
```bash
# Build lại images từ Dockerfile
docker-compose build --no-cache
```

#### Bước 4: Khởi động các containers
```bash
# Khởi động tất cả services (nginx, app)
docker-compose up -d

# Kiểm tra trạng thái
docker-compose ps
```

#### Bước 5: Kiểm tra FFmpeg trong container
Đảm bảo binary `ffmpeg` sẵn sàng bên trong container `app`:

```bash
# Kiểm tra phiên bản
docker-compose exec app ffmpeg -version

# Kiểm tra đường dẫn đầy đủ
docker-compose exec app which ffmpeg
```


- Nếu lệnh trên in ra version FFmpeg thì OK.
- Nếu báo `ffmpeg: command not found` thì kiểm tra lại `Dockerfile` phải có bước `apt-get install -y ffmpeg` và thực hiện lại từ Bước 2

#### Bước 6: Test kết nối MySQL
Nếu chắc chắn các thông tin kết nối là đúng thì có thể bỏ qua, hoặc chỉ cần chạy migration thì có thể thấy lỗi nếu không kết nối được.
```bash
# Test kết nối từ container đến MySQL host
docker-compose exec app php -r "new PDO('mysql:host=host.docker.internal;dbname=lms_database', 'lms_user', 'your_password');"

# Nếu thành công sẽ không có lỗi
```

#### Bước 7: Cài đặt dependencies cho Laravel
```bash
# Vào container và cài đặt Composer packages
docker-compose exec app composer install --optimize-autoloader --no-dev
```

#### Bước 8: Generate key
```bash
# Tự động tạo APP_KEY trong file .env
docker-compose exec app php artisan key:generate

# Tự động tạo JWT_SECRET trong file .env
docker-compose exec app php artisan jwt:secret
```

### Bước 9: Tạo symplink
Tạo symlink để ánh xạ thư mục `public/storage` -> `storage/app/public`
```bash
docker-compose exec app php artisan storage:link
```

#### Bước 10: Chạy database migrations
```bash
# Tạo tables trong database
docker-compose exec app php artisan migrate --force

# Nếu cần chạy seeders
docker-compose exec app php artisan db:seed --force
```

#### Bước 11: Cache configurations
```bash
# Cache config để tăng performance
docker-compose exec app php artisan config:cache

# Cache routes
docker-compose exec app php artisan route:cache

# Cache views
docker-compose exec app php artisan view:cache
```

#### Bước 12: Set permissions
```bash
# Cấp quyền cho thư mục storage và cache
docker-compose exec app chown -R www-data:www-data /COURCE/storage
docker-compose exec app chown -R www-data:www-data /COURCE/bootstrap/cache
docker-compose exec app chmod -R 775 /COURCE/storage
docker-compose exec app chmod -R 775 /COURCE/bootstrap/cache
```

#### Bước 13: Restart để áp dụng changes
```bash
docker-compose restart
```

## 6️⃣ Cấu hình Nginx ngoài server

Đảm bảo domain `lms.domain.vn` đã trỏ về IP server của bạn.

### 1. Đăng ký ssl cho `lms.domain.vn`
Hành động này là bắt buộc, xem tài liệu bên ngoài

### 2. Tạo file cấu hình
Copy cấu hình nginx từ source vào thư mục cấu hình Nginx ở Host (Ubuntu server)
```bash
cp nginx-host.conf /etc/nginx/sites-available/lms.domain.vn
```

### 3. Sử lại file cấu hình
#### 3.1 Thay đổi domain truy cập qua internet.
Tìm các giá trị `lms.domain.vn` và thay thế bằng domain mà bạn cần.

#### 3.2 Sửa lại điều hướng domain internet vào port docker
```bash
# Kiểm tra port docker
docker-compose ps
```

Kết quả tương tự như sau là app trên docker đang chạy ở port `8080`:
```bash
NAME        IMAGE          COMMAND                  SERVICE   CREATED      STATUS      PORTS
lms_app     rdi_app        "docker-php-entrypoi…"   app       4 days ago   Up 4 days   9000/tcp
lms_nginx   nginx:alpine   "/docker-entrypoint.…"   nginx     4 days ago   Up 4 days   0.0.0.0:8080->80/tcp, [::]:8080->80/tcp
```

Sửa lại post tại `proxy_pass http://localhost:8080;` sao cho đúng với port vừa tìm ở trên.


### 4. Kích hoạt cấu hình

```bash
# Link sang thư mục cấu hình chính
sudo ln -s /etc/nginx/sites-available/lms.domain.vn /etc/nginx/sites-enabled/

# Kiểm tra cấu hình
sudo nginx -t

# Khởi động lại nginx để kích hoạt cấu hình mới
sudo systemctl restart nginx
```


## 7️⃣ Kiểm tra sau deploy

```bash
# Cấp quyền thực thi cho script
chmod +x verify-deployment-with-docker.sh

# Chạy deploy
./verify-deployment-with-docker.sh
```

Mở trình duyệt và truy cập: `http://lms.domain.vn` hoặc `https://lms.domain.vn`

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
cd /COURCE
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
