<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Services\HlsSignedUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LessonVideoStreamController extends Controller
{
    protected HlsSignedUrlService $hlsSignedUrlService;

    public function __construct(HlsSignedUrlService $hlsSignedUrlService)
    {
        $this->hlsSignedUrlService = $hlsSignedUrlService;
    }
    /**
     * Stream lesson video with HTTP range support so the Flutter app
     * can play without downloading the entire file.
     */
    public function stream(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // 1. Kiểm tra tồn tại video
        if (!$lesson->video_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video.'
            ], 404);
        }

        // 2. Video đang được xử lý nền
        if (Str::startsWith($lesson->video_path, 'background-upload://')) {
            return response()->json([
                'success' => false,
                'message' => 'Video đang được xử lý. Vui lòng thử lại sau.'
            ], 409);
        }

        // 3. Phân quyền: chỉ root hoặc user thuộc khóa học mới xem được
        if (
            !$user->isRoot()
            && (
                $lesson->course_id === null
                || !$user->courses()->where('courses.id', $lesson->course_id)->exists()
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập video này.'
            ], 403);
        }

        $videoPath = $lesson->video_path;

        // 4. Nếu là URL ngoài thì redirect luôn (ví dụ Youtube, S3 public, v.v.)
        if ($this->isExternalUrl($videoPath)) {
            return redirect($videoPath);
        }

        // 5. Chuẩn hóa đường dẫn nội bộ
        //    - Nếu lưu kiểu "/storage/lesson/videos/xxx.mp4"
        //    - Hoặc lưu kiểu "lesson/videos/xxx.mp4"
        if (Str::startsWith($videoPath, '/storage/')) {
            $relativePath = ltrim(Str::after($videoPath, '/storage/'), '/');
        } else {
            $relativePath = ltrim($videoPath, '/');
        }

        $fullPath = storage_path('app/public/' . $relativePath);

        if (!File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy file video.'
            ], 404);
        }

        // 6. Thông tin file + Range
        $fileSize    = File::size($fullPath);
        $mimeType    = File::mimeType($fullPath) ?: 'video/mp4';
        $rangeHeader = $request->header('Range');

        [$start, $end, $status] = $this->resolveRange($rangeHeader, $fileSize);
        $length = $end - $start + 1;

        // 7. Header trả về
        $headers = [
            'Content-Type'   => $mimeType,
            'Content-Length' => $length,
            'Accept-Ranges'  => 'bytes',
            // Tùy nhu cầu có thể cho cache lâu
            // 'Cache-Control'  => 'public, max-age=31536000',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$fileSize}";
        }

        // 8. Stream từng chunk để không ăn nhiều RAM
        $streamCallback = function () use ($fullPath, $start, $length) {
            $chunkSize      = 1024 * 1024; // 1MB
            $bytesRemaining = $length;

            // Đảm bảo không bị timeout giữa chừng khi stream file lớn
            @set_time_limit(0);

            // Dọn các output buffer cũ (nếu có)
            if (function_exists('ob_get_level')) {
                while (ob_get_level() > 0) {
                    @ob_end_clean();
                }
            }

            $handle = fopen($fullPath, 'rb');

            if ($handle === false) {
                return;
            }

            // Nhảy tới vị trí bắt đầu
            fseek($handle, $start);

            while ($bytesRemaining > 0 && !feof($handle)) {
                $readLength = min($chunkSize, $bytesRemaining);
                $buffer     = fread($handle, $readLength);

                echo $buffer;
                flush();

                $bytesRemaining -= strlen($buffer);

                // Nếu client đóng kết nối thì dừng luôn
                if (connection_status() != CONNECTION_NORMAL) {
                    break;
                }
            }

            fclose($handle);
        };

        return response()->stream($streamCallback, $status, $headers);
    }

    /**
     * Parse Range header theo chuẩn "bytes=start-end" hoặc "bytes=-suffixLength"
     * Trả về: [start, end, statusCode]
     */
    private function resolveRange(?string $rangeHeader, int $fileSize): array
    {
        $start  = 0;
        $end    = $fileSize - 1;
        $status = 200;

        if ($rangeHeader && preg_match('/bytes=(\d*)-(\d*)/', $rangeHeader, $matches)) {
            $rangeStart = $matches[1];
            $rangeEnd   = $matches[2];

            // Cả hai đều rỗng -> bỏ qua
            if ($rangeStart === '' && $rangeEnd === '') {
                return [$start, $end, $status];
            }

            if ($rangeStart === '') {
                // suffix range: bytes=-N  => N byte cuối file
                $length = (int) $rangeEnd;
                if ($length > 0) {
                    $start = max($fileSize - $length, 0);
                }
            } else {
                $start = (int) $rangeStart;
            }

            if ($rangeEnd !== '') {
                $end = (int) $rangeEnd;
            }

            if ($end >= $fileSize) {
                $end = $fileSize - 1;
            }

            if ($start < 0) {
                $start = 0;
            }

            if ($start > $end) {
                // Range không hợp lệ -> fallback trả full file
                $start  = 0;
                $end    = $fileSize - 1;
                $status = 200;
            } else {
                $status = 206; // Partial Content
            }
        }

        return [$start, $end, $status];
    }

    /**
     * Kiểm tra xem path có phải URL ngoài hay không.
     */
    private function isExternalUrl(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://']);
    }

    /**
     * Cấp chữ ký (signed token) cho video HLS của bài học.
     * 
     * Client sẽ dùng token này kèm theo request tới Nginx để truy cập video.
     * Nginx sẽ xác thực token bằng module ngx_http_secure_link_module.
     * 
     * @param Request $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHlsSignature(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // 1. Kiểm tra tồn tại video
        if (!$lesson->video_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video.'
            ], 404);
        }

        // 2. Video đang được xử lý nền
        if (Str::startsWith($lesson->video_path, 'background-upload://')) {
            return response()->json([
                'success' => false,
                'message' => 'Video đang được xử lý. Vui lòng thử lại sau.'
            ], 409);
        }

        // 3. Phân quyền: chỉ root hoặc user thuộc khóa học mới xem được
        if (
            !$user->isRoot()
            && (
                $lesson->course_id === null
                || !$user->courses()->where('courses.id', $lesson->course_id)->exists()
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập video này.'
            ], 403);
        }

        // 4. Nếu là URL ngoài thì không cần sign
        $videoPath = $lesson->video_path;
        if ($this->isExternalUrl($videoPath)) {
            return response()->json([
                'success' => true,
                'message' => 'Video là URL ngoài, không cần chữ ký.',
                'data' => [
                    'type' => 'external',
                    'url' => $videoPath,
                ]
            ]);
        }

        // 5. Xây dựng URI cho file HLS (.m3u8)
        // Giả sử video được convert sang HLS và lưu tại: /hls/lessons/{lesson_id}/playlist.m3u8
        $hlsUri = $lesson->hls_path ?? '';

        // 6. Lấy thông tin cấu hình
        $baseUrl = config('hls.base_url');
        $restrictByIp = config('hls.restrict_by_ip', false);
        $clientIp = $restrictByIp ? $request->ip() : null;

        // 7. Tạo signed URL
        $signedData = $this->hlsSignedUrlService->generateSignedUrl(
            $baseUrl,
            $hlsUri,
            null, // Sử dụng default expiry
            $clientIp
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo chữ ký thành công.',
            'data' => [
                'type' => 'hls',
                'lesson_id' => $lesson->id,
                'signed_uri' => $signedData['signed_uri'],
                'signature' => $signedData['signature'],
                'expires' => $signedData['expires'],
                'expires_at' => $signedData['expires_at'],
                // Thông tin bổ sung để client có thể tự build URL nếu cần
                'base_url' => $baseUrl,
                'uri' => $hlsUri,
            ]
        ]);
    }
}
