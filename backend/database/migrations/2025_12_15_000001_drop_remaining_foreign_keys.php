<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropRemainingForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign keys từ bảng lesson_video_uploads
        if (Schema::hasTable('lesson_video_uploads')) {
            // Drop foreign key constraint cho lesson_id
            try {
                DB::statement('ALTER TABLE `lesson_video_uploads` DROP FOREIGN KEY `lesson_video_uploads_lesson_id_foreign`');
            } catch (\Throwable $e) {
                // Foreign key có thể không tồn tại
            }

            // Drop foreign key constraint cho user_id
            try {
                DB::statement('ALTER TABLE `lesson_video_uploads` DROP FOREIGN KEY `lesson_video_uploads_user_id_foreign`');
            } catch (\Throwable $e) {
                // Foreign key có thể không tồn tại
            }
        }

        // Drop foreign keys từ bảng lessons
        if (Schema::hasTable('lessons')) {
            // Drop foreign key constraint cho updated_by -> users
            try {
                DB::statement('ALTER TABLE `lessons` DROP FOREIGN KEY `lessons_updated_by_foreign`');
            } catch (\Throwable $e) {
                // Foreign key có thể không tồn tại
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Không khôi phục lại các khóa ngoại để giữ nguyên chủ trương "không FK ở DB"
    }
}

