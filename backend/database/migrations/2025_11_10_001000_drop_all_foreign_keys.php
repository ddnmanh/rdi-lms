<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropAllForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Loại bỏ toàn bộ ràng buộc khóa ngoại đã đặt tên trong các migration trước
        // Nếu khóa không tồn tại, câu lệnh có thể lỗi tùy môi trường; chạy từng nhóm độc lập

        if (Schema::hasTable('lessons')) {
            // từ 2024_01_01_000007_create_lessons_table.php
            try { DB::statement('ALTER TABLE `lessons` DROP FOREIGN KEY `fk_lesson_course`'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('lesson_views')) {
            // từ 2024_01_01_000008_create_lesson_views_table.php
            try { DB::statement('ALTER TABLE `lesson_views` DROP FOREIGN KEY `fk_lv_user`'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE `lesson_views` DROP FOREIGN KEY `fk_lv_lesson`'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('course_user')) {
            // từ 2024_01_01_000006_create_course_user_table.php
            try { DB::statement('ALTER TABLE `course_user` DROP FOREIGN KEY `fk_cu_user`'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE `course_user` DROP FOREIGN KEY `fk_cu_course`'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('role_permission')) {
            // từ 2024_01_01_000004_create_role_permission_table.php
            try { DB::statement('ALTER TABLE `role_permission` DROP FOREIGN KEY `fk_rp_role`'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE `role_permission` DROP FOREIGN KEY `fk_rp_perm`'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('user_role')) {
            // từ 2024_01_01_000003_create_user_role_table.php
            try { DB::statement('ALTER TABLE `user_role` DROP FOREIGN KEY `fk_user_role_user`'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE `user_role` DROP FOREIGN KEY `fk_user_role_role`'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('refresh_tokens')) {
            // từ 2025_11_06_070349_create_refresh_tokens_table.php
            try { DB::statement('ALTER TABLE `refresh_tokens` DROP FOREIGN KEY `fk_rt_user`'); } catch (\Throwable $e) {}
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


