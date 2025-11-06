<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Danh sách roles
     *
     * Query params:
     * - search: Tìm kiếm theo name hoặc description
     * - level_from: Lọc từ level (1-255)
     * - level_to: Lọc đến level (1-255)
     * - user_id: Lọc các roles có user này
     * - permission_id: Lọc các roles có permission này
     * - sort_by: Sắp xếp theo (id, name, level, created_at) - mặc định: level
     * - order_by: Thứ tự (asc, desc) - mặc định: asc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(Request $request)
    {
        $query = Role::with('users', 'permissions');

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo level (từ)
        if ($request->has('level_from') && $request->level_from) {
            $query->where('level', '>=', $request->level_from);
        }

        // Lọc theo level (đến)
        if ($request->has('level_to') && $request->level_to) {
            $query->where('level', '<=', $request->level_to);
        }

        // Lọc theo user_id
        if ($request->has('user_id') && $request->user_id) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }

        // Lọc theo permission_id
        if ($request->has('permission_id') && $request->permission_id) {
            $query->whereHas('permissions', function ($q) use ($request) {
                $q->where('permissions.id', $request->permission_id);
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'level');
        $orderBy = $request->get('order_by', 'asc');

        // Validate sort_by
        $allowedSortBy = ['id', 'name', 'level', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortBy)) {
            $sortBy = 'level';
        }

        // Validate order_by
        $orderBy = strtolower($orderBy);
        if (!in_array($orderBy, ['asc', 'desc'])) {
            $orderBy = 'asc';
        }

        $query->orderBy($sortBy, $orderBy);

        $perPage = $request->get('per_page', 15);
        $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

        $roles = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Chi tiết role
     */
    public function show($id)
    {
        $role = Role::with('users', 'permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy role'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }

    /**
     * Tạo role mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        // Gán permissions
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo role thành công',
            'data' => $role->load('permissions')
        ], 201);
    }

    /**
     * Cập nhật role
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy role'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:255',
            'level' => 'required|integer|min:1|max:255',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level,
        ]);

        // Cập nhật permissions
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($request->permission_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật role thành công',
            'data' => $role->load('permissions')
        ]);
    }

    /**
     * Xóa role (soft delete)
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy role'
            ], 404);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa role thành công'
        ]);
    }

    /**
     * Gán permissions cho role
     */
    public function assignPermissions(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy role'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $role->permissions()->sync($request->permission_ids);

        return response()->json([
            'success' => true,
            'message' => 'Gán permissions thành công',
            'data' => $role->load('permissions')
        ]);
    }
}

