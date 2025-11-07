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
    Route::get('/roles', [AdminController::class, 'roles'])->name('admin.roles');
    Route::get('/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::get('/lessons', [AdminController::class, 'lessons'])->name('admin.lessons');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});
