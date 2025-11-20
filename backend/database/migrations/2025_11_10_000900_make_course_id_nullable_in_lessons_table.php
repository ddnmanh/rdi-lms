<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeCourseIdNullableInLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bỏ ràng buộc FK hiện tại (nếu tồn tại) và đổi cột course_id sang NULLABLE
        // Không thêm lại bất kỳ khóa ngoại nào (loại bỏ ràng buộc ở DB)
        if (Schema::hasTable('lessons')) {
            // Xóa FK cũ (tên theo file tạo bảng: fk_lesson_course)
            // Sử dụng try-catch vì FK có thể không tồn tại
            try {
                DB::statement('ALTER TABLE `lessons` DROP FOREIGN KEY `fk_lesson_course`');
            } catch (\Throwable $e) {
                // FK không tồn tại, bỏ qua
            }

            // Đổi cột sang NULLABLE
            DB::statement('ALTER TABLE `lessons` MODIFY `course_id` BIGINT UNSIGNED NULL');
            // Không thêm lại FK
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('lessons')) {
            // Đổi cột về NOT NULL
            DB::statement('ALTER TABLE `lessons` MODIFY `course_id` BIGINT UNSIGNED NOT NULL');

            // Thêm lại FK với ON DELETE CASCADE như ban đầu
            DB::statement('ALTER TABLE `lessons` ADD CONSTRAINT `fk_lesson_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE');
        }
    }
}


