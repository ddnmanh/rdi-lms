<?php

namespace App\Http\Controllers;

use App\Http\Requests\lessons\DestroyRequest;
use App\Http\Requests\lessons\GetAllRequest;
use App\Http\Requests\lessons\StoreRequest;
use App\Http\Requests\lessons\UpdateRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
    public function index(GetAllRequest $request): JsonResponse
    {

        try {
            $body = $request->validated();

            $query = Lesson::with(['course', 'quizzes']);

            // Nếu không phải root/admin thì chỉ thấy các bài học thỏa điều kiện sau:
            // Do mình tạo
            // Thuộc khóa học do mình tạo
            if (!$request->user()->hasRole('ROOT') && !$request->user()->hasRole('ADMIN')) {
                $query->where(function ($q) use ($request) {
                    $q->where('created_by', $request->user()->id)
                        ->orWhereHas('course', function ($q) use ($request) {
                            $q->where('created_by', $request->user()->id);
                        });
                });
            }

            // Lọc theo khóa học
            if (isset($body['course_id']) && $body['course_id']) {
                $query->where('course_id', $body['course_id']);
            }

            if (isset($body['orphaned']) && $body['orphaned'] == true) {
                $query->where('course_id', NULL);
            }

            // Tìm kiếm
            if (isset($body['search']) && $body['search']) {
                $search = $body['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Lọc theo duration_min
            if (isset($body['duration_min']) && $body['duration_min']) {
                $query->where('duration', '>=', (int)$body['duration_min']);
            }

            // Lọc theo duration_max
            if (isset($body['duration_max']) && $body['duration_max']) {
                $query->where('duration', '<=', (int)$body['duration_max']);
            }

            // Sắp xếp
            $sortBy = $body['sort_by'] ?? 'course_id';
            $orderBy = $body['order_by'] ?? 'asc';

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

            $perPage = $body['per_page'] ?? 15;
            // $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

            $lessons = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $lessons
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết bài học
     */
    public function show($id)
    {
        try {
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
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê bài học (Quiz, Questions, Users Attempts)
     */
    public function getStatistics($id): JsonResponse
    {
        try {
            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            // Lấy danh sách quiz thuộc bài học này
            // Sử dụng quan hệ quizzes() nếu đã định nghĩa trong model Lesson
            // Hoặc query trực tiếp nếu chưa định nghĩa: LessonQuiz::where('lesson_id', $id)->get()
            // Giả sử có model LessonQuiz và quan hệ quizzes
            $quizzes = \App\Models\LessonQuiz::where('lesson_id', $id)->get();
            $quizIds = $quizzes->pluck('id');

            // Tổng số câu hỏi
            // Giả sử LessonQuiz có quan hệ questions() hoặc đếm từ bảng questions
            $totalQuestions = \App\Models\LessonQuizQuestion::whereIn('quiz_id', $quizIds)->count();

            // Lấy tất cả lượt làm bài (attempts) của các quiz này
            $attempts = \App\Models\LessonQuizAttempt::whereIn('quiz_id', $quizIds)
                ->whereNotNull('completed_at') // Chỉ lấy bài đã nộp (hoàn thành)
                ->with(['user', 'quiz']) // Eager load relations
                ->get();

            $totalAttempts = $attempts->count();
            $avgScore = $totalAttempts > 0 ? $attempts->avg('score_earned') : 0;

            // --- 1. Phân bổ điểm số (0-4, 5-7, 8-9, 10) ---
            $scoreDistribution = [
                '0-4' => 0,
                '5-7' => 0,
                '8-9' => 0,
                '10' => 0
            ];

            // --- 2. Thống kê Pass/Fail ---
            $passFailRatio = [
                'passed' => 0,
                'failed' => 0
            ];

            // --- 3. Top học viên (Điểm cao nhất) ---
            // Group by user_id -> lấy attempt có điểm cao nhất của mỗi user
            $userBestAttempts = [];
            // Lưu tổng điểm và tổng điểm tối đa của từng user để tính % tổng
            $userAccumulatedScores = [];

            // --- 4. Thời gian làm bài trung bình ---
            $totalDurationSeconds = 0;

            foreach ($attempts as $attempt) {

                // Score dist
                $score = $attempt->score_earned;
                if ($score < 5) {
                    $scoreDistribution['0-4']++;
                } else if ($score < 8) {
                    $scoreDistribution['5-7']++;
                } else if ($score < 10) {
                    $scoreDistribution['8-9']++;
                } else {
                    $scoreDistribution['10']++;
                }

                // Pass/Fail
                if ($attempt->passed) {
                    $passFailRatio['passed']++;
                } else {
                    $passFailRatio['failed']++;
                }

                // Top students processing
                $uid = $attempt->user_id;
                // Tích lũy điểm cho tất cả attempt của user
                if (!isset($userAccumulatedScores[$uid])) {
                    $userAccumulatedScores[$uid] = [
                        'score_earned' => 0,
                        'max_possible_score' => 0
                    ];
                }
                $userAccumulatedScores[$uid]['score_earned'] += (float) $attempt->score_earned;
                $userAccumulatedScores[$uid]['max_possible_score'] += (float) $attempt->max_possible_score;

                if (!isset($userBestAttempts[$uid]) || $score > $userBestAttempts[$uid]['score_earned']) {
                    $userBestAttempts[$uid] = [
                        'user' => $attempt->user,
                        'score_earned' => $score,
                        'percent_score_earned' => round($attempt->percent_score_earned, 2),
                        'quiz_title' => $attempt->quiz->title ?? '-',
                        'completed_at' => $attempt->completed_at
                    ];
                }

                // Avg Duration
                if ($attempt->started_at && $attempt->completed_at) {
                    $totalDurationSeconds += $attempt->completed_at->diffInSeconds($attempt->started_at);
                }
            }

            $avgDuration = $totalAttempts > 0 ? round($totalDurationSeconds / $totalAttempts) : 0;

            // Format Top Students (Top 5)
            // Bổ sung total_percent_score = (tổng score_earned / tổng max_possible_score) * 100
            foreach ($userBestAttempts as $uid => &$bestAttempt) {
                $totalEarned = $userAccumulatedScores[$uid]['score_earned'] ?? 0;
                $totalMax = $userAccumulatedScores[$uid]['max_possible_score'] ?? 0;
                $bestAttempt['total_percent_score'] = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 2) : 0;
            }
            unset($bestAttempt);

            $topStudents = collect($userBestAttempts)->sortByDesc('score_earned')->take(5)->values();


            // --- 5. Thống kê theo từng Quiz (Thêm số lượng câu hỏi) ---
            // Eager load question count
            $quizzes->loadCount('questions');

            $quizStats = $quizzes->map(function ($quiz) use ($attempts) {
                $quizAttempts = $attempts->where('quiz_id', $quiz->id);
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'total_attempts' => $quizAttempts->count(),
                    'avg_score' => $quizAttempts->count() > 0 ? round($quizAttempts->avg('percent_score_earned'), 2) : 0,
                    'passed_count' => $quizAttempts->where('passed', true)->count(),
                    'question_count' => $quiz->questions_count
                ];
            });

            // --- 6. Thống kê chi tiết câu hỏi (Tất cả câu hỏi để vẽ biểu đồ) ---
            $allQuestionIds = \App\Models\LessonQuizQuestion::whereIn('quiz_id', $quizIds)->pluck('id');
            // Aggregate answers
            $rawAnswers = \App\Models\LessonQuizAttemptAnswer::whereIn('question_id', $allQuestionIds)
                ->selectRaw('question_id, count(*) as total, sum(case when is_correct = 1 then 1 else 0 end) as correct')
                ->groupBy('question_id')
                ->get();

            $questionStats = [];
            foreach($rawAnswers as $ans) {
                $ratio = $ans->total > 0 ? ($ans->correct / $ans->total) * 100 : 0;
                $questionStats[$ans->question_id] = [
                    'question_id' => $ans->question_id,
                    'total' => $ans->total,
                    'correct' => $ans->correct,
                    'ratio' => $ratio
                ];
            }

            // Lấy nội dung tất cả câu hỏi để vẽ biểu đồ
            $allQuestionsDetails = \App\Models\LessonQuizQuestion::whereIn('id', $allQuestionIds)
                ->get()
                ->map(function($q) use ($questionStats) {
                    $stat = $questionStats[$q->id] ?? ['total' => 0, 'correct' => 0, 'ratio' => 0];
                    return [
                        'id' => $q->id,
                        'question' => $q->question_text,
                        'short_question' => \Illuminate\Support\Str::limit($q->question_text, 20),
                        'total_answers' => $stat['total'],
                        'correct_count' => (int)$stat['correct'],
                        'correct_ratio' => round($stat['ratio'], 2)
                    ];
                })
                ->values();

             // Top 5 hardest for table (subset of above)
            $hardestQuestions = $allQuestionsDetails->sortBy('correct_ratio')->take(5)->values();


            // Thống kê lượt làm bài theo thời gian (theo ngày)
            $attemptsOverTime = $attempts->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            })->map(function ($group) {
                return $group->count();
            })->sortKeys();

            $chartAttemptsOverTime = [];
            foreach ($attemptsOverTime as $date => $count) {
                $chartAttemptsOverTime[] = ['date' => $date, 'count' => $count];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total_quizzes' => $quizzes->count(),
                    'total_questions' => $totalQuestions,
                    'total_attempts' => $totalAttempts,
                    'avg_score' => round($avgScore, 2),
                    'avg_duration_seconds' => $avgDuration,
                    'score_distribution' => $scoreDistribution,
                    'pass_fail_ratio' => $passFailRatio,
                    'attempts_over_time' => $chartAttemptsOverTime,
                    'top_students' => $topStudents,
                    'quiz_stats' => $quizStats,
                    'all_questions_stats' => $allQuestionsDetails, // New full dataset
                    'hardest_questions' => $hardestQuestions
                ]
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo bài học mới
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            $lessonData = [
                'course_id' => null,
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'duration' => null,
                'display_order' => 0,
                'created_by' => $request->user()->id ?? null,
            ];

            $lesson = Lesson::create($lessonData);


            // Sau khi tạo khóa học thành công, mới xử lý upload thumbnail nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('thumbnail')) {
                try {
                    $thumbnailFile = $request->file('thumbnail');
                    $extension = $thumbnailFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $thumbnailFile->storeAs('lesson/thumbnails', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/thumbnails/lesson_1_<slug title>_1697059200.jpg

                    // Cập nhật thumbnail vào khóa học đã tạo
                    $lesson->update(['thumbnail_path' => $publicUrl]);

                    // Refresh để lấy dữ liệu mới nhất
                    $lesson->refresh();
                } catch (\Exception $e) {
                    report($e);
                }
            } else {
                // Nếu không upload thumbnail, đặt giá trị thumbnail_path là null
                $lesson->update(['thumbnail_path' => '/static/defaults/video-not-available.jpg']);
            }

            // Lưu video_path nếu có
            // File video sẽ được upload qua route khác, nên ở đây chỉ lưu video_path tạm thời
            if (isset($body['video_path']) && $body['video_path']) {
                $lesson->update(['video_path' => $body['video_path']]);
            }

            $lesson->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tạo bài học thành công',
                'data' => $lesson->load('course')
            ], 201);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bài học
     */
    public function update(UpdateRequest $request, $id)
    {

        try {
            $body = $request->validated();

            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            $updateData = [
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'updated_by' => $request->user()->id ?? null,
            ];

            // Xử lý upload thumbnail nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('thumbnail')) {
                // Xóa thumbnail cũ nếu có
                if ($lesson->thumbnail_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->thumbnail_path);
                    Storage::disk('public')->delete($oldPath);
                }

                try {
                    $thumbnailFile = $request->file('thumbnail');
                    $extension = $thumbnailFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $thumbnailFile->storeAs('lesson/thumbnails', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/thumbnails/lesson_1_<slug title>_1697059200.jpg

                    // Cập nhật thumbnail vào khóa học đã tạo
                    $updateData['thumbnail_path'] = $publicUrl;
                } catch (\Exception $e) {
                    report($e);
                }
            }

            if (isset($body['video_path']) && $body['video_path'] != $lesson->video_path) {
                // Lưu video path cũ để xóa sau
                $oldVideoPath = $lesson->video_path;

                // Cập nhật video path mới
                $updateData['video_path'] = $body['video_path'];

                // Xóa video cũ nếu là file local storage (không phải URL bên ngoài hoặc background upload)
                // Chỉ xóa sau khi cập nhật thành công
                if ($oldVideoPath && $this->isLocalStorageFile($oldVideoPath)) {
                    $oldPath = str_replace('/storage/', '', $oldVideoPath);
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Ghi vào database
            $lesson->update($updateData);
            $lesson->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài học thành công',
                'data' => $lesson->load('course')
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bài học (soft delete)
     */
    public function destroy(DestroyRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            // Chỉ được phép xóa các bài học mồ côi
            $lessonIds = $body['lesson_ids'] ?? [];
            $lessons = Lesson::whereIn('id', $lessonIds)
                ->whereNull('course_id')
                ->get();

            foreach ($lessons as $lesson) {
                // Xóa file thumbnail nếu có
                if ($lesson->thumbnail_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->thumbnail_path);
                    Storage::disk('public')->delete($oldPath);
                }
                // Xóa file video nếu có
                if ($lesson->video_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->video_path);
                    Storage::disk('public')->delete($oldPath);
                }

                // Xóa thư mục HLS nếu có
                if ($lesson->hls_path) {
                    $oldPath = explode('playlist.m3u8', $lesson->hls_path)[0];
                    $folderPath = str_replace('/storage/', '', $oldPath);
                    Storage::disk('public')->deleteDirectory($folderPath);
                }

                // Xóa mềm
                // $lesson->delete();
                // Xóa cứng
                $lesson->forceDelete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa bài học thành công'
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra xem path có phải là file local storage không
     * Không xóa nếu là URL external hoặc placeholder background-upload
     */
    private function isLocalStorageFile($path)
    {
        if (empty($path)) {
            return false;
        }

        // Không xóa nếu là URL bên ngoài (http://, https://)
        if (preg_match('/^https?:\/\//', $path)) {
            return false;
        }

        // Không xóa nếu là placeholder background upload
        if (strpos($path, 'background-upload://') === 0) {
            return false;
        }

        // Chỉ xóa file local storage (bắt đầu bằng /storage/)
        return strpos($path, '/storage/') === 0;
    }
}

