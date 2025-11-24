<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Login route (public)
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// Admin routes (protected)
Route::prefix('admin')->middleware('auth.cookie')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.list');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles.list');
    Route::get('/roles/create', [AdminController::class, 'createRole'])->name('admin.roles.create');
    Route::get('/roles/{id}/edit', [AdminController::class, 'editRole'])->name('admin.roles.edit');
    Route::get('/roles/{id}/show', [AdminController::class, 'showRole'])->name('admin.roles.show');
    Route::get('/courses', [AdminController::class, 'courses'])->name('admin.courses.list');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('admin.courses.create');
    Route::get('/courses/{id}', [AdminController::class, 'showCourse'])->name('admin.courses.show');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('admin.courses.edit');
    Route::get('/lessons', [AdminController::class, 'lessons'])->name('admin.lessons.list');
    Route::get('/lessons/create', [AdminController::class, 'createLesson'])->name('admin.lessons.create');
    Route::get('/lessons/{id}', [AdminController::class, 'showLesson'])->name('admin.lessons.show');
    Route::get('/lessons/{id}/edit', [AdminController::class, 'editLesson'])->name('admin.lessons.edit');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports.index');
    Route::get('/reports/students', [AdminController::class, 'reportsStudents'])->name('admin.reports.students');
    Route::get('/reports/courses', [AdminController::class, 'reportsCourses'])->name('admin.reports.courses');
    Route::get('/reports/activities', [AdminController::class, 'reportsActivities'])->name('admin.reports.activities');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile.show');
    Route::get('/profile/edit', [AdminController::class, 'editProfile'])->name('admin.profile.edit');
});
