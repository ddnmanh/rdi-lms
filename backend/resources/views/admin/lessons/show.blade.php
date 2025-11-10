@extends('admin.layout')

@section('title', 'Chi tiết bài học')
@section('description', 'Xem thông tin chi tiết của bài học')

@section('content')
<div class="min-h-full flex flex-col gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
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
    </div>

    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 p-6 sm:p-8">
        <div class="mx-auto max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
                    <div class="mx-auto flex flex-col items-center gap-4">
                        <div class="h-28 w-28 rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-4 w-40 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-3 w-52 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="h-14 rounded-lg bg-gray-100 dark:bg-gray-900 animate-pulse"></div>
                        <div class="h-14 rounded-lg bg-gray-100 dark:bg-gray-900 animate-pulse"></div>
                    </div>
                </div>
                <div class="lg:col-span-2 grid gap-6">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800 h-40 animate-pulse"></div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800 h-40 animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lesson Detail Card (Flat) --}}
    <div id="lessonDetailCard" class="hidden flex-1 w-full max-w-6xl mx-auto flex-col">
        {{-- Lesson Information --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-blue-600 grid place-items-center">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Thông tin bài học</h3>
            </div>

            <div class="p-6 flex flex-col xl:flex-row items-start justify-start gap-5">
                {{-- Summary --}}
                <div class="w-[300px] mx-auto lg:col-span-1">
                    <div class="relative mb-4">
                        <div class="w-full h-48 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="h-20 w-20 text-white opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="w-full flex-1 grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $infoField = function($label, $id, $hint = null) {
                            $hintHtml = $hint ? '<p class="text-[11px] leading-4 text-gray-500 dark:text-gray-400">'.$hint.'</p>' : '';
                            return <<<HTML
                            <div class="space-y-2">
                                <label class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">{$label}</label>
                                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                                    <p id="{$id}" class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">-</p>
                                    {$hintHtml}
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
                    {!! $infoField('Video URL', 'lessonVideoUrl') !!}
                    {!! $infoField('Ngày tạo', 'lessonCreatedAt') !!}
                    {!! $infoField('Ngày cập nhật', 'lessonUpdatedAt') !!}
                </div>
            </div>

            {{-- Description --}}
            <div class="px-6 pb-6">
                <label class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase mb-2">Mô tả</label>
                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                    <p id="lessonDescription" class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap break-words">-</p>
                </div>
            </div>

            {{-- Video Preview --}}
            <div class="px-6 pb-6">
                <label class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase mb-2">Video</label>
                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                    <div id="videoContainer" class="w-full">
                        <video id="lessonVideo" controls class="w-full rounded-lg" style="display: none;">
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
    </div>

    {{-- Error State --}}
    <div id="errorState" class="hidden flex-1 items-center justify-center min-h-[520px]">
        <div class="flex flex-col items-center text-center max-w-md px-6">
            <div class="relative mb-6">
                <div class="h-20 w-20 rounded-full bg-red-600 grid place-items-center">
                    <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Không thể tải thông tin</h3>
            <p id="errorMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-6">-</p>
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
        
        setText('lessonId', lesson.id);
        setText('lessonTitle', lesson.title || 'Chưa có tiêu đề');
        setText('lessonDescription', lesson.description || 'Chưa có mô tả');
        setText('lessonDisplayOrder', lesson.display_order || 0);
        setDate('lessonCreatedAt', lesson.created_at);
        setDate('lessonUpdatedAt', lesson.updated_at);

        // Course information
        if (lesson.course) {
            setText('lessonCourse', `${lesson.course.title || 'N/A'} (ID: ${lesson.course.id || '-'})`);
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

        // Video URL
        const videoUrl = lesson.video_url || '';
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
        setText('errorMessage', message || 'Đã xảy ra lỗi');
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value != null && value !== '' ? value : '-';
    }

    function setDate(id, raw, opts = {}) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!raw) { el.textContent = '-'; return; }
        const dt = new Date(raw);
        if (Number.isNaN(dt.getTime())) { el.textContent = '-'; return; }
        el.textContent = opts.dateOnly
            ? dt.toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' })
            : dt.toLocaleString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
</script>
@endsection

