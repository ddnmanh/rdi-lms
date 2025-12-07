@extends('admin.layout')

@section('title', 'Quản lý Lessons')

@section('description', 'Quản lý tất cả bài học trong hệ thống')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
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
            <button
                id="btnDeleteBulk"
                onclick="openBulkDeleteModal()"
                disabled
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-50 cursor-not-allowed">
                <i class="fas fa-trash"></i>
                <span>Xóa <span id="selectedCount"></span> bài học</span>
            </button>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Course Filter --}}
                <div class="min-w-32 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="courseFilter" class="block ml-3  font-medium text-gray-300 dark:text-gray-300 ml-2">
                        Khóa học
                    </label>
                    <select id="courseFilter"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Tất cả</option>
                    </select>
                </div>

                {{-- Search Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="searchFilter" class="block ml-3  font-medium text-gray-300 dark:text-gray-300">
                        Tìm kiếm
                    </label>
                    <input type="text" id="searchFilter" placeholder="Tìm theo tiêu đề, mô tả..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Duration Range Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="durationMin" class="block ml-3  font-medium text-gray-300 dark:text-gray-300">
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
                    class="px-4 py-2.5  font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    <i class="fas fa-redo"></i>
                </button>
                <button
                    type="submit"
                    class="px-4 py-2.5  font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
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
                <div class="w-fit mx-auto mt-[20dvh]">
                    <div id="SPINNER_LOADING">
                        <div id="SPINNER_LOADING_CONTAINER">
                            <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                                <div></div> <div></div> <div></div> <div></div> <div></div> <div></div> <div></div> <div></div>
                            </div>
                        </div>
                        <div id="SPINNER_LOADING_ICON">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
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
                    <select id="itemPerPage" onchange="loadLessons(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100  focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Custom Pagination --}}
                <div id="pagination" class="flex items-center justify-end gap-1   "></div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let coursesList = [];
    let deleteLessonId = null;
    let sortBy = 'course_id';
    let sortOrder = 'asc';
    let selectedLessonIds = new Set();

    document.addEventListener('DOMContentLoaded', async function() { 
        await Promise.all([loadCourses(), loadLessons()]);

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
            return '<i class="fas fa-sort text-gray-300 dark:text-gray-500  ml-1"></i>';
        } else if (sortOrder === 'asc') {
            // Sorted ascending
            return '<i class="fas fa-sort-up text-white  ml-1"></i>';
        } else {
            // Sorted descending
            return '<i class="fas fa-sort-down text-white  ml-1"></i>';
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
            <div class="w-fit mx-auto mt-[20dvh]">
                <div id="SPINNER_LOADING">
                    <div id="SPINNER_LOADING_CONTAINER">
                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                        </div>
                    </div>
                    <div id="SPINNER_LOADING_ICON">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        `;
    }

    async function loadLessons(page = 1) {
        renderTableLoading();

        currentPage = page;
        // Clear selected lessons when changing page or filters
        selectedLessonIds.clear();
        const search = document.getElementById('searchFilter').value;
        const courseId = document.getElementById('courseFilter').value;
        const durationMin = document.getElementById('durationMin').value;
        const durationMax = document.getElementById('durationMax').value;
        const itemPerPage = document.getElementById('itemPerPage').value || 50;

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

        const headTable = `
            <colgroup>
                <col class="w-[40px] 3xl:w-[60px]">
                <col class="w-[80px] 3xl:w-[100px]">
                <col class="w-[110px] 3xl:w-[120px]">
                <col class="">
                <col class="">
                <col class="">
                <col class="w-[125px] 3xl:w-[150px]">
                <col class="w-[120px] 3xl:w-[150px]">
            </colgroup>
            <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                <tr>
                    <th class="px-4 py-3 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                        ID${getSortIcon('id')}
                    </th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thumbnail</th>
                    <th onclick="handleSort('title')" class="${getHeaderClass('title')}">
                        Tiêu đề${getSortIcon('title')}
                    </th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Khóa học</th>
                    <th onclick="handleSort('duration')" class="${getHeaderClass('duration')}">
                        Thời lượng${getSortIcon('duration')}
                    </th>
                    <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                </tr>
            </thead>
        `;

        if (lessons.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 ">
                    ${headTable}
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="8" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 rounded-3xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <p class=" font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy bài học</p>
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
            <table class="w-full table-fixed border-separate border-spacing-0 ">
                ${headTable}
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
        `;

        lessons.forEach(lesson => { 
            const isChecked = selectedLessonIds.has(lesson.id);
            html += `
                <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox"
                            class="lesson-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            value="${lesson.id}"
                            ${isChecked ? 'checked' : ''}
                            onchange="toggleLessonSelection(${lesson.id}, this.checked)">
                    </td>
                    <td class="${getCellClass('id')} text-center">
                        <span class="text-gray-600 dark:text-gray-300">${lesson.id}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        <img src="${lesson.thumbnail_path}" class="w-[60px] 3xl:w-[70px] aspect-video m-auto object-cover rounded-lg bg-gray-200 dark:bg-gray-700" >
                    </td>
                    <td class="${getCellClass('title')} text-gray-600 dark:text-gray-300">
                        <span class="text-gray-600 dark:text-gray-300 break-all overflow-hidden text-ellipsis line-clamp-2">
                            ${lesson.title ? lesson.title : '-'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        <span class="text-gray-600 dark:text-gray-300 break-all overflow-hidden text-ellipsis line-clamp-2">
                            ${lesson.description ? lesson.description : '-'}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        <span class="text-gray-600 dark:text-gray-300 break-all overflow-hidden text-ellipsis line-clamp-2">
                            ${lesson.course ? lesson.course.title : '-'}
                        </span>
                    </td>
                    <td class="${getCellClass('duration')} text-gray-600 dark:text-gray-300">
                        ${formatSecondsToHHMMSS_Global(lesson.duration || 0, false)}
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-nowrap items-center justify-end gap-2 overflow-x-auto">
                            <a href="/admin/lessons/${lesson.id}"
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                title="Xem chi tiết">
                                <i class="fas fa-eye  3xl:"></i>
                            </a>
                            <a href="/admin/lessons/${lesson.id}/edit"
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                title="Chỉnh sửa">
                                <i class="fa-solid fa-pen  3xl:"></i>
                            </a>
                            <button onclick="openSingleDeleteModal(${lesson.id}, '${(lesson.title || '').replace(/'/g, "\\'")}', '${(lesson.course ? lesson.course.title : '-').replace(/'/g, "\\'")}')"
                                ${lesson.course_id !== null ? 'disabled' : ''}
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md transition-all duration-300 ${lesson.course_id !== null ? 'border border-gray-200 dark:border-gray-500 text-gray-200 dark:text-gray-500 cursor-not-allowed' : 'border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20'}"
                                title="Xóa"
                            >
                                <i class="fas fa-trash  3xl:"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        tableContainer.innerHTML = html;
        updateSelectAllCheckbox();
        toggleBulkDeleteBtn();
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

    function toggleLessonSelection(lessonId, checked) {
        if (checked) {
            selectedLessonIds.add(lessonId);
        } else {
            selectedLessonIds.delete(lessonId);
        }
        updateSelectAllCheckbox();
        toggleBulkDeleteBtn();
    }

    function toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.lesson-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const lessonId = parseInt(checkbox.value);
            if (checked) {
                selectedLessonIds.add(lessonId);
            } else {
                selectedLessonIds.delete(lessonId);
            }
        });
        toggleBulkDeleteBtn();
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        if (!selectAllCheckbox) return;

        const checkboxes = document.querySelectorAll('.lesson-checkbox');
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

    function toggleBulkDeleteBtn() {
        const btnDeleteBulk = document.getElementById('btnDeleteBulk');
        const selectedCount = document.getElementById('selectedCount');

        if (selectedLessonIds.size > 0) {
            btnDeleteBulk.disabled = false;
            btnDeleteBulk.style.opacity = '1';
            btnDeleteBulk.style.cursor = 'pointer';
            selectedCount.textContent = selectedLessonIds.size;
        } else {
            btnDeleteBulk.disabled = true;
            btnDeleteBulk.style.opacity = '0.5';
            btnDeleteBulk.style.cursor = 'not-allowed';
            selectedCount.textContent = '0';
        }
    }

    // Xử lý khi xóa một mục
    async function openSingleDeleteModal(lessonId, lessonTitle, courseName) {
        openSingleDeleteModalGeneric_Global({
            objectName: OBJECTNAMEMODAL.LESSON,
            idDelete: lessonId,
            nameValue: lessonTitle || '-',
            descValue: courseName || '',
            actionFuncCallback: () => handleDeleteLessons([lessonId]),
            successFuncCallback: () => loadLessons(currentPage),
            failFuncCallback: () => {}
        });
    }

    // Xử lý khi xóa nhiều mục cùng lúc
    async function openBulkDeleteModal() {
        openBulkDeleteModalGeneric_Global({
            arrayIds: Array.from(selectedLessonIds),
            objectName: OBJECTNAMEMODAL.LESSON,
            actionFuncCallback: () => handleDeleteLessons(Array.from(selectedLessonIds)),
            successFuncCallback: () => loadLessons(currentPage),
            failFuncCallback: () => {}
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
