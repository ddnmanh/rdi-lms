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
        return view('admin.roles');
    }

    /**
     * Trang quản lý courses
     */
    public function courses()
    {
        return view('admin.courses');
    }

    /**
     * Trang quản lý lessons
     */
    public function lessons()
    {
        return view('admin.lessons');
    }

    /**
     * Trang báo cáo
     */
    public function reports()
    {
        return view('admin.reports');
    }
}

