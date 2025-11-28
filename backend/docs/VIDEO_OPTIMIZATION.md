# Video Optimization for Streaming

## Tổng quan

Hệ thống đã được cập nhật để tự động chuẩn hóa codec của video khi upload, đảm bảo video có thể stream tốt trên client.

## Quy trình xử lý video

Khi video được upload qua background upload (chunk upload), hệ thống sẽ:

1. **Merge chunks**: Ghép các chunks thành một file video hoàn chỉnh
2. **Optimize video**: Chuẩn hóa codec bằng ffmpeg với các tham số:
   - Video codec: H.264 (libx264)
   - Audio codec: AAC
   - Flag: +faststart (cho phép progressive download/streaming)
3. **Store video**: Lưu video đã optimize vào storage
4. **Update lesson**: Cập nhật thông tin video và duration cho lesson

## Cài đặt ffmpeg

### macOS
```bash
brew install ffmpeg
```

### Ubuntu/Debian
```bash
sudo apt update
sudo apt install ffmpeg
```

### CentOS/RHEL
```bash
sudo yum install epel-release
sudo yum install ffmpeg
```

### Docker
Nếu chạy trong Docker, thêm vào Dockerfile:
```dockerfile
RUN apt-get update && apt-get install -y ffmpeg
```

## Cấu hình

Thêm vào file `.env`:

```env
# Path to ffmpeg binary (mặc định là 'ffmpeg' nếu đã có trong PATH)
FFMPEG_PATH=ffmpeg
```

Nếu ffmpeg không có trong PATH, chỉ định đường dẫn đầy đủ:
```env
FFMPEG_PATH=/usr/local/bin/ffmpeg
```

## Kiểm tra ffmpeg

Kiểm tra xem ffmpeg đã được cài đặt chưa:
```bash
ffmpeg -version
```

## Lệnh optimize thủ công

Nếu cần optimize video thủ công, sử dụng lệnh:
```bash
ffmpeg -i input.mp4 -c:v libx264 -c:a aac -movflags +faststart output.mp4
```

### Giải thích các tham số:
- `-i input.mp4`: File input
- `-c:v libx264`: Sử dụng H.264 codec cho video
- `-c:a aac`: Sử dụng AAC codec cho audio
- `-movflags +faststart`: Di chuyển metadata lên đầu file, cho phép streaming progressive
- `output.mp4`: File output

## Lợi ích của video optimization

1. **Tương thích tốt hơn**: H.264/AAC được hỗ trợ rộng rãi trên tất cả browser và thiết bị
2. **Streaming tốt hơn**: Flag `faststart` cho phép video bắt đầu play ngay mà không cần download toàn bộ
3. **Chất lượng ổn định**: Codec chuẩn đảm bảo chất lượng video nhất quán
4. **Tối ưu dung lượng**: H.264 cân bằng tốt giữa chất lượng và kích thước file

## Logging

Quá trình optimization được log chi tiết:
- Khi bắt đầu optimize: Log command và file paths
- Khi hoàn thành: Log kích thước file output
- Khi lỗi: Log error message từ ffmpeg

Kiểm tra log tại: `storage/logs/laravel.log`

## Troubleshooting

### Lỗi: "ffmpeg: command not found"
- Chưa cài đặt ffmpeg hoặc không có trong PATH
- Giải pháp: Cài đặt ffmpeg hoặc chỉ định đường dẫn đầy đủ trong FFMPEG_PATH

### Lỗi: "Không thể tối ưu hóa video"
- Kiểm tra log để xem chi tiết lỗi từ ffmpeg
- Video input có thể bị corrupt
- Định dạng video không được hỗ trợ

### Video xử lý chậm
- Quá trình optimize video có thể mất thời gian tùy thuộc vào kích thước và độ phân giải
- Đảm bảo queue worker đang chạy: `php artisan queue:work`
- Có thể tăng timeout cho queue nếu cần

## Performance

Thời gian xử lý phụ thuộc vào:
- Kích thước video
- Độ phân giải (720p, 1080p, 4K...)
- CPU server
- Codec gốc của video

Ước tính: Video 100MB, 1080p thường mất khoảng 1-3 phút để optimize trên server thông thường.
