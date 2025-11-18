# Hướng dẫn cấu hình upload file lớn (10GB)

Dự án đã được cấu hình để hỗ trợ upload file tối đa **10GB**. Để đảm bảo cấu hình hoạt động đúng, bạn cần kiểm tra và cấu hình các thành phần sau:

## 1. Cấu hình PHP (php.ini)

Nếu file `.htaccess` không hoạt động (ví dụ: sử dụng PHP-FPM hoặc Nginx), bạn cần cấu hình trực tiếp trong file `php.ini`:

### Tìm file php.ini:
```bash
php --ini
```

### Các giá trị cần cấu hình trong php.ini:

```ini
upload_max_filesize = 10240M
post_max_size = 10240M
max_execution_time = 7200
max_input_time = 7200
memory_limit = 1024M
```

**Giải thích:**
- `upload_max_filesize`: Kích thước tối đa của một file được upload (10GB = 10240MB)
- `post_max_size`: Kích thước tối đa của dữ liệu POST (phải >= upload_max_filesize)
- `max_execution_time`: Thời gian tối đa để script chạy (7200 giây = 2 giờ)
- `max_input_time`: Thời gian tối đa để parse input (7200 giây = 2 giờ)
- `memory_limit`: Giới hạn bộ nhớ cho PHP script (1024MB = 1GB)

### Sau khi cấu hình, khởi động lại web server:
```bash
# Apache
sudo service apache2 restart
# hoặc
sudo systemctl restart apache2

# PHP-FPM
sudo service php-fpm restart
# hoặc
sudo systemctl restart php-fpm
```

## 2. Cấu hình Nginx (nếu sử dụng Nginx)

Nếu bạn sử dụng Nginx, thêm các dòng sau vào file cấu hình Nginx (thường là `/etc/nginx/sites-available/your-site`):

```nginx
client_max_body_size 10240M;
client_body_timeout 7200s;
```

Sau đó khởi động lại Nginx:
```bash
sudo nginx -t  # Kiểm tra cấu hình
sudo systemctl restart nginx
```

## 3. Cấu hình Apache (nếu sử dụng Apache)

File `.htaccess` trong thư mục `public/` đã được cấu hình. Tuy nhiên, nếu không hoạt động, bạn có thể cấu hình trực tiếp trong file cấu hình Apache:

```apache
<Directory "/path/to/your/project/public">
    php_value upload_max_filesize 10240M
    php_value post_max_size 10240M
    php_value max_execution_time 7200
    php_value max_input_time 7200
    php_value memory_limit 1024M
</Directory>
```

## 4. Kiểm tra cấu hình hiện tại

Bạn có thể kiểm tra các giá trị PHP hiện tại bằng cách tạo file `phpinfo.php`:

```php
<?php
phpinfo();
```

Truy cập `http://your-domain/phpinfo.php` và tìm các giá trị:
- `upload_max_filesize`
- `post_max_size`
- `max_execution_time`
- `max_input_time`
- `memory_limit`

**Lưu ý:** Sau khi kiểm tra xong, hãy xóa file `phpinfo.php` vì lý do bảo mật.

## 5. Validation trong Laravel

Các validation rules đã được cập nhật trong:
- `app/Http/Requests/lessons/StoreRequest.php`
- `app/Http/Requests/lessons/UpdateRequest.php`

Giới hạn validation: **10485760 KB = 10GB**

## 6. Lưu ý quan trọng

1. **Giới hạn của web server**: Đảm bảo web server (Apache/Nginx) cũng được cấu hình để chấp nhận request lớn
2. **Timeout**: Upload file lớn có thể mất nhiều thời gian, đảm bảo timeout được cấu hình đủ lớn
3. **Băng thông**: Upload file lớn yêu cầu băng thông ổn định
4. **Disk space**: Đảm bảo server có đủ dung lượng lưu trữ
5. **Memory**: Xử lý file lớn có thể tốn nhiều bộ nhớ, đảm bảo `memory_limit` đủ lớn

## 7. Troubleshooting

### Lỗi: "The file exceeds your upload_max_filesize directive"
- Kiểm tra `upload_max_filesize` trong php.ini
- Đảm bảo `post_max_size` >= `upload_max_filesize`

### Lỗi: "Request Entity Too Large" (Nginx)
- Kiểm tra `client_max_body_size` trong cấu hình Nginx

### Lỗi: "413 Request Entity Too Large" (Apache)
- Kiểm tra `LimitRequestBody` trong cấu hình Apache

### Upload bị timeout
- Tăng `max_execution_time` và `max_input_time`
- Tăng timeout của web server

