<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Danh sách người dùng
     * 
     * Query params:
     * - search: Tìm kiếm theo email hoặc fullname
     * - role_id: Lọc theo role ID
     * - role_name: Lọc theo tên role (ROOT, ADMIN, TEACHER, STUDENT)
     * - created_at_from: Lọc từ ngày tạo (format: Y-m-d)
     * - created_at_to: Lọc đến ngày tạo (format: Y-m-d)
     * - sort_by: Sắp xếp theo (id, email, fullname, created_at) - mặc định: id
     * - order_by: Thứ tự (asc, desc) - mặc định: desc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%");
            });
        }

        // Lọc theo role_id
        if ($request->has('role_id') && $request->role_id) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        // Lọc theo role_name
        if ($request->has('role_name') && $request->role_name) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.name', $request->role_name);
            });
        }

        // Lọc theo ngày tạo (từ)
        if ($request->has('created_at_from') && $request->created_at_from) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        // Lọc theo ngày tạo (đến)
        if ($request->has('created_at_to') && $request->created_at_to) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $orderBy = $request->get('order_by', 'desc');
        
        // Validate sort_by
        $allowedSortBy = ['id', 'email', 'fullname', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'id';
        }
        
        // Validate order_by
        $orderBy = strtolower($orderBy);
        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'desc';
        }
        
        $query->orderBy($sortBy, $orderBy);

        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100
        
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Chi tiết người dùng
     */
    public function show($id)
    {
        $user = User::with('roles', 'courses')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'fullname' => 'nullable|string|max:150',
            'birthday' => 'nullable|date',
            'path_avatar' => 'nullable|string',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => $request->password,
            'fullname' => $request->fullname,
            'birthday' => $request->birthday,
            'path_avatar' => $request->path_avatar,
        ]);

        // Gán roles
        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo người dùng thành công',
            'data' => $user->load('roles')
        ], 201);
    }

    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'fullname' => 'nullable|string|max:150',
            'birthday' => 'nullable|date',
            'path_avatar' => 'nullable|string',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'email' => $request->email,
            'fullname' => $request->fullname,
            'birthday' => $request->birthday,
            'path_avatar' => $request->path_avatar,
        ];

        if ($request->has('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        // Cập nhật roles
        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công',
            'data' => $user->load('roles')
        ]);
    }

    /**
     * Xóa người dùng (soft delete)
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    }

    /**
     * Gán roles cho user
     */
    public function assignRoles(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->roles()->sync($request->role_ids);

        return response()->json([
            'success' => true,
            'message' => 'Phân quyền thành công',
            'data' => $user->load('roles')
        ]);
    }
}

