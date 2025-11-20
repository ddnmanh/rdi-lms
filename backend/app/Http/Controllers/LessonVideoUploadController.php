<?php

namespace App\Http\Controllers;

use App\Http\Requests\lessonVideoUploads\CompleteUploadRequest;
use App\Http\Requests\lessonVideoUploads\CreateSessionRequest;
use App\Http\Requests\lessonVideoUploads\UploadChunkRequest;
use App\Jobs\ProcessLessonVideoUpload;
use App\Models\LessonVideoUpload;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Controller xử lý upload video bài học theo phương thức chunked upload
 * Cho phép upload file lớn bằng cách chia nhỏ thành các chunk
 */
class LessonVideoUploadController extends Controller
{
    /**
     * Tạo phiên upload mới cho video
     *
     * Khởi tạo một session upload với các thông tin:
     * - Kích thước file, số lượng chunk cần upload
     * - Thư mục tạm để lưu các chunk
     * - Trạng thái ban đầu là PENDING
     *
     * @param CreateSessionRequest $request Request chứa thông tin file (tên, kích thước, mime type)
     * @return JsonResponse Trả về thông tin session: upload_id, chunk_size, total_chunks
     */
    public function createSession(CreateSessionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Xác định kích thước mỗi chunk (mặc định 50MB)
            $chunkSize = $data['chunk_size'] ?? (50 * 1024 * 1024);

            // Tính tổng số chunk cần upload dựa trên kích thước file
            $totalChunks = (int) ceil($data['file_size'] / $chunkSize);

            // Tạo thư mục tạm với UUID ngẫu nhiên để lưu các chunk
            $tempDirectory = 'lesson-video-uploads/' . Str::uuid()->toString();

            // Đảm bảo thư mục tạm đã được tạo
            $this->ensureDirectoryExists(storage_path('app/' . $tempDirectory));

            // Tạo bản ghi upload trong database để tracking
            $upload = LessonVideoUpload::create([
                'lesson_id' => $data['lesson_id'] ?? null,
                'user_id' => auth()->id(),
                'original_name' => $data['file_name'],
                'mime_type' => $data['mime_type'] ?? null,
                'size_bytes' => $data['file_size'],
                'chunk_size' => $chunkSize,
                'total_chunks' => $totalChunks,
                'uploaded_chunks' => 0,
                'status' => LessonVideoUpload::STATUS_PENDING,
                'storage_disk' => 'public',
                'temp_directory' => $tempDirectory,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phiên upload đã được tạo thành công.',
                'data' => [
                    'upload_id' => $upload->id,
                    'chunk_size' => $chunkSize,
                    'total_chunks' => $totalChunks,
                    'status' => $upload->status,
                    'temp_directory' => $tempDirectory,
                ],
            ], 201);
        } catch (\Exception $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo phiên upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload một chunk của file video
     *
     * Nhận và lưu từng chunk vào thư mục tạm. Kiểm tra:
     * - Trạng thái upload có hợp lệ không
     * - Chunk index có vượt quá tổng số chunk không
     * - Kích thước chunk có phù hợp không
     *
     * @param UploadChunkRequest $request Request chứa chunk file và chunk_index
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return JsonResponse Trả về tiến trình upload hiện tại
     */
    public function uploadChunk(UploadChunkRequest $request, $IdLessonVideoUpload): JsonResponse
    {
        // Không sử dụng session để tránh session locking làm chậm xử lý song song
        // Tất cả xác thực đều dựa vào JWT token trong header
        
        $lessonVideoUpload = LessonVideoUpload::findOrFail($IdLessonVideoUpload);
        $this->authorizeUploadAccess($lessonVideoUpload);

        // Kiểm tra trạng thái upload: không cho phép upload chunk nếu đã hoàn thành, thất bại hoặc đang xử lý
        // Sử dụng cache thay vì query DB liên tục để giảm database locking
        $status = $lessonVideoUpload->status;
        if (in_array($status, [
            LessonVideoUpload::STATUS_COMPLETED,
            LessonVideoUpload::STATUS_FAILED,
            LessonVideoUpload::STATUS_PROCESSING,
        ])) {
            return response()->json([
                'success' => false,
                'message' => 'Upload không thể nhận chunk mới ở trạng thái hiện tại.',
            ], 409);
        }

        $chunkIndex = (int) $request->input('chunk_index');

        // Validate chunk index: không được vượt quá tổng số chunk
        if ($chunkIndex > $lessonVideoUpload->total_chunks) {
            return response()->json([
                'success' => false,
                'message' => 'chunk_index vượt quá kích thước tệp dự kiến.',
            ], 422);
        }

        $chunkFile = $request->file('chunk');
        $tmpDirectory = storage_path('app/' . $lessonVideoUpload->temp_directory);
        $this->ensureDirectoryExists($tmpDirectory);

        // Validate kích thước chunk: không được vượt quá chunk_size + 1MB dung sai
        // (chunk cuối cùng có thể nhỏ hơn nên chỉ check các chunk trước đó)
        $expectedChunkSize = $lessonVideoUpload->chunk_size;
        if (
            $chunkIndex < $lessonVideoUpload->total_chunks
            && $chunkFile->getSize() > ($expectedChunkSize + 1048576) // cho phép lệch 1MB
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Chunk vượt quá kích thước mong đợi.',
            ], 422);
        }

        // Tạo tên file chunk với format 000001.part, 000002.part, ...
        $chunkFileName = sprintf('%06d.part', $chunkIndex);
        $chunkPath = $tmpDirectory . '/' . $chunkFileName;
        $chunkAlreadyExists = File::exists($chunkPath);

        // Sử dụng stream để copy chunk vào thư mục tạm (tiết kiệm memory cho file lớn)
        $chunkStream = fopen($chunkFile->getRealPath(), 'rb');
        $destination = fopen($chunkPath, 'wb');
        stream_copy_to_stream($chunkStream, $destination);
        fclose($chunkStream);
        fclose($destination);

        // Cập nhật trạng thái từ PENDING sang UPLOADING (chỉ thực hiện một lần)
        if ($lessonVideoUpload->status === LessonVideoUpload::STATUS_PENDING) {
            $lessonVideoUpload->status = LessonVideoUpload::STATUS_UPLOADING;
            $lessonVideoUpload->save();
        }

        // Không cập nhật uploaded_chunks vào DB để tránh Row Locking gây chậm trễ
        // Client sẽ tự theo dõi tiến độ upload

        return response()->json([
            'success' => true,
            'data' => [
                'chunk_index' => $chunkIndex,
                'message' => 'Chunk uploaded successfully'
            ],
        ]);
    }

    /**
     * Hoàn tất quá trình upload và bắt đầu ghép file
     *
     * Kiểm tra tất cả chunk đã upload xong, sau đó:
     * - Cập nhật trạng thái sang PROCESSING
     * - Dispatch job để ghép các chunk thành file hoàn chỉnh
     * - Di chuyển file vào storage chính thức
     *
     * @param CompleteUploadRequest $request Request có thể chứa lesson_id nếu chưa set
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return JsonResponse Trả về thông tin upload đã được xử lý
     */
    public function complete(CompleteUploadRequest $request, $IdLessonVideoUpload): JsonResponse
    {
        $lessonVideoUpload = LessonVideoUpload::findOrFail($IdLessonVideoUpload);
        $this->authorizeUploadAccess($lessonVideoUpload);

        // Nếu đang xử lý rồi thì trả về thông báo (tránh dispatch job trùng lặp)
        if ($lessonVideoUpload->status === LessonVideoUpload::STATUS_PROCESSING) {
            return response()->json([
                'success' => true,
                'message' => 'Video đang được xử lý.',
                'data' => $this->presentUpload($lessonVideoUpload),
            ]);
        }

        // Kiểm tra số lượng file chunk thực tế trên ổ cứng
        // Vì ta không cập nhật DB mỗi khi upload chunk để tối ưu tốc độ
        $tmpDirectory = storage_path('app/' . $lessonVideoUpload->temp_directory);
        $uploadedFiles = glob($tmpDirectory . '/*.part');
        $uploadedCount = $uploadedFiles ? count($uploadedFiles) : 0;

        // Kiểm tra tất cả chunk đã upload đủ chưa
        if ($uploadedCount < $lessonVideoUpload->total_chunks) {
            return response()->json([
                'success' => false,
                'message' => "Vẫn còn chunk chưa upload xong. Đã nhận: $uploadedCount / {$lessonVideoUpload->total_chunks}",
            ], 409);
        }

        // Cập nhật số lượng chunk đã upload vào DB
        $lessonVideoUpload->uploaded_chunks = $uploadedCount;

        // Nếu đã hoàn thành rồi thì không cần xử lý lại
        if ($lessonVideoUpload->status === LessonVideoUpload::STATUS_COMPLETED) {
            return response()->json([
                'success' => true,
                'message' => 'Upload đã hoàn tất trước đó.',
                'data' => $this->presentUpload($lessonVideoUpload),
            ]);
        }

        // Cập nhật lesson_id (nếu có) và chuyển trạng thái sang PROCESSING
        $lessonVideoUpload->update([
            'lesson_id' => $request->input('lesson_id', $lessonVideoUpload->lesson_id),
            'status' => LessonVideoUpload::STATUS_PROCESSING,
        ]);

        // Dispatch job xử lý background để ghép các chunk thành file hoàn chỉnh
        ProcessLessonVideoUpload::dispatch($lessonVideoUpload->id);

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu ghép file đã được đưa vào hàng đợi.',
            'data' => $this->presentUpload($lessonVideoUpload->fresh()),
        ]);
    }

    /**
     * Lấy thông tin chi tiết của một session upload
     *
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return JsonResponse Trả về thông tin upload: tiến trình, trạng thái, error nếu có
     */
    public function show($IdLessonVideoUpload): JsonResponse
    {
        $lessonVideoUpload = LessonVideoUpload::findOrFail($IdLessonVideoUpload);
        $this->authorizeUploadAccess($lessonVideoUpload);

        return response()->json([
            'success' => true,
            'data' => $this->presentUpload($lessonVideoUpload),
        ]);
    }

    /**
     * Hủy session upload và xóa các chunk đã upload
     *
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return JsonResponse Xác nhận upload đã bị hủy
     */
    public function cancel($IdLessonVideoUpload): JsonResponse
    {
        $lessonVideoUpload = LessonVideoUpload::findOrFail($IdLessonVideoUpload);
        $this->authorizeUploadAccess($lessonVideoUpload);

        // Xóa toàn bộ thư mục tạm và các chunk bên trong
        $this->cleanupTempDirectory($lessonVideoUpload);

        // Đánh dấu upload là FAILED với lý do hủy bởi người dùng
        $lessonVideoUpload->update([
            'status' => LessonVideoUpload::STATUS_FAILED,
            'error_message' => 'Người dùng hủy upload.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Upload đã bị hủy.',
        ]);
    }

    /**
     * Format dữ liệu upload để trả về API response
     *
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return array Mảng dữ liệu đã format bao gồm progress, status, timestamps
     */
    protected function authorizeUploadAccess(LessonVideoUpload $lessonVideoUpload): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(response()->json([
                'success' => false,
                'message' => 'Người dùng chưa được xác thực.'
            ], 401));
        }

        if (!$lessonVideoUpload->user_id) {
            $lessonVideoUpload->user_id = $user->id;
            $lessonVideoUpload->save();
            return;
        }

        if ($lessonVideoUpload->user_id === $user->id) {
            return;
        }

        if ($user instanceof User && $user->isRoot()) {
            return;
        }

        abort(response()->json([
            'success' => false,
            'message' => 'Bạn không có quyền thao tác với phiên upload này.'
        ], 403));
    }

    protected function presentUpload(LessonVideoUpload $lessonVideoUpload): array
    {
        return [
            'id' => $lessonVideoUpload->id,
            'lesson_id' => $lessonVideoUpload->lesson_id,
            'original_name' => $lessonVideoUpload->original_name,
            'status' => $lessonVideoUpload->status,
            'uploaded_chunks' => $lessonVideoUpload->uploaded_chunks,
            'total_chunks' => $lessonVideoUpload->total_chunks,
            'storage_path' => $lessonVideoUpload->storage_path,
            'progress' => $lessonVideoUpload->total_chunks > 0
                ? ($lessonVideoUpload->uploaded_chunks / $lessonVideoUpload->total_chunks) * 100
                : 0,
            'error_message' => $lessonVideoUpload->error_message,
            'processing_started_at' => $lessonVideoUpload->processing_started_at,
            'processing_finished_at' => $lessonVideoUpload->processing_finished_at,
        ];
    }

    /**
     * Xóa thư mục tạm chứa các chunk
     *
     * @param LessonVideoUpload $lessonVideoUpload Model của session upload
     * @return void
     */
    protected function cleanupTempDirectory(LessonVideoUpload $lessonVideoUpload): void
    {
        $tmpDirectory = storage_path('app/' . $lessonVideoUpload->temp_directory);

        if (File::isDirectory($tmpDirectory)) {
            File::deleteDirectory($tmpDirectory);
        }
    }

    /**
     * Đảm bảo thư mục tồn tại, nếu chưa có thì tạo mới
     *
     * @param string $path Đường dẫn tuyệt đối đến thư mục
     * @return void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }
}
