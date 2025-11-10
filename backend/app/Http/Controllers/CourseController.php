<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Danh sách khóa học
     *
     * Query params:
     * - search: Tìm kiếm theo title hoặc description
     * - user_id: Lọc các khóa học có user này tham gia
     * - start_date_from: Lọc từ ngày bắt đầu (format: Y-m-d)
     * - start_date_to: Lọc đến ngày bắt đầu (format: Y-m-d)
     * - end_date_from: Lọc từ ngày kết thúc (format: Y-m-d)
     * - end_date_to: Lọc đến ngày kết thúc (format: Y-m-d)
     * - date_range: Lọc khóa học trong khoảng thời gian (active, upcoming, past, all)
     *   - active: Khóa học đang diễn ra (start_date <= now <= end_date)
     *   - upcoming: Khóa học sắp diễn ra (start_date > now)
     *   - past: Khóa học đã kết thúc (end_date < now)
     *   - all: Tất cả (mặc định)
     * - min_users: Lọc khóa học có số lượng users >= giá trị này
     * - max_users: Lọc khóa học có số lượng users <= giá trị này
     * - min_lessons: Lọc khóa học có số lượng lessons >= giá trị này
     * - max_lessons: Lọc khóa học có số lượng lessons <= giá trị này
     * - sort_by: Sắp xếp theo (id, title, start_date, end_date, created_at, users_count, lessons_count) - mặc định: id
     * - order_by: Thứ tự (asc, desc) - mặc định: desc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(Request $request)
    {
        $query = Course::with('lessons', 'users')
            ->withCount('users', 'lessons');

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Lọc theo user_id
        if ($request->has('user_id') && $request->user_id) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', $request->user_id);
            });
        }

        // Lọc theo start_date (từ)
        if ($request->has('start_date_from') && $request->start_date_from) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }

        // Lọc theo start_date (đến)
        if ($request->has('start_date_to') && $request->start_date_to) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }

        // Lọc theo end_date (từ)
        if ($request->has('end_date_from') && $request->end_date_from) {
            $query->whereDate('end_date', '>=', $request->end_date_from);
        }

        // Lọc theo end_date (đến)
        if ($request->has('end_date_to') && $request->end_date_to) {
            $query->whereDate('end_date', '<=', $request->end_date_to);
        }

        // Lọc theo date_range
        if ($request->has('date_range') && $request->date_range) {
            $now = now();
            switch (strtolower($request->date_range)) {
                case 'active':
                    $query->where(function ($q) use ($now) {
                        $q->where(function ($q2) use ($now) {
                            $q2->whereNull('start_date')
                                ->orWhereDate('start_date', '<=', $now);
                        })
                        ->where(function ($q2) use ($now) {
                            $q2->whereNull('end_date')
                                ->orWhereDate('end_date', '>=', $now);
                        });
                    });
                    break;
                case 'upcoming':
                    $query->where(function ($q) use ($now) {
                        $q->whereNotNull('start_date')
                            ->whereDate('start_date', '>', $now);
                    });
                    break;
                case 'past':
                    $query->where(function ($q) use ($now) {
                        $q->whereNotNull('end_date')
                            ->whereDate('end_date', '<', $now);
                    });
                    break;
                case 'all':
                default:
                    // Không filter gì
                    break;
            }
        }

        // Lọc theo số lượng users
        if ($request->has('min_users') && $request->min_users !== null) {
            $query->having('users_count', '>=', (int)$request->min_users);
        }

        if ($request->has('max_users') && $request->max_users !== null) {
            $query->having('users_count', '<=', (int)$request->max_users);
        }

        // Lọc theo số lượng lessons
        if ($request->has('min_lessons') && $request->min_lessons !== null) {
            $query->having('lessons_count', '>=', (int)$request->min_lessons);
        }

        if ($request->has('max_lessons') && $request->max_lessons !== null) {
            $query->having('lessons_count', '<=', (int)$request->max_lessons);
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'id');
        $orderBy = $request->get('order_by', 'desc');

        // Validate sort_by
        $allowedSortBy = ['id', 'title', 'start_date', 'end_date', 'created_at', 'updated_at', 'users_count', 'lessons_count'];
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

        $courses = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $courses
        ]);
    }

    /**
     * Chi tiết khóa học
     */
    public function show($id)
    {
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('display_order');
        }, 'users'])->find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $course
        ]);
    }

    /**
     * Tạo khóa học mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Chuyển đổi local time sang UTC nếu có timezone
        $timezone = $request->timezone;
        $startDate = $this->convertToUTC($request->start_date, $timezone);
        $endDate = $this->convertToUTC($request->end_date, $timezone);

        $course = Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo khóa học thành công',
            'data' => $course
        ], 201);
    }

    /**
     * Cập nhật khóa học
     */
    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'timezone' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Chuyển đổi local time sang UTC nếu có timezone
        $timezone = $request->timezone;
        $startDate = $this->convertToUTC($request->start_date, $timezone);
        $endDate = $this->convertToUTC($request->end_date, $timezone);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật khóa học thành công',
            'data' => $course
        ]);
    }

    /**
     * Xóa khóa học (soft delete)
     */
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa khóa học thành công'
        ]);
    }

    /**
     * Phân quyền sinh viên vào khóa học
     */
    public function assignUsers(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->users()->sync($request->user_ids);

        return response()->json([
            'success' => true,
            'message' => 'Phân quyền sinh viên thành công',
            'data' => $course->load('users')
        ]);
    }

    /**
     * Thêm sinh viên vào khóa học
     */
    public function addUser(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->users()->attach($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Thêm sinh viên thành công',
            'data' => $course->load('users')
        ]);
    }

    /**
     * Xóa sinh viên khỏi khóa học
     */
    public function removeUser(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->users()->detach($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa sinh viên thành công',
            'data' => $course->load('users')
        ]);
    }

    /**
     * Xóa nhiều sinh viên khỏi khóa học
     */
    public function removeUsers(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $course->users()->detach($request->user_ids);

        return response()->json([
            'success' => true,
            'message' => 'Xóa sinh viên thành công',
            'data' => $course->load('users')
        ]);
    }

    /**
     * Thêm một bài học vào khóa học
     */
    public function addLesson(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson = Lesson::find($request->lesson_id);
        $lesson->update(['course_id' => $course->id]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm bài học vào khóa học thành công',
            'data' => $course->load('lessons')
        ]);
    }

    /**
     * Thêm nhiều bài học vào khóa học
     */
    public function addLessons(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        Lesson::whereIn('id', $request->lesson_ids)
            ->update(['course_id' => $course->id]);

        return response()->json([
            'success' => true,
            'message' => 'Thêm bài học vào khóa học thành công',
            'data' => $course->load('lessons')
        ]);
    }

    /**
     * Xóa một bài học khỏi khóa học
     */
    public function removeLesson(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $lesson = Lesson::find($request->lesson_id);

        // Kiểm tra lesson có thuộc khóa học này không
        if ($lesson->course_id != $course->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bài học không thuộc khóa học này'
            ], 422);
        }

        $lesson->update(['course_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài học khỏi khóa học thành công',
            'data' => $course->load('lessons')
        ]);
    }

    /**
     * Xóa nhiều bài học khỏi khóa học
     */
    public function removeLessons(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lesson_ids' => 'required|array',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Chỉ xóa các lesson thuộc khóa học này
        Lesson::whereIn('id', $request->lesson_ids)
            ->where('course_id', $course->id)
            ->update(['course_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Xóa bài học khỏi khóa học thành công',
            'data' => $course->load('lessons')
        ]);
    }
}

