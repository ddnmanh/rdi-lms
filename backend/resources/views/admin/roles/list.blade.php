@extends('admin.layout')

@section('title', 'Quản lý Roles')
@section('description', 'Quản lý tất cả vai trò trong hệ thống')


@section('content')
<div class="h-full min-w-0 flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
    <div class="flex flex-col items-stretch justify-start gap-2.5">

        {{-- Header Bar --}}
        <div class="w-full mx-auto p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

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
                    <button onclick="handleGotoOtherPageOfRole(null, 'CREATE')"
                        type="button"
                        class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                        </svg>
                        <span>Thêm vai trò</span>
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
                    <span>Xóa <span id="selectedCount"></span> người dùng</span>
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="relative z-20 max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4 overflow-visible">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">

                {{-- Block Status Filter --}}
                <div class="w-[180px] flex flex-col items-stretch justify-start gap-0.5">
                    <label for="blockStatusFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Trạng thái</label>
                    @include('components.select', [
                        'id' => 'blockStatusFilter',
                        'placeholder' => 'Chọn vai trò...',
                        // 'searchable' => true
                    ])
                </div>

                {{-- Name Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="nameFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Tên</label>
                    <input type="text" id="nameFilter" placeholder="Tìm theo tên..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Level Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="levelFrom" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Level</label>
                    <div class="flex items-center gap-1">
                        <input type="number" id="levelFrom" min="1" max="255" placeholder="1"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <div class="text-gray-300 dark:text-gray-500">
                            <svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor">
                                <path d="M0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32z"/>
                            </svg>
                        </div>
                        <input type="number" id="levelTo" min="1" max="255" placeholder="255"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                {{-- Created Date Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="createdFromFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Ngày tạo</label>
                    <div class="flex items-center gap-1">
                        @include('components.date-picker', [
                            'id' => 'createdFromFilter',
                            'placeholder' => 'Từ ngày'
                        ])
                        <div class="text-gray-300 dark:text-gray-500">
                            <svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor">
                                <path d="M0 256c0-17.7 14.3-32 32-32l384 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 288c-17.7 0-32-14.3-32-32z"/>
                            </svg>
                        </div>
                        @include('components.date-picker', [
                            'id' => 'createdToFilter',
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
    </div>

    {{-- Table Card --}}
    <div class="flex-1 min-w-0 min-h-0 flex flex-col items-stretch justify-start bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="table-scroll-container flex-1 min-h-0 w-0 min-w-full overflow-x-auto overflow-y-auto">
            <div id="rolesTable" class="w-fit min-w-full"></div>
        </div>

        {{-- Pagination Footer --}}
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
    // ==================== Constants & State ====================
    let currentPage = 1;
    let itemPerPage = 50;
    let sortBy = 'level';
    let sortOrder = 'asc';
    let selectedRoleIds = new Set();

    const FILTERS_STORAGE_KEY = `FILTERS_${window.location.pathname}`;

    const createRoleRouteSystemName = '{{ route('admin.roles.create') }}';
    const detailRoleRouteSystemName = '{{ route('admin.roles.show', ['id' => ':id']) }}';
    const editRoleRouteSystemName = '{{ route('admin.roles.edit', ['id' => ':id']) }}';

    // Hàm cập nhật URL parameters
    function updateUrlParamsAndStorage(params) {
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

        // Cập nhật storage
        localStorage.setItem(FILTERS_STORAGE_KEY, JSON.stringify(params));
    }

    // Hàm khởi tạo filters từ URL
    function initFiltersFromStorageOrUrl() {
        const pathName = window.location.pathname;
        const search = window.location.search;
        let params = null;

        if (search) {
            const searchParams = new URLSearchParams(window.location.search);
            params = {
                page: searchParams.get('page') || 1,
                per_page: searchParams.get('per_page') || 50,
                sort_by: searchParams.get('sort_by') || sortBy,
                order_by: searchParams.get('order_by') || sortOrder,
                permission_id: searchParams.get('permission_id') || null,
                user_id: searchParams.get('user_id') || null,
                search: searchParams.get('search') ?? '',
                level_from: searchParams.get('level_from') || null,
                level_to: searchParams.get('level_to') || null,
                is_block: searchParams.get('is_block') || null,
                created_at_from: searchParams.get('created_at_from') || null,
                created_at_to: searchParams.get('created_at_to') || null,
            };
        } else {
            const filters = localStorage.getItem(FILTERS_STORAGE_KEY);
            params = JSON.parse(filters) || null;
        }

        // Set giá trị cho các input filter
        Select.setValue('blockStatusFilter', params?.is_block);
        document.getElementById('nameFilter').value = decodeURIComponent(params?.search ?? '') || '';
        document.getElementById('levelFrom').value = params?.level_from != null ? params?.level_from : '';
        document.getElementById('levelTo').value = params?.level_to != null ? params?.level_to : '';
        DatePicker.setValue('createdFromFilter', params?.created_at_from);
        DatePicker.setValue('createdToFilter', params?.created_at_to);

        // Set giá trị sort
        sortBy = params?.sort_by;
        sortOrder = params?.order_by;
        currentPage = parseInt(params?.page) || currentPage;
        itemPerPage = parseInt(params?.per_page) || itemPerPage;
    }


    // ==================== Initialization ====================
    document.addEventListener('DOMContentLoaded', async () => {

        Select.init('blockStatusFilter', {
            options: [
                { value: null, label: 'Tất cả' },
                { value: 0, label: 'Hoạt động' },
                { value: 1, label: 'Đã khóa' }
            ],
            defaultValue: null
        });

        DatePicker.init('createdFromFilter', {
            maxDate: DatePicker.today(),
            onChange: (value) => {
                DatePicker.setMinDate('createdToFilter', value || null);
            }
        });
        DatePicker.init('createdToFilter', {
            maxDate: DatePicker.today()
        });

        // Khởi tạo filters từ URL trước
        initFiltersFromStorageOrUrl();

        await loadRoles(currentPage, itemPerPage);

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['nameFilter', 'levelFrom', 'levelTo', 'createdFromFilter', 'createdToFilter'];
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
            initFiltersFromStorageOrUrl();
            loadRoles(currentPage, itemPerPage);
        });
    });

    // ==================== Filter Handlers ====================
    function resetFilters() {
        Select.setValue('blockStatusFilter', '');
        document.getElementById('nameFilter').value = '';
        document.getElementById('levelFrom').value = '';
        document.getElementById('levelTo').value = '';
        DatePicker.setValue('createdFromFilter', '');
        DatePicker.setValue('createdToFilter', '');

        // Reset sort về mặc định
        sortBy = 'level';
        sortOrder = 'asc';
        // Xóa URL params
        window.history.pushState({}, '', window.location.pathname);
        localStorage.removeItem(FILTERS_STORAGE_KEY);
        loadRoles(1, itemPerPage);
    }

    function getFilterParams() {
        return {
            name: document.getElementById('nameFilter').value,
            levelFrom: document.getElementById('levelFrom').value,
            levelTo: document.getElementById('levelTo').value,
            createdFromFilter: DatePicker.getValue('createdFromFilter'),
            createdToFilter: DatePicker.getValue('createdToFilter'),
            blockStatus: Select.getValue('blockStatusFilter'),
        };
    }

    function handleSubmitFilter(event) {
        event?.preventDefault();
        loadRoles(1, itemPerPage);
        return false;
    }

    // ==================== Sort Handlers ====================
    function handleSort(column) {
        sortOrder = (sortBy === column) ? (sortOrder === 'asc' ? 'desc' : 'asc') : 'desc';
        sortBy = column;
        loadRoles(1, itemPerPage);
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

    // ==================== Data Loading ====================
    async function loadRoles(page = 1, perPage = 50) {

        document.getElementById('rolesTable').innerHTML =  `
            <div class="w-fit mx-auto mt-[20dvh]">
                <div id="SPINNER_LOADING">
                    <div id="SPINNER_LOADING_CONTAINER">
                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                            ${Array(8).fill('<div></div>').join('')}
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
        selectedRoleIds.clear();

        const filters = getFilterParams();

        // Cập nhật URL parameters
        updateUrlParamsAndStorage({
            page: currentPage,
            per_page: itemPerPage,
            search: filters.name,
            level_from: filters.levelFrom,
            level_to: filters.levelTo,
            created_at_from: filters.createdFromFilter,
            created_at_to: filters.createdToFilter,
            is_block: filters.blockStatus,
            sort_by: sortBy,
            order_by: sortOrder
        });

        try {
            let queryParams = {};
            queryParams.page = currentPage;
            queryParams.per_page = itemPerPage;
            if (filters.name) queryParams.search = filters.name;
            if (filters.levelFrom) queryParams.level_from = filters.levelFrom;
            if (filters.levelTo) queryParams.level_to = filters.levelTo;
            if (filters.createdFromFilter) queryParams.created_at_from = filters.createdFromFilter;
            if (filters.createdToFilter) queryParams.created_at_to = filters.createdToFilter;
            if (filters.blockStatus !== null) queryParams.is_block = filters.blockStatus;
            if (sortBy) queryParams.sort_by = sortBy;
            if (sortOrder) queryParams.order_by = sortOrder;

            const params = new URLSearchParams(queryParams);
            const res = await apiRequest(`/roles?${params}`);
            if (res.success) {
                renderRolesTable(res?.data?.data || []);
                renderPaginationInfo_Global('paginationInfo', res?.data || {});
                renderPaginationChangeItemPerPage_Global('paginationChangeItemPerPage', itemPerPage, res?.data || {}, loadRoles);
                renderPaginationChangePage_Global('paginationChangePage', 'paginationChangeItemPerPage', res?.data || {}, loadRoles);
            }
        } catch (error) {
            document.getElementById('rolesTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <p class="text-red-600 dark:text-red-400">${error.message}</p>
                </div>
            `;
        }
    }

    // ==================== Table Rendering ====================
    function renderRolesTable(roles = []) {
        const tableContainer = document.getElementById('rolesTable');

        const PIN_COLS_STYLES = {
            checkbox: {
                sticky: 'PIN left-[0px] 3xl:left-[0px]',
                width: 'w-[40px] 3xl:w-[60px]'
            },
            id: {
                sticky: null,
                width: 'w-[60px] 3xl:w-[80px]'
            },
            name: {
                sticky: 'PIN left-[40px] 3xl:left-[60px] sticky-shadow-left',
                width: 'w-[160px] 3xl:w-[180px]'
            },
            description: {
                sticky: null,
                width: 'w-[500px] 3xl:w-[600px]'
            },
            level: {
                sticky: null,
                width: 'w-[80px] 3xl:w-[90px]'
            },
            permissions: {
                sticky: null,
                width: 'w-[1000px] 3xl:w-[1500px]'
            },
            actions: {
                sticky: 'PIN right-[0px] 3xl:right-[0px] sticky-shadow-right',
                width: 'w-[130px] 3xl:w-[150px]'
            },
        }

        const headerTable = `
            <colgroup>
                <col class="${PIN_COLS_STYLES['checkbox'].width}">
                <col class="${PIN_COLS_STYLES['id'].width}">
                <col class="${PIN_COLS_STYLES['name'].width}">
                <col class="${PIN_COLS_STYLES['description'].width}">
                <col class="${PIN_COLS_STYLES['level'].width}">
                <col class="${PIN_COLS_STYLES['permissions'].width}">
                <col class="${PIN_COLS_STYLES['actions'].width}">
            </colgroup>
            <thead class="custom-thead">
                <tr>
                    <th class="${PIN_COLS_STYLES['checkbox'].sticky} text-center">
                        <div class="flex items-center justify-center">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" >
                        </div>
                    </th>
                    <th class="HAS_SORT ${sortBy === 'id' ? 'ACTIVE' : ''}" onclick="handleSort('id')">
                        <span>
                            ID${getSortIcon('id')}
                        </span>
                    </th>
                    <th class="HAS_SORT ${sortBy === 'name' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['name'].sticky}" onclick="handleSort('name')">
                        <span>
                            Tên${getSortIcon('name')}
                        </span>
                    </th>
                    <th>Mô tả</th>
                    <th class="${sortBy === 'level' ? 'ACTIVE' : ''} HAS_SORT" onclick="handleSort('level')">
                        <span>
                            Hạng${getSortIcon('level')}
                        </span>
                    </th>
                    <th>Permissions</th>
                    <th class="${PIN_COLS_STYLES['actions'].sticky} text-right">Thao tác</th>
                </tr>
            </thead>
        `;

        if (roles.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full min-w-max border-separate border-spacing-0">
                    ${headerTable}
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="7" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 text-3xl text-gray-400 dark:text-gray-500 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-7 h-7" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M91.8 32C59.9 32 32.9 55.4 28.4 86.9L.6 281.2c-.4 3-.6 6-.6 9.1L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-125.7c0-3-.2-6.1-.6-9.1L483.6 86.9C479.1 55.4 452.1 32 420.2 32L91.8 32zm0 64l328.5 0 27.4 192-59.9 0c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7l-120.4 0c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7L64.3 288 91.8 96z"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy vai trò</p>
                                    <p class="text-gray-500 dark:text-gray-400">Hãy thử lại với các điều kiện lọc khác</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;
            return;
        }

        tableContainer.innerHTML = `
            <table class="w-full min-w-max border-separate border-spacing-0">
                ${headerTable}
                <tbody class="custom-tbody">
                    ${roles.map((role) => {
                        const permissions = (role.permissions || []).map(p => {
                            const permName = p.name || `${p.method} ${p.path}`;
                            return `<span class="inline-flex items-center gap-1.5 px-2 py-0.5 font-medium rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">${permName}</span>`;
                        }).join(' ') || '<span class="text-gray-400 dark:text-gray-500">-</span>';

                        const isChecked = selectedRoleIds.has(role.id);

                        return `
                            <tr>
                                <td class="${PIN_COLS_STYLES['checkbox'].sticky} text-center">
                                    <div class="flex items-center justify-center">
                                        <input type="checkbox" class="role-checkbox" value="${role.id}" ${isChecked ? 'checked' : ''} onchange="toggleRoleSelection(${role.id}, this.checked)">
                                    </div>
                                </td>
                                <td class="${sortBy === 'id' ? 'ACTIVE' : ''} text-center">
                                    <span>${role.id}</span>
                                </td>
                                <td class="${sortBy === 'name' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['name'].sticky}">
                                    <span class="flex flex-row items-center gap-1">
                                        ${role.name}

                                        ${
                                            role.is_block
                                            ? `
                                                <span class="ml-2 !text-red-600 dark:!text-red-300">
                                                    <svg class="w-4 h-4" viewBox="0 0 384 512" fill="currentColor">
                                                        <path d="M128 96l0 64 128 0 0-64c0-35.3-28.7-64-64-64s-64 28.7-64 64zM64 160l0-64C64 25.3 121.3-32 192-32S320 25.3 320 96l0 64c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64z"/>
                                                    </svg>
                                                </span>
                                            `
                                            : ''
                                        }
                                    </span>
                                </td>
                                <td class="">
                                    <span class="overflow-hidden text-ellipsis line-clamp-2">${role.description || '-'}</span>
                                </td>
                                <td class="${sortBy === 'level' ? 'ACTIVE' : ''} text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold rounded-xl bg-gradient-to-r from-purple-500 to-purple-500 !text-white">${role.level}</span>
                                </td>
                                <td class="align-top whitespace-normal break-words">
                                    <div class="flex flex-wrap gap-1">${permissions}</div>
                                </td>
                                <td class="${PIN_COLS_STYLES['actions'].sticky} align-top ">
                                    <div class="flex flex-wrap items-center justify-end gap-2 overflow-x-auto">
                                        <button onclick="handleGotoOtherPageOfRole(${role.id}, 'DETAIL')"
                                            class="DETAIL"
                                            title="Xem chi tiết">
                                                <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                                                    <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6-46.8 43.5-78.1 95.4-93 131.1-3.3 7.9-3.3 16.7 0 24.6 14.9 35.7 46.2 87.7 93 131.1 47.1 43.7 111.8 80.6 192.6 80.6s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1 3.3-7.9 3.3-16.7 0-24.6-14.9-35.7-46.2-87.7-93-131.1-47.1-43.7-111.8-80.6-192.6-80.6zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64-11.5 0-22.3-3-31.7-8.4-1 10.9-.1 22.1 2.9 33.2 13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-12.2-45.7-55.5-74.8-101.1-70.8 5.3 9.3 8.4 20.1 8.4 31.7z"/>
                                                </svg>
                                        </button>
                                        <button onclick="handleGotoOtherPageOfRole(${role.id}, 'EDIT')"
                                            class="EDIT"
                                            title="Chỉnh sửa">
                                                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                                                    <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                                                </svg>
                                        </button>
                                        <button onclick="openSingleDeleteModal(${role.id}, '${role.name}', '${role.description?.slice(0,30) || ''}')"
                                            class="DELETE"
                                            title="Xóa">
                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                                                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                                                </svg>
                                        </button>
                                        <button onclick="openSingleBlockModal(${role.id}, '${role.name}', ${role.is_block}, '${(role.description || '').slice(0, 50).replace(/'/g, "\\'")}')"
                                            class="${role.is_block ? 'BLOCK' : 'UNBLOCK'}"
                                            title="${role.is_block ? 'Khóa vai trò' : 'Mở khóa vai trò'}">
                                            ${
                                                role.is_block
                                                ? `
                                                    <svg class="w-4 h-4" viewBox="0 0 384 512" fill="currentColor">
                                                        <path d="M128 96l0 64 128 0 0-64c0-35.3-28.7-64-64-64s-64 28.7-64 64zM64 160l0-64C64 25.3 121.3-32 192-32S320 25.3 320 96l0 64c35.3 0 64 28.7 64 64l0 224c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 224c0-35.3 28.7-64 64-64z"/>
                                                    </svg>
                                                `
                                                : `
                                                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                                                        <path d="M384 96c0-35.3 28.7-64 64-64s64 28.7 64 64l0 32c0 17.7 14.3 32 32 32s32-14.3 32-32l0-32c0-70.7-57.3-128-128-128S320 25.3 320 96l0 64-160 0c-35.3 0-64 28.7-64 64l0 224c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-224c0-35.3-28.7-64-64-64l-32 0 0-64z"/>
                                                    </svg>
                                                `
                                            }
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('')}
                </tbody>
            </table>
        `;
        updateSelectAllCheckbox();
    }

    // Di chuyển đến trang khác của vai trò, đồng thời gửi kèm url hiện tại
    function handleGotoOtherPageOfRole(roleId = null, targetPage = 'DETAIL') {
        let url = '';
        switch (targetPage) {
            case 'DETAIL':
                url = detailRoleRouteSystemName.replace(':id', roleId);
                break;
            case 'EDIT':
                url = editRoleRouteSystemName.replace(':id', roleId);
                break;
            case 'CREATE':
                url = createRoleRouteSystemName;
                break;
            default:
                url = detailRoleRouteSystemName.replace(':id', roleId);
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }


    // ==================== Selection Handlers ====================
    function toggleRoleSelection(roleId, checked) {
        checked ? selectedRoleIds.add(roleId) : selectedRoleIds.delete(roleId);
        updateSelectAllCheckbox();
    }

    function toggleSelectAll(checked) {
        document.querySelectorAll('.role-checkbox').forEach(checkbox => {
            checkbox.checked = checked;
            const roleId = parseInt(checkbox.value);
            checked ? selectedRoleIds.add(roleId) : selectedRoleIds.delete(roleId);
        });
    }

    function updateSelectAllCheckbox() {
        const selectAll = document.getElementById('selectAll');
        if (!selectAll) return;

        const checkboxes = document.querySelectorAll('.role-checkbox');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

        selectAll.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
        selectAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
    }

    // ==================== Delete Handlers ====================
    async function openSingleDeleteModal(roleId, roleName, roleDescription) {
        DeleteModal.openSingle({
            objectName: OBJECTNAMEMODAL.ROLE,
            idDelete: roleId,
            nameValue: roleName || '-',
            descValue: roleDescription || '',
            actionFuncCallback: () => handleDeleteRoles([roleId]),
            successFuncCallback: () => loadRoles(currentPage, itemPerPage),
            failFuncCallback: () => {}
        });
    }

    async function openBulkDeleteModal() {
        const arrayIds = Array.from(selectedRoleIds);
        DeleteModal.openBulk({
            arrayIds,
            objectName: OBJECTNAMEMODAL.ROLE,
            actionFuncCallback: () => handleDeleteRoles(arrayIds),
            successFuncCallback: () => loadRoles(currentPage, itemPerPage),
            failFuncCallback: () => {}
        });
    }

    async function handleDeleteRoles(arrayIds = []) {
        try {
            const data = await apiRequest('/roles/', {
                method: 'DELETE',
                body: JSON.stringify({ role_ids: arrayIds })
            });
            return data.success || false;
        } catch {
            return false;
        }
    }

    // ==================== Block/Unblock Handlers ====================
    async function openSingleBlockModal(roleId, roleName, isCurrentlyBlocked, roleDescription = null) {
        BlockModal.openSingle({
            objectName: OBJECTNAMEMODAL.ROLE,
            idBlock: roleId,
            nameValue: roleName || '-',
            descValue: roleDescription,
            isCurrentlyBlocked: isCurrentlyBlocked,
            actionFuncCallback: () => handleBlockRoles([roleId], !isCurrentlyBlocked),
            successFuncCallback: async () => {
                const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
                NotificationModal.show(`Đã ${action} vai trò thành công`, 'success');
                await loadRoles(currentPage, itemPerPage);
            },
            failFuncCallback: () => {
                const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
                NotificationModal.show(`Không thể ${action} vai trò`, 'error');
            }
        });
    }

    async function handleBlockRoles(arrayIds = [], isBlock = true) {
        try {
            const data = await apiRequest('/roles/block', {
                method: 'POST',
                body: JSON.stringify({
                    role_ids: arrayIds,
                    is_block: isBlock
                })
            });
            return data.success || false;
        } catch (error) {
            console.error('Error blocking roles:', error);
            return false;
        }
    }

</script>
@endsection
