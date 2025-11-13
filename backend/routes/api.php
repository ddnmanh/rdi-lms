<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
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
    Route::post('/register', [AuthController::class, 'register'], [
        'group' => 'Xác thực',
        'name' => 'Đăng ký',
        'description' => 'Cho phép đăng ký tạo tài khoản mới'
    ]);
    Route::post('/login', [AuthController::class, 'login'], [
        'group' => 'Xác thực',
        'name' => 'Đăng nhập',
        'description' => 'Cho phép đăng nhập vào hệ thống'
    ]);
    Route::post('/refresh', [AuthController::class, 'refresh'], [
        'group' => 'Xác thực',
        'name' => 'Refresh token',
        'description' => 'Làm mới access token'
    ]);
});

// Protected routes - require authentication
Route::middleware('auth:api')->group(function () {
    // Authentication (không cần kiểm tra permission vì là thông tin cá nhân)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'], [
            'group' => 'Xác thực',
            'name' => 'Đăng xuất',
            'description' => 'Cho phép đăng xuất khỏi hệ thống'
        ]);
        Route::get('/me', [AuthController::class, 'me'], [
            'group' => 'Xác thực',
            'name' => 'Lấy thông tin user hiện tại',
            'description' => 'Cho phép lấy thông tin user hiện tại'
        ]);
    });

    // User Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('users')->group(function () {
        routeWithPermission('get', '/', [UserController::class, 'index'], [
            'group' => 'Người dùng',
            'name' => 'Xem danh sách Người dùng',
            'description' => 'Cho phép xem danh sách tất cả người dùng trong hệ thống'
        ]);
        routeWithPermission('post', '/', [UserController::class, 'store'], [
            'group' => 'Người dùng',
            'name' => 'Tạo mới Người dùng',
            'description' => 'Cho phép tạo mới người dùng trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [UserController::class, 'show'], [
            'group' => 'Người dùng',
            'name' => 'Xem chi tiết Người dùng',
            'description' => 'Cho phép xem thông tin chi tiết của một người dùng'
        ]);
        routeWithPermission('put', '/{id}', [UserController::class, 'update'], [
            'group' => 'Người dùng',
            'name' => 'Cập nhật Người dùng',
            'description' => 'Cho phép cập nhật thông tin của người dùng'
        ]);
        routeWithPermission('delete', '/', [UserController::class, 'destroyUsers'], [
            'group' => 'Người dùng',
            'name' => 'Xóa Người dùng',
            'description' => 'Cho phép xóa n người dùng khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/roles', [UserController::class, 'assignRoles'], [
            'group' => 'Người dùng',
            'name' => 'Gán vai trò cho Người dùng',
            'description' => 'Cho phép gán hoặc thay đổi vai trò của người dùng'
        ]);
    });

    // Role Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('roles')->group(function () {
        routeWithPermission('get', '/', [RoleController::class, 'index'], [
            'group' => 'Vai trò',
            'name' => 'Xem danh sách Vai trò',
            'description' => 'Cho phép xem danh sách tất cả vai trò trong hệ thống'
        ]);
        routeWithPermission('post', '/', [RoleController::class, 'store'], [
            'group' => 'Vai trò',
            'name' => 'Tạo mới Vai trò',
            'description' => 'Cho phép tạo mới vai trò trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [RoleController::class, 'show'], [
            'group' => 'Vai trò',
            'name' => 'Xem chi tiết Vai trò',
            'description' => 'Cho phép xem thông tin chi tiết của một vai trò'
        ]);
        routeWithPermission('put', '/{id}', [RoleController::class, 'update'], [
            'group' => 'Vai trò',
            'name' => 'Cập nhật Vai trò',
            'description' => 'Cho phép cập nhật thông tin của vai trò'
        ]);
        routeWithPermission('delete', '/{id}', [RoleController::class, 'destroy'], [
            'group' => 'Vai trò',
            'name' => 'Xóa Vai trò',
            'description' => 'Cho phép xóa vai trò khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/permissions', [RoleController::class, 'assignPermissions'], [
            'group' => 'Vai trò',
            'name' => 'Gán phân quyền cho Vai trò',
            'description' => 'Cho phép gán hoặc thay đổi phân quyền của vai trò'
        ]);
    });

    // Course Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('courses')->group(function () {
        routeWithPermission('get', '/', [CourseController::class, 'index'], [
            'group' => 'Khóa học',
            'name' => 'Xem danh sách Khóa học',
            'description' => 'Cho phép xem danh sách tất cả khóa học trong hệ thống'
        ]);
        routeWithPermission('post', '/', [CourseController::class, 'store'], [
            'group' => 'Khóa học',
            'name' => 'Tạo mới Khóa học',
            'description' => 'Cho phép tạo mới khóa học trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [CourseController::class, 'show'], [
            'group' => 'Khóa học',
            'name' => 'Xem chi tiết Khóa học',
            'description' => 'Cho phép xem thông tin chi tiết của một khóa học'
        ]);
        routeWithPermission('put', '/{id}', [CourseController::class, 'update'], [
            'group' => 'Khóa học',
            'name' => 'Cập nhật Khóa học',
            'description' => 'Cho phép cập nhật thông tin của khóa học'
        ]);
        routeWithPermission('delete', '/', [CourseController::class, 'destroy'], [
            'group' => 'Khóa học',
            'name' => 'Xóa Khóa học',
            'description' => 'Cho phép xóa khóa học khỏi hệ thống'
        ]);
        routeWithPermission('post', '/{id}/users/add', [CourseController::class, 'addUsers'], [
            'group' => 'Khóa học',
            'name' => 'Thêm sinh viên vào Khóa học',
            'description' => 'Cho phép thêm n sinh viên vào khóa học'
        ]);
        routeWithPermission('post', '/{id}/users/remove', [CourseController::class, 'removeUsers'], [
            'group' => 'Khóa học',
            'name' => 'Xóa sinh viên khỏi Khóa học',
            'description' => 'Cho phép xóa n sinh viên khỏi khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons/add', [CourseController::class, 'addLessons'], [
            'group' => 'Khóa học',
            'name' => 'Thêm bài học vào Khóa học',
            'description' => 'Cho phép thêm n bài học vào khóa học đồng thời có thể cập nhật thứ tự hiển thị của bài học trong khóa học'
        ]);
        routeWithPermission('post', '/{id}/lessons/remove', [CourseController::class, 'removeLessons'], [
            'group' => 'Khóa học',
            'name' => 'Xóa bài học khỏi Khóa học',
            'description' => 'Cho phép xóa n bài học khỏi khóa học'
        ]);
    });

    // Lesson Management (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('lessons')->group(function () {
        routeWithPermission('get', '/', [LessonController::class, 'index'], [
            'group' => 'Bài học',
            'name' => 'Xem danh sách Bài học',
            'description' => 'Cho phép xem danh sách tất cả bài học trong hệ thống'
        ]);
        routeWithPermission('post', '/', [LessonController::class, 'store'], [
            'group' => 'Bài học',
            'name' => 'Tạo mới Bài học',
            'description' => 'Cho phép tạo mới bài học trong hệ thống'
        ]);
        routeWithPermission('get', '/{id}', [LessonController::class, 'show'], [
            'group' => 'Bài học',
            'name' => 'Xem chi tiết Bài học',
            'description' => 'Cho phép xem thông tin chi tiết của một bài học'
        ]);
        routeWithPermission('put', '/{id}', [LessonController::class, 'update'], [
            'group' => 'Bài học',
            'name' => 'Cập nhật Bài học',
            'description' => 'Cho phép cập nhật thông tin của bài học'
        ]);
        routeWithPermission('delete', '/', [LessonController::class, 'destroy'], [
            'group' => 'Bài học',
            'name' => 'Xóa Bài học',
            'description' => 'Cho phép xóa bài học, không thể xóa bài học đang thuộc về một khóa học'
        ]);
    });

    // Student routes - yêu cầu permission
    Route::middleware('check.permission')->prefix('student')->group(function () {
        routeWithPermission('get', '/courses', [StudentController::class, 'courses'], [
            'group' => 'Sinh viên',
            'name' => 'Xem danh sách Khóa học của Học viên',
            'description' => 'Cho phép học viên xem danh sách các khóa học của mình'
        ]);
        routeWithPermission('get', '/courses/{courseId}', [StudentController::class, 'courseDetail'], [
            'group' => 'Sinh viên',
            'name' => 'Xem chi tiết Khóa học (Học viên)',
            'description' => 'Cho phép học viên xem chi tiết khóa học'
        ]);
        routeWithPermission('get', '/lessons/{lessonId}', [StudentController::class, 'lessonDetail'], [
            'group' => 'Sinh viên',
            'name' => 'Xem chi tiết Bài học (Học viên)',
            'description' => 'Cho phép học viên xem chi tiết bài học'
        ]);
        routeWithPermission('post', '/progress', [StudentController::class, 'updateProgress'], [
            'group' => 'Sinh viên',
            'name' => 'Cập nhật tiến độ học tập',
            'description' => 'Cho phép học viên cập nhật tiến độ học tập của mình'
        ]);
        routeWithPermission('get', '/courses/{courseId}/progress', [StudentController::class, 'courseProgress'], [
            'group' => 'Sinh viên',
            'name' => 'Xem tiến độ Khóa học',
            'description' => 'Cho phép học viên xem tiến độ học tập của một khóa học'
        ]);
    });

    // Report routes (Admin) - yêu cầu permission
    Route::middleware('check.permission')->prefix('reports')->group(function () {
        routeWithPermission('get', '/overview', [ReportController::class, 'overview'], [
            'group' => 'Báo cáo',
            'name' => 'Xem tổng quan Báo cáo',
            'description' => 'Cho phép xem tổng quan các báo cáo của hệ thống'
        ]);
        routeWithPermission('get', '/courses/{courseId}/statistics', [ReportController::class, 'courseStatistics'], [
            'group' => 'Báo cáo',
            'name' => 'Xem thống kê Khóa học',
            'description' => 'Cho phép xem các thống kê chi tiết của một khóa học'
        ]);
        routeWithPermission('get', '/courses/{courseId}/students', [ReportController::class, 'courseStudents'], [
            'group' => 'Báo cáo',
            'name' => 'Xem danh sách Học viên của Khóa học',
            'description' => 'Cho phép xem danh sách học viên tham gia một khóa học'
        ]);
        routeWithPermission('get', '/courses/{courseId}/students/{userId}/progress', [ReportController::class, 'studentCourseProgress'], [
            'group' => 'Báo cáo',
            'name' => 'Xem tiến độ Học viên trong Khóa học',
            'description' => 'Cho phép xem tiến độ học tập của một học viên trong khóa học'
        ]);
        routeWithPermission('get', '/students/{userId}/login-history', [ReportController::class, 'studentLoginHistory'], [
            'group' => 'Báo cáo',
            'name' => 'Xem lịch sử đăng nhập Học viên',
            'description' => 'Cho phép xem lịch sử đăng nhập của một học viên'
        ]);
    });
});
