<?php

namespace App\Http\Controllers;

use App\Http\Requests\courses\AddLessonRequest;
use App\Http\Requests\courses\AddRemoveUsersRequest;
use App\Http\Requests\courses\DestroyRequest;
use App\Http\Requests\courses\GetAllRequest;
use App\Http\Requests\courses\StoreRequest;
use App\Http\Requests\courses\UpdateRequest;
use App\Http\Requests\courses\RemoveLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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
    public function index(GetAllRequest $request): JsonResponse
    {
        try {

            $body = $request->validated();

            $query = Course::with('lessons', 'users')->withCount('users', 'lessons');

            // Nếu không phải root/admin thì chỉ thấy các khóa học do mình tạo
            if (!$request->user()->hasRole('ROOT') && !$request->user()->hasRole('ADMIN')) {
                // Chỉ thấy các khóa học do mình tạo
                $query->where('created_by', $request->user()->id);
            }

            // Tìm kiếm
            if (isset($body['search']) && $body['search']) {
                $search = $body['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Lọc theo user_id
            if (isset($body['user_id']) && $body['user_id']) {
                $query->whereHas('users', function ($q) use ($body) {
                    $q->where('users.id', $body['user_id']);
                });
            }

            // Lọc theo start_date (từ)
            if (isset($body['start_date_from']) && $body['start_date_from']) {
                $query->whereDate('start_date', '>=', $body['start_date_from']);
            }

            // Lọc theo start_date (đến)
            if (isset($body['start_date_to']) && $body['start_date_to']) {
                $query->whereDate('start_date', '<=', $body['start_date_to']);
            }

            // Lọc theo end_date (từ)
            if (isset($body['end_date_from']) && $body['end_date_from']) {
                $query->whereDate('end_date', '>=', $body['end_date_from']);
            }

            // Lọc theo end_date (đến)
            if (isset($body['end_date_to']) && $body['end_date_to']) {
                $query->whereDate('end_date', '<=', $body['end_date_to']);
            }

            // Lọc theo date_range
            if (isset($body['date_range']) && $body['date_range']) {
                $now = now();
                switch (strtolower($body['date_range'])) {
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
            if (isset($body['min_users']) && $body['min_users'] !== null) {
                $query->having('users_count', '>=', (int)$body['min_users']);
            }

            if (isset($body['max_users']) && $body['max_users'] !== null) {
                $query->having('users_count', '<=', (int)$body['max_users']);
            }

            // Lọc theo số lượng lessons
            if (isset($body['min_lessons']) && $body['min_lessons'] !== null) {
                $query->having('lessons_count', '>=', (int)$body['min_lessons']);
            }

            if (isset($body['max_lessons']) && $body['max_lessons'] !== null) {
                $query->having('lessons_count', '<=', (int)$body['max_lessons']);
            }

            // Sắp xếp
            $sortBy = $body['sort_by'] ?? 'id';
            $orderBy = $body['order_by'] ?? 'desc';

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

            $perPage = $body['per_page'] ?? 15;
            // $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

            $courses = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết khóa học
     */
    public function show($id)
    {
        try {
            $course = Course::with(['lessons' => function ($query) {
                $query->orderBy('display_order');
            }, 'users'])->find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            // Lấy thông tin user tạo khóa học
            $course->load('creator:id,fullname,email,avatar_path');

            // Lấy tất cả lesson IDs của course
            $lessonIds = $course->lessons->pluck('id');

            // Lấy tất cả user IDs
            $userIds = $course->users->pluck('id');

            // Lấy tất cả lesson_views cho các user trong course
            $lessonViews = \App\Models\LessonView::whereIn('user_id', $userIds)
                ->whereIn('lesson_id', $lessonIds)
                ->get()
                ->groupBy('user_id');

            for ($i = 0; $i < count($course->users); $i++) {
                $userId = $course->users[$i]->id;

                $course->users[$i]->course_progress = [
                    'completion_percentage' => $course->users[$i]->pivot->completion_percentage,
                    'is_passed' => $course->users[$i]->pivot->is_passed,
                    'created_at' => $course->users[$i]->pivot->created_at,
                    'updated_at' => $course->users[$i]->pivot->updated_at,
                ];

                // Gán lesson_views cho từng user
                $course->users[$i]->lesson_progress = $lessonViews->get($userId, collect());

                unset($course->users[$i]->pivot);
            }

            return response()->json([
                'success' => true,
                'data' => $course
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo khóa học mới
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            // Chuyển đổi local time sang UTC nếu có timezone
            $timezone = isset($body['timezone']) ? $body['timezone'] : null;
            $startDate = isset($body['start_date']) ? $this->convertDateTimeToUTC($body['start_date'], $timezone) : null;
            $endDate = isset($body['end_date']) ? $this->convertDateTimeToUTC($body['end_date'], $timezone) : null;

            // Tạo khóa học trước (không có thumbnail)
            $courseData = [
                'title' => isset($body['title']) ? $body['title'] : null,
                'description' => isset($body['description']) ? $body['description'] : null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_by' => $request->user()->id,
            ];

            $course = Course::create($courseData);

            // Sau khi tạo khóa học thành công, mới xử lý upload thumbnail nếu có
            if ($request->hasFile('thumbnail')) {
                try {

                    $thumbnailFile = $request->file('thumbnail');
                    $extension = $thumbnailFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($course->title);
                    $customFileName = 'course_' . $course->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $thumbnailFile->storeAs('course/thumbnails', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/course/thumbnails/course_1_<slug title>_1697059200.jpg

                    // Cập nhật thumbnail vào khóa học đã tạo
                    $course->update(['thumbnail_path' => $publicUrl]);

                    // Refresh để lấy dữ liệu mới nhất
                    $course->refresh();
                } catch (\Exception $e) {
                    report($e);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tạo khóa học thành công',
                'data' => $course
            ], 201);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo khóa học: ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Cập nhật khóa học
     */
    public function update(UpdateRequest $request, $id): JsonResponse
    {
        try {
            $body = $request->validated();

            $course = Course::find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            // Chuyển đổi local time sang UTC nếu có timezone
            $timezone = isset($body['timezone']) ? $body['timezone'] : null;
            $startDate = isset($body['start_date']) ? $this->convertDateTimeToUTC($body['start_date'], $timezone) : null;
            $endDate = isset($body['end_date']) ? $this->convertDateTimeToUTC($body['end_date'], $timezone) : null;

            $updateData = [
                'title' => isset($body['title']) ? $body['title'] : null,
                'description' => isset($body['description']) ? $body['description'] : null,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ];

            // Xử lý upload thumbnail nếu có
            if (isset($body['thumbnail']) && $body['thumbnail']) {
                // Xóa thumbnail cũ nếu có
                if ($course->thumbnail_path) {
                    $oldPath = str_replace('/storage/', '', $course->thumbnail_path);
                    Storage::disk('public')->delete($oldPath);
                }

                // Lưu thumbnail mới
                $extension = $body['thumbnail']->getClientOriginalExtension();
                $slugTitle = $this->createSlug($course->title);
                $customFileName = 'course_' . $course->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                $storedPath = $body['thumbnail']->storeAs('course-thumbnails', $customFileName, 'public');
                $publicUrl = Storage::url($storedPath); // ví dụ: /storage/course-thumbnails/course_1_<slug title>_1697059200.jpg

                $updateData['thumbnail_path'] = $publicUrl;
            }

            $course->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khóa học thành công',
                'data' => $course
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa khóa học (soft delete)
     */
    public function destroy(DestroyRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();
            $courseIds = isset($body['course_ids']) ? $body['course_ids'] : [];
            $courses = Course::whereIn('id', $courseIds)->get();

            foreach ($courses as $course) {
            // Xóa thumbnail nếu có
            if ($course->thumbnail_path) {
                $oldPath = str_replace('/storage/', '', $course->thumbnail_path);
                Storage::disk('public')->delete($oldPath);
            }

            // Xóa quan hệ với users
            $course->users()->detach();

            // Cập nhật course_id = null cho các lessons
            Lesson::where('course_id', $course->id)->update(['course_id' => null, 'display_order' => null]);

            // Xóa vĩnh viễn course
            $course->forceDelete();
            }

            return response()->json([
            'success' => true,
            'message' => 'Xóa khóa học thành công'
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
            'success' => false,
            'message' => 'Lỗi khi xóa khóa học: ' . $e->getMessage()
            ], 500);
        }

        // try {
        //     $body = $request->validated();
        //     $courseIds = isset($body['course_ids']) ? $body['course_ids'] : [];
        //     $courses = Course::whereIn('id', $courseIds)->get();

        //     foreach ($courses as $course) {
        //         $course->delete();
        //         Lesson::where('course_id', $course->id)->delete();
        //         $course->users()->detach();
        //     }

        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Xóa khóa học thành công'
        //     ]);
        // } catch (\Exception $e) {
        //     report($e);
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Lỗi khi xóa khóa học: ' . $e->getMessage()
        //     ], 500);
        // }
    }

    /**
     * Thêm n sinh viên vào khóa học
     */
    public function addUsers(AddRemoveUsersRequest $request, $id): JsonResponse
    {
        try {
            $body = $request->validated();

            $course = Course::find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $userIds = isset($body['user_ids']) ? $body['user_ids'] : [];

            // Lấy danh sách user_id hiện có trong khóa học
            $existingUserIds = $course->users()->pluck('user_id')->toArray();

            // sync() sẽ tự động:
            // - Thêm user mới (có trong $userIds nhưng chưa có trong DB)
            // - Xóa user cũ (có trong DB nhưng không có trong $userIds)
            // - Giữ nguyên user đã tồn tại (có trong cả DB và $userIds)
            $syncData = $existingUserIds;
            foreach ($userIds as $userId) {
                if (in_array($userId, $existingUserIds)) {
                    // User đã tồn tại, không set gì để giữ nguyên mọi thứ
                    $syncData[$userId] = [];
                } else {
                    // User mới, set cả created_at và updated_at
                    $syncData[$userId] = [
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }

            $course->users()->sync($syncData);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật danh sách sinh viên thành công',
                'data' => $course->load('users')
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật danh sách sinh viên: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Xóa n sinh viên khỏi khóa học
     */
    public function removeUsers(AddRemoveUsersRequest $request, $id): JsonResponse
    {
        try {
            $body = $request->validated();

            $course = Course::find($id);

            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $userIds = isset($body['user_ids']) ? $body['user_ids'] : [];
            $course->users()->detach($userIds);

            return response()->json([
                'success' => true,
                'message' => 'Xóa sinh viên thành công',
                'data' => $course->load('users')
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa sinh viên khỏi khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm n bài học vào khóa học
     */
    public function addLessons(AddLessonRequest $request, $id): JsonResponse
    {
        try {
            $body = $request->validated();

            $course = Course::find($id);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $lessons = isset($body['lessons']) ? $body['lessons'] : [];
            $items   = collect($lessons)->map(fn($it) => ['id' => (int)$it['id'], 'display_order' => (int)$it['display_order']]);
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
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm bài học vào khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa n bài học khỏi khóa học
     */
    public function removeLessons(RemoveLessonRequest $request, $id)
    {
        try {
            $body = $request->validated();

            $course = Course::find($id);
            if (!$course) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khóa học'
                ], 404);
            }

            $lessonIds = isset($body['lesson_ids']) ? $body['lesson_ids'] : [];
            $ids = collect($lessonIds)->map(fn($v) => (int)$v)->values();

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
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài học khỏi khóa học: ' . $e->getMessage()
            ], 500);
        }
    }
}
