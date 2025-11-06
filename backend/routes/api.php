<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // User Management (Admin)
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::post('/{id}/roles', [UserController::class, 'assignRoles']);
    });

    // Course Management (Admin)
    Route::prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index']);
        Route::post('/', [CourseController::class, 'store']);
        Route::get('/{id}', [CourseController::class, 'show']);
        Route::put('/{id}', [CourseController::class, 'update']);
        Route::delete('/{id}', [CourseController::class, 'destroy']);
        Route::post('/{id}/users', [CourseController::class, 'assignUsers']);
        Route::post('/{id}/users/add', [CourseController::class, 'addUser']);
        Route::post('/{id}/users/remove', [CourseController::class, 'removeUser']);
    });

    // Lesson Management (Admin)
    Route::prefix('lessons')->group(function () {
        Route::get('/', [LessonController::class, 'index']);
        Route::post('/', [LessonController::class, 'store']);
        Route::get('/{id}', [LessonController::class, 'show']);
        Route::put('/{id}', [LessonController::class, 'update']);
        Route::delete('/{id}', [LessonController::class, 'destroy']);
    });

    // Student routes
    Route::prefix('student')->group(function () {
        Route::get('/courses', [StudentController::class, 'courses']);
        Route::get('/courses/{courseId}', [StudentController::class, 'courseDetail']);
        Route::get('/lessons/{lessonId}', [StudentController::class, 'lessonDetail']);
        Route::post('/progress', [StudentController::class, 'updateProgress']);
        Route::get('/courses/{courseId}/progress', [StudentController::class, 'courseProgress']);
    });

    // Report routes (Admin)
    Route::prefix('reports')->group(function () {
        Route::get('/overview', [ReportController::class, 'overview']);
        Route::get('/courses/{courseId}/statistics', [ReportController::class, 'courseStatistics']);
        Route::get('/courses/{courseId}/students', [ReportController::class, 'courseStudents']);
        Route::get('/courses/{courseId}/students/{userId}/progress', [ReportController::class, 'studentCourseProgress']);
        Route::get('/students/{userId}/login-history', [ReportController::class, 'studentLoginHistory']);
    });
});
