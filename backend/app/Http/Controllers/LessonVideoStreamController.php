<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LessonVideoStreamController extends Controller
{
    /**
     * Stream lesson video with HTTP range support so the Flutter app
     * can play without downloading the entire file.
     */
    public function stream(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // dump($user->toArray());

        if (!$lesson->video_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video.'
            ], 404);
        }

        if (Str::startsWith($lesson->video_path, 'background-upload://')) {
            return response()->json([
                'success' => false,
                'message' => 'Video đang được xử lý. Vui lòng thử lại sau.'
            ], 409);
        } 

        // Chỉ cho phép người dùng thuộc khóa học của bài học này
        if (!$user->isRoot() && ($lesson->course_id == null || !$user->courses()->where('courses.id', $lesson->course_id)->exists()) ) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập video này.'
            ], 403);
        }

        if ($this->isExternalUrl($lesson->video_path)) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Video được lưu ở nguồn bên ngoài, không thể stream nội bộ.'
            // ], 422);
            return redirect($lesson->video_path);
        }

        $relativePath = Str::after($lesson->video_path, '/storage/');
        $fullPath = storage_path('app/public/' . $relativePath);

        if (!File::exists($fullPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy file video.'
            ], 404);
        }

        $fileSize = File::size($fullPath);
        $mimeType = File::mimeType($fullPath) ?: 'video/mp4';
        $rangeHeader = $request->header('Range');

        [$start, $end, $status] = $this->resolveRange($rangeHeader, $fileSize);
        $length = $end - $start + 1;

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Accept-Ranges' => 'bytes',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$fileSize}";
        }

        $streamCallback = function () use ($fullPath, $start, $length) {
            $chunkSize = 1024 * 1024; // 1MB
            $bytesRemaining = $length;

            $handle = fopen($fullPath, 'rb');
            fseek($handle, $start);

            while ($bytesRemaining > 0 && !feof($handle)) {
                $readLength = min($chunkSize, $bytesRemaining);
                $buffer = fread($handle, $readLength);
                echo $buffer;
                flush();

                $bytesRemaining -= strlen($buffer);
            }

            fclose($handle);
        };

        return response()->stream($streamCallback, $status, $headers);
    }

    private function resolveRange(?string $rangeHeader, int $fileSize): array
    {
        $start = 0;
        $end = $fileSize - 1;
        $status = 200;

        if ($rangeHeader && preg_match('/bytes=(\d*)-(\d*)/', $rangeHeader, $matches)) {
            if ($matches[1] !== '') {
                $start = (int) $matches[1];
            }

            if ($matches[2] !== '') {
                $end = (int) $matches[2];
            }

            if ($end >= $fileSize) {
                $end = $fileSize - 1;
            }

            if ($start > $end || $start < 0) {
                $start = 0;
            }

            $status = 206;
        }

        return [$start, $end, $status];
    }

    private function isExternalUrl(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://']);
    }
}

