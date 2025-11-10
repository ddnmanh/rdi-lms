@extends('admin.layout')

@section('title', 'Chi tiết khóa học')
@section('description', 'Xem thông tin chi tiết của khóa học')

@section('content')
<div class="min-h-full flex flex-col gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.list') }}"
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

    {{-- Course Detail Card (Flat) --}}
    <div id="courseDetailCard" class="hidden flex-1 w-full max-w-6xl mx-auto flex-col">
        {{-- Course Information --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-blue-600 grid place-items-center">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Thông tin khóa học</h3>
            </div>

            <div class="p-6 flex flex-col xl:flex-row items-start justify-start gap-5">
                {{-- Summary --}}
                <div class="w-[300px] mx-auto lg:col-span-1">
                    <div class="relative mb-4">
                        <div class="w-full h-48 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                            <svg class="h-20 w-20 text-white opacity-80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
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
                <label class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase mb-2">Mô tả</label>
                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                    <p id="courseDescription" class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-wrap break-words">-</p>
                </div>
            </div>
        </div>

        {{-- Lessons --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 mt-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded bg-green-600 grid place-items-center">
                        <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Bài học</h3>
                </div>
                <a id="manageLessonsButton" href="#"
                   class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700 focus:outline-none">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Quản lý bài học</span>
                </a>
            </div>
            <div class="p-6">
                <div id="lessonsList" class="space-y-2">
                    <div class="w-full flex justify-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Users --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 mt-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded bg-purple-600 grid place-items-center">
                        <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Học viên</h3>
                </div>
                <a id="manageUsersButton" href="#"
                   class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-700 focus:outline-none">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Quản lý học viên</span>
                </a>
            </div>
            <div class="p-6">
                <div id="usersList" class="space-y-2">
                    <div class="w-full flex justify-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
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
            <a href="{{ route('admin.courses.list') }}"
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
    const courseId = {{ $courseId }};

    document.addEventListener('DOMContentLoaded', async () => {
        await loadCourseData();
    });

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
        document.getElementById('errorState').classList.toggle('hidden', !error);
    }

    function displayCourseData(course) {
        toggleStates({ loading: false, detail: true, error: false });
        document.getElementById('editButton').href = `/admin/courses/${course.id}/edit`;
        document.getElementById('manageLessonsButton').href = `/admin/courses/${course.id}/lessons`;
        document.getElementById('manageUsersButton').href = `/admin/courses/${course.id}/users`;
        setText('courseId', course.id);
        setText('courseTitle', course.title || 'Chưa có tiêu đề');
        setText('courseDescription', course.description || 'Chưa có mô tả');
        setDate('courseStartDate', course.start_date);
        setDate('courseEndDate', course.end_date);
        setDate('courseCreatedAt', course.created_at);
        setDate('courseUpdatedAt', course.updated_at);

        // Lessons count
        const lessonsCount = Array.isArray(course.lessons) ? course.lessons.length : 0;
        setText('courseLessonsCount', lessonsCount);

        // Users count
        const usersCount = Array.isArray(course.users) ? course.users.length : 0;
        setText('courseUsersCount', usersCount);

        // Display lessons
        const lessons = Array.isArray(course.lessons) ? course.lessons : [];
        const lessonsList = document.getElementById('lessonsList');
        if (!lessons.length) {
            lessonsList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có bài học nào</p>`;
        } else {
            lessonsList.innerHTML = lessons.map(lesson => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded bg-green-600 grid place-items-center">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
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

        // Display users
        const users = Array.isArray(course.users) ? course.users : [];
        const usersList = document.getElementById('usersList');
        if (!users.length) {
            usersList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có học viên nào</p>`;
        } else {
            usersList.innerHTML = users.map(user => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-purple-600 grid place-items-center text-white text-sm font-semibold">
                            ${escapeHtml((user.fullname || user.email || 'U').charAt(0).toUpperCase())}
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

