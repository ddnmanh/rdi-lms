## Tổng quan
- Tính năng **Upload nền video bài học** cho phép quản trị viên tải lên tệp lớn (≤10 GB) mà không khóa trình duyệt.
- Luồng gồm 3 giai đoạn: tạo phiên chunk, đẩy từng chunk, yêu cầu ghép & cập nhật bài học.
- Bộ API nằm dưới prefix `/api/lesson-video-uploads`. Mỗi phiên được ghi trong bảng `lesson_video_uploads` và được job `ProcessLessonVideoUpload` xử lý bất đồng bộ.

## Dòng chảy tổng quát
1. **Chọn chế độ upload nền trong form bài học** (`resources/views/admin/lessons/form.blade.php`).
2. Người dùng chọn video → JS gọi `POST /lesson-video-uploads/sessions` để lấy `upload_id`, `chunk_size`, `total_chunks`.
3. Trình duyệt cắt file thành chunk 50 MB (mặc định) và gửi tuần tự qua `POST /lesson-video-uploads/{upload}/chunks`.
4. Khi đủ chunk, trạng thái chuyển sang `uploaded`. Người dùng nhấn **Lưu** bài học:
   - Form gửi `video_path = background-upload://{upload_id}`.
   - Sau khi bài học lưu, JS gọi `POST /lesson-video-uploads/{upload}/complete` kèm `lesson_id`.
5. Job `ProcessLessonVideoUpload` được enqueue:
   - Gộp file tạm, đẩy lên disk `public`, cập nhật `lessons.video_path` với URL công khai.
   - Xóa thư mục tạm, set status `completed` hoặc `failed`.
6. UI tiếp tục poll `GET /lesson-video-uploads/{upload}` để hiển thị badge “Đang ghép video / Đã hoàn tất”.

## Bảng trạng thái phiên
| Trạng thái | Diễn giải | Ai thiết lập |
| --- | --- | --- |
| `pending` | Mới khởi tạo, chưa nhận chunk | Controller khi tạo |
| `uploading` | Đang nhận chunk | Controller sau chunk đầu tiên |
| `processing` | Đã nhận đủ chunk, job ghép đang chạy | `complete()` và job |
| `completed` | Video đã ghép, trường `storage_path` + `lesson.video_path` đã cập nhật | Job |
| `failed` | Lỗi upload/ghép hoặc người dùng hủy | Controller/job |

## API chi tiết

### 1. Tạo phiên upload
```
POST /api/lesson-video-uploads/sessions
```
- **Body**: `file_name` (string, bắt buộc), `file_size` (int, ≤10GB), `chunk_size` (tùy chọn 256KB–500MB), `mime_type`, `lesson_id` (nếu đã biết).
- **200/201**: `{ upload_id, chunk_size, total_chunks, status, temp_directory }`.
- Được gọi ngay khi người dùng chọn video trong form nền.

### 2. Upload chunk
```
POST /api/lesson-video-uploads/{upload}/chunks
FormData: chunk_index (>=1), chunk (file)
```
- Từ chối nếu upload chuyển sang `processing/completed/failed`.
- Tự động tăng `uploaded_chunks`, cập nhật status `uploading`.
- Response: tiến độ hiện tại (tỉ lệ %).

### 3. Hoàn tất & yêu cầu xử lý
```
POST /api/lesson-video-uploads/{upload}/complete
Body: lesson_id (bắt buộc nếu chưa gán)
```
- Kiểm tra đủ chunk → set `processing`, dispatch `ProcessLessonVideoUpload`.
- Response 200: thông điệp “Yêu cầu ghép file đã được đưa vào hàng đợi” + payload trạng thái hiện tại.
- Nếu gọi khi job đang chạy sẽ trả về message “Video đang được xử lý”.

### 4. Theo dõi trạng thái
```
GET /api/lesson-video-uploads/{upload}
```
- Trả về đầy đủ metadata (progress %, error message, mốc thời gian). Form sử dụng để cập nhật badge.

### 5. Hủy upload
```
DELETE /api/lesson-video-uploads/{upload}
```
- Xóa thư mục chunk tạm, set status `failed`, `error_message = 'Người dùng hủy upload.'`.
- UI gọi khi admin bấm “Hủy upload”.

## Hậu trường job `ProcessLessonVideoUpload`
- Mỗi job nhận `upload_id`.
- Hợp nhất từng file `*.part` theo tên tăng dần → file tạm `.merged`.
- Lưu file cuối cùng vào `storage/app/public/lesson/videos/...` và phát sinh URL bằng `Storage::disk($disk)->url()`.
- Cập nhật `lessons.video_path` nếu `lesson_id` tồn tại.
- Xóa dữ liệu tạm bất kể thành công/thất bại.

## Tương tác với form bài học
- **Placeholder path**: Khi lưu bài học ở chế độ nền, `video_path` tạm thời set `background-upload://{upload_id}`. Backend lesson controller nhận giá trị này bình thường; khi job xong nó sẽ tự đẩy URL thật.
- **Cảnh báo đóng tab**: `beforeunload` bị chặn khi trạng thái là `creating_session` hoặc `uploading` để tránh mất tiến trình.
- **Retry**: nếu upload lỗi, người dùng nhấn “Thử lại” → JS gọi lại `startBackgroundUploadFlow()` trên file đã chọn (trạng thái về `uploading`).
- **Processing badge**: Sau khi gọi `complete`, UI poll `show()` mỗi 5 s để đổi badge `processing` → `completed` và cho phép admin rời trang.

## Checklist triển khai
- Queue worker phải chạy (`php artisan queue:work`).
- Disk `public` cần link `storage:link` để lesson hiển thị video.
- Bảo đảm `lesson_video_uploads` migration được migrate trước khi bật tính năng.

