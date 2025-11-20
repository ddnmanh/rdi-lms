
<div align="center">

# 🚀 Cấu hình Server Upload Multi-Chunk

### Thiết lập upload đồng thời, hiệu năng cao cho Laravel 8

[![PHP](https://img.shields.io/badge/PHP-8.0-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Nginx](https://img.shields.io/badge/Nginx-Latest-009639?style=flat&logo=nginx&logoColor=white)](https://nginx.org/)
[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![macOS](https://img.shields.io/badge/macOS-Homebrew-000000?style=flat&logo=apple&logoColor=white)](https://brew.sh/)

</div>

---

## 📋 Mục lục

- [Tổng quan](#-tổng-quan)
- [Yêu cầu hệ thống](#-yêu-cầu-hệ-thống)
- [Cấu hình PHP-FPM](#-phần-i-cấu-hình-php-fpm)
- [Cấu hình Nginx](#-phần-ii-cấu-hình-nginx)
- [Kiểm tra & xác nhận](#-kiểm-tra--xác-nhận)
- [Xử lý sự cố](#-xử-lý-sự-cố)

---

## 🎯 Tổng quan

Tài liệu này hướng dẫn chi tiết cách cấu hình hệ thống để xử lý **upload file dạng multi-chunk đồng thời (concurrent)** trên macOS với:

- **Web Server:** Nginx
- **PHP Processor:** PHP-FPM 8.0
- **Framework:** Laravel 8.x
- **Package Manager:** Homebrew

**Tính năng chính:**
- ✅ Hỗ trợ upload nhiều chunk song song
- ✅ Hỗ trợ file dung lượng lớn (tới 2.5GB)
- ✅ Tối ưu quản lý tiến trình (process management)
- ✅ Tăng timeout cho các tác vụ upload lâu

---

## 💻 Yêu cầu hệ thống

| Thành phần | Phiên bản | Mục đích |
|-----------|-----------|----------|
| macOS | 10.15+ | Hệ điều hành |
| Homebrew | Mới nhất | Trình quản lý gói |
| PHP | 8.0.x | Môi trường chạy PHP |
| Nginx | 1.x | Web Server |
| Laravel | 8.x | Framework ứng dụng |

---

## 🐘 Phần I: Cấu hình PHP-FPM

### Bước 1: Cài đặt PHP 8.0

Cài đặt PHP 8.0 để tương thích với Laravel 8:

```bash
brew install php@8.0
```

**Kiểm tra lại cài đặt:**

```bash
php -v
```

<details>
<summary>Output mong đợi</summary>

```
PHP 8.0.x (cli) (built: ...)
Copyright (c) The PHP Group
Zend Engine v4.0.x
```

</details>

---

### Bước 2: Cấu hình PHP

#### 2.1 Xác định file `php.ini`

```bash
php --ini
```

#### 2.2 Chỉnh sửa cấu hình

Mở file cấu hình trong editor và thêm/sửa các thông số sau:

```ini
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 📦 CẤU HÌNH UPLOAD
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

; Giới hạn dung lượng tối đa cho mỗi file/chunk upload
upload_max_filesize = 256M

; Giới hạn tổng dung lượng dữ liệu POST (phải >= upload_max_filesize)
post_max_size = 256M

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# ⏱️ CẤU HÌNH TIMEOUT
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

; Thời gian tối đa script được phép chạy (2 giờ)
max_execution_time = 7200

; Thời gian tối đa để parse input (2 giờ)
max_input_time = 7200

# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🧠 CẤU HÌNH BỘ NHỚ
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

; Mức RAM tối đa cho mỗi script PHP
memory_limit = 1024M
```

> **💡 Gợi ý:** Trên môi trường production, hãy điều chỉnh các giá trị này phù hợp với tài nguyên server và kích thước file thực tế.

---

### Bước 3: Kiểm tra service PHP-FPM

Kiểm tra các phiên bản PHP đã cài:

```bash
brew list | grep php
```

Xem trạng thái service:

```bash
brew services list
```

>⚠️ **Lưu ý:** Nếu `php@8.0` chưa có trạng thái `started`, hãy khởi động nó:

```bash
brew services start php@8.0
``` 

---

### Bước 4: Xác định địa chỉ lắng nghe (Listen Address)

Xem PHP-FPM đang lắng nghe qua Socket hay TCP:

```bash
cat /opt/homebrew/etc/php/8.0/php-fpm.d/www.conf | grep ^listen
```

**Kết quả có thể là:**

| Loại | Cấu hình | Ghi chú |
|------|----------|--------|
| **Unix Socket** | `listen = /opt/homebrew/var/run/php-fpm.sock` | Hiệu năng tốt hơn |
| **TCP Port** | `listen = 127.0.0.1:9000` | Dễ truy cập qua mạng |

> **⚠️ Lưu ý:** Ghi nhớ giá trị này để dùng ở phần cấu hình Nginx (Phần II).

---

### Bước 5: Tối ưu Process Manager

Chỉnh sửa file cấu hình pool của PHP-FPM (Mở bằng VS Code):

```bash
code /opt/homebrew/etc/php/8.0/php-fpm.d/www.conf
```

**Cập nhật các giá trị sau:**

```ini
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
# 🔄 CẤU HÌNH PROCESS MANAGER
# ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

; Chế độ quản lý process: dynamic (tự co giãn)
pm = dynamic

; Số process con tối đa (tùy thuộc RAM)
pm.max_children = 20

; Số process khởi tạo ban đầu
pm.start_servers = 5

; Số process nhàn rỗi tối thiểu
pm.min_spare_servers = 3

; Số process nhàn rỗi tối đa
pm.max_spare_servers = 10
```

**Restart PHP-FPM để áp dụng cấu hình:**

```bash
brew services restart php@8.0
```

---

## 🌐 Phần II: Cấu hình Nginx

### Bước 1: Cài đặt Nginx

```bash
brew install nginx
```

---

### Bước 2: Cấu hình Virtual Host

Mở file cấu hình Nginx:

```bash
code /opt/homebrew/etc/nginx/nginx.conf
```

**Thêm/thay thế block `server` như sau:**

```nginx
server {
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 🌍 THÔNG TIN CƠ BẢN CỦA SERVER
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
    listen 8000;
    server_name localhost;
    
    # ⚠️ QUAN TRỌNG: Cập nhật đường dẫn này trỏ tới thư mục public của dự án Laravel
    root /Users/ducmanh/Documents/VIENPHATTRIENNGUONLUC/lms-2/backend/public;
    
    index index.php index.html;
    
    # Giới hạn dung lượng upload tối đa (256MB cho file lớn)
    client_max_body_size 256M;
    
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 📁 ĐỊNH TUYẾN (ROUTING)
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 🐘 XỬ LÝ PHP
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
    location ~ \.php$ {
        # ⚠️ QUAN TRỌNG: Cấu hình này phải khớp với địa chỉ PHP-FPM ở Phần I, Bước 4
        # Tùy chọn 1 (TCP): fastcgi_pass 127.0.0.1:9000;
        # Tùy chọn 2 (Socket): fastcgi_pass unix:/opt/homebrew/var/run/php-fpm.sock;
        fastcgi_pass   127.0.0.1:9000;
        
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include        fastcgi_params;
        
        # Tăng timeout để xử lý file lớn, upload lâu
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
    }
    
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    # 🔒 BẢO MẬT CƠ BẢN
    # ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    
    # Chặn truy cập các file/ thư mục ẩn (bắt đầu bằng .)
    location ~ /\. {
        deny all;
    }
}
```

> **📝 Checklist cấu hình:**
> - [ ] Cập nhật `root` đúng đường dẫn project
> - [ ] Đảm bảo `fastcgi_pass` trùng với cấu hình PHP-FPM
> - [ ] Điều chỉnh `client_max_body_size` nếu cần

---

### Bước 3: Kiểm tra & áp dụng cấu hình

**Kiểm tra cú pháp Nginx:**

```bash
nginx -t
```

<details>
<summary>✅ Output mong đợi</summary>

```
nginx: the configuration file /opt/homebrew/etc/nginx/nginx.conf syntax is ok
nginx: configuration file /opt/homebrew/etc/nginx/nginx.conf test is successful
```

</details>

**Khởi động lại Nginx:**

```bash
brew services restart nginx
```

---

## ✅ Kiểm tra & xác nhận

### Kiểm tra nhanh tình trạng hệ thống

```bash
# Kiểm tra trạng thái PHP-FPM
brew services list | grep php

# Kiểm tra trạng thái Nginx
brew services list | grep nginx

# Kiểm tra lại cấu hình Nginx
nginx -t

# Xem nhanh các thông số cấu hình PHP quan trọng
php -i | grep -E '(upload_max_filesize|post_max_size|memory_limit)'
```

### Test chức năng upload

Truy cập ứng dụng Laravel tại `http://localhost:8000` và thực hiện upload thử với file dung lượng lớn hoặc upload nhiều file cùng lúc để kiểm tra.

---

## 🔧 Xử lý sự cố

<details>
<summary><strong>Lỗi: 502 Bad Gateway</strong></summary>

**Nguyên nhân có thể:**
- PHP-FPM không chạy
- Cấu hình `fastcgi_pass` sai
- Quyền truy cập (permission) không đúng

**Cách xử lý:**
```bash
# Restart PHP-FPM
brew services restart php@8.0

# Kiểm tra log lỗi
tail -f /opt/homebrew/var/log/nginx/error.log
tail -f /opt/homebrew/var/log/php-fpm.log
```

</details>

<details>
<summary><strong>Lỗi: Upload thất bại với file lớn</strong></summary>

**Kiểm tra lại cấu hình:**
```bash
# Kiểm tra các thông số PHP
php -i | grep -E '(upload_max_filesize|post_max_size|max_execution_time)'

# Kiểm tra cấu hình Nginx
grep client_max_body_size /opt/homebrew/etc/nginx/nginx.conf
```

**Đảm bảo tất cả giới hạn dung lượng đồng bộ và đủ lớn.**

</details>

<details>
<summary><strong>Lỗi: Timeout khi upload</strong></summary>

**Cần tăng thêm timeout tại:**
- `php.ini`: `max_execution_time`, `max_input_time`
- `nginx.conf`: `fastcgi_read_timeout`, `fastcgi_send_timeout`
- `www.conf`: `request_terminate_timeout`

</details>

---

<div align="center">

## 🎉 Hoàn tất!

Server của bạn đã được cấu hình để xử lý upload file dạng multi-chunk đồng thời với hiệu năng cao.

**Bước tiếp theo gợi ý:**
- Thử nghiệm với các kịch bản upload thực tế
- Theo dõi log và hiệu năng hệ thống
- Tinh chỉnh thêm cấu hình dựa trên tải thực tế

---

### 📚 Tài liệu tham khảo

[Tài liệu PHP-FPM](https://www.php.net/manual/en/install.fpm.php) • [Tài liệu Nginx](https://nginx.org/en/docs/) • [Laravel File Upload](https://laravel.com/docs/8.x/filesystem)


</div>
