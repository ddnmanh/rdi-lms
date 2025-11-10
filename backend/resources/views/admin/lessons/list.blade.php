@extends('admin.layout')

@section('title', 'Quản lý Lessons')

@section('description', 'Quản lý tất cả bài học trong hệ thống')

@section('content')
<style>
    /* Custom scrollbar overlay - đè lên header, không chiếm chỗ */
    .table-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }

    /* Webkit browsers (Chrome, Safari, Edge) */
    .table-scroll-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-scroll-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 4px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }

    /* Dark mode */
    .dark .table-scroll-container {
        scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
    }

    .dark .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(75, 85, 99, 0.5);
    }

    .dark .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: rgba(75, 85, 99, 0.7);
    }
</style>
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 2xl:gap-4">
    <div class="flex flex-col items-stretch justify-start gap-2.5">
        {{-- Actions Bar --}}
        <div class="flex items-center justify-start gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.lessons.create') }}"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                    <span>Thêm bài học</span>
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 2xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Course Filter --}}
                <div class="min-w-32 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="courseFilter" class="block text-sm font-medium text-gray-300 dark:text-gray-300 ml-2">
                        Khóa học
                    </label>
                    <select id="courseFilter"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Tất cả</option>
                    </select>
                </div>

                {{-- Search Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="searchFilter" class="block text-sm font-medium text-gray-300 dark:text-gray-300">
                        Tìm kiếm
                    </label>
                    <input type="text" id="searchFilter" placeholder="Tìm theo tiêu đề, mô tả..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Duration Range Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="durationMin" class="block text-sm font-medium text-gray-300 dark:text-gray-300">
                        Thời lượng từ (giây)
                    </label>
                    <div class="flex items-center gap-1">
                        <input type="number" id="durationMin" min="0" placeholder="0"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <i class="fa-solid fa-minus text-gray-300 dark:text-gray-500"></i>
                        <input type="number" id="durationMax" min="0" placeholder="9999"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex items-end gap-3">
                <button onclick="resetFilters()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    <i class="fas fa-redo"></i>
                </button>
                <button
                    type="submit"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Lọc</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="flex-1 flex flex-col items-stretch justify-start bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="table-scroll-container w-full overflow-x-hidden h-full overflow-y-auto">
            <div id="lessonsTable">
                <div class="pt-40 flex flex-col items-center justify-center gap-2">
                    <div id="SPINNER_LOADING">
                        <div id="SPINNER_LOADING_LDS_ROLLER">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <div class="text-sm text-[#9ca3af80] dark:text-[#9ca3af80]">Đang tải dữ liệu...</div>
                </div>
            </div>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                {{-- Info --}}
                <span id="paginationInfo" class="text-sm text-gray-600 dark:text-gray-400 text-left"></span>

                {{-- Items per page selector --}}
                <div class="flex items-center justify-center gap-2">
                    <label for="itemPerPage" class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>
                    <select id="itemPerPage" onchange="loadLessons(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Custom Pagination --}}
                <div id="pagination" class="flex items-center justify-end gap-1 text-sm 2xl:text-base"></div>
            </div>
        </div>
    </div>
</div>


{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 z-[100] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDeleteModal()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20">
                        <i class="fas fa-exclamation-triangle text-xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 id="deleteModalTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Xác nhận xóa bài học
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Hành động này không thể hoàn tác
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="deleteModalMessage" class="text-gray-700 dark:text-gray-300 mb-4">
                    Bạn có chắc chắn muốn xóa bài học này không?
                </p>
                <div id="deleteLessonInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-24">Tiêu đề:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="deleteLessonTitle"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400 w-24">Khóa học:</span>
                        <span class="text-sm text-gray-900 dark:text-gray-100" id="deleteLessonCourse"></span>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" onclick="confirmDelete()"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Xác nhận xóa</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Alert Notification Modal --}}
<div id="alertModal" class="fixed inset-0 z-[100] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAlertModal()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="alertIcon" class="flex h-12 w-12 items-center justify-center rounded-xl">
                        <i id="alertIconClass" class="text-xl"></i>
                    </div>
                    <div>
                        <h3 id="alertTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Thông báo
                        </h3>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="alertMessage" class="text-gray-700 dark:text-gray-300">
                </p>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAlertModal()"
                    id="alertButton"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span>Đóng</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let coursesList = [];
    let deleteLessonId = null;
    let sortBy = 'display_order';
    let sortOrder = 'asc';

    document.addEventListener('DOMContentLoaded', async function() {
        await loadCourses();
        await loadLessons();

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['searchFilter', 'courseFilter', 'durationMin', 'durationMax'];
        filterInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const filterForm = input.closest('form');
                        if (filterForm) {
                            filterForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                        }
                    }
                });
            }
        });
    });

    function resetFilters() {
        document.getElementById('courseFilter').value = '';
        document.getElementById('searchFilter').value = '';
        document.getElementById('durationMin').value = '';
        document.getElementById('durationMax').value = '';
        loadLessons(1);
    }

    function handleSubmitFilter(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        loadLessons(1);
        return false;
    }

    async function loadCourses() {
        try {
            const data = await apiRequest('/courses?per_page=100');
            if (data.success) {
                coursesList = data.data.data || [];
                const courseFilter = document.getElementById('courseFilter');

                // Clear existing options except first one
                courseFilter.innerHTML = '<option value="">Tất cả</option>';

                coursesList.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.title;
                    courseFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading courses:', error);
        }
    }

    function handleSort(column) {
        if (sortBy === column) {
            // Toggle sort order if clicking the same column
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new column and default to asc
            sortBy = column;
            sortOrder = 'asc';
        }
        loadLessons(1);
    }

    function getSortIcon(column) {
        if (sortBy !== column) {
            // Not sorted by this column - show neutral icon
            return '<i class="fas fa-sort text-gray-300 dark:text-gray-500 text-sm ml-1"></i>';
        } else if (sortOrder === 'asc') {
            // Sorted ascending
            return '<i class="fas fa-sort-up text-white text-sm ml-1"></i>';
        } else {
            // Sorted descending
            return '<i class="fas fa-sort-down text-white text-sm ml-1"></i>';
        }
    }

    function getHeaderClass(column) {
        const baseClass = 'px-4 py-3 text-left sticky top-0 z-20 shadow-sm whitespace-normal break-words cursor-pointer hover:bg-blue-700 dark:hover:bg-gray-600 transition-colors select-none';
        if (sortBy === column) {
            // Column is being sorted - highlight with darker blue
            return baseClass + ' bg-blue-700 dark:bg-gray-600';
        } else {
            // Normal state
            return baseClass + ' bg-blue-600 dark:bg-gray-700';
        }
    }

    function getCellClass(column) {
        const baseClass = 'px-4 py-3 whitespace-normal break-words';
        if (sortBy === column) {
            // Column is being sorted - highlight with light background
            return baseClass + ' bg-blue-50 dark:bg-gray-700/40';
        } else {
            // Normal state
            return baseClass;
        }
    }

    function renderTableLoading() {
        const tableContainer = document.getElementById('lessonsTable');
        tableContainer.innerHTML = `
            <div class="pt-40 flex flex-col items-center justify-center gap-2">
                <div id="SPINNER_LOADING">
                    <div id="SPINNER_LOADING_LDS_ROLLER">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
                <div class="text-sm text-[#9ca3af80] dark:text-[#9ca3af80]">Đang tải dữ liệu...</div>
            </div>
        `;
    }

    async function loadLessons(page = 1) {
        renderTableLoading();

        currentPage = page;
        const search = document.getElementById('searchFilter').value;
        const courseId = document.getElementById('courseFilter').value;
        const durationMin = document.getElementById('durationMin').value;
        const durationMax = document.getElementById('durationMax').value;
        const itemPerPage = document.getElementById('itemPerPage').value || 15;

        try {
            let url = `/lessons?page=${page}&per_page=${itemPerPage}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (courseId) url += `&course_id=${courseId}`;
            if (durationMin) url += `&duration_min=${durationMin}`;
            if (durationMax) url += `&duration_max=${durationMax}`;
            if (sortBy) url += `&sort_by=${sortBy}`;
            if (sortOrder) url += `&order_by=${sortOrder}`;

            const data = await apiRequest(url);

            if (data.success) {
                renderLessonsTable(data.data.data || []);
                renderPagination(data.data || {});
                renderPaginationInfo(data.data || {});
            }
        } catch (error) {
            document.getElementById('lessonsTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${error.message}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderLessonsTable(lessons = []) {
        const tableContainer = document.getElementById('lessonsTable');

        if (lessons.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 text-sm">
                    <colgroup>
                        <col class="w-[7%]">
                        <col class="w-[20%]">
                        <col class="w-[25%]">
                        <col class="w-[13%]">
                        <col class="w-[10%]">
                        <col class="w-[25%]">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                                ID${getSortIcon('id')}
                            </th>
                            <th onclick="handleSort('title')" class="${getHeaderClass('title')}">
                                Tiêu đề${getSortIcon('title')}
                            </th>
                            <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Khóa học</th>
                            <th onclick="handleSort('duration')" class="${getHeaderClass('duration')}">
                                Thời lượng${getSortIcon('duration')}
                            </th>
                            <th onclick="handleSort('display_order')" class="${getHeaderClass('display_order')}">
                                Thứ tự${getSortIcon('display_order')}
                            </th>
                            <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="6" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy bài học</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Hãy thử lại với các điều kiện lọc khác</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            return;
        }

        let html = `
            <table class="w-full table-fixed border-separate border-spacing-0 text-sm">
                <colgroup>
                    <col class="w-[7%]">
                    <col class="w-[20%]">
                    <col class="w-[25%]">
                    <col class="w-[13%]">
                    <col class="w-[10%]">
                    <col class="w-[25%]">
                </colgroup>
                <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                    <tr>
                        <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                            ID${getSortIcon('id')}
                        </th>
                        <th onclick="handleSort('title')" class="${getHeaderClass('title')}">
                            Tiêu đề${getSortIcon('title')}
                        </th>
                        <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Khóa học</th>
                        <th onclick="handleSort('duration')" class="${getHeaderClass('duration')}">
                            Thời lượng${getSortIcon('duration')}
                        </th>
                        <th onclick="handleSort('display_order')" class="${getHeaderClass('display_order')}">
                            Thứ tự${getSortIcon('display_order')}
                        </th>
                        <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
        `;

        lessons.forEach(lesson => {
            const courseName = lesson.course ? lesson.course.title : '-';
            const durationMinutes = Math.floor(lesson.duration / 60);
            const durationSeconds = lesson.duration % 60;
            const durationFormatted = `${durationMinutes}:${durationSeconds.toString().padStart(2, '0')}`;

            html += `
                <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="${getCellClass('id')} text-center">
                        <span class="text-gray-600 dark:text-gray-300">${lesson.id}</span>
                    </td>
                    <td class="${getCellClass('title')} text-gray-600 dark:text-gray-300">
                        ${lesson.title || '-'}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        ${courseName}
                    </td>
                    <td class="${getCellClass('duration')} text-gray-600 dark:text-gray-300">
                        ${durationFormatted}
                    </td>
                    <td class="${getCellClass('display_order')} text-center text-gray-600 dark:text-gray-300">
                        ${lesson.display_order || 0}
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-wrap items-center justify-end gap-2">
                            <a href="/admin/lessons/${lesson.id}"
                                class="group/action inline-flex items-center justify-center w-8 h-8 rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                title="Xem chi tiết">
                                <i class="fas fa-eye text-sm 2xl:text-sm"></i>
                            </a>
                            <a href="/admin/lessons/${lesson.id}/edit"
                                class="group/action inline-flex items-center justify-center w-8 h-8 rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                title="Chỉnh sửa">
                                <i class="fa-solid fa-pen text-sm 2xl:text-sm"></i>
                            </a>
                            <button onclick="openDeleteModal(${lesson.id}, '${(lesson.title || '').replace(/'/g, "\\'")}', '${(courseName || '').replace(/'/g, "\\'")}')"
                                class="group/action inline-flex items-center justify-center w-8 h-8 rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                                title="Xóa">
                                <i class="fas fa-trash text-sm 2xl:text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        tableContainer.innerHTML = html;
    }

    function renderPaginationInfo(paginationData = {}) {
        const firstItem = paginationData?.from || 0;
        const lastItem = paginationData?.to || 0;
        const total = paginationData?.total || 0;

        document.getElementById('paginationInfo').innerHTML = `
            Hiển thị <span class="font-semibold text-gray-900 dark:text-gray-100">${firstItem}</span>
            – <span class="font-semibold text-gray-900 dark:text-gray-100">${lastItem}</span>
            trong <span class="font-semibold text-gray-900 dark:text-gray-100">${total}</span>
        `;
    }

    function renderPagination(paginationData) {
        const pagination = document.getElementById('pagination');
        const current = paginationData.current_page;
        const last = paginationData.last_page;

        if (last <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let html = '';

        // Previous Button
        if (current > 1) {
            html += `<button onclick="loadLessons(${current - 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Trước</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Trước</button>`;
        }

        // Page Numbers
        if (last <= 7) {
            // Show all pages if 7 or fewer
            for (let i = 1; i <= last; i++) {
                if (i === current) {
                    html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                } else {
                    html += `<button onclick="loadLessons(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                }
            }
        } else {
            // Complex pagination for many pages
            if (current <= 3) {
                // Show first 3, ellipsis, last
                for (let i = 1; i <= 3; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadLessons(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadLessons(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            } else if (current >= last - 2) {
                // Show first, ellipsis, last 3
                html += `<button onclick="loadLessons(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = last - 2; i <= last; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadLessons(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
            } else {
                // Show first, ellipsis, current-1, current, current+1, ellipsis, last
                html += `<button onclick="loadLessons(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = current - 1; i <= current + 1; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadLessons(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadLessons(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            }
        }

        // Next Button
        if (current < last) {
            html += `<button onclick="loadLessons(${current + 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Sau</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Sau</button>`;
        }
        pagination.innerHTML = html;
    }


    function openDeleteModal(id, lessonTitle, courseName) {
        deleteLessonId = id;

        document.getElementById('deleteModalTitle').textContent = 'Xác nhận xóa bài học';
        document.getElementById('deleteModalMessage').textContent = 'Bạn có chắc chắn muốn xóa bài học này không?';
        document.getElementById('deleteLessonTitle').textContent = lessonTitle || '-';
        document.getElementById('deleteLessonCourse').textContent = courseName || '-';

        document.getElementById('deleteModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        document.body.style.overflow = '';
        deleteLessonId = null;
    }

    async function confirmDelete() {
        if (!deleteLessonId) return;

        try {
            const data = await apiRequest(`/lessons/${deleteLessonId}`, {
                method: 'DELETE'
            });

            if (data.success) {
                closeDeleteModal();
                showNotificationModel(data.message || 'Xóa thành công');
                loadLessons(currentPage);
            }
        } catch (error) {
            showNotificationModel(error.message, 'error');
        }
    }

    // Alert Modal Functions
    function showNotificationModel(message, type = 'success') {
        const alertModal = document.getElementById('alertModal');
        const alertIcon = document.getElementById('alertIcon');
        const alertIconClass = document.getElementById('alertIconClass');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertButton = document.getElementById('alertButton');

        // Set message
        alertMessage.textContent = message;

        // Set type-specific styles
        switch (type) {
            case 'success':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20';
                alertIconClass.className = 'fas fa-check-circle text-xl text-green-600 dark:text-green-400';
                alertTitle.textContent = 'Thành công';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'error':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20';
                alertIconClass.className = 'fas fa-exclamation-circle text-xl text-red-600 dark:text-red-400';
                alertTitle.textContent = 'Lỗi';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'warning':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20';
                alertIconClass.className = 'fas fa-exclamation-triangle text-xl text-amber-600 dark:text-amber-400';
                alertTitle.textContent = 'Cảnh báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'info':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20';
                alertIconClass.className = 'fas fa-info-circle text-xl text-blue-600 dark:text-blue-400';
                alertTitle.textContent = 'Thông tin';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2';
                break;
            default:
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700';
                alertIconClass.className = 'fas fa-bell text-xl text-gray-600 dark:text-gray-400';
                alertTitle.textContent = 'Thông báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-700 transition-all duration-300 flex items-center gap-2';
        }

        // Show modal
        alertModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeAlertModal() {
        const alertModal = document.getElementById('alertModal');
        alertModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const deleteModal = document.getElementById('deleteModal');
            const alertModal = document.getElementById('alertModal');

            if (deleteModal && deleteModal.style.display !== 'none') {
                closeDeleteModal();
            }
            if (alertModal && alertModal.style.display !== 'none') {
                closeAlertModal();
            }
        }
    });
</script>
@endsection
