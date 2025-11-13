@extends('admin.layout')

@section('title', $mode === 'create' ? 'Thêm bài học' : 'Chỉnh sửa bài học')

@section('description', $mode === 'create' ? 'Thêm bài học mới vào hệ thống' : 'Chỉnh sửa thông tin bài học')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    {{-- <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.lessons.list') }}"
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
    <form id="lessonForm" onsubmit="saveLesson(event)" class="w-full max-w-[1400px] mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="lessonId" value="{{ $mode === 'edit' ? ($lessonId ?? '') : '' }}">
        <input type="hidden" id="path_thumbnail" value="">
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
                        <svg id="thumbnailPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
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

            {{-- Video Upload Section --}}
            <div class="flex flex-col gap-0.5">
                <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                    Video <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2 mb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_type" value="url" id="video_type_url" checked class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">URL Video</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_type" value="file" id="video_type_file" class="w-4 h-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Upload File</span>
                    </label>
                </div>

                {{-- Video URL Input --}}
                <div id="videoUrlSection" class="flex flex-col gap-2">
                    <input type="url" id="video_url" placeholder="https://example.com/video.mp4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 ml-3">Đường dẫn đến video bài học</p>
                </div>

                {{-- Video File Upload --}}
                <div id="videoFileSection" class="hidden">
                    <div
                        id="videoDropZone"
                        class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div class="w-[150px] aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <div class="text-sm text-gray-600 dark:text-gray-300">Kéo & thả video vào đây, hoặc</div>
                            <div class="mt-2 flex items-center gap-3">
                                <label for="video_file" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 text-sm">
                                    Chọn video
                                </label>
                                <button
                                    id="btnClearNewVideo"
                                    type="button"
                                    class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700 text-sm"
                                >
                                    Xóa video mới
                                </button>
                                <input
                                    id="video_file"
                                    type="file"
                                    accept="video/*"
                                    class="hidden"
                                />
                            </div>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Hỗ trợ MP4, AVI, MOV, WEBM — Tối đa 500MB</div>
                            <div id="videoFileName" class="mt-2 text-sm text-gray-700 dark:text-gray-300 hidden"></div>
                        </div>
                    </div>
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
                <div class="flex flex-col gap-0.5">
                    <label for="duration" class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">
                        Thời lượng (giây) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="duration" min="1" required
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
            <a href="{{ route('admin.lessons.list') }}"
                class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                Hủy
            </a>
            <button type="submit"
                class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Lưu</span>
            </button>
        </div>
    </form>

</div>


<script>
    const mode = '{{ $mode }}';
    const lessonId = @if($mode === 'edit' && isset($lessonId)) {{ $lessonId }} @else null @endif;
    let existingThumbnail = null;
    let thumbnailPreview = null;
    let videoFile = null;
    let isThumbnailDragActive = false;
    let isVideoDragActive = false;

    document.addEventListener('DOMContentLoaded', async function() {
        await loadCourses();
        if (mode === 'edit' && lessonId) {
            await loadLessonData(lessonId);
        }
        initThumbnailPreviewForm();
        initVideoUploadForm();
    });

    async function loadCourses() {
        try {
            const data = await apiRequest('/courses?per_page=100');
            if (data.success) {
                const courses = data.data.data || [];
                const courseSelect = document.getElementById('course_id');

                // Clear existing options except first one
                courseSelect.innerHTML = '<option value="">Chọn khóa học</option>';

                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.title;
                    courseSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            showNotificationModel('Không thể tải danh sách khóa học: ' + error.message, 'error');
        }
    }

    async function loadLessonData(id) {
        try {
            const data = await apiRequest(`/lessons/${id}`);
            if (data.success) {
                const lesson = data.data;
                document.getElementById('course_id').value = lesson.course_id || '';
                document.getElementById('title').value = lesson.title || '';
                document.getElementById('description').value = lesson.description || '';
                document.getElementById('duration').value = lesson.duration || '';
                document.getElementById('display_order').value = lesson.display_order || 0;
                document.getElementById('path_thumbnail').value = lesson.thumbnail || '';

                // Set thumbnail preview
                const thumbnailEl = document.getElementById('thumbnailPreview');
                const thumbnailPlaceholderEl = document.getElementById('thumbnailPlaceholder');
                if (lesson.thumbnail) {
                    thumbnailEl.src = lesson.thumbnail;
                    thumbnailEl.classList.remove('hidden');
                    thumbnailPlaceholderEl.classList.add('hidden');
                    existingThumbnail = lesson.thumbnail;
                }

                // Set video - check if it's a URL or file path
                if (lesson.video_url) {
                    // Check if it's a URL (starts with http:// or https://)
                    if (lesson.video_url.startsWith('http://') || lesson.video_url.startsWith('https://')) {
                        document.getElementById('video_type_url').checked = true;
                        document.getElementById('video_url').value = lesson.video_url;
                        document.getElementById('videoUrlSection').classList.remove('hidden');
                        document.getElementById('videoFileSection').classList.add('hidden');
                    } else {
                        // It's a file path, show in file section
                        document.getElementById('video_type_file').checked = true;
                        document.getElementById('videoUrlSection').classList.add('hidden');
                        document.getElementById('videoFileSection').classList.remove('hidden');
                        document.getElementById('videoFileName').textContent = lesson.video_url.split('/').pop();
                        document.getElementById('videoFileName').classList.remove('hidden');
                    }
                }
            }
        } catch (error) {
            showNotificationModel('Không thể tải thông tin bài học: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.lessons.list') }}';
            }, 2000);
        }
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
            showNotificationModel('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            showNotificationModel('Ảnh quá lớn. Kích thước tối đa 2MB.', 'error');
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

    function initThumbnailPreviewForm() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const input = document.getElementById('thumbnail');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        // Default preview for create mode
        if (!existingThumbnail && mode === 'create') {
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailPlaceholder');
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

    function handleVideoFile(file) {
        const MAX_SIZE = 500 * 1024 * 1024; // 500MB
        const ALLOWED_TYPES = ['video/mp4', 'video/avi', 'video/quicktime', 'video/webm', 'video/x-msvideo'];

        if (!file) {
            const clearBtn = document.getElementById('btnClearNewVideo');
            const fileNameEl = document.getElementById('videoFileName');
            const input = document.getElementById('video_file');
            clearBtn.classList.add('hidden');
            fileNameEl.classList.add('hidden');
            if (input) input.value = '';
            videoFile = null;
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            showNotificationModel('Định dạng không hỗ trợ. Hãy chọn video MP4, AVI, MOV hoặc WEBM.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            showNotificationModel('Video quá lớn. Kích thước tối đa 500MB.', 'error');
            return;
        }

        videoFile = file;
        const clearBtn = document.getElementById('btnClearNewVideo');
        const fileNameEl = document.getElementById('videoFileName');
        clearBtn.classList.remove('hidden');
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
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

        // Radio button handlers
        if (videoTypeUrl) {
            videoTypeUrl.addEventListener('change', () => {
                if (videoTypeUrl.checked) {
                    videoUrlSection.classList.remove('hidden');
                    videoFileSection.classList.add('hidden');
                    videoUrlInput.required = true;
                    if (input) input.required = false;
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
                    handleVideoFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleVideoFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleVideoFile(null);
            });
        }
    }

    async function saveLesson(event) {
        event.preventDefault();
        const lessonIdValue = document.getElementById('lessonId').value;
        // const courseId = document.getElementById('course_id').value;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const duration = document.getElementById('duration').value;
        const displayOrder = document.getElementById('display_order').value || 0;
        const pathThumbnail = document.getElementById('path_thumbnail').value || null;
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailFile = thumbnailInput && thumbnailInput.files && thumbnailInput.files[0] ? thumbnailInput.files[0] : null;
        const videoTypeUrl = document.getElementById('video_type_url').checked;
        const videoUrl = videoTypeUrl ? document.getElementById('video_url').value.trim() : '';
        const videoInput = document.getElementById('video_file');
        const videoFile = videoInput && videoInput.files && videoInput.files[0] ? videoInput.files[0] : null;

        // Validation
        // if (!courseId) {
        //     showNotificationModel('Vui lòng chọn khóa học', 'error');
        //     return;
        // }

        if (!title) {
            showNotificationModel('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

        if (!duration || parseInt(duration) < 1) {
            showNotificationModel('Vui lòng nhập thời lượng hợp lệ (ít nhất 1 giây)', 'error');
            return;
        }

        if (videoTypeUrl && !videoUrl) {
            showNotificationModel('Vui lòng nhập URL video', 'error');
            return;
        }

        if (!videoTypeUrl && !videoFile) {
            showNotificationModel('Vui lòng chọn file video', 'error');
            return;
        }

        const isEdit = Boolean(lessonIdValue);
        const url = isEdit ? `/lessons/${lessonIdValue}` : '/lessons';

        try {
            let data;
            // Use multipart/form-data when file is selected or existing thumbnail
            if (thumbnailFile || videoFile || existingThumbnail) {
                const fd = new FormData();
                // Laravel/Symfony không parse multipart cho PUT/PATCH -> dùng POST + _method
                if (isEdit) {
                    fd.append('_method', 'PUT');
                }
                // fd.append('course_id', parseInt(courseId) ? parseInt(courseId) : null);
                fd.append('title', title);
                if (description) fd.append('description', description);
                fd.append('duration', parseInt(duration));
                fd.append('display_order', parseInt(displayOrder) || 0);

                if (existingThumbnail) fd.append('existingThumbnail', existingThumbnail);
                if (pathThumbnail) fd.append('path_thumbnail', pathThumbnail);
                if (thumbnailFile) fd.append('thumbnail', thumbnailFile);

                if (videoTypeUrl) {
                    fd.append('video_url', videoUrl);
                } else if (videoFile) {
                    fd.append('video', videoFile);
                }

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
            } else {
                // Fallback to JSON request when no file selected
                const payload = {
                    // course_id: parseInt(courseId),
                    title: title,
                    description: description || null,
                    duration: parseInt(duration),
                    display_order: parseInt(displayOrder) || 0,
                };
                if (videoTypeUrl) {
                    payload.video_url = videoUrl;
                }

                data = await apiRequest(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    body: JSON.stringify(payload)
                });
            }

            if (data.success) {
                showNotificationModel(data.message || 'Lưu thành công', 'success', handleBackToPrevPage);
            }
        } catch (error) {
            showNotificationModel(error.message, 'error', handleBackToPrevPage);
        }
    }

    function handleBackToPrevPage() {
        window.location.href = '/admin/lessons';
    }

    function closeAlertModal() {
        const alertModal = document.getElementById('alertModal');
        alertModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const alertModal = document.getElementById('alertModal');
            if (alertModal && alertModal.style.display !== 'none') {
                closeAlertModal();
            }
        }
    });
</script>
@endsection

