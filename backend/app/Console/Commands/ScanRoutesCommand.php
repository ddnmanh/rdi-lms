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
                // Lấy permission metadata từ route action (nếu có)
                $permissionMeta = $route->action['permission'] ?? null;

                // Nếu có metadata trong route, sử dụng nó; nếu không thì tự động generate
                $name = $permissionMeta['name'] ?? $this->generatePermissionName($method, $normalizedPath);
                $description = $permissionMeta['description'] ?? $this->generatePermissionDescription($method, $normalizedPath);
                $group = $permissionMeta['group'] ?? $this->generatePermissionGroup($normalizedPath);

                // Kiểm tra permission đã tồn tại chưa
                $existingPermission = Permission::where('method', $method)
                    ->where('path', $normalizedPath)
                    ->first();

                if ($existingPermission) {
                    // Nếu đã tồn tại và bị soft delete, restore nó
                    if ($existingPermission->trashed()) {
                        $existingPermission->restore();
                        // Nếu route có permission metadata, luôn cập nhật; nếu không thì chỉ cập nhật khi chưa có
                        $shouldUpdate = $permissionMeta !== null || !$existingPermission->name || !$existingPermission->description || !$existingPermission->group;
                        if ($shouldUpdate) {
                            $existingPermission->update([
                                'name' => $name,
                                'description' => $description,
                                'group' => $group,
                            ]);
                        }
                        $updated++;
                        $this->line("  ✓ Restored: {$method} {$normalizedPath}");
                    } else {
                        // Nếu route có permission metadata, luôn cập nhật; nếu không thì chỉ cập nhật khi chưa có
                        $shouldUpdate = $permissionMeta !== null || !$existingPermission->name || !$existingPermission->description || !$existingPermission->group;
                        if ($shouldUpdate) {
                            $existingPermission->update([
                                'name' => $name,
                                'description' => $description,
                                'group' => $group,
                            ]);
                            $updated++;
                            $this->line("  ✓ Updated: {$method} {$normalizedPath}" . ($permissionMeta ? ' (từ route metadata)' : ''));
                        } else {
                            $skipped++;
                            if ($this->option('verbose')) {
                                $this->line("  - Skipped: {$method} {$normalizedPath} (đã tồn tại)");
                            }
                        }
                    }
                } else {
                    // Tạo permission mới
                    Permission::create([
                        'name' => $name,
                        'description' => $description,
                        'group' => $group,
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

    /**
     * Tạo tên permission từ method và path
     */
    private function generatePermissionName($method, $path)
    {
        // Loại bỏ prefix /api nếu có
        $pathWithoutPrefix = preg_replace('/^\/api\//', '/', $path);
        $pathWithoutPrefix = ltrim($pathWithoutPrefix, '/');

        // Chuyển đổi path thành tên dễ đọc
        $segments = explode('/', $pathWithoutPrefix);
        $resource = '';
        $action = '';

        // Xác định resource và action dựa trên path và method
        if (count($segments) > 0) {
            $resource = $segments[0];

            // Loại bỏ {id} nếu có
            $resource = str_replace('{id}', '', $resource);
            $resource = trim($resource, '/');
        }

        // Xác định action dựa trên method và path
        switch (strtoupper($method)) {
            case 'GET':
                if (strpos($path, '{id}') !== false) {
                    $action = 'Xem chi tiết';
                } else {
                    $action = 'Xem danh sách';
                }
                break;
            case 'POST':
                $action = 'Tạo mới';
                break;
            case 'PUT':
            case 'PATCH':
                $action = 'Cập nhật';
                break;
            case 'DELETE':
                $action = 'Xóa';
                break;
            default:
                $action = 'Thao tác';
        }

        // Tạo tên từ resource và action
        $resourceName = $this->formatResourceName($resource);
        return $action . ' ' . $resourceName;
    }

    /**
     * Tạo mô tả permission từ method và path
     */
    private function generatePermissionDescription($method, $path)
    {
        // Loại bỏ prefix /api nếu có
        $pathWithoutPrefix = preg_replace('/^\/api\//', '/', $path);
        $pathWithoutPrefix = ltrim($pathWithoutPrefix, '/');

        $segments = explode('/', $pathWithoutPrefix);
        $resource = '';

        if (count($segments) > 0) {
            $resource = $segments[0];
            $resource = str_replace('{id}', '', $resource);
            $resource = trim($resource, '/');
        }

        $resourceName = $this->formatResourceName($resource);
        $methodName = strtoupper($method);

        // Tạo mô tả chi tiết
        $description = "Cho phép thực hiện {$methodName} trên route {$path}";

        switch (strtoupper($method)) {
            case 'GET':
                if (strpos($path, '{id}') !== false) {
                    $description = "Cho phép xem chi tiết {$resourceName}";
                } else {
                    $description = "Cho phép xem danh sách {$resourceName}";
                }
                break;
            case 'POST':
                $description = "Cho phép tạo mới {$resourceName}";
                break;
            case 'PUT':
            case 'PATCH':
                $description = "Cho phép cập nhật {$resourceName}";
                break;
            case 'DELETE':
                $description = "Cho phép xóa {$resourceName}";
                break;
        }

        return $description;
    }

    /**
     * Format resource name để dễ đọc hơn
     */
    private function formatResourceName($resource)
    {
        // Chuyển đổi từ snake_case hoặc kebab-case sang tên dễ đọc
        $resource = str_replace(['-', '_'], ' ', $resource);
        $resource = ucwords($resource);

        // Mapping một số resource phổ biến
        $mapping = [
            'Users' => 'Người dùng',
            'User' => 'Người dùng',
            'Roles' => 'Vai trò',
            'Role' => 'Vai trò',
            'Permissions' => 'Phân quyền',
            'Permission' => 'Phân quyền',
            'Courses' => 'Khóa học',
            'Course' => 'Khóa học',
            'Lessons' => 'Bài học',
            'Lesson' => 'Bài học',
            'Auth' => 'Xác thực',
            'Login' => 'Đăng nhập',
            'Logout' => 'Đăng xuất',
            'Register' => 'Đăng ký',
            'Refresh' => 'Làm mới',
        ];

        foreach ($mapping as $key => $value) {
            if (stripos($resource, $key) !== false) {
                $resource = str_ireplace($key, $value, $resource);
            }
        }

        return $resource;
    }

    /**
     * Tạo group permission từ path
     */
    private function generatePermissionGroup($path)
    {
        // Loại bỏ prefix /api nếu có
        $pathWithoutPrefix = preg_replace('/^\/api\//', '/', $path);
        $pathWithoutPrefix = ltrim($pathWithoutPrefix, '/');

        // Lấy segment đầu tiên làm group
        $segments = explode('/', $pathWithoutPrefix);
        if (count($segments) > 0) {
            $group = $segments[0];
            // Loại bỏ {id} nếu có
            $group = str_replace('{id}', '', $group);
            $group = trim($group, '/');
            
            // Format group name (chuyển từ kebab-case/snake_case sang title case)
            $group = str_replace(['-', '_'], ' ', $group);
            $group = ucwords($group);
            
            // Mapping một số group phổ biến
            $mapping = [
                'Users' => 'Người dùng',
                'User' => 'Người dùng',
                'Roles' => 'Vai trò',
                'Role' => 'Vai trò',
                'Permissions' => 'Phân quyền',
                'Permission' => 'Phân quyền',
                'Courses' => 'Khóa học',
                'Course' => 'Khóa học',
                'Lessons' => 'Bài học',
                'Lesson' => 'Bài học',
                'Auth' => 'Xác thực',
                'Login' => 'Xác thực',
                'Logout' => 'Xác thực',
                'Register' => 'Xác thực',
                'Refresh' => 'Xác thực',
            ];

            foreach ($mapping as $key => $value) {
                if (stripos($group, $key) !== false) {
                    $group = $value;
                    break;
                }
            }
            
            return $group ?: 'Khác';
        }

        return 'Khác';
    }
}

