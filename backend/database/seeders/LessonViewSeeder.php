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

        $users = User::all();
        $lessons = Lesson::all();

        if ($users->isEmpty()) {
            $this->command->warn('⚠ Chưa có users nào. Vui lòng chạy UserSeederDev hoặc UserSeederProduction trước.');
            return;
        }

        if ($lessons->isEmpty()) {
            $this->command->warn('⚠ Chưa có bài học nào. Vui lòng chạy LessonSeeder trước.');
            return;
        }

        $viewCount = 0;

        // Tạo dữ liệu xem bài học cho một số users
        foreach ($users as $user) {
            // Mỗi user sẽ xem một số bài học ngẫu nhiên
            $userLessons = $lessons->random(rand(3, min(10, $lessons->count())));
            
            foreach ($userLessons as $lesson) {
                // Kiểm tra xem đã có view chưa
                $lessonView = LessonView::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();

                if (!$lessonView) {
                    // Tạo view mới với dữ liệu ngẫu nhiên
                    $watchedDuration = rand(0, $lesson->duration);
                    $lastPosition = rand(0, min($watchedDuration, $lesson->duration));
                    $lastWatchedAt = Carbon::now()->subDays(rand(0, 30));

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

