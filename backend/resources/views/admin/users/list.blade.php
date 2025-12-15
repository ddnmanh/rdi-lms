@extends('admin.layout')

@section('title', 'Quản lý Người dùng')

@section('description', 'Quản lý tất cả người dùng trong hệ thống')

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
                <button onclick="handleGotoOtherPageOfUser(null, 'CREATE')"
                    type="button"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                    </svg>
                    <span>Thêm người dùng</span>
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

            {{-- Role Filter --}}
            <div class="min-w-[200px] flex flex-col items-stretch justify-start gap-0.5">
                <label for="roleFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300 ml-2">
                    Vai trò
                </label>
                @include('components.select', [
                    'id' => 'roleFilter',
                    'placeholder' => 'Chọn vai trò...',
                    // 'searchable' => true
                ])
            </div>

            <div class="min-w-[500px] flex flex-wrap items-stretch justify-start gap-4">
                {{-- Name Filter --}}
                <div class="flex-1 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="searchFullNameFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                        Tên
                    </label>
                    <input type="text" id="searchFullNameFilter" placeholder="Tìm theo tên..."
                        {{-- onkeyup="debounceLoadUsers()" --}}
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Email Filter --}}
                <div class="flex-1 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="searchEmailFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                        Email
                    </label>
                    <input type="text" id="searchEmailFilter" placeholder="Tìm theo email..."
                        {{-- onkeyup="debounceLoadUsers()" --}}
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
            </div>


            {{-- Created From Date --}}
            <div class="flex flex-col items-stretch justify-start gap-0.5">
                <label for="createdFromFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                    Ngày tạo
                </label>
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
            <button
                type="submit"
                class="px-4 py-2.5  font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
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
            <div id="usersTable" class="w-fit min-w-full"></div>
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
    let currentPage = 1;
    let itemPerPage = 50;
    let rolesList = [];
    let deleteUserId = null;
    let isBulkDelete = false;
    let debounceTimer = null;
    let sortBy = 'created_at';
    let sortOrder = 'desc';
    let selectedUserIds = new Set();

    const createUserRouteSystemName = '{{ route('admin.users.create') }}';
    const detailUserRouteSystemName = '{{ route('admin.users.show', ['id' => ':id']) }}';
    const editUserRouteSystemName = '{{ route('admin.users.edit', ['id' => ':id']) }}';

    // Hàm đọc URL parameters
    function getUrlParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            page: params.get('page') || 1,
            role_id: params.get('role_id') || '',
            search_fullname: decodeURIComponent(params.get('search_fullname') ?? ''),
            search_email: decodeURIComponent(params.get('search_email') ?? ''),
            created_from: params.get('created_from') || '',
            created_to: params.get('created_to') || '',
            per_page: params.get('per_page') || 50,
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
                if (key === 'search_fullname' && params[key] != '') value = encodeURIComponent(params[key]);
                if (key === 'search_email' && params[key] != '') value = encodeURIComponent(params[key]);
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
        Select.setValue('roleFilter', parseInt(params.role_id) || '');
        document.getElementById('searchFullNameFilter').value = decodeURIComponent(params.search_fullname) || '';
        document.getElementById('searchEmailFilter').value = decodeURIComponent(params.search_email) || '';
        DatePicker.setValue('createdFromFilter', params.created_from);
        DatePicker.setValue('createdToFilter', params.created_to);

        // Set giá trị sort
        sortBy = params.sort_by || sortBy;
        sortOrder = params.order_by || sortOrder;
        currentPage = parseInt(params.page) || currentPage;
        itemPerPage = parseInt(params.per_page) || itemPerPage;
    }

    document.addEventListener('DOMContentLoaded', async function() {
        DatePicker.init('createdFromFilter', {
            maxDate: DatePicker.today(),
            onChange: (value) => {
                DatePicker.setMinDate('createdToFilter', value || null);
            }
        });
        DatePicker.init('createdToFilter', {
            maxDate: DatePicker.today()
        });

        // Load roles
        await loadRoles();

        // Khởi tạo Select cho roleFilter
        Select.init('roleFilter', {
            options: rolesList.map(role => ({
                value: role.id,
                label: role.name
            }))
        });

        // Khởi tạo filters từ URL trước
        initFiltersFromUrl();

        // Load roles và users
        await loadUsers(currentPage, itemPerPage);

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['searchFullNameFilter', 'searchEmailFilter', 'createdFromFilter', 'createdToFilter', 'roleFilter'];
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

        // Xử lý khi người dùng nhấn nút back/forward của trình duyệt
        window.addEventListener('popstate', function() {
            initFiltersFromUrl();
            loadUsers(currentPage);
        });
    });

    function debounceLoadUsers() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadUsers(1);
        }, 500);
    }

    function resetFilters() {
        // document.getElementById('roleFilter').value = '';
        Select.setValue('roleFilter', '');
        document.getElementById('searchFullNameFilter').value = '';
        document.getElementById('searchEmailFilter').value = '';
        DatePicker.setValue('createdFromFilter', '');
        DatePicker.setValue('createdToFilter', '');
        // Reset sort về mặc định
        sortBy = 'created_at';
        sortOrder = 'desc';
        // Xóa URL params
        window.history.pushState({}, '', window.location.pathname);
        loadUsers(1);
    }

    function handleSubmitFilter(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        loadUsers(1);
        return false;
    }

    async function loadRoles() {
        try {
            const data = await RoleProvider.handleGetRoles({
                per_page: 100
            });
            if (data.success) {
                rolesList = data?.data?.data || [];
            }
        } catch (error) {
            console.error('Error loading roles:', error);
        }
    }

    function handleSortWhenClickTableHead(column) {
        sortBy = column;
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        loadUsers(1);
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

    async function loadUsers(page = 1, itemPerPage = 50) {

        const tableContainer = document.getElementById('usersTable');
        if (!tableContainer) return;
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
        // Clear selected users when changing page or filters
        selectedUserIds.clear();
        const roleId = parseInt(Select.getValue('roleFilter')) || null;
        const name = document.getElementById('searchFullNameFilter').value;
        const email = document.getElementById('searchEmailFilter').value;
        const createdFromFilter = DatePicker.getValue('createdFromFilter');
        const createdToFilter = DatePicker.getValue('createdToFilter');

        // Cập nhật URL parameters
        updateUrlParams({
            page: currentPage,
            role_id: roleId,
            search_fullname: name,
            search_email: email,
            created_from: createdFromFilter,
            created_to: createdToFilter,
            per_page: itemPerPage,
            sort_by: sortBy,
            order_by: sortOrder
        });

        try {
            let queryParams = {};
            queryParams.page = currentPage;
            queryParams.per_page = itemPerPage;
            if (roleId) queryParams.role_id = roleId;
            if (name) queryParams.search_fullname = name;
            if (email) queryParams.search_email = email;
            if (createdFromFilter) queryParams.created_at_from = createdFromFilter;
            if (createdToFilter) queryParams.created_at_to = createdToFilter;
            if (sortBy) queryParams.sort_by = sortBy;
            if (sortOrder) queryParams.order_by = sortOrder;

            const res = await UserProvider.handleGetUsers(queryParams);

            if (res.success) {
                renderUsersTable(res?.data?.data || []);
                renderPaginationInfo_Global('paginationInfo', res?.data || {});
                renderPaginationChangeItemPerPage_Global('paginationChangeItemPerPage', itemPerPage, res?.data || {}, loadUsers);
                renderPaginationChangePage_Global('paginationChangePage', 'paginationChangeItemPerPage', res?.data || {}, loadUsers);
            }
        } catch (error) {
            document.getElementById('usersTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${error.message}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderUsersTable(users = []) {
        const tableContainer = document.getElementById('usersTable');

        const PIN_COLS_STYLES = {
            checkbox: {
                sticky: 'PIN left-[0px] 3xl:left-[0px]',
                width: 'w-[40px] 3xl:w-[80px]'
            },
            id: {
                sticky: null,
                width: 'w-[60px] 3xl:w-[110px]'
            },
            avatar: {
                sticky: 'PIN left-[40px] 3xl:left-[60px]',
                width: 'w-[80px] 3xl:w-[110px]'
            },
            fullname: {
                sticky: 'PIN left-[120px] 3xl:left-[190px] sticky-shadow-left',
                width: 'w-[250px] 3xl:w-[400px]'
            },
            email: {
                sticky: null,
                width: 'w-[250px] 3xl:w-[400px]'
            },
            roles: {
                sticky: null,
                width: 'w-[140px] 3xl:w-[160px]'
            },
            created_at: {
                sticky: null,
                width: 'w-[120px] 3xl:w-[150px]'
            },
            actions: {
                sticky: 'PIN right-[0px] 3xl:right-[0px] sticky-shadow-right',
                width: 'w-[120px] 3xl:w-[150px]'
            }
        }

        const headTable = `
            <colgroup>
                <col class="${PIN_COLS_STYLES['checkbox'].width}">
                <col class="${PIN_COLS_STYLES['id'].width}">
                <col class="${PIN_COLS_STYLES['avatar'].width}">
                <col class="${PIN_COLS_STYLES['fullname'].width}">
                <col class="${PIN_COLS_STYLES['email'].width}">
                <col class="${PIN_COLS_STYLES['roles'].width}">
                <col class="${PIN_COLS_STYLES['created_at'].width}">
                <col class="${PIN_COLS_STYLES['actions'].width}">
            </colgroup>
            <thead class="custom-thead">
                <tr>
                    <th class="${PIN_COLS_STYLES['checkbox'].sticky} align-center">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" >
                    </th>
                    <th class="${sortBy === 'id' ? 'ACTIVE' : ''} HAS_SORT" onclick="handleSortWhenClickTableHead('id')">
                        <span>
                            ID${getSortIcon('id')}
                        </span>
                    </th>
                    <th class="${PIN_COLS_STYLES['avatar'].sticky}">Avatar</th>
                    <th class="HAS_SORT ${sortBy === 'fullname' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['fullname'].sticky}" onclick="handleSortWhenClickTableHead('fullname')">
                        <span>
                            Tên${getSortIcon('fullname')}
                        </span>
                    </th>
                    <th class="HAS_SORT ${sortBy === 'email' ? 'ACTIVE' : ''}" onclick="handleSortWhenClickTableHead('email')">
                        <span>
                            Email${getSortIcon('email')}
                        </span>
                    </th>
                    <th>Vai trò</th>
                    <th onclick="handleSortWhenClickTableHead('created_at')" class="HAS_SORT ${sortBy === 'created_at' ? 'ACTIVE' : ''}">
                        <span>
                            Ngày tạo${getSortIcon('created_at')}
                        </span>
                    </th>
                    <th class="${PIN_COLS_STYLES['actions'].sticky} text-right">Thao tác</th>
                </tr>
            </thead>
        `;


        if (users.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 ">
                    ${headTable}
                    <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                        <tr>
                            <td colspan="8" class="pt-40 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-20 w-20 text-3xl text-gray-400 dark:text-gray-500 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <svg class="w-7 h-7" viewBox="0 0 512 512" fill="currentColor">
                                            <path d="M91.8 32C59.9 32 32.9 55.4 28.4 86.9L.6 281.2c-.4 3-.6 6-.6 9.1L0 416c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-125.7c0-3-.2-6.1-.6-9.1L483.6 86.9C479.1 55.4 452.1 32 420.2 32L91.8 32zm0 64l328.5 0 27.4 192-59.9 0c-12.1 0-23.2 6.8-28.6 17.7l-14.3 28.6c-5.4 10.8-16.5 17.7-28.6 17.7l-120.4 0c-12.1 0-23.2-6.8-28.6-17.7l-14.3-28.6c-5.4-10.8-16.5-17.7-28.6-17.7L64.3 288 91.8 96z"/>
                                        </svg>
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy người dùng</p>
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
                ${headTable}
                <tbody class="custom-tbody">
                    ${
                        users.map(user => {
                            const roleBadge = user?.roles?.map(r => {
                                if (r.name === 'ROOT') {
                                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-red-500 to-red-500 !text-white">${r.name}</span>`;
                                } else if (r.name === 'ADMIN') {
                                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-amber-500 to-amber-500 !text-white">${r.name}</span>`;
                                } else if (r.name === 'TEACHER') {
                                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-500 !text-white">${r.name}</span>`;
                                } else if (r.name === 'STUDENT') {
                                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-500 to-blue-500 !text-white">${r.name}</span>`;
                                } else {
                                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-gray-500 to-gray-500 !text-white">${r.name}</span>`;
                                }
                            }).join(' ');

                            const isChecked = selectedUserIds.has(user.id);
                            return `
                                <tr class="${isChecked ? '!bg-red-500' : ''}">
                                    <td class="${PIN_COLS_STYLES['checkbox'].sticky} align-center">
                                        <input type="checkbox" class="user-checkbox" value="${user.id}" ${isChecked ? 'checked' : ''} onchange="toggleUserSelection(${user.id}, this.checked)">
                                    </td>
                                    <td class="${sortBy === 'id' ? 'ACTIVE' : ''} text-center">
                                        <span class="text-gray-600 dark:text-gray-300">${user.id}</span>
                                    </td>
                                    <td class="${PIN_COLS_STYLES['avatar'].sticky} align-center">
                                        <img src="${user.avatar_path ?? ''}" alt="" class="m-auto w-10 aspect-square object-cover rounded-full bg-gray-200 dark:bg-gray-700">
                                    </td>
                                    <td class="${sortBy === 'fullname' ? 'ACTIVE' : ''} ${PIN_COLS_STYLES['fullname'].sticky}">
                                        <span>${user.fullname || '-'}</span>
                                    </td>
                                    <td class="${sortBy === 'email' ? 'ACTIVE' : ''} text-gray-600 dark:text-gray-300 break-all [overflow-wrap:anywhere]">
                                        <span>${user.email}</span>
                                    </td>
                                    <td class="px-4 py-3 align-center whitespace-normal break-words">
                                        ${roleBadge}
                                    </td>
                                    <td class="${sortBy === 'created_at' ? 'ACTIVE' : ''} text-gray-600 dark:text-gray-300">
                                        ${formatDate_Global(user.created_at)}
                                    </td>
                                    <td class="${PIN_COLS_STYLES['actions'].sticky}">
                                        <div class="flex flex-nowrap items-center justify-end gap-2 overflow-x-auto">
                                            <button
                                                type="button"
                                                onclick="handleGotoOtherPageOfUser(${user.id}, 'DETAIL')"
                                                class="DETAIL"
                                                title="Xem chi tiết">
                                                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                                                        <path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6-46.8 43.5-78.1 95.4-93 131.1-3.3 7.9-3.3 16.7 0 24.6 14.9 35.7 46.2 87.7 93 131.1 47.1 43.7 111.8 80.6 192.6 80.6s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1 3.3-7.9 3.3-16.7 0-24.6-14.9-35.7-46.2-87.7-93-131.1-47.1-43.7-111.8-80.6-192.6-80.6zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64-11.5 0-22.3-3-31.7-8.4-1 10.9-.1 22.1 2.9 33.2 13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-12.2-45.7-55.5-74.8-101.1-70.8 5.3 9.3 8.4 20.1 8.4 31.7z"/>
                                                    </svg>
                                            </button>
                                            <button
                                                type="button"
                                                onclick="handleGotoOtherPageOfUser(${user.id}, 'EDIT')"
                                                class="EDIT"
                                                title="Chỉnh sửa">
                                                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                                                    <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                                                </svg>
                                            </button>
                                            <button onclick="openSingleDeleteModal(${user.id}, '${(user.fullname || '').replace(/'/g, "\\'")}', '${user.email.replace(/'/g, "\\'")}')"
                                                class="DELETE"
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

        tableContainer.innerHTML = html;
        updateSelectAllCheckbox();
    }

    function handleGotoOtherPageOfUser(userId = null, targetPage = 'DETAIL') {
        let url = '';
        switch (targetPage) {
            case 'DETAIL':
                url = detailUserRouteSystemName.replace(':id', userId);
                break;
            case 'EDIT':
                url = editUserRouteSystemName.replace(':id', userId);
                break;
            case 'CREATE':
                url = createUserRouteSystemName;
                break;
            default:
                url = detailUserRouteSystemName.replace(':id', userId);
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }


    function toggleUserSelection(userId, checked) {
        if (checked) {
            selectedUserIds.add(userId);
        } else {
            selectedUserIds.delete(userId);
        }
        updateSelectAllCheckbox();
        toggleBulkDeleteBtn();
    }

    function toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const userId = parseInt(checkbox.value);
            if (checked) {
                selectedUserIds.add(userId);
            } else {
                selectedUserIds.delete(userId);
            }
        });
        toggleBulkDeleteBtn();
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        if (!selectAllCheckbox) return;

        const checkboxes = document.querySelectorAll('.user-checkbox');
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

        if (selectedUserIds.size > 0) {
            btnDeleteBulk.disabled = false;
            btnDeleteBulk.style.opacity = '1';
            btnDeleteBulk.style.cursor = 'pointer';
            selectedCount.textContent = selectedUserIds.size;
        } else {
            btnDeleteBulk.disabled = true;
            btnDeleteBulk.style.opacity = '0.5';
            btnDeleteBulk.style.cursor = 'not-allowed';
            selectedCount.textContent = '0';
        }
    }


    // Xử lý khi xóa một mục
    async function openSingleDeleteModal(userId, userName, userEmail) {
        DeleteModal.openSingle({
            objectName: OBJECTNAMEMODAL.USER,
            idDelete: userId,
            nameValue: userName || '-',
            descValue: userEmail || '',
            actionFuncCallback: async () => {
                const result = await UserProvider.handleDeleteUsers([userId]);
                return result.success;
            },
            successFuncCallback: () => loadUsers(currentPage),
            failFuncCallback: () => {}
        });
    }

    // Xử lý khi xóa nhiều mục cùng lúc
    async function openBulkDeleteModal() {
        DeleteModal.openBulk({
            arrayIds: Array.from(selectedUserIds),
            objectName: OBJECTNAMEMODAL.USER,
            actionFuncCallback: async () => {
                const result = await UserProvider.handleDeleteUsers(Array.from(selectedUserIds));
                return result.success;
            },
            successFuncCallback: () => loadUsers(currentPage),
            failFuncCallback: () => {}
        });
    }

</script>

@endsection
