@extends('admin.layout')

@section('title', 'Xem trước Quiz - Bài học')
@section('description', 'Xem quiz như sinh viên sẽ thấy')

@section('content')
<div class="w-full max-w-[1600px] mx-auto h-full mx-auto flex flex-col gap-4 3xl:gap-6">

    {{-- Header Bar --}}
    <div class="sticky top-0 z-10 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

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
                    Xem trước Quiz - Bài học
                </h2>
                <p class="text-xs 3xl:text-sm text-gray-600 dark:text-gray-400 truncate">
                    Mô phỏng trải nghiệm học như sinh viên
                </p>
            </div>
        </div>

        <div class="flex items-center justify-start gap-3">
            <button
                onclick="resetQuizPreview()"
                class="inline-flex items-center gap-2 rounded-lg bg-red-500 px-4 py-2.5 font-semibold text-white hover:bg-red-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M256 64c-56.8 0-107.9 24.7-143.1 64l47.1 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 192c-17.7 0-32-14.3-32-32L0 32C0 14.3 14.3 0 32 0S64 14.3 64 32l0 54.7C110.9 33.6 179.5 0 256 0 397.4 0 512 114.6 512 256S397.4 512 256 512c-87 0-163.9-43.4-210.1-109.7-10.1-14.5-6.6-34.4 7.9-44.6s34.4-6.6 44.6 7.9c34.8 49.8 92.4 82.3 157.6 82.3 106 0 192-86 192-192S362 64 256 64z"/>
                </svg>
                <span>Khôi phục lại</span>
            </button>
        </div>
    </div>

    {{-- Loading State --}}
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

    {{-- Video Player & Quiz Preview Content --}}
    <div id="quizPreviewContent" class="hidden flex-1 min-h-0">
        <div class="flex flex-col gap-4">
        {{-- Video Player --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <div class="w-full aspect-video rounded-lg overflow-hidden bg-black">
                <video
                    id="lessonVideo"
                    controls
                    class="w-full h-full"
                    style="display: none;">
                    <source id="videoSource" src="" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ video.
                </video>
                <div id="videoPlaceholder" class="w-full h-full flex items-center justify-center text-gray-400">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM188.3 147.1c7.6-4.2 16.8-4.1 24.3 .5l144 88c7.1 4.4 11.5 12.1 11.5 20.5s-4.4 16.1-11.5 20.5l-144 88c-7.4 4.5-16.7 4.7-24.3 .5s-12.3-12.2-12.3-20.9V168c0-8.7 4.7-16.7 12.3-20.9z"/>
                        </svg>
                        <p class="text-sm">Chưa có video</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quiz Timeline --}}
        <div id="quizTimelineContainer" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 hidden">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Timeline Quiz</span>
                <span id="videoDurationText" class="text-xs text-gray-500 dark:text-gray-400">0:00</span>
            </div>
            <div class="relative">
                <div id="quizTimelineTrack" class="relative h-8 bg-gray-200 dark:bg-gray-700 rounded-full overflow-visible">
                    {{-- Timeline markers sẽ được render ở đây --}}
                </div>
                <div id="quizTimelineLabels" class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>0:00</span>
                    <span id="videoDurationEndLabel">0:00</span>
                </div>
            </div>
        </div>
        </div>
    </div>

    {{-- Quiz Modal --}}
    <div id="quizModal" class="hidden fixed inset-0 z-50 p-4 bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center h-full">
            <div class="w-[800px] max-h-[70vh] mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-y-auto flex flex-col">
                {{-- Modal Header --}}
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex-1">
                        <h3 id="quizModalTitle" class="text-xl font-bold text-gray-900 dark:text-white"></h3>
                        <p id="quizModalDescription" class="text-sm text-gray-600 dark:text-gray-400 mt-1"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="quizModalRequired" class="px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 rounded-full hidden">
                            Bắt buộc
                        </span>
                        <button
                            id="closeQuizModal"
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Content --}}
                <div id="quizModalContent" class="flex-1 overflow-y-auto p-6">
                    {{-- Quiz form sẽ được render ở đây --}}
                </div>

                {{-- Modal Footer --}}
                <div id="quizModalFooter" class="p-6 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between gap-4">
                    <div id="quizModalInfo" class="text-sm text-gray-500 dark:text-gray-400">
                        <span>Điểm đạt: <span id="quizModalPassingScore" class="font-semibold"></span>%</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            id="submitQuizBtn"
                            class="px-6 py-2.5 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-all">
                            Nộp bài
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quiz Result Modal --}}
    <div id="quizResultModal" class="hidden fixed inset-0 z-50 p-4 bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center h-full">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full p-6">
            <div class="text-center">
                <div id="quizResultIcon" class="mx-auto w-16 h-16 mb-4 rounded-full flex items-center justify-center">
                    {{-- Icon sẽ được render động --}}
                </div>
                <h3 id="quizResultTitle" class="text-2xl font-bold mb-2"></h3>
                <p id="quizResultMessage" class="text-gray-600 dark:text-gray-400 mb-6"></p>
                <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Điểm số</p>
                            <p id="quizResultScore" class="text-2xl font-bold text-gray-900 dark:text-white"></p>
                        </div>
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Điểm đạt</p>
                            <p id="quizResultPassing" class="text-2xl font-bold text-gray-900 dark:text-white"></p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        id="retryQuizBtn"
                        class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-amber-500 text-white rounded-lg font-semibold hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 transition-all">
                        <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 64c-56.8 0-107.9 24.7-143.1 64l47.1 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 192c-17.7 0-32-14.3-32-32L0 32C0 14.3 14.3 0 32 0S64 14.3 64 32l0 54.7C110.9 33.6 179.5 0 256 0 397.4 0 512 114.6 512 256S397.4 512 256 512c-87 0-163.9-43.4-210.1-109.7-10.1-14.5-6.6-34.4 7.9-44.6s34.4-6.6 44.6 7.9c34.8 49.8 92.4 82.3 157.6 82.3 106 0 192-86 192-192S362 64 256 64z"/>
                        </svg>
                        Làm lại bài tập
                    </button>
                    <button
                        id="continueVideoBtn"
                        class="flex-1 px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-all">
                        Tiếp tục xem video
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>

</div>

<script>
    // ========================================
    // GLOBAL VARIABLES
    // ========================================
    const lessonId = {{ $lessonId }}; // ID của lesson đang preview
    let quizzes = []; // Danh sách tất cả quiz của lesson
    let lessonData = null; // Thông tin lesson (title, video_path, duration, ...)
    let videoElement = null; // Reference đến element <video> trong DOM
    let currentQuiz = null; // Quiz đang được hiển thị trong modal
    let quizAnswers = {}; // Lưu đáp án đã chọn: { quizId: { questionId: [optionId1, optionId2, ...] } }
    let quizResults = {}; // Lưu kết quả quiz: { quizId: { passed: boolean, score: number, pointsEarned: number, maxPoints: number } }
    let videoTimeBeforeSeek = 0; // Lưu vị trí video trước khi seek (dùng để revert khi seek bị chặn)
    let lastBlockedMessageTime = 0; // Timestamp lần cuối hiển thị blocked message (để tránh spam)
    let isBlockedMessageShowing = false; // Flag để tránh hiển thị nhiều blocked message cùng lúc
    const STORAGE_KEY = `quiz_preview_${lessonId}`; // Key để lưu quiz answers và results vào localStorage
    const BLOCKED_MESSAGE_COOLDOWN = 2000; // 2 giây cooldown giữa các lần hiển thị blocked message

    // ========================================
    // HELPER FUNCTIONS
    // ========================================
    /**
     * Escape HTML để tránh XSS khi render user input
     * @param {string} str - Chuỗi cần escape
     * @returns {string} - Chuỗi đã được escape
     */
    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    /**
     * Toggle hiển thị loading state và content state
     * @param {Object} options - { loading: boolean, content: boolean }
     */
    function toggleStates({ loading = false, content = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('quizPreviewContent').classList.toggle('hidden', !content);
    }

    /**
     * Xác định MIME type của video dựa vào extension của URL
     * @param {string} url - URL của video
     * @returns {string} - MIME type (mặc định: 'video/mp4')
     */
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
    // LOCAL STORAGE FUNCTIONS
    // ========================================
    /**
     * Load quiz answers và results từ localStorage
     * @returns {boolean} - true nếu load thành công, false nếu không có data hoặc lỗi
     */
    function loadFromStorage() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                const data = JSON.parse(stored);
                quizAnswers = data.answers || {};
                quizResults = data.results || {};
                return true;
            }
        } catch (e) {
            console.error('Error loading from storage:', e);
        }
        return false;
    }

    /**
     * Lưu quiz answers và results vào localStorage
     * Dữ liệu được lưu: { answers: {}, results: {}, timestamp: string }
     */
    function saveToStorage() {
        try {
            const data = {
                answers: quizAnswers,
                results: quizResults,
                timestamp: new Date().toISOString()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (e) {
            console.error('Error saving to storage:', e);
        }
    }

    /**
     * Xóa tất cả dữ liệu quiz từ localStorage và reset biến global
     */
    function clearStorage() {
        localStorage.removeItem(STORAGE_KEY);
        quizAnswers = {};
        quizResults = {};
    }

    // ========================================
    // QUIZ VALIDATION FUNCTIONS
    // ========================================
    /**
     * Kiểm tra xem có quiz nào chưa làm hoặc chưa đạt trước một thời điểm
     * Logic:
     * - Quiz bắt buộc (is_required = true): phải đã làm VÀ đạt điểm (passed = true)
     * - Quiz không bắt buộc: nếu đã làm thì phải đạt điểm, nếu chưa làm thì không chặn
     * @param {number} targetTime - Thời điểm cần kiểm tra (giây)
     * @returns {Object} - { blocked: boolean, quiz?: Object, reason?: 'not_done' | 'not_passed' }
     */
    function hasUncompletedQuizBeforeTime(targetTime) {
        // Lọc các quiz active có start_at_seconds <= targetTime
        const activeQuizzes = quizzes.filter(q =>
            q.is_active &&
            q.start_at_seconds &&
            q.start_at_seconds <= targetTime
        );

        // Kiểm tra từng quiz theo thứ tự thời gian
        for (const quiz of activeQuizzes) {
            const result = quizResults[quiz.id];

            // Quiz bắt buộc: phải đã làm VÀ đạt điểm
            if (quiz.is_required) {
                if (!result || !result.passed) {
                    return {
                        blocked: true,
                        quiz: quiz,
                        reason: result ? 'not_passed' : 'not_done' // Đã làm nhưng chưa đạt / Chưa làm
                    };
                }
            }

            // Quiz không bắt buộc: nếu đã làm thì phải đạt điểm
            // Nếu chưa làm thì không chặn (cho phép bỏ qua)
            if (result && !result.passed) {
                return { blocked: true, quiz: quiz, reason: 'not_passed' };
            }
        }

        return { blocked: false };
    }

    /**
     * Kiểm tra xem có thể seek (tua) video đến vị trí này không
     * @param {number} targetTime - Thời điểm muốn seek đến (giây)
     * @returns {Object} - { blocked: boolean, quiz?: Object, reason?: string }
     */
    function canSeekToTime(targetTime) {
        return hasUncompletedQuizBeforeTime(targetTime);
    }

    /**
     * Kiểm tra xem có thể play video từ vị trí hiện tại không
     * @returns {Object} - { blocked: boolean, quiz?: Object, reason?: string }
     */
    function canPlayFromCurrentTime() {
        if (!videoElement) return true;
        const currentTime = Math.floor(videoElement.currentTime);
        return hasUncompletedQuizBeforeTime(currentTime);
    }

    // ========================================
    // DATA LOADING
    // ========================================
    /**
     * Load thông tin lesson từ API
     * @returns {Promise<Object|null>} - Thông tin lesson hoặc null nếu lỗi
     */
    async function loadLessonData() {
        try {
            const data = await apiRequest(`/lessons/${lessonId}`);
            if (data?.success) {
                return data.data;
            }
            return null;
        } catch (error) {
            console.error('Error loading lesson:', error);
            return null;
        }
    }

    /**
     * Load danh sách quiz của lesson từ API
     * @returns {Promise<Array>} - Mảng các quiz hoặc [] nếu lỗi
     */
    async function loadQuizzes() {
        try {
            const data = await apiRequest(`/lesson-quizzes/lesson/${lessonId}`);
            if (data?.success) {
                return data.data || [];
            }
            return [];
        } catch (error) {
            console.error('Error loading quizzes:', error);
            return [];
        }
    }

    /**
     * Load signed URL của video (HLS hoặc MP4)
     * Ưu tiên HLS, nếu không có thì fallback về MP4
     * @returns {Promise<Object|null>} - { url: string, type: string } hoặc null nếu lỗi
     */
    async function loadVideoUrl() {
        if (!lessonData) return null;

        const videoPath = lessonData.hls_path || lessonData.video_path;
        const isBackgroundUpload = lessonData.video_path?.startsWith('background-upload://');

        // Không load video nếu đang upload background
        if (!videoPath || isBackgroundUpload) {
            return null;
        }

        // Chọn endpoint dựa vào loại video (HLS hoặc MP4)
        const endpoint = lessonData.hls_path
            ? `/lessons/${lessonData.id}/hls-signature`
            : `/lessons/${lessonData.id}/mp4-signature`;

        try {
            let data = await apiRequest(endpoint);
            // Nếu HLS fail, thử MP4
            if (!data?.success && lessonData.hls_path) {
                data = await apiRequest(`/lessons/${lessonData.id}/mp4-signature`);
            }
            if (data?.success && data?.data?.signed_uri) {
                return {
                    url: data.data.base_url + data.data.signed_uri,
                    type: lessonData.hls_path ? 'application/x-mpegURL' : getVideoMimeType(lessonData.video_path)
                };
            }
        } catch (error) {
            console.error('Error loading video URL:', error);
        }
        return null;
    }

    // ========================================
    // VIDEO FUNCTIONS
    // ========================================
    /**
     * Khởi tạo video player: load video URL, setup event listeners, render timeline
     */
    async function setupVideo() {
        videoElement = document.getElementById('lessonVideo');
        const videoSource = document.getElementById('videoSource');
        const videoPlaceholder = document.getElementById('videoPlaceholder');

        if (!videoElement) return;

        // Load signed video URL
        const videoData = await loadVideoUrl();
        if (!videoData) {
            // Không có video, hiển thị placeholder
            videoElement.style.display = 'none';
            videoPlaceholder.style.display = 'flex';
            return;
        }

        // Set video source và load
        videoSource.src = videoData.url;
        videoSource.type = videoData.type;
        videoElement.load();
        videoElement.style.display = 'block';
        videoPlaceholder.style.display = 'none';

        // Setup event listeners và render timeline
        setupVideoListeners();
        renderQuizTimeline();
    }

    /**
     * Setup các event listeners cho video player:
     * - timeupdate: Theo dõi thời gian để hiển thị quiz khi đến start_at_seconds
     * - seeking: Chặn seek nếu vượt qua quiz chưa hoàn thành
     * - seeked: Kiểm tra lại sau khi seek xong
     * - play: Chặn play nếu có quiz chưa hoàn thành trước vị trí hiện tại
     * - loadedmetadata: Load vị trí video đã lưu (nếu có)
     */
    function setupVideoListeners() {
        if (!videoElement) return;

        // Theo dõi thời gian video để hiển thị quiz khi đến start_at_seconds
        videoElement.addEventListener('timeupdate', handleVideoTimeUpdate);

        // Chặn seek nếu seek qua quiz chưa làm/chưa đạt
        videoElement.addEventListener('seeking', handleVideoSeeking);
        videoElement.addEventListener('seeked', handleVideoSeeked);

        // Chặn play nếu có quiz chưa làm/chưa đạt trước vị trí hiện tại
        videoElement.addEventListener('play', handleVideoPlay);

        // Load saved video position từ localStorage (chỉ nếu không bị chặn)
        const savedPosition = localStorage.getItem(`video_position_${lessonId}`);
        if (savedPosition) {
            videoElement.addEventListener('loadedmetadata', () => {
                const targetTime = parseFloat(savedPosition);
                const check = canSeekToTime(targetTime);
                if (!check.blocked) {
                    // Vị trí đã lưu hợp lệ, restore nó
                    videoElement.currentTime = targetTime;
                    videoTimeBeforeSeek = targetTime;
                } else {
                    // Vị trí đã lưu không hợp lệ (có quiz chưa hoàn thành), tìm vị trí an toàn
                    const safeTime = findSafeVideoPosition(targetTime);
                    videoElement.currentTime = safeTime;
                    videoTimeBeforeSeek = safeTime;
                }
            }, { once: true });
        } else {
            // Không có vị trí đã lưu, khởi tạo từ đầu
            videoElement.addEventListener('loadedmetadata', () => {
                videoTimeBeforeSeek = 0;
            }, { once: true });
        }
    }

    // Flags để quản lý trạng thái seeking
    let isSeeking = false; // Đang trong quá trình seek
    let hasShownBlockedMessageDuringSeek = false; // Đã hiển thị blocked message trong lần seek này

    /**
     * Xử lý sự kiện 'seeking' - khi người dùng bắt đầu tua video
     * Logic:
     * 1. Lưu vị trí trước khi seek (chỉ lần đầu)
     * 2. Kiểm tra xem vị trí seek đến có hợp lệ không
     * 3. Nếu không hợp lệ, revert về vị trí an toàn và hiển thị thông báo
     */
    function handleVideoSeeking(e) {
        if (!videoElement) return;

        // Lưu vị trí trước khi seek (chỉ lần đầu tiên khi bắt đầu seek)
        if (!isSeeking) {
            videoTimeBeforeSeek = videoElement.currentTime;
            hasShownBlockedMessageDuringSeek = false; // Reset flag khi bắt đầu seek mới
        }

        isSeeking = true;

        const targetTime = videoElement.currentTime;
        const check = canSeekToTime(targetTime);

        if (check.blocked) {
            // Tìm vị trí hợp lệ gần nhất, ưu tiên vị trí trước khi seek
            const safeTime = findSafeVideoPosition(targetTime, videoTimeBeforeSeek);
            if (videoElement) {
                videoElement.currentTime = safeTime; // Force revert về vị trí an toàn
            }
            // Cập nhật videoTimeBeforeSeek về vị trí hợp lệ
            videoTimeBeforeSeek = safeTime;

            // Chỉ hiển thị thông báo 1 lần trong mỗi lần seek (tránh spam)
            if (!hasShownBlockedMessageDuringSeek) {
                showBlockedMessage(check.quiz, check.reason);
                hasShownBlockedMessageDuringSeek = true;
            }
        }
    }

    /**
     * Xử lý sự kiện 'seeked' - khi người dùng hoàn tất việc tua video
     * Logic:
     * 1. Kiểm tra lại xem vị trí sau khi seek có hợp lệ không
     * 2. Nếu không hợp lệ, revert về vị trí an toàn và pause video
     * 3. Nếu đang ở vị trí quiz chưa hoàn thành, hiển thị quiz modal
     * 4. Nếu hợp lệ, cập nhật videoTimeBeforeSeek
     */
    function handleVideoSeeked() {
        isSeeking = false;
        hasShownBlockedMessageDuringSeek = false; // Reset flag khi seek kết thúc

        if (!videoElement) return;

        const currentTime = videoElement.currentTime;
        const check = canPlayFromCurrentTime();

        if (check.blocked) {
            // Tìm vị trí hợp lệ gần nhất, ưu tiên vị trí trước khi seek
            const safeTime = findSafeVideoPosition(currentTime, videoTimeBeforeSeek);
            videoTimeBeforeSeek = safeTime;

            // Sử dụng setTimeout để đảm bảo video đã hoàn tất seek trước khi force revert
            setTimeout(() => {
                if (videoElement) {
                    // Double check và force revert nếu cần
                    if (Math.abs(videoElement.currentTime - safeTime) > 0.1) {
                        videoElement.currentTime = safeTime;
                    }
                    // Pause video để người dùng không thể tiếp tục
                    if (!videoElement.paused) {
                        videoElement.pause();
                    }
                }
            }, 100);

            // Nếu đang ở vị trí quiz (trong khoảng ±1 giây), hiển thị quiz modal
            const currentTimeFloor = Math.floor(safeTime);
            const activeQuizzes = quizzes.filter(q =>
                q.is_active &&
                q.start_at_seconds &&
                Math.abs(currentTimeFloor - q.start_at_seconds) <= 1
            );
            if (activeQuizzes.length > 0) {
                const quiz = activeQuizzes[0];
                const result = quizResults[quiz.id];
                // Chỉ hiển thị nếu chưa làm hoặc chưa đạt
                if (!result || !result.passed) {
                    showQuizModal(quiz);
                }
            }
        } else {
            // Seek hợp lệ, cập nhật videoTimeBeforeSeek để lần seek sau có reference point
            videoTimeBeforeSeek = currentTime;
        }
    }


    /**
     * Xử lý sự kiện 'play' - khi người dùng nhấn play
     * Chặn play nếu có quiz chưa hoàn thành trước vị trí hiện tại
     */
    function handleVideoPlay(e) {
        const check = canPlayFromCurrentTime();
        if (check.blocked) {
            e.preventDefault();
            videoElement.pause();
            showBlockedMessage(check.quiz, check.reason);
        }
    }

    /**
     * Tìm vị trí video an toàn gần nhất (không có quiz chưa làm/chưa đạt)
     *
     * Logic:
     * 1. Ưu tiên preferredTime (vị trí trước khi seek) nếu nó hợp lệ
     * 2. Nếu không, duyệt các quiz theo thứ tự thời gian để tìm quiz đầu tiên chưa hoàn thành
     * 3. Trả về vị trí an toàn (không vượt quá quiz chưa hoàn thành đầu tiên)
     *
     * @param {number} targetTime - Thời điểm muốn seek đến (giây)
     * @param {number|null} preferredTime - Vị trí ưu tiên (thường là vị trí trước khi seek)
     * @returns {number} - Vị trí an toàn (giây)
     */
    function findSafeVideoPosition(targetTime, preferredTime = null) {
        // Lọc và sắp xếp các quiz active theo thứ tự thời gian
        const activeQuizzes = quizzes
            .filter(q => q.is_active && q.start_at_seconds && q.start_at_seconds > 0)
            .sort((a, b) => a.start_at_seconds - b.start_at_seconds);

        // Bước 1: Kiểm tra preferredTime (vị trí trước khi seek) có hợp lệ không
        if (preferredTime !== null && preferredTime >= 0) {
            const checkPreferred = canSeekToTime(preferredTime);
            if (!checkPreferred.blocked && preferredTime <= targetTime) {
                // Vị trí trước khi seek là hợp lệ và không vượt quá targetTime
                // → Trả về ngay để giữ nguyên vị trí người dùng đang xem
                return preferredTime;
            }
        }

        // Bước 2: preferredTime không hợp lệ, tìm vị trí hợp lệ gần nhất
        // Bắt đầu từ preferredTime (hoặc 0 nếu không có)
        const startTime = preferredTime !== null && preferredTime >= 0 ? preferredTime : 0;
        let safeTime = startTime; // Vị trí an toàn hiện tại

        // Duyệt các quiz theo thứ tự thời gian
        for (const quiz of activeQuizzes) {
            // Chỉ xét các quiz sau startTime và trước targetTime
            if (quiz.start_at_seconds <= startTime) continue;
            if (quiz.start_at_seconds > targetTime) break;

            const result = quizResults[quiz.id];

            // Kiểm tra quiz này có chặn không
            let isBlocked = false;
            if (quiz.is_required) {
                // Quiz bắt buộc: phải đã làm VÀ đạt điểm
                if (!result || !result.passed) {
                    isBlocked = true;
                }
            } else {
                // Quiz không bắt buộc: nếu đã làm thì phải đạt điểm
                if (result && !result.passed) {
                    isBlocked = true;
                }
            }

            if (isBlocked) {
                // Tìm thấy quiz chưa hoàn thành đầu tiên → dừng ở đây
                // safeTime là vị trí ngay trước quiz này (hoặc startTime nếu quiz này là đầu tiên)
                break;
            } else {
                // Quiz này đã hoàn thành → có thể qua
                // safeTime có thể là vị trí sau quiz này, nhưng không vượt quá targetTime
                safeTime = Math.min(quiz.start_at_seconds, targetTime);
            }
        }

        // Đảm bảo safeTime không vượt quá targetTime và không nhỏ hơn startTime
        return Math.max(startTime, Math.min(safeTime, targetTime));
    }

    /**
     * Hiển thị thông báo khi người dùng bị chặn (seek/play qua quiz chưa hoàn thành)
     *
     * Logic:
     * 1. Kiểm tra cooldown để tránh spam message
     * 2. Tìm vị trí an toàn và revert video về đó
     * 3. Hiển thị modal/alert với message phù hợp
     * 4. Khi đóng modal, đảm bảo video vẫn ở vị trí an toàn
     *
     * @param {Object} quiz - Quiz đang chặn
     * @param {string} reason - 'not_done' (chưa làm) hoặc 'not_passed' (chưa đạt)
     */
    function showBlockedMessage(quiz, reason) {
        // Kiểm tra cooldown để tránh hiển thị message liên tục (spam protection)
        const now = Date.now();
        if (isBlockedMessageShowing || (now - lastBlockedMessageTime < BLOCKED_MESSAGE_COOLDOWN)) {
            return;
        }

        // Tạo message dựa vào lý do bị chặn
        const message = reason === 'not_done'
            ? `Bạn cần hoàn thành quiz "${quiz.title || 'Quiz'}" trước khi tiếp tục xem video.`
            : `Bạn cần đạt điểm tối thiểu ${quiz.passing_percent_score}% cho quiz "${quiz.title || 'Quiz'}" trước khi tiếp tục xem video.`;

        lastBlockedMessageTime = now;
        isBlockedMessageShowing = true;

        // Tìm vị trí hợp lệ gần nhất, ưu tiên vị trí trước khi seek
        const currentVideoTime = videoElement ? videoElement.currentTime : videoTimeBeforeSeek;
        const safeTime = findSafeVideoPosition(currentVideoTime, videoTimeBeforeSeek);

        // Đảm bảo video quay về vị trí hợp lệ ngay lập tức
        if (videoElement && Math.abs(videoElement.currentTime - safeTime) > 0.1) {
            videoElement.currentTime = safeTime;
        }
        // Cập nhật videoTimeBeforeSeek về vị trí hợp lệ
        videoTimeBeforeSeek = safeTime;

        // Sử dụng NotificationModal nếu có, hoặc fallback về alert
        if (typeof NotificationModal !== 'undefined' && NotificationModal.show) {
            NotificationModal.show(message, 'warning', () => {
                // Callback khi modal đóng - đảm bảo video ở vị trí hợp lệ
                isBlockedMessageShowing = false;

                if (videoElement) {
                    // Reset ngay lập tức về vị trí hợp lệ
                    const finalSafeTime = findSafeVideoPosition(videoTimeBeforeSeek);
                    videoElement.currentTime = finalSafeTime;
                    videoTimeBeforeSeek = finalSafeTime;

                    // Đảm bảo video pause
                    if (!videoElement.paused) {
                        videoElement.pause();
                    }

                    // Double check sau một khoảng thời gian ngắn (để đảm bảo video không tự động seek lại)
                    setTimeout(() => {
                        if (videoElement) {
                            const checkSafeTime = findSafeVideoPosition(videoTimeBeforeSeek);
                            if (Math.abs(videoElement.currentTime - checkSafeTime) > 0.05) {
                                videoElement.currentTime = checkSafeTime;
                                videoTimeBeforeSeek = checkSafeTime;
                            }
                            if (!videoElement.paused) {
                                videoElement.pause();
                            }
                        }
                    }, 100);
                }
            });
        } else {
            // Fallback: sử dụng alert
            alert(message);
            isBlockedMessageShowing = false;

            // Đảm bảo video quay về vị trí hợp lệ sau khi đóng alert
            if (videoElement) {
                const currentTime = videoElement.currentTime;
                const finalSafeTime = findSafeVideoPosition(currentTime, videoTimeBeforeSeek);
                videoElement.currentTime = finalSafeTime;
                videoTimeBeforeSeek = finalSafeTime;
                if (!videoElement.paused) {
                    videoElement.pause();
                }
                // Double check sau khi alert đóng
                setTimeout(() => {
                    if (videoElement) {
                        const checkCurrentTime = videoElement.currentTime;
                        const checkSafeTime = findSafeVideoPosition(checkCurrentTime, videoTimeBeforeSeek);
                        if (Math.abs(videoElement.currentTime - checkSafeTime) > 0.05) {
                            videoElement.currentTime = checkSafeTime;
                            videoTimeBeforeSeek = checkSafeTime;
                        }
                        if (!videoElement.paused) {
                            videoElement.pause();
                        }
                    }
                }, 100);
            }
        }
    }

    /**
     * Xử lý sự kiện 'timeupdate' - được gọi liên tục khi video đang play
     *
     * Logic:
     * 1. Kiểm tra xem vị trí hiện tại có hợp lệ không
     * 2. Nếu hợp lệ: lưu vị trí vào localStorage và cập nhật videoTimeBeforeSeek
     * 3. Nếu không hợp lệ: revert về vị trí an toàn và pause
     * 4. Kiểm tra xem có quiz nào cần hiển thị không (khi đến start_at_seconds)
     */
    function handleVideoTimeUpdate() {
        // Bỏ qua nếu đang seek hoặc không có video/quizzes
        if (!videoElement || !quizzes.length || isSeeking) return;

        const currentTime = videoElement.currentTime;
        const currentTimeFloor = Math.floor(currentTime);

        // Kiểm tra xem có thể tiếp tục không
        const check = canSeekToTime(currentTime);

        if (!check.blocked) {
            // Vị trí hợp lệ: lưu vào localStorage và cập nhật videoTimeBeforeSeek
            localStorage.setItem(`video_position_${lessonId}`, currentTime);
            videoTimeBeforeSeek = currentTime;
        } else {
            // Bị chặn: quay về vị trí hợp lệ và pause
            // Kiểm tra nghiêm ngặt để đảm bảo video luôn ở vị trí đúng
            if (Math.abs(currentTime - videoTimeBeforeSeek) > 0.1) {
                videoElement.pause();
                videoElement.currentTime = videoTimeBeforeSeek;
                // Force update lại một lần nữa để đảm bảo
                setTimeout(() => {
                    if (videoElement && Math.abs(videoElement.currentTime - videoTimeBeforeSeek) > 0.1) {
                        videoElement.currentTime = videoTimeBeforeSeek;
                    }
                }, 50);
                return;
            }
        }

        // Kiểm tra xem có quiz nào cần hiển thị không (chỉ khi video đang ở vị trí hợp lệ)
        if (!check.blocked) {
            // Tìm quiz có start_at_seconds gần với currentTime (trong khoảng ±1 giây)
            const activeQuizzes = quizzes.filter(q => {
                if (!q.is_active || !q.start_at_seconds) return false;
                if (Math.abs(currentTimeFloor - q.start_at_seconds) > 1) return false;

                // Chỉ hiển thị nếu chưa làm hoặc chưa đạt
                const result = quizResults[q.id];
                return !result || !result.passed;
            });

            if (activeQuizzes.length > 0) {
                // Tìm thấy quiz cần hiển thị → pause video và hiển thị modal
                const quiz = activeQuizzes[0];
                videoElement.pause();
                showQuizModal(quiz);
            }
        }
    }

    // ========================================
    // QUIZ TIMELINE
    // ========================================
    /**
     * Render timeline hiển thị vị trí các quiz trên thanh timeline
     *
     * Màu sắc marker:
     * - Xanh dương (bg-blue-500): Chưa làm
     * - Xanh lá (bg-green-500): Đã đạt
     * - Đỏ (bg-red-500): Chưa đạt
     * - Vàng (bg-amber-500): Quiz bắt buộc (badge nhỏ)
     */
    function renderQuizTimeline() {
        const timelineContainer = document.getElementById('quizTimelineContainer');
        const timelineTrack = document.getElementById('quizTimelineTrack');
        const durationText = document.getElementById('videoDurationText');
        const durationEndLabel = document.getElementById('videoDurationEndLabel');

        if (!timelineContainer || !timelineTrack || !videoElement) return;

        const videoDuration = lessonData?.duration || 0;
        const validQuizzes = quizzes.filter(q => q.is_active && q.start_at_seconds && q.start_at_seconds > 0);

        // Ẩn timeline nếu không có video duration hoặc không có quiz
        if (videoDuration <= 0 || validQuizzes.length === 0) {
            timelineContainer.classList.add('hidden');
            return;
        }

        timelineContainer.classList.remove('hidden');
        const durationFormatted = formatSecondsToHHMMSS_Global(videoDuration, true) || '0:00';
        durationText.textContent = `Tổng: ${durationFormatted}`;
        durationEndLabel.textContent = durationFormatted;

        // Render markers cho từng quiz
        timelineTrack.innerHTML = validQuizzes.map((quiz, index) => {
            // Tính phần trăm vị trí trên timeline (clamp trong khoảng 2-98% để không bị tràn)
            const positionPercent = (quiz.start_at_seconds / videoDuration) * 100;
            const positionPercentClamped = Math.min(Math.max(positionPercent, 2), 98);

            // Xác định màu marker dựa vào trạng thái quiz
            const result = quizResults[quiz.id];
            const isPassed = result && result.passed;
            const isDone = result !== undefined;
            let markerColor = 'bg-blue-500'; // Chưa làm
            if (isDone) {
                markerColor = isPassed ? 'bg-green-500' : 'bg-red-500'; // Đã đạt / Chưa đạt
            }

            return `
                <div
                    class="absolute top-1/2 -translate-y-1/2 transform transition-all hover:scale-125 cursor-pointer group z-10"
                    style="left: ${positionPercentClamped}%"
                    title="${escapeHtml(quiz.title || 'Quiz #' + (index + 1))} - ${formatSecondsToHHMMSS_Global(quiz.start_at_seconds, true)}${isDone ? (isPassed ? ' (Đã đạt)' : ' (Chưa đạt)') : ' (Chưa làm)'}"
                >
                    <div class="relative">
                        <div class="w-4 h-4 rounded-full ${markerColor} border-2 border-white dark:border-gray-800 shadow-md"></div>
                        ${quiz.is_required ? `
                            <div class="absolute -top-1 -right-1 w-2.5 h-2.5 rounded-full bg-amber-500 border border-white dark:border-gray-800"></div>
                        ` : ''}
                    </div>
                </div>
            `;
        }).join('');
    }

    // ========================================
    // QUIZ MODAL FUNCTIONS
    // ========================================
    /**
     * Hiển thị modal quiz với các câu hỏi và đáp án
     * Load đáp án đã lưu (nếu có) và render form
     *
     * @param {Object} quiz - Quiz cần hiển thị
     */
    function showQuizModal(quiz) {
        if (!videoElement) return;

        // Pause video khi hiển thị quiz
        videoElement.pause();
        currentQuiz = quiz;

        // Load đáp án đã lưu (nếu có)
        const savedAnswers = quizAnswers[quiz.id] || {};

        // Render thông tin quiz (title, description, passing score)
        document.getElementById('quizModalTitle').textContent = quiz.title || 'Quiz';
        document.getElementById('quizModalDescription').textContent = quiz.description || '';
        document.getElementById('quizModalPassingScore').textContent = quiz.passing_percent_score + '%';

        // Hiển thị/ẩn badge "Bắt buộc"
        const requiredBadge = document.getElementById('quizModalRequired');
        if (quiz.is_required) {
            requiredBadge.classList.remove('hidden');
        } else {
            requiredBadge.classList.add('hidden');
        }

        // Render questions
        const content = document.getElementById('quizModalContent');
        const questions = quiz.questions || [];

        content.innerHTML = questions.map((question, qIndex) => {
            const options = question.options || [];
            const inputType = question.question_type === 'multiple_choice' ? 'checkbox' : 'radio';
            const inputName = `quiz_${quiz.id}_question_${question.id}`;
            const savedQuestionAnswers = savedAnswers[question.id] || [];

            return `
                <div class="mb-6 p-5 bg-gray-50 dark:bg-gray-900/40 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center text-sm font-bold bg-blue-500 text-white rounded-full">
                            ${qIndex + 1}
                        </span>
                        <div class="flex-1">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <p class="text-base font-semibold text-gray-900 dark:text-white flex-1">
                                    ${escapeHtml(question.question_text)}
                                </p>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="px-2 py-1 text-xs font-medium ${question.question_type === 'multiple_choice' ? 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300'} rounded">
                                        ${question.question_type === 'multiple_choice' ? 'Nhiều đáp án' : '1 đáp án'}
                                    </span>
                                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 rounded">
                                        ${question.points || 1} điểm
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-2 mt-4">
                                ${options.map((option) => {
                                    const isChecked = savedQuestionAnswers.includes(option.id);
                                    return `
                                        <label class="flex items-start gap-3 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 cursor-pointer transition-all">
                                            <input
                                                type="${inputType}"
                                                name="${inputName}"
                                                value="${option.id}"
                                                ${isChecked ? 'checked' : ''}
                                                onchange="handleAnswerChange(${quiz.id}, ${question.id}, ${option.id}, this.checked, '${question.question_type}')"
                                                class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">
                                                ${escapeHtml(option.option_text)}
                                            </span>
                                        </label>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('quizModal').classList.remove('hidden');
    }

    /**
     * Ẩn modal quiz và reset currentQuiz
     */
    function hideQuizModal() {
        document.getElementById('quizModal').classList.add('hidden');
        currentQuiz = null;
    }

    /**
     * Xử lý khi người dùng thay đổi đáp án
     *
     * Logic:
     * - Single choice: chỉ giữ 1 đáp án (thay thế đáp án cũ)
     * - Multiple choice: thêm/xóa đáp án vào mảng
     *
     * @param {number} quizId - ID của quiz
     * @param {number} questionId - ID của câu hỏi
     * @param {number} optionId - ID của đáp án
     * @param {boolean} isChecked - Đáp án được chọn hay bỏ chọn
     * @param {string} questionType - 'single_choice' hoặc 'multiple_choice'
     */
    function handleAnswerChange(quizId, questionId, optionId, isChecked, questionType) {
        // Khởi tạo cấu trúc dữ liệu nếu chưa có
        if (!quizAnswers[quizId]) {
            quizAnswers[quizId] = {};
        }
        if (!quizAnswers[quizId][questionId]) {
            quizAnswers[quizId][questionId] = [];
        }

        if (questionType === 'single_choice') {
            // Single choice: chỉ giữ 1 đáp án (thay thế đáp án cũ)
            quizAnswers[quizId][questionId] = isChecked ? [optionId] : [];
        } else {
            // Multiple choice: thêm/xóa đáp án vào mảng
            const index = quizAnswers[quizId][questionId].indexOf(optionId);
            if (isChecked && index === -1) {
                // Thêm đáp án nếu chưa có
                quizAnswers[quizId][questionId].push(optionId);
            } else if (!isChecked && index !== -1) {
                // Xóa đáp án nếu đã có
                quizAnswers[quizId][questionId].splice(index, 1);
            }
        }

        // Lưu vào localStorage
        saveToStorage();
    }

    // ========================================
    // QUIZ SUBMISSION & SCORING
    // ========================================
    /**
     * Kiểm tra đáp án có đúng không
     *
     * @param {string} questionType - 'single_choice' hoặc 'multiple_choice'
     * @param {Array<number>} selectedIds - Mảng ID đáp án người dùng chọn
     * @param {Array<number>} correctIds - Mảng ID đáp án đúng
     * @returns {boolean} - true nếu đúng, false nếu sai
     */
    function checkAnswer(questionType, selectedIds, correctIds) {
        // Sắp xếp để so sánh
        const sortedSelected = [...selectedIds].sort((a, b) => a - b);
        const sortedCorrect = [...correctIds].sort((a, b) => a - b);

        if (questionType === 'single_choice') {
            // Single choice: phải chọn đúng 1 đáp án và đáp án đó phải đúng
            return sortedSelected.length === 1 && sortedSelected[0] === sortedCorrect[0];
        }

        // Multiple choice: số lượng đáp án phải khớp và tất cả đáp án phải đúng
        return sortedSelected.length === sortedCorrect.length &&
               sortedSelected.every((id, index) => id === sortedCorrect[index]);
    }

    /**
     * Submit quiz: tính điểm và lưu kết quả
     *
     * Logic:
     * 1. Duyệt từng câu hỏi, tính điểm
     * 2. Tính phần trăm điểm
     * 3. Kiểm tra đạt hay không (so với passing_percent_score)
     * 4. Lưu kết quả vào quizResults và localStorage
     * 5. Hiển thị result modal
     */
    function submitQuiz() {
        if (!currentQuiz) return;

        const questions = currentQuiz.questions || [];
        const answers = quizAnswers[currentQuiz.id] || {};
        let totalPointsEarned = 0;
        let maxPoints = 0;

        // Duyệt từng câu hỏi để tính điểm
        questions.forEach(question => {
            maxPoints += question.points || 1; // Tổng điểm tối đa
            const selectedIds = answers[question.id] || []; // Đáp án người dùng chọn
            const correctIds = question.options
                .filter(opt => opt.is_correct)
                .map(opt => opt.id); // Đáp án đúng

            // Kiểm tra đáp án có đúng không
            const isCorrect = checkAnswer(question.question_type, selectedIds, correctIds);
            if (isCorrect) {
                totalPointsEarned += question.points || 1; // Cộng điểm nếu đúng
            }
        });

        // Tính phần trăm điểm
        const scorePercent = maxPoints > 0 ? Math.round((totalPointsEarned / maxPoints) * 100) : 0;
        const passed = scorePercent >= currentQuiz.passing_percent_score; // Kiểm tra đạt hay không

        // Lưu kết quả quiz
        quizResults[currentQuiz.id] = {
            passed: passed,
            score: scorePercent,
            pointsEarned: totalPointsEarned,
            maxPoints: maxPoints,
            timestamp: new Date().toISOString()
        };
        saveToStorage();

        // Hiển thị result modal và ẩn quiz modal
        showQuizResult(scorePercent, currentQuiz.passing_percent_score, passed, totalPointsEarned, maxPoints);
        hideQuizModal();
    }

    /**
     * Hiển thị modal kết quả quiz
     *
     * @param {number} score - Phần trăm điểm đạt được
     * @param {number} passingScore - Điểm tối thiểu cần đạt
     * @param {boolean} passed - Đạt hay không
     * @param {number} pointsEarned - Điểm đạt được
     * @param {number} maxPoints - Điểm tối đa
     */
    function showQuizResult(score, passingScore, passed, pointsEarned, maxPoints) {
        const resultModal = document.getElementById('quizResultModal');
        const iconEl = document.getElementById('quizResultIcon');
        const titleEl = document.getElementById('quizResultTitle');
        const messageEl = document.getElementById('quizResultMessage');
        const scoreEl = document.getElementById('quizResultScore');
        const passingEl = document.getElementById('quizResultPassing');

        // Lưu quiz ID vào data attribute của modal để có thể lấy lại sau (phục vụ cho retry button)
        if (currentQuiz) {
            resultModal.setAttribute('data-quiz-id', currentQuiz.id);
        }

        // Render UI dựa vào kết quả (đạt hay không)
        if (passed) {
            // Đạt: hiển thị icon xanh, message chúc mừng
            iconEl.className = 'mx-auto w-16 h-16 mb-4 rounded-full flex items-center justify-center bg-green-100 dark:bg-green-500/20';
            iconEl.innerHTML = `
                <svg class="w-10 h-10 text-green-600 dark:text-green-400" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                </svg>
            `;
            titleEl.textContent = 'Chúc mừng!';
            titleEl.className = 'text-2xl font-bold mb-2 text-green-600 dark:text-green-400';
            messageEl.textContent = 'Bạn đã hoàn thành quiz thành công!';
        } else {
            // Chưa đạt: hiển thị icon đỏ, message khuyến khích làm lại
            iconEl.className = 'mx-auto w-16 h-16 mb-4 rounded-full flex items-center justify-center bg-red-100 dark:bg-red-500/20';
            iconEl.innerHTML = `
                <svg class="w-10 h-10 text-red-600 dark:text-red-400" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>
                </svg>
            `;
            titleEl.textContent = 'Chưa đạt yêu cầu';
            titleEl.className = 'text-2xl font-bold mb-2 text-red-600 dark:text-red-400';
            messageEl.textContent = 'Bạn chưa đạt điểm tối thiểu. Hãy thử lại!';
        }

        // Hiển thị điểm số
        scoreEl.textContent = `${score}% (${pointsEarned}/${maxPoints} điểm)`;
        passingEl.textContent = `${passingScore}%`;

        // Hiển thị modal
        resultModal.classList.remove('hidden');
    }

    /**
     * Tiếp tục xem video sau khi xem kết quả quiz
     *
     * Logic:
     * - Nếu quiz đã đạt: cho phép play video
     * - Nếu quiz chưa đạt: hiển thị thông báo và tự động mở lại quiz modal để làm lại
     */
    function continueVideo() {
        document.getElementById('quizResultModal').classList.add('hidden');

        // Chỉ cho phép tiếp tục nếu quiz đã đạt
        if (currentQuiz) {
            const result = quizResults[currentQuiz.id];
            if (result && result.passed) {
                // Quiz đã đạt → cho phép play video
                if (videoElement) {
                    videoElement.play();
                }
            } else {
                // Quiz chưa đạt → hiển thị thông báo và tự động mở lại quiz modal
                showBlockedMessage(currentQuiz, 'not_passed');
                setTimeout(() => {
                    showQuizModal(currentQuiz);
                }, 1000);
                return;
            }
        } else {
            // Không có quiz hiện tại (fallback)
            hideQuizModal();
        }

        // Re-render timeline để cập nhật trạng thái quiz
        renderQuizTimeline();
    }

    /**
     * Làm lại quiz: xóa kết quả và đáp án, mở lại quiz modal
     *
     * Logic:
     * 1. Lấy quiz từ currentQuiz hoặc data attribute của result modal
     * 2. Xóa kết quả và đáp án của quiz
     * 3. Đóng result modal
     * 4. Mở lại quiz modal để làm lại
     * 5. Re-render timeline
     */
    function retryQuiz() {
        // Lấy quiz từ currentQuiz hoặc từ data attribute của result modal
        let quizToRetry = currentQuiz;

        // Nếu không có currentQuiz, thử lấy từ data attribute (fallback)
        if (!quizToRetry) {
            const resultModal = document.getElementById('quizResultModal');
            const quizId = resultModal?.getAttribute('data-quiz-id');
            if (quizId) {
                quizToRetry = quizzes.find(q => q.id == quizId);
            }
        }

        if (!quizToRetry) {
            console.error('Không tìm thấy quiz để làm lại');
            if (typeof NotificationModal !== 'undefined' && NotificationModal.show) {
                NotificationModal.show('Không tìm thấy quiz để làm lại', 'error');
            } else {
                alert('Không tìm thấy quiz để làm lại');
            }
            return;
        }

        // Xóa kết quả và đáp án của quiz hiện tại
        delete quizResults[quizToRetry.id];
        delete quizAnswers[quizToRetry.id];
        saveToStorage();

        // Đóng result modal
        document.getElementById('quizResultModal').classList.add('hidden');

        // Hiển thị lại quiz modal để làm lại
        showQuizModal(quizToRetry);

        // Re-render timeline để cập nhật trạng thái (marker sẽ chuyển từ đỏ/xanh lá về xanh dương)
        renderQuizTimeline();
    }

    // ========================================
    // INITIALIZATION
    // ========================================
    document.addEventListener('DOMContentLoaded', async () => {
        toggleStates({ loading: true, content: false });

        // Load from storage
        loadFromStorage();

        // Load lesson data và quizzes
        lessonData = await loadLessonData();
        quizzes = await loadQuizzes();

        // Filter chỉ các quiz active để preview
        quizzes = quizzes.filter(q => q.is_active && q.start_at_seconds);

        // Setup video
        await setupVideo();

        toggleStates({ loading: false, content: true });

        // Event listeners - đăng ký ngay khi DOM ready
        document.getElementById('closeQuizModal')?.addEventListener('click', hideQuizModal);
        document.getElementById('submitQuizBtn')?.addEventListener('click', submitQuiz);
        document.getElementById('continueVideoBtn')?.addEventListener('click', continueVideo);

        // Đăng ký event listener cho retry button (có thể dùng event delegation vì button trong modal)
        document.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'retryQuizBtn') {
                e.preventDefault();
                retryQuiz();
            }
        });

        // Close modal on outside click
        document.getElementById('quizModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'quizModal') {
                hideQuizModal();
            }
        });
    });

    // ========================================
    // RESET FUNCTIONS
    // ========================================
    function resetQuizPreview() {
        // Xác nhận trước khi xóa
        if (confirm('Bạn có chắc chắn muốn xóa tất cả kết quả quiz và bắt đầu lại?')) {
            performReset();
        }
    }

    function performReset() {
        // Xóa localStorage
        clearStorage();
        localStorage.removeItem(`video_position_${lessonId}`);

        // Reset variables
        quizAnswers = {};
        quizResults = {};
        currentQuiz = null;
        videoTimeBeforeSeek = 0;

        // Reset video
        if (videoElement) {
            videoElement.pause();
            videoElement.currentTime = 0;
        }

        // Đóng các modal
        document.getElementById('quizModal').classList.add('hidden');
        document.getElementById('quizResultModal').classList.add('hidden');

        // Re-render timeline
        renderQuizTimeline();

        // Hiển thị thông báo thành công
        if (typeof NotificationModal !== 'undefined' && NotificationModal.show) {
            NotificationModal.show('Đã xóa kết quả. Bạn có thể bắt đầu lại từ đầu.', 'success');
        } else {
            alert('Đã xóa kết quả. Bạn có thể bắt đầu lại từ đầu.');
        }
    }

    // ========================================
    // NAVIGATION
    // ========================================
    function handleGotoOtherPageOfLesson(lessonId, targetPage = 'DETAIL') {
        const routes = {
            'DETAIL': '{{ route("admin.lessons.show", ["id" => ":id"]) }}',
            'EDIT': '{{ route("admin.lessons.edit", ["id" => ":id"]) }}',
        };

        const url = routes[targetPage]?.replace(':id', lessonId) || routes['DETAIL'].replace(':id', lessonId);
        window.location.href = url;
    }
</script>
@endsection

