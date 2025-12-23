<?php

namespace App\Http\Controllers;

use App\Http\Requests\lessonQuizzes\StoreQuizRequest;
use App\Http\Requests\lessonQuizzes\UpdateQuizRequest;
use App\Http\Requests\lessonQuizzes\StoreQuestionRequest;
use App\Http\Requests\lessonQuizzes\UpdateQuestionRequest;
use App\Http\Requests\lessonQuizzes\SubmitAttemptRequest;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonQuizQuestion;
use App\Models\LessonQuizOption;
use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAttemptAnswer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonQuizController extends Controller
{
    /**
     * Lấy danh sách quiz của một lesson
     */
    public function index(Request $request, $lessonId): JsonResponse
    {
        try {
            $lesson = Lesson::find($lessonId);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            $quizzes = LessonQuiz::where('lesson_id', $lessonId)
                ->with(['questions.options'])
                ->orderBy('start_at_seconds')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $quizzes
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách tất cả quiz (Admin)
     */
    public function indexAll(Request $request): JsonResponse
    {
        try {
            $query = LessonQuiz::query()->with('lesson')->withCount('questions');

            // Filter by lesson_id
            if ($request->filled('lesson_id')) {
                $query->where('lesson_id', $request->lesson_id);
            }

            // Filter by search (title)
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhereHas('lesson', function ($q2) use ($search) {
                          $q2->where('title', 'like', "%{$search}%");
                      });
                });
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('order_by', 'desc');
            $validSortColumns = ['id', 'title', 'created_at', 'lesson_id', 'passing_percent_score', 'questions_count'];

            if (in_array($sortBy, $validSortColumns)) {

                if ($sortBy === 'questions_count') {
                    $query->orderBy('questions_count', $sortOrder);
                } else {
                    $query->orderBy($sortBy, $sortOrder);
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 50);
            $quizzes = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $quizzes
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết một quiz
     */
    public function show($quizId): JsonResponse
    {
        try {
            $quiz = LessonQuiz::with(['questions.options', 'lesson'])
                ->find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $quiz
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo quiz mới (có thể kèm câu hỏi và đáp án)
     */
    public function store(StoreQuizRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            DB::beginTransaction();

            // Tạo quiz
            $quiz = LessonQuiz::create([
                'lesson_id' => $body['lesson_id'],
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'passing_percent_score' => $body['passing_percent_score'] ?? 70,
                'is_required' => $body['is_required'] ?? true,
                'start_at_seconds' => $body['start_at_seconds'] ?? null,
                'max_questions' => $body['max_questions'] ?? 4,
                'is_active' => $body['is_active'] ?? true,
                'created_by' => $request->user()->id,
            ]);

            // Tạo câu hỏi nếu có
            if (isset($body['questions']) && is_array($body['questions'])) {
                foreach ($body['questions'] as $qIndex => $questionData) {
                    $question = LessonQuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['question_text'],
                        'question_type' => $questionData['question_type'],
                        'points' => $questionData['points'] ?? 1,
                        'display_order' => $questionData['display_order'] ?? $qIndex,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);

                    // Tạo đáp án cho câu hỏi
                    if (isset($questionData['options']) && is_array($questionData['options'])) {
                        foreach ($questionData['options'] as $oIndex => $optionData) {
                            LessonQuizOption::create([
                                'question_id' => $question->id,
                                'option_text' => $optionData['option_text'],
                                'is_correct' => $optionData['is_correct'] ?? false,
                                'display_order' => $optionData['display_order'] ?? $oIndex,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // Load relationships
            $quiz->load(['questions.options', 'lesson']);

            return response()->json([
                'success' => true,
                'message' => 'Tạo quiz thành công',
                'data' => $quiz
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật thông tin quiz
     */
    public function update(UpdateQuizRequest $request, $quizId): JsonResponse
    {
        try {
            // Lấy toàn bộ payload (FormRequest vẫn validate các field được khai báo trong rules)
            $body = $request->all();

            $quiz = LessonQuiz::with('questions.options')->find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            DB::beginTransaction();

            // Cập nhật các field của quiz
            $updateData = array_filter([
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'passing_percent_score' => $body['passing_percent_score'] ?? null,
                'is_required' => $body['is_required'] ?? null,
                'start_at_seconds' => $body['start_at_seconds'] ?? null,
                'max_questions' => $body['max_questions'] ?? null,
                'is_active' => $body['is_active'] ?? null,
                'updated_by' => $request->user()->id,
            ], function ($value) {
                return $value !== null; // Loại bỏ các thuộc tính có giá trị null
            });

            if (!empty($updateData)) {
                $quiz->update($updateData);
            }

            // Nếu payload có kèm danh sách câu hỏi, ta cập nhật / tạo mới / xóa theo diff
            if (isset($body['questions']) && is_array($body['questions'])) {
                $existingQuestions = $quiz->questions->keyBy('id');
                $keptQuestionIds = [];

                foreach ($body['questions'] as $qIndex => $questionData) {
                    $questionId = $questionData['id'] ?? null;

                    if ($questionId && $existingQuestions->has($questionId)) {
                        // Cập nhật câu hỏi cũ
                        /** @var LessonQuizQuestion $question */
                        $question = $existingQuestions->get($questionId);
                        $question->update([
                            'question_text' => $questionData['question_text'],
                            'question_type' => $questionData['question_type'],
                            'points' => $questionData['points'] ?? $question->points,
                            'display_order' => $questionData['display_order'] ?? $qIndex,
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                    } else {
                        // Tạo câu hỏi mới
                        $question = LessonQuizQuestion::create([
                            'quiz_id' => $quiz->id,
                            'question_text' => $questionData['question_text'],
                            'question_type' => $questionData['question_type'],
                            'points' => $questionData['points'] ?? 1,
                            'display_order' => $questionData['display_order'] ?? $qIndex,
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                    }

                    $keptQuestionIds[] = $question->id;

                    // Cập nhật / tạo / xóa đáp án cho từng câu hỏi
                    $existingOptions = $question->options->keyBy('id');
                    $keptOptionIds = [];

                    if (isset($questionData['options']) && is_array($questionData['options'])) {
                        foreach ($questionData['options'] as $oIndex => $optionData) {
                            $optionId = $optionData['id'] ?? null;

                            if ($optionId && $existingOptions->has($optionId)) {
                                /** @var LessonQuizOption $option */
                                $option = $existingOptions->get($optionId);
                                $option->update([
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'] ?? $option->is_correct,
                                    'display_order' => $optionData['display_order'] ?? $oIndex,
                                ]);
                            } else {
                                $option = LessonQuizOption::create([
                                    'question_id' => $question->id,
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'] ?? false,
                                    'display_order' => $optionData['display_order'] ?? $oIndex,
                                ]);
                            }

                            $keptOptionIds[] = $option->id;
                        }
                    }

                    // Xóa các options không còn trong payload
                    if (!empty($keptOptionIds)) {
                        $question->options()->whereNotIn('id', $keptOptionIds)->delete();
                    } else {
                        // Nếu không còn option nào được gửi lên cho câu hỏi này thì xóa hết options
                        $question->options()->delete();
                    }
                }

                // Xóa các câu hỏi không còn trong payload (và options của chúng)
                if (!empty($keptQuestionIds)) {
                    $quiz->questions()
                        ->whereNotIn('id', $keptQuestionIds)
                        ->get()
                        ->each(function (LessonQuizQuestion $q) {
                            $q->options()->delete();
                            $q->delete();
                        });
                }
            }

            DB::commit();

            $quiz->load(['questions.options', 'lesson']);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật quiz thành công',
                'data' => $quiz
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa quiz (soft delete)
     */
    public function destroy(Request $request, $quizId): JsonResponse
    {
        try {
            $quiz = LessonQuiz::find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            DB::beginTransaction();

            // Xóa các options của câu hỏi
            $questionIds = $quiz->questions()->pluck('id');
            LessonQuizOption::whereIn('question_id', $questionIds)->delete();

            // Xóa câu hỏi
            $quiz->questions()->delete();

            // Xóa quiz
            $quiz->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa quiz thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // QUẢN LÝ CÂU HỎI
    // ========================================

    /**
     * Thêm câu hỏi vào quiz
     */
    public function storeQuestion(StoreQuestionRequest $request, $quizId): JsonResponse
    {
        try {
            $body = $request->validated();

            $quiz = LessonQuiz::find($quizId);

            if (!$quiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy quiz'
                ], 404);
            }

            // Kiểm tra số lượng câu hỏi
            $currentCount = $quiz->questions()->count();
            if ($currentCount >= $quiz->max_questions) {
                return response()->json([
                    'success' => false,
                    'message' => "Quiz này chỉ cho phép tối đa {$quiz->max_questions} câu hỏi"
                ], 400);
            }

            DB::beginTransaction();

            // Tạo câu hỏi
            $question = LessonQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_text' => $body['question_text'],
                'question_type' => $body['question_type'],
                'points' => $body['points'] ?? 1,
                'display_order' => $body['display_order'] ?? $currentCount,
                'explanation' => $body['explanation'] ?? null,
            ]);

            // Tạo đáp án
            foreach ($body['options'] as $index => $optionData) {
                LessonQuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['option_text'],
                    'is_correct' => $optionData['is_correct'] ?? false,
                    'display_order' => $optionData['display_order'] ?? $index,
                ]);
            }

            DB::commit();

            $question->load('options');

            return response()->json([
                'success' => true,
                'message' => 'Thêm câu hỏi thành công',
                'data' => $question
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm câu hỏi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật câu hỏi
     */
    public function updateQuestion(UpdateQuestionRequest $request, $questionId): JsonResponse
    {
        try {
            $body = $request->validated();

            $question = LessonQuizQuestion::with('options')->find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy câu hỏi'
                ], 404);
            }

            DB::beginTransaction();

            // Cập nhật câu hỏi
            $updateData = array_filter([
                'question_text' => $body['question_text'] ?? null,
                'question_type' => $body['question_type'] ?? null,
                'points' => $body['points'] ?? null,
                'display_order' => $body['display_order'] ?? null,
                'explanation' => $body['explanation'] ?? null,
            ], function ($value) {
                return $value !== null;
            });

            if (!empty($updateData)) {
                $question->update($updateData);
            }

            // Cập nhật đáp án nếu có
            if (isset($body['options']) && is_array($body['options'])) {

                $existingOptions = $question->options->keyBy('id');
                $keptOptionIds = [];

                foreach ($body['options'] as $oIndex => $optionData) {
                    $optionId = $optionData['id'] ?? null;

                    if ($optionId && $existingOptions->has($optionId)) {
                        $option = $existingOptions->get($optionId);
                        $option->update([
                            'option_text' => $optionData['option_text'],
                            'is_correct' => $optionData['is_correct'] ?? false,
                            'display_order' => $optionData['display_order'] ?? $oIndex,
                        ]);

                        $keptOptionIds[] = $option->id;
                    }

                }

                if (!empty($keptOptionIds)) {
                    $question->options()->whereNotIn('id', $keptOptionIds)->delete();
                } else {
                    // Nếu không còn option nào được gửi lên cho câu hỏi này thì xóa hết options
                    $question->options()->delete();
                }
            }

            DB::commit();

            $question->load('options');

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật câu hỏi thành công',
                'data' => $question
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật câu hỏi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa câu hỏi
     */
    public function destroyQuestion(Request $request, $questionId): JsonResponse
    {
        try {
            $question = LessonQuizQuestion::find($questionId);

            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy câu hỏi'
                ], 404);
            }

            DB::beginTransaction();

            // Xóa đáp án
            $question->options()->delete();

            // Xóa câu hỏi
            $question->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa câu hỏi thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa câu hỏi: ' . $e->getMessage()
            ], 500);
        }
    }

}

