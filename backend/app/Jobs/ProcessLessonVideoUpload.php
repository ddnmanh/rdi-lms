<?php

namespace App\Jobs;

use App\Models\Lesson;
use App\Models\LessonVideoUpload;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessLessonVideoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $uploadId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $uploadId)
    {
        $this->uploadId = $uploadId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var LessonVideoUpload|null $upload */
        $upload = LessonVideoUpload::find($this->uploadId);

        if (!$upload || $upload->status === LessonVideoUpload::STATUS_COMPLETED) {
            return;
        }

        $upload->update([
            'status' => LessonVideoUpload::STATUS_PROCESSING,
            'processing_started_at' => Carbon::now(),
            'error_message' => null,
        ]);

        $tmpDirectory = storage_path('app/' . $upload->temp_directory);

        if (!File::isDirectory($tmpDirectory)) {
            $upload->update([
                'status' => LessonVideoUpload::STATUS_FAILED,
                'error_message' => 'Không tìm thấy thư mục chunk tạm thời.',
            ]);
            return;
        }

        $mergedTempFile = $tmpDirectory . '.merged';

        try {
            $this->mergeChunkFiles($tmpDirectory, $mergedTempFile);
            $storagePath = $this->moveToFinalStorage($upload, $mergedTempFile);
            $hlsPath = $this->generateHLSPlaylist($storagePath);
            $this->updateLessonVideo($upload, $storagePath, $hlsPath);

            $upload->update([
                'status' => LessonVideoUpload::STATUS_COMPLETED,
                'storage_path' => $storagePath,
                'processing_finished_at' => Carbon::now(),
            ]);
        } catch (Throwable $exception) {
            Log::error('ProcessLessonVideoUpload failed', [
                'upload_id' => $upload->id,
                'error' => $exception->getMessage(),
            ]);

            $upload->update([
                'status' => LessonVideoUpload::STATUS_FAILED,
                'error_message' => $exception->getMessage(),
                'processing_finished_at' => Carbon::now(),
            ]);
        } finally {
            if (File::exists($mergedTempFile)) {
                File::delete($mergedTempFile);
            }
            if (File::isDirectory($tmpDirectory)) {
                File::deleteDirectory($tmpDirectory);
            }
        }
    }

    protected function mergeChunkFiles(string $tmpDirectory, string $mergedTempFile): void
    {
        $chunkFiles = collect(File::files($tmpDirectory))
            ->sortBy(fn ($file) => $file->getFilename())
            ->values();

        $output = fopen($mergedTempFile, 'w+b');

        foreach ($chunkFiles as $chunkFile) {
            $input = fopen($chunkFile->getPathname(), 'rb');
            stream_copy_to_stream($input, $output);
            fclose($input);
        }

        fclose($output);
    }

    protected function moveToFinalStorage(LessonVideoUpload $upload, string $mergedTempFile): string
    {
        $extension = pathinfo($upload->original_name, PATHINFO_EXTENSION);
        $slug = Str::slug(pathinfo($upload->original_name, PATHINFO_FILENAME));
        $fileName = sprintf(
            'lesson_%s_%s.%s',
            $upload->lesson_id ?? 'independent',
            $slug ?: 'video',
            $extension ?: 'mp4'
        );

        $storagePath = 'lesson/videos/' . now()->timestamp . '_' . $fileName;
        $disk = Storage::disk($upload->storage_disk);

        $stream = fopen($mergedTempFile, 'rb');
        $disk->put($storagePath, $stream);
        fclose($stream);

        return $storagePath;
    }

    protected function updateLessonVideo(LessonVideoUpload $upload, string $storagePath, ?string $hlsPath = null): void
    {
        if (!$upload->lesson_id) {
            return;
        }

        $lesson = Lesson::find($upload->lesson_id);

        if (!$lesson) {
            return;
        }

        // Lưu lại video path cũ để xóa sau
        $oldVideoPath = $lesson->video_path;
        $oldHlsPath = $lesson->hls_path;

        // Cập nhật video path mới
        $publicUrl = Storage::url($storagePath);
        
        // Tính toán duration video
        $duration = $this->calculateVideoDuration($storagePath);
        
        // Cập nhật lesson với video path, hls path và duration
        $updateData = [
            'video_path' => $publicUrl,
            'duration' => $duration,
        ];
        
        if ($hlsPath) {
            $updateData['hls_path'] = Storage::url($hlsPath);
        }
        
        $lesson->update($updateData);

        // Xóa video cũ nếu là file local storage (không phải URL bên ngoài hoặc background upload placeholder)
        // Chỉ xóa sau khi cập nhật thành công
        if ($oldVideoPath && $this->isLocalStorageFile($oldVideoPath)) {
            try {
                $oldPath = str_replace('/storage/', '', $oldVideoPath);
                Storage::disk('public')->delete($oldPath);
                Log::info('Deleted old video file', [
                    'lesson_id' => $lesson->id,
                    'old_path' => $oldPath
                ]);
            } catch (Throwable $exception) {
                // Log lỗi nhưng không fail job
                Log::warning('Failed to delete old video file', [
                    'lesson_id' => $lesson->id,
                    'old_path' => $oldVideoPath,
                    'error' => $exception->getMessage()
                ]);
            }
        }
        
        // Xóa HLS playlist cũ nếu có
        if ($oldHlsPath && $this->isLocalStorageFile($oldHlsPath)) {
            try {
                $oldHlsStoragePath = str_replace('/storage/', '', $oldHlsPath);
                // Lấy thư mục chứa file .m3u8
                $hlsDirectory = dirname($oldHlsStoragePath);
                // Xóa toàn bộ thư mục HLS (bao gồm .m3u8 và các .ts segments)
                Storage::disk('public')->deleteDirectory($hlsDirectory);
                Log::info('Deleted old HLS directory', [
                    'lesson_id' => $lesson->id,
                    'old_hls_directory' => $hlsDirectory
                ]);
            } catch (Throwable $exception) {
                Log::warning('Failed to delete old HLS directory', [
                    'lesson_id' => $lesson->id,
                    'old_hls_path' => $oldHlsPath,
                    'error' => $exception->getMessage()
                ]);
            }
        }
    }

    /**
     * Tạo HLS playlist từ video đã merge
     * Sử dụng FFmpeg để convert video sang định dạng HLS
     */
    protected function generateHLSPlaylist(string $storagePath): ?string
    {
        try {
            $fullVideoPath = storage_path('app/public/' . $storagePath);

            if (!file_exists($fullVideoPath)) {
                Log::warning('Video file not found for HLS generation', [
                    'path' => $fullVideoPath
                ]);
                return null;
            }

            // Tạo thư mục HLS riêng cho video này
            $pathInfo = pathinfo($storagePath);
            $hlsDirectory = $pathInfo['dirname'] . '/hls_' . $pathInfo['filename'];
            $hlsFullDirectory = storage_path('app/public/' . $hlsDirectory);

            if (!File::isDirectory($hlsFullDirectory)) {
                File::makeDirectory($hlsFullDirectory, 0755, true);
            }

            $hlsPlaylistPath = $hlsDirectory . '/playlist.m3u8';
            $hlsFullPlaylistPath = storage_path('app/public/' . $hlsPlaylistPath);

            // Sử dụng FFmpeg để tạo HLS playlist
            // -c copy: copy codec (không re-encode) - nhanh nhất
            // -hls_time 10: mỗi segment 10 giây
            // -hls_list_size 0: giữ tất cả segments trong playlist
            // -hls_segment_filename: pattern cho tên file segment
            // -f hls: output format HLS
            $segmentPattern = $hlsFullDirectory . '/segment_%03d.ts';
            
            // Tìm đường dẫn đến FFmpeg
            $ffmpegPath = $this->findFFmpegPath();
            
            if (!$ffmpegPath) {
                Log::error('FFmpeg not found in system');
                return null;
            }
            
            // Sử dụng -c copy để không re-encode, chỉ remux sang HLS
            // Nếu video đã là H.264/AAC thì sẽ rất nhanh (vài giây)
            $command = sprintf(
                '%s -i %s -c copy -bsf:v h264_mp4toannexb -hls_time 10 -hls_list_size 0 -hls_segment_filename %s -f hls %s 2>&1',
                escapeshellarg($ffmpegPath),
                escapeshellarg($fullVideoPath),
                escapeshellarg($segmentPattern),
                escapeshellarg($hlsFullPlaylistPath)
            );

            Log::info('Starting HLS conversion', [
                'command' => $command,
                'video_path' => $fullVideoPath
            ]);

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('FFmpeg HLS conversion failed', [
                    'return_code' => $returnCode,
                    'output' => implode("\n", $output)
                ]);
                return null;
            }

            Log::info('HLS conversion completed successfully', [
                'hls_path' => $hlsPlaylistPath,
                'segments_created' => count(glob($hlsFullDirectory . '/*.ts'))
            ]);

            return $hlsPlaylistPath;
        } catch (Throwable $exception) {
            Log::error('Failed to generate HLS playlist', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Tìm đường dẫn đến FFmpeg
     */
    private function findFFmpegPath(): ?string
    {
        // Các đường dẫn phổ biến để tìm FFmpeg
        $possiblePaths = [
            '/opt/homebrew/bin/ffmpeg',  // Homebrew trên macOS Apple Silicon
            '/usr/local/bin/ffmpeg',      // Homebrew trên macOS Intel
            '/usr/bin/ffmpeg',            // Linux
            '/opt/local/bin/ffmpeg',      // MacPorts
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        // Thử tìm trong PATH
        exec('which ffmpeg 2>/dev/null', $output, $returnCode);
        if ($returnCode === 0 && !empty($output[0])) {
            return trim($output[0]);
        }

        return null;
    }

    /**
     * Kiểm tra xem path có phải là file local storage không
     * Không xóa nếu là URL external hoặc placeholder background-upload
     */
    private function isLocalStorageFile($path)
    {
        if (empty($path)) {
            return false;
        }

        // Không xóa nếu là URL bên ngoài (http://, https://)
        if (preg_match('/^https?:\/\//', $path)) {
            return false;
        }

        // Không xóa nếu là placeholder background upload
        if (strpos($path, 'background-upload://') === 0) {
            return false;
        }

        // Chỉ xóa file local storage (bắt đầu bằng /storage/)
        return strpos($path, '/storage/') === 0;
    }

    /**
     * Tính toán duration của video
     */
    private function calculateVideoDuration(string $storagePath): ?int
    {
        try {
            $fullPath = storage_path('app/public/' . $storagePath);

            if (!file_exists($fullPath)) {
                Log::warning('Video file not found for duration calculation', [
                    'path' => $fullPath
                ]);
                return null;
            }

            $getID3 = new \getID3();
            $fileInfo = $getID3->analyze($fullPath);

            if (isset($fileInfo['playtime_seconds'])) {
                $durationInSeconds = (int) round($fileInfo['playtime_seconds']);

                Log::info('Video duration calculated successfully', [
                    'duration' => $durationInSeconds,
                    'formatted' => gmdate('H:i:s', $durationInSeconds)
                ]);

                return $durationInSeconds;
            } else {
                Log::warning('Could not determine video duration', [
                    'file_info' => $fileInfo
                ]);
                return null;
            }
        } catch (Throwable $exception) {
            // Log lỗi nhưng không fail job
            Log::error('Failed to calculate video duration', [
                'error' => $exception->getMessage()
            ]);
            return null;
        }
    }
}
