@extends('admin.layout')

@section('title', 'Chi tiết bài học')
@section('description', 'Xem thông tin chi tiết của bài học')

@section('content')
<div class="w-full max-w-[1600px] mx-auto flex flex-col gap-4 3xl:gap-6">

    {{-- Header Bar --}}
    <div class="p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

        <div class="flex items-center justify-start gap-3">
            <button onclick="handleGotoBackPage_Global()"
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

        <div class="flex items-center justify-start gap-3">
            <button
                onclick="handleGotoOtherPageOfLesson('{{ $lessonId }}', 'EDIT')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M16 512A32 32 0 0 1 0 480V144A16 16 0 0 1 16 128h80a16 16 0 0 1 16 16v320A16 16 0 0 1 96 512zm352-48a48 48 0 0 1-48 48H80a48 48 0 0 1-48-48V112A48 48 0 0 1 80 64h352a48 48 0 0 1 48 48zM400 0H304a16 16 0 0 0-16 16v320a16 16 0 0 0 16 16h96a16 16 0 0 0 16-16V16a16 16 0 0 0-16-16z"/>
                </svg>
                <span>Chỉnh sửa</span>
            </button>
            <button
                id="deleteLessonButton"
                onclick="openSingleDeleteModal()"
                disabled
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-20 cursor-not-allowed">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                </svg>
                <span>Xóa bài học này</span>
            </button>
        </div>
    </div>

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
                    <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Lesson Detail Card (Flat) --}}
    <div id="lessonDetailCard" class="hidden w-full mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Avatar Section --}}
                <div class="md:col-span-2 flex flex-row items-stretch gap-4">
                    <div class="flex-1">
                        <label class="block  font-semibold text-blue-700 dark:text-gray-300 mb-2">Thumbnail</label>
                        <div class="px-4 py-3 flex items-center gap-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                            <div class="w-full aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                <img id="userAvatar" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                                <svg id="avatarPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                {{-- <div class=" text-gray-600 dark:text-gray-300">Thumbnail của người dùng</div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="flex-1">
                        <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Video</label>
                        <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                            <div id="videoContainer" class="w-full">
                                <video id="lessonVideo" controls class="w-full aspect-video rounded-lg" style="display: none;">
                                    <source id="videoSource" src="" type="video/mp4">
                                    Trình duyệt của bạn không hỗ trợ video.
                                </video>
                                <a id="videoLink" href="#" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline" style="display: none;">
                                    <span class="mr-2">
                                        <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M320 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l82.7 0-201.4 201.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L448 109.3 448 192c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160c0-17.7-14.3-32-32-32L320 0zM80 96C35.8 96 0 131.8 0 176L0 432c0 44.2 35.8 80 80 80l256 0c44.2 0 80-35.8 80-80l0-80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 80c0 8.8-7.2 16-16 16L80 448c-8.8 0-16-7.2-16-16l0-256c0-8.8 7.2-16 16-16l80 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 96z"/>
                                        </svg>
                                    </span>
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
                            <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                            <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <p class="{$id}  text-gray-900 dark:text-gray-100 break-all">-</p>
                            </div>
                        </div>
                        HTML;
                    };
                @endphp

                {!! $infoField('ID', 'lessonId') !!}
                {!! $infoField('Khóa học', 'lessonCourse') !!}
                {!! $infoField('Tiêu đề', 'lessonTitle') !!}
                {!! $infoField('Thời lượng', 'lessonDuration') !!}
                {!! $infoField('Thứ tự học', 'lessonDisplayOrder') !!}
                {!! $infoField('Video Path', 'lessonVideoUrl') !!}
                {!! $infoField('Ngày tạo', 'lessonCreatedAt') !!}
                {!! $infoField('Ngày cập nhật', 'lessonUpdatedAt') !!}
            </div>

            <div class="">
                <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <p class="lessonDescription  text-gray-900 dark:text-gray-100 whitespace-pre-wrap break-words">-</p>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    const lessonId = {{ $lessonId }};
    let lessonData = null;

    const detailLessonRouteSystemName = '{{ route('admin.lessons.show', ['id' => ':id']) }}';
    const editLessonRouteSystemName = '{{ route('admin.lessons.edit', ['id' => ':id']) }}';
    const createLessonRouteSystemName = '{{ route('admin.lessons.create') }}';

    document.addEventListener('DOMContentLoaded', async () => {
        lessonData = await loadLessonData();
        if (lessonData != null) {
            displayLessonData();
        } else {
            NotificationModal.show('Không thể tải thông tin bài học', 'error', handleGotoBackPage_Global);
        }
    });

    async function loadLessonData() {
        try {
            const data = await apiRequest(`/lessons/${lessonId}`);
            if (data?.success) {
                return data.data;
            } else {
                return null;
            }
        } catch (error) {
            return null;
        }
    }

    function toggleStates({ loading = false, detail = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('lessonDetailCard').classList.toggle('hidden', !detail);
    }

    async function displayLessonData() {
        toggleStates({ loading: false, detail: true });

        document.getElementById('deleteLessonButton').disabled = lessonData.course_id !== null;
        document.getElementById('deleteLessonButton').classList.toggle('opacity-20', lessonData.course_id !== null);
        document.getElementById('deleteLessonButton').classList.toggle('cursor-not-allowed', lessonData.course_id !== null);

        // Set avatar preview
        const avatarEl = document.getElementById('userAvatar');
        const placeholderEl = document.getElementById('avatarPlaceholder');
        if (lessonData.thumbnail_path) {
            avatarEl.src = lessonData.thumbnail_path;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
        }

        setText('lessonId', lessonData.id);
        setText('lessonTitle', lessonData.title || '-');
        setText('lessonDescription', lessonData.description || '-');
        setText('lessonDisplayOrder', lessonData.display_order || 0);
        setDate('lessonCreatedAt', lessonData.created_at);
        setDate('lessonUpdatedAt', lessonData.updated_at);

        // Course information
        if (lessonData.course) {
            setText('lessonCourse', lessonData.course.title || 'N/A');
        } else {
            setText('lessonCourse', '-');
        }

        setText('lessonDuration', formatSecondsToHHMMSS_Global(lessonData.duration || 0, true) || '0 giây');

        setText('lessonVideoUrl', lessonData.video_path || 'Chưa có video');

        // Video preview
        const videoContainer = document.getElementById('videoContainer');
        const videoElement = document.getElementById('lessonVideo');
        const videoSource = document.getElementById('videoSource');
        const videoLink = document.getElementById('videoLink');

        // Hiển thị video, ưu tiên HLS nếu có
        const videoPath = lessonData.hls_path || lessonData.video_path;
        const isBackgroundUpload = lessonData.video_path?.startsWith('background-upload://');
        if (!videoPath || isBackgroundUpload) {
            videoElement.style.display = 'none';
            videoLink.style.display = 'none';
            return;
        }
        const endpoint = lessonData.hls_path
            ? `/lessons/${lessonData.id}/hls-signature`
            : `/lessons/${lessonData.id}/mp4-signature`;

        let data = await apiRequest(endpoint);
        if (!data?.success && lessonData.hls_path) {
            data = await apiRequest(`/lessons/${lessonData.id}/mp4-signature`);
        }
        if (data?.success && data?.data?.signed_uri) {
            videoSource.src = data.data.base_url + data.data.signed_uri;
            // Set đúng MIME type cho HLS hoặc MP4
            if (lessonData.hls_path) {
                videoSource.type = 'application/x-mpegURL';
            } else {
                videoSource.type = getVideoMimeType(lessonData.video_path);
            }
            videoElement.load();
            videoElement.style.display = 'block';
            videoLink.style.display = 'none';
        } else {
            videoElement.style.display = 'none';
            videoLink.style.display = 'none';
        }

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

    // Di chuyển đến trang khác của bài học, đồng thời gửi kèm url hiện tại
    function handleGotoOtherPageOfLesson(lessonId = null, targetPage = 'DETAIL') {
        let url = '';

        switch (targetPage) {
            case 'DETAIL':
                url = detailLessonRouteSystemName.replace(':id', lessonId);
                break;
            case 'EDIT':
                url = editLessonRouteSystemName.replace(':id', lessonId);
                break;
            case 'CREATE':
                url = createLessonRouteSystemName;
                break;
            default:
                url = detailLessonRouteSystemName.replace(':id', lessonId);
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }

    // Xử lý khi xóa một mục
    async function openSingleDeleteModal(lessonId = lessonData.id || null, lessonTitle = lessonData.title || '-', courseName = lessonData.course?.title || '') {
        DeleteModal.openSingle({
            objectName: OBJECTNAMEMODAL.LESSON,
            idDelete: lessonId,
            nameValue: lessonTitle || '-',
            descValue: courseName || '',
            actionFuncCallback: () => handleDeleteLessons([lessonId]),
            successFuncCallback: () => NotificationModal.show('Đã xóa bài học thành công', 'success', handleGotoBackPage_Global),
            failFuncCallback: () => NotificationModal.show('Không thể xóa bài học', 'error')
        });
    }

    async function handleDeleteLessons(arrayIds = []) {
        try {
            const data = await apiRequest(`/lessons/`, {
                method: 'DELETE',
                body: JSON.stringify({
                    lesson_ids: [...arrayIds]
                })
            });
            if (data.success) {
                return true;
            } else {
                return false;
            }
        } catch (error) {
            return false;
        }
    }
</script>
@endsection

