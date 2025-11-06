<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedInteger('watched_duration')->default(0);
            $table->unsignedInteger('last_position')->default(0)->comment('giây để resume');
            $table->timestamp('last_watched_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'lesson_id'], 'uq_lv');
            $table->index('user_id', 'idx_lv_user');
            $table->foreign('user_id', 'fk_lv_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('lesson_id', 'fk_lv_lesson')
                ->references('id')
                ->on('lessons')
                ->onDelete('cascade');
            
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_0900_ai_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_views');
    }
}

