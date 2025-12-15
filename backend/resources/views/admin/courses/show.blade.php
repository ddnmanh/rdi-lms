@extends('admin.layout')

@section('title', 'Chi tiết khóa học')
@section('description', 'Xem thông tin chi tiết của khóa học')

@section('content')
<div class="w-full h-full flex flex-col overflow-hidden gap-2.5 3xl:gap-4"> {{-- khung ngoài chiếm toàn bộ viewport, chặn tràn --}}

    {{-- Header Bar --}}
    <div class="max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

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
                onclick="handleGotoOtherPageOfCourse(courseData_MainShow.id, 'EDIT')"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                </svg>
                <span>Chỉnh sửa</span>
            </button>
            <button
                onclick="openSingleDeleteModal()"
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700">
                <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                </svg>
                <span>Xóa khóa học này</span>
            </button>
        </div>
    </div>

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
                    <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                    </svg>
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
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/>
                    </svg>
                    <span>Khóa học</span>
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
                    data-tab-target="tab-users">
                    <svg class="w-4 h-4" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z"/>
                    </svg>
                    <span>Sinh viên</span>
                    <span id="usersTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-6 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-statistics">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64L0 400c0 44.2 35.8 80 80 80l400 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 416c-8.8 0-16-7.2-16-16L64 64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 210.7 262.6 153.4c-12.5-12.5-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l73.4-73.4 57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l128-128z"/>
                    </svg>
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

    const detailCourseRouteSystemName = '{{ route('admin.courses.show', ['id' => ':id']) }}';
    const editCourseRouteSystemName = '{{ route('admin.courses.edit', ['id' => ':id']) }}';

    // Di chuyển đến trang khác của khóa học, đồng thời gửi kèm url hiện tại
    function handleGotoOtherPageOfCourse(courseId = null, targetPage = 'DETAIL') {
        if (!courseId) return;

        let url = '';

        switch (targetPage) {
            case 'DETAIL':
                url = detailCourseRouteSystemName.replace(':id', courseId);
                break;
            case 'EDIT':
                url = editCourseRouteSystemName.replace(':id', courseId);
                break;
            default:
                url = detailCourseRouteSystemName.replace(':id', courseId);
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }

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
                NotificationModal.show('Lỗi', data?.message || 'Không thể tải thông tin khóa học', 'error');
            }
        } catch (error) {
            NotificationModal.show('Lỗi', data?.message || 'Không thể tải thông tin khóa học', 'error');
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

    // Xử lý khi xóa một mục
    async function openSingleDeleteModal(courseId = courseData_MainShow.id || null, name = courseData_MainShow.id.toString() || '-', desc = courseData_MainShow.title || '') {
        DeleteModal.openSingle({
            objectName: OBJECTNAMEMODAL.COURSE,
            idDelete: courseId,
            nameValue: name,
            descValue: desc,
            actionFuncCallback: () => handleDeleteUsers([courseId]),
            successFuncCallback: () => NotificationModal.show('Đã xóa khóa học thành công', 'success', handleGotoBackPage_Global),
            failFuncCallback: () => NotificationModal.show('Không thể xóa khóa học', 'error')
        });
    }

    async function handleDeleteUsers(arrayIds = []) {
        try {
            const data = await apiRequest(`/courses/`, {
                method: 'DELETE',
                body: JSON.stringify({
                    course_ids: [...arrayIds]
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
