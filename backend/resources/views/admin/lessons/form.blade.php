@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm bài học' : 'Chỉnh sửa bài học')

@section('description', $mode === 'CREATE' ? 'Thêm bài học mới vào hệ thống' : 'Chỉnh sửa thông tin bài học')

@section('content')
<div class="w-full max-w-[1600px] mx-auto flex flex-col items-stretch justify-start gap-4 3xl:gap-6">

    {{-- Header Bar --}}
    <div class="p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

        <div class="flex items-center justify-start gap-3">
            <button onclick="handleCancelUpdateLesson()"
                type="button"
                class="group px-2.5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 cursor-pointer text-[18px]">
                <svg class="h-6" viewBox="0 0 320 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                </svg>
            </button>
            <div class="flex flex-col gap-0 min-w-0 flex-1">
                <h2
                    class="font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                    @yield('title', 'Admin Panel')
                </h2>
                @hasSection('description')
                    <p class="text-xs 3xl:text-sm text-gray-600 dark:text-gray-400 truncate">
                        @yield('description')
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <form id="lessonForm" onsubmit="saveLesson(event)" class="w-full bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="lessonId" value="{{ $mode === 'EDIT' ? ($lessonId ?? '') : '' }}">
        <input type="hidden" id="thumbnail_path" value="">
        <div class="flex flex-col gap-6">

            {{-- Thumbnail Upload Section --}}
            <div class="flex flex-col gap-0.5">
                <span class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh thumbnail</span>
                <div
                    id="thumbnailDropZone"
                    class="h-[142px] flex items-stretch gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                >
                    <div class="h-full aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <img id="thumbnailPreview" alt="thumbnail preview" class="h-full w-full object-cover hidden">
                        <span id="thumbnailIconPlaceholder" class="text-gray-400">
                            <svg class="w-6" viewBox="0 0 448 512" fill="currentColor">
                                <path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm64 80a48 48 0 1 1 0 96 48 48 0 1 1 0-96zM272 224c8.4 0 16.1 4.4 20.5 11.5l88 144c4.5 7.4 4.7 16.7 .5 24.3S368.7 416 360 416L88 416c-8.9 0-17.2-5-21.3-12.9s-3.5-17.5 1.6-24.8l56-80c4.5-6.4 11.8-10.2 19.7-10.2s15.2 3.8 19.7 10.2l26.4 37.8 61.4-100.5c4.4-7.1 12.1-11.5 20.5-11.5z"/>
                            </svg>
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class=" text-gray-600 dark:text-gray-300">Kéo & thả ảnh hoặc chọn ảnh</div>
                        <div class="mt-2 flex items-center gap-3">
                            <label for="thumbnail" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ">
                                Chọn ảnh
                            </label>
                            <button
                                id="btnClearNewThumbnail"
                                type="button"
                                class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700 "
                            >
                                Xóa ảnh mới
                            </button>
                            <input
                                id="thumbnail"
                                type="file"
                                accept="image/png,image/jpeg,image/webp"
                                class="hidden"
                            />
                        </div>
                        <div class="mt-2  text-gray-500 dark:text-gray-400">Hỗ trợ PNG, JPG, WEBP — Tối đa 10MB</div>
                    </div>
                </div>
            </div>

            {{-- Video Upload Section --}}
            <div class="flex flex-col gap-0.5">
                <span class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Video <span class="text-red-500">*</span>
                </span>

                {{-- Video File Upload --}}
                <div id="videoFileSection">
                    <div
                        id="videoDropZone"
                        class="flex flex-col items-stretch justify-start gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >

                        <div class="h-[110px] flex flex-row items-start gap-4">
                            <div id="videoPreviewContainer" class="h-full flex flex-col gap-2">
                                <div class="relative h-full aspect-video bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center">
                                    <video id="videoFilePreview" controls class="hidden w-full h-full object-cover bg-black"></video>
                                    <iframe id="videoUrlPreview" class="hidden w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                    <span id="videoIconPlaceholder" class="text-gray-400 dark:text-gray-400">
                                        <svg class="w-6" viewBox="0 0 576 512" fill="currentColor">
                                            <path d="M96 64c-35.3 0-64 28.7-64 64l0 256c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-256c0-35.3-28.7-64-64-64L96 64zM464 336l73.5 58.8c4.2 3.4 9.4 5.2 14.8 5.2 13.1 0 23.7-10.6 23.7-23.7l0-240.6c0-13.1-10.6-23.7-23.7-23.7-5.4 0-10.6 1.8-14.8 5.2L464 176 464 336z"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div id="videoDirectUploadHint" class=" text-gray-600 dark:text-gray-300">Kéo & thả video hoặc chọn video</div>
                                <div class="mt-2 flex items-center gap-3">
                                    <label for="video_file" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ">
                                        Chọn video
                                    </label>
                                    <button
                                        id="btnClearNewVideo"
                                        type="button"
                                        onclick="handleClearVideoFile()"
                                        class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700  transition"
                                    >
                                        <span id="btnClearVideoText">Xóa video</span>
                                    </button>
                                    <button
                                        id="btnRetryBackgroundUpload"
                                        type="button"
                                        class="hidden px-3 py-2 rounded-md border border-amber-300 text-amber-600 hover:bg-amber-50 dark:border-amber-600 dark:hover:bg-gray-700  transition"
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
                                <div class="mt-2  text-gray-500 dark:text-gray-400">Hỗ trợ MP4, MOV có codec MPEG-4 HE AAC, H.264, Timed Metadata, HEVC — Tối đa 10GB</div>

                            </div>
                        </div>

                        <div id="backgroundUploadPanel" class="hidden p-4 rounded-lg border border-blue-200 dark:border-blue-500/40 bg-blue-50/80 dark:bg-slate-800/60">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class=" font-semibold text-blue-700 dark:text-blue-200">Tải video lên máy chủ</p>
                                    <div id="videoFileName" class="mt-2  text-gray-700 dark:text-gray-300 hidden"></div>
                                </div>
                                <span id="backgroundUploadStatusBadge" class="px-2 py-1  rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100 whitespace-nowrap">Chưa khởi tạo</span>
                            </div>
                            <div class="mt-3">
                                <div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                    <div id="backgroundUploadProgressBar" class="h-2 bg-blue-500 rounded-full transition-all duration-300" style="width: 0%;"></div>
                                </div>
                                <div class="flex items-center justify-between  text-gray-600 dark:text-gray-300 mt-1">
                                    <span id="backgroundUploadProgressText">0%</span>
                                    <span id="backgroundUploadChunkText">0 / 0 chunks</span>
                                </div>
                            </div>
                            <p class="mt-3  text-blue-700 dark:text-blue-200">Vui lòng đợi cho đến khi quá trình tải video hoàn tất trước khi nhấn lưu. Trong lúc đó bạn có thể điền các thông tin khác.</p>
                        </div>

                    </div>

                </div>

            </div>

            <div class="flex flex-col gap-0.5">
                <label name="title_LABEL" for="title" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" required
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                <span id="title_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="description" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả <span id="description_COUNT_WORDS" class="text-gray-500 dark:text-gray-400 font-normal"></span></label>
                <textarea id="description" rows="4" minlength="0" maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"></textarea>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-6">
            <button
                id="lessonFormCancelButton"
                type="button"
                onclick="handleCancelUpdateLesson()"
                class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                <span>Hủy</span>
            </button>
            <button type="submit" id="lessonFormSubmitButton" class="min-w-[100px] px-4 py-2 flex flex-row items-center justify-center gap-2 rounded-md border border-blue-600 hover:border-blue-500 bg-blue-600 hover:bg-blue-500 text-white transition-all duration-300">
                {{-- <span id="lessonFormSubmitButton_saveIcon">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-242.7c0-17-6.7-33.3-18.7-45.3L352 50.7C340 38.7 323.7 32 306.7 32L64 32zm32 96c0-17.7 14.3-32 32-32l160 0c17.7 0 32 14.3 32 32l0 64c0 17.7-14.3 32-32 32l-160 0c-17.7 0-32-14.3-32-32l0-64zM224 288a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/>
                    </svg>
                </span> --}}
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

        // Cập nhật số ký tự mô tả
        const descriptionInput = document.getElementById('description');
        const descCountWord = document.getElementById('description_COUNT_WORDS');

        const updateDescriptionCount = () => {
            if (descCountWord) {
                descCountWord.textContent = `(${descriptionInput.value.length}/255)`;
            }
        };

        // Cập nhật khi load và khi thay đổi
        if (descriptionInput && descCountWord) {
            updateDescriptionCount();
            descriptionInput.addEventListener('input', updateDescriptionCount);
            descriptionInput.addEventListener('change', updateDescriptionCount);
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
            NotificationModal.show('Không thể tải thông tin bài học: ' + error.message, 'error', handleGotoBackPage_Global);
        }
    }

    /**
     * Hiển thị thông tin bài học lên form
     */
    async function renderLesson() {
        // document.getElementById('course_id').value = lessonData.course_id || '';
        document.getElementById('title').value = lessonData.title || '';
        document.getElementById('description').value = lessonData.description || '';
        document.getElementById('thumbnail_path').value = lessonData.thumbnail_path || '';

        // Set thumbnail preview
        const thumbnailEl = document.getElementById('thumbnailPreview');
        const thumbnailIconPlaceholderEl = document.getElementById('thumbnailIconPlaceholder');
        if (lessonData.thumbnail_path) {
            thumbnailEl.src = lessonData.thumbnail_path;
            thumbnailEl.classList.remove('hidden');
            thumbnailIconPlaceholderEl.classList.add('hidden');
        }

        if (lessonData.video_path) {
            document.getElementById('videoFileSection').classList.remove('hidden');
            document.getElementById('videoFileName').textContent = lessonData.video_path.split('/').pop();
            document.getElementById('videoFileName').classList.remove('hidden');
            existingVideoSource = lessonData.video_path;
            showVideoPreview(lessonData.video_path);
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
            NotificationModal.show('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            NotificationModal.show('Ảnh quá lớn. Kích thước tối đa 10MB.', 'error');
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
    async function handleChangeVideoFile(file) {
        const ALLOWED_TYPES = ['video/mp4', 'video/quicktime'];

        // Nếu file = null, reset video và hủy upload nếu đang chạy
        if (!file) {
            const clearBtn = document.getElementById('btnClearNewVideo');
            const fileNameEl = document.getElementById('videoFileName');
            const input = document.getElementById('video_file');
            clearBtn.classList.add('hidden');
            fileNameEl.classList.add('hidden');
            if (input) input.value = '';
            if (existingVideoSource ) {
                showVideoPreview(existingVideoSource);
            }
            cancelBackgroundUpload(true);
            changeVideoUploadUI();
            return;
        }

        // Kiểm tra định dạng file
        if (!ALLOWED_TYPES.includes(file.type)) {
            NotificationModal.show('Định dạng không hỗ trợ. Chỉ chấp nhận file MP4 hoặc MOV.', 'error');
            const input = document.getElementById('video_file');
            if (input) input.value = '';
            return;
        }

        if (file.size > MAX_BACKGROUND_VIDEO_SIZE) {
            NotificationModal.show('Video quá lớn, Tối đa 10GB.', 'error');
            const input = document.getElementById('video_file');
            if (input) input.value = '';
            return;
        }

        // Kiểm tra codec của video
        const codecValidation = await validateVideoCodecs(file);
        codecValidation.valid = true; // Tạm thời bỏ qua kiểm tra codec do độ tin cậy không cao
        if (!codecValidation.valid) {
            NotificationModal.show(codecValidation.message, 'error');
            const input = document.getElementById('video_file');
            if (input) input.value = '';
            return;
        }

        const clearBtn = document.getElementById('btnClearNewVideo');
        const fileNameEl = document.getElementById('videoFileName');
        clearBtn.classList.remove('hidden');
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
        showVideoPreview(file);

        startBackgroundUploadFlow(file);
    }

    /**
     * Kiểm tra video codecs (H.264 cho video, AAC cho audio)
     * @param {File} file - File video cần kiểm tra
     * @returns {Promise<{valid: boolean, message: string}>}
     */
    async function validateVideoCodecs(file) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.preload = 'metadata';
            video.muted = true; // Mute để tránh lỗi autoplay

            const objectUrl = URL.createObjectURL(file);
            video.src = objectUrl;

            const timeout = setTimeout(() => {
                cleanup();
                resolve({
                    valid: false,
                    message: 'Không thể đọc thông tin video. Vui lòng thử file khác.'
                });
            }, 15000); // Timeout sau 15 giây

            const cleanup = () => {
                clearTimeout(timeout);
                video.removeEventListener('loadedmetadata', onLoadedMetadata);
                video.removeEventListener('error', onError);
                video.pause();
                video.src = '';
                video.load();
                try {
                    URL.revokeObjectURL(objectUrl);
                } catch (e) {
                    // noop
                }
            };

            const onLoadedMetadata = async () => {
                try {
                    // Kiểm tra video có audio và video track không
                    const hasVideoTrack = video.videoWidth > 0 && video.videoHeight > 0;

                    if (!hasVideoTrack) {
                        cleanup();
                        resolve({
                            valid: false,
                            message: 'Video không hợp lệ hoặc bị hỏng.'
                        });
                        return;
                    }

                    // Kiểm tra format file trước
                    if (file.type === 'video/webm') {
                        cleanup();
                        resolve({
                            valid: false,
                            message: 'Không hỗ trợ định dạng WebM. Vui lòng sử dụng MP4 hoặc MOV với codec H.264 và AAC.'
                        });
                        return;
                    }

                    // Kiểm tra codec không được phép TRƯỚC KHI thử phát
                    // AV1 codec detection
                    const testAV1Codecs = [
                        'video/mp4; codecs="av01.0.05M.08"',
                        'video/mp4; codecs="av01.0.04M.08"',
                        'video/mp4; codecs="av01.0.08M.08"',
                        'video/webm; codecs="av01"',
                        'video/webm; codecs="av01.0.05M.08"'
                    ];

                    const hasAV1Support = testAV1Codecs.some(codec =>
                        video.canPlayType(codec) === 'probably' || video.canPlayType(codec) === 'maybe'
                    );

                    // VP9 codec detection
                    const hasVP9Support = video.canPlayType('video/webm; codecs="vp9"') === 'probably' ||
                                         video.canPlayType('video/webm; codecs="vp9"') === 'maybe';

                    // VP8 codec detection
                    const hasVP8Support = video.canPlayType('video/webm; codecs="vp8"') === 'probably' ||
                                         video.canPlayType('video/webm; codecs="vp8"') === 'maybe';

                    // HEVC/H.265 codec detection
                    const hasHEVCSupport = video.canPlayType('video/mp4; codecs="hvc1"') === 'probably' ||
                                          video.canPlayType('video/mp4; codecs="hvc1"') === 'maybe' ||
                                          video.canPlayType('video/mp4; codecs="hev1"') === 'probably' ||
                                          video.canPlayType('video/mp4; codecs="hev1"') === 'maybe';

                    // H.264/AVC codec detection
                    const testH264Codecs = [
                        'video/mp4; codecs="avc1.42E01E"',  // H.264 Baseline
                        'video/mp4; codecs="avc1.4D401E"',  // H.264 Main
                        'video/mp4; codecs="avc1.64001E"',  // H.264 High
                        'video/mp4; codecs="avc1.640028"'   // H.264 High Profile
                    ];

                    const hasH264Support = testH264Codecs.some(codec =>
                        video.canPlayType(codec) === 'probably' || video.canPlayType(codec) === 'maybe'
                    );

                    // Tạo canvas để capture frame và kiểm tra video có decode được không
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = Math.min(video.videoWidth, 320);
                    canvas.height = Math.min(video.videoHeight, 240);

                    // Thử phát và capture frame để xác minh codec thực tế
                    try {
                        // Seek đến 1 giây để tránh frame đầu đen
                        video.currentTime = Math.min(1, video.duration / 2);

                        await new Promise((resolveSeek, rejectSeek) => {
                            const seekTimeout = setTimeout(() => {
                                rejectSeek(new Error('Seek timeout'));
                            }, 5000);

                            const onSeeked = () => {
                                clearTimeout(seekTimeout);
                                video.removeEventListener('seeked', onSeeked);
                                resolveSeek();
                            };

                            video.addEventListener('seeked', onSeeked);
                        });

                        // Thử play video
                        const playPromise = video.play();
                        if (playPromise !== undefined) {
                            await playPromise;
                        }

                        // Đợi một chút để video render
                        await new Promise(resolve => setTimeout(resolve, 150));

                        // Thử vẽ frame lên canvas
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                        // Kiểm tra xem canvas có dữ liệu không (không phải toàn màu đen hoặc trong suốt)
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const data = imageData.data;
                        let totalBrightness = 0;
                        let nonZeroPixels = 0;

                        // Kiểm tra nhiều pixel hơn để chính xác hơn
                        for (let i = 0; i < data.length; i += 400) {
                            const brightness = data[i] + data[i+1] + data[i+2];
                            totalBrightness += brightness;
                            if (brightness > 30) {
                                nonZeroPixels++;
                            }
                        }

                        const avgBrightness = totalBrightness / (data.length / 400);
                        const hasValidFrame = nonZeroPixels > 10 || avgBrightness > 20;

                        video.pause();

                        // Nếu video không decode được frame -> có thể là codec không hỗ trợ
                        if (!hasValidFrame) {
                            cleanup();
                            resolve({
                                valid: false,
                                message: 'Video sử dụng codec không được hỗ trợ hoặc bị lỗi. Vui lòng sử dụng H.264 (video) và AAC (audio).'
                            });
                            return;
                        }

                        // Nếu video phát được NHƯNG không hỗ trợ H.264, có thể là AV1 hoặc codec khác
                        if (!hasH264Support && (hasAV1Support || hasVP9Support || hasVP8Support || hasHEVCSupport)) {
                            let codecName = 'không xác định';
                            if (hasAV1Support) codecName = 'AV1';
                            else if (hasHEVCSupport) codecName = 'HEVC/H.265';
                            else if (hasVP9Support) codecName = 'VP9';
                            else if (hasVP8Support) codecName = 'VP8';

                            cleanup();
                            resolve({
                                valid: false,
                                message: `Video sử dụng codec ${codecName} không được hỗ trợ. Vui lòng chuyển đổi sang H.264 (video) và AAC (audio).`
                            });
                            return;
                        }

                        // Video phát được và có H.264 support -> chấp nhận
                        if (hasValidFrame && hasH264Support) {
                            cleanup();
                            resolve({
                                valid: true,
                                message: 'Video hợp lệ'
                            });
                            return;
                        }

                        // Trường hợp không xác định được codec
                        cleanup();
                        resolve({
                            valid: false,
                            message: 'Không thể xác định codec video. Vui lòng đảm bảo sử dụng H.264 (video) và AAC (audio).'
                        });

                    } catch (playError) {
                        console.error('Video playback error:', playError);
                        cleanup();
                        resolve({
                            valid: false,
                            message: 'Video không thể phát được. Vui lòng đảm bảo video sử dụng codec H.264 (video) và AAC (audio).'
                        });
                    }
                } catch (error) {
                    console.error('Validation error:', error);
                    cleanup();
                    resolve({
                        valid: false,
                        message: 'Lỗi khi kiểm tra codec. Vui lòng thử lại.'
                    });
                }
            };

            const onError = (e) => {
                console.error('Video error event:', e);
                cleanup();
                resolve({
                    valid: false,
                    message: 'File video không hợp lệ hoặc bị hỏng. Vui lòng chọn file khác.'
                });
            };

            video.addEventListener('loadedmetadata', onLoadedMetadata);
            video.addEventListener('error', onError);
        });
    }


    // ========================================
    // XỬ LÝ VIDEO PREVIEW
    // ========================================

    function resetVideoPreview() {
        if (!videoPreviewContainer || !videoFilePreview || !videoUrlPreview || !videoIconPlaceholder) return;

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

        const badgeConfig = {
            idle: ['Chưa khởi tạo', 'px-2 py-1 rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-100'],
            creating_session: ['Đang tạo phiên', 'px-2 py-1 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200'],
            uploading: ['Đang upload', 'px-2 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-200'],
            uploaded: ['Đã upload', 'px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200'],
            completing: ['Đang gửi xử lý', 'px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200'],
            processing: ['Đang ghép video', 'px-2 py-1 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-200'],
            completed: ['Hoàn tất', 'px-2 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200'],
            failed: ['Lỗi upload', 'px-2 py-1 rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200'],
            cancelled: ['Đã hủy', 'px-2 py-1 rounded-full bg-gray-200 text-gray-600 dark:bg-gray-600 dark:text-gray-200']
        };

        const [badgeText, badgeClass] = badgeConfig[status] || badgeConfig.idle;
        statusBadge.textContent = badgeText;
        statusBadge.className = badgeClass;

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
            const sessionResponse = await apiRequest('/lessons/video-uploads/sessions', {
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
                NotificationModal.show(error.message || 'Upload video thất bại.', 'error');
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

        const response = await fetch(`/api/lessons/video-uploads/${backgroundUploadState.uploadId}/chunks`, {
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
            await apiRequest(`/lessons/video-uploads/${backgroundUploadState.uploadId}`, {
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
            const response = await apiRequest(`/lessons/video-uploads/${backgroundUploadState.uploadId}/complete`, {
                method: 'POST',
                body: JSON.stringify({
                    lesson_id: targetLessonId
                })
            });
            backgroundUploadState.status = response?.data?.status || 'processing';
            backgroundUploadState.lastError = null;
            changeVideoUploadUI();
            NotificationModal.show(response.message || 'Video đang được xử lý, bạn có thể tiếp tục làm việc.', 'success');
            startBackgroundStatusPolling();
        } catch (error) {
            console.error('Complete upload error:', error);
            backgroundUploadState.lastError = error.message;
            backgroundUploadState.status = 'failed';
            changeVideoUploadUI();
            NotificationModal.show(error.message || 'Không thể gửi yêu cầu xử lý video.', 'error');
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
                    NotificationModal.show('Video đã được xử lý và cập nhật.', 'success');
                } else if (backgroundUploadState.lastError) {
                    NotificationModal.show(backgroundUploadState.lastError, 'error');
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
            const response = await apiRequest(`/lessons/video-uploads/${backgroundUploadState.uploadId}`, {
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

    async function showVideoPreview(source) {
        if (!videoPreviewContainer || !videoFilePreview || !videoUrlPreview || !videoIconPlaceholder) return;

        let finalSrc = null;

        if (source instanceof File) {
            finalSrc = URL.createObjectURL(source);
        } else if (source && typeof source === 'string' && mode === 'EDIT' && lessonId) {
            let data = await apiRequest(`/lessons/${lessonId}/auto-signature`);

            if (data?.success && data?.data?.signed_uri) {
                finalSrc = data.data.base_url + data.data.signed_uri;
                // Set đúng MIME type cho HLS hoặc MP4
                if (data?.data?.type === 'hls') {
                    videoFilePreview.type = 'application/x-mpegURL';
                } else if (data?.data?.type === 'mp4') {
                    videoFilePreview.type = getVideoMimeType(data?.data?.uri);
                }
            }
        }

        videoIconPlaceholder.classList.add('hidden');
        videoUrlPreview.classList.add('hidden');
        videoFilePreview.classList.remove('hidden');
        videoFilePreview.src = finalSrc;
        videoFilePreview.load();
    }

    function initVideoUploadForm() {
        const dropZone = document.getElementById('videoDropZone');
        const input = document.getElementById('video_file');
        const backgroundRetryBtn = document.getElementById('btnRetryBackgroundUpload');

        // Không set required cho video input vì nó bị ẩn và sẽ gây lỗi validation
        // Sẽ validate bằng JavaScript trong hàm saveLesson
        if (input) input.required = false;
        if (backgroundUploadState.file) {
            showVideoPreview(backgroundUploadState.file);
        } else if (existingVideoSource) {
            showVideoPreview(existingVideoSource);
        } else {
            resetVideoPreview();
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

        changeVideoUploadUI();
    }

    function handleClearVideoFile() {
        // Nếu đang upload, hủy upload
        if (['creating_session', 'uploading'].includes(backgroundUploadState.status)) {

        DeleteModal.openSingle({
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
    }

    function handleCancelUpdateLesson() {
        DeleteModal.openSingle({
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
                    // handleGotoBackPage_Global();
                    return isSuccess;
                } catch (error) {
                    console.error('Error canceling upload:', error);
                    return false; // Trả về false nếu có lỗi
                }
            },
            successFuncCallback: () => {
                handleGotoBackPage_Global();
            }
        })
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
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailFile = thumbnailInput && thumbnailInput.files && thumbnailInput.files[0] ? thumbnailInput.files[0] : null;
        const videoInput = document.getElementById('video_file');
        const selectedVideoFile = videoInput && videoInput.files && videoInput.files[0] ? videoInput.files[0] : null;

        // Validation: Tiêu đề bắt buộc
        if (!title) {
            NotificationModal.show('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

        // Validation: Video bắt buộc khi CREATE
        if (mode === 'CREATE' && !backgroundUploadState.file && !selectedVideoFile) {
            NotificationModal.show('Vui lòng chọn video cho bài học', 'error');
            return;
        }

        if (backgroundUploadState.file != null && !backgroundUploadState.uploadId) {
            NotificationModal.show('Vui lòng chọn video và upload nền hoàn tất trước khi lưu.', 'warning');
            return;
        }
        if (backgroundUploadState.file != null && !['uploaded', 'processing', 'completed'].includes(backgroundUploadState.status)) {
            NotificationModal.show('Video vẫn đang upload. Vui lòng chờ hoàn tất để lưu.', 'warning');
            return;
        }

        handleChangeStateButtonSubmitting(true);


        const url = mode === 'EDIT' ? `/lessons/${lessonIdValue}` : '/lessons';

        try {
            const fd = new FormData();
            // Laravel/Symfony không parse multipart cho PUT/PATCH -> dùng POST + _method
            if (mode === 'EDIT') {
                fd.append('_method', 'PUT');
            }
            fd.append('title', title || '');
            fd.append('description', description || '');

            if (thumbnailFile) {
                fd.append('thumbnail', thumbnailFile);
            }

            const placeholderVideoPath = existingVideoSource || `background-upload://${backgroundUploadState.uploadId}`;
            fd.append('video_path', placeholderVideoPath);

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
                if (response.status === 422) {
                    let errorsField = data.errors || null;
                    renderInputErrors_Global(errorsField);
                } else {
                    throw new Error(data.message || 'Có lỗi xảy ra');
                }
            }

            if (data.success) {
                const savedLessonId = mode === 'EDIT'
                    ? parseInt(lessonIdValue, 10)
                    : (data?.data?.id || data?.data?.data?.id || null);

                if (!(mode === 'EDIT') && savedLessonId) {
                    document.getElementById('lessonId').value = savedLessonId;
                }

                if (backgroundUploadState.uploadId && savedLessonId) {
                    try {
                        await startBackgroundProcessing(savedLessonId);
                        existingVideoSource = `background-upload://${backgroundUploadState.uploadId}`;
                    } catch (error) {
                        console.error(error);
                    }
                }

                handleChangeStateButtonSubmitting(false);
                NotificationModal.show(data.message || 'Lưu thành công', 'success', handleGotoBackPage_Global);
            }
        } catch (error) {
            handleChangeStateButtonSubmitting(false);
            NotificationModal.show(error.message, 'error', handleGotoBackPage_Global);
        }
    }

    function handleChangeStateButtonSubmitting(isSubmitting) {
        const submitButton = document.getElementById('lessonFormSubmitButton');
        // const saveIcon = document.getElementById('lessonFormSubmitButton_saveIcon');
        const loadingIcon = document.getElementById('lessonFormSubmitButton_loadingIcon');
        const submitButtonText = document.getElementById('lessonFormSubmitButton_text');
        const lessonFormCancelButton = document.getElementById('lessonFormCancelButton');
        if (isSubmitting == true) {
            lessonFormCancelButton.disabled = true;
            submitButton.disabled = true;
            submitButton.classList.add('cursor-not-allowed', 'opacity-50');
            submitButtonText.textContent = mode === 'CREATE' ? 'Đang tạo...' : 'Đang cập nhật...';
            // saveIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
        }
        if (isSubmitting == false) {
            lessonFormCancelButton.disabled = false;
            submitButton.disabled = false;
            // saveIcon.classList.remove('hidden');
            loadingIcon.classList.add('hidden');
            initUI();
        }
    }

    // ========================================
    // ĐIỀU HƯỚNG VÀ EVENT HANDLERS
    // ========================================

    /**
     * Cảnh báo người dùng khi thoát trang trong khi đang upload
     */
    window.addEventListener('beforeunload', (event) => {
        if (['creating_session', 'uploading'].includes(backgroundUploadState.status)) {
            event.preventDefault();
            event.returnValue = '';
        }
    });

    function getVideoMimeType(url) {
        const extension = url.split('?')[0].split('.').pop().toLowerCase();
        const mimeTypes = {
            mp4: 'video/mp4',
            webm: 'video/webm',
            ogg: 'video/ogg',
            mov: 'video/quicktime',
            avi: 'video/x-msvideo',
            mpeg: 'video/mpeg'
        };

        return mimeTypes[extension] || 'video/mp4';
    }

</script>
@endsection
