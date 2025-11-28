# HLS Signed URL Configuration

## Tổng quan

Hệ thống sử dụng cơ chế **Signed URL** để bảo vệ video HLS khỏi truy cập trái phép. Laravel chịu trách nhiệm **cấp chữ ký**, còn **Nginx** sẽ xác thực chữ ký và phục vụ file video.

## Quy trình hoạt động

```
┌─────────────┐     1. Request signature     ┌─────────────┐
│             │ ───────────────────────────► │             │
│   Client    │                              │   Laravel   │
│  (Flutter)  │ ◄─────────────────────────── │   Backend   │
│             │     2. Return signed URL     │             │
└─────────────┘                              └─────────────┘
       │
       │ 3. Request video with token
       ▼
┌─────────────┐
│             │
│    Nginx    │ ──► 4. Validate token
│   (Static)  │ ──► 5. Serve HLS files (.m3u8, .ts)
│             │
└─────────────┘
```

## Cấu hình Laravel

### 1. Thêm biến môi trường (.env)

```env
# HLS Signed URL Configuration
HLS_SECRET_KEY=your-super-secret-key-must-match-nginx
HLS_DEFAULT_EXPIRY=3600
HLS_BASE_URL=https://video.yourdomain.com/hls
HLS_RESTRICT_BY_IP=false
HLS_STORAGE_PATH=hls
```

### 2. API Endpoint

```
GET /api/lessons/{lesson}/hls-signature
Authorization: Bearer {token}
```

**Response thành công:**

```json
{
    "success": true,
    "message": "Đã tạo chữ ký thành công.",
    "data": {
        "type": "hls",
        "lesson_id": 123,
        "signed_uri": "https://video.yourdomain.com/hls/lessons/123/playlist.m3u8?signature=abc123&expires=1732800000",
        "signature": "abc123",
        "expires": 1732800000,
        "expires_at": "2025-11-28T12:00:00+00:00",
        "base_url": "https://video.yourdomain.com/hls",
        "uri": "/lessons/123/playlist.m3u8"
    }
}
```

## Cấu hình Nginx

### 1. Cài đặt module secure_link

Module `ngx_http_secure_link_module` thường được biên dịch sẵn trong Nginx. Kiểm tra:

```bash
nginx -V 2>&1 | grep -o with-http_secure_link_module
```

### 2. Cấu hình Nginx

```nginx
# /etc/nginx/sites-available/video.conf

server {
    listen 8090;
    server_name localhost;

    # ============================
    # 1) Playlist .m3u8 (BẮT BUỘC CÓ SIGNATURE)
    # ============================
    # Khớp mọi URL kết thúc bằng .m3u8 trong thư mục /storage/lesson/videos/...
    location ~ ^/storage/lesson/videos/.+\.m3u8$ {
        # Thư mục gốc Laravel public
        # => file thật sẽ là: /Users/.../backend/public/storage/lesson/videos/...
        root /Users/ducmanh/Documents/VIENPHATTRIENNGUONLUC/lms-backend/backend/public;

        # secure_link: md5(expires + uri + secret)
        secure_link $arg_signature,$arg_expires;
        secure_link_md5 "$secure_link_expires$uri your-super-secret-key-must-match-nginx";

        # BẮT BUỘC phải có md5 trong query, nếu không → 403
        if ($arg_signature = "") {
            return 403;
        }

        # Nếu md5 không khớp / hết hạn → 410 (Gone) hoặc 403 tuỳ bạn
        if ($secure_link = "") {
            return 403;
        }
        if ($secure_link = "0") {
            return 410;
        }

        # CORS (nếu cần)
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods 'GET, OPTIONS';
        add_header Access-Control-Allow-Headers 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range';
        add_header Access-Control-Expose-Headers 'Content-Length,Content-Range';

        # Cache
        add_header Cache-Control "public, max-age=31536000";

        types {
            application/vnd.apple.mpegurl m3u8;
        }
    }

    # ============================
    # 2) Segment .ts (KHÔNG CHECK SIGNATURE)
    # ============================
    # Khớp mọi URL .ts dưới /storage/lesson/videos/...
    location ~ ^/storage/lesson/videos/.+\.ts$ {
        root /Users/ducmanh/Documents/VIENPHATTRIENNGUONLUC/lms-backend/backend/public;

        # CORS
        add_header Access-Control-Allow-Origin *;
        add_header Access-Control-Allow-Methods 'GET, OPTIONS';
        add_header Access-Control-Allow-Headers 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range';
        add_header Access-Control-Expose-Headers 'Content-Length,Content-Range';

        # Cache dài cho segment
        add_header Cache-Control "public, max-age=31536000";

        types {
            video/mp2t ts;
        }
    }

    # ============================
    # 3) Health check
    # ============================
    location /health {
        return 200 'OK';
        add_header Content-Type text/plain;
    }
}
```

### 3. Cấu hình với IP restriction (tùy chọn)

Nếu bật `HLS_RESTRICT_BY_IP=true`:

```nginx
location /hls/ {
    alias /var/www/storage/app/public/hls/;

    # Format: sig(expires + uri + client_ip + secret_key)
    secure_link $arg_sig,$arg_expires;
    secure_link_sig "$arg_expires$uri$remote_addr your-super-secret-key-must-match-nginx";

    if ($secure_link = "") {
        return 403;
    }
    if ($secure_link = "0") {
        return 410;
    }

    # ... rest of config
}
```

## Cấu trúc thư mục video HLS

```
storage/app/public/hls/
└── lessons/
    └── {lesson_id}/
        ├── playlist.m3u8       # Master playlist
        ├── 720p.m3u8          # Variant playlist (720p)
        ├── 480p.m3u8          # Variant playlist (480p)
        ├── segment_0.ts       # Video segments
        ├── segment_1.ts
        └── ...
```

## Tích hợp phía Client (Flutter)

### 1. Lấy signed URL

```dart
Future<String> getSignedVideoUrl(int lessonId) async {
  final response = await dio.get(
    '/api/lessons/$lessonId/hls-signature',
    options: Options(
      headers: {'Authorization': 'Bearer $accessToken'},
    ),
  );

  if (response.data['success']) {
    return response.data['data']['signed_uri'];
  }
  throw Exception(response.data['message']);
}
```

### 2. Phát video với video_player

```dart
import 'package:video_player/video_player.dart';

class VideoPlayerWidget extends StatefulWidget {
  final int lessonId;
  
  @override
  _VideoPlayerWidgetState createState() => _VideoPlayerWidgetState();
}

class _VideoPlayerWidgetState extends State<VideoPlayerWidget> {
  VideoPlayerController? _controller;

  @override
  void initState() {
    super.initState();
    _initializePlayer();
  }

  Future<void> _initializePlayer() async {
    final signedUrl = await getSignedVideoUrl(widget.lessonId);
    
    _controller = VideoPlayerController.network(signedUrl)
      ..initialize().then((_) {
        setState(() {});
        _controller?.play();
      });
  }

  @override
  Widget build(BuildContext context) {
    return _controller?.value.isInitialized ?? false
        ? AspectRatio(
            aspectRatio: _controller!.value.aspectRatio,
            child: VideoPlayer(_controller!),
          )
        : CircularProgressIndicator();
  }

  @override
  void dispose() {
    _controller?.dispose();
    super.dispose();
  }
}
```

## Bảo mật

### Khuyến nghị

1. **Secret Key mạnh**: Sử dụng key ngẫu nhiên dài ít nhất 32 ký tự
2. **HTTPS**: Luôn sử dụng HTTPS để ngăn chặn man-in-the-middle
3. **Expiry ngắn**: Đặt thời gian hết hạn phù hợp (1-4 giờ cho video dài)
4. **IP restriction**: Bật nếu muốn ngăn chia sẻ URL
5. **Rate limiting**: Giới hạn số lần gọi API lấy signature

### Generate Secret Key

```bash
# Linux/Mac
openssl rand -hex 32

# Hoặc
php -r "echo bin2hex(random_bytes(32));"
```

## Troubleshooting

### 1. Lỗi 403 Forbidden

- Kiểm tra secret key giữa Laravel và Nginx có khớp không
- Kiểm tra format của `secure_link_sig` trong Nginx
- Kiểm tra log Nginx: `tail -f /var/log/nginx/error.log`

### 2. Lỗi 410 Gone

- URL đã hết hạn, cần lấy signature mới

### 3. Video không phát được

- Kiểm tra CORS headers
- Kiểm tra Content-Type của file .m3u8 và .ts
- Kiểm tra đường dẫn file trong Nginx alias

### 4. Debug signature

```php
// Trong Laravel Tinker
$service = app(\App\Services\HlsSignedUrlService::class);
$result = $service->generateSignedUrl(
    'https://video.example.com/hls',
    '/lessons/123/playlist.m3u8'
);
dd($result);
```

## Tài liệu tham khảo

- [Nginx ngx_http_secure_link_module](http://nginx.org/en/docs/http/ngx_http_secure_link_module.html)
- [Apple HLS Specification](https://developer.apple.com/documentation/http_live_streaming)
