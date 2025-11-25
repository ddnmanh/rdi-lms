<?php

namespace App\Http\Controllers;

use App\Http\Requests\users\DestroyRequest;
use App\Http\Requests\users\StoreRequest;
use App\Http\Requests\users\UpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Danh sách người dùng
     *
     * Query params:
     * - search: Tìm kiếm theo email hoặc fullname
     * - search_email: Tìm kiếm theo email
     * - search_fullname: Tìm kiết theo fullname
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

        // Tìm kiếm chung theo email hoặc fullname
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%");
            });
        }

        // Tìm kiếm theo email
        if ($request->has('search_email') && $request->search_email) {
            $query->where('email', 'like', "%{$request->search_email}%");
        }

        // Tìm kiếm theo fullname
        if ($request->has('search_fullname') && $request->search_fullname) {
            $query->where('fullname', 'like', "%{$request->search_fullname}%");
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

        $perPage = $request->get('per_page', 30);

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
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            $user = User::create([
                'email' => $body['email'] ?? null,
                'password' => bcrypt($body['password'] ?? ''),
                'fullname' => $body['fullname'] ?? null,
                'birthday' => $body['birthday'] ?? null
            ]);

            // Gán roles
            if ($body['role_ids'] ?? false) {
                $user->roles()->sync($body['role_ids']);
            }

            // Xử lý upload avatar nếu có
            if (isset($body['avatar']) && $body['avatar']) {
                // Xóa avatar cũ nếu có
                if ($user->avatar_path) {
                    $oldPath = str_replace('/storage/', '', $user->avatar_path);
                    Storage::disk('public')->delete($oldPath);
                }

                // Lưu avatar mới
                $extension = $body['avatar']->getClientOriginalExtension();
                $slugTitle = $this->createSlug($user->fullname);
                $customFileName = 'user_' . $user->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                $storedPath = $body['avatar']->storeAs('avatars', $customFileName, 'public');
                $publicUrl = Storage::url($storedPath); // ví dụ: /storage/avatars/user_1_<slug fullname>_1697059200.jpg

                $user->update(['avatar_path' => $publicUrl]);

            }

            $user->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Tạo người dùng thành công',
                'data' => $user->load('roles')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật người dùng
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $body = $request->validated();

            $authUser = $request->user();
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ], 404);
            }

            // Kiểm tra quyền: chỉ cho phép cập nhật chính mình hoặc user có level cao hơn
            if ($authUser->id != $id) {
                // Lấy level thấp nhất (quyền cao nhất) của auth user
                $authUserMinLevel = $authUser->roles()->min('level');

                // Lấy level thấp nhất (quyền cao nhất) của user bị cập nhật
                $targetUserMinLevel = $user->roles()->min('level');

                // Chỉ cho phép nếu level của auth user < level của user bị cập nhật
                // (level thấp hơn = quyền cao hơn)
                if ($authUserMinLevel === null || $targetUserMinLevel === null || $authUserMinLevel >= $targetUserMinLevel) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền cập nhật người dùng này'
                    ], 403);
                }
            } 

            $updateData = [
                'email' => $body['email'] ?? null,
                'fullname' => $body['fullname'] ?? null,
                'birthday' => $body['birthday'] ?? null,
            ];

            
            if (isset($body['avatar']) && $body['avatar']) {
                // Xóa avatar cũ nếu có
                if ($user->avatar_path) {
                    $oldPath = str_replace('/storage/', '', $user->avatar_path);
                    Storage::disk('public')->delete($oldPath);
                }

                // Lưu avatar mới
                $extension = $body['avatar']->getClientOriginalExtension();
                $slugTitle = $this->createSlug($user->fullname);
                $customFileName = 'user_' . $user->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                $storedPath = $body['avatar']->storeAs('avatars', $customFileName, 'public');
                $publicUrl = Storage::url($storedPath); // ví dụ: /storage/avatars/user_1_<slug fullname>_1697059200.jpg

                $user->update(['avatar_path' => $publicUrl]);

            }

            if (isset($body['password']) && $body['password']) {
                $updateData['password'] = bcrypt($body['password'] ?? '');
            }

            $user->update($updateData);

            // Cập nhật roles
            if (isset($body['role_ids']) && $body['role_ids']) {
                $user->roles()->sync($body['role_ids']);
            }

            $user->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật người dùng thành công',
                'data' => $user->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật người dùng: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa người dùng (soft delete)
     */
    public function destroyUsers(DestroyRequest $request): JsonResponse
    {
        try {

            $body = $request->validated();

            $authUser = $request->user();

            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không được phép truy cập'
                ], 401);
            } 
            
            $users = User::with('roles')->whereIn('id', $body['user_ids'])->get();

            // Lấy level thấp nhất (quyền cao nhất) của user yêu cầu xóa
            $authUserMinLevel = $authUser?->roles()->min('level');

            $userWillDelete = [];

            foreach ($users as $user) {
                $targetUserMinLevel = $user->roles->min('level');

                // Level thấp hơn = quyền cao hơn, chỉ cho phép khi authUser < targetUser
                if ($authUserMinLevel === null || $targetUserMinLevel === null || $authUserMinLevel >= $targetUserMinLevel) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn không có quyền xóa người dùng: ' . ($user->email ?? $user->id)
                    ], 403);
                }

                $userWillDelete[] = $user;
            }

            foreach ($userWillDelete as $user) {
                $user->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa người dùng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa người dùng: ' . $e->getMessage()
            ], 500);
        }
    } 
}

