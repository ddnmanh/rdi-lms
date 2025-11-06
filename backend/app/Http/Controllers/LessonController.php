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
     */
    public function index(Request $request)
    {
        $query = Lesson::with('course');

        // Lọc theo khóa học
        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Tìm kiếm
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy('display_order');

        $perPage = $request->get('per_page', 15);
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

