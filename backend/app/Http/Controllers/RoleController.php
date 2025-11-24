<?php

namespace App\Http\Controllers;

use App\Http\Requests\roles\DestroyRequest;
use App\Http\Requests\roles\GetAllRequest;
use App\Http\Requests\roles\StoreRequest;
use App\Http\Requests\roles\UpdateRequest;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use Nette\Utils\Json;

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
    public function index(GetAllRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            $query = Role::with('users', 'permissions');

            // Tìm kiếm
            if (isset($body['search']) && $body['search']) {
                $search = $body['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Lọc theo level (từ)
            if (isset($body['level_from']) && $body['level_from']) {
                $query->where('level', '>=', $body['level_from']);
            }

            // Lọc theo level (đến)
            if (isset($body['level_to']) && $body['level_to']) {
                $query->where('level', '<=', $body['level_to']);
            }

            // Lọc theo user_id
            if (isset($body['user_id']) && $body['user_id']) {
                $query->whereHas('users', function ($q) use ($body) {
                    $q->where('users.id', $body['user_id']);
                });
            }

            // Lọc theo permission_id
            if (isset($body['permission_id']) && $body['permission_id']) {
                $query->whereHas('permissions', function ($q) use ($body) {
                    $q->where('permissions.id', $body['permission_id']);
                });
            }

            // Sắp xếp
            $sortBy = $body['sort_by'] ?? 'level';
            $orderBy = $body['order_by'] ?? 'asc';

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

            $perPage = $body['per_page'] ?? 15;
            $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

            $roles = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết role
     */
    public function show($id): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy chi tiết role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo role mới
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            $role = Role::create([
                'name' => $body['name'],
                'description' => $body['description'] ?? null,
                'level' => $body['level'],
            ]);

            // Gán permissions
            if (isset($body['permission_ids'])) {
                $role->permissions()->sync($body['permission_ids']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tạo role thành công',
                'data' => $role->load('permissions')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tạo role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật role
     */
    public function update(UpdateRequest $request, $id):JsonResponse
    {
        try {

            $body = $request->validated();

            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy role'
                ], 404);
            } 

            $role->update([
                'name' => $body['name'] ?? $role->name,
                'description' => $body['description'] ?? $role->description,
                'level' => $body['level'] ?? $role->level,
            ]);

            // Cập nhật permissions
            if (isset($body['permission_ids'])) {
                $role->permissions()->sync($body['permission_ids'] ?? []);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật role thành công',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi cập nhật role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa role (soft delete)
     */
    public function destroy(DestroyRequest $request): JsonResponse
    {
        try {

            $body = $request->validated();

            $roles = Role::whereIn('id', $body['role_ids'])->get();

            if (!$roles) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy role'
                ], 404);
            }

            foreach ($roles as $role) {
                if ($role->name !== 'ROOT') { // Không xóa role ROOT
                    $role->delete();
                }
            } 

            return response()->json([
                'success' => true,
                'message' => 'Xóa roles thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi xóa roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gán permissions cho role
     */
    // public function assignPermissions(Request $request, $id)
    // {
    //     $role = Role::find($id);

    //     if (!$role) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Không tìm thấy role'
    //         ], 404);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'permission_ids' => 'required|array',
    //         'permission_ids.*' => 'exists:permissions,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation errors',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $role->permissions()->sync($request->permission_ids);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Gán permissions thành công',
    //         'data' => $role->load('permissions')
    //     ]);
    // }
}

