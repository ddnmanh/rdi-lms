<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Danh sách khóa học của sinh viên
     *
     * Query params:
     * - search: Tìm kiếm theo title hoặc description
     * - sort_by: Sắp xếp theo (id, title, start_date, end_date, created_at) - mặc định: id
     * - order_by: Thứ tự (asc, desc) - mặc định: desc
     */
    public function courses(Request $request)
    {
        $user = $request->user();

        $query = $user->courses()
            ->with(['lessons' => function ($query) {
                $query->orderBy('display_order');
            }]);

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $sortBy = strtolower($request->get('sort_by', 'joined_at'));
        $orderBy = strtolower($request->get('order_by', 'desc'));

        // Validate order_by
        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'desc';
        }

        switch ($sortBy) {
            case 'id':
            case 'title':
            case 'start_date':
            case 'end_date':
            case 'created_at':
            case 'updated_at':
                $query->orderBy($sortBy, $orderBy);
                break;
            case 'joined_at':
                // Sắp xếp theo created_at của course_user (thời điểm sinh viên được gán vào khóa học)
                $query->orderBy('course_user.created_at', $orderBy);
                break;
            default:
                $sortBy = 'joined_at';
        }

        $courses = $query->get();

        // Lấy tất cả lesson_views của user cho các lessons trong courses
        $lessonIds = $courses->flatMap(function ($course) {
            return $course->lessons->pluck('id');
        });

        $lessonViews = LessonView::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        // Gán lesson_view vào từng lesson và thêm progress vào course
        $courses->each(function ($course) use ($lessonViews) {
            $course->lessons->each(function ($lesson) use ($lessonViews) {
                $lesson->progress = $lessonViews->get($lesson->id);
            });

            // Chuyển dữ liệu từ pivot sang progress
            $course->progress = [
                'completion_percentage' => $course->pivot->completion_percentage ?? 0,
                'is_passed' => $course->pivot->is_passed ?? false,
                'joined_at' => $course->pivot->created_at,
                'updated_at' => $course->pivot->updated_at,
            ];

            // Xóa pivot khỏi response
            unset($course->pivot);
        });

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    public function courseDetail(Request $request, $courseId)
    {
        $user = $request->user();

        // Query course và eager load user hiện tại qua pivot
        $course = Course::with([
            'lessons' => function ($query) {
                $query->orderBy('display_order');
            },
            'users' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ])->find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        // Kiểm tra sinh viên có quyền truy cập khóa học không
        if ($course->users->isEmpty()) {
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
                    ? round(($lessonView ? $lessonView->watched_duration + 1 : 0) / $lesson->duration * 100, 2)
                    : 0,
            ];

            return $lesson;
        });

        $course->lessons = $lessons;

        // Lấy thông tin pivot từ user đầu tiên (chỉ có 1 user do filter ở trên)
        $userPivot = $course->users->first()->pivot;

        $course->progress = [
            'completion_percentage' => $userPivot->completion_percentage ?? 0,
            'is_passed' => $userPivot->is_passed ?? false,
            'joined_at' => $userPivot->created_at,
            'updated_at' => $userPivot->updated_at,
        ];

        // Xóa users khỏi response vì không cần thiết
        unset($course->users);

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
            'completion_percentage' => $lessonView ? $lessonView->completion_percentage : 0,
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
            'last_position' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Lấy thông tin bài học
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

        $lessonView = LessonView::where('user_id', $user->id)
            ->where('lesson_id', $request->lesson_id)
            ->first();

        // Đảm bảo last_position không vượt quá duration
        $lastPosition = min($request->last_position, $lesson->duration);

        // Tính watched_duration - chỉ tăng, không giảm
        $watchedDuration = max(($lessonView ? $lessonView->watched_duration : 0), $lastPosition);

        // Tính completion_percentage
        $completionPercentage = $lesson->duration > 0
            ? round(($watchedDuration + 1) / $lesson->duration * 100, 2)
            : 0;

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
                'completion_percentage' => $completionPercentage,
            ]
        );

        // Cập nhật tiến độ của khóa học (course_user)
        $this->updateProcessCourseUserPivot($user->id, $lesson->course_id);

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
                'is_completed' => $overallPercentage > 80,
            ]
        ]);
    }

    // Cập nhật bảng course_user với completion_percentage và is_passed
    private function updateProcessCourseUserPivot($user_id, $course_id)
    {
        $completionPercentage = $this->calculateCourseCompletionPercentage($user_id, $course_id);

        // Cập nhật bảng course_user
        $user = User::find($user_id);
        if ($user) {
            $user->courses()->updateExistingPivot($course_id, [
                'completion_percentage' => $completionPercentage,
                // 'is_passed' => $completionPercentage > 80,
            ]);
        }
    }

    // Tính phần trăm hoàn thành khóa học dựa trên tổng thời lượng đã xem của tất cả bài học
    private function calculateCourseCompletionPercentage($user_id, $course_id)
    {
        $course = Course::with('lessons')->find($course_id);

        if (!$course) {
            return 0;
        }

        $totalLessonDuration = 0; // Tổng thời lượng tất cả bài học
        $totalLessonWatchedDuration = 0; // Tổng thời lượng đã xem của tất cả bài học

        if ($course->lessons->count() === 0) {
            return 0;
        }

        $lessonIds = [];
        foreach ($course->lessons as $lesson) {
            $totalLessonDuration += $lesson->duration;
            $lessonIds[] = $lesson->id;
        }

        $lessonViews = LessonView::where('user_id', $user_id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        if ($lessonViews->isEmpty()) {
            return 0;
        }

        $totalLessonWatched = 0; // Đếm các bài học đã xem hơn 1s

        foreach ($lessonViews as $view) {
            $totalLessonWatchedDuration += $view->watched_duration;

            if ($view->watched_duration > 0) {
                $totalLessonWatched += 1;
            }
        }

        $percent = $totalLessonDuration > 0
            ? round(($totalLessonWatchedDuration / $totalLessonDuration) * 100, 2)
            : 0;

        // Tính toán sai số 1s cho mỗi bài học đã xem
        if (round((($totalLessonWatchedDuration + $totalLessonWatched) / $totalLessonDuration) * 100, 2) >= 100) {
            $percent = 100;
        }

        return $percent;
    }
}

