<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonView;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LessonViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo dữ liệu xem bài học...');

        $users = User::orderBy('id')->get();
        $lessons = Lesson::orderBy('id')->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠ Chưa có users nào. Vui lòng chạy UserSeederDev hoặc UserSeederProduction trước.');
            return;
        }

        if ($lessons->isEmpty()) {
            $this->command->warn('⚠ Chưa có bài học nào. Vui lòng chạy LessonSeeder trước.');
            return;
        }

        $viewCount = 0;
        $lessonsCount = $lessons->count();

        // Tạo dữ liệu xem bài học với logic cố định
        foreach ($users as $userIndex => $user) {
            // Mỗi user sẽ xem 5 bài học (cố định)
            $numLessonsToView = 5;

            for ($i = 0; $i < $numLessonsToView; $i++) {
                // Tính index của lesson dựa trên user index và offset
                $lessonIndex = ($userIndex * $numLessonsToView + $i) % $lessonsCount;
                $lesson = $lessons[$lessonIndex];

                // Kiểm tra xem đã có view chưa
                if (!LessonView::where('user_id', $user->id)->where('lesson_id', $lesson->id)->exists()) {
                    // Tính watched_duration cố định (50-80% của duration)
                    $maxDuration = $lesson->duration > 0 ? $lesson->duration : 600;
                    // Đảm bảo maxDuration ít nhất là 120 để có khoảng từ 60 đến maxDuration
                    $safeMaxDuration = max(120, $maxDuration);
                    $range = $safeMaxDuration - 60;
                    $watchedDuration = 60 + (($userIndex * $numLessonsToView + $i) * 100) % $range;
                    // Giới hạn trong khoảng 60 đến maxDuration
                    if ($watchedDuration > $maxDuration) {
                        $watchedDuration = max(60, (int)($maxDuration * 0.8));
                    }

                    // Tính last_position cố định
                    $lastPosition = (int)($watchedDuration * 0.9);

                    // Tính last_watched_at cố định (trong 30 ngày gần đây)
                    $daysAgo = ($userIndex * $numLessonsToView + $i) % 30;
                    $lastWatchedAt = Carbon::now()->subDays($daysAgo);

                    LessonView::create([
                        'user_id' => $user->id,
                        'lesson_id' => $lesson->id,
                        'watched_duration' => $watchedDuration,
                        'last_position' => $lastPosition,
                        'last_watched_at' => $lastWatchedAt,
                    ]);

                    $viewCount++;
                }
            }
        }

        $this->command->info("✓ Hoàn thành! Đã tạo {$viewCount} bản ghi xem bài học.");
    }
}

