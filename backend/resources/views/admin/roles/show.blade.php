@extends('admin.layout')

@section('title', 'Chi tiết vai trò')

@section('description', 'Xem thông tin chi tiết và phân quyền của một vai trò')

@section('content')
<div class="h-full w-full max-w-[1800px] max-h-full mx-auto flex flex-col items-stretch justify-start gap-4 3xl:gap-6">
    <input type="hidden" id="roleId" value="{{ $roleId ?? '' }}">

    {{-- Header Card --}}
    <div
        class="w-full mx-auto p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="space-y-1.5">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                <div>
                    <p class=" uppercase tracking-wide text-gray-500 dark:text-gray-400">Vai trò</p>
                    <h1 id="roleNameHeading" class="text-2xl font-semibold text-gray-900 dark:text-white">Đang tải...</h1>
                </div>
            </div>
            <p id="roleDescriptionHeading" class=" text-gray-600 dark:text-gray-400 max-w-2xl">-</p>
            <div class="flex flex-wrap items-center gap-3  text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-circle-dot text-blue-500"></i>
                    <span>ID: <span id="roleIdLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-layer-group text-purple-500"></i>
                    <span>Level: <span id="roleLevelLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-emerald-500"></i>
                    <span>Tạo lúc: <span id="roleCreatedAtLabel">-</span></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate text-amber-500"></i>
                    <span>Cập nhật: <span id="roleUpdatedAtLabel">-</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full lg:w-auto">
            <a href="{{ route('admin.roles.list') }}"
                class="flex-1 lg:flex-none px-4 py-2.5  font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300 text-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Quay lại
            </a>
            <a id="roleEditButton" href="#"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                <i class="fas fa-pen me-2"></i>
                Chỉnh sửa
            </a>
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
                            <i class="fa-solid fa-magnifying-glass"></i>
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
                                <i class="fas fa-graduation-cap"></i>
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
                            <i class="fa-solid fa-magnifying-glass"></i>
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
                                <i class="fas fa-graduation-cap"></i>
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
            showNotificationModel_Global('Không tìm thấy thông tin vai trò cần xem', 'error', handleBackToList);
            return;
        }

        document.getElementById('roleEditButton').setAttribute('href', `/admin/roles/${roleId}/edit`);

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

    async function loadRoleDetail() {
        try {
            const response = await apiRequest(`/roles/${roleId}`);
            if (!response.success) {
                throw new Error(response.message || 'Không thể lấy dữ liệu vai trò');
            }

            return response.data || {};
        } catch (error) {
            showNotificationModel_Global(error.message, 'error', handleBackToList);
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
                        <i class="fas fa-key text-2xl text-gray-400 dark:text-gray-500"></i>
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
                        <i class="fas fa-search text-2xl text-gray-400 dark:text-gray-500"></i>
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
                        <i class="fas fa-users text-2xl text-gray-400 dark:text-gray-500"></i>
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
                        <i class="fas fa-search text-2xl text-gray-400 dark:text-gray-500"></i>
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

