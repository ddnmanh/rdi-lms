<?php

namespace App\Http\Controllers;

use App\Http\Requests\lessons\DestroyRequest;
use App\Http\Requests\lessons\GetAllRequest;
use App\Http\Requests\lessons\StoreRequest;
use App\Http\Requests\lessons\UpdateRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    /**
     * Danh sách bài học
     *
     * Query params:
     * - orphaned: Lọc theo không thuộc về khóa học nào
     * - course_id: Lọc theo khóa học
     * - search: Tìm kiếm theo title hoặc description
     * - duration_min: Lọc thời lượng tối thiểu (giây)
     * - duration_max: Lọc thời lượng tối đa (giây)
     * - sort_by: Sắp xếp theo ('id', 'title', 'duration', 'course_id', 'created_at', 'updated_at', 'display_order') - mặc định: course_id
     * - order_by: Thứ tự (asc, desc) - mặc định: asc
     * - per_page: Số lượng mỗi trang - mặc định: 15
     * - page: Số trang
     */
    public function index(GetAllRequest $request): JsonResponse
    {

        try {
            $body = $request->validated();

            $query = Lesson::with('course');

            // Lọc theo khóa học
            if (isset($body['course_id']) && $body['course_id']) {
                $query->where('course_id', $body['course_id']);
            }

            if (isset($body['orphaned']) && $body['orphaned'] == true) {
                $query->where('course_id', NULL);
            }

            // Tìm kiếm
            if (isset($body['search']) && $body['search']) {
                $search = $body['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Lọc theo duration_min
            if (isset($body['duration_min']) && $body['duration_min']) {
                $query->where('duration', '>=', (int)$body['duration_min']);
            }

            // Lọc theo duration_max
            if (isset($body['duration_max']) && $body['duration_max']) {
                $query->where('duration', '<=', (int)$body['duration_max']);
            }

            // Sắp xếp
            $sortBy = $body['sort_by'] ?? 'course_id';
            $orderBy = $body['order_by'] ?? 'asc';

            // Validate sort_by
            $allowedSortBy = ['id', 'title', 'duration', 'course_id', 'created_at', 'updated_at', 'display_order'];
            if (!in_array($sortBy, $allowedSortBy)) {
                $sortBy = 'course_id';
            }

            // Validate order_by
            $orderBy = strtolower($orderBy);
            if (!in_array($orderBy, ['asc', 'desc'])) {
                $orderBy = 'asc';
            }

            $query->orderBy($sortBy, $orderBy);

            $perPage = $body['per_page'] ?? 15;
            $perPage = min(max(1, (int)$perPage), 100); // Giới hạn từ 1-100

            $lessons = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $lessons
            ]);

        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Chi tiết bài học
     */
    public function show($id)
    {
        try {
            $lesson = Lesson::with('course')->find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $lesson
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo bài học mới
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            // Validate that either video_path or video is provided
            if (!isset($body['video_path']) && !isset($body['video_file'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng cung cấp video path hoặc upload file video'
                ], 422);
            }

            $lessonData = [
                'course_id' => $body['course_id'] ?? null,
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'duration' => $body['duration'] ?? null,
                'display_order' => $body['display_order'] ?? 0,
            ];

            $lesson = Lesson::create($lessonData);


            // Sau khi tạo khóa học thành công, mới xử lý upload thumbnail nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('thumbnail_file')) {
                try {
                    $thumbnailFile = $request->file('thumbnail_file');
                    $extension = $thumbnailFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $thumbnailFile->storeAs('lesson/thumbnails', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/thumbnails/lesson_1_<slug title>_1697059200.jpg

                    // Cập nhật thumbnail vào khóa học đã tạo
                    $lesson->update(['thumbnail_path' => $publicUrl]);

                    // Refresh để lấy dữ liệu mới nhất
                    $lesson->refresh();
                } catch (\Exception $e) {
                    report($e);
                }
            }

            // Xử lý upload video nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('video_file')) {
                try {
                    $videoFile = $request->file('video_file');
                    $extension = $videoFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $videoFile->storeAs('lesson/videos', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/videos/lesson_1_<slug title>_1697059200.mp4

                    // Cập nhật video path vào bài học đã tạo
                    $lesson->update(['video_path' => $publicUrl]);

                    // Refresh để lấy dữ liệu mới nhất
                    $lesson->refresh();
                } catch (\Exception $e) {
                    report($e);
                }
            } elseif (isset($body['video_path']) && $body['video_path']) {
                $lesson->update(['video_path' => $body['video_path']]);
            }

            $lesson->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Tạo bài học thành công',
                'data' => $lesson->load('course')
            ], 201);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bài học
     */
    public function update(UpdateRequest $request, $id)
    {

        try {
            $body = $request->validated();

            $lesson = Lesson::find($id);

            if (!$lesson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy bài học'
                ], 404);
            }

            // Validate that either video_path or video is provided (or keep existing)
            // if (!isset($body['video_path']) && !isset($body['video_file'])) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Vui lòng cung cấp video path hoặc upload file video'
            //     ], 422);
            // }

            $updateData = [
                'title' => $body['title'] ?? null,
                'description' => $body['description'] ?? null,
                'duration' => $body['duration'] ?? 0
            ];

            // Cập nhật course_id nếu có
            if (isset($body['course_id']) && $body['course_id']) {
                $existingCourseId = Course::find($lesson->course_id)?->id;
                if ($existingCourseId) {
                    $updateData['course_id'] = $body['course_id'];
                }
            }

            // Ghi vào database
            $lesson->update($updateData);
            $lesson->refresh();

            // Xử lý upload thumbnail nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('thumbnail_file')) {
                // Xóa thumbnail cũ nếu có
                if ($lesson->thumbnail_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->thumbnail_path);
                    Storage::disk('public')->delete($oldPath);
                }

                try {
                    $thumbnailFile = $request->file('thumbnail_file');
                    $extension = $thumbnailFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $thumbnailFile->storeAs('lesson/thumbnails', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/thumbnails/lesson_1_<slug title>_1697059200.jpg

                    // Cập nhật thumbnail vào khóa học đã tạo
                    $lesson->update(['thumbnail_path' => $publicUrl]);
                } catch (\Exception $e) {
                    report($e);
                }
            }

            // Xử lý upload video nếu có
            // Sử dụng $request->hasFile() để kiểm tra file thực sự, tránh trường hợp chuỗi "null"
            if ($request->hasFile('video_file')) {
                try {
                    $videoFile = $request->file('video_file');
                    $extension = $videoFile->getClientOriginalExtension();
                    $slugTitle = $this->createSlug($lesson->title);
                    $customFileName = 'lesson_' . $lesson->id . '_' . $slugTitle . '_' . time() . '.' . $extension;
                    $storedPath = $videoFile->storeAs('lesson/videos', $customFileName, 'public');
                    $publicUrl = Storage::url($storedPath); // ví dụ: /storage/lesson/videos/lesson_1_<slug title>_1697059200.mp4

                    // Xóa video cũ sau khi upload thành công
                    if ($lesson->video_path && $this->isLocalStorageFile($lesson->video_path)) {
                        $oldPath = str_replace('/storage/', '', $lesson->video_path);
                        Storage::disk('public')->delete($oldPath);
                    }

                    // Cập nhật video path vào bài học đã tạo
                    $lesson->update(['video_path' => $publicUrl]);
                } catch (\Exception $e) {
                    report($e);
                }
            } else {
                if (isset($body['video_path']) && $body['video_path'] != $lesson->video_path) {
                    // Lưu video path cũ để xóa sau
                    $oldVideoPath = $lesson->video_path;
                    
                    // Cập nhật video path mới
                    $lesson->update(['video_path' => $body['video_path']]);
                    
                    // Xóa video cũ nếu là file local storage (không phải URL bên ngoài hoặc background upload)
                    // Chỉ xóa sau khi cập nhật thành công
                    if ($oldVideoPath && $this->isLocalStorageFile($oldVideoPath)) {
                        $oldPath = str_replace('/storage/', '', $oldVideoPath);
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }

            // Refresh để lấy dữ liệu mới nhất
            $lesson->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài học thành công',
                'data' => $lesson->load('course')
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bài học (soft delete)
     */
    public function destroy(DestroyRequest $request): JsonResponse
    {
        try {
            $body = $request->validated();

            // Chỉ được phép xóa các bài học mồ côi
            $lessonIds = $body['lesson_ids'] ?? [];
            $lessons = Lesson::whereIn('id', $lessonIds)
                ->whereNull('course_id')
                ->get();

            foreach ($lessons as $lesson) {
                // Xóa file thumbnail nếu có
                if ($lesson->thumbnail_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->thumbnail_path);
                    Storage::disk('public')->delete($oldPath);
                }
                // Xóa file video nếu có
                if ($lesson->video_path) {
                    $oldPath = str_replace('/storage/', '', $lesson->video_path);
                    Storage::disk('public')->delete($oldPath);
                }

                // Xóa mềm
                // $lesson->delete();
                // Xóa cứng
                $lesson->forceDelete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Xóa bài học thành công'
            ]);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra xem path có phải là file local storage không
     * Không xóa nếu là URL external hoặc placeholder background-upload
     */
    private function isLocalStorageFile($path)
    {
        if (empty($path)) {
            return false;
        }

        // Không xóa nếu là URL bên ngoài (http://, https://)
        if (preg_match('/^https?:\/\//', $path)) {
            return false;
        }

        // Không xóa nếu là placeholder background upload
        if (strpos($path, 'background-upload://') === 0) {
            return false;
        }

        // Chỉ xóa file local storage (bắt đầu bằng /storage/)
        return strpos($path, '/storage/') === 0;
    }
}

