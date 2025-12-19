<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAttemptAnswer;
use App\Models\LessonQuizOption;
use App\Models\LessonQuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Get some lessons to attach quizzes to
        $lessons = Lesson::inRandomOrder()->take(10)->get();

        if ($lessons->isEmpty()) {
            $this->command->info('No lessons found, skipping LessonQuizSeeder.');
            return;
        }

        // 2. Get some users to be students
        $students = User::inRandomOrder()->take(5)->get();

        DB::transaction(function () use ($lessons, $students) {
            foreach ($lessons as $lesson) {
                // Create 1-2 Quizzes per lesson
                $quizzes = LessonQuiz::factory()->count(rand(1, 2))->create([
                    'lesson_id' => $lesson->id,
                    'created_by' => 1, // Admin
                    'updated_by' => 1,
                ]);

                foreach ($quizzes as $quiz) {
                    // Create 3-5 Questions per Quiz
                    $questions = LessonQuizQuestion::factory()->count(rand(3, 5))->create([
                        'quiz_id' => $quiz->id,
                    ]);

                    foreach ($questions as $question) {
                        // Create 2-4 Options per Question
                        $options = LessonQuizOption::factory()->count(rand(2, 4))->create([
                            'question_id' => $question->id,
                        ]);

                        // Ensure at least one option is correct
                        $correctOption = $options->random();
                        $correctOption->update(['is_correct' => true]);
                    }

                    // Create attempts for students
                    if ($students->isNotEmpty()) {
                        foreach ($students as $student) {
                            // 50% chance student attempts this quiz
                            if (rand(0, 1)) {
                                $startedAt = now()->subDays(rand(1, 30));
                                $completedAt = (clone $startedAt)->addMinutes(rand(5, 20));

                                $attempt = LessonQuizAttempt::factory()->create([
                                    'quiz_id' => $quiz->id,
                                    'user_id' => $student->id,
                                    'max_points' => $quiz->max_points, // Calculate based on questions
                                    'started_at' => $startedAt,
                                    'completed_at' => $completedAt,
                                ]);

                                $pointsEarnedTotal = 0;

                                // Create answers for questions
                                foreach ($questions as $question) {
                                    $correctOptions = $question->correctOptions;
                                    $isCorrect = (bool)rand(0, 1);
                                    $points = $isCorrect ? $question->points : 0;
                                    $pointsEarnedTotal += $points;

                                    // Pick selected options
                                    $selectedIds = [];
                                    if ($isCorrect) {
                                        $selectedIds = $correctOptions->pluck('id')->toArray();
                                    } else {
                                        // Pick random wrong one
                                        $wrongOptions = $question->options->where('is_correct', false);
                                        if ($wrongOptions->isNotEmpty()) {
                                            $selectedIds[] = $wrongOptions->random()->id;
                                        }
                                    }

                                    LessonQuizAttemptAnswer::factory()->create([
                                        'attempt_id' => $attempt->id,
                                        'question_id' => $question->id,
                                        'selected_option_ids' => $selectedIds,
                                        'is_correct' => $isCorrect,
                                        'points_earned' => $points,
                                    ]);
                                }

                                // Update attempt score
                                $attempt->update([
                                    'points_earned' => $pointsEarnedTotal,
                                    'score' => ($attempt->max_points > 0) ? ($pointsEarnedTotal / $attempt->max_points) * 10 : 0,
                                    'passed' => $pointsEarnedTotal >= ($attempt->max_points * ($quiz->passing_percent_score / 100)),
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }
}
