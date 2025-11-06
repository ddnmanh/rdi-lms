<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonView;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Thống kê khóa học - số lượng sinh viên
     */
    public function courseStatistics(Request $request, $courseId)
    {
        $course = Course::with('lessons')->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $totalStudents = $course->users()->count();
        $totalLessons = $course->lessons()->count();

        // Tính tổng thời lượng khóa học
        $totalDuration = $course->lessons->sum('duration');

        return response()->json([
            'success' => true,
            'data' => [
                'course_id' => $courseId,
                'course_title' => $course->title,
                'total_students' => $totalStudents,
                'total_lessons' => $totalLessons,
                'total_duration' => $totalDuration,
            ]
        ]);
    }

    /**
     * Danh sách sinh viên trong khóa học với tiến độ học
     */
    public function courseStudents(Request $request, $courseId)
    {
        $course = Course::with('lessons')->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $students = $course->users()->get();

        $totalDuration = $course->lessons->sum('duration');

        $studentsWithProgress = $students->map(function ($student) use ($courseId, $totalDuration) {
            $course = Course::with('lessons')->find($courseId);
            $totalWatchedDuration = 0;

            foreach ($course->lessons as $lesson) {
                $lessonView = LessonView::where('user_id', $student->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();

                if ($lessonView) {
                    $totalWatchedDuration += $lessonView->watched_duration;
                }
            }

            $overallPercentage = $totalDuration > 0 
                ? round($totalWatchedDuration / $totalDuration * 100, 2)
                : 0;

            return [
                'user_id' => $student->id,
                'email' => $student->email,
                'fullname' => $student->fullname,
                'total_watched_duration' => $totalWatchedDuration,
                'overall_percentage' => $overallPercentage,
                'is_completed' => $overallPercentage >= 80,
            ];
        });

        $perPage = $request->get('per_page', 15);
        $currentPage = $request->get('page', 1);
        $items = $studentsWithProgress->forPage($currentPage, $perPage)->values();

        return response()->json([
            'success' => true,
            'data' => [
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $studentsWithProgress->count(),
                'items' => $items,
            ]
        ]);
    }

    /**
     * Chi tiết tiến độ học của một sinh viên trong khóa học
     */
    public function studentCourseProgress(Request $request, $courseId, $userId)
    {
        $course = Course::with('lessons')->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $student = User::find($userId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sinh viên'
            ], 404);
        }

        // Kiểm tra sinh viên có trong khóa học không
        if (!$course->users->contains($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Sinh viên không thuộc khóa học này'
            ], 404);
        }

        $totalDuration = 0;
        $totalWatchedDuration = 0;
        $lessonProgress = [];

        foreach ($course->lessons as $lesson) {
            $totalDuration += $lesson->duration;

            $lessonView = LessonView::where('user_id', $userId)
                ->where('lesson_id', $lesson->id)
                ->first();

            $watchedDuration = $lessonView ? $lessonView->watched_duration : 0;
            $totalWatchedDuration += $watchedDuration;

            $completionPercentage = $lesson->duration > 0 
                ? round($watchedDuration / $lesson->duration * 100, 2)
                : 0;

            $lessonProgress[] = [
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'duration' => $lesson->duration,
                'watched_duration' => $watchedDuration,
                'completion_percentage' => $completionPercentage,
                'last_position' => $lessonView ? $lessonView->last_position : 0,
                'last_watched_at' => $lessonView ? $lessonView->last_watched_at : null,
            ];
        }

        $overallPercentage = $totalDuration > 0 
            ? round($totalWatchedDuration / $totalDuration * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'email' => $student->email,
                    'fullname' => $student->fullname,
                ],
                'course' => [
                    'id' => $course->id,
                    'title' => $course->title,
                ],
                'total_duration' => $totalDuration,
                'total_watched_duration' => $totalWatchedDuration,
                'overall_percentage' => $overallPercentage,
                'is_completed' => $overallPercentage >= 80,
                'lesson_progress' => $lessonProgress,
            ]
        ]);
    }

    /**
     * Lịch sử đăng nhập của sinh viên
     */
    public function studentLoginHistory(Request $request, $userId)
    {
        $student = User::find($userId);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy sinh viên'
            ], 404);
        }

        $loginHistory = RefreshToken::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'user_id' => $token->user_id,
                    'expires_at' => $token->expires_at,
                    'ip_address' => $token->ip_address,
                    'user_agent' => $token->user_agent,
                    'is_revoked' => $token->is_revoked,
                    'created_at' => $token->created_at,
                    'updated_at' => $token->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'email' => $student->email,
                    'fullname' => $student->fullname,
                ],
                'login_history' => $loginHistory,
            ]
        ]);
    }

    /**
     * Báo cáo tổng quan - thống kê tất cả khóa học
     */
    public function overview(Request $request)
    {
        $totalCourses = Course::count();
        $totalStudents = User::whereHas('roles', function ($query) {
            $query->where('name', 'student');
        })->count();
        $totalLessons = Lesson::count();

        $courses = Course::withCount('users')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_courses' => $totalCourses,
                'total_students' => $totalStudents,
                'total_lessons' => $totalLessons,
                'courses' => $courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'student_count' => $course->users_count,
                    ];
                }),
            ]
        ]);
    }
}

