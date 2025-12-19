@extends('admin.layout')

@section('title', 'Quản lý Quizzes')

@section('description', 'Quản lý tất cả các bài tập trắc nghiệm trong hệ thống')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">

    {{-- Header Bar --}}
    <div class="w-full mx-auto p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

        <div class="flex items-center justify-start gap-3">
            <div class="flex flex-col gap-0 min-w-0 flex-1">
                <h2
                    class="font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                    @yield('title', 'Admin Panel')
                </h2>
                @hasSection('description')
                    <p class="text-xs 3xl: text-gray-600 dark:text-gray-400 truncate">
                        @yield('description')
                    </p>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-start gap-3">

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    onclick="handleGotoOtherPageOfQuiz(null, 'CREATE')"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                    </svg>
                    <span>Thêm Quiz</span>
                </button>
            </div>

            {{-- Bulk Actions --}}
            <div id="bulkActions" class="items-center gap-3">
                <button
                    id="btnDeleteBulk"
                    disabled
                    onclick="handleBulkDelete()"
                    class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-50 cursor-not-allowed">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                    </svg>
                    <span>Xóa <span id="selectedCount">0</span> quiz</span>
                </button>
            </div>

        </div>
    </div>

    {{-- Filter Section --}}
    <form onsubmit="return handleSubmitFilter(event)" class="relative z-20 max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-start gap-4 overflow-visible">
        <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
             {{-- Search Filter --}}
            <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                <label for="searchFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                    Tìm kiếm
                </label>
                <input type="text" id="searchFilter" placeholder="Tìm theo tiêu đề..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            </div>
        </div>

        <div class="flex items-end gap-3">
            <button onclick="resetFilters()"
                class="px-4 py-3  font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M48.5 224H40c-13.3 0-24-10.7-24-24V72c0-9.7 5.8-18.5 14.8-22.2s19.3-1.7 26.2 5.2L98.6 96.6c87.6-86.5 228.7-86.2 315.8 1c87.5 87.5 87.5 229.3 0 316.8s-229.3 87.5-316.8 0c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0c62.5 62.5 163.8 62.5 226.3 0s62.5-163.8 0-226.3c-62.2-62.2-162.7-62.5-225.3-1L185 183c6.9 6.9 8.9 17.2 5.2 26.2s-12.5 14.8-22.2 14.8H48.5z"/>
                </svg>
            </button>
            <button type="submit"
                class="px-4 py-2.5 font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6.1-12.8-15.5-12.8-25.6V320.9L9 97.3C-.8 85.4-2.8 68.8 3.9 54.9z"/>
                </svg>
                <span>Lọc</span>
            </button>
        </div>
    </form>

    {{-- Table Card --}}
    <div class="flex-1 min-w-0 min-h-0 flex flex-col items-stretch justify-start bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="table-scroll-container flex-1 min-h-0 w-0 min-w-full overflow-x-auto overflow-y-auto">
            <div id="quizzesTable" class="w-fit min-w-full"></div>
        </div>
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                <span id="paginationInfo" class="text-gray-600 dark:text-gray-400 text-left"></span>
                <div id="paginationChangeItemPerPage" class="flex items-center justify-center gap-2"></div>
                <div id="paginationChangePage" class="flex items-center justify-end gap-1 3xl:text-base"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let itemPerPage = 50;
    let sortBy = 'created_at';
    let sortOrder = 'desc';
    let selectedQuizzes = [];

    const editQuizRouteSystemName = '{{ route('admin.quizzes.edit', ['id' => ':id']) }}';
    const createQuizRouteSystemName = '{{ route('admin.quizzes.create') }}';

    // Hàm đọc URL parameters
    function getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            page: params.get('page') || 1,
            per_page: params.get('per_page') || 50,
            search: decodeURIComponent(params.get('search') ?? ''),
            sort_by: params.get('sort_by') || 'created_at',
            order_by: params.get('order_by') || 'desc',
            next_page_url: params.get('next_page_url') || '',
            prev_page_url: params.get('prev_page_url') || '',
        };
    }

    // Hàm cập nhật URL parameters
    function updateUrlParams(params) {
        const url = new URL(window.location.href);
        // Xóa tất cả params cũ
        url.search = '';

        // Thêm các params mới (chỉ thêm nếu có giá trị)
        Object.keys(params).forEach(key => {
            if (params[key] !== '' && params[key] !== null && params[key] !== undefined) {
                let value = params[key];
                if (key === 'search' && params[key] != '') value = encodeURIComponent(params[key]);
                url.searchParams.set(key, value);
            }
        });

        // Cập nhật URL mà không reload trang
        window.history.pushState({}, '', url.toString());
    }

    // Hàm khởi tạo filters từ URL
    function initFiltersFromUrl() {
        const params = getUrlParams();

        // Set giá trị cho các input filter
        document.getElementById('searchFilter').value = decodeURIComponent(params.search) || '';

        // Set giá trị sort
        sortBy = params.sort_by || sortBy;
        sortOrder = params.order_by || sortOrder;
        currentPage = parseInt(params.page) || currentPage;
        itemPerPage = parseInt(params.per_page) || itemPerPage;
    }

    document.addEventListener('DOMContentLoaded', async function() {
        initFiltersFromUrl();

        await loadQuizzes(currentPage, itemPerPage);

        const filterInputs = ['searchFilter'];
        filterInputs.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                input.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        const filterForm = input.closest('form');
                        if (filterForm) {
                            filterForm.dispatchEvent(new Event('submit', {
                                cancelable: true,
                                bubbles: true
                            }));
                        }
                    }
                });
            }
        });

        // Xử lý khi người dùng nhấn nút back/forward của trình duyệt
        window.addEventListener('popstate', function() {
            initFiltersFromUrl();
            loadUsers(currentPage);
        });
    });

    function resetFilters() {
        document.getElementById('searchFilter').value = '';
        currentPage = 1;
        itemPerPage = 50;
        sortBy = 'created_at';
        sortOrder = 'desc';
        window.history.pushState({}, '', window.location.pathname);
        loadQuizzes(1, itemPerPage);
    }

    function handleSubmitFilter(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        loadQuizzes(1, itemPerPage);
        return false;
    }

    function handleSort(column) {
        if (sortBy === column) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy = column;
            sortOrder = 'asc';
        }
        loadQuizzes(1, itemPerPage);
    }

    function getSortIcon(column) {
        if (sortBy !== column) {
            return `
                <svg class="w-4 h-4 text-white ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                     <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                </svg>
            `;
        } else if (sortOrder === 'asc') {
            return `
                <svg class="w-4 h-4 text-white ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                    <path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/>
                </svg>
            `;
        } else {
            return `
                <svg class="w-4 h-4 text-white ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                    <path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/>
                </svg>
            `;
        }
    }

    async function loadQuizzes(page = 1, perPage = 50) {
        const tableContainer = document.getElementById('quizzesTable');
        tableContainer.innerHTML = `
            <div class="w-fit mx-auto mt-[20dvh]">
                <div id="SPINNER_LOADING">
                    <div id="SPINNER_LOADING_CONTAINER">
                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                            <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
                        </div>
                    </div>
                    <div id="SPINNER_LOADING_ICON">
                        <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                            <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        `;

        currentPage = page;
        itemPerPage = perPage;
        const search = document.getElementById('searchFilter').value;

        updateUrlParams({
            page: currentPage,
            per_page: itemPerPage,
            search: search,
            sort_by: sortBy,
            order_by: sortOrder
        });

        try {
            let url = `/lesson-quizzes?page=${page}&per_page=${perPage}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (sortBy) url += `&sort_by=${sortBy}`;
            if (sortOrder) url += `&order_by=${sortOrder}`;

            const res = await apiRequest(url);

            if (res.success) {
                renderQuizzesTable(res?.data?.data || []);
                renderPaginationInfo_Global('paginationInfo', res?.data || {});
                renderPaginationChangeItemPerPage_Global('paginationChangeItemPerPage', itemPerPage, res?.data || {}, loadQuizzes);
                renderPaginationChangePage_Global('paginationChangePage', 'paginationChangeItemPerPage', res?.data || {}, loadQuizzes);

                // Sync select all checkbox
                const selectAllCheckbox = document.getElementById('selectAllQuizzes');
                if (selectAllCheckbox) {
                    const allCheckboxes = document.querySelectorAll('.quiz-checkbox');
                    selectAllCheckbox.checked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
                }
            }
        } catch (error) {
            document.getElementById('quizzesTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${error.message}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderQuizzesTable(quizzes = []) {
        const tableContainer = document.getElementById('quizzesTable');

        const PIN_COLS_STYLES = {
            checkbox: {
                sticky: 'PIN left-[0px] 3xl:left-[0px]',
                width: 'w-[40px] 3xl:w-[60px]'
            },
            id: {
                sticky: null,
                width: 'w-[60px] 3xl:w-[80px]'
            },
            title: {
                sticky: 'PIN left-[40px] 3xl:left-[60px] sticky-shadow-left',
                width: 'w-[300px] 3xl:w-[450px]'
            },
            description: {
                sticky: null,
                width: 'w-[600px] 3xl:w-[900px]'
            },
            lesson: {
                sticky: null,
                width: 'w-[300px] 3xl:w-[450px]'
            },
            questions_count: {
                sticky: null,
                width: 'w-[80px] 3xl:w-[110px]'
            },
            passing_percent_score: {
                sticky: null,
                width: 'w-[80px] 3xl:w-[120px]'
            },
            start_at_seconds: {
                sticky: null,
                width: 'w-[150px] 3xl:w-[180px]'
            },
            is_active: {
                sticky: 'PIN right-[100px] 3xl:right-[150px] sticky-shadow-right border-l',
                width: 'w-[100px] 3xl:w-[150px]'
            },
            actions: {
                sticky: 'PIN right-[0px] 3xl:right-[0px]',
                width: 'w-[100px] 3xl:w-[150px]'
            }
        }

        const headTable = `
            <colgroup>
                <col class="${PIN_COLS_STYLES['checkbox'].width}">
                <col class="${PIN_COLS_STYLES['id'].width}">
                <col class="${PIN_COLS_STYLES['title'].width}">
                <col class="${PIN_COLS_STYLES['description'].width}">
                <col class="${PIN_COLS_STYLES['lesson'].width}">
                <col class="${PIN_COLS_STYLES['questions_count'].width}">
                <col class="${PIN_COLS_STYLES['passing_percent_score'].width}">
                <col class="${PIN_COLS_STYLES['start_at_seconds'].width}">
                <col class="${PIN_COLS_STYLES['is_active'].width}">
                <col class="${PIN_COLS_STYLES['actions'].width}">
            </colgroup>
            <thead class="custom-thead">
                <tr>
                    <th class="${PIN_COLS_STYLES['checkbox'].sticky} text-center">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="selectAllQuizzes" onchange="toggleSelectAll(this)"
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 bg-white dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </th>
                    <th onclick="handleSort('id')" class="${PIN_COLS_STYLES['id'].sticky} HAS_SORT ${sortBy === 'id' ? 'ACTIVE' : ''}">
                        <span>ID${getSortIcon('id')}</span>
                    </th>
                    <th class="${PIN_COLS_STYLES['title'].sticky}">
                        <span>Tiêu đề</span>
                    </th>
                    <th class="${PIN_COLS_STYLES['description'].sticky}">Mô tả</th>
                    <th class="${PIN_COLS_STYLES['lesson'].sticky}">Bài học</th>
                    <th onclick="handleSort('questions_count')" class="${PIN_COLS_STYLES['questions_count'].sticky} text-center HAS_SORT ${sortBy === 'questions_count' ? 'ACTIVE' : ''}">
                        <span>Câu hỏi${getSortIcon('questions_count')}</span>
                    </th>
                    <th onclick="handleSort('passing_percent_score')" class="${PIN_COLS_STYLES['passing_percent_score'].sticky} text-center HAS_SORT ${sortBy === 'passing_percent_score' ? 'ACTIVE' : ''}">
                        <span>Điểm đạt${getSortIcon('passing_percent_score')}</span>
                    </th>
                    <th class="${PIN_COLS_STYLES['start_at_seconds'].sticky} text-center">Thời điểm bắt đầu</th>
                    <th class="${PIN_COLS_STYLES['is_active'].sticky} text-center">Trạng thái</th>
                    <th class="${PIN_COLS_STYLES['actions'].sticky} text-right">Thao tác</th>
                </tr>
            </thead>
        `;

        if (quizzes.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 ">
                    ${headTable}
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="10" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 text-3xl text-gray-400 dark:text-gray-500 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-7 h-7" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M91.8 32C59.9 32 32.9 55.4 28.4 86.9L.6 281.2c-.4 3-.6 6-.6 9.1L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-125.7c0-3-.2-6.1-.6-9.1L483.6 86.9C479.1 55.4 452.1 32 420.2 32L91.8 32zm0 64l328.5 0 27.4 192-59.9 0c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7l-120.4 0c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7L64.3 288 91.8 96z"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy quiz</p>
                                    <p class=" text-gray-500 dark:text-gray-400">Hãy thử lại với các điều kiện lọc khác</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            return;
        }

        tableContainer.innerHTML = `
            <table class="w-full table-fixed border-separate border-spacing-0">
                ${headTable}
                <tbody class="custom-tbody">
                    ${quizzes.map(quiz => `
                        <tr class="${selectedQuizzes.includes(quiz.id) ? 'bg-blue-50/50 dark:bg-blue-900/20' : ''}">
                            <td class="${PIN_COLS_STYLES['checkbox'].sticky} text-center">
                                <div class="flex items-center justify-center">
                                    <input type="checkbox" onchange="toggleSelectQuiz(${quiz.id})"
                                        ${selectedQuizzes.includes(quiz.id) ? 'checked' : ''}
                                        class="quiz-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 bg-white dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </td>
                            <td class="${sortBy === 'id' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['id'].sticky} text-center">${quiz.id}</td>
                            <td class="${PIN_COLS_STYLES['title'].sticky}">
                                <div class="font-medium text-gray-900 dark:text-white">${escapeHtml_Global(quiz.title || '-')}</div>
                            </td>
                            <td class="${PIN_COLS_STYLES['description'].sticky}">
                                <div class=" text-gray-700 dark:text-gray-300">
                                    ${quiz.description ? escapeHtml_Global(quiz.description) : '<span class="italic text-gray-400">Không có</span>'}
                                </div>
                            </td>
                            <td class="${PIN_COLS_STYLES['lesson'].sticky}">
                                <div class=" text-gray-700 dark:text-gray-300">
                                    ${quiz.lesson ? escapeHtml_Global(quiz.lesson.title) : '<span class="italic text-gray-400">Không có</span>'}
                                </div>
                            </td>
                             <td class="${sortBy === 'questions_count' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['questions_count'].sticky} text-center">
                                <span>
                                    ${quiz.questions_count || (quiz.questions ? quiz.questions.length : 0)}
                                </span>
                            </td>
                            <td class="${sortBy === 'passing_percent_score' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['passing_percent_score'].sticky} text-center ">${quiz.passing_percent_score}%</td>
                            <td class="${PIN_COLS_STYLES['start_at_seconds'].sticky} text-center ">
                                ${quiz.start_at_seconds ? formatSecondsToHHMMSS_Global(quiz.start_at_seconds, false) : '-'}
                            </td>
                            <td class="${PIN_COLS_STYLES['is_active'].sticky} text-center">
                                ${quiz.is_active
                                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>'
                                    : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Inactive</span>'
                                }
                            </td>
                            <td class="${PIN_COLS_STYLES['actions'].sticky}">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        type="button"
                                        onclick="handleGotoOtherPageOfQuiz(${quiz.id}, 'EDIT')"
                                        class="EDIT"
                                        title="Chỉnh sửa">
                                        <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="openDeleteQuizModal(${quiz.id}, '${(quiz.title || '').replace(/'/g, "\\'")}')"
                                        class="DELETE"
                                        title="Xóa">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                                            <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    }


    function handleGotoOtherPageOfQuiz(quizId = null, targetPage = 'DETAIL') {
        let url = '';
        switch (targetPage) {
            case 'EDIT':
                url = editQuizRouteSystemName.replace(':id', quizId);
                break;
            case 'CREATE':
                url = createQuizRouteSystemName;
                break;
            default:
                url = detailQuizRouteSystemName.replace(':id', quizId);
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }


    function openDeleteQuizModal(id, title) {
        DeleteModal.openSingle({
            objectName: 'Quiz',
            idDelete: id,
            nameValue: title,
            descValue: 'Hành động này không thể hoàn tác.',
            actionFuncCallback: () => handleDeleteQuiz(id),
            successFuncCallback: () => loadQuizzes(currentPage, itemPerPage),
            failFuncCallback: () => {}
        });
    }

    async function handleDeleteQuiz(id) {
        try {
            const res = await apiRequest(`/lesson-quizzes/${id}`, {
                method: 'DELETE'
            });
            return res.success;
        } catch (error) {
            console.error(error);
            return false;
        }
    }

    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.quiz-checkbox');
        const isChecked = checkbox.checked;

        checkboxes.forEach(cb => {
            cb.checked = isChecked;
            const quizId = parseInt(cb.closest('tr').querySelector('td:nth-child(2)').textContent);
            if (isChecked) {
                if (!selectedQuizzes.includes(quizId)) {
                    selectedQuizzes.push(quizId);
                }
                cb.closest('tr').classList.add('bg-blue-50/50', 'dark:bg-blue-900/20');
            } else {
                selectedQuizzes = selectedQuizzes.filter(id => id !== quizId);
                cb.closest('tr').classList.remove('bg-blue-50/50', 'dark:bg-blue-900/20');
            }
        });

        updateBulkActionVisibility();
    }

    function toggleSelectQuiz(id) {
        const index = selectedQuizzes.indexOf(id);
        const checkbox = event.target;
        const row = checkbox.closest('tr');

        if (index > -1) {
            selectedQuizzes.splice(index, 1);
            row.classList.remove('bg-blue-50/50', 'dark:bg-blue-900/20');
        } else {
            selectedQuizzes.push(id);
            row.classList.add('bg-blue-50/50', 'dark:bg-blue-900/20');
        }

        const selectAllCheckbox = document.getElementById('selectAllQuizzes');
        const allCheckboxes = document.querySelectorAll('.quiz-checkbox');
        selectAllCheckbox.checked = selectedQuizzes.length === allCheckboxes.length && allCheckboxes.length > 0;

        updateBulkActionVisibility();
    }

    function updateBulkActionVisibility() {
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        const btnDeleteBulk = document.getElementById('btnDeleteBulk');

        if (selectedQuizzes.length > 0) {
            btnDeleteBulk.disabled = false;
            btnDeleteBulk.style.opacity = '1';
            btnDeleteBulk.style.cursor = 'pointer';
            if (selectedCount) selectedCount.textContent = selectedQuizzes.length;
        } else {
            btnDeleteBulk.disabled = true;
            btnDeleteBulk.style.opacity = '0.5';
            btnDeleteBulk.style.cursor = 'not-allowed';
        }
    }

    function handleBulkDelete() {
        DeleteModal.openSingle({
            objectName: 'Quizzes',
            idDelete: selectedQuizzes,
            nameValue: `${selectedQuizzes.length} bài tập đã chọn`,
            descValue: 'Hành động này không thể hoàn tác.',
            actionFuncCallback: async () => {
                let successCount = 0;
                for (const id of selectedQuizzes) {
                    const success = await handleDeleteQuiz(id);
                    if (success) successCount++;
                }
                return successCount > 0;
            },
            successFuncCallback: () => {
                selectedQuizzes = [];
                updateBulkActionVisibility();
                loadQuizzes(currentPage, itemPerPage);
            },
            failFuncCallback: () => {}
        });
    }

</script>
@endsection
