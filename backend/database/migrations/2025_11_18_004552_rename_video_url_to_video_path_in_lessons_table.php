<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameVideoUrlToVideoPathInLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('lessons', 'video_url')) {
            DB::statement("ALTER TABLE lessons CHANGE COLUMN video_url video_path TEXT NULL");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('lessons', 'video_path')) {
            DB::statement("ALTER TABLE lessons CHANGE COLUMN video_path video_url TEXT NULL");
        }
    }
}
