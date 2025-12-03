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

        // *** NEW: để dùng trong catch nếu cần xóa file đã lưu
        $storagePath = null;

        try {
            // Merge chunks and move merged file to final storage first
            $this->mergeChunkFiles($tmpDirectory, $mergedTempFile);
            $storagePath = $this->moveToFinalStorage($upload, $mergedTempFile);

            // Try to generate HLS but do NOT fail the whole job if HLS generation fails.
            // HLS is independent: we will log the error and continue processing other steps.
            $hlsPath = null;
            try {
                $hlsPath = $this->generateHLSPlaylist($storagePath);
            } catch (Throwable $hlsException) {
                Log::warning('HLS generation failed but continuing processing', [
                    'upload_id' => $upload->id,
                    'error' => $hlsException->getMessage(),
                ]);

                // Record HLS-specific error on the upload record but do not mark the job as failed
                try {
                    $upload->update([
                        'error_message' => 'HLS generation error: ' . $hlsException->getMessage(),
                    ]);
                } catch (Throwable $e) {
                    Log::warning('Failed to persist HLS error message to upload record', [
                        'upload_id' => $upload->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // keep $hlsPath as null and continue
                $hlsPath = null;
            }

            // Update lesson with video and (optional) HLS path. This step is independent of HLS success.
            $this->updateLessonVideo($upload, $storagePath, $hlsPath);

            // Mark upload as completed (even if HLS failed). Keep storage_path saved.
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

            // If a fatal error occurred (before or during moveToFinalStorage/update), try to clean stored video
            if ($storagePath && $upload->storage_disk) {
                try {
                    Storage::disk($upload->storage_disk)->delete($storagePath);
                    Log::info('Deleted uploaded video due to processing failure', [
                        'upload_id' => $upload->id,
                        'storage_path' => $storagePath,
                    ]);
                } catch (Throwable $e) {
                    Log::warning('Failed to delete uploaded video after failure', [
                        'upload_id' => $upload->id,
                        'storage_path' => $storagePath,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

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

        // Xóa video cũ nếu là file local storage
        if ($oldVideoPath && $this->isLocalStorageFile($oldVideoPath)) {
            try {
                $oldPath = str_replace('/storage/', '', $oldVideoPath);
                Storage::disk('public')->delete($oldPath);
                Log::info('Deleted old video file', [
                    'lesson_id' => $lesson->id,
                    'old_path' => $oldPath
                ]);
            } catch (Throwable $exception) {
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
                $hlsDirectory = dirname($oldHlsStoragePath);
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
     * CHỈ chấp nhận codec H.264 (h264/avc1). Không re-encode.
     */
    protected function generateHLSPlaylist(string $storagePath): ?string
    {
        try {
            $fullVideoPath = storage_path('app/public/' . $storagePath);

            if (!file_exists($fullVideoPath)) {
                Log::warning('Video file not found for HLS generation', [
                    'path' => $fullVideoPath
                ]);
                throw new \RuntimeException('Không tìm thấy file video để tạo HLS.');
            }

            // Tìm đường dẫn đến FFmpeg
            $ffmpegPath = $this->findFFmpegPath();
            if (!$ffmpegPath) {
                Log::error('FFmpeg not found in system');
                throw new \RuntimeException('Hệ thống không tìm thấy FFmpeg để xử lý video.');
            }

            // *** NEW: detect codec và CHỈ cho phép H.264
            $videoCodec = $this->detectVideoCodec($fullVideoPath, $ffmpegPath);

            // Danh sách codec cho phép (không cần re-encode)
            $allowedCodecs = ['h264', 'avc1', 'avc3'];

            if (!$videoCodec || !in_array($videoCodec, $allowedCodecs, true)) {
                $message = sprintf(
                    'UNSUPPORTED_CODEC: Hệ thống chỉ chấp nhận video H.264 (AVC). Codec hiện tại: %s',
                    $videoCodec ?: 'unknown'
                );

                Log::warning($message, [
                    'path' => $fullVideoPath,
                    'codec' => $videoCodec,
                ]);

                // Ném exception để job fail và không tạo HLS
                throw new \RuntimeException($message);
            }

            // Tạo thư mục HLS riêng cho video này
            $pathInfo = pathinfo($storagePath);
            $hlsDirectory      = $pathInfo['dirname'] . '/hls_' . $pathInfo['filename'];
            $hlsFullDirectory  = storage_path('app/public/' . $hlsDirectory);

            if (!File::isDirectory($hlsFullDirectory)) {
                File::makeDirectory($hlsFullDirectory, 0755, true);
            }

            $hlsPlaylistPath      = $hlsDirectory . '/playlist.m3u8';
            $hlsFullPlaylistPath  = storage_path('app/public/' . $hlsPlaylistPath);
            $segmentPattern       = $hlsFullDirectory . '/segment_%03d.ts';

            // GIỮ FAST-PATH: copy codec, KHÔNG re-encode
            $command = sprintf(
                '%s -i %s -c copy -bsf:v h264_mp4toannexb -hls_time 10 -hls_list_size 0 -hls_segment_filename %s -f hls %s 2>&1',
                escapeshellarg($ffmpegPath),
                escapeshellarg($fullVideoPath),
                escapeshellarg($segmentPattern),
                escapeshellarg($hlsFullPlaylistPath)
            );

            Log::info('Starting HLS conversion (fast copy mode)', [
                'command'     => $command,
                'video_path'  => $fullVideoPath,
                'video_codec' => $videoCodec,
            ]);

            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('FFmpeg HLS conversion failed', [
                    'return_code' => $returnCode,
                    'output'      => implode("\n", $output),
                    'codec'       => $videoCodec,
                ]);

                throw new \RuntimeException('Không thể tạo HLS từ video. Vui lòng thử lại với file H.264 (MP4).');
            }

            Log::info('HLS conversion completed successfully', [
                'hls_path'          => $hlsPlaylistPath,
                'segments_created'  => count(glob($hlsFullDirectory . '/*.ts')),
                'codec'             => $videoCodec,
            ]);

            return $hlsPlaylistPath;
        } catch (Throwable $exception) {
            // Ném tiếp cho handle() catch để job FAILED + ghi error_message
            throw $exception;
        }
    }

    /**
     * Phát hiện codec video (h264, av1, hevc, vp9, ...)
     * Ưu tiên dùng ffprobe, nếu không có thì fallback sang ffmpeg -i.
     */
    private function detectVideoCodec(string $fullVideoPath, string $ffmpegPath): ?string
    {
        $codec = null;
        $output = [];
        $returnCode = 0;

        // Thử đoán ffprobe từ ffmpegPath (thường /usr/bin/ffmpeg <-> /usr/bin/ffprobe)
        $ffprobePath = preg_replace('/ffmpeg$/', 'ffprobe', $ffmpegPath);

        if ($ffprobePath && is_file($ffprobePath) && is_executable($ffprobePath)) {
            $cmd = sprintf(
                '%s -v error -select_streams v:0 -show_entries stream=codec_name -of default=nokey=1:noprint_wrappers=1 %s 2>&1',
                escapeshellarg($ffprobePath),
                escapeshellarg($fullVideoPath)
            );
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && !empty($output[0])) {
                $codec = trim($output[0]);
            }
        }

        // Fallback: nếu ffprobe không chạy được, dùng ffmpeg -i và parse "Video: xxx"
        if (!$codec) {
            $output = [];
            $returnCode = 0;
            $cmd = sprintf(
                '%s -i %s -hide_banner 2>&1',
                escapeshellarg($ffmpegPath),
                escapeshellarg($fullVideoPath)
            );
            exec($cmd, $output, $returnCode);

            foreach ($output as $line) {
                if (stripos($line, 'Video:') !== false) {
                    if (preg_match('/Video:\s*([a-zA-Z0-9_]+)/', $line, $matches)) {
                        $codec = strtolower($matches[1]);
                        break;
                    }
                }
            }
        }

        if (!$codec) {
            Log::warning('Could not detect video codec', [
                'path' => $fullVideoPath,
            ]);
            return null;
        }

        Log::info('Detected video codec for HLS', [
            'path'  => $fullVideoPath,
            'codec' => $codec,
        ]);

        return $codec;
    }

    /**
     * Tìm đường dẫn đến FFmpeg
     */
    private function findFFmpegPath(): ?string
    {
        $possiblePaths = [
            '/opt/homebrew/bin/ffmpeg',  // Homebrew trên macOS Apple Silicon
            '/usr/local/bin/ffmpeg',     // Homebrew trên macOS Intel
            '/usr/bin/ffmpeg',           // Linux
            '/opt/local/bin/ffmpeg',     // MacPorts
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

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

        if (preg_match('/^https?:\/\//', $path)) {
            return false;
        }

        if (strpos($path, 'background-upload://') === 0) {
            return false;
        }

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
            Log::error('Failed to calculate video duration', [
                'error' => $exception->getMessage()
            ]);
            return null;
        }
    }
}
