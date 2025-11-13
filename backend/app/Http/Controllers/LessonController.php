<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * Danh sách bài học
     *
     * Query params:
     * - orphaned: Lọc theo không thuộc về khóa học nào
     * - course_id: Lọc theo khóa học
     * - search: Tìm kiếm theo title hoặc description
     * - duration_min: Lọc thời lượng tối thiểu (giây)
     * - duration_max: Lọc thời lượng tối đa (giây)
     * - sort_by: Sắp xếp theo ('id', 'title', 'duration', 'course_id', 'created_at', 'updated_at', 'display_order') - mặc định: course_id
     * - order_by: Thứ tự (asc, desc) - mặc định: asc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(Request $request)
    {
        $query = Lesson::with('course');

        // Lọc theo khóa học
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('orphaned') && $request->orphaned == true) {
            $query->where('course_id', NULL);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo duration_min
        if ($request->has('duration_min') && $request->duration_min) {
            $query->where('duration', '>=', (int)$request->duration_min);
        }

        // Lọc theo duration_max
        if ($request->has('duration_max') && $request->duration_max) {
            $query->where('duration', '<=', (int)$request->duration_max);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'course_id');
        $orderBy = $request->get('order_by', 'asc');

        // Validate sort_by
        $allowedSortBy = ['id', 'title', 'duration', 'course_id', 'created_at', 'updated_at', 'display_order'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'course_id';
        }

        // Validate order_by
        $orderBy = strtolower($orderBy);
        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'asc';
        }

        $query->orderBy($sortBy, $orderBy);

        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

        $lessons = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $lessons
        ]);
    }

    /**
     * Chi tiết bài học
     */
    public function show($id)
    {
        $lesson = Lesson::with('course')->find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lesson
        ]);
    }

    /**
     * Tạo bài học mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'duration' => 'required|integer|min:1',
            'video_url' => 'nullable|string',
            'video' => 'nullable|file|mimes:mp4,avi,mov,webm|max:512000', // 500MB
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that either video_url or video is provided
        if (!$request->has('video_url') && !$request->hasFile('video')) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp video URL hoặc upload file video'
            ], 422);
        }

        $lessonData = [
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'display_order' => $request->display_order ?? 0,
        ];

        // Xử lý upload thumbnail nếu có
        if ($request->hasFile('thumbnail')) {
            $storedPath = $request->file('thumbnail')->store('lesson-thumbnails', 'public');
            $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson-thumbnails/xxx.jpg
            $lessonData['thumbnail'] = $publicUrl;
        } elseif ($request->has('existingThumbnail') && $request->existingThumbnail) {
            // Giữ nguyên thumbnail cũ nếu có
            $lessonData['thumbnail'] = $request->existingThumbnail;
        } elseif ($request->has('path_thumbnail') && $request->path_thumbnail) {
            // Giữ nguyên thumbnail từ path_thumbnail
            $lessonData['thumbnail'] = $request->path_thumbnail;
        }

        // Xử lý upload video nếu có
        if ($request->hasFile('video')) {
            $storedPath = $request->file('video')->store('lesson-videos', 'public');
            $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson-videos/xxx.mp4
            $lessonData['video_url'] = $publicUrl;
        } elseif ($request->has('video_url') && $request->video_url) {
            $lessonData['video_url'] = $request->video_url;
        }

        $lesson = Lesson::create($lessonData);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bài học thành công',
            'data' => $lesson->load('course')
        ], 201);
    }

    /**
     * Cập nhật bài học
     */
    public function update(Request $request, $id)
    {

        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'duration' => 'required|integer|min:1',
            'video_url' => 'nullable|string',
            'video' => 'nullable|file|mimes:mp4,avi,mov,webm|max:512000', // 500MB
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate that either video_url or video is provided (or keep existing)
        if (!$request->has('video_url') && !$request->hasFile('video') && !$lesson->video_url) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng cung cấp video URL hoặc upload file video'
            ], 422);
        }

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration
        ];

        // if ($request->course_id !== "null" && !is_null($request->course_id) && $request->course_id !== NAN) {
        //     $updateData['course_id'] = $request->course_id;
        //     $updateData['display_order'] = $request->display_order ?? $lesson->display_order;
        // } else {
        //     $updateData['course_id'] = null;
        //     $updateData['display_order'] = null;
        // }

        // Xử lý upload thumbnail nếu có
        if ($request->hasFile('thumbnail')) {
            // Xóa thumbnail cũ nếu có
            if ($lesson->thumbnail) {
                $oldPath = str_replace('/storage/', '', $lesson->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }

            // Lưu thumbnail mới
            $storedPath = $request->file('thumbnail')->store('lesson-thumbnails', 'public');
            $publicUrl = Storage::url($storedPath);
            $updateData['thumbnail'] = $publicUrl;
        } elseif ($request->has('existingThumbnail') && $request->existingThumbnail) {
            // Giữ nguyên thumbnail cũ nếu có
            $updateData['thumbnail'] = $request->existingThumbnail;
        } elseif ($request->has('path_thumbnail') && $request->path_thumbnail) {
            // Giữ nguyên thumbnail từ path_thumbnail
            $updateData['thumbnail'] = $request->path_thumbnail;
        }

        // Xử lý upload video nếu có
        if ($request->hasFile('video')) {
            // Xóa video cũ nếu có (chỉ xóa nếu là file local, không xóa URL)
            if ($lesson->video_url && !filter_var($lesson->video_url, FILTER_VALIDATE_URL)) {
                $oldPath = str_replace('/storage/', '', $lesson->video_url);
                Storage::disk('public')->delete($oldPath);
            }

            // Lưu video mới
            $storedPath = $request->file('video')->store('lesson-videos', 'public');
            $publicUrl = Storage::url($storedPath);
            $updateData['video_url'] = $publicUrl;
        } elseif ($request->has('video_url') && $request->video_url) {
            $updateData['video_url'] = $request->video_url;
        }

        $lesson->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài học thành công',
            'data' => $lesson->load('course')
        ]);
    }

    /**
     * Xóa bài học (soft delete)
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Chỉ được phép xóa các bài học mồ côi
        $lessons = Lesson::whereIn('id', $request->lesson_ids)
            ->whereNull('course_id')
            ->get();

        foreach ($lessons as $lesson) {
            $lesson->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài học thành công'
        ]);
    }
}

