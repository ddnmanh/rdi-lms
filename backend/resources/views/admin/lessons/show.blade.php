@extends('admin.layout')

@section('title', 'Chi tiết bài học')
@section('description', 'Xem thông tin chi tiết của bài học')

@section('content')
<div class="w-full max-w-[1600px] mx-auto h-full flex flex-col gap-4 3xl:gap-6">

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
                onclick="handleGotoOtherPageOfLesson('{{ $lessonId }}', 'PREVIEW')"
                class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 font-semibold text-white hover:bg-blue-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                </svg>
                <span>Xem như sinh viên</span>
            </button>
            <button
                onclick="handleGotoOtherPageOfLesson('{{ $lessonId }}', 'EDIT')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
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

    {{-- Lesson Detail Card (Tabs) --}}
    <div id="lessonDetailCard" class="hidden w-full h-full mx-auto min-h-0">
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Tabs header --}}
            <div class="relative bg-transparent">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_LESSON_INFO">
                    <div>
                        <svg class="w-4 h-4" viewBox="0 0 384 512" fill="currentColor">
                            <path d="M0 64C0 28.7 28.7 0 64 0L320 0c35.3 0 64 28.7 64 64l0 417.1c0 25.6-28.5 40.8-49.8 26.6L192 412.8 49.8 507.7C28.5 521.9 0 506.6 0 481.1L0 64zM64 48c-8.8 0-16 7.2-16 16l0 387.2 117.4-78.2c16.1-10.7 37.1-10.7 53.2 0L336 451.2 336 64c0-8.8-7.2-16-16-16L64 48z"/>
                        </svg>
                    </div>
                    <span>Thông tin bài học</span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_QUIZ">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                    </svg>
                    <span>Bài tập trắc nghiệm</span>
                    <span id="quizTabCount" class="rounded-full bg-gray-200 dark:bg-gray-700 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300 text-xs">0</span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_STATISTICS">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64L0 400c0 44.2 35.8 80 80 80l400 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 416c-8.8 0-16-7.2-16-16L64 64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 210.7 262.6 153.4c-12.5-12.5-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l73.4-73.4 57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l128-128z"/>
                    </svg>
                    <span>Thống kê</span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full p-6 bg-white dark:bg-gray-800 rounded-b-xl rounded-tr-xl overflow-hidden">
                {{-- Lesson Information Tab --}}
                <div class="tab-panel h-full max-h-full overflow-y-auto" data-tab-content="TAB_LESSON_INFO">
                    @include('admin.lessons.show.tab-lesson-info')
                </div>

                {{-- Quiz Tab --}}
                <div class="tab-panel hidden h-full max-h-full overflow-y-auto" data-tab-content="TAB_QUIZ">
                    @include('admin.lessons.show.tab-quiz')
                </div>

                {{-- Statistics Tab --}}
                @include('admin.lessons.show.tab-statistics')
            </div>
        </div>
    </div>

</div>

<script>
    // ========================================
    // GLOBAL VARIABLES & ROUTES
    // ========================================
    const lessonId = {{ $lessonId }};
    let lessonData = null;

    // Export lessonId to window để các tab có thể truy cập
    window.lessonId = lessonId;

    const detailLessonRouteSystemName = '{{ route('admin.lessons.show', ['id' => ':id']) }}';
    const editLessonRouteSystemName = '{{ route('admin.lessons.edit', ['id' => ':id']) }}';
    const createLessonRouteSystemName = '{{ route('admin.lessons.create') }}';
    const previewLessonRouteSystemName = '{{ route('admin.lessons.quiz-preview', ['id' => ':id']) }}';

    // ========================================
    // TAB FUNCTIONALITY
    // ========================================
    function setupTabs() {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const firstTab = tabButtons[0]?.dataset.tabTarget;
        // Thêm event listener cho các nút tab
        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });

        // Lắng nghe sự kiện hashchange để hỗ trợ nút back/forward của browser
        window.addEventListener('hashchange', () => {
            const hash = window.location.hash.slice(1); // Bỏ ký tự #
            if (hash && document.querySelector(`[data-tab-target="${hash}"]`)) {
                activateTab(hash, false); // false = không cập nhật URL lại
            }
        });

        // Kiểm tra URL hash để active đúng tab khi load trang
        const urlHash = window.location.hash.slice(1);
        if (urlHash && document.querySelector(`[data-tab-target="${urlHash}"]`)) {
            activateTab(urlHash, false);
        } else if (firstTab) {
            activateTab(firstTab);
        }
    }

    function activateTab(target, updateUrl = true) {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const tabPanels = document.querySelectorAll('.tab-panel');
        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('dark:bg-gray-800', isActive);
            button.classList.toggle('border-gray-200', isActive);
            button.classList.toggle('dark:border-gray-700', isActive);
            button.classList.toggle('border-transparent', !isActive);
            button.classList.toggle('border-b-0', isActive);
            button.classList.toggle('text-gray-900', isActive);
            button.classList.toggle('dark:text-gray-100', isActive);
            button.classList.toggle('text-gray-500', !isActive);
            button.classList.toggle('dark:text-gray-400', !isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            button.classList.toggle('hover:text-gray-900', !isActive);
            button.classList.toggle('dark:hover:text-gray-100', !isActive);
            button.classList.toggle('hover:bg-white/70', !isActive);
            button.classList.toggle('dark:hover:bg-gray-800/90', !isActive);
        });
        // tabPanels.forEach(panel => {
        //     const isActive = panel.dataset.tabContent === target;
        //     panel.classList.toggle('hidden', !isActive);
        // });
        tabPanels.forEach(panel => {
            const isActive = panel.dataset.tabContent === target;
            if (isActive) {
                panel.classList.remove('hidden');
                if (panel.id === 'quizSection') {
                    panel.classList.add('flex');
                }
            } else {
                panel.classList.add('hidden');
                if (panel.id === 'quizSection') {
                    panel.classList.remove('flex');
                }
            }
        });

        // Load statistics when tab is active
        if (target === 'TAB_STATISTICS') {
            if (typeof loadLessonStatistics === 'function') {
                loadLessonStatistics();
            }
        }

        // Cập nhật URL hash nếu cần
        if (updateUrl) {
            window.history.replaceState(null, '', `#${target}`);
        }
    }

    // ========================================
    // MAIN DATA LOADING
    // ========================================
    document.addEventListener('DOMContentLoaded', async () => {
        setupTabs();
        lessonData = await loadLessonData();
        // Export lessonData ra window để các tab có thể truy cập
        window.lessonData = lessonData;
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

        // Cập nhật trạng thái nút xóa
        document.getElementById('deleteLessonButton').disabled = lessonData.course_id !== null;
        document.getElementById('deleteLessonButton').classList.toggle('opacity-20', lessonData.course_id !== null);
        document.getElementById('deleteLessonButton').classList.toggle('cursor-not-allowed', lessonData.course_id !== null);

        // Gọi hàm hiển thị thông tin lesson từ tab-lesson-info
        if (typeof displayLessonInfoData === 'function') {
            await displayLessonInfoData(lessonData);
        }
    }

    // ========================================
    // NAVIGATION & DELETE FUNCTIONS
    // ========================================

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
            case 'PREVIEW':
                url = previewLessonRouteSystemName.replace(':id', lessonId);
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

