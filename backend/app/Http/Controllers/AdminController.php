<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Trang đăng nhập
     */
    public function login()
    {
        return view('admin.login');
    }

    /**
     * Trang dashboard
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Trang quản lý users
     */
    public function users()
    {
        return view('admin.users.list');
    }

    /**
     * Trang tạo mới user
     */
    public function createUser()
    {
        return view('admin.users.form', ['mode' => 'create']);
    }

    /**
     * Trang xem chi tiết user
     */
    public function showUser($id)
    {
        return view('admin.users.show', ['userId' => $id]);
    }

    /**
     * Trang chỉnh sửa user
     */
    public function editUser($id)
    {
        return view('admin.users.form', ['mode' => 'edit', 'userId' => $id]);
    }

    /**
     * Trang quản lý roles
     */
    public function roles()
    {
        return view('admin.roles.list');
    }

    /**
     * Trang tạo mới role
     */
    public function createRole()
    {
        return view('admin.roles.form', ['mode' => 'create']);
    }

    /**
     * Trang chỉnh sửa role
     */
    public function editRole($id)
    {
        return view('admin.roles.form', ['mode' => 'edit', 'roleId' => $id]);
    }

    /**
     * Trang quản lý courses
     */
    public function courses()
    {
        return view('admin.courses.list');
    }

    /**
     * Trang tạo mới course
     */
    public function createCourse()
    {
        return view('admin.courses.form', ['mode' => 'CREATE_COURSE']);
    }

    /**
     * Trang xem chi tiết course
     */
    public function showCourse($id)
    {
        return view('admin.courses.show', ['courseId' => $id]);
    }

    /**
     * Trang chỉnh sửa course
     */
    public function editCourse($id)
    {
        return view('admin.courses.form', ['mode' => 'EDIT_COURSE', 'courseId' => $id]);
    }

    /**
     * Trang quản lý lessons của course
     */
    public function manageCourseLessons($id)
    {
        return view('admin.courses.lessons', ['courseId' => $id]);
    }

    /**
     * Trang quản lý users của course
     */
    public function manageCourseUsers($id)
    {
        return view('admin.courses.users', ['courseId' => $id]);
    }

    /**
     * Trang quản lý lessons
     */
    public function lessons()
    {
        return view('admin.lessons.list');
    }

    /**
     * Trang tạo mới lesson
     */
    public function createLesson()
    {
        return view('admin.lessons.form', ['mode' => 'create']);
    }

    /**
     * Trang xem chi tiết lesson
     */
    public function showLesson($id)
    {
        return view('admin.lessons.show', ['lessonId' => $id]);
    }

    /**
     * Trang chỉnh sửa lesson
     */
    public function editLesson($id)
    {
        return view('admin.lessons.form', ['mode' => 'edit', 'lessonId' => $id]);
    }

    /**
     * Trang báo cáo
     */
    public function reports()
    {
        return view('admin.reports.index');
    }

    /**
     * Trang báo cáo sinh viên
     */
    public function reportsStudents()
    {
        return view('admin.reports.students');
    }

    /**
     * Trang báo cáo khóa học
     */
    public function reportsCourses()
    {
        return view('admin.reports.courses');
    }

    /**
     * Trang báo cáo hoạt động
     */
    public function reportsActivities()
    {
        return view('admin.reports.activities');
    }
}

