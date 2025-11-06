<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Danh sách khóa học của sinh viên
     */
    public function courses(Request $request)
    {
        $user = $request->user();

        $courses = $user->courses()
            ->with(['lessons' => function ($query) {
                $query->orderBy('display_order');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Chi tiết khóa học của sinh viên
     */
    public function courseDetail(Request $request, $courseId)
    {
        $user = $request->user();

        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('display_order');
        }])->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        // Kiểm tra sinh viên có quyền truy cập khóa học không
        if (!$user->courses->contains($courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập khóa học này'
            ], 403);
        }

        // Lấy tiến độ học của từng bài học
        $lessons = $course->lessons->map(function ($lesson) use ($user) {
            $lessonView = LessonView::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            $lesson->progress = [
                'watched_duration' => $lessonView ? $lessonView->watched_duration : 0,
                'last_position' => $lessonView ? $lessonView->last_position : 0,
                'last_watched_at' => $lessonView ? $lessonView->last_watched_at : null,
                'completion_percentage' => $lesson->duration > 0 
                    ? round(($lessonView ? $lessonView->watched_duration : 0) / $lesson->duration * 100, 2)
                    : 0,
            ];

            return $lesson;
        });

        $course->lessons = $lessons;

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     * Chi tiết bài học của sinh viên
     */
    public function lessonDetail(Request $request, $lessonId)
    {
        $user = $request->user();

        $lesson = Lesson::with('course')->find($lessonId);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        // Kiểm tra sinh viên có quyền truy cập khóa học không
        if (!$user->courses->contains($lesson->course_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập bài học này'
            ], 403);
        }

        // Lấy tiến độ học
        $lessonView = LessonView::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        $lesson->progress = [
            'watched_duration' => $lessonView ? $lessonView->watched_duration : 0,
            'last_position' => $lessonView ? $lessonView->last_position : 0,
            'last_watched_at' => $lessonView ? $lessonView->last_watched_at : null,
            'completion_percentage' => $lesson->duration > 0 
                ? round(($lessonView ? $lessonView->watched_duration : 0) / $lesson->duration * 100, 2)
                : 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $lesson
        ]);
    }

    /**
     * Cập nhật tiến độ xem bài học
     */
    public function updateProgress(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
            'watched_duration' => 'required|integer|min:0',
            'last_position' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson = Lesson::find($request->lesson_id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        // Kiểm tra sinh viên có quyền truy cập khóa học không
        if (!$user->courses->contains($lesson->course_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập bài học này'
            ], 403);
        }

        // Đảm bảo watched_duration không vượt quá duration
        $watchedDuration = min($request->watched_duration, $lesson->duration);
        $lastPosition = min($request->last_position, $lesson->duration);

        // Cập nhật hoặc tạo mới lesson_view
        $lessonView = LessonView::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $request->lesson_id,
            ],
            [
                'watched_duration' => $watchedDuration,
                'last_position' => $lastPosition,
                'last_watched_at' => now(),
            ]
        );

        $completionPercentage = $lesson->duration > 0 
            ? round($watchedDuration / $lesson->duration * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật tiến độ thành công',
            'data' => [
                'lesson_view' => $lessonView,
                'completion_percentage' => $completionPercentage,
            ]
        ]);
    }

    /**
     * Tiến độ học của sinh viên trong một khóa học
     */
    public function courseProgress(Request $request, $courseId)
    {
        $user = $request->user();

        $course = Course::with('lessons')->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        // Kiểm tra sinh viên có quyền truy cập khóa học không
        if (!$user->courses->contains($courseId)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền truy cập khóa học này'
            ], 403);
        }

        $totalDuration = 0;
        $totalWatchedDuration = 0;

        foreach ($course->lessons as $lesson) {
            $totalDuration += $lesson->duration;
            
            $lessonView = LessonView::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            if ($lessonView) {
                $totalWatchedDuration += $lessonView->watched_duration;
            }
        }

        $overallPercentage = $totalDuration > 0 
            ? round($totalWatchedDuration / $totalDuration * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => $courseId,
                'course_title' => $course->title,
                'total_duration' => $totalDuration,
                'total_watched_duration' => $totalWatchedDuration,
                'overall_percentage' => $overallPercentage,
                'is_completed' => $overallPercentage >= 80,
            ]
        ]);
    }
}

