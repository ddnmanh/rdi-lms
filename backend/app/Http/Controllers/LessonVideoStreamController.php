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
     * Cấp chữ ký (signed token) cho video của bài học (tự động chọn HLS hoặc MP4).
     * 
     * Client sẽ dùng token này kèm theo request tới Nginx để truy cập video.
     * Nginx sẽ xác thực token bằng module ngx_http_secure_link_module.
     * 
     * @param Request $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUriWithSignatureForVideo(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // 1. Kiểm tra tồn tại video
        if (!$lesson->video_path && !$lesson->hls_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video.'
            ], 404);
        }

        // 2. Video đang được xử lý nền
        if (Str::startsWith($lesson->video_path, 'background-upload://') || Str::startsWith($lesson->video_path, 'background-upload://')) {
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

        // 4. Chuẩn hóa đường dẫn video
        $baseUrl = config('static-source.base_url'); 
        $videoUri = $lesson->hls_path ?? $lesson->video_path;
        
        // 5. Tạo signed URL
        $signedData = $this->hlsSignedUrlService->generateSignedUrl(
            $baseUrl,
            $videoUri,
            null, // Sử dụng default expiry
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo chữ ký thành công.',
            'data' => [
                'type' => $lesson->hls_path ? 'hls' : 'mp4',
                'lesson_id' => $lesson->id,
                'base_url' => $baseUrl,
                'uri' => $videoUri,
                'signed_uri' => $signedData['signed_uri'],
                'signature' => $signedData['signature'],
                'expires' => $signedData['expires'],
                'expires_at' => $signedData['expires_at'],
            ]
        ]);
    }

    /**
     * Cấp chữ ký (signed token) cho video MP4 của bài học.
     * 
     * Client sẽ dùng token này kèm theo request tới Nginx để truy cập video.
     * Nginx sẽ xác thực token bằng module ngx_http_secure_link_module.
     * 
     * @param Request $request
     * @param Lesson $lesson
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUriWithSignatureForMp4Video(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // 1. Kiểm tra tồn tại video
        if (!$lesson->video_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video dạng MP4.'
            ], 404);
        }

        // 2. Video đang được xử lý nền
        if (Str::startsWith($lesson->video_path, 'background-upload://') || Str::startsWith($lesson->video_path, 'background-upload://')) {
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

        // 4. Chuẩn hóa đường dẫn HLS
        $baseUrl = config('static-source.base_url'); 
        $mp4Uri = $lesson->video_path ?? '';

        // 5. Tạo signed URL
        $signedData = $this->hlsSignedUrlService->generateSignedUrl(
            $baseUrl,
            $mp4Uri,
            null, // Sử dụng default expiry
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo chữ ký thành công.',
            'data' => [
                'type' => 'mp4',
                'lesson_id' => $lesson->id,
                'base_url' => $baseUrl,
                'uri' => $mp4Uri,
                'signed_uri' => $signedData['signed_uri'],
                'signature' => $signedData['signature'],
                'expires' => $signedData['expires'],
                'expires_at' => $signedData['expires_at'],
            ]
        ]);
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
    public function getUriWithSignatureForHlsVideo(Request $request, Lesson $lesson)
    {
        $user = $request->user();

        // 1. Kiểm tra tồn tại video
        if (!$lesson->hls_path) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học chưa có video dạng HLS.'
            ], 404);
        }

        // 2. Video đang được xử lý nền
        if (Str::startsWith($lesson->hls_path, 'background-upload://') || Str::startsWith($lesson->video_path, 'background-upload://')) {
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

        // 4. Chuẩn hóa đường dẫn HLS
        $baseUrl = config('static-source.base_url'); 
        $hlsUri = $lesson->hls_path ?? '';

        // 5. Tạo signed URL
        $signedData = $this->hlsSignedUrlService->generateSignedUrl(
            $baseUrl,
            $hlsUri,
            null, // Sử dụng default expiry
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã tạo chữ ký thành công.',
            'data' => [
                'type' => 'hls',
                'lesson_id' => $lesson->id,
                'base_url' => $baseUrl,
                'uri' => $hlsUri,
                'signed_uri' => $signedData['signed_uri'],
                'signature' => $signedData['signature'],
                'expires' => $signedData['expires'],
                'expires_at' => $signedData['expires_at'],
            ]
        ]);
    }
}
