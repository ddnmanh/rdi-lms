<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Helper function để định nghĩa route với permission metadata
 */
if (!function_exists('routeWithPermission')) {
    function routeWithPermission($method, $uri, $action, $permission = null)
    {
        $method = strtolower($method);
        $route = null;

        switch ($method) {
            case 'get':
                $route = Route::get($uri, $action);
                break;
            case 'post':
                $route = Route::post($uri, $action);
                break;
            case 'put':
                $route = Route::put($uri, $action);
                break;
            case 'patch':
                $route = Route::patch($uri, $action);
                break;
            case 'delete':
                $route = Route::delete($uri, $action);
                break;
            case 'options':
                $route = Route::options($uri, $action);
                break;
            default:
                throw new InvalidArgumentException("Invalid HTTP method: {$method}");
        }

        if ($permission) {
            $route->action['permission'] = $permission;
        }
        return $route;
    }
}

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes - Authentication
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']); // Refresh token (public)
});

// Protected routes - require authentication
Route::middleware('auth:api')->group(function () {
    // Authentication (không cần kiểm tra permission vì là thông tin cá nhân)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // User Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('users')->group(function () {
        routeWithPermission('get', '/', [UserController::class, 'index'], [
            'name' => 'Xem danh sách Người dùng',
            'description' => 'Cho phép xem danh sách tất cả người dùng trong hệ thống'
        ]);
        routeWithPermission('post', '/', [UserController::class, 'store'], [
            'name' => 'Tạo mới Người dùng',
            'description' => 'Cho phép tạo mới người dùng trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [UserController::class, 'show'], [
            'name' => 'Xem chi tiết Người dùng',
            'description' => 'Cho phép xem thông tin chi tiết của một người dùng'
        ]);
        routeWithPermission('put', '/{id}', [UserController::class, 'update'], [
            'name' => 'Cập nhật Người dùng',
            'description' => 'Cho phép cập nhật thông tin của người dùng'
        ]);
        routeWithPermission('delete', '/{id}', [UserController::class, 'destroy'], [
            'name' => 'Xóa Người dùng',
            'description' => 'Cho phép xóa người dùng khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/roles', [UserController::class, 'assignRoles'], [
            'name' => 'Gán vai trò cho Người dùng',
            'description' => 'Cho phép gán hoặc thay đổi vai trò của người dùng'
        ]);
    });

    // Role Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('roles')->group(function () {
        routeWithPermission('get', '/', [RoleController::class, 'index'], [
            'name' => 'Xem danh sách Vai trò',
            'description' => 'Cho phép xem danh sách tất cả vai trò trong hệ thống'
        ]);
        routeWithPermission('post', '/', [RoleController::class, 'store'], [
            'name' => 'Tạo mới Vai trò',
            'description' => 'Cho phép tạo mới vai trò trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [RoleController::class, 'show'], [
            'name' => 'Xem chi tiết Vai trò',
            'description' => 'Cho phép xem thông tin chi tiết của một vai trò'
        ]);
        routeWithPermission('put', '/{id}', [RoleController::class, 'update'], [
            'name' => 'Cập nhật Vai trò',
            'description' => 'Cho phép cập nhật thông tin của vai trò'
        ]);
        routeWithPermission('delete', '/{id}', [RoleController::class, 'destroy'], [
            'name' => 'Xóa Vai trò',
            'description' => 'Cho phép xóa vai trò khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/permissions', [RoleController::class, 'assignPermissions'], [
            'name' => 'Gán phân quyền cho Vai trò',
            'description' => 'Cho phép gán hoặc thay đổi phân quyền của vai trò'
        ]);
    });

    // Course Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('courses')->group(function () {
        routeWithPermission('get', '/', [CourseController::class, 'index'], [
            'name' => 'Xem danh sách Khóa học',
            'description' => 'Cho phép xem danh sách tất cả khóa học trong hệ thống'
        ]);
        routeWithPermission('post', '/', [CourseController::class, 'store'], [
            'name' => 'Tạo mới Khóa học',
            'description' => 'Cho phép tạo mới khóa học trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [CourseController::class, 'show'], [
            'name' => 'Xem chi tiết Khóa học',
            'description' => 'Cho phép xem thông tin chi tiết của một khóa học'
        ]);
        routeWithPermission('put', '/{id}', [CourseController::class, 'update'], [
            'name' => 'Cập nhật Khóa học',
            'description' => 'Cho phép cập nhật thông tin của khóa học'
        ]);
        routeWithPermission('delete', '/{id}', [CourseController::class, 'destroy'], [
            'name' => 'Xóa Khóa học',
            'description' => 'Cho phép xóa khóa học khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/users', [CourseController::class, 'assignUsers'], [
            'name' => 'Gán người dùng cho Khóa học',
            'description' => 'Cho phép gán nhiều người dùng vào khóa học'
        ]);
        routeWithPermission('post', '/{id}/users/add', [CourseController::class, 'addUser'], [
            'name' => 'Thêm người dùng vào Khóa học',
            'description' => 'Cho phép thêm một người dùng vào khóa học'
        ]);
        routeWithPermission('post', '/{id}/users/remove', [CourseController::class, 'removeUser'], [
            'name' => 'Xóa người dùng khỏi Khóa học',
            'description' => 'Cho phép xóa một người dùng khỏi khóa học'
        ]);
        routeWithPermission('post', '/{id}/users/remove-multiple', [CourseController::class, 'removeUsers'], [
            'name' => 'Xóa nhiều người dùng khỏi Khóa học',
            'description' => 'Cho phép xóa nhiều người dùng khỏi khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons/add', [CourseController::class, 'addLesson'], [
            'name' => 'Thêm một bài học vào Khóa học',
            'description' => 'Cho phép thêm một bài học vào khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons', [CourseController::class, 'addLessons'], [
            'name' => 'Thêm nhiều bài học vào Khóa học',
            'description' => 'Cho phép thêm nhiều bài học vào khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons/remove', [CourseController::class, 'removeLesson'], [
            'name' => 'Xóa một bài học khỏi Khóa học',
            'description' => 'Cho phép xóa một bài học khỏi khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons/remove-multiple', [CourseController::class, 'removeLessons'], [
            'name' => 'Xóa nhiều bài học khỏi Khóa học',
            'description' => 'Cho phép xóa nhiều bài học khỏi khóa học'
        ]);
    });

    // Lesson Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('lessons')->group(function () {
        routeWithPermission('get', '/', [LessonController::class, 'index'], [
            'name' => 'Xem danh sách Bài học',
            'description' => 'Cho phép xem danh sách tất cả bài học trong hệ thống'
        ]);
        routeWithPermission('post', '/', [LessonController::class, 'store'], [
            'name' => 'Tạo mới Bài học',
            'description' => 'Cho phép tạo mới bài học trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [LessonController::class, 'show'], [
            'name' => 'Xem chi tiết Bài học',
            'description' => 'Cho phép xem thông tin chi tiết của một bài học'
        ]);
        routeWithPermission('put', '/{id}', [LessonController::class, 'update'], [
            'name' => 'Cập nhật Bài học',
            'description' => 'Cho phép cập nhật thông tin của bài học'
        ]);
        routeWithPermission('delete', '/{id}', [LessonController::class, 'destroy'], [
            'name' => 'Xóa Bài học',
            'description' => 'Cho phép xóa bài học khỏi hệ thống'
        ]);
    });

    // Student routes - yêu cầu permission
    Route::middleware('check.permission')->prefix('student')->group(function () {
        routeWithPermission('get', '/courses', [StudentController::class, 'courses'], [
            'name' => 'Xem danh sách Khóa học của Học viên',
            'description' => 'Cho phép học viên xem danh sách các khóa học của mình'
        ]);
        routeWithPermission('get', '/courses/{courseId}', [StudentController::class, 'courseDetail'], [
            'name' => 'Xem chi tiết Khóa học (Học viên)',
            'description' => 'Cho phép học viên xem chi tiết khóa học'
        ]);
        routeWithPermission('get', '/lessons/{lessonId}', [StudentController::class, 'lessonDetail'], [
            'name' => 'Xem chi tiết Bài học (Học viên)',
            'description' => 'Cho phép học viên xem chi tiết bài học'
        ]);
        routeWithPermission('post', '/progress', [StudentController::class, 'updateProgress'], [
            'name' => 'Cập nhật tiến độ học tập',
            'description' => 'Cho phép học viên cập nhật tiến độ học tập của mình'
        ]);
        routeWithPermission('get', '/courses/{courseId}/progress', [StudentController::class, 'courseProgress'], [
            'name' => 'Xem tiến độ Khóa học',
            'description' => 'Cho phép học viên xem tiến độ học tập của một khóa học'
        ]);
    });

    // Report routes (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('reports')->group(function () {
        routeWithPermission('get', '/overview', [ReportController::class, 'overview'], [
            'name' => 'Xem tổng quan Báo cáo',
            'description' => 'Cho phép xem tổng quan các báo cáo của hệ thống'
        ]);
        routeWithPermission('get', '/courses/{courseId}/statistics', [ReportController::class, 'courseStatistics'], [
            'name' => 'Xem thống kê Khóa học',
            'description' => 'Cho phép xem các thống kê chi tiết của một khóa học'
        ]);
        routeWithPermission('get', '/courses/{courseId}/students', [ReportController::class, 'courseStudents'], [
            'name' => 'Xem danh sách Học viên của Khóa học',
            'description' => 'Cho phép xem danh sách học viên tham gia một khóa học'
        ]);
        routeWithPermission('get', '/courses/{courseId}/students/{userId}/progress', [ReportController::class, 'studentCourseProgress'], [
            'name' => 'Xem tiến độ Học viên trong Khóa học',
            'description' => 'Cho phép xem tiến độ học tập của một học viên trong khóa học'
        ]);
        routeWithPermission('get', '/students/{userId}/login-history', [ReportController::class, 'studentLoginHistory'], [
            'name' => 'Xem lịch sử đăng nhập Học viên',
            'description' => 'Cho phép xem lịch sử đăng nhập của một học viên'
        ]);
    });
});
