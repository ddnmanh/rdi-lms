<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Avatar Section --}}
        <div class="md:col-span-2 flex flex-row items-stretch gap-4">
            <div class="flex-1">
                <label class="block font-semibold text-blue-700 dark:text-gray-300 mb-2">Thumbnail</label>
                <div class="px-4 py-3 flex items-center gap-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                    <div class="w-full aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                        <img id="userAvatar" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                        <svg id="avatarPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="flex-1">
                <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Video</label>
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
                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                    <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <p class="{$id} text-gray-900 dark:text-gray-100 break-all">-</p>
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
        <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
        <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <p class="lessonDescription text-gray-900 dark:text-gray-100 whitespace-pre-wrap break-words">-</p>
        </div>
    </div>
</div>

<script>
    // ========================================
    // LESSON INFO TAB - HELPER FUNCTIONS
    // ========================================

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

    // ========================================
    // LESSON INFO TAB - DISPLAY FUNCTION
    // ========================================

    /**
     * Hiển thị thông tin lesson trong tab lesson info
     * Hàm này sẽ được gọi từ show.blade.php sau khi load lesson data
     */
    async function displayLessonInfoData(lessonData) {
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
</script>
