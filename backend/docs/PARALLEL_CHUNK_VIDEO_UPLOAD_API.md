
<div align="center">

# 🎬 API Upload Video Background Multi-Chunk

### Tài liệu API xử lý upload video bất đồng bộ cho hệ thống LMS

[![Laravel](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.0-777BB4?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![Queue](https://img.shields.io/badge/Queue-Redis-DC382D?style=flat&logo=redis&logoColor=white)](https://redis.io/)

</div>

---

## 📋 Mục lục

- [Tổng quan](#-tổng-quan)
- [Luồng tổng quát](#-luồng-tổng-quát)
- [Bảng trạng thái phiên](#-bảng-trạng-thái-phiên)
- [API chi tiết](#-api-chi-tiết)
- [Hậu trường Job](#-hậu-trường-job-processlessionvideoupload)
- [Tương tác với Form](#-tương-tác-với-form-bài-học)
- [Checklist triển khai](#-checklist-triển-khai)

---

## 🎯 Tổng quan

Tính năng **Upload nền video bài học** cho phép quản trị viên tải lên tệp video lớn (≤10 GB) mà không khóa trình duyệt.

**Kiến trúc hệ thống:**
- ✅ Upload bất đồng bộ với chunk 50MB (mặc định)
- ✅ Xử lý background job với queue worker
- ✅ Hỗ trợ theo dõi tiến trình realtime
- ✅ Tự động ghép file và cập nhật bài học

**Thông tin kỹ thuật:**
- **API Prefix:** `/api/lessons/video-uploads`
- **Bảng Database:** `lesson_video_uploads`
- **Background Job:** `ProcessLessonVideoUpload`
- **Giới hạn file:** 10 GB
- **Chunk size:** 50 MB (có thể tùy chỉnh 256KB–500MB)

---

## 🔄 Luồng tổng quát

```
┌─────────────┐  1. Chọn video & tạo session  ┌─────────────┐
│             │ ──────────────────────────────►│             │
│   Browser   │                                │   Laravel   │
│  (Admin UI) │ ◄──────────────────────────────│   Backend   │
│             │  2. Return upload_id + config  │             │
└─────────────┘                                └─────────────┘
       │
       │ 3. Upload chunks (1→N)
       │    POST /chunks with binary data
       ▼
┌─────────────┐
│             │ ──► 4. Save chunks to temp/
│   Laravel   │ ──► 5. Track progress
│  API Server │ ──► 6. Update uploaded_chunks
│             │
└─────────────┘
       │
       │ 7. Complete request (lesson_id)
       ▼
┌─────────────┐
│             │ ──► 8. Dispatch background job
│ Queue Worker│ ──► 9. Merge all chunks
│ (Background)│ ──► 10. Upload to storage/public
│             │ ──► 11. Update lesson.video_path
│             │ ──► 12. Cleanup temp files
└─────────────┘
       │
       │ 13. Poll status every 5s
       ▼
┌─────────────┐
│             │ ──► Status: processing → completed
│   Browser   │ ──► Display badge & allow navigation
│  (Admin UI) │
│             │
└─────────────┘
```

### Giai đoạn 1: Tạo sesion upload

Người dùng chọn video → JavaScript gọi `POST /lessons/video-uploads/sessions`

**Response:**
```json
{
  "upload_id": "uuid-string",
  "chunk_size": 52428800, // Đơn vị byte
  "total_chunks": 20,
  "status": "pending"
}
```

---

### Giai đoạn 2: Upload từng chunk

Trình duyệt cắt file thành chunk có độ dài được chỉ định ở phản hồi của "Giai đoạn 1" và gửi tuần tự qua `POST /lessons/video-uploads/{upload}/chunks`

**Progress tracking:**
- Chunk 1/20 → 5%
- Chunk 10/20 → 50%
- Chunk 20/20 → 100% (status chuyển sang `uploaded`)

---

### Giai đoạn 3: Lưu bài học & yêu cầu ghép

Người dùng nhấn **Lưu** bài học:

1. Form gửi `video_path = background-upload://{upload_id}`
2. Bài học được lưu vào database
3. JavaScript gọi `POST /lessons/video-uploads/{upload}/complete` kèm `lesson_id`
4. Job `ProcessLessonVideoUpload` được đưa vào queue

---

### Giai đoạn 4: Xử lý background

Job `ProcessLessonVideoUpload` thực hiện:

- ✅ Gộp tất cả chunk thành file hoàn chỉnh
- ✅ Đẩy lên storage disk `public`
- ✅ Cập nhật `lessons.video_path` với URL công khai
- ✅ Xóa thư mục tạm
- ✅ Set status `completed` hoặc `failed`

---

### Giai đoạn 5: Theo dõi & hoàn tất

UI poll `GET /lessons/video-uploads/{upload}` mỗi 5 giây để hiển thị badge:

- 🔄 **Processing:** Đang ghép video
- ✅ **Completed:** Đã hoàn tất
- ❌ **Failed:** Lỗi xảy ra

---

## 📊 Bảng trạng thái phiên

| Trạng thái | Icon | Diễn giải | Ai thiết lập |
|-----------|------|-----------|--------------|
| `pending` | ⏳ | Mới khởi tạo, chưa nhận chunk | Controller khi tạo session |
| `uploading` | ⬆️ | Đang nhận chunk từ client | Controller sau chunk đầu tiên |
| `processing` | 🔄 | Đã nhận đủ chunk, job đang ghép file | Method `complete()` và job |
| `completed` | ✅ | Video đã ghép xong, URL đã cập nhật vào lesson | Background job |
| `failed` | ❌ | Lỗi upload/ghép hoặc người dùng hủy | Controller/Job khi có lỗi |

---

## 🚀 API chi tiết

### 1️⃣ Tạo phiên upload

```http
POST /api/lessons/video-uploads/sessions
```

#### Request Body

```json
{
  "file_name": "lesson-01.mp4",
  "file_size": 1073741824,
  "chunk_size": 52428800,
  "mime_type": "video/mp4",
  "lesson_id": 123
}
```

| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `file_name` | string | ✅ | Tên file video |
| `file_size` | int | ✅ | Dung lượng file (≤10GB) |
| `chunk_size` | int | ❌ | Kích thước chunk (256KB–500MB) |
| `mime_type` | string | ❌ | MIME type của video |
| `lesson_id` | int | ❌ | ID bài học (nếu đã biết) |

#### Response (200/201)

```json
{
  "upload_id": "9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f",
  "chunk_size": 52428800,
  "total_chunks": 20,
  "status": "pending",
  "temp_directory": "temp/lesson_video_uploads/9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f"
}
```

> **📝 Ghi chú:** Endpoint này được gọi ngay khi người dùng chọn video trong form upload nền.

---

### 2️⃣ Upload chunk

```http
POST /api/lessons/video-uploads/{upload}/chunks
Content-Type: multipart/form-data
```

#### Request Form Data

| Field | Kiểu | Bắt buộc | Mô tả |
|-------|------|----------|-------|
| `chunk_index` | int | ✅ | Số thứ tự chunk (bắt đầu từ 1) |
| `chunk` | file | ✅ | Dữ liệu chunk (binary) |

#### Response (200)

```json
{
  "message": "Chunk uploaded successfully",
  "uploaded_chunks": 15,
  "total_chunks": 20,
  "progress": 75,
  "status": "uploading"
}
```

> **⚠️ Lưu ý:**
> - Từ chối nếu upload đã chuyển sang trạng thái `processing/completed/failed`
> - Tự động tăng `uploaded_chunks` và cập nhật status thành `uploading`

---

### 3️⃣ Hoàn tất & yêu cầu xử lý

```http
POST /api/lessons/video-uploads/{upload}/complete
```

#### Request Body

```json
{
  "lesson_id": 123
}
```

| Tham số | Kiểu | Bắt buộc | Mô tả |
|---------|------|----------|-------|
| `lesson_id` | int | ✅* | ID bài học (*nếu chưa gán ở session) |

#### Response (200)

```json
{
  "message": "Yêu cầu ghép file đã được đưa vào hàng đợi",
  "upload_id": "9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f",
  "status": "processing",
  "lesson_id": 123
}
```

**Xử lý:**
- ✅ Kiểm tra đủ chunk → set status `processing`
- ✅ Dispatch job `ProcessLessonVideoUpload` vào queue
- ❌ Nếu job đang chạy → return message "Video đang được xử lý"

---

### 4️⃣ Theo dõi trạng thái

```http
GET /api/lessons/video-uploads/{upload}
```

#### Response (200)

```json
{
  "upload_id": "9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f",
  "file_name": "lesson-01.mp4",
  "file_size": 1073741824,
  "uploaded_chunks": 20,
  "total_chunks": 20,
  "progress": 100,
  "status": "completed",
  "storage_path": "lesson/videos/2025/11/lesson-01_9c8f7e6d.mp4",
  "lesson_id": 123,
  "error_message": null,
  "created_at": "2025-11-22T10:30:00Z",
  "updated_at": "2025-11-22T10:45:00Z"
}
```

> **💡 Sử dụng:** Form sử dụng endpoint này để cập nhật badge realtime thông qua polling.

---

### 5️⃣ Hủy upload

```http
DELETE /api/lessons/video-uploads/{upload}
```

#### Response (200)

```json
{
  "message": "Upload đã được hủy thành công",
  "upload_id": "9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f"
}
```

**Xử lý:**
- 🗑️ Xóa thư mục chunk tạm
- ❌ Set status `failed`
- 📝 Ghi `error_message = 'Người dùng hủy upload.'`

> **📝 Ghi chú:** UI gọi endpoint này khi admin bấm nút "Hủy upload".

---

## ⚙️ Hậu trường Job `ProcessLessonVideoUpload`

### Quy trình xử lý

```php
ProcessLessonVideoUpload::dispatch($upload_id)
```

#### Bước 1: Hợp nhất chunks

```
temp/lesson_video_uploads/{upload_id}/
├── 1.part
├── 2.part
├── 3.part
└── ...
```

→ Ghép thành file tạm `.merged`

---

#### Bước 2: Lưu vào storage

```php
Storage::disk('public')->putFileAs(
    'lesson/videos/2025/11',
    $mergedFile,
    'lesson-01_9c8f7e6d.mp4'
);
```

**Đường dẫn cuối cùng:**
```
storage/app/public/lesson/videos/2025/11/lesson-01_9c8f7e6d.mp4
```

---

#### Bước 3: Cập nhật database

```php
Lesson::find($lesson_id)->update([
    'video_path' => Storage::disk('public')->url($path)
]);
```

**URL công khai:**
```
http://localhost:8000/storage/lesson/videos/2025/11/lesson-01_9c8f7e6d.mp4
```

---

#### Bước 4: Dọn dẹp

- 🗑️ Xóa thư mục tạm `temp/lesson_video_uploads/{upload_id}`
- 🗑️ Xóa file `.merged`
- ✅ Set `lesson_video_uploads.status = 'completed'`

> **⚠️ Quan trọng:** Việc dọn dẹp được thực hiện bất kể job thành công hay thất bại.

---

## 🎨 Tương tác với Form bài học

### Placeholder Path Pattern

Khi lưu bài học ở chế độ upload nền:

```
video_path = background-upload://{upload_id}
```

**Ví dụ:**
```
background-upload://9c8f7e6d-5b4a-3c2d-1e0f-9a8b7c6d5e4f
```

> **📝 Ghi chú:** Backend lesson controller nhận giá trị này bình thường. Khi job hoàn tất, nó sẽ tự động được thay thế bằng URL thật.

---

### Cảnh báo đóng tab

JavaScript chặn sự kiện `beforeunload` khi:
- ✅ Trạng thái = `creating_session`
- ✅ Trạng thái = `uploading`

```javascript
window.addEventListener('beforeunload', (e) => {
    if (['creating_session', 'uploading'].includes(uploadState)) {
        e.preventDefault();
        e.returnValue = 'Upload đang tiến hành. Bạn có chắc muốn rời khỏi trang?';
    }
});
```

> **💡 Mục đích:** Tránh người dùng vô tình đóng tab và mất tiến trình upload.

---

### Retry khi lỗi

Nếu upload lỗi, người dùng nhấn nút **"Thử lại"**:

```javascript
retryButton.addEventListener('click', () => {
    startBackgroundUploadFlow(selectedFile);
});
```

**Quy trình:**
1. Tạo lại session mới
2. Upload lại từ chunk đầu tiên
3. Trạng thái quay về `uploading`

---

### Processing Badge

Sau khi gọi `complete()`, UI bắt đầu polling:

```javascript
const pollInterval = setInterval(() => {
    fetch(`/api/lessons/video-uploads/${uploadId}`)
        .then(res => res.json())
        .then(data => {
            updateBadge(data.status);
            
            if (data.status === 'completed') {
                clearInterval(pollInterval);
                showSuccessMessage();
            }
        });
}, 5000); // Poll mỗi 5 giây
```

**Badge states:**
- 🔄 **processing:** "Đang ghép video..."
- ✅ **completed:** "Hoàn tất" (cho phép rời trang)
- ❌ **failed:** "Lỗi xảy ra"

---

## ✅ Checklist triển khai

### Yêu cầu hệ thống

- [ ] Queue worker đang chạy (`php artisan queue:work`)
- [ ] Disk `public` đã được symlink (`php artisan storage:link`)
- [ ] Migration `lesson_video_uploads` đã được migrate
- [ ] Redis/Database queue driver được cấu hình đúng

### Kiểm tra cấu hình

```bash
# Kiểm tra queue worker
ps aux | grep "queue:work"

# Kiểm tra symlink storage
ls -la public/storage

# Kiểm tra migration
php artisan migrate:status | grep lesson_video_uploads

# Test queue connection
php artisan queue:failed
```

### Quyền truy cập

```bash
# Đảm bảo Laravel có quyền ghi vào storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Kiểm tra owner
ls -la storage/app
```

---

<div align="center">

### 🎉 Hoàn tất!

Hệ thống upload video background multi-chunk của bạn đã sẵn sàng xử lý file lớn một cách hiệu quả.

---

### 📚 Tài liệu liên quan

[Cấu hình Server](PARALLEL_CHUNK_VIDEO_UPLOAD_CONFIG.md) • [Laravel Queue](https://laravel.com/docs/8.x/queues) • [Laravel Storage](https://laravel.com/docs/8.x/filesystem)

</div>
