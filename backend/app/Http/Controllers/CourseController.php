<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        $courseData = [
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        // Xử lý upload thumbnail nếu có
        if ($request->hasFile('thumbnail')) {
            $storedPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $publicUrl = Storage::url($storedPath); // ví dụ: /storage/course-thumbnails/xxx.jpg
            $courseData['thumbnail'] = $publicUrl;
        }

        $course = Course::create($courseData);

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
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        // Xử lý upload thumbnail nếu có
        if ($request->hasFile('thumbnail')) {
            // Xóa thumbnail cũ nếu có
            if ($course->thumbnail) {
                $oldPath = str_replace('/storage/', '', $course->thumbnail);
                Storage::disk('public')->delete($oldPath);
            }

            // Lưu thumbnail mới
            $storedPath = $request->file('thumbnail')->store('course-thumbnails', 'public');
            $publicUrl = Storage::url($storedPath);
            $updateData['thumbnail'] = $publicUrl;
        }

        $course->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật khóa học thành công',
            'data' => $course
        ]);
    }

    /**
     * Xóa khóa học (soft delete)
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $courses = Course::whereIn('id', $request->course_ids)->get();

        foreach ($courses as $course) {
            $course->delete();
            Lesson::where('course_id', $course->id)->delete();
            $course->users()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Xóa khóa học thành công'
        ]);
    }

    /**
     * Thêm n sinh viên vào khóa học
     */
    public function addUsers(Request $request, $id)
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
            'message' => 'Thêm sinh viên vào khóa học thành công',
            'data' => $course->load('users')
        ]);
    }


    /**
     * Xóa n sinh viên khỏi khóa học
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
     * Thêm và xóa n bài học của khóa học
     */
    public function addAndRemoveLessons(Request $request, $id)
    {

        $course = Course::find($id);
        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'lessons'                 => 'required|array',
            'lessons.*.id'            => 'required|integer|exists:lessons,id|distinct',
            'lessons.*.display_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Lấy danh sách hiện tại của khóa
        $oldLessonsOfCourse = Lesson::where('course_id', $id)->get();

        // Chuẩn hoá input
        $newItems = collect($request->input('lessons', []))
            ->map(fn($item) => [
                'id'            => (int) data_get($item, 'id'),
                'display_order' => (int) data_get($item, 'display_order', 0),
            ]);

        $newIds = $newItems->pluck('id')->values();

        // 1) Gỡ những bài cũ KHÔNG còn trong request: đặt null course_id & display_order
        if ($newIds->isEmpty()) {
            // Nếu request trống, gỡ toàn bộ bài của khóa
            Lesson::where('course_id', $id)->update([
                'course_id'     => null,
                'display_order' => null,
            ]);
        } else {
            Lesson::where('course_id', $id)
                ->whereNotIn('id', $newIds)
                ->update([
                    'course_id'     => null,
                    'display_order' => null,
                ]);
        }

        // 2) Với các bài trong request:
        //    - Nếu đã thuộc khóa: chỉ cập nhật display_order ([2,3,10])
        //    - Nếu chưa thuộc khóa (hoặc thuộc khóa khác): gán course_id + display_order ([5,7])
        foreach ($newItems as $item) {
            Lesson::where('id', $item['id'])->update([
                'course_id'     => $id,
                'display_order' => $item['display_order'],
            ]);
        }

        // Trả về khóa học với danh sách bài học mới (tuỳ chọn: sắp theo display_order)
        $course->load(['lessons' => fn($q) => $q->orderBy('display_order')]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bài học của khóa học thành công',
            'data'    => $course
        ]);
    }

    /**
     * Thêm n bài học vào khóa học
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
            'lessons'                 => 'required|array|min:1',
            'lessons.*.id'            => 'required|integer|exists:lessons,id|distinct',
            'lessons.*.display_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $items   = collect($request->input('lessons', []))->map(fn($it) => ['id' => (int)$it['id'], 'display_order' => (int)$it['display_order']]);
        $idToOrder = $items->pluck('display_order', 'id'); // map: id => display_order
        $ids    = $items->pluck('id')->values();

        // Lấy trạng thái hiện tại của các lesson
        $lessons = Lesson::whereIn('id', $ids)->get();

        $eligible   = $lessons->filter(fn($l) => is_null($l->course_id) || $l->course_id = $id);             // chỉ add khi course_id == null hoặc course_id là chính course này
        $conflicted = $lessons->reject(fn($l) => is_null($l->course_id));             // đã thuộc 1 khóa nào đó

        // Cập nhật từng bản ghi vì display_order khác nhau theo từng id
        foreach ($eligible as $l) {
            $l->update([
                'course_id'     => $course->id,
                'display_order' => (int)$idToOrder[$l->id],
            ]);
        }

        // Trả về kết quả chi tiết
        $course->load(['lessons' => fn($q) => $q->orderBy('display_order')]);

        return response()->json([
            'success'  => true,
            'message'  => 'Thêm bài học vào khóa học (chỉ các lesson chưa thuộc khóa nào).',
            'added'    => $eligible->pluck('id')->values(),
            'skipped'  => $conflicted->map(fn($l) => [
                'id' => $l->id,
                'reason' => "lesson đang thuộc course_id={$l->course_id}",
            ])->values(),
            'data'     => $course,
        ]);
    }

    /**
     * Xóa n bài học khỏi khóa học
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
            'lesson_ids'   => 'required|array|min:1',
            'lesson_ids.*' => 'required|integer|exists:lessons,id|distinct',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $ids = collect($request->input('lesson_ids', []))->map(fn($v) => (int)$v)->values();

        $lessons = Lesson::whereIn('id', $ids)->get();

        // Chỉ cho phép remove nếu lesson đang thuộc course hiện tại
        $eligibleIds = $lessons->filter(fn($l) => (int)$l->course_id === (int)$course->id)
                               ->pluck('id')->values();

        $ineligible = $lessons->reject(fn($l) => (int)$l->course_id === (int)$course->id)
                              ->map(fn($l) => [
                                  'id' => $l->id,
                                  'reason' => is_null($l->course_id)
                                      ? 'lesson chưa thuộc khóa nào'
                                      : "lesson thuộc course_id={$l->course_id}, không phải {$course->id}",
                              ])->values();

        if ($eligibleIds->isNotEmpty()) {
            Lesson::whereIn('id', $eligibleIds)->update([
                'course_id'     => null,
                'display_order' => null,
            ]);
        }

        $course->load(['lessons' => fn($q) => $q->orderBy('display_order')]);

        return response()->json([
            'success'   => true,
            'message'   => 'Đã remove các bài học đủ điều kiện khỏi khóa.',
            'removed'   => $eligibleIds,
            'skipped'   => $ineligible,
            'data'      => $course,
        ]);
    }
}
