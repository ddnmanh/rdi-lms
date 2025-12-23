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

<div class="w-full h-full flex flex-col overflow-hidden gap-2.5 3xl:gap-4">
    {{-- Header Bar --}}
    <div class="w-full max-w-[1600px] mx-auto p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">
        <div class="flex items-center justify-start gap-3">
            <button onclick="handleGotoBackPage_Global()"
                type="button"
                class="group px-2.5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 cursor-pointer text-[18px]">
                <svg class="h-6" viewBox="0 0 320 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                </svg>
            </button>
            <div class="flex flex-col gap-0 min-w-0 flex-1">
                <h2 class="font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                    @yield('title', 'Admin Panel')
                </h2>
                @hasSection('description')
                    <p class="text-xs 3xl:text-sm text-gray-600 dark:text-gray-400 truncate">
                        @yield('description')
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- Course Form Card (Tabs) --}}
    <div class="w-full max-w-[1600px] mx-auto h-full mx-auto min-h-0">
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Hidden input for course ID --}}
            <input type="hidden" id="courseId" value="{{ $mode === 'EDIT_COURSE' ? ($courseId ?? '') : '' }}">

            {{-- Tabs header --}}
            <div class="relative bg-transparent flex flex-row items-end gap-2">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-course-info">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/>
                    </svg>
                    <span>Thông tin cơ bản</span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-lessons">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM188.3 147.1c-7.6 4.2-12.3 12.3-12.3 20.9l0 176c0 8.7 4.7 16.7 12.3 20.9s16.8 4.1 24.3-.5l144-88c7.1-4.4 11.5-12.1 11.5-20.5s-4.4-16.1-11.5-20.5l-144-88c-7.4-4.5-16.7-4.7-24.3-.5z"/>
                    </svg>
                    <span>Bài học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-students">
                    <svg class="w-4 h-4" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z"/>
                    </svg>
                    <span>Sinh viên</span>
                    <span id="studentsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-800 rounded-b-xl overflow-hidden">
                {{-- Course Information Tab --}}
                @include('admin.courses.form.tab-course-info')

                {{-- Lessons Tab --}}
                @include('admin.courses.form.tab-lessons')

                {{-- Students Tab --}}
                @include('admin.courses.form.tab-students')
            </div>
        </div>
    </div>
</div>

{{-- Main JavaScript --}}
<script>
    const mode = '{{ $mode }}';
    let courseId = @if($mode === 'EDIT_COURSE' && isset($courseId)) {{ $courseId }} @else null @endif;
    let courseData = null;

    // Shared data for all tabs
    let orphanedLessonsList = [];
    let parentedLessonsList = [];
    let availableStudentsList = [];
    let enrolledStudentsList = [];
    let initialEnrolledStudentIds = new Set();
    let existingThumbnail = null;
    let thumbnailPreview = null;

    document.addEventListener('DOMContentLoaded', async function() {
        setupTabs();

        if (mode === 'EDIT_COURSE' && courseId) {
            const data = await CourseProvider.handleGetCourseById(courseId);
            if (data?.success) {
                courseData = data?.data;
                handleProcessDataForGeneralUse();
                hubToCallActionAllSubPage();
            } else {
                NotificationModal.show(data?.message || 'Không thể tải thông tin khóa học', 'error', handleGotoBackPage_Global);
            }
        } else {
            hubToCallActionAllSubPage();
        }
    });

    // ===== SETUP FUNCTIONS =====
    function setupTabs() {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const firstTab = tabButtons[0]?.dataset.tabTarget;

        // Add event listeners to tab buttons
        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });

        // Listen for hashchange to support browser back/forward
        window.addEventListener('hashchange', () => {
            const hash = window.location.hash.slice(1);
            if (hash && document.querySelector(`[data-tab-target="${hash}"]`)) {
                activateTab(hash, false);
            }
        });

        // Check URL hash to activate correct tab on page load
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
            panel.classList.toggle('hidden', !isActive);
        });

        // Update URL hash if needed
        if (updateUrl) {
            window.history.replaceState(null, '', `#${target}`);
        }
    }

    // ===== DATA PROCESSING =====
    function handleProcessDataForGeneralUse() {
        if (courseData && Array.isArray(courseData.lessons)) {
            parentedLessonsList = courseData.lessons;
        }
        if (courseData && Array.isArray(courseData.users)) {
            enrolledStudentsList = courseData.users.map(normalizeStudent);
            sortStudentsInPlace(enrolledStudentsList);
            initialEnrolledStudentIds = new Set(enrolledStudentsList.map(student => student.id));
        }
    }

    // ===== MAIN INIT FUNCTION =====
    function hubToCallActionAllSubPage() {
        // Initialize course info tab
        initCourseInfoTab();

        // Initialize lessons tab
        initLessonsTab();

        // Initialize students tab
        initStudentsTab();

        // Update tab badges
        updateTabBadge('lessonsTabCount', parentedLessonsList.length);
        updateTabBadge('studentsTabCount', enrolledStudentsList.length);
    }

    // ===== TAB INITIALIZATION FUNCTIONS =====
    function initCourseInfoTab() {
        // Initialize datetime pickers
        DateTimePicker.init('start_date', {
            onChange: (value) => {
                DateTimePicker.setMinDateTime('end_date', value || null);
            }
        });
        DateTimePicker.init('end_date', {});

        if (mode === 'CREATE_COURSE') {
            DateTimePicker.setMinDateTime('start_date', DateTimePicker.today());
            DateTimePicker.setMinDateTime('end_date', DateTimePicker.today());
        }

        // Initialize thumbnail preview
        initThumbnailPreview();

        // Load course data if editing
        if (mode === 'EDIT_COURSE' && courseData) {
            renderCourseInfo();
        }
    }

    function initLessonsTab() {
        if (mode === 'EDIT_COURSE') {
            loadParentedLessons();
        }
        loadOrphanedLessons();
        initDropZones();
    }

    function initStudentsTab() {
        initStudentsSection();
    }
</script>

<script>
        // ===== SHARED UTILITY FUNCTIONS =====
        async function loadCourseData() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data.success) {
                courseData = data.data;
            }
        } catch (error) {
            NotificationModal.show('Không thể tải thông tin khóa học: ' + error.message, 'error', handleGotoBackPage_Global);
        }
    }

    function normalizeStudent(raw) {
        if (!raw) return { id: null, fullname: '', email: '', roles: [] };
        const rawId = raw.id ?? raw.user_id;
        const id = rawId !== undefined && rawId !== null ? Number(rawId) : null;
        return {
            id,
            fullname: raw.fullname || raw.name || '',
            email: raw.email || '',
            avatar_path: raw.avatar_path || raw.avatar || raw.avatar_url || '',
            phone: raw.phone || raw.phone_number || '',
            roles: Array.isArray(raw.roles) ? raw.roles : [],
            created_at: raw.created_at || ''
        };
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

    function updateTabBadge(id, count) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!count) {
            el.classList.add('hidden');
            el.textContent = '';
            return;
        }
        el.textContent = count;
        el.classList.remove('hidden');
    }
</script>

{{-- Include separate JavaScript files for each tab --}}
{{-- @include('admin.courses.form.shared-js') --}}

@endsection
