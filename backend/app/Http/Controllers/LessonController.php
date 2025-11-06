<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * Danh sách bài học
     * 
     * Query params:
     * - course_id: Lọc theo khóa học
     * - search: Tìm kiếm theo title hoặc description
     * - duration_min: Lọc thời lượng tối thiểu (giây)
     * - duration_max: Lọc thời lượng tối đa (giây)
     * - sort_by: Sắp xếp theo (id, title, duration, display_order, created_at) - mặc định: display_order
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
        $sortBy = $request->get('sort_by', 'display_order');
        $orderBy = $request->get('order_by', 'asc');
        
        // Validate sort_by
        $allowedSortBy = ['id', 'title', 'duration', 'display_order', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'display_order';
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
            'duration' => 'required|integer|min:1',
            'video_url' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson = Lesson::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'video_url' => $request->video_url,
            'display_order' => $request->display_order ?? 0,
        ]);

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
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:1',
            'video_url' => 'required|string',
            'display_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson->update([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'video_url' => $request->video_url,
            'display_order' => $request->display_order ?? $lesson->display_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài học thành công',
            'data' => $lesson->load('course')
        ]);
    }

    /**
     * Xóa bài học (soft delete)
     */
    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài học thành công'
        ]);
    }
}

