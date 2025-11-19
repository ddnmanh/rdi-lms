<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLessonVideoUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lesson_video_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            $table->unsignedInteger('uploaded_chunks')->default(0);
            $table->enum('status', [
                'pending',
                'uploading',
                'processing',
                'completed',
                'failed'
            ])->default('pending');
            $table->string('storage_disk')->default('public');
            $table->string('storage_path')->nullable();
            $table->string('temp_directory');
            $table->text('error_message')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_finished_at')->nullable();
            $table->timestamps();

            $table->index('lesson_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lesson_video_uploads');
    }
}
