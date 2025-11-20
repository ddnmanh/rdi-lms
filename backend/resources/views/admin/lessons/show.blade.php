@extends('admin.layout')

@section('title', 'Chi tiết bài học')
@section('description', 'Xem thông tin chi tiết của bài học')

@section('content')
<div class="min-h-full flex flex-col gap-4 2xl:gap-6">
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
                    <a id="editButton" href="#"
                       class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z"/>
                        </svg>
                        <span>Chỉnh sửa</span>
                    </a>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 p-6 sm:p-8">
        <div class="w-fit mx-auto mt-[20dvh]">
            <div id="SPINNER_LOADING">
                <div id="SPINNER_LOADING_CONTAINER">
                    <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                        <div></div><div></div><div></div><div></div>
                        <div></div><div></div><div></div><div></div>
                    </div>
                </div>
                <div id="SPINNER_LOADING_ICON">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Lesson Detail Card (Flat) --}}
    <div id="lessonDetailCard" class="hidden w-full max-w-[1400px] mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

        <div class="flex items-center justify-between mb-6">
            <div></div>
            <a id="editButton" href="#"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z"/>
                </svg>
                <span>Chỉnh sửa</span>
            </a>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Avatar Section --}}
                <div class="md:col-span-2 flex flex-row items-stretch gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-2">Thumbnail</label>
                        <div class="px-4 py-3 flex items-center gap-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                            <div class="w-full aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <img id="userAvatar" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                                <svg id="avatarPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                {{-- <div class="text-sm text-gray-600 dark:text-gray-300">Thumbnail của người dùng</div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Video</label>
                        <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                            <div id="videoContainer" class="w-full">
                                <video id="lessonVideo" controls class="w-full aspect-video rounded-lg" style="display: none;">
                                    <source id="videoSource" src="" type="video/mp4">
                                    Trình duyệt của bạn không hỗ trợ video.
                                </video>
                                <a id="videoLink" href="#" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline" style="display: none;">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    <span id="videoLinkText">Mở video trong tab mới</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $infoField = function($label, $id) {
                        return <<<HTML
                        <div>
                            <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                            <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <p class="{$id} text-sm text-gray-900 dark:text-gray-100 break-all">-</p>
                            </div>
                        </div>
                        HTML;
                    };
                @endphp

                {!! $infoField('ID', 'lessonId') !!}
                {!! $infoField('Tiêu đề', 'lessonTitle') !!}
                {!! $infoField('Khóa học', 'lessonCourse') !!}
                {!! $infoField('Thời lượng', 'lessonDuration') !!}
                {!! $infoField('Thứ tự hiển thị', 'lessonDisplayOrder') !!}
                {!! $infoField('Video Path', 'lessonVideoUrl') !!}
                {!! $infoField('Ngày tạo', 'lessonCreatedAt') !!}
                {!! $infoField('Ngày cập nhật', 'lessonUpdatedAt') !!}
            </div>

            {{-- Description --}}
            <div class="">
                <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <p class="lessonDescription text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap break-words">-</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Error State --}}
    <div id="errorState" class="hidden flex-1 items-center justify-center min-h-[520px] mt-[20dvh] mx-auto">
        <div class="flex flex-col items-center text-center max-w-md px-6">
            <div class="relative mb-6">
                <div class="h-20 w-20 rounded-full bg-red-600 grid place-items-center">
                    <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Xảy ra lỗi khi lấy thông tin</h3>
            <p id="errorMessage" class="errorMessage text-sm text-gray-600 dark:text-gray-400 mb-6">-</p>
            <a href="{{ route('admin.lessons.list') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    const lessonId = {{ $lessonId }};

    document.addEventListener('DOMContentLoaded', async () => {
        await loadLessonData();
    });

    async function loadLessonData() {
        try {
            const data = await apiRequest(`/lessons/${lessonId}`);
            if (data?.success) {
                displayLessonData(data.data);
            } else {
                showError(data?.message || 'Không thể tải thông tin bài học');
            }
        } catch (error) {
            showError(error.message || 'Đã xảy ra lỗi khi tải thông tin');
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('lessonDetailCard').classList.toggle('hidden', !detail);
        document.getElementById('errorState').classList.toggle('hidden', !error);
    }

    function displayLessonData(lesson) {
        toggleStates({ loading: false, detail: true, error: false });

        document.getElementById('editButton').href = `/admin/lessons/${lesson.id}/edit`;

        // Set avatar preview
        const avatarEl = document.getElementById('userAvatar');
        const placeholderEl = document.getElementById('avatarPlaceholder');
        if (lesson.thumbnail_path) {
            avatarEl.src = lesson.thumbnail_path;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
        } else {
            const name = lesson.title || 'Bài học';
            avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
        }

        setText('lessonId', lesson.id);
        setText('lessonTitle', lesson.title || 'Chưa có tiêu đề');
        setText('lessonDescription', lesson.description || 'Chưa có mô tả');
        setText('lessonDisplayOrder', lesson.display_order || 0);
        setDate('lessonCreatedAt', lesson.created_at);
        setDate('lessonUpdatedAt', lesson.updated_at);

        // Course information
        if (lesson.course) {
            setText('lessonCourse', lesson.course.title || 'N/A');
        } else {
            setText('lessonCourse', '-');
        }

        // Duration formatting
        const duration = lesson.duration || 0;
        const hours = Math.floor(duration / 3600);
        const minutes = Math.floor((duration % 3600) / 60);
        const seconds = duration % 60;
        let durationText = '';
        if (hours > 0) {
            durationText += `${hours} giờ `;
        }
        if (minutes > 0) {
            durationText += `${minutes} phút `;
        }
        if (seconds > 0 || durationText === '') {
            durationText += `${seconds} giây`;
        }
        setText('lessonDuration', durationText.trim() || '0 giây');

        // // Video Path
        const videoUrl = lesson.video_path || '';
        setText('lessonVideoUrl', videoUrl || 'Chưa có video');

        // Video preview
        const videoContainer = document.getElementById('videoContainer');
        const videoElement = document.getElementById('lessonVideo');
        const videoSource = document.getElementById('videoSource');
        const videoLink = document.getElementById('videoLink');
        

        if (videoUrl) {
            // Check if it's a direct video file
            const videoExtensions = ['.mp4', '.webm', '.ogg', '.mov', '.avi'];
            const isVideoFile = videoExtensions.some(ext => videoUrl.toLowerCase().includes(ext));

            if (isVideoFile) {
                videoSource.src = videoUrl;
                videoSource.type = getVideoMimeType(videoUrl);
                videoElement.load();
                videoElement.style.display = 'block';
                videoLink.style.display = 'none';
            } else {
                // External video link (YouTube, Vimeo, etc.)
                videoElement.style.display = 'none';
                videoLink.href = videoUrl;
                document.getElementById('videoLinkText').textContent = videoUrl;
                videoLink.style.display = 'inline-flex';
            }
        } else {
            videoElement.style.display = 'none';
            videoLink.style.display = 'none';
        }
    }

    function showError(message) {
        toggleStates({ loading: false, detail: false, error: true });
        setText('errorMessage', message || 'Xảy ra lỗi khi lấy thông tin');
    }

    function setText(id, value) {
        const el = document.getElementsByClassName(id);
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = value != null && value !== '' ? value : '-';
        }
    }

    function setDate(id, raw, opts = {}) {
        const el = document.getElementsByClassName(id);
        if (!el && el.length <1) return;
        if (!raw) { el.textContent = '-'; return; }
        const dt = new Date(raw);
        let data = '-'
        if (Number.isNaN(dt.getTime())) { data = '-'; return; }
        data = opts.dateOnly
            ? dt.toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' })
            : dt.toLocaleString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = data;
        }
    }

    function escapeHtml_Global(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

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

