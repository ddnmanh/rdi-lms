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
                        class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div id="videoPreviewContainer" class="flex flex-col gap-2">
                            <div class="relative w-[150px] aspect-video bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden flex items-center justify-center">
                                <video id="videoFilePreview" controls class="hidden w-full h-full object-cover bg-black"></video>
                                <iframe id="videoUrlPreview" class="hidden w-full h-full" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                                <i id="videoIconPlaceholder" class="fa-solid fa-video text-[20px] text-gray-400 dark:text-gray-400"></i>
                            </div>
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
                                    name="video_file"
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
                <div class="flex flex-col gap-0.5">
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
    const lessonId = @if($mode === 'EDIT' && isset($lessonId)) {{ $lessonId }} @else null @endif;
    let lessonData = null;
    let thumbnailPreview = null;
    let videoFile = null;
    let existingVideoSource = null;
    let existingVideoIsExternal = false;
    let videoPreviewObjectUrl = null;
    let isThumbnailDragActive = false;
    let isVideoDragActive = false;

    document.addEventListener('DOMContentLoaded', async function() {
        if (mode === 'EDIT' && lessonId) {
            lessonData = await loadLessonData(lessonId);
            if (lessonData) {
                await renderLesson(lessonData);
            }
        }
        initThumbnailPreviewForm();
        initVideoUploadForm();
    });

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
            showNotificationModel_Global('Ảnh quá lớn. Kích thước tối đa 2MB.', 'error');
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

    function initThumbnailPreviewForm() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const input = document.getElementById('thumbnail');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        // Default preview for create mode
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
        const videoTypeUrlRadio = document.getElementById('video_type_url');

        if (!file) {
            const clearBtn = document.getElementById('btnClearNewVideo');
            const fileNameEl = document.getElementById('videoFileName');
            const input = document.getElementById('video_file');
            clearBtn.classList.add('hidden');
            fileNameEl.classList.add('hidden');
            if (input) input.value = '';
            videoFile = null;
            if (!videoTypeUrlRadio || !videoTypeUrlRadio.checked) {
                if (existingVideoSource && !existingVideoIsExternal) {
                    showVideoFilePreview(existingVideoSource);
                } else {
                    resetVideoPreview();
                }
            }
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            showNotificationModel_Global('Định dạng không hỗ trợ. Hãy chọn video MP4, AVI, MOV hoặc WEBM.', 'error');
            return;
        }

        // if (file.size > MAX_SIZE) {
        //     showNotificationModel_Global('Video quá lớn. Kích thước tối đa 500MB.', 'error');
        //     return;
        // }

        videoFile = file;
        const clearBtn = document.getElementById('btnClearNewVideo');
        const fileNameEl = document.getElementById('videoFileName');
        clearBtn.classList.remove('hidden');
        fileNameEl.textContent = file.name;
        fileNameEl.classList.remove('hidden');
        showVideoFilePreview(file, true);
    }

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

    function isDirectVideoUrl(url) {
        return /\.(mp4|mov|webm|ogg|m3u8)(\?.*)?$/i.test(url);
    }

    function getVideoPreviewElements() {
        return {
            container: document.getElementById('videoPreviewContainer'),
            videoEl: document.getElementById('videoFilePreview'),
            iframeEl: document.getElementById('videoUrlPreview'),
            placeholder: document.getElementById('videoIconPlaceholder')
        };
    }

    function resetVideoPreview() {
        const { container, videoEl, iframeEl, placeholder } = getVideoPreviewElements();
        if (!container || !videoEl || !iframeEl || !placeholder) return;

        if (videoPreviewObjectUrl) {
            try {
                URL.revokeObjectURL(videoPreviewObjectUrl);
            } catch (e) {
                // noop
            } finally {
                videoPreviewObjectUrl = null;
            }
        }

        videoEl.pause();
        videoEl.removeAttribute('src');
        videoEl.load();
        iframeEl.src = '';

        videoEl.classList.add('hidden');
        iframeEl.classList.add('hidden');
        placeholder.classList.remove('hidden');
        // container.classList.add('hidden');
    }

    function showVideoFilePreview(source, isBlobSource = false) {
        const { container, videoEl, iframeEl, placeholder } = getVideoPreviewElements();
        if (!container || !videoEl || !iframeEl || !placeholder) return;

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

        // container.classList.remove('hidden');
        placeholder.classList.add('hidden');
        iframeEl.classList.add('hidden');
        videoEl.classList.remove('hidden');
        videoEl.src = finalSrc;
        videoEl.load();
    }

    function showVideoUrlPreview(url) {
        const { container, videoEl, iframeEl, placeholder } = getVideoPreviewElements();
        if (!container || !videoEl || !iframeEl || !placeholder) return;

        if (videoPreviewObjectUrl) {
            try {
                URL.revokeObjectURL(videoPreviewObjectUrl);
            } catch (e) {
                // noop
            } finally {
                videoPreviewObjectUrl = null;
            }
        }

        // container.classList.remove('hidden');
        placeholder.classList.add('hidden');
        videoEl.classList.add('hidden');
        iframeEl.classList.remove('hidden');
        iframeEl.src = url;
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

        if (isDirectVideoUrl(url) || (!url.startsWith('http://') && !url.startsWith('https://'))) {
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

        if (videoUrlInput) {
            videoUrlInput.addEventListener('input', () => handleVideoUrlInput());
            videoUrlInput.addEventListener('change', () => handleVideoUrlInput());
            videoUrlInput.addEventListener('blur', () => handleVideoUrlInput());
        }
    }

    async function saveLesson(event) {
        event.preventDefault();
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
        const videoFile = videoInput && videoInput.files && videoInput.files[0] ? videoInput.files[0] : null;

        if (!title) {
            showNotificationModel_Global('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

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
                fd.append('video_path', document.getElementById('video_url')?.value?.trim());
            } else {
                fd.append('video_path', lessonData?.video_path);
                fd.append('video_file', videoFile);
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
                showNotificationModel_Global(data.message || 'Lưu thành công', 'success', handleBackToPrevPage);
            }
        } catch (error) {
            showNotificationModel_Global(error.message, 'error', handleBackToPrevPage);
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

