@extends('admin.layout')

@section('title', 'Chi tiết vai trò')

@section('description', 'Xem thông tin chi tiết và phân quyền của một vai trò')

@section('content')
<div class="h-full w-full max-w-[1800px] max-h-full mx-auto flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
    <input type="hidden" id="roleId" value="{{ $roleId ?? '' }}">

    {{-- Header Bar --}}
    <div class="w-full max-w-[1800px] mx-auto p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">

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
                onclick="handleGotoEditRolePage()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                </svg>
                <span>Chỉnh sửa</span>
            </button>
            <button
                id="btnDeleteBulk"
                onclick="openBulkDeleteModal()"
                disabled
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                </svg>
                <span>Xóa vai trò này</span>
            </button>
        </div>
    </div>

    {{-- Header Card --}}
    <div
        class="w-full mx-auto p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M256 0c4.6 0 9.2 1 13.4 2.9L457.8 82.8c22 9.3 38.4 31 38.3 57.2-.5 99.2-41.3 280.7-213.6 363.2-16.7 8-36.1 8-52.8 0-172.4-82.5-213.1-264-213.6-363.2-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.9 1 251.4 0 256 0zm0 66.8l0 378.1c138-66.8 175.1-214.8 176-303.4l-176-74.6 0 0z"/>
                    </svg>
                </span>
                <div>
                    <p class=" uppercase tracking-wide text-gray-500 dark:text-gray-400">Vai trò</p>
                    <h1 id="roleNameHeading" class="text-2xl font-semibold text-gray-900 dark:text-white">Đang tải...</h1>
                </div>
            </div>
            <p id="roleDescriptionHeading" class=" text-gray-600 dark:text-gray-400 max-w-2xl">-</p>
            <div class="flex flex-wrap items-center gap-3  text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <span class="text-blue-500">
                        <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512a256 256 0 1 1 0-512 256 256 0 1 1 0 512zm0-464a208 208 0 1 0 0 416 208 208 0 1 0 0-416zm0 304a96 96 0 1 1 0-192 96 96 0 1 1 0 192z"/>
                        </svg>
                    </span>

                    <span>ID: <span id="roleIdLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-purple-500">
                        <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M232.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L13.9 149.8C5.4 145.8 0 137.3 0 128s5.4-17.9 13.9-21.8L232.5 5.2zM48.1 218.4l164.3 75.9c27.7 12.8 59.6 12.8 87.3 0l164.3-75.9 34.1 15.8c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L13.9 277.8C5.4 273.8 0 265.3 0 256s5.4-17.9 13.9-21.8l34.1-15.8zM13.9 362.2l34.1-15.8 164.3 75.9c27.7 12.8 59.6 12.8 87.3 0l164.3-75.9 34.1 15.8c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L13.9 405.8C5.4 401.8 0 393.3 0 384s5.4-17.9 13.9-21.8z"/>
                        </svg>
                    </span>
                    <span>Level: <span id="roleLevelLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-emerald-500">
                        <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM232 344l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                        </svg>
                    </span>
                    <span>Tạo lúc: <span id="roleCreatedAtLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-amber-500">
                        <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M480.1 192l7.9 0c13.3 0 24-10.7 24-24l0-144c0-9.7-5.8-18.5-14.8-22.2S477.9 .2 471 7L419.3 58.8C375 22.1 318 0 256 0 127 0 20.3 95.4 2.6 219.5 .1 237 12.2 253.2 29.7 255.7s33.7-9.7 36.2-27.1C79.2 135.5 159.3 64 256 64 300.4 64 341.2 79 373.7 104.3L327 151c-6.9 6.9-8.9 17.2-5.2 26.2S334.3 192 344 192l136.1 0zm29.4 100.5c2.5-17.5-9.7-33.7-27.1-36.2s-33.7 9.7-36.2 27.1c-13.3 93-93.4 164.5-190.1 164.5-44.4 0-85.2-15-117.7-40.3L185 361c6.9-6.9 8.9-17.2 5.2-26.2S177.7 320 168 320L24 320c-13.3 0-24 10.7-24 24L0 488c0 9.7 5.8 18.5 14.8 22.2S34.1 511.8 41 505l51.8-51.8C137 489.9 194 512 256 512 385 512 491.7 416.6 509.4 292.5z"/>
                        </svg>
                    </span>

                    </i>
                    <span>Cập nhật: <span id="roleUpdatedAtLabel">-</span></span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-row items-stretch gap-4 3xl:gap-6 min-h-0 overflow-hidden">

        {{-- Permissions --}}
        <div class="flex-1 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col flex-shrink-0 min-h-0 overflow-hidden">
            <div class="px-6 py-5 flex flex-col gap-2 flex-shrink-0">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        {{-- <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Danh sách permissions</h2> --}}
                        <p class=" text-gray-500 dark:text-gray-400">Các quyền chi tiết của role</p>
                    </div>
                    <div class="relative w-full max-w-[250px]">
                        <input type="text" id="permissionSearchInput" placeholder="Tìm theo tên, mô tả hoặc group..."
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-4 pb-4 min-h-0 overflow-hidden">
                <div id="permissionsTableContainer" class="h-full overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 table-scroll-container">
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
                </div>
            </div>
        </div>

        {{-- Users List --}}
        <div class="flex-1 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col min-h-0 overflow-hidden">
            <div class="px-6 py-5 flex flex-col gap-2 flex-shrink-0">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        {{-- <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Danh sách người dùng</h2> --}}
                        <p class=" text-gray-500 dark:text-gray-400">Người dùng được gán vai trò này
                        </p>
                    </div>
                    <div class="relative w-full max-w-[250px]">
                        <input type="text" id="userSearchInput" placeholder="Tìm theo tên, email..."
                            class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-4 pb-4 min-h-0 overflow-hidden">
                <div id="usersTableContainer" class="h-full overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 table-scroll-container">
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
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const roleId = {{ $roleId }};
    let roleData = null;
    let rolePermissions = [];
    let roleUsers = [];

    document.addEventListener('DOMContentLoaded', async function () {
        if (!roleId) {
            NotificationModal.show('Không tìm thấy thông tin vai trò cần xem', 'error', handleBackToList);
            return;
        }

        roleData = await loadRoleDetail();

        roleUsers = roleData?.users || [];

        if (!roleData) {
            return;
        } else {
            renderRoleInfo();
            renderPermissionsTable();
            renderUsersTable();
        }


        const permissionSearchInput = document.getElementById('permissionSearchInput');
        if (permissionSearchInput) {
            permissionSearchInput.addEventListener('input', function (event) {
                renderPermissionsTable((event.target.value || '').trim().toLowerCase());
            });
        }

        const userSearchInput = document.getElementById('userSearchInput');
        if (userSearchInput) {
            userSearchInput.addEventListener('input', function (event) {
                renderUsersTable((event.target.value || '').trim().toLowerCase());
            });
        }
    });

    function handleGotoEditRolePage() {
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = '{{ route('admin.roles.edit', ['id' => $roleId]) }}' + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }

    async function loadRoleDetail() {
        try {
            const response = await apiRequest(`/roles/${roleId}`);
            if (!response.success) {
                throw new Error(response.message || 'Không thể lấy dữ liệu vai trò');
            }

            return response.data || {};
        } catch (error) {
            NotificationModal.show(error.message, 'error', handleBackToList);
            return null;
        }
    }


    function renderRoleInfo() {
        const name = roleData.name || '-';
        const description = roleData.description || '-';
        const level = roleData.level ?? '-';
        const permissionCount = (roleData.permissions || []).length;

        document.getElementById('roleNameHeading').textContent = name;
        document.getElementById('roleDescriptionHeading').textContent = description;
        document.getElementById('roleIdLabel').textContent = roleData.id ?? '-';
        document.getElementById('roleLevelLabel').textContent = level;
        document.getElementById('roleCreatedAtLabel').textContent = formatDate_Global(roleData.created_at);
        document.getElementById('roleUpdatedAtLabel').textContent = formatDate_Global(roleData.updated_at);
    }

    function renderPermissionsTable(filterText = '') {
        const container = document.getElementById('permissionsTableContainer');

        if (!roleData?.permissions?.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                    <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <span class="text-2xl text-gray-400 dark:text-gray-500">
                            <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0 160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17l0 80c0 13.3 10.7 24 24 24l80 0c13.3 0 24-10.7 24-24l0-40 40 0c13.3 0 24-10.7 24-24l0-40 40 0c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Chưa có permission nào</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Vai trò này chưa được gán quyền cụ thể</p>
                </div>
            `;
            return;
        }

        const normalizedFilter = (filterText || '').trim().toLowerCase();
        const filteredPermissions = normalizedFilter
            ? roleData?.permissions?.filter(perm => {
                const name = (perm.name || `${perm.method} ${perm.path}` || '').toLowerCase();
                const description = (perm.description || '').toLowerCase();
                const group = (perm.group || 'khác').toLowerCase();
                return name.includes(normalizedFilter) || description.includes(normalizedFilter) || group.includes(normalizedFilter);
            })
            : roleData?.permissions;

        if (!filteredPermissions.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                    <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <span class="text-2xl text-gray-400 dark:text-gray-500">
                            <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy dữ liệu phù hợp</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Thử từ khóa khác hoặc xóa bộ lọc</p>
                </div>
            `;
            return;
        }

        // Group by group name
        const grouped = {};
        filteredPermissions.forEach(perm => {
            const groupName = perm.group || 'Khác';
            grouped[groupName] = grouped[groupName] || [];
            grouped[groupName].push(perm);
        });

        const sortedGroups = Object.keys(grouped).sort((a, b) => {
            if (a === 'Khác') return 1;
            if (b === 'Khác') return -1;
            return a.localeCompare(b);
        });

        let html = `
            <div class="overflow-x-auto h-full">
                <table class="w-full table-fixed border-separate border-spacing-0">
                    <colgroup>
                        <col class="w-[45px] 3xl:w-[60px]">
                        <col class="">
                        <col class="">
                        <col class="w-[80px] 3xl:w-[100px]">
                        <col class="w-[150px]">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words truncate">ID</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words truncate">Tên</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words truncate">Mô tả</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words truncate">Method</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words truncate">Path</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        `;

        sortedGroups.forEach(groupName => {
            const groupPermissions = grouped[groupName];
            const color = getGroupColor(groupName);
            html += `
                <tr class="${color.bg} ${color.borderLeft}">
                    <td colspan="5" class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full ${color.badge}"></span>
                            <span class="text-sm font-semibold ${color.text}">${groupName}</span>
                            <span class="text-xs ${color.text} opacity-70">(${groupPermissions.length})</span>
                        </div>
                    </td>
                </tr>
            `;

            groupPermissions.forEach(perm => {
                const permName = perm.name || `${perm.method} ${perm.path}`;
                const permDescription = perm.description || '-';
                const permMethod = perm.method || '-';
                const permPath = perm.path || '-';
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                        <td class="px-4 py-3">
                            <span class="text-gray-700 dark:text-gray-300">${perm.id}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-medium text-gray-900 dark:text-white">${permName}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 dark:text-gray-400">${permDescription}</span>
                        </td>
                        <td class="px-4 py-3">
                            ${renderMethodBadge(permMethod)}
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs text-gray-600 dark:text-gray-400 font-mono truncate block" title="${permPath}">${permPath}</code>
                        </td>
                    </tr>
                `;
            });
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function renderMethodBadge(method = '-') {
        const methodUpper = (method || '-').toUpperCase();
        const mapColor = {
            GET: 'text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            POST: 'text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            PUT: 'text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            PATCH: 'text-xs bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            DELETE: 'text-xs bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        };
        const cls = mapColor[methodUpper] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
        return `<span class="inline-flex items-center px-2 py-1  font-semibold rounded ${cls}">${methodUpper}</span>`;
    }

    function getGroupColor(groupName) {
        if (!groupName) {
            return {
                bg: 'bg-gray-100 dark:bg-gray-700',
                border: 'border-gray-300 dark:border-gray-600',
                borderLeft: 'border-l-gray-300 dark:border-l-gray-600',
                text: 'text-gray-700 dark:text-gray-300',
                badge: 'bg-gray-500'
            };
        }

        const colors = [
            { bg: 'bg-blue-50 dark:bg-blue-900/20', border: 'border-blue-200 dark:border-blue-800', borderLeft: 'border-l-blue-500 dark:border-l-blue-400', text: 'text-blue-700 dark:text-blue-300', badge: 'bg-blue-500' },
            { bg: 'bg-green-50 dark:bg-green-900/20', border: 'border-green-200 dark:border-green-800', borderLeft: 'border-l-green-500 dark:border-l-green-400', text: 'text-green-700 dark:text-green-300', badge: 'bg-green-500' },
            { bg: 'bg-purple-50 dark:bg-purple-900/20', border: 'border-purple-200 dark:border-purple-800', borderLeft: 'border-l-purple-500 dark:border-l-purple-400', text: 'text-purple-700 dark:text-purple-300', badge: 'bg-purple-500' },
            { bg: 'bg-orange-50 dark:bg-orange-900/20', border: 'border-orange-200 dark:border-orange-800', borderLeft: 'border-l-orange-500 dark:border-l-orange-400', text: 'text-orange-700 dark:text-orange-300', badge: 'bg-orange-500' },
            { bg: 'bg-pink-50 dark:bg-pink-900/20', border: 'border-pink-200 dark:border-pink-800', borderLeft: 'border-l-pink-500 dark:border-l-pink-400', text: 'text-pink-700 dark:text-pink-300', badge: 'bg-pink-500' },
            { bg: 'bg-indigo-50 dark:bg-indigo-900/20', border: 'border-indigo-200 dark:border-indigo-800', borderLeft: 'border-l-indigo-500 dark:border-l-indigo-400', text: 'text-indigo-700 dark:text-indigo-300', badge: 'bg-indigo-500' },
            { bg: 'bg-teal-50 dark:bg-teal-900/20', border: 'border-teal-200 dark:border-teal-800', borderLeft: 'border-l-teal-500 dark:border-l-teal-400', text: 'text-teal-700 dark:text-teal-300', badge: 'bg-teal-500' },
            { bg: 'bg-red-50 dark:bg-red-900/20', border: 'border-red-200 dark:border-red-800', borderLeft: 'border-l-red-500 dark:border-l-red-400', text: 'text-red-700 dark:text-red-300', badge: 'bg-red-500' },
            { bg: 'bg-yellow-50 dark:bg-yellow-900/20', border: 'border-yellow-200 dark:border-yellow-800', borderLeft: 'border-l-yellow-500 dark:border-l-yellow-400', text: 'text-yellow-700 dark:text-yellow-300', badge: 'bg-yellow-500' },
            { bg: 'bg-cyan-50 dark:bg-cyan-900/20', border: 'border-cyan-200 dark:border-cyan-800', borderLeft: 'border-l-cyan-500 dark:border-l-cyan-400', text: 'text-cyan-700 dark:text-cyan-300', badge: 'bg-cyan-500' },
        ];

        let hash = 0;
        for (let i = 0; i < groupName.length; i++) {
            hash = groupName.charCodeAt(i) + ((hash << 5) - hash);
        }
        const index = Math.abs(hash) % colors.length;
        return colors[index];
    }

    function renderUsersTable(filterText = '') {
        const container = document.getElementById('usersTableContainer');

        if (!roleData?.users?.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                    <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <span class="text-2xl text-gray-400 dark:text-gray-500">
                            <svg class="w-4" viewBox="0 0 640 512" fill="currentColor">
                                <path d="M320 16a104 104 0 1 1 0 208 104 104 0 1 1 0-208zM96 88a72 72 0 1 1 0 144 72 72 0 1 1 0-144zM0 416c0-70.7 57.3-128 128-128 12.8 0 25.2 1.9 36.9 5.4-32.9 36.8-52.9 85.4-52.9 138.6l0 16c0 11.4 2.4 22.2 6.7 32L32 480c-17.7 0-32-14.3-32-32l0-32zm521.3 64c4.3-9.8 6.7-20.6 6.7-32l0-16c0-53.2-20-101.8-52.9-138.6 11.7-3.5 24.1-5.4 36.9-5.4 70.7 0 128 57.3 128 128l0 32c0 17.7-14.3 32-32 32l-86.7 0zM472 160a72 72 0 1 1 144 0 72 72 0 1 1 -144 0zM160 432c0-88.4 71.6-160 160-160s160 71.6 160 160l0 16c0 17.7-14.3 32-32 32l-256 0c-17.7 0-32-14.3-32-32l0-16z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Chưa có người dùng nào</p>
                    <p class=" text-gray-500 dark:text-gray-400">Vai trò này chưa được gán cho người dùng nào</p>
                </div>
            `;
            return;
        }

        const normalizedFilter = (filterText || '').trim().toLowerCase();
        const filteredUsers = normalizedFilter
            ? roleData?.users?.filter(user => {
                const name = (user.name || user.full_name || '').toLowerCase();
                const email = (user.email || '').toLowerCase();
                return name.includes(normalizedFilter) || email.includes(normalizedFilter);
            })
            : roleData?.users;

        if (!filteredUsers.length) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full py-12 text-center">
                    <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                        <span class="text-2xl text-gray-400 dark:text-gray-500">
                            <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                            </svg>
                        </span>
                    </div>
                    <p class="text-base font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy người dùng</p>
                    <p class=" text-gray-500 dark:text-gray-400">Thử từ khóa khác hoặc xóa bộ lọc</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="overflow-x-auto h-full">
                <table class="w-full table-fixed border-separate border-spacing-0">
                    <colgroup>
                        <col class="w-[45px] 3xl:w-[60px]">
                        <col class="w-[80px] 3xl:w-[100px]">
                        <col class="w-auto">
                        <col class="w-auto">
                        <col class="w-[180px]">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">ID</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Avatar</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Tên</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Email</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        `;

        filteredUsers.forEach(user => {
            const userName = user.name || user.full_name || '-';
            const userEmail = user.email || '-';
            const createdAt = formatDate_Global(user.created_at);
            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                    <td class="px-4 py-3">
                        <span class="text-gray-700 dark:text-gray-300">${user.id}</span>
                    </td>
                    <td class="px-4 py-3 align-center whitespace-normal break-words">
                        <img src="${user.avatar_path ?? ''}" alt="" class="m-auto w-10 aspect-square object-cover rounded-full border border-gray-300 dark:border-gray-600">
                    </td>
                    <td class="px-4 py-3">
                        ${user.fullname || '-'}
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-600 dark:text-gray-400">${userEmail}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class=" text-gray-500 dark:text-gray-400">${createdAt}</span>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function formatDate_Global(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            if (Number.isNaN(date.getTime())) return '-';
            return date.toLocaleString('vi-VN', {
                hour12: false,
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return '-';
        }
    }

    function handleBackToList() {
        window.location.href = '{{ route('admin.roles.list') }}';
    }
</script>
@endsection

