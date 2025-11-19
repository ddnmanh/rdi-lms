@extends('admin.layout')

@section('title', $mode === 'CREATE_COURSE' ? 'Thêm khóa học' : 'Chỉnh sửa khóa học')

@section('description', $mode === 'CREATE_COURSE' ? 'Thêm khóa học mới vào hệ thống' : 'Chỉnh sửa thông tin khóa học')

@section('content')
<style>
    .drop-zone.drag-over {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }
    .dark .drop-zone.drag-over {
        background-color: rgba(59, 130, 246, 0.1);
    }
    .draggable-item:active {
        cursor: grabbing;
    }
    .draggable-item.dragging {
        opacity: 0.5;
        transform: scale(0.95);
        z-index: 1000;
    }
    .draggable-item {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.3s ease-out,
                    background-color 0.3s ease-out;
    }
    .draggable-item.moving {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1),
                    opacity 0.3s ease-out;
    }
    .drop-indicator {
        height: 2px;
        background-color: #3b82f6;
        margin: 4px 0;
        border-radius: 2px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .drop-indicator.active {
        opacity: 1;
    }
    .selectable-item {
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }
    .selectable-item:hover {
        border-color: #3b82f6;
        transform: translateY(-1px);
    }
    .selectable-item.selected {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.08);
    }
    .dark .selectable-item.selected {
        border-color: rgba(59, 130, 246, 0.6);
        background-color: rgba(59, 130, 246, 0.18);
    }
    .draggable-item.dropped {
        animation: dropSuccess 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1001;
    }
    @keyframes dropSuccess {
        0% {
            transform: scale(0.9);
            opacity: 0.5;
            background-color: rgba(59, 130, 246, 0.2);
        }
        50% {
            transform: scale(1.02);
            background-color: rgba(59, 130, 246, 0.3);
        }
        100% {
            transform: scale(1);
            opacity: 1;
            background-color: transparent;
        }
    }
    .dark .draggable-item.dropped {
        animation: dropSuccessDark 0.5s ease-out;
    }
    @keyframes dropSuccessDark {
        0% {
            transform: scale(0.9);
            opacity: 0.5;
            background-color: rgba(59, 130, 246, 0.3);
        }
        50% {
            transform: scale(1.02);
            background-color: rgba(59, 130, 246, 0.4);
        }
        100% {
            transform: scale(1);
            opacity: 1;
            background-color: transparent;
        }
    }
</style>
<div class=" flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    {{-- <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.list') }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Quay lại</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Form Card --}}
    <form id="courseForm" onsubmit="saveCourse(event)" class="h-full w-full max-w-[1400px] mx-auto p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <input type="hidden" id="courseId" value="{{ $mode === 'EDIT_COURSE' ? ($courseId ?? '') : '' }}">

        <div class="h-full flex flex-col items-stretch justify-between gap-4 2xl:gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 2xl:gap-6">
                {{-- Thumbnail Preview + Upload --}}
                <div class="col-span-1 row-span-3">
                    <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh thumbnail</label>
                    <div
                        id="thumbnailDropZone"
                        class="flex flex-row flex-wrap items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div class="w-[200px] aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="thumbnailPreview" alt="thumbnail preview" class="h-full w-full object-cover hidden">
                            <svg id="thumbnailPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-12 w-12 text-gray-400">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/>
                            </svg>
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

                <div class="col-span-1">
                    <label for="title" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Tiêu đề *</label>
                    <input type="text" id="title" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                        placeholder="Nhập tiêu đề khóa học">
                </div>

                <div class="col-span-1">
                    <label for="start_date" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Ngày bắt đầu</label>
                    <input type="datetime-local" id="start_date"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">
                        Ngày và giờ bắt đầu khóa học
                        <span id="timezoneIndicator" class="text-blue-600 dark:text-blue-400 font-medium"></span>
                    </p>
                </div>

                <div class="col-span-1">
                    <label for="end_date" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Ngày kết thúc</label>
                    <input type="datetime-local" id="end_date"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Ngày và giờ kết thúc khóa học (phải sau ngày bắt đầu)</p>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                    <textarea id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"
                        placeholder="Nhập mô tả khóa học"></textarea>
                </div>
            </div>

            <div class="h-[400px] flex-shrink-0">
                <div class="h-full min-h-0 rounded-lg relative table-scroll-container flex flex-row items-stretch gap-4">

                    <div class="flex-1 min-h-0 flex flex-col items-stretch justify-start">
                        <label for="description" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Bài học tự do</label>
                        <div id="orphanedLessonsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 drop-zone" data-drop-zone="orphaned">
                            <div class="w-fit mx-auto mt-[100px]">
                                <div id="SPINNER_LOADING">
                                    <div id="SPINNER_LOADING_CONTAINER">
                                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div id="SPINNER_LOADING_ICON">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center w-[100px] 2xl:w-[120px]">
                        <i class="fa-solid fa-arrow-left"></i>
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>

                    <div class="flex-1 min-h-0 flex flex-col items-stretch justify-start">
                        <label for="description" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Bài học của khóa học</label>
                        <div id="parentedLessonsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 drop-zone" data-drop-zone="parented">

                        </div>
                    </div>

                </div>
            </div>

            <div class="mt-[30px] h-[420px] flex-shrink-0 overflow-hidden">
                <div class="h-full min-h-0 rounded-lg relative flex flex-col gap-4 overflow-hidden">
                    <div class="grid h-full min-h-0 grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4">
                        <div class="flex flex-col min-h-0 h-full">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-1 px-2">
                                <label class="ml-2 text-sm font-semibold text-blue-700 dark:text-gray-300">Danh sách sinh viên</label>
                                <input
                                    id="availableStudentsSearch"
                                    type="text"
                                    class="w-full sm:w-64 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                    placeholder="Tìm kiếm theo tên hoặc email"
                                >
                            </div>
                            <div id="availableStudentsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800">
                                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                    <p class="text-sm">Đang tải danh sách sinh viên...</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center gap-3 w-[100px] 2xl:w-[120px] h-full">
                            <button
                                type="button"
                                id="moveStudentsToCourseBtn"
                                class="px-3 py-2 w-full sm:w-auto rounded-md text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition"
                                onclick="moveSelectedStudentsToEnrolled()"
                                disabled
                            >
                                Thêm vào khóa
                            </button>
                            <button
                                type="button"
                                id="removeStudentsFromCourseBtn"
                                class="px-3 py-2 w-full sm:w-auto rounded-md text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 disabled:bg-gray-200 disabled:text-gray-400 disabled:dark:bg-gray-700/40 disabled:dark:text-gray-500 disabled:cursor-not-allowed transition"
                                onclick="moveSelectedStudentsToAvailable()"
                                disabled
                            >
                                Gỡ khỏi khóa
                            </button>
                        </div>

                        <div class="flex flex-col min-h-0 h-full">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-1 px-2">
                                <label class="ml-2 text-sm font-semibold text-blue-700 dark:text-gray-300">Học viên của khóa học</label>
                                <input
                                    id="enrolledStudentsSearch"
                                    type="text"
                                    class="w-full sm:w-64 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                    placeholder="Lọc học viên đã chọn"
                                >
                            </div>
                            <div id="enrolledStudentsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800">
                                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                    <p class="text-sm">Chưa có học viên trong khóa học</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    onclick="handleBackToPrevPage()"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                >
                    Hủy
                </button>
                <button
                    type="submit"
                    id="submitBtn"
                    class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                    {{ $mode === 'CREATE_COURSE' ? 'Tạo' : 'Cập nhật' }}
                </button>
            </div>
        </div>
    </form>

</div>

<script>
    const mode = '{{ $mode }}';
    let courseId = @if($mode === 'EDIT_COURSE' && isset($courseId)) {{ $courseId }} @else null @endif;
    let courseData = null;
    let orphanedLessonsList = []; // Các bài học chưa thuộc về khóa học nào
    let parentedLessonsList = []; // Các bài học thuộc khóa học này
    let selectedLessonIds = new Set();
    let lessonTextSearch = '';

    let availableStudentsList = [];
    let enrolledStudentsList = [];
    let selectedAvailableStudentIds = new Set();
    let selectedEnrolledStudentIds = new Set();
    let initialEnrolledStudentIds = new Set();
    let availableStudentsSearchTerm = '';
    let enrolledStudentsSearchTerm = '';

    let existingThumbnail = null;
    let thumbnailPreview = null;
    let isDragActive = false;

    document.addEventListener('DOMContentLoaded', async function() {
        // Hiển thị timezone của client
        const timezoneIndicator = document.getElementById('timezoneIndicator');
        if (timezoneIndicator) {
            const clientTimezone = getClientTimezone_Global();
            timezoneIndicator.textContent = `(Múi giờ: ${clientTimezone})`;
        }

        if (mode === 'EDIT_COURSE' && courseId) {
            await Promise.all([
                loadCourseData(),
                loadParentedLessons(),
                loadOrphanedLessons()
            ]);

            if (courseData != null) {
                renderCourse();
                renderOrphanedLessons();
                renderParentedLessons();
            }

        } else {
            // For create mode, still load orphaned lessons
            await loadOrphanedLessons();
            renderOrphanedLessons();
        }

        initThumbnailPreview();
        initDropZones();
        await initStudentsSection();
        initStudentSearchHandlers();
        updateStudentActionButtons();

    });

    function handleBackToPrevPage() {
        window.location.href = '{{ route('admin.courses.list') }}';
    }

    function handleThumbnailFile(file) {
        const MAX_SIZE = 2 * 1024 * 1024; // 2MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (thumbnailPreview) {
            try {
                URL.revokeObjectURL(thumbnailPreview);
            } catch (e) {
                // noop
            }
        }

        if (!file) {
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailPlaceholder');
            const clearBtn = document.getElementById('btnClearNewThumbnail');
            const input = document.getElementById('thumbnail');

            if (existingThumbnail) {
                thumbnailEl.src = existingThumbnail;
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
            showNotificationModel_Global('Ảnh quá lớn. Kích thước tối đa 2MB.', 'error');
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        thumbnailPreview = previewUrl;

        const thumbnailEl = document.getElementById('thumbnailPreview');
        const placeholderEl = document.getElementById('thumbnailPlaceholder');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        thumbnailEl.src = previewUrl;
        thumbnailEl.classList.remove('hidden');
        placeholderEl.classList.add('hidden');
        clearBtn.classList.remove('hidden');
    }

    function initThumbnailPreview() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const input = document.getElementById('thumbnail');
        const clearBtn = document.getElementById('btnClearNewThumbnail');
        const thumbnailEl = document.getElementById('thumbnailPreview');
        const placeholderEl = document.getElementById('thumbnailPlaceholder');

        // Default preview for create mode
        if (!existingThumbnail && mode === 'CREATE_COURSE') {
            thumbnailEl.classList.add('hidden');
            placeholderEl.classList.remove('hidden');
        }

        // Drag and drop handlers
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                isDragActive = true;
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
            });

            dropZone.addEventListener('dragleave', () => {
                isDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                isDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');

                const file = e.dataTransfer.files && e.dataTransfer.files[0] ? e.dataTransfer.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleThumbnailFile(null);
            });
        }
    }

    // Render nội dung vào html
    function renderCourse() {
        try {
            document.getElementById('title').value = courseData.title || '';
            document.getElementById('description').value = courseData.description || '';
            document.getElementById('start_date').value = formatDateTimeLocal(courseData.start_date);
            document.getElementById('end_date').value = formatDateTimeLocal(courseData.end_date);

            // Load thumbnail
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailPlaceholder');
            if (courseData.thumbnail_path) {
                existingThumbnail = courseData.thumbnail_path;
                thumbnailEl.src = courseData.thumbnail_path;
                thumbnailEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải thông tin khóa học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    function renderOrphanedLessons() {
        try {
            const orphanedLessonsListBox = document.getElementById('orphanedLessonsListBox');

            if (!orphanedLessonsListBox) return;

            // Hide spinner if exists
            const spinner = orphanedLessonsListBox.querySelector('#SPINNER_LOADING');
            if (spinner) {
                spinner.style.display = 'none';
            }

            if (orphanedLessonsList.length === 0) {
                orphanedLessonsListBox.innerHTML = `
                    <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                        <p class="text-sm">Không có bài học tự do</p>
                    </div>
                `;
                return;
            }

            orphanedLessonsListBox.innerHTML = orphanedLessonsList.map(orphanedLesson => `
                <div
                    class="draggable-item group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-move transition-all"
                    draggable="true"
                    data-lesson-id="${orphanedLesson.id}"
                    data-list-type="orphaned"
                >
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                            <img src="${orphanedLesson.thumbnail_path}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(orphanedLesson.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ${orphanedLesson.id ?? '-'}</p>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-4">
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">${orphanedLesson.description ? escapeHtml_Global(orphanedLesson.description.slice(0, 40) + (orphanedLesson.description.length > 40 ? '…' : '')) : ''}</span>
                        </div>
                        <div class="flex flex-row items-center gap-0.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 cursor-pointer">
                            <i class="fa-solid fa-grip-vertical"></i>
                        </div>
                    </div>
                </div>
            `).join('');

            // Attach drag event listeners
            attachDragListeners(orphanedLessonsListBox, 'orphaned');

        } catch (error) {
            showNotificationModel_Global('Lỗi khi render các bài học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    function renderParentedLessons() {
        try {
            const parentedLessonsListBox = document.getElementById('parentedLessonsListBox');

            if (!parentedLessonsListBox) return;

            if (parentedLessonsList.length === 0) {
                parentedLessonsListBox.innerHTML = `
                    <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                        <p class="text-sm">Không có bài học trong khóa học</p>
                    </div>
                `;
                return;
            }

            parentedLessonsListBox.innerHTML = parentedLessonsList.map(parentedLesson => `
                <div
                    class="draggable-item group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-move transition-all"
                    draggable="true"
                    data-lesson-id="${parentedLesson.id}"
                    data-list-type="parented"
                >
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                            <img src="${parentedLesson.thumbnail_path}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(parentedLesson.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ${parentedLesson.id ?? '-'} | Thứ tự: ${parentedLesson.display_order ?? '-'}</p>
                        </div>
                    </div>
                    <div class="flex flex-row items-center gap-4">
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">${parentedLesson.description ? escapeHtml_Global(parentedLesson.description.slice(0, 80) + (parentedLesson.description.length > 80 ? '…' : '')) : ''}</span>
                        </div>
                        <div class="flex flex-row items-center gap-0.5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 cursor-pointer">
                            <i class="fa-solid fa-grip-vertical"></i>
                        </div>
                    </div>
                </div>
            `).join('');

            // Attach drag event listeners
            attachDragListeners(parentedLessonsListBox, 'parented');

        } catch (error) {
            showNotificationModel_Global('Lỗi khi render các bài học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    async function initStudentsSection() {
        try {
            hydrateEnrolledStudentsFromCourse();
            await loadAvailableStudents();
            renderAvailableStudents();
            renderEnrolledStudents();
        } catch (error) {
            console.error(error);
            const container = document.getElementById('availableStudentsListBox');
            if (container) {
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full text-sm text-red-500 dark:text-red-400 text-center px-4">
                        ${escapeHtml_Global(error.message || 'Không thể tải danh sách sinh viên')}
                    </div>
                `;
            }
            showNotificationModel_Global('Không thể tải danh sách sinh viên: ' + (error.message || ''), 'error');
        }
    }

    function hydrateEnrolledStudentsFromCourse() {
        if (mode === 'EDIT_COURSE' && courseData && Array.isArray(courseData.users)) {
            enrolledStudentsList = courseData.users.map(normalizeStudent);
        } else {
            enrolledStudentsList = Array.isArray(enrolledStudentsList) ? enrolledStudentsList.map(normalizeStudent) : [];
        }

        sortStudentsInPlace(enrolledStudentsList);
        initialEnrolledStudentIds = new Set(enrolledStudentsList.map(student => student.id));
    }

    async function loadAvailableStudents() {
        const params = new URLSearchParams({
            role_name: 'STUDENT',
            per_page: 200
        });

        const data = await apiRequest(`/users?${params.toString()}`);
        const apiStudents = data?.data?.data || [];

        const enrolledIds = new Set(enrolledStudentsList.map(student => student.id));
        availableStudentsList = apiStudents
            .map(normalizeStudent)
            .filter(student => !enrolledIds.has(student.id));

        sortStudentsInPlace(availableStudentsList);
    }

    function initStudentSearchHandlers() {
        const availableInput = document.getElementById('availableStudentsSearch');
        if (availableInput && !availableInput.dataset.initialized) {
            availableInput.dataset.initialized = 'true';
            availableInput.addEventListener('input', (event) => {
                availableStudentsSearchTerm = event.target.value.trim().toLowerCase();
                renderAvailableStudents();
            });
        }

        const enrolledInput = document.getElementById('enrolledStudentsSearch');
        if (enrolledInput && !enrolledInput.dataset.initialized) {
            enrolledInput.dataset.initialized = 'true';
            enrolledInput.addEventListener('input', (event) => {
                enrolledStudentsSearchTerm = event.target.value.trim().toLowerCase();
                renderEnrolledStudents();
            });
        }
    }

    function normalizeStudent(raw) {
        if (!raw) return { id: null, fullname: '', email: '' };
        const rawId = raw.id ?? raw.user_id;
        const id = rawId !== undefined && rawId !== null ? Number(rawId) : null;
        return {
            id,
            fullname: raw.fullname || raw.name || '',
            email: raw.email || '',
            avatar_path: raw.avatar_path || raw.avatar || raw.avatar_url || '',
            phone: raw.phone || raw.phone_number || ''
        };
    }

    function renderAvailableStudents() {
        const container = document.getElementById('availableStudentsListBox');
        if (!container) return;

        const searchTerm = availableStudentsSearchTerm.trim().toLowerCase();
        const filteredStudents = availableStudentsList.filter(student => studentMatchesSearch(student, searchTerm));

        if (filteredStudents.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 text-sm text-center px-4">
                    ${searchTerm ? 'Không tìm thấy sinh viên phù hợp' : 'Không còn sinh viên nào để thêm'}
                </div>
            `;
            return;
        }

        container.innerHTML = filteredStudents.map(student => {
            const isSelected = selectedAvailableStudentIds.has(student.id);
            const initial = (student.fullname || student.email || 'S').trim().charAt(0).toUpperCase();

            return `
                <label
                    class="selectable-item flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 cursor-pointer ${isSelected ? 'selected ring-1 ring-blue-400/60' : ''}"
                >
                    <input
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        onchange="toggleStudentSelection('available', ${student.id}, this.checked)"
                        ${isSelected ? 'checked' : ''}
                    >
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="h-9 w-9 overflow-hidden flex-shrink-0 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 grid place-items-center text-sm font-semibold uppercase">
                            <img src="${student.avatar_path}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(student.fullname || student.email || 'Không có tên')}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${escapeHtml_Global(student.email || '')}</p>
                        </div>
                    </div>
                </label>
            `;
        }).join('');
    }

    function renderEnrolledStudents() {
        const container = document.getElementById('enrolledStudentsListBox');
        if (!container) return;

        const searchTerm = enrolledStudentsSearchTerm.trim().toLowerCase();
        const filteredStudents = enrolledStudentsList.filter(student => studentMatchesSearch(student, searchTerm));

        if (filteredStudents.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 text-sm text-center px-4">
                    ${enrolledStudentsList.length === 0 ? 'Chưa có học viên trong khóa học' : 'Không tìm thấy học viên phù hợp'}
                </div>
            `;
            return;
        }

        container.innerHTML = filteredStudents.map(student => {
            const isSelected = selectedEnrolledStudentIds.has(student.id);
            const initial = (student.fullname || student.email || 'S').trim().charAt(0).toUpperCase();

            return `
                <label
                    class="selectable-item flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 cursor-pointer ${isSelected ? 'selected ring-1 ring-blue-400/60' : ''}"
                >
                    <input
                        type="checkbox"
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        onchange="toggleStudentSelection('enrolled', ${student.id}, this.checked)"
                        ${isSelected ? 'checked' : ''}
                    >
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="h-9 w-9 overflow-hidden flex-shrink-0 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 grid place-items-center text-sm font-semibold uppercase">
                            <img src="${student.avatar_path}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(student.fullname || student.email || 'Không có tên')}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${escapeHtml_Global(student.email || '')}</p>
                        </div>
                    </div>
                </label>
            `;
        }).join('');
    }

    function toggleStudentSelection(listType, studentId, isChecked) {
        const numericId = Number(studentId);
        if (!Number.isFinite(numericId)) {
            updateStudentActionButtons();
            return;
        }
        const targetSet = listType === 'available' ? selectedAvailableStudentIds : selectedEnrolledStudentIds;

        if (isChecked) {
            targetSet.add(numericId);
        } else {
            targetSet.delete(numericId);
        }

        updateStudentActionButtons();
    }

    function moveSelectedStudentsToEnrolled() {
        if (selectedAvailableStudentIds.size === 0) return;

        const idsToMove = new Set(selectedAvailableStudentIds);
        const movingStudents = [];

        availableStudentsList = availableStudentsList.filter(student => {
            if (idsToMove.has(student.id)) {
                movingStudents.push(student);
                return false;
            }
            return true;
        });

        if (movingStudents.length === 0) {
            selectedAvailableStudentIds.clear();
            updateStudentActionButtons();
            renderAvailableStudents();
            return;
        }

        const existingEnrolledIds = new Set(enrolledStudentsList.map(student => student.id));
        movingStudents.forEach(student => {
            if (!existingEnrolledIds.has(student.id)) {
                enrolledStudentsList.push(student);
            }
        });

        sortStudentsInPlace(enrolledStudentsList);
        selectedAvailableStudentIds.clear();
        renderAvailableStudents();
        renderEnrolledStudents();
        updateStudentActionButtons();
    }

    function moveSelectedStudentsToAvailable() {
        if (selectedEnrolledStudentIds.size === 0) return;

        const idsToMove = new Set(selectedEnrolledStudentIds);
        const movingStudents = [];

        enrolledStudentsList = enrolledStudentsList.filter(student => {
            if (idsToMove.has(student.id)) {
                movingStudents.push(student);
                return false;
            }
            return true;
        });

        if (movingStudents.length > 0) {
            const existingAvailableIds = new Set(availableStudentsList.map(student => student.id));
            movingStudents.forEach(student => {
                if (!existingAvailableIds.has(student.id)) {
                    availableStudentsList.push(student);
                }
            });
            sortStudentsInPlace(availableStudentsList);
        }

        selectedEnrolledStudentIds.clear();
        renderAvailableStudents();
        renderEnrolledStudents();
        updateStudentActionButtons();
    }

    function updateStudentActionButtons() {
        const addBtn = document.getElementById('moveStudentsToCourseBtn');
        if (addBtn) {
            addBtn.disabled = selectedAvailableStudentIds.size === 0;
        }

        const removeBtn = document.getElementById('removeStudentsFromCourseBtn');
        if (removeBtn) {
            removeBtn.disabled = selectedEnrolledStudentIds.size === 0;
        }
    }

    function sortStudentsInPlace(list) {
        if (!Array.isArray(list)) return;
        list.sort((a, b) => {
            const nameA = (a.fullname || a.email || '').toLowerCase();
            const nameB = (b.fullname || b.email || '').toLowerCase();
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return 0;
        });
    }

    function studentMatchesSearch(student, term) {
        if (!term) return true;
        const fullname = (student.fullname || '').toLowerCase();
        const email = (student.email || '').toLowerCase();
        return fullname.includes(term) || email.includes(term);
    }

    function escapeHtml_Global(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    // Drag and Drop functionality
    let draggedElement = null;
    let draggedLessonData = null;
    let draggedFromListType = null;
    let draggedFromIndex = null;
    let dropIndicator = null;

    function attachDragListeners(container, listType) {
        const items = container.querySelectorAll('.draggable-item');
        items.forEach((item, index) => {
            item.addEventListener('dragstart', (e) => handleDragStart(e, index));
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragover', handleItemDragOver);
            item.addEventListener('dragleave', handleItemDragLeave);
        });
    }

    function handleDragStart(e, index) {
        // Tìm element có class draggable-item (có thể là e.target hoặc parent)
        draggedElement = e.target.closest('.draggable-item');
        if (!draggedElement) return;

        draggedElement.classList.add('dragging', 'opacity-50', 'scale-95');

        const lessonId = parseInt(draggedElement.getAttribute('data-lesson-id'));
        draggedFromListType = draggedElement.getAttribute('data-list-type');
        draggedFromIndex = index;

        // Tìm lesson data từ mảng tương ứng
        if (draggedFromListType === 'orphaned') {
            draggedLessonData = orphanedLessonsList.find(lesson => lesson.id === lessonId);
        } else {
            draggedLessonData = parentedLessonsList.find(lesson => lesson.id === lessonId);
        }

        if (!draggedLessonData) {
            e.preventDefault();
            return;
        }

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', draggedElement.outerHTML);
    }

    function handleDragEnd(e) {
        if (draggedElement) {
            draggedElement.classList.remove('dragging', 'opacity-50', 'scale-95');
        }
        draggedElement = null;
        draggedLessonData = null;
        draggedFromListType = null;
        draggedFromIndex = null;

        // Remove all drag-over classes and drop indicators
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.classList.remove('drag-over');
        });
        document.querySelectorAll('.drop-indicator').forEach(indicator => {
            indicator.remove();
        });
        dropIndicator = null;
    }

    function handleItemDragOver(e) {
        e.preventDefault();
        e.stopPropagation();

        const item = e.currentTarget;
        if (!item || item === draggedElement) return;

        const rect = item.getBoundingClientRect();
        const mouseY = e.clientY;
        const itemMiddle = rect.top + rect.height / 2;
        const zone = item.parentElement;

        // Xóa tất cả indicators cũ (cả ở item và ở cuối zone)
        const existingIndicators = zone.querySelectorAll('.drop-indicator');
        existingIndicators.forEach(indicator => {
            indicator.remove();
        });

        // Tạo indicator mới
        const indicator = document.createElement('div');
        indicator.className = 'drop-indicator active';

        // Chèn indicator trước hoặc sau item dựa trên vị trí chuột
        if (mouseY < itemMiddle) {
            item.parentElement.insertBefore(indicator, item);
        } else {
            item.parentElement.insertBefore(indicator, item.nextSibling);
        }

        dropIndicator = indicator;
    }

    function handleItemDragLeave(e) {
        // Chỉ xóa indicator nếu không còn trong item
        if (!e.currentTarget.contains(e.relatedTarget)) {
            const indicator = e.currentTarget.parentElement.querySelector('.drop-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }

    function initDropZones() {
        const orphanedZone = document.getElementById('orphanedLessonsListBox');
        const parentedZone = document.getElementById('parentedLessonsListBox');

        [orphanedZone, parentedZone].forEach(zone => {
            if (!zone) return;

            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'move';
                zone.classList.add('drag-over');

                // Nếu drag over vùng trống (không phải item), hiển thị indicator ở cuối
                const target = e.target;
                if (!target.closest('.draggable-item')) {
                    // Xóa tất cả indicators cũ nếu có
                    const existingIndicators = zone.querySelectorAll('.drop-indicator');
                    existingIndicators.forEach(indicator => {
                        indicator.remove();
                    });

                    // Tạo indicator ở cuối danh sách
                    const indicator = document.createElement('div');
                    indicator.className = 'drop-indicator active';
                    zone.appendChild(indicator);
                }
            });

            zone.addEventListener('dragleave', (e) => {
                // Only remove class if we're leaving the zone itself, not a child
                if (!zone.contains(e.relatedTarget)) {
                    zone.classList.remove('drag-over');
                    // Xóa indicator khi rời zone
                    const indicators = zone.querySelectorAll('.drop-indicator');
                    indicators.forEach(indicator => {
                        indicator.remove();
                    });
                }
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                e.stopPropagation();
                zone.classList.remove('drag-over');

                if (!draggedLessonData || !draggedFromListType) return;

                const targetListType = zone.getAttribute('data-drop-zone');

                // Tìm vị trí drop dựa trên indicator hoặc vị trí chuột
                let dropIndex = null;
                const indicator = zone.querySelector('.drop-indicator');

                if (indicator) {
                    // Tìm index của indicator trong danh sách các items (bỏ qua indicator)
                    const allChildren = Array.from(zone.children);
                    const indicatorIndex = allChildren.indexOf(indicator);

                    // Đếm số items trước indicator
                    let itemCount = 0;
                    for (let i = 0; i < indicatorIndex; i++) {
                        if (allChildren[i].classList.contains('draggable-item')) {
                            itemCount++;
                        }
                    }
                    dropIndex = itemCount;
                } else {
                    // Nếu không có indicator, tìm item gần nhất với vị trí chuột
                    const items = Array.from(zone.querySelectorAll('.draggable-item'));

                    if (items.length === 0) {
                        // Nếu không có item nào, thêm vào đầu
                        dropIndex = 0;
                    } else {
                        let closestItem = null;
                        let closestDistance = Infinity;

                        items.forEach(item => {
                            const rect = item.getBoundingClientRect();
                            const itemCenterY = rect.top + rect.height / 2;
                            const distance = Math.abs(e.clientY - itemCenterY);

                            if (distance < closestDistance) {
                                closestDistance = distance;
                                closestItem = item;
                            }
                        });

                        if (closestItem) {
                            const rect = closestItem.getBoundingClientRect();
                            const itemMiddle = rect.top + rect.height / 2;
                            const itemIndex = items.indexOf(closestItem);

                            if (e.clientY < itemMiddle) {
                                dropIndex = itemIndex;
                            } else {
                                dropIndex = itemIndex + 1;
                            }
                        } else {
                            // Fallback: thêm vào cuối
                            dropIndex = items.length;
                        }
                    }
                }

                // Xóa tất cả drop indicators
                document.querySelectorAll('.drop-indicator').forEach(indicator => {
                    indicator.remove();
                });

                // Xử lý di chuyển giữa hai danh sách
                if (draggedFromListType !== targetListType) {
                    // Xóa lesson khỏi danh sách nguồn
                    if (draggedFromListType === 'orphaned') {
                        orphanedLessonsList = orphanedLessonsList.filter(lesson => lesson.id !== draggedLessonData.id);
                    } else {
                        parentedLessonsList = parentedLessonsList.filter(lesson => lesson.id !== draggedLessonData.id);
                    }

                    // Thêm lesson vào danh sách đích tại vị trí drop
                    if (targetListType === 'orphaned') {
                        orphanedLessonsList.splice(dropIndex, 0, draggedLessonData);
                    } else {
                        parentedLessonsList.splice(dropIndex, 0, draggedLessonData);
                    }
                } else {
                    // Sắp xếp lại trong cùng một danh sách
                    let sourceList = draggedFromListType === 'orphaned' ? orphanedLessonsList : parentedLessonsList;

                    // Tìm vị trí cũ của item
                    const oldIndex = sourceList.findIndex(lesson => lesson.id === draggedLessonData.id);
                    if (oldIndex === -1) return;

                    // Tính toán index mới
                    let newIndex = dropIndex;

                    // Điều chỉnh newIndex nếu cần
                    if (dropIndex > oldIndex) {
                        // Di chuyển về phía sau: sau khi xóa oldIndex, các item sau sẽ dịch lên
                        newIndex = Math.min(dropIndex - 1, sourceList.length - 1);
                    } else if (dropIndex <= oldIndex) {
                        // Di chuyển về phía trước: giữ nguyên dropIndex
                        newIndex = Math.max(0, dropIndex);
                    }

                    // Nếu vị trí không thay đổi, không làm gì
                    if (oldIndex === newIndex) return;

                    // Xóa item khỏi vị trí cũ
                    const [movedItem] = sourceList.splice(oldIndex, 1);

                    // Chèn item vào vị trí mới
                    sourceList.splice(newIndex, 0, movedItem);
                }

                // Cập nhật display_order cho danh sách đích
                const targetList = targetListType === 'orphaned' ? orphanedLessonsList : parentedLessonsList;
                targetList.forEach((lesson, index) => {
                    lesson.display_order = index + 1;
                });

                // Cập nhật display_order cho danh sách nguồn (khi di chuyển giữa hai danh sách)
                if (draggedFromListType !== targetListType) {
                    const sourceList = draggedFromListType === 'orphaned' ? orphanedLessonsList : parentedLessonsList;
                    sourceList.forEach((lesson, index) => {
                        lesson.display_order = index + 1;
                    });
                }

                // Lưu ID của item vừa drop để highlight sau khi render
                const droppedLessonId = draggedLessonData.id;

                // Lưu vị trí cũ của các item trước khi re-render (FLIP technique)
                const targetZone = targetListType === 'orphaned'
                    ? document.getElementById('orphanedLessonsListBox')
                    : document.getElementById('parentedLessonsListBox');

                const sourceZone = draggedFromListType === 'orphaned'
                    ? document.getElementById('orphanedLessonsListBox')
                    : document.getElementById('parentedLessonsListBox');

                // Lưu vị trí cũ của tất cả items trong target zone
                const oldPositions = new Map();
                if (targetZone) {
                    const items = targetZone.querySelectorAll('.draggable-item');
                    items.forEach((item, index) => {
                        const lessonId = parseInt(item.getAttribute('data-lesson-id'));
                        const rect = item.getBoundingClientRect();
                        oldPositions.set(lessonId, {
                            top: rect.top,
                            left: rect.left,
                            index: index
                        });
                    });
                }

                // Lưu vị trí cũ của items trong source zone (nếu khác target zone)
                const sourceOldPositions = new Map();
                if (sourceZone && sourceZone !== targetZone) {
                    const items = sourceZone.querySelectorAll('.draggable-item');
                    items.forEach((item, index) => {
                        const lessonId = parseInt(item.getAttribute('data-lesson-id'));
                        const rect = item.getBoundingClientRect();
                        sourceOldPositions.set(lessonId, {
                            top: rect.top,
                            left: rect.left,
                            index: index
                        });
                    });
                }

                // Re-render cả hai danh sách
                renderOrphanedLessons();
                renderParentedLessons();

                // Animate các item di chuyển (FLIP technique)
                requestAnimationFrame(() => {
                    if (targetZone) {
                        const newItems = targetZone.querySelectorAll('.draggable-item');
                        newItems.forEach((item, newIndex) => {
                            const lessonId = parseInt(item.getAttribute('data-lesson-id'));

                            // Bỏ qua item vừa drop (sẽ có animation riêng)
                            if (lessonId === droppedLessonId) return;

                            const newRect = item.getBoundingClientRect();

                            // Kiểm tra xem item này có di chuyển không
                            const oldPos = oldPositions.get(lessonId);
                            if (oldPos) {
                                const deltaY = oldPos.top - newRect.top;
                                const deltaX = oldPos.left - newRect.left;

                                // Nếu có sự thay đổi vị trí đáng kể (> 1px)
                                if (Math.abs(deltaY) > 1 || Math.abs(deltaX) > 1) {
                                    // Invert: đặt item về vị trí cũ
                                    item.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                                    item.style.opacity = '0.8';
                                    item.classList.add('moving');

                                    // Play: animate về vị trí mới
                                    requestAnimationFrame(() => {
                                        item.style.transform = '';
                                        item.style.opacity = '';

                                        // Xóa class moving sau khi animation kết thúc
                                        setTimeout(() => {
                                            item.classList.remove('moving');
                                        }, 400);
                                    });
                                }
                            }
                        });
                    }

                    // Animate items trong source zone nếu khác target zone
                    if (sourceZone && sourceZone !== targetZone) {
                        const newItems = sourceZone.querySelectorAll('.draggable-item');
                        newItems.forEach((item, newIndex) => {
                            const lessonId = parseInt(item.getAttribute('data-lesson-id'));
                            const newRect = item.getBoundingClientRect();

                            const oldPos = sourceOldPositions.get(lessonId);
                            if (oldPos) {
                                const deltaY = oldPos.top - newRect.top;
                                const deltaX = oldPos.left - newRect.left;

                                if (Math.abs(deltaY) > 1 || Math.abs(deltaX) > 1) {
                                    item.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
                                    item.style.opacity = '0.7';
                                    item.classList.add('moving');

                                    requestAnimationFrame(() => {
                                        item.style.transform = '';
                                        item.style.opacity = '';

                                        setTimeout(() => {
                                            item.classList.remove('moving');
                                        }, 400);
                                    });
                                }
                            }
                        });
                    }

                    // Thêm animation cho item vừa drop
                    setTimeout(() => {
                        if (targetZone) {
                            const droppedItem = targetZone.querySelector(`[data-lesson-id="${droppedLessonId}"]`);
                            if (droppedItem) {
                                // Nếu item này là item mới (không có trong oldPositions),
                                // thêm animation fade in và scale
                                if (!oldPositions.has(droppedLessonId)) {
                                    droppedItem.style.opacity = '0';
                                    droppedItem.style.transform = 'scale(0.8)';
                                    requestAnimationFrame(() => {
                                        droppedItem.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                                        droppedItem.style.opacity = '';
                                        droppedItem.style.transform = '';
                                    });
                                }

                                // Thêm animation highlight
                                droppedItem.classList.add('dropped');
                                // Xóa class sau khi animation kết thúc
                                setTimeout(() => {
                                    droppedItem.classList.remove('dropped');
                                    droppedItem.style.transition = '';
                                }, 600);
                            }
                        }
                    }, 50);
                });
            });
        });
    }

    // Lấy thông tin khóa học này
    async function loadCourseData() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data.success) {
                courseData = data.data;
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải thông tin khóa học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    // Lấy danh sách bài học thuộc về khóa học này
    async function loadParentedLessons() {
        try {
            const data = await apiRequest(`/lessons?course_id=${courseId}`);
            if (data.success) {
                parentedLessonsList = [...data.data?.data];
            }
        } catch (error) {
            showNotificationModel_Global('Xảy ra lỗi khi lấy các bài học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    // Lấy danh sách bài học chưa thuộc về khóa học nào
    async function loadOrphanedLessons() {
        try {
            const data = await apiRequest(`/lessons?orphaned=true`);
            if (data.success) {
                orphanedLessonsList = [...data.data?.data];
            }
        } catch (error) {
            showNotificationModel_Global('Xảy ra lỗi khi lấy các bài học: ' + error.message, 'error', handleBackToPrevPage);
        }
    }

    async function saveCourse(event) {
        event.preventDefault();

        const submitBtn = document.getElementById('submitBtn');

        // Disable submit button
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';
        }

        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const fileInput = document.getElementById('thumbnail');
        const thumbnailFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

        // Validation
        if (!title) {
            showNotificationModel_Global('Vui lòng nhập tiêu đề khóa học', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = mode === 'CREATE_COURSE' ? 'Tạo' : 'Cập nhật';
            }
            return;
        }

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (end < start) {
                showNotificationModel_Global('Ngày kết thúc phải sau ngày bắt đầu', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = mode === 'CREATE_COURSE' ? 'Tạo' : 'Cập nhật';
                }
                return;
            }
        }

        const clientTimezone = getClientTimezone_Global();

        try {
            let data;
            const fd = new FormData();
            // Laravel/Symfony không parse multipart cho PUT/PATCH -> dùng POST + _method
            if (mode == 'EDIT_COURSE') {
                fd.append('_method', 'PUT');
            }
            fd.append('title', title);
            if (description) fd.append('description', description);
            if (startDate) fd.append('start_date', startDate);
            if (endDate) fd.append('end_date', endDate);
            fd.append('timezone', clientTimezone);
            if (existingThumbnail) fd.append('existingThumbnail', existingThumbnail);
            if (thumbnailFile) fd.append('thumbnail', thumbnailFile);

            const url = mode == 'EDIT_COURSE' ? `/courses/${courseId}` : '/courses';
            const response = await fetch(`/api${url}`, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                    // DO NOT set Content-Type here; browser will set with boundary
                }
            });
            const json = await response.json();
            if (!response.ok) {
                throw new Error(json.message || 'Có lỗi xảy ra');
            }
            data = json;


            if (data.success) {
                courseId = data.data?.id ?? courseId;
                if (data.data) {
                    courseData = data.data;
                }
                await handleUpdateLessonsForCourse();
                await handleUpdateStudentsForCourse();
                showNotificationModel_Global(mode == 'EDIT_COURSE' ? 'Cập nhật khóa học thành công' : 'Tạo khóa học thành công', 'success', handleBackToPrevPage);
            }
        } catch (error) {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = mode === 'CREATE_COURSE' ? 'Tạo' : 'Cập nhật';
            }
            showNotificationModel_Global(error.message || 'Thao tác thất bại', 'error', handleBackToPrevPage);
        }
    }

    async function handleUpdateLessonsForCourse() {
        try {
            if (!courseId) {
                return;
            }

            let lessons_ids_add = parentedLessonsList.map(lesson => ({id: lesson.id, display_order: lesson.display_order}));

            if (lessons_ids_add.length > 0) {
                await apiRequest(`/courses/${courseId}/lessons/add`, {
                    method: 'POST',
                    body: JSON.stringify({
                        lessons: lessons_ids_add
                    })
                });
            }


            let lessons_ids_remove = orphanedLessonsList.filter(lesson => lesson.course_id !== null)?.map(lesson => lesson.id);

            if (lessons_ids_remove.length > 0) {
                await apiRequest(`/courses/${courseId}/lessons/remove`, {
                    method: 'POST',
                    body: JSON.stringify({
                        lesson_ids: lessons_ids_remove
                    })
                });
            }

        } catch (error) {
            throw new Error(error.message);
        }
    }

    async function handleUpdateStudentsForCourse() {
        if (!courseId) {
            return;
        }

        const finalIds = Array.from(new Set(
            enrolledStudentsList
                .map(student => student.id)
                .filter(id => Number.isInteger(id))
        ));

        const initialIdsArray = Array.from(initialEnrolledStudentIds);
        const finalIdSet = new Set(finalIds);

        const hasChanged =
            finalIds.length !== initialIdsArray.length ||
            initialIdsArray.some(id => !finalIdSet.has(id));

        if (!hasChanged) {
            return;
        }

        if (finalIds.length === 0) {
            if (initialIdsArray.length === 0) {
                return;
            }
            await apiRequest(`/courses/${courseId}/users/remove`, {
                method: 'POST',
                body: JSON.stringify({
                    user_ids: initialIdsArray
                })
            });
            initialEnrolledStudentIds = new Set();
            if (!courseData) {
                courseData = {};
            }
            courseData.users = [];
            return;
        }

        await apiRequest(`/courses/${courseId}/users/add`, {
            method: 'POST',
            body: JSON.stringify({
                user_ids: finalIds
            })
        });

        initialEnrolledStudentIds = new Set(finalIds);
        if (!courseData) {
            courseData = {};
        }
        courseData.users = enrolledStudentsList.map(student => ({ ...student }));
    }
</script>
@endsection

