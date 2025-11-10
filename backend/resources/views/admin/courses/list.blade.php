@extends('admin.layout')

@section('title', 'Quản lý Khóa học')

@section('description', 'Quản lý tất cả khóa học trong hệ thống')

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
                <a href="{{ route('admin.courses.create') }}"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                    <span>Thêm khóa học</span>
                </a>
            </div>
            <button
                id="bulkDeleteActions"
                onclick="openBulkDeleteModal()"
                disabled
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-50 cursor-not-allowed">
                <i class="fas fa-trash"></i>
                <span>Xóa <span id="selectedCount"></span> khóa học</span>
            </button>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 2xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Title Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="titleFilter" class="block text-sm font-medium text-gray-300 dark:text-gray-300">
                        Tiêu đề
                    </label>
                    <input type="text" id="titleFilter" placeholder="Tìm theo tiêu đề..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Date Range Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="dateRangeFilter" class="block text-sm font-medium text-gray-300 dark:text-gray-300 ml-2">
                        Trạng thái
                    </label>
                    <select id="dateRangeFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Tất cả</option>
                        <option value="active">Đang diễn ra</option>
                        <option value="upcoming">Sắp diễn ra</option>
                        <option value="past">Đã kết thúc</option>
                    </select>
                </div>

                {{-- Date From Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="startDateFrom" class="block text-sm font-medium text-gray-300 dark:text-gray-300">
                        Ngày bắt đầu
                    </label>
                    <div class="flex items-center gap-1">
                        <input type="date" id="startDateFrom"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <i class="fa-solid fa-minus text-gray-300 dark:text-gray-500"></i>
                        <input type="date" id="startDateTo"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="flex items-end gap-3">
                <button onclick="resetFilters()"
                    class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    <i class="fas fa-redo"></i>
                </button>
                <button
                    type="submit"
                    class="px-4 py-2.5 font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Lọc</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="flex-1 flex flex-col items-stretch justify-start bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="table-scroll-container w-full overflow-x-hidden h-full overflow-y-auto">
            <div id="coursesTable">
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
                    <div class=" text-[#9ca3af80] dark:text-[#9ca3af80]">Đang tải dữ liệu...</div>
                </div>
            </div>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                {{-- Info --}}
                <span id="paginationInfo" class=" text-gray-600 dark:text-gray-400 text-left"></span>

                {{-- Items per page selector --}}
                <div class="flex items-center justify-center gap-2">
                    <label for="itemPerPage" class=" text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>
                    <select id="itemPerPage" onchange="loadCourses(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100  focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15" selected>15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Custom Pagination --}}
                <div id="pagination" class="flex items-center justify-end gap-1"></div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal (Single & Bulk) --}}
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
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 id="deleteModalTitle" class=" font-semibold text-gray-900 dark:text-gray-100">
                            Xác nhận xóa khóa học
                        </h3>
                        <p class=" text-gray-500 dark:text-gray-400">
                            Hành động này không thể hoàn tác
                        </p>
                    </div>
        </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="deleteModalMessage" class="text-gray-700 dark:text-gray-300 mb-4">
                    Bạn có chắc chắn muốn xóa khóa học này không?
                </p>
                {{-- Single delete info --}}
                <div id="singleDeleteInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class=" font-medium text-gray-600 dark:text-gray-400 w-20">Tiêu đề:</span>
                        <span class=" text-gray-900 dark:text-gray-100" id="deleteCourseTitle"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class=" font-medium text-gray-600 dark:text-gray-400 w-20">ID:</span>
                        <span class=" text-gray-900 dark:text-gray-100" id="deleteCourseId"></span>
                    </div>
                </div>
                {{-- Bulk delete info --}}
                <div id="bulkDeleteInfo" class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4" style="display: none;">
                    <p class=" text-gray-600 dark:text-gray-400">
                        Tất cả dữ liệu liên quan đến <span id="bulkDeleteCount" class="font-semibold text-red-600 dark:text-red-400">0</span> khóa học đã chọn sẽ bị xóa vĩnh viễn và không thể khôi phục.
                    </p>
            </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2.5  font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="button" onclick="confirmDelete()"
                    class="px-4 py-2.5  font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Xác nhận xóa</span>
                </button>
            </div>
            </div>
    </div>
</div>

<script>
let currentPage = 1;
    let deleteCourseId = null;
    let isBulkDelete = false;
    let sortBy = 'created_at';
    let sortOrder = 'desc';
    let selectedCourseIds = new Set();

    document.addEventListener('DOMContentLoaded', async function() {
        await loadCourses();

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['titleFilter', 'dateRangeFilter', 'startDateFrom', 'startDateTo'];
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
        document.getElementById('titleFilter').value = '';
        document.getElementById('dateRangeFilter').value = '';
        document.getElementById('startDateFrom').value = '';
        document.getElementById('startDateTo').value = '';
        loadCourses(1);
    }

    function handleSubmitFilter(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        loadCourses(1);
        return false;
    }

    function handleSort(column) {
        if (sortBy === column) {
            // Toggle sort order if clicking the same column
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            // Set new column and default to desc
            sortBy = column;
            sortOrder = 'desc';
        }
        loadCourses(1);
    }

    function getSortIcon(column) {
        if (sortBy !== column) {
            // Not sorted by this column - show neutral icon
            return '<i class="fas fa-sort text-gray-300 dark:text-gray-500 ml-1"></i>';
        } else if (sortOrder === 'asc') {
            // Sorted ascending
            return '<i class="fas fa-sort-up text-white ml-1"></i>';
        } else {
            // Sorted descending
            return '<i class="fas fa-sort-down text-white ml-1"></i>';
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
        const baseClass = 'px-3.5 py-2.5 whitespace-normal break-words';
        if (sortBy === column) {
            // Column is being sorted - highlight with light background
            return baseClass + ' bg-blue-50 dark:bg-gray-700/40';
        } else {
            // Normal state
            return baseClass;
        }
    }

    function renderTableLoading() {
        const tableContainer = document.getElementById('coursesTable');
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
                <div class=" text-[#9ca3af80] dark:text-[#9ca3af80]">Đang tải dữ liệu...</div>
            </div>
        `;
    }

async function loadCourses(page = 1) {
        renderTableLoading();

    currentPage = page;
        // Clear selected courses when changing page or filters
        selectedCourseIds.clear();
        const title = document.getElementById('titleFilter').value;
    const dateRange = document.getElementById('dateRangeFilter').value;
        const startDateFrom = document.getElementById('startDateFrom').value;
        const startDateTo = document.getElementById('startDateTo').value;
        const itemPerPage = document.getElementById('itemPerPage').value || 15;

    try {
            let url = `/courses?page=${page}&per_page=${itemPerPage}`;
            if (title) url += `&search=${encodeURIComponent(title)}`;
        if (dateRange) url += `&date_range=${dateRange}`;
            if (startDateFrom) url += `&start_date_from=${startDateFrom}`;
            if (startDateTo) url += `&start_date_to=${startDateTo}`;
            if (sortBy) url += `&sort_by=${sortBy}`;
            if (sortOrder) url += `&order_by=${sortOrder}`;

            const res = await apiRequest(url);

            if (res.success) {
                renderCoursesTable(res?.data?.data || []);
                renderPagination(res?.data || {});
                renderPaginationInfo(res?.data || {});
            }
        } catch (error) {
            document.getElementById('coursesTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${error.message}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderCoursesTable(courses = []) {
        const tableContainer = document.getElementById('coursesTable');

        if (courses.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 ">
                    <colgroup>
                        <col class="w-[3%]">
                        <col class="w-[4%]">
                        <col class="w-[20%]">
                        <col class="w-[18%]">
                        <col class="w-[9%]">
                        <col class="w-[9%]">
                        <col class="w-[8%]">
                        <col class="w-[9%]">
                        <col class="w-[8%]">
                        <col class="w-[8%]">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th class="px-3.5 py-2.5 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </th>
                            <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                                ID${getSortIcon('id')}
                            </th>
                            <th onclick="handleSort('title')" class="${getHeaderClass('title')}">
                                Tiêu đề${getSortIcon('title')}
                            </th>
                            <th class="px-3.5 py-2.5 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                            <th onclick="handleSort('start_date')" class="${getHeaderClass('start_date')}">
                                Ngày bắt đầu${getSortIcon('start_date')}
                            </th>
                            <th onclick="handleSort('end_date')" class="${getHeaderClass('end_date')}">
                                Ngày kết thúc${getSortIcon('end_date')}
                            </th>
                            <th class="px-3.5 py-2.5 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Trạng Thái</th>
                            <th onclick="handleSort('users_count')" class="${getHeaderClass('users_count')}">
                                Số sinh viên${getSortIcon('users_count')}
                            </th>
                            <th onclick="handleSort('lessons_count')" class="${getHeaderClass('lessons_count')}">
                                Số bài học${getSortIcon('lessons_count')}
                            </th>
                            <th class="px-3.5 py-2.5 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="10" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <i class="fas fa-inbox text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <p class=" font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy khóa học</p>
                                    <p class=" text-gray-500 dark:text-gray-400">Hãy thử lại với các điều kiện lọc khác</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            return;
        }

        let html = `
            <table class="w-full table-fixed border-separate border-spacing-0">
                <colgroup>
                    <col class="w-[3%]">
                    <col class="w-[4%]">
                    <col class="w-[20%]">
                    <col class="w-[18%]">
                    <col class="w-[9%]">
                    <col class="w-[9%]">
                    <col class="w-[8%]">
                    <col class="w-[9%]">
                    <col class="w-[8%]">
                    <col class="w-[8%]">
                </colgroup>
                <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                    <tr>
                        <th class="px-3.5 py-2.5 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </th>
                        <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                            ID${getSortIcon('id')}
                        </th>
                        <th onclick="handleSort('title')" class="${getHeaderClass('title')}">
                            Tiêu đề${getSortIcon('title')}
                        </th>
                        <th class="px-3.5 py-2.5 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                        <th onclick="handleSort('start_date')" class="${getHeaderClass('start_date')}">
                            Ngày bắt đầu${getSortIcon('start_date')}
                        </th>
                        <th onclick="handleSort('end_date')" class="${getHeaderClass('end_date')}">
                            Ngày kết thúc${getSortIcon('end_date')}
                        </th>
                        <th class="px-3.5 py-2.5 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Trạng Thái</th>
                        <th onclick="handleSort('users_count')" class="${getHeaderClass('users_count')}">
                            Số sinh viên${getSortIcon('users_count')}
                        </th>
                        <th onclick="handleSort('lessons_count')" class="${getHeaderClass('lessons_count')}">
                            Số bài học${getSortIcon('lessons_count')}
                        </th>
                        <th class="px-3.5 py-2.5 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
        `;

        courses.forEach(course => {
            const lessonsCount = (course.lessons || []).length;
            const usersCount = course.users_count !== undefined ? course.users_count : (course.users || []).length;
            const isChecked = selectedCourseIds.has(course.id);

            // Xác định trạng thái dựa trên start_date và end_date
            let status = 'upcoming';
            let statusText = 'Sắp diễn ra';
            let statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
            const now = new Date();
            const startDate = course.start_date ? new Date(course.start_date) : null;
            const endDate = course.end_date ? new Date(course.end_date) : null;

            console.log(now, startDate, endDate);
            console.log(now < startDate);
            console.log(now >= startDate && now <= endDate);
            console.log(now > endDate);

            console.log(status, statusText, statusClass);

            if (startDate && endDate) {
                if (now < startDate) {
                    status = 'upcoming';
                    statusText = 'Sắp diễn ra';
                    statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 truncate';
                } else if (now >= startDate && now <= endDate) {
                    status = 'active';
                    statusText = 'Đang diễn ra';
                    statusClass = 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 truncate';
                } else {
                    status = 'past';
                    statusText = 'Đã kết thúc';
                    statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 truncate';
                }
            }

            html += `
                <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-3.5 py-2.5 text-center">
                        <input type="checkbox"
                            class="course-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            value="${course.id}"
                            ${isChecked ? 'checked' : ''}
                            onchange="toggleCourseSelection(${course.id}, this.checked)">
                    </td>
                    <td class="${getCellClass('id')} text-center">
                        <span class="text-gray-600 dark:text-gray-300">${course.id}</span>
                    </td>
                    <td class="${getCellClass('title')} text-gray-600 dark:text-gray-300">
                        ${course.title || '-'}
                    </td>
                    <td class="px-3.5 py-2.5 text-gray-600 dark:text-gray-300 break-all [overflow-wrap:anywhere]">
                        ${course.description ? (course.description.substring(0, 50) + (course.description.length > 50 ? '...' : '')) : '-'}
                    </td>
                    <td class="${getCellClass('start_date')} text-gray-600 dark:text-gray-300">
                        ${formatDate(course.start_date)}
                    </td>
                    <td class="${getCellClass('end_date')} text-gray-600 dark:text-gray-300">
                        ${formatDate(course.end_date)}
                    </td>
                    <td class="${getCellClass('status')} text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium ${statusClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td class="${getCellClass('users_count')} text-center text-gray-600 dark:text-gray-300">
                        ${usersCount}
                    </td>
                    <td class="px-3.5 py-2.5 text-center text-gray-600 dark:text-gray-300">
                        ${lessonsCount}
                    </td>
                    <td class="px-3.5 py-2.5 align-top">
                        <div class="flex flex-nowrap items-center justify-end gap-2">
                            <a href="/admin/courses/${course.id}"
                                class="group/action text-sm inline-flex items-center justify-center w-7 2xl:w-8 aspect-square rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                title="Xem chi tiết">
                                <i class="fas fa-eye 2xl:"></i>
                            </a>
                            <a href="/admin/courses/${course.id}/edit"
                                class="group/action text-sm inline-flex items-center justify-center w-7 2xl:w-8 aspect-square rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                title="Chỉnh sửa">
                                <i class="fa-solid fa-pen 2xl:"></i>
                            </a>
                            <button onclick="openDeleteModal(${course.id}, '${(course.title || '').replace(/'/g, "\\'")}')"
                                class="group/action text-sm inline-flex items-center justify-center w-7 2xl:w-8 aspect-square rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                                title="Xóa">
                                <i class="fas fa-trash 2xl:"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        tableContainer.innerHTML = html;
        updateSelectAllCheckbox();
        updateBulkDeleteActions();
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
            html += `<button onclick="loadCourses(${current - 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Trước</button>`;
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
                    html += `<button onclick="loadCourses(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
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
                        html += `<button onclick="loadCourses(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadCourses(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            } else if (current >= last - 2) {
                // Show first, ellipsis, last 3
                html += `<button onclick="loadCourses(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = last - 2; i <= last; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadCourses(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
            } else {
                // Show first, ellipsis, current-1, current, current+1, ellipsis, last
                html += `<button onclick="loadCourses(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = current - 1; i <= current + 1; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadCourses(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadCourses(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            }
        }

        // Next Button
        if (current < last) {
            html += `<button onclick="loadCourses(${current + 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Sau</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Sau</button>`;
        }
        pagination.innerHTML = html;
    }

    function openDeleteModal(id, courseTitle) {
        isBulkDelete = false;
        deleteCourseId = id;

        // Update modal for single delete
        document.getElementById('deleteModalTitle').textContent = 'Xác nhận xóa khóa học';
        document.getElementById('deleteModalMessage').textContent = 'Bạn có chắc chắn muốn xóa khóa học này không?';
        document.getElementById('singleDeleteInfo').style.display = 'block';
        document.getElementById('bulkDeleteInfo').style.display = 'none';
        document.getElementById('deleteCourseTitle').textContent = courseTitle || '-';
        document.getElementById('deleteCourseId').textContent = id;

        document.getElementById('deleteModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        document.body.style.overflow = '';
        deleteCourseId = null;
        isBulkDelete = false;
    }

    async function confirmDelete() {
        if (isBulkDelete) {
            // Bulk delete
            if (selectedCourseIds.size === 0) return;

            const courseIds = Array.from(selectedCourseIds);

            try {
                let successCount = 0;
                let failCount = 0;

                for (const courseId of courseIds) {
                    try {
                        const data = await apiRequest(`/courses/${courseId}`, {
                            method: 'DELETE'
                        });
                        if (data.success) {
                            successCount++;
        } else {
                            failCount++;
        }
    } catch (error) {
                        failCount++;
                        console.error(`Error deleting course ${courseId}:`, error);
                    }
                }

                if (successCount > 0) {
                    showNotificationModel(`Đã xóa thành công ${successCount} khóa học${failCount > 0 ? `, ${failCount} khóa học xóa thất bại` : ''}`);
                    selectedCourseIds.clear();
                    closeDeleteModal();
                    loadCourses(currentPage);
                } else {
                    showNotificationModel('Không thể xóa khóa học đã chọn', 'error');
                }
            } catch (error) {
                showNotificationModel(error.message, 'error');
            }
        } else {
            // Single delete
            if (!deleteCourseId) return;

            try {
                const data = await apiRequest(`/courses/${deleteCourseId}`, {
            method: 'DELETE'
        });

        if (data.success) {
                    closeDeleteModal();
                    showNotificationModel(data.message || 'Xóa thành công');
                    selectedCourseIds.delete(deleteCourseId);
            loadCourses(currentPage);
        }
    } catch (error) {
                showNotificationModel(error.message, 'error');
            }
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
                alertIconClass.className = 'fas fa-check-circle text-green-600 dark:text-green-400';
                alertTitle.textContent = 'Thành công';
                alertButton.className = 'px-4 py-2.5  font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'error':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20';
                alertIconClass.className = 'fas fa-exclamation-circle text-red-600 dark:text-red-400';
                alertTitle.textContent = 'Lỗi';
                alertButton.className = 'px-4 py-2.5  font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'warning':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20';
                alertIconClass.className = 'fas fa-exclamation-triangle text-amber-600 dark:text-amber-400';
                alertTitle.textContent = 'Cảnh báo';
                alertButton.className = 'px-4 py-2.5  font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'info':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20';
                alertIconClass.className = 'fas fa-info-circle text-blue-600 dark:text-blue-400';
                alertTitle.textContent = 'Thông tin';
                alertButton.className = 'px-4 py-2.5  font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2';
                break;
            default:
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700';
                alertIconClass.className = 'fas fa-bell text-gray-600 dark:text-gray-400';
                alertTitle.textContent = 'Thông báo';
                alertButton.className = 'px-4 py-2.5  font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-700 transition-all duration-300 flex items-center gap-2';
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

    function toggleCourseSelection(courseId, checked) {
        if (checked) {
            selectedCourseIds.add(courseId);
        } else {
            selectedCourseIds.delete(courseId);
        }
        updateSelectAllCheckbox();
        updateBulkDeleteActions();
    }

    function toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.course-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const courseId = parseInt(checkbox.value);
            if (checked) {
                selectedCourseIds.add(courseId);
            } else {
                selectedCourseIds.delete(courseId);
            }
        });
        updateBulkDeleteActions();
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        if (!selectAllCheckbox) return;

        const checkboxes = document.querySelectorAll('.course-checkbox');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

        if (checkboxes.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === checkboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }

    function updateBulkDeleteActions() {
        const bulkDeleteActions = document.getElementById('bulkDeleteActions');
        const selectedCount = document.getElementById('selectedCount');

        if (selectedCourseIds.size > 0) {
            bulkDeleteActions.disabled = false;
            bulkDeleteActions.style.opacity = '1';
            bulkDeleteActions.style.cursor = 'pointer';
            selectedCount.textContent = selectedCourseIds.size;
        } else {
            bulkDeleteActions.disabled = true;
            bulkDeleteActions.style.opacity = '0.5';
            bulkDeleteActions.style.cursor = 'not-allowed';
            selectedCount.textContent = '0';
        }
    }

    function openBulkDeleteModal() {
        if (selectedCourseIds.size === 0) {
            showNotificationModel('Vui lòng chọn ít nhất một khóa học để xóa', 'error');
            return;
        }

        isBulkDelete = true;
        deleteCourseId = null;

        // Update modal for bulk delete
        document.getElementById('deleteModalTitle').textContent = 'Xác nhận xóa nhiều khóa học';
        document.getElementById('deleteModalMessage').textContent = `Bạn có chắc chắn muốn xóa ${selectedCourseIds.size} khóa học đã chọn không?`;
        document.getElementById('singleDeleteInfo').style.display = 'none';
        document.getElementById('bulkDeleteInfo').style.display = 'block';
        document.getElementById('bulkDeleteCount').textContent = selectedCourseIds.size;

        document.getElementById('deleteModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
}
</script>

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
                        <i id="alertIconClass" class="></i>
                    </div>
                    <div>
                        <h3 id="alertTitle" class=" font-semibold text-gray-900 dark:text-gray-100">
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
                    class="px-4 py-2.5  font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span>Đóng</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
