<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Lấy danh sách tất cả permissions
     *
     * Query params:
     * - search: Tìm kiếm theo name, description, group
     * - group: Lọc theo nhóm permission
     * - method: Lọc theo HTTP method (GET, POST, PUT, DELETE,...)
     * - sort_by: Sắp xếp theo (id, name, group, created_at) - mặc định: group
     * - order_by: Thứ tự (asc, desc) - mặc định: asc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Permission::query();

            // Tìm kiếm (loại bỏ dấu tiếng Việt)
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $searchNoAccent = $this->removeVietnameseTones($search);

                $query->where(function ($q) use ($search, $searchNoAccent) {
                    // Tìm kiếm có dấu
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('group', 'like', "%{$search}%");

                    // Tìm kiếm không dấu nếu search term khác với bản không dấu
                    if ($search !== $searchNoAccent) {
                        $q->orWhere('name', 'like', "%{$searchNoAccent}%")
                            ->orWhere('description', 'like', "%{$searchNoAccent}%")
                            ->orWhere('group', 'like', "%{$searchNoAccent}%");
                    }
                });
            }

            // Lọc theo group
            if ($request->has('group') && $request->group) {
                $query->where('group', $request->group);
            }

            // Lọc theo method
            if ($request->has('method') && $request->method) {
                $query->where('method', strtoupper($request->method));
            }

            // Sắp xếp
            $sortBy = $request->input('sort_by', 'group');
            $orderBy = $request->input('order_by', 'asc');

            // Validate sort_by
            $allowedSortBy = ['id', 'name', 'group', 'method', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortBy)) {
                $sortBy = 'group';
            }

            // Validate order_by
            $orderBy = strtolower($orderBy);
            if (!in_array($orderBy, ['asc', 'desc'])) {
                $orderBy = 'asc';
            }

            $query->orderBy($sortBy, $orderBy);

            // Nếu có per_page thì phân trang, ngược lại lấy tất cả
            if ($request->has('per_page')) {
                $perPage = $request->input('per_page', 15);
                $permissions = $query->paginate($perPage);
            } else {
                $permissions = $query->paginate(999999);
            }

            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi lấy danh sách permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Loại bỏ dấu tiếng Việt
     */
    private function removeVietnameseTones($str)
    {
        $vietnameseTones = [
            'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ằ', 'ắ', 'ẳ', 'ẵ', 'ặ', 'â', 'ầ', 'ấ', 'ẩ', 'ẫ', 'ậ',
            'À', 'Á', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ằ', 'Ắ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ầ', 'Ấ', 'Ẩ', 'Ẫ', 'Ậ',
            'è', 'é', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ề', 'ế', 'ể', 'ễ', 'ệ',
            'È', 'É', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ề', 'Ế', 'Ể', 'Ễ', 'Ệ',
            'ì', 'í', 'ỉ', 'ĩ', 'ị',
            'Ì', 'Í', 'Ỉ', 'Ĩ', 'Ị',
            'ò', 'ó', 'ỏ', 'õ', 'ọ', 'ô', 'ồ', 'ố', 'ổ', 'ỗ', 'ộ', 'ơ', 'ờ', 'ớ', 'ở', 'ỡ', 'ợ',
            'Ò', 'Ó', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ồ', 'Ố', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ờ', 'Ớ', 'Ở', 'Ỡ', 'Ợ',
            'ù', 'ú', 'ủ', 'ũ', 'ụ', 'ư', 'ừ', 'ứ', 'ử', 'ữ', 'ự',
            'Ù', 'Ú', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ừ', 'Ứ', 'Ử', 'Ữ', 'Ự',
            'ỳ', 'ý', 'ỷ', 'ỹ', 'ỵ',
            'Ỳ', 'Ý', 'Ỷ', 'Ỹ', 'Ỵ',
            'đ', 'Đ'
        ];

        $replacements = [
            'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
            'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
            'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
            'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
            'i', 'i', 'i', 'i', 'i',
            'I', 'I', 'I', 'I', 'I',
            'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
            'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
            'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
            'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
            'y', 'y', 'y', 'y', 'y',
            'Y', 'Y', 'Y', 'Y', 'Y',
            'd', 'D'
        ];

        return str_replace($vietnameseTones, $replacements, $str);
    }
}

