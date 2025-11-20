<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameThumbnailToThumbnailPathAndAddCreatedByDeletedByToCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Bước 1: Đổi tên cột thumbnail thành thumbnail_path
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('thumbnail', 'thumbnail_path');
        });

        // Bước 2: Thêm các cột mới sau khi đã đổi tên
        Schema::table('courses', function (Blueprint $table) {
            // Thêm cột created_by
            $table->unsignedBigInteger('created_by')->nullable()->after('deleted_at');

            // Thêm cột deleted_by
            $table->unsignedBigInteger('deleted_by')->nullable()->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Bước 1: Xóa các cột và foreign keys đã thêm
        Schema::table('courses', function (Blueprint $table) {

            // Xóa các cột đã thêm
            $table->dropColumn(['created_by', 'deleted_by']);
        });

        // Bước 2: Đổi tên cột thumbnail_path về thumbnail
        Schema::table('courses', function (Blueprint $table) {
            $table->renameColumn('thumbnail_path', 'thumbnail');
        });
    }
}
