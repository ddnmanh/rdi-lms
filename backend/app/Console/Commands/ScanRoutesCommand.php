<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ScanRoutesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'routes:scan
                            {--prefix=api : Chỉ quét routes có prefix này}
                            {--force : Xóa tất cả permissions cũ trước khi quét}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quét tất cả routes và thêm vào bảng permissions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $prefix = $this->option('prefix');
        $force = $this->option('force');

        $this->info("Bắt đầu quét routes với prefix: /{$prefix}");

        // Xóa tất cả permissions cũ nếu có option --force
        if ($force) {
            $this->warn("Đang xóa tất cả permissions cũ...");
            Permission::withTrashed()->forceDelete();
            $this->info("Đã xóa tất cả permissions cũ.");
        }

        // Lấy tất cả routes
        $routes = Route::getRoutes();
        $skipped = 0;
        $added = 0;
        $updated = 0;

        foreach ($routes as $route) {
            // Lấy URI đầy đủ
            $uri = $route->uri();

            // Chỉ lấy routes có prefix mong muốn
            // Kiểm tra cả với và không có leading slash
            $prefixWithSlash = '/' . ltrim($prefix, '/');
            if (!Str::startsWith($uri, $prefix) && !Str::startsWith($uri, $prefixWithSlash)) {
                continue;
            }

            // Lấy method (GET, POST, PUT, DELETE, PATCH, etc.)
            $methods = $route->methods();

            // Bỏ qua method OPTIONS và HEAD
            $methods = array_filter($methods, function ($method) {
                return !in_array($method, ['OPTIONS', 'HEAD']);
            });

            if (empty($methods)) {
                continue;
            }

            // Lấy path (đảm bảo có leading slash)
            $path = '/' . ltrim($uri, '/');

            // Chuẩn hóa path: thay thế tất cả route parameters thành {id}
            // Ví dụ: /api/users/{id} -> /api/users/{id}
            // Ví dụ: /api/courses/{courseId} -> /api/courses/{id}
            $normalizedPath = preg_replace('/\{[^}]+\}/', '{id}', $path);

            foreach ($methods as $method) {
                // Kiểm tra permission đã tồn tại chưa
                $existingPermission = Permission::where('method', $method)
                    ->where('path', $normalizedPath)
                    ->first();

                if ($existingPermission) {
                    // Nếu đã tồn tại và bị soft delete, restore nó
                    if ($existingPermission->trashed()) {
                        $existingPermission->restore();
                        $updated++;
                        $this->line("  ✓ Restored: {$method} {$normalizedPath}");
                    } else {
                        $skipped++;
                        if ($this->option('verbose')) {
                            $this->line("  - Skipped: {$method} {$normalizedPath} (đã tồn tại)");
                        }
                    }
                } else {
                    // Tạo permission mới
                    Permission::create([
                        'method' => $method,
                        'path' => $normalizedPath,
                    ]);
                    $added++;
                    $this->line("  + Added: {$method} {$normalizedPath}");
                }
            }
        }

        // Hiển thị kết quả
        $this->newLine();
        $this->info("Hoàn thành quét routes!");
        $this->table(
            ['Thao tác', 'Số lượng'],
            [
                ['Đã thêm', $added],
                ['Đã cập nhật', $updated],
                ['Đã bỏ qua', $skipped],
                ['Tổng cộng', $added + $updated + $skipped],
            ]
        );

        // Hiển thị tổng số permissions trong database
        $totalPermissions = Permission::count();
        $this->info("Tổng số permissions trong database: {$totalPermissions}");

        // Tự động sync permissions cho role ROOT
        $rootRole = Role::where('name', 'ROOT')->first();
        if ($rootRole) {
            $allPermissions = Permission::pluck('id')->toArray();
            $rootRole->permissions()->sync($allPermissions);
            $this->info("✓ Đã tự động sync {$totalPermissions} permissions cho role ROOT");
        }

        return Command::SUCCESS;
    }
}

