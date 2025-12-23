<?php

namespace App\Http\Controllers;

use App\Http\Requests\lessonQuizzes\SubmitAttemptRequest;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAttemptAnswer;
use App\Models\LessonView;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /**
     * Lấy danh sách ghi chú của sinh viên cho một bài học
     *
     * Query params:
     * - sort_by: Sắp xếp theo (duration_at, created_at) - mặc định: duration_at
     * - order_by: Thứ tự (asc, desc) - mặc định: asc
     */
    public function getNotes(Request $request, $lessonId)
    {
        $user = $request->user();

        // Kiểm tra bài học tồn tại
        $lesson = Lesson::find($lessonId);
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

        // Sắp xếp
        $sortBy = strtolower($request->get('sort_by', 'duration_at'));
        $orderBy = strtolower($request->get('order_by', 'asc'));

        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'asc';
        }

        if (!in_array($sortBy, ['duration_at', 'created_at'])) {
            $sortBy = 'duration_at';
        }

        $notes = Note::where('lesson_id', $lessonId)
            ->where('user_id', $user->id)
            ->orderBy($sortBy, $orderBy)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $notes
        ]);
    }

    /**
     * Tạo ghi chú mới cho bài học
     */
    public function storeNote(StoreNoteRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        // Kiểm tra bài học tồn tại
        $lesson = Lesson::find($validated['lesson_id']);
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

        // Kiểm tra duration_at không vượt quá duration của bài học
        if ($lesson->duration && $validated['duration_at'] > $lesson->duration) {
            return response()->json([
                'success' => false,
                'message' => 'Thời điểm ghi chú không thể vượt quá thời lượng bài học'
            ], 422);
        }

        // Kiểm tra có ghi chú tại thời điểm đó chưa
        $note = Note::where('lesson_id', $validated['lesson_id'])
            ->where('user_id', $user->id)
            ->where('duration_at', $validated['duration_at'])
            ->first();
        if ($note) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có ghi chú tại thời điểm đó. Vui lòng cập nhật ghi chú đó'
            ], 400);
        }

        // Tạo ghi chú
        $note = Note::create([
            'lesson_id' => $validated['lesson_id'],
            'user_id' => $user->id,
            'duration_at' => $validated['duration_at'],
            'content' => $validated['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo ghi chú thành công',
            'data' => $note
        ], 201);
    }

    /**
     * Cập nhật ghi chú
     */
    public function updateNote(UpdateNoteRequest $request, $noteId)
    {
        $user = $request->user();

        // Tìm ghi chú
        $note = Note::where('id', $noteId)
            ->where('user_id', $user->id)
            ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ghi chú'
            ], 404);
        }

        $validated = $request->validated();

        // Kiểm tra duration_at không vượt quá duration của bài học
        if (isset($validated['duration_at'])) {
            $lesson = Lesson::find($note->lesson_id);
            if ($lesson && $lesson->duration && $validated['duration_at'] > $lesson->duration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời điểm ghi chú không thể vượt quá thời lượng bài học'
                ], 422);
            }
        }

        // Cập nhật ghi chú
        $note->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ghi chú thành công',
            'data' => $note
        ]);
    }

    /**
     * Xóa ghi chú
     */
    public function deleteNote(Request $request, $noteId)
    {
        $user = $request->user();

        // Tìm ghi chú
        $note = Note::where('id', $noteId)
            ->where('user_id', $user->id)
            ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy ghi chú'
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa ghi chú thành công'
        ]);
    }

    // ========================================
    // API CHO SINH VIÊN LÀM BÀI
    // ========================================

    /**
     * Submit bài làm quiz và tính điểm
     */
    public function submitAttempt(SubmitAttemptRequest $request, $quizId): JsonResponse
    {
        try {
            $body = $request->validated();
            $user = $request->user();

            $quiz = LessonQuiz::with(['questions.options'])->find($quizId);

            // Kiểm tra quiz có tồn tại không
            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            // Kiểm tra quiz có hoạt động không
            if (!$quiz->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz này hiện không hoạt động'
                ], 400);
            }

            // Kiểm tra quiz có câu hỏi không
            if ($quiz->questions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz này chưa có câu hỏi'
                ], 400);
            }

            // Kiểm tra quiz có thuộc về lesson của user không
            $lesson = Lesson::find($quiz->lesson_id);
            if (!$user->courses->contains($lesson->course_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bài học này không thuộc về khóa học của bạn'
                ], 403);
            }

            // Kiểm tra user đã pass quiz này chưa
            $attempt = LessonQuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $user->id)
                ->where('passed', true)
                ->first();

            if ($attempt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã làm bài quiz này rồi'
                ], 400);
            }

            DB::beginTransaction();

            // Tối ưu: Tạo map questions và correct options trước để lookup nhanh O(1)
            $questionMap = $quiz->questions->keyBy('id');
            $correctOptionsMap = [];

            foreach ($quiz->questions as $question) {
                // Lấy danh sách ID các đáp án đúng của câu hỏi (đã sort để so sánh chính xác)
                $correctOptionIds = $question->options
                    ->where('is_correct', true)
                    ->pluck('id')
                    ->sort()
                    ->values()
                    ->toArray();

                $correctOptionsMap[$question->id] = $correctOptionIds;
            }

            // Tính điểm tối đa
            $maxPoints = $quiz->questions->sum('points');

            // Tạo attempt
            $attempt = LessonQuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $user->id,
                'percent_score_earned' => 0,
                'score_earned' => 0,
                'max_possible_score' => $maxPoints,
                'passed' => false,
                'started_at' => now(),
                'completed_at' => now(),
            ]);

            $totalScoresEarned = 0;
            $answers = $body['answers'];
            $answerRecords = [];

            // Xử lý từng câu trả lời
            foreach ($answers as $answerData) {
                $questionId = $answerData['question_id'];
                $selectedOptionIds = $answerData['selected_option_ids'];

                // Validate question tồn tại trong quiz này
                if (!isset($questionMap[$questionId])) {
                    continue;
                }

                $question = $questionMap[$questionId];
                $correctOptionIds = $correctOptionsMap[$questionId] ?? [];

                // Validate selected option IDs thuộc về question này
                $questionOptionIds = $question->options->pluck('id')->toArray();
                $validSelectedIds = array_intersect($selectedOptionIds, $questionOptionIds);

                // Nếu có option ID không hợp lệ, bỏ qua câu hỏi này
                if (count($validSelectedIds) !== count($selectedOptionIds)) {
                    continue;
                }

                // Sort selected IDs để so sánh chính xác
                sort($validSelectedIds);

                // Kiểm tra câu trả lời đúng
                $isCorrect = $this->checkAnswer(
                    $question->question_type,
                    $validSelectedIds,
                    $correctOptionIds
                );

                $scoresEarned = $isCorrect ? $question->points : 0;
                $totalScoresEarned += $scoresEarned;

                // Chuẩn bị dữ liệu để batch insert
                // Lưu ý: Khi dùng insert(), casts của model không được apply, nên cần JSON encode mảng
                $answerRecords[] = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'selected_option_ids' => json_encode($validSelectedIds),
                    'is_correct' => $isCorrect,
                    'score_earned' => $scoresEarned,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Batch insert tất cả answers trong một query
            if (!empty($answerRecords)) {
                LessonQuizAttemptAnswer::insert($answerRecords);
            }

            // Tính điểm phần trăm
            $scorePercent = $maxPoints > 0 ? round(($totalScoresEarned / $maxPoints) * 100, 2) : 0;
            $passed = $scorePercent >= $quiz->passing_percent_score;

            // Cập nhật attempt
            $attempt->update([
                'percent_score_earned' => $scorePercent,
                'score_earned' => $totalScoresEarned,
                'passed' => $passed,
            ]);

            DB::commit();

            // Load relationships
            $attempt->load(['answers.question']);

            return response()->json([
                'success' => true,
                'message' => $passed ? 'Chúc mừng! Bạn đã hoàn thành quiz.' : 'Bạn chưa đạt điểm tối thiểu. Hãy thử lại!',
                'data' => [
                    'attempt' => $attempt,
                    'percent_score_earned' => $scorePercent,
                    'passing_percent_score' => $quiz->passing_percent_score,
                    'passed' => $passed,
                    'score_earned' => $totalScoresEarned,
                    'max_possible_score' => $maxPoints,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi submit bài làm: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra câu trả lời đúng hay sai
     *
     * @param string $questionType Loại câu hỏi: 'single_choice' hoặc 'multiple_choice'
     * @param array $selectedIds Mảng ID các đáp án đã chọn (đã được sort)
     * @param array $correctIds Mảng ID các đáp án đúng (đã được sort)
     * @return bool True nếu đúng, False nếu sai
     */
    private function checkAnswer(string $questionType, array $selectedIds, array $correctIds): bool
    {
        // Đảm bảo cả hai mảng đã được sort (đã sort ở nơi gọi, nhưng để chắc chắn)
        sort($selectedIds);
        sort($correctIds);

        if ($questionType === 'single_choice') {
            // Single choice: phải chọn đúng 1 đáp án và đáp án đó phải đúng
            return count($selectedIds) === 1
                && count($correctIds) === 1
                && $selectedIds[0] === $correctIds[0];
        }

        // Multiple choice: phải chọn đúng TẤT CẢ đáp án đúng
        // Không được thừa (chọn thêm đáp án sai) hoặc thiếu (thiếu đáp án đúng)
        // Số lượng phải bằng nhau và nội dung phải giống hệt
        return count($selectedIds) === count($correctIds)
            && $selectedIds === $correctIds;
    }

    /**
     * Lấy lịch sử làm bài của user cho một quiz
     */
    public function getAttempts(Request $request, $quizId): JsonResponse
    {
        try {
            $user = $request->user();

            $quiz = LessonQuiz::find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            $attempts = LessonQuizAttempt::where('quiz_id', $quizId)
                ->where('user_id', $user->id)
                ->with(['answers'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Kiểm tra user đã pass chưa
            $hasPassed = $attempts->where('passed', true)->isNotEmpty();

            return response()->json([
                'success' => true,
                'data' => [
                    'attempts' => $attempts,
                    'has_passed' => $hasPassed,
                    'total_attempts' => $attempts->count(),
                    'best_score' => $attempts->max('percent_score_earned') ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy lịch sử làm bài: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getQuizzesByLesson(Request $request, $lessonId): JsonResponse
    {
        try {
            $user = $request->user();

            // Lấy thông tin bài học
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            // Kiểm tra user có thuộc khóa học của bài học này không (tức là có quyền truy cập bài học)
            if (!$user->courses->contains($lesson->course_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền truy cập bài học này'
                ], 403);
            }

            $quizzes = $lesson->quizzes()->where('is_active', true)->orderBy('start_at_seconds')->get()->load('questions.options');

            return response()->json([
                'success' => true,
                'data' => $quizzes
            ]);


        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách quiz của bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra user đã pass quiz của một lesson chưa
     * Dùng cho frontend để kiểm tra có cho phép xem tiếp video không
     */
    public function checkQuizStatus(Request $request, $lessonId): JsonResponse
    {
        try {
            $user = $request->user();

            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            // Lấy tất cả quiz active và required của lesson
            $quizzes = LessonQuiz::where('lesson_id', $lessonId)
                ->where('is_active', true)
                ->where('is_required', true)
                ->orderBy('start_at_seconds')
                ->get(['id', 'title', 'start_at_seconds', 'passing_percent_score']);

            // Nếu không có quiz nào, trả về ngay
            if ($quizzes->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'lesson_id' => $lessonId,
                        'all_passed' => true,
                        'quizzes' => [],
                    ]
                ]);
            }

            // Tối ưu: Lấy tất cả quiz IDs đã pass của user trong một query duy nhất
            $quizIds = $quizzes->pluck('id');
            $passedQuizIds = LessonQuizAttempt::whereIn('quiz_id', $quizIds)
                ->where('user_id', $user->id)
                ->where('passed', true)
                ->pluck('quiz_id')
                ->unique()
                ->toArray();

            // Tạo map để lookup nhanh
            $passedQuizMap = array_flip($passedQuizIds);

            // Xây dựng kết quả và kiểm tra all_passed trong một lần duyệt
            $quizStatuses = [];
            $allPassed = true;

            foreach ($quizzes as $quiz) {
                $passed = isset($passedQuizMap[$quiz->id]);

                $quizStatuses[] = [
                    'quiz_id' => $quiz->id,
                    'title' => $quiz->title,
                    'start_at_seconds' => $quiz->start_at_seconds,
                    'passing_percent_score' => $quiz->passing_percent_score,
                    'passed' => $passed,
                ];

                if (!$passed) {
                    $allPassed = false;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'lesson_id' => $lessonId,
                    'all_passed' => $allPassed,
                    'quizzes' => $quizStatuses,
                ]
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi kiểm tra trạng thái quiz: ' . $e->getMessage()
            ], 500);
        }
    }
}

