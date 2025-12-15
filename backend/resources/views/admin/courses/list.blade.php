@extends('admin.layout')

@section('title', 'Quản lý Khóa học')

@section('description', 'Quản lý tất cả khóa học trong hệ thống')

@section('content')
    <div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">

        {{-- Header Bar --}}
        <div class="max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

            <div class="flex items-center justify-start gap-3">

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
                <div class="flex items-center gap-3">
                    <button onclick="handleGotoOtherPageOfCourse(null, 'CREATE')"
                        type="button"
                        class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                        </svg>
                        <span>Thêm khóa học</span>
                    </button>
                </div>
                <button
                    id="btnDeleteBulk"
                    onclick="openBulkDeleteModal()"
                    disabled
                    class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-50 cursor-not-allowed">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                    </svg>
                    <span>Xóa <span id="selectedCount"></span> khóa học</span>
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="relative z-20 max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4 overflow-visible">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Date Range Filter --}}
                <div class="w-[180px] flex flex-col items-stretch justify-start gap-0.5">
                    <label for="courseStateFilter"
                        class="block ml-3  font-medium text-gray-300 dark:text-gray-300 ml-2">
                        Trạng thái
                    </label>
                    @include('components.select', [
                        'id' => 'courseStateFilter',
                        'placeholder' => 'Chọn trạng thái khóa học...'
                    ])
                </div>

                {{-- Title Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="titleFilter" class="block ml-3  font-medium text-gray-300 dark:text-gray-300">
                        Tiêu đề
                    </label>
                    <input type="text" id="titleFilter" placeholder="Tìm theo tiêu đề..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>


                {{-- Date From Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="startDateFromFilter" class="block ml-3  font-medium text-gray-300 dark:text-gray-300">
                        Ngày bắt đầu
                    </label>
                    <div class="flex items-center gap-1">
                        @include('components.date-picker', [
                            'id' => 'startDateFromFilter',
                            'placeholder' => 'Từ ngày'
                        ])
                        <div class="text-gray-300 dark:text-gray-500">
                            <svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor">
                                <path d="M0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32z"/>
                            </svg>
                        </div>
                        @include('components.date-picker', [
                            'id' => 'startDateToFilter',
                            'placeholder' => 'Đến ngày'
                        ])
                    </div>
                </div>

                {{-- Date To Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="endDateToFilter" class="block ml-3  font-medium text-gray-300 dark:text-gray-300">
                        Ngày kết thúc
                    </label>
                    <div class="flex items-center gap-1">
                        @include('components.date-picker', [
                            'id' => 'endDateFromFilter',
                            'placeholder' => 'Từ ngày'
                        ])
                        <div class="text-gray-300 dark:text-gray-500">
                            <svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor">
                                <path d="M0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32z"/>
                            </svg>
                        </div>
                        @include('components.date-picker', [
                            'id' => 'endDateToFilter',
                            'placeholder' => 'Đến ngày'
                        ])
                    </div>
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
                <div id="coursesTable" class="w-fit min-w-full"></div>

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
    </div>

    <script>
        let currentPage = 1;
        let itemPerPage = 50;
        let sortBy = 'created_at';
        let sortOrder = 'desc';
        let selectedCourseIds = new Set();
        let deleteCourseId = null;
        let isBulkDelete = false;

        const detailCourseRouteSystemName = '{{ route('admin.courses.show', ['id' => ':id']) }}';
        const editCourseRouteSystemName = '{{ route('admin.courses.edit', ['id' => ':id']) }}';
        const createCourseRouteSystemName = '{{ route('admin.courses.create') }}';

        document.addEventListener('DOMContentLoaded', async function() {
            Select.init('courseStateFilter', {
                options: [
                    { value: '', label: 'Tất cả' },
                    { value: 'active', label: 'Đang diễn ra' },
                    { value: 'upcoming', label: 'Sắp diễn ra' },
                    { value: 'past', label: 'Đã kết thúc' }
                ]
            });

            DatePicker.init('startDateFromFilter', {
                maxDate: DatePicker.today(),
                onChange: (value) => {
                    DatePicker.setMinDate('startDateToFilter', value || null);
                }
            });
            DatePicker.init('startDateToFilter', {
                maxDate: DatePicker.today()
            });

            DatePicker.init('endDateFromFilter', {
                maxDate: DatePicker.today(),
                onChange: (value) => {
                    DatePicker.setMinDate('endDateToFilter', value || null);
                }
            });
            DatePicker.init('endDateToFilter', {
                maxDate: DatePicker.today()
            });

            // Khởi tạo filters từ URL trước
            initFiltersFromUrl();

            await loadCourses(currentPage, itemPerPage);

            // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
            const filterInputs = ['titleFilter', 'courseStateFilter', 'startDateFromFilter', 'startDateToFilter', 'endDateFromFilter', 'endDateToFilter'];
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
                loadCourses(currentPage, itemPerPage);
            });
        });

        // Hàm đọc URL parameters
        function getUrlParams() {
            const params = new URLSearchParams(window.location.search);
            return {
                page: params.get('page') || 1,
                per_page: params.get('per_page') || 50,
                title: decodeURIComponent(params.get('title') ?? ''),
                course_state: params.get('course_state') || '',
                start_date_from: params.get('start_date_from') || '',
                start_date_to: params.get('start_date_to') || '',
                end_date_from: params.get('end_date_from') || '',
                end_date_to: params.get('end_date_to') || '',
                sort_by: params.get('sort_by') || 'created_at',
                order_by: params.get('order_by') || 'desc'
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
                    if (key === 'title' && params[key] != '') value = encodeURIComponent(params[key]);
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
            document.getElementById('titleFilter').value = params.title;
            Select.setValue('courseStateFilter', params.course_state);
            DatePicker.setValue('startDateFromFilter', params.start_date_from);
            DatePicker.setValue('startDateToFilter', params.start_date_to);
            DatePicker.setValue('endDateFromFilter', params.end_date_from);
            DatePicker.setValue('endDateToFilter', params.end_date_to);

            // Set giá trị sort
            sortBy = params.sort_by || sortBy;
            sortOrder = params.order_by || sortOrder;
            currentPage = parseInt(params.page) || currentPage;
            itemPerPage = parseInt(params.per_page) || itemPerPage;
        }

        function resetFilters() {
            document.getElementById('titleFilter').value = '';
            Select.setValue('courseStateFilter', '');
            DatePicker.setValue('startDateFromFilter', '');
            DatePicker.setValue('startDateToFilter', '');
            DatePicker.setValue('endDateFromFilter', '');
            DatePicker.setValue('endDateToFilter', '');

            // Reset sort về mặc định
            currentPage = 1;
            itemPerPage = 50;
            sortBy = 'created_at';
            sortOrder = 'desc';
            // Xóa URL params
            window.history.pushState({}, '', window.location.pathname);
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
                return `
                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-500 ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                        <path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z"/>
                    </svg>
                `;
            } else if (sortOrder === 'asc') {
                // Sorted ascending
                return `
                    <svg class="w-4 h-4 text-white ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                        <path d="M182.6 137.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z"/>
                    </svg>
                `;
            } else {
                // Sorted descending
                return `
                    <svg class="w-4 h-4 text-white ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                        <path d="M137.4 374.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8L32 192c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z"/>
                    </svg>
                `;
            }
        }

        async function loadCourses(page = 1, itemPerPage = 50) {
            const tableContainer = document.getElementById('coursesTable');
            tableContainer.innerHTML = `
                <div class="w-fit mx-auto mt-[20dvh]">
                    <div id="SPINNER_LOADING">
                        <div id="SPINNER_LOADING_CONTAINER">
                            <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
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
                        <div id="SPINNER_LOADING_ICON">
                            <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                    </svg>
                        </div>
                    </div>
                </div>
            `;

            currentPage = page;
            itemPerPage = itemPerPage;
            // Clear selected courses when changing page or filters
            selectedCourseIds.clear();
            const title = document.getElementById('titleFilter').value;
            const dateRange = Select.getValue('courseStateFilter');
            const startDateFromFilter = DatePicker.getValue('startDateFromFilter');
            const startDateToFilter = DatePicker.getValue('startDateToFilter');
            const endDateFromFilter = DatePicker.getValue('endDateFromFilter');
            const endDateToFilter = DatePicker.getValue('endDateToFilter');

            // Cập nhật URL parameters
            updateUrlParams({
                page: currentPage,
                title: title,
                course_state: dateRange,
                start_date_from: startDateFromFilter,
                start_date_to: startDateToFilter,
                end_date_from: endDateFromFilter,
                end_date_to: endDateToFilter,
                per_page: itemPerPage,
                sort_by: sortBy,
                order_by: sortOrder
            });

            try {
                let url = `/courses?page=${page}&per_page=${itemPerPage}`;
                if (title) url += `&search=${encodeURIComponent(title)}`;
                if (dateRange) url += `&date_range=${dateRange}`;
                if (startDateFromFilter) url += `&start_date_from=${startDateFromFilter}`;
                if (startDateToFilter) url += `&start_date_to=${startDateToFilter}`;
                if (endDateFromFilter) url += `&end_date_from=${endDateFromFilter}`;
                if (endDateToFilter) url += `&end_date_to=${endDateToFilter}`;
                if (sortBy) url += `&sort_by=${sortBy}`;
                if (sortOrder) url += `&order_by=${sortOrder}`;

                const res = await apiRequest(url);

                if (res.success) {
                    renderCoursesTable(res?.data?.data || []);
                    renderPaginationInfo_Global('paginationInfo', res?.data || {});
                    renderPaginationChangeItemPerPage_Global('paginationChangeItemPerPage', itemPerPage, res?.data || {}, loadCourses);
                    renderPaginationChangePage_Global('paginationChangePage', 'paginationChangeItemPerPage', res?.data || {}, loadCourses);
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

            const PIN_COLS_STYLES = {
                checkbox: {
                    sticky: 'PIN left-[0px] 3xl:left-[0px]',
                    width: 'w-[40px] 3xl:w-[60px]'
                },
                id: {
                    sticky: null,
                    width: 'w-[60px] 3xl:w-[80px]'
                },
                thumbnail: {
                    sticky: 'PIN left-[40px] 3xl:left-[60px] sticky-shadow-left',
                    width: 'w-[100px] 3xl:w-[130px]'
                },
                title: {
                    sticky: 'PIN left-[140px] 3xl:left-[190px] sticky-shadow-left',
                    width: 'w-[350px] 3xl:w-[450px]'
                },
                description: {
                    sticky: null,
                    width: 'w-[1000px]'
                },
                users_count: {
                    sticky: null,
                    width: 'w-[105px] 3xl:w-[120px]'
                },
                lessons_count: {
                    sticky: null,
                    width: 'w-[95px] 3xl:w-[110px]'
                },
                start_date: {
                    sticky: 'PIN right-[340px] 3xl:right-[450px] sticky-shadow-right border-l',
                    width: 'w-[100px] 3xl:w-[160px]'
                },
                end_date: {
                    sticky: 'PIN right-[240px] 3xl:right-[290px]',
                    width: 'w-[100px] 3xl:w-[160px]'
                },
                status: {
                    sticky: 'PIN right-[120px] 3xl:right-[140px]',
                    width: 'w-[120px] 3xl:w-[150px]'
                },
                actions: {
                    sticky: 'PIN right-[0px] 3xl:right-[0px]',
                    width: 'w-[120px] 3xl:w-[140px]'
                }
            }

            const headTable =`
                <colgroup>
                    <col class="${PIN_COLS_STYLES['checkbox'].width}">
                    <col class="${PIN_COLS_STYLES['id'].width}">
                    <col class="${PIN_COLS_STYLES['thumbnail'].width}">
                    <col class="${PIN_COLS_STYLES['title'].width}">
                    <col class="${PIN_COLS_STYLES['description'].width}">
                    <col class="${PIN_COLS_STYLES['users_count'].width}">
                    <col class="${PIN_COLS_STYLES['lessons_count'].width}">
                    <col class="${PIN_COLS_STYLES['start_date'].width}">
                    <col class="${PIN_COLS_STYLES['end_date'].width}">
                    <col class="${PIN_COLS_STYLES['status'].width}">
                    <col class="${PIN_COLS_STYLES['actions'].width}">
                </colgroup>
                <thead class="custom-thead">
                    <tr>
                        <th class="${PIN_COLS_STYLES['checkbox'].sticky} text-center">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="">
                        </th>
                        <th onclick="handleSort('id')" class="HAS_SORT ${sortBy === 'id' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['id'].sticky}">
                            <span>
                                ID${getSortIcon('id')}
                            </span>
                        </th>
                        <th class="${PIN_COLS_STYLES['thumbnail'].sticky}">
                            Thumbnail
                        </th>
                        <th onclick="handleSort('title')" class="HAS_SORT ${sortBy === 'title' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['title'].sticky}">
                            <span>
                                Tiêu đề${getSortIcon('title')}
                            </span>
                        </th>
                        <th class="">Mô tả</th>

                        <th class="HAS_SORT ${sortBy === 'users_count' ? 'ACTIVE' : ''}" onclick="handleSort('users_count')">
                            <span>
                                Sinh viên${getSortIcon('users_count')}
                            </span>
                        </th>
                        <th class="HAS_SORT ${sortBy === 'lessons_count' ? 'ACTIVE' : ''}" onclick="handleSort('lessons_count')">
                            <span>
                                Bài học${getSortIcon('lessons_count')}
                            </span>
                        </th>

                        <th class="HAS_SORT ${sortBy === 'start_date' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['start_date'].sticky}" onclick="handleSort('start_date')">
                            <span>
                                Bắt đầu${getSortIcon('start_date')}
                            </span>
                        </th>
                        <th class="HAS_SORT ${sortBy === 'end_date' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['end_date'].sticky}" onclick="handleSort('end_date')">
                            <span>
                                Kết thúc${getSortIcon('end_date')}
                            </span>
                        </th>
                        <th class="${PIN_COLS_STYLES['status'].sticky} text-center">Trạng Thái</th>

                        <th class="${PIN_COLS_STYLES['actions'].sticky}">Thao tác</th>
                    </tr>
                </thead>
            `;

            if (courses.length === 0) {
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
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy khóa học</p>
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
                        ${
                            courses.map(course => {
                                const lessonsCount = (course.lessons || []).length;
                                const usersCount = course.users_count !== undefined ? course.users_count : (course.users || [])
                                    .length;
                                const isChecked = selectedCourseIds.has(course.id);

                                // Xác định trạng thái dựa trên start_date và end_date
                                let status = 'upcoming';
                                let statusText = 'Sắp diễn ra';
                                let statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
                                const now = new Date();
                                const startDate = course.start_date ? new Date(course.start_date) : null;
                                const endDate = course.end_date ? new Date(course.end_date) : null;

                                if (startDate && endDate) {
                                    if (now < startDate) {
                                        status = 'upcoming';
                                        statusText = 'Sắp diễn ra';
                                        statusClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400 truncate';
                                    } else if (now >= startDate && now <= endDate) {
                                        status = 'active';
                                        statusText = 'Đang diễn ra';
                                        statusClass =
                                            'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 truncate';
                                    } else {
                                        status = 'past';
                                        statusText = 'Đã kết thúc';
                                        statusClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 truncate';
                                    }
                                }

                                return `
                                    <tr>
                                        <td class="${PIN_COLS_STYLES['checkbox'].sticky} align-center">
                                            <input type="checkbox"
                                                class="course-checkbox"
                                                value="${course.id}"
                                                ${isChecked ? 'checked' : ''}
                                                onchange="toggleCourseSelection(${course.id}, this.checked)">
                                        </td>
                                        <td class="${sortBy === 'id' ? 'ACTIVE' : ''} text-center">
                                            <span class="text-gray-600 dark:text-gray-300">${course.id}</span>
                                        </td>
                                        <td class="${PIN_COLS_STYLES['thumbnail'].sticky}">
                                            <img src="${course.thumbnail_path}" class="w-[60px] 3xl:w-[70px] aspect-video m-auto object-cover rounded-lg bg-gray-200 dark:bg-gray-700" >
                                        </td>
                                        <td class="${sortBy === 'title' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['title'].sticky}">
                                            <span>${course.title ? course.title : '-'}</span>
                                        </td>
                                        <td>
                                            <span>${course.description || '-'}</span>
                                        </td>

                                        <td class="${sortBy === 'users_count' ? 'ACTIVE' : ''}">
                                            <span>${usersCount}</span>
                                        </td>
                                        <td class="${sortBy === 'lessons_count' ? 'ACTIVE' : ''}">
                                            <span>${lessonsCount}</span>
                                        </td>

                                        <td class="${sortBy === 'start_date' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['start_date'].sticky}">
                                            <span>${formatDate_Global(course.start_date)}</span>
                                        </td>
                                        <td class="${sortBy === 'end_date' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['end_date'].sticky}">
                                            <span>${formatDate_Global(course.end_date)}</span>
                                        </td>
                                        <td class="${sortBy === 'status' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['status'].sticky}r">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full font-medium ${statusClass}">
                                                ${statusText}
                                            </span>
                                        </td>

                                        <td class="${PIN_COLS_STYLES['actions'].sticky}">
                                            <div class="flex flex-nowrap items-center justify-end gap-2 overflow-x-auto">
                                                <button
                                                    type="button"
                                                    onclick="handleGotoOtherPageOfCourse(${course.id}, 'DETAIL')"
                                                    class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                                    title="Xem chi tiết">
                                                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6-46.8 43.5-78.1 95.4-93 131.1-3.3 7.9-3.3 16.7 0 24.6 14.9 35.7 46.2 87.7 93 131.1 47.1 43.7 111.8 80.6 192.6 80.6s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1 3.3-7.9 3.3-16.7 0-24.6-14.9-35.7-46.2-87.7-93-131.1-47.1-43.7-111.8-80.6-192.6-80.6zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64-11.5 0-22.3-3-31.7-8.4-1 10.9-.1 22.1 2.9 33.2 13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-12.2-45.7-55.5-74.8-101.1-70.8 5.3 9.3 8.4 20.1 8.4 31.7z"/>
                                                    </svg>
                                                </button>

                                                <button
                                                    type="button"
                                                    onclick="handleGotoOtherPageOfCourse(${course.id}, 'EDIT')"
                                                    class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                                    title="Chỉnh sửa">
                                                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                                                        <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                                                    </svg>
                                                </button>

                                                <button
                                                    onclick="openSingleDeleteModal(${course.id}, ${course.id}, '${(course.title || '').replace(/'/g, '\\\'')}')"
                                                    class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                                                    title="Xóa">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                                                        <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }).join('\n')
                        }
                    </tbody>
                </table>
            `;

            updateSelectAllCheckbox();
            // updateBulkDeleteActions();
        }

        // Di chuyển đến trang khác của khóa học, đồng thời gửi kèm url hiện tại
        function handleGotoOtherPageOfCourse(courseId = null, targetPage = 'DETAIL') {
            let url = '';

            switch (targetPage) {
                case 'DETAIL':
                    url = detailCourseRouteSystemName.replace(':id', courseId);
                    break;
                case 'EDIT':
                    url = editCourseRouteSystemName.replace(':id', courseId);
                    break;
                case 'CREATE':
                    url = createCourseRouteSystemName;
                    break;
                default:
                    url = detailCourseRouteSystemName.replace(':id', courseId);
                    break;
            }
            const currentRoute = window.location.pathname + (window.location.search || '');
            window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
        }

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
            const btnDeleteBulk = document.getElementById('btnDeleteBulk');
            const selectedCount = document.getElementById('selectedCount');

            if (selectedCourseIds.size > 0) {
                btnDeleteBulk.disabled = false;
                btnDeleteBulk.style.opacity = '1';
                btnDeleteBulk.style.cursor = 'pointer';
                selectedCount.textContent = selectedCourseIds.size;
            } else {
                btnDeleteBulk.disabled = true;
                btnDeleteBulk.style.opacity = '0.5';
                btnDeleteBulk.style.cursor = 'not-allowed';
                selectedCount.textContent = '0';
            }
        }

        // Xử lý khi xóa một mục
        async function openSingleDeleteModal(courseId = null, name = '-', desc = '') {
            console.log(courseId, name, desc);
            DeleteModal.openSingle({
                objectName: OBJECTNAMEMODAL.COURSE,
                idDelete: courseId,
                nameValue: name || '-',
                descValue: desc || '',
                actionFuncCallback: () => handleDeleteUsers([courseId]),
                successFuncCallback: () => loadCourses(currentPage, itemPerPage),
                failFuncCallback: () => {}
            });
        }

        // Xử lý khi xóa nhiều mục cùng lúc
        async function openBulkDeleteModal() {
            DeleteModal.openBulk({
                arrayIds: Array.from(selectedCourseIds),
                objectName: OBJECTNAMEMODAL.COURSE,
                actionFuncCallback: () => handleDeleteUsers(Array.from(selectedCourseIds)),
                successFuncCallback: () => loadCourses(currentPage, itemPerPage),
                failFuncCallback: () => {}
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
