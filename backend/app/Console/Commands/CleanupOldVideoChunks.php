<?php

namespace App\Console\Commands;

use App\Models\LessonVideoUpload;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanupOldVideoChunks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:cleanup-chunks {--hours=24 : Xóa chunk cũ hơn số giờ này}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa các file chunk video cũ chưa được ghép hoàn chỉnh';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $this->info("Bắt đầu quét và xóa các chunk cũ hơn {$hours} giờ...");

        $cutoffTime = Carbon::now()->subHours($hours);
        $deletedCount = 0;
        $freedSpace = 0;

        // Tìm các upload session cũ chưa hoàn thành (PENDING, UPLOADING, FAILED)
        $oldUploads = LessonVideoUpload::whereIn('status', [
            LessonVideoUpload::STATUS_PENDING,
            LessonVideoUpload::STATUS_UPLOADING,
            LessonVideoUpload::STATUS_FAILED,
        ])
            ->where('created_at', '<', $cutoffTime)
            ->get();

        $this->info("Tìm thấy {$oldUploads->count()} upload session cũ cần xử lý.");

        foreach ($oldUploads as $upload) {
            $tmpDirectory = storage_path('app/' . $upload->temp_directory);

            if (File::isDirectory($tmpDirectory)) {
                try {
                    // Tính tổng kích thước trước khi xóa
                    $size = $this->getDirectorySize($tmpDirectory);
                    $freedSpace += $size;

                    // Xóa thư mục chunk
                    File::deleteDirectory($tmpDirectory);
                    $deletedCount++;

                    $this->line("✓ Đã xóa: {$upload->temp_directory} (" . $this->formatBytes($size) . ")");

                    Log::info('Cleaned up old video chunks', [
                        'upload_id' => $upload->id,
                        'temp_directory' => $upload->temp_directory,
                        'size' => $size,
                        'status' => $upload->status,
                        'created_at' => $upload->created_at,
                    ]);

                    // Cập nhật status thành FAILED nếu đang PENDING hoặc UPLOADING
                    if (in_array($upload->status, [
                        LessonVideoUpload::STATUS_PENDING,
                        LessonVideoUpload::STATUS_UPLOADING,
                    ])) {
                        $upload->update([
                            'status' => LessonVideoUpload::STATUS_FAILED,
                            'error_message' => 'Upload bị hủy do quá thời gian chờ.',
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->error("✗ Lỗi khi xóa {$upload->temp_directory}: {$e->getMessage()}");
                    Log::error('Failed to cleanup video chunks', [
                        'upload_id' => $upload->id,
                        'temp_directory' => $upload->temp_directory,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Xóa các thư mục chunk rác không có record trong database
        $this->cleanupOrphanedChunks($cutoffTime, $deletedCount, $freedSpace);

        $this->info("\n=== Kết quả ===");
        $this->info("Tổng số thư mục đã xóa: {$deletedCount}");
        $this->info("Dung lượng giải phóng: " . $this->formatBytes($freedSpace));
        $this->info("Hoàn tất!");

        return Command::SUCCESS;
    }

    /**
     * Xóa các thư mục chunk không có record trong database (orphaned chunks)
     */
    protected function cleanupOrphanedChunks(Carbon $cutoffTime, int &$deletedCount, int &$freedSpace): void
    {
        $baseDir = storage_path('app/lesson-video-uploads');

        if (!File::isDirectory($baseDir)) {
            return;
        }

        $this->line("\nQuét các thư mục chunk không có record trong database...");

        $directories = File::directories($baseDir);

        foreach ($directories as $directory) {
            $directoryName = basename($directory);
            $fullPath = "lesson-video-uploads/{$directoryName}";

            // Kiểm tra xem có record trong database không
            $exists = LessonVideoUpload::where('temp_directory', $fullPath)->exists();

            if (!$exists) {
                // Kiểm tra thời gian tạo thư mục
                $modifiedTime = Carbon::createFromTimestamp(File::lastModified($directory));

                if ($modifiedTime->lt($cutoffTime)) {
                    try {
                        $size = $this->getDirectorySize($directory);
                        $freedSpace += $size;

                        File::deleteDirectory($directory);
                        $deletedCount++;

                        $this->line("✓ Đã xóa thư mục rác: {$fullPath} (" . $this->formatBytes($size) . ")");

                        Log::info('Cleaned up orphaned video chunks', [
                            'directory' => $fullPath,
                            'size' => $size,
                            'modified_at' => $modifiedTime,
                        ]);
                    } catch (\Exception $e) {
                        $this->error("✗ Lỗi khi xóa {$fullPath}: {$e->getMessage()}");
                    }
                }
            }
        }
    }

    /**
     * Tính tổng kích thước của thư mục
     */
    protected function getDirectorySize(string $directory): int
    {
        $size = 0;

        foreach (File::allFiles($directory) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    /**
     * Format bytes thành đơn vị dễ đọc
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $value = $bytes;

        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }

        return round($value, 2) . ' ' . $units[$i];
    }
}
