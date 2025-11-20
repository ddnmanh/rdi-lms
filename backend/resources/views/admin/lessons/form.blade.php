@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm bài học' : 'Chỉnh sửa bài học')

@section('description', $mode === 'CREATE' ? 'Thêm bài học mới vào hệ thống' : 'Chỉnh sửa thông tin bài học')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">

    {{-- Form Card --}}
    <form id="lessonForm" onsubmit="saveLesson(event)" class="w-full max-w-[1400px] mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="lessonId" value="{{ $mode === 'EDIT' ? ($lessonId ?? '') : '' }}">
        <input type="hidden" id="thumbnail_path" value="">
        <div class="flex flex-col gap-6">

            {{-- Thumbnail Upload Section --}}
            <div class="flex flex-col gap-0.5">
                <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh thumbnail</label>
                <div
                    id="thumbnailDropZone"
                    class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                >
                    <div class="w-[150px] aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <img id="thumbnailPreview" alt="thumbnail preview" class="h-full w-full object-cover hidden">
                        <i id="thumbnailIconPlaceholder" class="fa-solid fa-image text-[20px] text-gray-400"></i>
                    </div>

                    <div class="flex-1">
                        <div class="text-sm text-gray-600 dark:text-gray-300">Kéo & thả ảnh vào đây, hoặc</div>
                        <div class="mt-2 flex items-center gap-3">
                            <label for="thumbnail" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                Chọn ảnh
                            </label>
                            <button
                                id="btnClearNewThumbnail"
                                type="button"
                                class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700 text-sm"
                            >
                                Xóa ảnh mới
                            </button>
                            <input
                                id="thumbnail"
                                type="file"
                                accept="image/*"
                                class="hidden"
                            />
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Hỗ trợ PNG, JPG, WEBP, GIF — Tối đa 2MB</div>
                    </div>
                </div>
            </div>

            {{-- Video Upload Section --}}
            <div class="flex flex-col gap-0.5">
                <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Video <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2 mb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_type" value="file" id="video_type_file" checked class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Video file</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_type" value="url" id="video_type_url" class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">URL Video</span>
                    </label>
                </div>

                {{-- Video File Upload --}}
                <div id="videoFileSection">
                    <div
                        id="videoDropZone"
                        class="flex flex-col items-stretch justify-start gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >

                        <div class="flex flex-row items-start gap-4">
                            <div id="videoPreviewContainer" class="flex flex-col gap-2">
                                <div class="relative w-[150px] aspect-video bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center">
                                    <video id="videoFilePreview" controls class="hidden w-full h-full object-cover bg-black"></video>
                                    <iframe id="videoUrlPreview" class="hidden w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                    <i id="videoIconPlaceholder" class="fa-solid fa-video text-[20px] text-gray-400 dark:text-gray-400"></i>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div id="videoDirectUploadHint" class="text-sm text-gray-600 dark:text-gray-300">Kéo & thả video vào đây, hoặc</div>
                                <div class="mt-2 flex items-center gap-3">
                                    <label for="video_file" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                        Chọn video
                                    </label>
                                    <button
                                        id="btnClearNewVideo"
                                        type="button"
                                        class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700 text-sm transition"
                                    >
                                        <span id="btnClearVideoText">Xóa video</span>
                                    </button>
                                    <button
                                        id="btnRetryBackgroundUpload"
                                        type="button"
                                        class="hidden px-3 py-2 rounded-md border border-amber-300 text-amber-600 hover:bg-amber-50 dark:border-amber-600 dark:hover:bg-gray-700 text-sm transition"
                                    >
                                        Thử tải lại video
                                    </button>
                                    <input
                                        name="video_file"
                                        id="video_file"
                                        type="file"
                                        accept="video/*"
                                        class="hidden"
                                    />
                                </div>
                                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Hỗ trợ MP4, AVI, MOV, WEBM — Tối đa 10GB</div>
                                <div id="videoFileName" class="mt-2 text-sm text-gray-700 dark:text-gray-300 hidden"></div>

                            </div>
                        </div>

                        <div id="backgroundUploadPanel" class="hidden p-4 rounded-lg border border-blue-200 dark:border-blue-500/40 bg-blue-50/80 dark:bg-slate-800/60">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-blue-700 dark:text-blue-200">Tải video lên máy chủ</p>
                                    {{-- <p id="backgroundUploadFileInfo" class="text-xs text-gray-600 dark:text-gray-300 mt-1">Chưa chọn video</p> --}}
                                </div>
                                <span id="backgroundUploadStatusBadge" class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100 whitespace-nowrap">Chưa khởi tạo</span>
                            </div>
                            <div class="mt-3">
                                <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    <div id="backgroundUploadProgressBar" class="h-2 bg-blue-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-300 mt-1">
                                    <span id="backgroundUploadProgressText">0%</span>
                                    <span id="backgroundUploadChunkText">0 / 0 chunks</span>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-blue-700 dark:text-blue-200">Vui lòng đợi cho đến khi quá trình tải video hoàn tất trước khi nhấn lưu. Trong lúc đó bạn có thể điền các thông tin khác.</p>
                        </div>

                    </div>

                </div>

                {{-- Video URL Input --}}
                <div id="videoUrlSection" class="flex flex-col gap-2 hidden">
                    <input type="url" id="video_url" placeholder="https://example.com/video.mp4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 ml-3">Đường dẫn đến video bài học</p>
                </div>


            </div>

            <div class="flex flex-col gap-0.5 hidden">
                <label for="course_id" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Khóa học <span class="text-red-500">*</span>
                </label>
                <select id="course_id"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    <option value="">Chọn khóa học</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Chọn khóa học mà bài học này thuộc về</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="title" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Tiêu đề bài học sẽ hiển thị trong danh sách</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="description" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                <textarea id="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Mô tả chi tiết về bài học</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-0.5 hidden">
                    <label for="duration" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                        Thời lượng (giây)
                    </label>
                    <input type="number" id="duration" min="0" value="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Thời lượng bài học tính bằng giây</p>
                </div>

                <div class="flex flex-col gap-0.5 hidden">
                    <label for="display_order" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Thứ tự hiển thị</label>
                    <input type="number" id="display_order" min="0" value="0"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Thứ tự hiển thị bài học trong khóa học</p>
                </div>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <button type="button"
                id="lessonFormCancelButton"
                class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200">
                <span>Hủy</span>
            </button>
            <button type="submit" id="lessonFormSubmitButton" class="min-w-[100px] px-4 py-2 flex flex-row items-center justify-center gap-2 rounded-md border border-blue-600 hover:border-blue-500 bg-blue-600 hover:bg-blue-500 text-white transition-all duration-300">
                <i class="fa-solid fa-floppy-disk" id="lessonFormSubmitButton_saveIcon"></i>
                <span class="LOADING_IN_BTN hidden" id="lessonFormSubmitButton_loadingIcon"></span>
                <span id="lessonFormSubmitButton_text">Cập nhật</span>
            </button>
        </div>
    </form>

</div>


<script>
    const mode = '{{ $mode }}';
    const lessonId = @if($mode === 'EDIT' && isset($lessonId)) {{ $lessonId }} @else null @endif;

    // Dữ liệu bài học (dùng cho mode EDIT)
    let lessonData = null;

    // Quản lý thumbnail
    let thumbnailPreview = null; // URL preview của thumbnail

    // Quản lý video
    let existingVideoSource = null; // Đường dẫn video hiện tại (cho mode EDIT)
    let existingVideoIsExternal = false; // Kiểm tra video có phải URL bên ngoài không
    let videoPreviewObjectUrl = null; // Object URL cho video preview

    // Trạng thái drag & drop
    let isThumbnailDragActive = false;
    let isVideoDragActive = false;

    const MAX_BACKGROUND_VIDEO_SIZE = 10 * 1024 * 1024 * 1024; // 10GB
    const DEFAULT_BACKGROUND_CHUNK_SIZE = 5 * 1024 * 1024; // 5MB
    const MAX_PARALLEL_CHUNKS = 6; // Ngưỡng trên để tránh bão hòa kết nối
    const MIN_PARALLEL_CHUNKS = 2; // Đảm bảo vẫn có song song ngay cả trên máy yếu
    const CONCURRENT_CHUNKS = (() => {
        const hardwareThreads = Number(navigator?.hardwareConcurrency) || 4;
        const suggested = Math.max(MIN_PARALLEL_CHUNKS, Math.floor(hardwareThreads / 2));
        return Math.min(MAX_PARALLEL_CHUNKS, suggested);
    })(); // Tự động chọn số chunk upload đồng thời dựa trên thiết bị
    const MAX_RETRY_PER_CHUNK = 3; // Số lần retry tối đa cho mỗi chunk

    // Trạng thái upload nền
    let backgroundUploadState = {
        uploadId: null,              // ID phiên upload
        chunkSize: DEFAULT_BACKGROUND_CHUNK_SIZE, // Kích thước mỗi chunk
        totalChunks: 0,              // Tổng số chunks
        uploadedChunks: 0,           // Số chunks đã upload
        status: 'idle',              // Trạng thái: idle, creating_session, uploading, uploaded, completing, processing, completed, failed, cancelled
        file: null,                  // File video đang upload
        fileName: '',                // Tên file
        fileSize: 0,                 // Kích thước file
        mimeType: '',                // Loại file
        cancelRequested: false,      // Yêu cầu hủy upload
        lastError: null,             // Lỗi gần nhất
        pollingIntervalId: null,     // ID của interval polling status
        uploadingChunks: new Set(),  // Set các chunk đang upload
        failedChunks: new Map()      // Map chunk index -> số lần retry
    };

    const videoPreviewContainer = document.getElementById('videoPreviewContainer');
    const videoFilePreview = document.getElementById('videoFilePreview');
    const videoUrlPreview = document.getElementById('videoUrlPreview');
    const videoIconPlaceholder = document.getElementById('videoIconPlaceholder');

    // ========================================
    // KHỞI TẠO KHI TRANG TẢI
    // ========================================
    document.addEventListener('DOMContentLoaded', async function() {
        // Nếu đang ở chế độ EDIT, load dữ liệu bài học
        if (mode === 'EDIT' && lessonId) {
            lessonData = await loadLessonData(lessonId);
            if (lessonData) {
                await renderLesson();
            }
        }
        // Khởi tạo form thumbnail và video
        initThumbnailPreviewForm();
        initVideoUploadForm();
        initUI();
    });


    // Khởi tạo UI chung cho từng mode
    function initUI() {
        // Xử lý nút submit
        if (mode === 'CREATE') {
            document.getElementById('lessonFormSubmitButton_text').textContent = 'Tạo bài học';
        } else {
            document.getElementById('lessonFormSubmitButton_text').textContent = 'Cập nhật';
        }
    }

    // ========================================
    // XỬ LÝ LOAD VÀ RENDER DỮ LIỆU BÀI HỌC
    // ========================================

    /**
     * Load thông tin bài học từ API
     * @param {number} id - ID của bài học
     * @returns {Object|null} Dữ liệu bài học hoặc null nếu lỗi
     */
    async function loadLessonData(id) {
        try {
            const data = await apiRequest(`/lessons/${id}`);
            if (data.success) {
                return data.data;
            }
        } catch (error) {
            console.log(error);
            showNotificationModel_Global('Không thể tải thông tin bài học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    /**
     * Hiển thị thông tin bài học lên form
     */
    async function renderLesson() {
        document.getElementById('course_id').value = lessonData.course_id || '';
        document.getElementById('title').value = lessonData.title || '';
        document.getElementById('description').value = lessonData.description || '';
        document.getElementById('duration').value = lessonData.duration || 0;
        document.getElementById('display_order').value = lessonData.display_order || 0;
        document.getElementById('thumbnail_path').value = lessonData.thumbnail_path || '';

        // Set thumbnail preview
        const thumbnailEl = document.getElementById('thumbnailPreview');
        const thumbnailIconPlaceholderEl = document.getElementById('thumbnailIconPlaceholder');
        if (lessonData.thumbnail_path) {
            thumbnailEl.src = lessonData.thumbnail_path;
            thumbnailEl.classList.remove('hidden');
            thumbnailIconPlaceholderEl.classList.add('hidden');
        }

        // Set video - check if it's a URL or file path
        if (lessonData.video_path) {
            // Check if it's a URL (starts with http:// or https://)
            if (lessonData.video_path.startsWith('http://') || lessonData.video_path.startsWith('https://')) {
                existingVideoIsExternal = true;
                document.getElementById('video_type_url').checked = true;
                document.getElementById('video_url').value = lessonData.video_path;
                document.getElementById('videoUrlSection').classList.remove('hidden');
                document.getElementById('videoFileSection').classList.add('hidden');
                existingVideoSource = lessonData.video_path;
                handleVideoUrlInput(true);
            } else {
                // It's a file path, show in file section
                existingVideoIsExternal = false;
                document.getElementById('video_type_file').checked = true;
                document.getElementById('videoUrlSection').classList.add('hidden');
                document.getElementById('videoFileSection').classList.remove('hidden');
                document.getElementById('videoFileName').textContent = lessonData.video_path.split('/').pop();
                document.getElementById('videoFileName').classList.remove('hidden');
                existingVideoSource = lessonData.video_path;
                showVideoFilePreview(lessonData.video_path);
            }
        }
    }

    // ========================================
    // XỬ LÝ THUMBNAIL
    // ========================================

    /**
     * Khởi tạo form upload thumbnail với drag & drop và button handlers
     */
    function initThumbnailPreviewForm() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const input = document.getElementById('thumbnail');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        // Hiển thị placeholder mặc định khi ở chế độ CREATE
        if (!lessonData?.thumbnail_path && mode === 'CREATE') {
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailIconPlaceholder');
            thumbnailEl.classList.add('hidden');
            placeholderEl.classList.remove('hidden');
        }

        // Drag and drop handlers
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                isThumbnailDragActive = true;
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
            });

            dropZone.addEventListener('dragleave', () => {
                isThumbnailDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                isThumbnailDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');

                const file = e.dataTransfer.files && e.dataTransfer.files[0] ? e.dataTransfer.files[0] : null;
                if (file) {
                    handleChangeThumbnailFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleChangeThumbnailFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleChangeThumbnailFile(null);
            });
        }
    }

        /**
     * Xử lý khi người dùng chọn/xóa file thumbnail
     * @param {File|null} file - File ảnh hoặc null để xóa
     */
    function handleChangeThumbnailFile(file) {
        const MAX_SIZE = 10 * 1024 * 1024; // 10MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        // Giải phóng URL preview cũ nếu có
        if (thumbnailPreview) {
            try {
                URL.revokeObjectURL(thumbnailPreview);
            } catch (e) {
                // noop
            }
        }

        // Nếu file = null, reset về trạng thái ban đầu
        if (!file) {
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailIconPlaceholder');
            const clearBtn = document.getElementById('btnClearNewThumbnail');
            const input = document.getElementById('thumbnail');

            if (lessonData?.thumbnail_path) {
                thumbnailEl.src = lessonData?.thumbnail_path;
                thumbnailEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
            clearBtn.classList.add('hidden');
            if (input) input.value = '';
            thumbnailPreview = null;
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            showNotificationModel_Global('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            showNotificationModel_Global('Ảnh quá lớn. Kích thước tối đa 10MB.', 'error');
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        thumbnailPreview = previewUrl;

        const thumbnailEl = document.getElementById('thumbnailPreview');
        const placeholderEl = document.getElementById('thumbnailIconPlaceholder');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        thumbnailEl.src = previewUrl;
        thumbnailEl.classList.remove('hidden');
        placeholderEl.classList.add('hidden');
        clearBtn.classList.remove('hidden');
    }

    // ========================================
    // XỬ LÝ VIDEO FILE
    // ========================================

    /**
     * Xử lý khi người dùng chọn/xóa file video
     * @param {File|null} file - File video hoặc null để xóa
     */
    function handleChangeVideoFile(file) {
        const ALLOWED_TYPES = ['video/mp4', 'video/avi', 'video/quicktime', 'video/webm', 'video/x-msvideo'];
        const videoTypeUrlRadio = document.getElementById('video_type_url');

        // Nếu file = null, reset video và hủy upload nếu đang chạy
        if (!file) {
            const clearBtn = document.getElementById('btnClearNewVideo');
            const fileNameEl = document.getElementById('videoFileName');
            const input = document.getElementById('video_file');
            clearBtn.classList.add('hidden');
            fileNameEl.classList.add('hidden');
            if (input) input.value = '';
            if (!videoTypeUrlRadio || !videoTypeUrlRadio.checked) {
                if (existingVideoSource && !existingVideoIsExternal) {
                    showVideoFilePreview(existingVideoSource);
                } else {
                    resetVideoPreview();
                }
            }
            cancelBackgroundUpload(true);
            changeVideoUploadUI();
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            showNotificationModel_Global('Định dạng không hỗ trợ. Hãy chọn video MP4, AVI, MOV hoặc WEBM.', 'error');
            return;
        }

        if (file.size > MAX_BACKGROUND_VIDEO_SIZE) {
            showNotificationModel_Global('Video quá lớn, Tối đa 10GB.', 'error');
            return;
        }

        const clearBtn = document.getElementById('btnClearNewVideo');
        const fileNameEl = document.getElementById('videoFileName');
        clearBtn.classList.remove('hidden');
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
        showVideoFilePreview(file, true);

        startBackgroundUploadFlow(file);
    }


    // ========================================
    // XỬ LÝ VIDEO PREVIEW
    // ========================================

    /**
     * Chuyển đổi URL YouTube thành URL embed
     * @param {string} url - URL YouTube
     * @returns {string|null} URL embed hoặc null nếu không hợp lệ
     */
    function getYoutubeEmbedUrl(url) {
        try {
            const parsed = new URL(url);
            const host = parsed.hostname.replace('www.', '');
            let videoId = null;

            if (host === 'youtu.be') {
                videoId = parsed.pathname.replace('/', '');
            } else if (host === 'youtube.com' || host.endsWith('.youtube.com')) {
                videoId = parsed.searchParams.get('v');
                if (!videoId && parsed.pathname.startsWith('/embed/')) {
                    videoId = parsed.pathname.replace('/embed/', '');
                }
            }

            return videoId ? `https://www.youtube.com/embed/${videoId}` : null;
        } catch (error) {
            return null;
        }
    }

    function resetVideoPreview() {
        if (!videoPreviewContainer || !videoFilePreview || !videoUrlPreview || !videoIconPlaceholder) return;

        if (videoPreviewObjectUrl) {
            try {
                URL.revokeObjectURL(videoPreviewObjectUrl);
            } catch (e) {
                console.error('Error revoking object URL:', e);
            } finally {
                videoPreviewObjectUrl = null;
            }
        }

        videoFilePreview.pause();
        videoFilePreview.removeAttribute('src');
        videoFilePreview.load();
        videoUrlPreview.src = '';

        videoFilePreview.classList.add('hidden');
        videoUrlPreview.classList.add('hidden');
        videoIconPlaceholder.classList.remove('hidden');
    }

    // ========================================
    // QUẢN LÝ TRẠNG THÁI BACKGROUND UPLOAD
    // ========================================

    /**
     * Reset trạng thái upload nền về ban đầu
     * @param {Object} options - Tùy chọn: keepFile (giữ thông tin file), silent (không update UI)
     */
    function resetVideoUploadState(options = {}) {
        // Dừng polling status nếu đang chạy
        stopBackgroundStatusPolling();

        // Reset toàn bộ state
        backgroundUploadState = {
            uploadId: null,
            chunkSize: DEFAULT_BACKGROUND_CHUNK_SIZE,
            totalChunks: 0,
            uploadedChunks: 0,
            status: 'idle',
            file: options.keepFile ? backgroundUploadState.file : null,
            fileName: options.keepFile ? backgroundUploadState.fileName : '',
            fileSize: options.keepFile ? backgroundUploadState.fileSize : 0,
            mimeType: options.keepFile ? backgroundUploadState.mimeType : '',
            cancelRequested: false,
            lastError: null,
            pollingIntervalId: null,
            uploadingChunks: new Set(),
            failedChunks: new Map()
        };
        if (!options.silent) {
            changeVideoUploadUI();
        }
    }

    function formatBytes(bytes) {
        if (!bytes || bytes <= 0) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB', 'TB'];
        const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
        const value = bytes / Math.pow(1024, exponent);
        return `${value.toFixed(exponent === 0 ? 0 : 2)} ${units[exponent]}`;
    }

    /**
     * Cập nhật giao diện upload nền dựa trên trạng thái hiện tại
     * Bao gồm: progress bar, text, buttons, panel visibility
     */
    function changeVideoUploadUI() {
        // Lấy các elements cần thiết
        const backgroundPanel = document.getElementById('backgroundUploadPanel');
        const progressBar = document.getElementById('backgroundUploadProgressBar');
        const progressText = document.getElementById('backgroundUploadProgressText');
        const chunkText = document.getElementById('backgroundUploadChunkText');
        const clearBtn = document.getElementById('btnClearNewVideo');
        const clearBtnText = document.getElementById('btnClearVideoText');
        const retryBtn = document.getElementById('btnRetryBackgroundUpload');
        const statusBadge = document.getElementById('backgroundUploadStatusBadge');

        // Kiểm tra elements tồn tại
        if (!progressBar || !progressText || !chunkText) {
            return;
        }

        const { fileName, fileSize, status, totalChunks, uploadedChunks, lastError } = backgroundUploadState;

        // Ẩn/hiện panel upload dựa trên trạng thái
        if (backgroundPanel) {
            if (status === 'idle' || !fileName) {
                backgroundPanel.classList.add('hidden');
            } else {
                backgroundPanel.classList.remove('hidden');
            }
        }

        // fileInfoEl.textContent = fileName ? `${fileName} (${formatBytes(fileSize)})` : 'Chưa chọn video';

        const percent = totalChunks > 0 ? Math.round((uploadedChunks / totalChunks) * 100) : 0;
        progressBar.style.width = `${percent}%`;
        progressText.textContent = `${percent}%`;
        chunkText.textContent = `${uploadedChunks} / ${totalChunks} chunks`;

        const badgeStyles = {
            idle: { text: 'Chưa khởi tạo', className: 'px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100' },
            creating_session: { text: 'Đang tạo phiên', className: 'px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200' },
            uploading: { text: 'Đang upload', className: 'px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200' },
            uploaded: { text: 'Đã upload', className: 'px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' },
            completing: { text: 'Đang gửi xử lý', className: 'px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200' },
            processing: { text: 'Đang ghép video', className: 'px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200' },
            completed: { text: 'Hoàn tất', className: 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200' },
            failed: { text: 'Lỗi upload', className: 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200' },
            cancelled: { text: 'Đã hủy', className: 'px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-200' }
        };

        const badge = badgeStyles[status] || badgeStyles.idle;
        statusBadge.textContent = badge.text;
        statusBadge.className = badge.className;

        // Cập nhật text và hiển thị button Clear/Cancel
        if (clearBtn && clearBtnText) {
            if (['creating_session', 'uploading'].includes(status)) {
                clearBtnText.textContent = 'Hủy upload';
                clearBtn.classList.remove('hidden');
            } else if (fileName) {
                clearBtnText.textContent = 'Xóa video';
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }
        if (retryBtn) {
            retryBtn.classList.toggle('hidden', status !== 'failed' || !backgroundUploadState.file);
        }

        if (lastError && status === 'failed') {
            // progressText.textContent = lastError;
            progressText.textContent = 'Đã xảy ra lỗi vui lòng thử lại!';
        }
    }


    /**
     * Bắt đầu quy trình upload video nền
     * Các bước: tạo session -> upload chunks -> hoàn tất
     * @param {File} file - File video cần upload
     */
    async function startBackgroundUploadFlow(file) {
        if (!file) return;

        // Hủy upload cũ nếu có
        if (backgroundUploadState.uploadId) {
            await cancelBackgroundUpload(true);
        }

        // Khởi tạo state mới
        resetVideoUploadState({ silent: true });
        backgroundUploadState.file = file;
        backgroundUploadState.fileName = file.name;
        backgroundUploadState.fileSize = file.size;
        backgroundUploadState.mimeType = file.type || 'application/octet-stream';
        backgroundUploadState.status = 'creating_session';
        changeVideoUploadUI();

        try {
            // Bước 1: Tạo session upload
            const payload = {
                lesson_id: lessonId || null,
                file_name: file.name,
                file_size: file.size,
                mime_type: backgroundUploadState.mimeType,
                chunk_size: DEFAULT_BACKGROUND_CHUNK_SIZE
            };
            const sessionResponse = await apiRequest('/lesson-video-uploads/sessions', {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            const sessionData = sessionResponse.data;
            backgroundUploadState.uploadId = sessionData.upload_id;
            backgroundUploadState.chunkSize = sessionData.chunk_size;
            backgroundUploadState.totalChunks = sessionData.total_chunks;
            backgroundUploadState.uploadedChunks = 0;
            backgroundUploadState.status = 'uploading';
            changeVideoUploadUI();

            await handleVideoUploadByChunk();

            backgroundUploadState.status = 'uploaded';
            changeVideoUploadUI();
        } catch (error) {
            console.error('Background upload error:', error);
            backgroundUploadState.lastError = error.message;
            backgroundUploadState.status = backgroundUploadState.cancelRequested ? 'cancelled' : 'failed';
            changeVideoUploadUI();
            if (!backgroundUploadState.cancelRequested) {
                showNotificationModel_Global(error.message || 'Upload video thất bại.', 'error');
            }
        }
    }

    /**
     * Upload tất cả các chunks của video song song
     * Upload đồng thời CONCURRENT_CHUNKS chunks, với retry mechanism
     */
    async function handleVideoUploadByChunk() {
        if (!backgroundUploadState?.file || !backgroundUploadState?.chunkSize) return;

        // Tạo danh sách các chunk cần upload (bỏ qua chunk đã upload)
        const chunksToUpload = [];
        for (let chunkIndex = 1; chunkIndex <= backgroundUploadState?.totalChunks; chunkIndex++) {
            if (chunkIndex > backgroundUploadState?.uploadedChunks) {
                chunksToUpload.push(chunkIndex);
            }
        }

        // Upload song song với concurrency limit
        const uploadPromises = [];
        let currentIndex = 0;

        const uploadNextChunk = async () => {
            while (currentIndex < chunksToUpload.length) {
                // Kiểm tra yêu cầu hủy
                if (backgroundUploadState.cancelRequested) {
                    throw new Error('Upload đã bị hủy.');
                }

                const chunkIndex = chunksToUpload[currentIndex];
                currentIndex++;

                // Đánh dấu chunk đang upload
                backgroundUploadState.uploadingChunks.add(chunkIndex);

                try {
                    // Cắt chunk từ file
                    const start = (chunkIndex - 1) * backgroundUploadState?.chunkSize;
                    const end = Math.min(backgroundUploadState.file.size, start + backgroundUploadState?.chunkSize);
                    const chunkBlob = backgroundUploadState.file.slice(start, end);

                    // Upload chunk với retry
                    await uploadSingleChunkWithRetry(chunkIndex, chunkBlob);

                    // Xóa khỏi danh sách đang upload
                    backgroundUploadState.uploadingChunks.delete(chunkIndex);

                    // Cập nhật số chunk đã upload
                    backgroundUploadState.uploadedChunks++;
                    changeVideoUploadUI();
                } catch (error) {
                    // Xóa khỏi danh sách đang upload
                    backgroundUploadState.uploadingChunks.delete(chunkIndex);

                    // Nếu đã retry quá giới hạn, throw error
                    const retryCount = backgroundUploadState.failedChunks.get(chunkIndex) || 0;
                    if (retryCount >= MAX_RETRY_PER_CHUNK) {
                        throw new Error(`Chunk ${chunkIndex} thất bại sau ${MAX_RETRY_PER_CHUNK} lần thử.`);
                    }
                    throw error;
                }
            }
        };

        // Tạo pool các worker upload song song
        for (let i = 0; i < CONCURRENT_CHUNKS; i++) {
            uploadPromises.push(uploadNextChunk());
        }

        // Chờ tất cả chunks upload xong
        await Promise.all(uploadPromises);
    }

    /**
     * Upload một chunk với retry mechanism
     * @param {number} chunkIndex - Số thứ tự chunk (bắt đầu từ 1)
     * @param {Blob} chunkBlob - Dữ liệu chunk
     */
    async function uploadSingleChunkWithRetry(chunkIndex, chunkBlob) {
        let lastError = null;
        const retryCount = backgroundUploadState.failedChunks.get(chunkIndex) || 0;

        for (let attempt = 0; attempt <= MAX_RETRY_PER_CHUNK - retryCount; attempt++) {
            try {
                await uploadSingleChunkRequest(chunkIndex, chunkBlob);
                // Xóa khỏi danh sách failed nếu thành công
                backgroundUploadState.failedChunks.delete(chunkIndex);
                return;
            } catch (error) {
                lastError = error;
                console.warn(`Chunk ${chunkIndex} thất bại (lần thử ${attempt + 1}):`, error);

                // Cập nhật số lần retry
                backgroundUploadState.failedChunks.set(chunkIndex, retryCount + attempt + 1);

                // Đợi một chút trước khi retry (exponential backoff)
                if (attempt < MAX_RETRY_PER_CHUNK - retryCount) {
                    await new Promise(resolve => setTimeout(resolve, Math.min(1000 * Math.pow(2, attempt), 5000)));
                }
            }
        }

        throw lastError || new Error(`Chunk ${chunkIndex} upload thất bại.`);
    }

    /**
     * Upload một chunk lên server
     * @param {number} chunkIndex - Số thứ tự chunk (bắt đầu từ 1)
     * @param {Blob} chunkBlob - Dữ liệu chunk
     */
    async function uploadSingleChunkRequest(chunkIndex, chunkBlob) {
        const formData = new FormData();
        formData.append('chunk_index', chunkIndex);
        formData.append('chunk', chunkBlob);

        const response = await fetch(`/api/lesson-video-uploads/${backgroundUploadState.uploadId}/chunks`, {
            method: 'POST',
            body: formData,
            credentials: 'include'
        });
        const data = await response.json();
        if (!response.ok) {
            const message = data?.message || 'Upload chunk thất bại.';
            throw new Error(message);
        }
        return data;
    }

    /**
     * Hủy upload nền đang chạy
     * @param {boolean} silent - Không hiển thị thông báo nếu true
     */
    async function cancelBackgroundUpload(silent = false) {
        if (!backgroundUploadState.uploadId) {
            resetVideoUploadState({ silent: true });
            if (!silent) changeVideoUploadUI();
            return true;
        }

        let isSuccess = false;

        try {
            // Đánh dấu yêu cầu hủy TRƯỚC KHI gọi API
            backgroundUploadState.cancelRequested = true;

            // Đợi một chút để các chunk đang upload có thời gian kiểm tra flag
            await new Promise(resolve => setTimeout(resolve, 100));

            // Gọi API hủy upload
            await apiRequest(`/lesson-video-uploads/${backgroundUploadState.uploadId}`, {
                method: 'DELETE'
            });

            // Đợi thêm chút nữa để đảm bảo các chunk đã dừng
            await new Promise(resolve => setTimeout(resolve, 200));
            isSuccess = true;
        } catch (error) {
            console.error('Cancel upload error:', error);
            isSuccess = false;
        } finally {
            resetVideoUploadState({ silent: false }); 
            return isSuccess;
        }
    }

    /**
     * Bắt đầu xử lý video sau khi upload chunks xong
     * Server sẽ ghép các chunks thành video hoàn chỉnh
     * @param {number} targetLessonId - ID bài học để gắn video
     */
    async function startBackgroundProcessing(targetLessonId) {
        if (!backgroundUploadState.uploadId || !targetLessonId) {
            return;
        }
        backgroundUploadState.status = 'completing';
        changeVideoUploadUI();
        try {
            const response = await apiRequest(`/lesson-video-uploads/${backgroundUploadState.uploadId}/complete`, {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: targetLessonId
                })
            });
            backgroundUploadState.status = response?.data?.status || 'processing';
            backgroundUploadState.lastError = null;
            changeVideoUploadUI();
            showNotificationModel_Global(response.message || 'Video đang được xử lý, bạn có thể tiếp tục làm việc.', 'success');
            startBackgroundStatusPolling();
        } catch (error) {
            console.error('Complete upload error:', error);
            backgroundUploadState.lastError = error.message;
            backgroundUploadState.status = 'failed';
            changeVideoUploadUI();
            showNotificationModel_Global(error.message || 'Không thể gửi yêu cầu xử lý video.', 'error');
            throw error;
        }
    }

    /**
     * Bắt đầu polling để kiểm tra trạng thái xử lý video
     * Polling mỗi 4 giây cho đến khi hoàn tất hoặc lỗi
     */
    function startBackgroundStatusPolling() {
        stopBackgroundStatusPolling();
        backgroundUploadState.pollingIntervalId = setInterval(async () => {
            await fetchBackgroundUploadStatus();
            // Dừng polling khi hoàn tất hoặc lỗi
            if (['completed', 'failed'].includes(backgroundUploadState.status)) {
                stopBackgroundStatusPolling();
                // Thông báo kết quả
                if (backgroundUploadState.status === 'completed') {
                    showNotificationModel_Global('Video đã được xử lý và cập nhật.', 'success');
                } else if (backgroundUploadState.lastError) {
                    showNotificationModel_Global(backgroundUploadState.lastError, 'error');
                }
            }
        }, 4000);
    }

    /**
     * Dừng polling status
     */
    function stopBackgroundStatusPolling() {
        if (backgroundUploadState.pollingIntervalId) {
            clearInterval(backgroundUploadState.pollingIntervalId);
            backgroundUploadState.pollingIntervalId = null;
        }
    }

    async function fetchBackgroundUploadStatus() {
        if (!backgroundUploadState.uploadId) {
            return;
        }

        try {
            const response = await apiRequest(`/lesson-video-uploads/${backgroundUploadState.uploadId}`, {
                method: 'GET'
            });
            const data = response.data;
            backgroundUploadState.status = data.status || backgroundUploadState.status;
            backgroundUploadState.uploadedChunks = data.uploaded_chunks ?? backgroundUploadState.uploadedChunks;
            backgroundUploadState.totalChunks = data.total_chunks ?? backgroundUploadState.totalChunks;
            backgroundUploadState.lastError = data.error_message || backgroundUploadState.lastError;
            changeVideoUploadUI();
        } catch (error) {
            console.error('Fetch status error:', error);
        }
    }

    function showVideoFilePreview(source, isBlobSource = false) {
        if (!videoPreviewContainer || !videoFilePreview || !videoUrlPreview || !videoIconPlaceholder) return;

        if (videoPreviewObjectUrl) {
            try {
                URL.revokeObjectURL(videoPreviewObjectUrl);
            } catch (e) {
                // noop
            } finally {
                videoPreviewObjectUrl = null;
            }
        }

        let finalSrc = source;
        if (isBlobSource && source instanceof File) {
            finalSrc = URL.createObjectURL(source);
            videoPreviewObjectUrl = finalSrc;
        }

        // videoPreviewContainer.classList.remove('hidden');
        videoIconPlaceholder.classList.add('hidden');
        videoUrlPreview.classList.add('hidden');
        videoFilePreview.classList.remove('hidden');
        videoFilePreview.src = finalSrc;
        videoFilePreview.load();
    }

    function showVideoUrlPreview(url) {
        if (!videoPreviewContainer || !videoFilePreview || !videoUrlPreview || !videoIconPlaceholder) return;

        if (videoPreviewObjectUrl) {
            try {
                URL.revokeObjectURL(videoPreviewObjectUrl);
            } catch (e) {
                // noop
            } finally {
                videoPreviewObjectUrl = null;
            }
        }

        // videoPreviewContainer.classList.remove('hidden');
        videoIconPlaceholder.classList.add('hidden');
        videoFilePreview.classList.add('hidden');
        videoUrlPreview.classList.remove('hidden');
        videoUrlPreview.src = url;
    }

    function handleVideoUrlInput(force = false) {
        const videoTypeUrlRadio = document.getElementById('video_type_url');
        if (!videoTypeUrlRadio || (!videoTypeUrlRadio.checked && !force)) {
            return;
        }

        const videoUrlInput = document.getElementById('video_url');
        if (!videoUrlInput) return;

        const url = videoUrlInput.value.trim();
        if (!url) {
            resetVideoPreview();
            return;
        }

        const youtubeEmbedUrl = getYoutubeEmbedUrl(url);
        if (youtubeEmbedUrl) {
            showVideoUrlPreview(youtubeEmbedUrl);
            return;
        }

        const isDirectVideoUrl = /\.(mp4|mov|webm|ogg|m3u8)(\?.*)?$/i.test(url);

        if (isDirectVideoUrl || (!url.startsWith('http://') && !url.startsWith('https://'))) {
            showVideoFilePreview(url);
            return;
        }

        showVideoUrlPreview(url);
    }

    function initVideoUploadForm() {
        const dropZone = document.getElementById('videoDropZone');
        const input = document.getElementById('video_file');
        const clearBtn = document.getElementById('btnClearNewVideo');
        const videoTypeUrl = document.getElementById('video_type_url');
        const videoTypeFile = document.getElementById('video_type_file');
        const videoUrlSection = document.getElementById('videoUrlSection');
        const videoFileSection = document.getElementById('videoFileSection');
        const videoUrlInput = document.getElementById('video_url');
        const backgroundRetryBtn = document.getElementById('btnRetryBackgroundUpload');
        const lessonFormCancelButton = document.getElementById('lessonFormCancelButton');

        // Cancel button handler
        if (lessonFormCancelButton) {
            lessonFormCancelButton.addEventListener('click', async () => {

                openSingleDeleteModalGeneric_Global({
                    objectName: lessonData != null ? OBJECTNAMEMODAL.LESSON : OBJECTNAMEMODAL.VIDEO,
                    title: `Hủy ${mode === 'CREATE' ? 'tạo mới' : 'chỉnh sửa'} bài học`,
                    message: `Việc ${mode === 'CREATE' ? 'tạo mới' : 'chỉnh sửa'} bài học sẽ bị hủy, video bạn chọn sẽ bị hủy. Bạn có chắc chắn?`,
                    nameValue: lessonData?.title || '-',
                    descValue: lessonData?.description || '-',
                    confirmText: `Hủy ${mode === 'CREATE' ? 'tạo mới' : 'chỉnh sửa'} bài học`,
                    cancelText: 'Đóng',
                    actionFuncCallback: async () => {
                        try {
                            let isSuccess = true;
                            // Hủy upload nền nếu đang chạy
                            if (['creating_session', 'uploading', 'uploaded', 'completing'].includes(backgroundUploadState.status)) {
                                isSuccess = await cancelBackgroundUpload(true);
                            }
                            // handleBackToPrevPage();
                            return isSuccess;
                        } catch (error) {
                            console.error('Error canceling upload:', error);
                            return false; // Trả về false nếu có lỗi
                        }
                    },
                    successFuncCallback: () => {
                        handleBackToPrevPage();
                        // showNotificationModel_Global(`Hủy ${mode === 'CREATE' ? 'tạo mới' : 'chỉnh sửa'} bài học thành công`, 'success', handleBackToPrevPage);
                    }
                }) 
            });
        }

        // Radio button handlers
        if (videoTypeUrl) {
            videoTypeUrl.addEventListener('change', () => {
                if (videoTypeUrl.checked) {
                    videoUrlSection.classList.remove('hidden');
                    videoFileSection.classList.add('hidden');
                    videoUrlInput.required = true;
                    if (input) input.required = false;
                    handleVideoUrlInput(true);
                }
            });
        }

        if (videoTypeFile) {
            videoTypeFile.addEventListener('change', () => {
                if (videoTypeFile.checked) {
                    videoUrlSection.classList.add('hidden');
                    videoFileSection.classList.remove('hidden');
                    videoUrlInput.required = false;
                    if (input) input.required = true;
                    if (videoFile) {
                        showVideoFilePreview(videoFile, true);
                    } else if (existingVideoSource && !existingVideoIsExternal) {
                        showVideoFilePreview(existingVideoSource);
                    } else {
                        resetVideoPreview();
                    }
                }
            });
        }

        if (backgroundRetryBtn) {
            backgroundRetryBtn.addEventListener('click', () => {
                if (backgroundUploadState.file) {
                    startBackgroundUploadFlow(backgroundUploadState.file);
                }
            });
        }

        // Drag and drop handlers
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                isVideoDragActive = true;
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
            });

            dropZone.addEventListener('dragleave', () => {
                isVideoDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                isVideoDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');

                const file = e.dataTransfer.files && e.dataTransfer.files[0] ? e.dataTransfer.files[0] : null;
                if (file) {
                    handleChangeVideoFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleChangeVideoFile(file);
                }
            });
        }

        // Clear button handler - xử lý cả xóa video và hủy upload
        if (clearBtn) {
            clearBtn.addEventListener('click', async () => {
                // Nếu đang upload, hủy upload
                if (['creating_session', 'uploading'].includes(backgroundUploadState.status)) {

                    openSingleDeleteModalGeneric_Global({
                        objectName: OBJECTNAMEMODAL.VIDEO,
                        title: 'Hủy upload video',
                        message: 'Video đang được upload. Bạn có chắc muốn hủy upload và xóa video đã chọn không?',
                        nameValue: backgroundUploadState.fileName || 'Video hiện tại',
                        descValue: formatBytes(backgroundUploadState.fileSize || 0) + ' - ' + backgroundUploadState.mimeType,
                        confirmText: 'Hủy upload',
                        cancelText: 'Đóng',
                        actionFuncCallback: async () => {
                            try {
                                await cancelBackgroundUpload();
                                // Xóa video đã chọn sau khi hủy upload
                                handleChangeVideoFile(null);
                                return true; // Trả về true để báo thành công
                            } catch (error) {
                                console.error('Error canceling upload:', error);
                                return false; // Trả về false nếu có lỗi
                            }
                        }
                    })
                } else {
                    // Xóa video đã chọn
                    handleChangeVideoFile(null);
                }
            });
        }

        if (videoUrlInput) {
            videoUrlInput.addEventListener('input', () => handleVideoUrlInput());
            videoUrlInput.addEventListener('change', () => handleVideoUrlInput());
            videoUrlInput.addEventListener('blur', () => handleVideoUrlInput());
        }

        changeVideoUploadUI();
    }

    // ========================================
    // LƯU BÀI HỌC
    // ========================================

    /**
     * Xử lý submit form lưu bài học
     * Kiểm tra validation, upload thumbnail/video, gọi API lưu
     */
    async function saveLesson(event) {
        event.preventDefault();

        // Lấy dữ liệu từ form
        const lessonIdValue = document.getElementById('lessonId').value;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const duration = document.getElementById('duration').value;
        const displayOrder = document.getElementById('display_order').value || 0;
        const pathThumbnail = document.getElementById('thumbnail_path').value || null;
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailFile = thumbnailInput && thumbnailInput.files && thumbnailInput.files[0] ? thumbnailInput.files[0] : null;
        const isChoiceVideoUrl = document.getElementById('video_type_url').checked;
        const videoUrl = isChoiceVideoUrl ? document.getElementById('video_url').value.trim() : '';
        const videoInput = document.getElementById('video_file');
        const selectedVideoFile = videoInput && videoInput.files && videoInput.files[0] ? videoInput.files[0] : null;
        const isBackgroundMode = !isChoiceVideoUrl; // Luôn dùng background upload cho video file

        // Validation: Tiêu đề bắt buộc
        if (!title) {
            showNotificationModel_Global('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

        if (isBackgroundMode) {
            // if (backgroundUploadState.file != null && !backgroundUploadState.uploadId) {
            //     showNotificationModel_Global('Vui lòng chọn video và upload nền hoàn tất trước khi lưu.', 'warning');
            //     return;
            // }
            if (backgroundUploadState.file != null && !['uploaded', 'processing', 'completed'].includes(backgroundUploadState.status)) {
                showNotificationModel_Global('Video vẫn đang upload. Vui lòng chờ hoàn tất để lưu.', 'warning');
                return;
            }
        }

        handleChangeStateButtonSubmitting(true);


        const isEdit = Boolean(lessonIdValue);
        const url = isEdit ? `/lessons/${lessonIdValue}` : '/lessons';

        try {
            const fd = new FormData();
            // Laravel/Symfony không parse multipart cho PUT/PATCH -> dùng POST + _method
            if (isEdit) {
                fd.append('_method', 'PUT');
            }
            fd.append('title', title || '');
            fd.append('description', description || '');
            fd.append('duration', parseInt(duration) || 0);
            fd.append('display_order', parseInt(displayOrder) || 0);

            fd.append('thumbnail_path', lessonData?.thumbnail_path || '');
            if (thumbnailFile) {
                fd.append('thumbnail_file', thumbnailFile);
            }

            // Nếu chọn URL video
            if (isChoiceVideoUrl) {
                fd.append('video_path', videoUrl);
            } else if (isBackgroundMode) {
                const placeholderVideoPath = existingVideoSource || `background-upload://${backgroundUploadState.uploadId}`;
                fd.append('video_path', placeholderVideoPath);
            } else {
                fd.append('video_path', lessonData?.video_path);
                if (selectedVideoFile) {
                    fd.append('video_file', selectedVideoFile);
                }
            }

            const response = await fetch(`/api${url}`, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

            if (data.success) {
                const savedLessonId = isEdit
                    ? parseInt(lessonIdValue, 10)
                    : (data?.data?.id || data?.data?.data?.id || null);

                if (!isEdit && savedLessonId) {
                    document.getElementById('lessonId').value = savedLessonId;
                }

                if (isBackgroundMode && backgroundUploadState.uploadId && savedLessonId) {
                    try {
                        await startBackgroundProcessing(savedLessonId);
                        existingVideoSource = `background-upload://${backgroundUploadState.uploadId}`;
                    } catch (error) {
                        console.error(error);
                    }
                }

                handleChangeStateButtonSubmitting(false);
                showNotificationModel_Global(data.message || 'Lưu thành công', 'success', handleBackToPrevPage);
            }
        } catch (error) {
            handleChangeStateButtonSubmitting(false);
            showNotificationModel_Global(error.message, 'error', handleBackToPrevPage);
        }
    }

    function handleChangeStateButtonSubmitting(isSubmitting) {
        const submitButton = document.getElementById('lessonFormSubmitButton');
        const cancelButton = document.getElementById('lessonFormCancelButton');
        const saveIcon = document.getElementById('lessonFormSubmitButton_saveIcon');
        const loadingIcon = document.getElementById('lessonFormSubmitButton_loadingIcon');
        const submitButtonText = document.getElementById('lessonFormSubmitButton_text');
        if (isSubmitting == true) {
            cancelButton.disabled = true;
            submitButton.disabled = true;
            submitButton.classList.add('cursor-not-allowed', 'opacity-50');
            submitButtonText.textContent = mode === 'CREATE' ? 'Đang tạo...' : 'Đang cập nhật...';
            saveIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
        }
        if (isSubmitting == false) {
            cancelButton.disabled = false;
            submitButton.disabled = false;
            saveIcon.classList.remove('hidden');
            loadingIcon.classList.add('hidden');
            initUI();
        }
    }

    // ========================================
    // ĐIỀU HƯỚNG VÀ EVENT HANDLERS
    // ========================================

    /**
     * Quay lại trang danh sách bài học
     */
    function handleBackToPrevPage() {
        window.location.href = '/admin/lessons';
    }

    /**
     * Cảnh báo người dùng khi thoát trang trong khi đang upload
     */
    window.addEventListener('beforeunload', (event) => {
        if (['creating_session', 'uploading'].includes(backgroundUploadState.status)) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
</script>
@endsection

