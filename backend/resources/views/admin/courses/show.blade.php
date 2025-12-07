@extends('admin.layout')

@section('title', 'Chi tiết khóa học')
@section('description', 'Xem thông tin chi tiết của khóa học')

@section('content')
<div class="w-full h-full flex flex-col overflow-hidden"> {{-- khung ngoài chiếm toàn bộ viewport, chặn tràn --}}
    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 overflow-auto p-6 sm:p-8">
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

    {{-- Course Detail Card (Tabs) --}}
    <div id="courseDetailCard" class="hidden w-full max-w-[1600px] h-full mx-auto min-h-0"> {{-- cho phép co giãn & cuộn --}}
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Tabs header --}}
            <div class="relative bg-transparent flex flex-row items-end gap-2">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-course-info">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Khóa học</span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-lessons">
                    <i class="fa-regular fa-clipboard"></i>
                    <span>Bài học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-users">
                    <i class="fa-regular fa-user"></i>
                    <span>Sinh viên</span>
                    <span id="usersTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-statistics">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Thống kê</span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-800 rounded-b-xl overflow-hidden">
                {{-- Course Information Tab --}}
                @include('admin.courses.show.tab-course-info')

                {{-- Lessons Tab --}}
                @include('admin.courses.show.tab-lessons')

                {{-- Users Tab --}}
                @include('admin.courses.show.tab-users')

                {{-- Statistics Tab --}}
                @include('admin.courses.show.tab-statistics')
            </div>
        </div>
    </div>
</div>

{{-- Main JavaScript --}}
<script>
    const courseId = {{ $courseId }};
    let courseData_MainShow = null;
    let userCreator_MainShow = null;
    let lessonsData_MainShow = [];
    let usersData_MainShow = [];

    let lessonUserWatchedCounts_MainShow = {}; // Số sinh viên xem mỗi bài học
    let lessonUserFinishedCounts_MainShow = {}; // Số sinh viên hoàn thành mỗi bài học

    document.addEventListener('DOMContentLoaded', async () => {
        setupTabs();
        await loadCourseData();
        if (courseData_MainShow != null) {
            handleProcessDataForGeneralUse();
            hubToCallActionAllSubPage();
        }
    });

    // Xử lý dữ liệu chung cho các tab
    function handleProcessDataForGeneralUse() {

        userCreator_MainShow = courseData_MainShow.creator || null;
        lessonsData_MainShow = Array.isArray(courseData_MainShow.lessons) ? courseData_MainShow.lessons : [];
        usersData_MainShow = Array.isArray(courseData_MainShow.users) ? courseData_MainShow.users : [];

        usersData_MainShow.forEach(user => {
            const lessonViews = Array.isArray(user.lesson_progress) ? user.lesson_progress : [];
            lessonViews.forEach(view => {
                if (view.lesson_id) {
                    lessonUserWatchedCounts_MainShow[view.lesson_id] = (lessonUserWatchedCounts_MainShow[view.lesson_id] || 0) + 1;
                }
                if (view.completion_percentage >= 100) {
                    lessonUserFinishedCounts_MainShow[view.lesson_id] = (lessonUserFinishedCounts_MainShow[view.lesson_id] || 0) + 1;
                }
            });
        });
    }

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
            panel.classList.toggle('hidden', !isActive);
        });

        // Cập nhật URL hash nếu cần
        if (updateUrl) {
            window.history.replaceState(null, '', `#${target}`);
        }

        // Khởi tạo biểu đồ khi tab Statistics được active
        if (target === 'tab-statistics' && courseData_MainShow) {
            setTimeout(() => {
                renderCharts();
            }, 50);
        }
    }

    async function loadCourseData() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data?.success) {
                courseData_MainShow = data.data;
            } else {
                showNotificationModel_Global('Lỗi', data?.message || 'Không thể tải thông tin khóa học', 'error');
            }
        } catch (error) {
            showNotificationModel_Global('Lỗi', data?.message || 'Không thể tải thông tin khóa học', 'error');
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('courseDetailCard').classList.toggle('hidden', !detail);
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

    function renderInfoInTab() {
        // const lessonsCount = Array.isArray(courseData_MainShow.lessons) ? courseData_MainShow.lessons.length : 0;
        // setText('courseLessonsCount', lessonsCount);
        // updateTabBadge('lessonsTabCount', lessonsCount);

        // const usersCount = Array.isArray(courseData_MainShow.users) ? courseData_MainShow.users.length : 0;
        // setText('courseUsersCount', usersCount);
        // updateTabBadge('usersTabCount', usersCount);
    }

    // Hàm gọi các hàm cần thiết của tất cả các tab
    function hubToCallActionAllSubPage(course) {
        toggleStates({ loading: false, detail: true, error: false });

        renderInfoInTab();

        renderCourse();

        renderLessons();

        renderUsers();

        renderStatistics();
    }

</script>

@endsection
