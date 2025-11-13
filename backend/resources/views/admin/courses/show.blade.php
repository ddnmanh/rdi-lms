@extends('admin.layout')

@section('title', 'Chi tiết khóa học')
@section('description', 'Xem thông tin chi tiết của khóa học')

@section('content')
<div class="w-full h-full flex flex-col overflow-hidde"> {{-- khung ngoài chiếm toàn bộ viewport, chặn tràn --}}
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
    <div id="courseDetailCard" class="hidden w-full max-w-[1400px] h-full mx-auto min-h-0"> {{-- cho phép co giãn & cuộn --}}
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Tabs header --}}
            <div class="relative bg-transparent">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-900/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="tab-course-info">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Thông tin khóa học</span>
                </button>
                <button type="button"
                        class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-900/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        data-tab-target="tab-lessons">
                    <i class="fa-regular fa-clipboard"></i>
                    <span>Bài học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
                <button type="button"
                        class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-900/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        data-tab-target="tab-users">
                    <i class="fa-regular fa-user"></i>
                    <span>Học viên</span>
                    <span id="usersTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-900 rounded-b-xl overflow-hidden">
                {{-- Course Information --}}
                <div class="tab-panel max-h-full overflow-y-auto" data-tab-content="tab-course-info">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none rounded-b-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="editButton" href="#"
                           class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm">
                            <i class="fa-solid fa-pen"></i>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>

                    <div class="space-y-6 p-6">
                        {{-- Personal Information Section --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Avatar Section --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-2">Ảnh đại diện</label>
                                <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                    <div class="w-[300px] aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                        <img id="courseThumbnail" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                                    </div>
                                    <div class="flex-1">
                                        {{-- <div class="text-sm text-gray-600 dark:text-gray-300">Ảnh đại diện của người dùng</div> --}}
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

                            {!! $infoField('ID', 'courseId') !!}
                            {!! $infoField('Tiêu đề', 'courseTitle') !!}
                            {!! $infoField('Ngày bắt đầu', 'courseStartDate') !!}
                            {!! $infoField('Ngày kết thúc', 'courseEndDate') !!}
                            {!! $infoField('Số bài học', 'courseLessonsCount') !!}
                            {!! $infoField('Số học viên', 'courseUsersCount') !!}
                            {!! $infoField('Ngày tạo', 'courseCreatedAt') !!}
                            {!! $infoField('Ngày cập nhật', 'courseUpdatedAt') !!}
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="px-6 pb-6">
                        <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                        <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <p class="courseDescription text-sm text-gray-900 dark:text-gray-100 break-all">-</p>
                        </div>
                    </div>
                </div>

                {{-- Lessons --}}
                <div class="tab-panel hidden h-full min-h-0 overflow-y-auto" data-tab-content="tab-lessons">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="manageLessonsButton" href="#"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm">
                            <i class="fa-solid fa-pen"></i>
                            <span>Quản lý bài học</span>
                        </a>
                    </div>
                    <div class="flex-1 min-h-0 p-6 pt-0">
                        <div id="lessonsList" class="space-y-2 h-full overflow-y-auto">

                        </div>
                    </div>
                </div>

                {{-- Users --}}
                <div class="tab-panel hidden h-full flex flex-col items-stretch justify-start" data-tab-content="tab-users">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="manageUsersButton" href="#"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm">
                            <i class="fa-solid fa-pen"></i>
                            <span>Quản lý học viên</span>
                        </a>
                    </div>
                    <div class="flex-1 min-h-0 p-6 pt-0">
                        <div id="usersList" class="space-y-2 h-full overflow-y-auto">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script>
    const courseId = {{ $courseId }};

    document.addEventListener('DOMContentLoaded', async () => {
        setupTabs();
        await loadCourseData();
    });

    function setupTabs() {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const firstTab = tabButtons[0]?.dataset.tabTarget;
        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });
        if (firstTab) {
            activateTab(firstTab);
        }
    }

    function activateTab(target) {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const tabPanels = document.querySelectorAll('.tab-panel');
        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('dark:bg-gray-900', isActive);
            button.classList.toggle('border-gray-200', isActive);
            button.classList.toggle('dark:border-gray-700', isActive);
            button.classList.toggle('border-transparent', !isActive);
            button.classList.toggle('border-b-0', isActive);
            button.classList.toggle('text-gray-900', isActive);
            button.classList.toggle('dark:text-gray-100', isActive);
            button.classList.toggle('text-gray-500', !isActive);
            button.classList.toggle('dark:text-gray-400', !isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
        tabPanels.forEach(panel => {
            const isActive = panel.dataset.tabContent === target;
            panel.classList.toggle('hidden', !isActive);
        });
    }

    async function loadCourseData() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data?.success) {
                displayCourseData(data.data);
            } else {
                showError(data?.message || 'Không thể tải thông tin khóa học');
            }
        } catch (error) {
            showError(error.message || 'Đã xảy ra lỗi khi tải thông tin');
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('courseDetailCard').classList.toggle('hidden', !detail);
        // document.getElementById('errorState').classList.toggle('hidden', !error);
    }

    function displayCourseData(course) {
        toggleStates({ loading: false, detail: true, error: false });
        const pageTitleEl = document.getElementById('coursePageTitle');
        if (pageTitleEl) pageTitleEl.textContent = course.title || 'Chi tiết khóa học';
        document.getElementById('editButton').href = `/admin/courses/${course.id}/edit`;
        document.getElementById('manageLessonsButton').href = `/admin/courses/${course.id}/edit`;
        document.getElementById('manageUsersButton').href = `/admin/courses/${course.id}/edit`;
        setText('courseId', course.id);
        setText('courseTitle', course.title || 'Chưa có tiêu đề');
        setText('courseDescription', course.description || 'Chưa có mô tả');
        setDate('courseStartDate', course.start_date);
        setDate('courseEndDate', course.end_date);
        setDate('courseCreatedAt', course.created_at);
        setDate('courseUpdatedAt', course.updated_at);

        // Hiển thị thumbnail
        const thumbnailEl = document.getElementById('courseThumbnail');
        if (thumbnailEl) {
            if (course.thumbnail) {
                thumbnailEl.src = course.thumbnail;
                thumbnailEl.classList.remove('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
            }
        }

        const lessonsCount = Array.isArray(course.lessons) ? course.lessons.length : 0;
        setText('courseLessonsCount', lessonsCount);
        updateTabBadge('lessonsTabCount', lessonsCount);

        const usersCount = Array.isArray(course.users) ? course.users.length : 0;
        setText('courseUsersCount', usersCount);
        updateTabBadge('usersTabCount', usersCount);

        const lessons = Array.isArray(course.lessons) ? course.lessons : [];
        const lessonsList = document.getElementById('lessonsList');
        if (!lessons.length) {
            lessonsList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có bài học nào</p>`;
        } else {
            lessonsList.innerHTML = lessons.map(lesson => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                            <img src="${lesson.thumbnail}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(lesson.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ${escapeHtml(String(lesson.id ?? '-'))} | Thứ tự: ${escapeHtml(String(lesson.display_order ?? '-'))}</p>
                        </div>
                    </div>
                    ${lesson.description ? `
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">${escapeHtml(String(lesson.description)).slice(0, 80)}${String(lesson.description).length > 80 ? '…' : ''}</span>
                        </div>` : ''
                    }
                </div>
            `).join('');
        }

        const users = Array.isArray(course.users) ? course.users : [];
        const usersList = document.getElementById('usersList');
        if (!users.length) {
            usersList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có học viên nào</p>`;
        } else {
            usersList.innerHTML = users.map(user => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="flex-shrink-0 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 grid place-items-center">
                            <img src="${user.path_avatar}" alt="" class="w-[50px] aspect-square object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(user.fullname || user.email || 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${escapeHtml(user.email || '')}</p>
                        </div>
                    </div>
                    <div class="ml-4">
                        <span class="text-xs text-gray-500 dark:text-gray-400">ID: ${escapeHtml(String(user.id ?? '-'))}</span>
                    </div>
                </div>
            `).join('');
        }
    }

    function showError(message) {
        toggleStates({ loading: false, detail: false, error: true });
        setText('errorMessage', message || 'Đã xảy ra lỗi');
    }

    function setText(id, value) {
        const el = document.getElementsByClassName(id);
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = value != null && value !== '' ? value : '-';
        }
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
