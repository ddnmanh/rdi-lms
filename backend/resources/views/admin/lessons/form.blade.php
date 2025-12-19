@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm bài học' : 'Chỉnh sửa bài học')

@section('description', $mode === 'CREATE' ? 'Quản trị > Bài học > Thêm mới' : 'Quản trị > Bài học > Chỉnh sửa')

@section('content')

<script>
    const mode = '{{ $mode }}';
    const lessonId = @if($mode === 'EDIT' && isset($lessonId)) {{ $lessonId }} @else null @endif;
</script>

<div class="w-full max-w-[1600px] mx-auto h-full flex flex-col gap-4 3xl:gap-6">

    <!-- Header bar -->
    <div class="sticky top-0 z-10 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

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
                    <p class="text-xs 3xl:text-sm text-blue-600 dark:text-blue-400 truncate">
                        @yield('description')
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Form card with tabs -->
    <div id="lessonForm" class="w-full h-full mx-auto min-h-0">
        <input type="hidden" id="lessonId" value="{{ $mode === 'EDIT' ? ($lessonId ?? '') : '' }}">
        <input type="hidden" id="thumbnail_path" value="">
        <div class="h-full flex flex-col items-stretch justify-start">
            <!-- Tab header -->
            <div id="formTabsHeader" class="relative bg-transparent {{ $mode === 'CREATE' ? 'hidden' : '' }}">
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
            </div>

            <!-- Content area -->
            <div class="flex-1 min-h-0 h-full p-6 bg-white dark:bg-gray-800 rounded-b-xl rounded-tr-xl overflow-hidden">
                <!-- Lesson Information Tab -->
                <div class="tab-panel h-full max-h-full overflow-y-auto" data-tab-content="TAB_LESSON_INFO">
                    @include('admin.lessons.form.tab-lesson-info')
                </div>

                <!-- Quiz Tab -->
                <div id="quizSection" class="tab-panel hidden h-full overflow-hidden flex flex-col" data-tab-content="TAB_QUIZ">
                    @include('admin.lessons.form.tab-quiz')
                </div>
            </div>
        </div>

    </div>

</div>

<script>
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

        // Cập nhật URL hash nếu cần
        if (updateUrl) {
            window.history.replaceState(null, '', `#${target}`);
        }
    }

    // ========================================
    // KHỞI TẠO KHI TRANG TẢI
    // ========================================
    document.addEventListener('DOMContentLoaded', function() {
        // Setup tabs for EDIT mode
        if (mode === 'EDIT') {
            setupTabs();
        }
    });

</script>
@endsection
