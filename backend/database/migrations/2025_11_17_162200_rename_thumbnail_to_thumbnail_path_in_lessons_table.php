<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameThumbnailToThumbnailPathInLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('lessons', 'thumbnail')) {
            DB::statement("ALTER TABLE lessons CHANGE COLUMN thumbnail thumbnail_path VARCHAR(255) NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('lessons', 'thumbnail_path')) {
            DB::statement("ALTER TABLE lessons CHANGE COLUMN thumbnail_path thumbnail VARCHAR(255) NULL");
        }
    }
}
