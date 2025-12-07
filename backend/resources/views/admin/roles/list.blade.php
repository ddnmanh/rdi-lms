@extends('admin.layout')

@section('title', 'Quản lý Roles')
@section('description', 'Quản lý tất cả vai trò trong hệ thống')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
    <div class="flex flex-col items-stretch justify-start gap-2.5">
        {{-- Actions Bar --}}
        <div class="flex items-center justify-start gap-3">
            <a href="{{ route('admin.roles.create') }}"
                class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                <i class="fas fa-plus"></i>
                <span>Thêm vai trò</span>
            </a>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">

                {{-- Block Status Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="blockStatusFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Trạng thái</label>
                    <select id="blockStatusFilter"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Tất cả</option>
                        <option value="0">Hoạt động</option>
                        <option value="1">Đã khóa</option>
                    </select>
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
                        <i class="fa-solid fa-minus text-gray-300 dark:text-gray-500"></i>
                        <input type="number" id="levelTo" min="1" max="255" placeholder="255"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                {{-- Created Date Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="createdFrom" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">Ngày tạo</label>
                    <div class="flex items-center gap-1">
                        <input type="date" id="createdFrom"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <i class="fa-solid fa-minus text-gray-300 dark:text-gray-500"></i>
                        <input type="date" id="createdTo"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

            </div>

            <div class="flex items-end gap-3">
                <button type="button" onclick="resetFilters()"
                    class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    <i class="fas fa-redo"></i>
                </button>
                <button type="submit"
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
            <div id="rolesTable"></div>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                <span id="paginationInfo" class="text-gray-600 dark:text-gray-400 text-left"></span>
                <div class="flex items-center justify-center gap-2">
                    <label for="itemPerPage" class="text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>
                    <select id="itemPerPage" onchange="loadRoles(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div id="pagination" class="flex items-center justify-end gap-1 3xl:text-base"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // ==================== Constants & State ====================
    const FILTER_INPUT_IDS = ['nameFilter', 'levelFrom', 'levelTo', 'createdFrom', 'createdTo', 'blockStatusFilter'];
    const SPINNER_HTML = `
        <div class="w-fit mx-auto mt-[20dvh]">
            <div id="SPINNER_LOADING">
                <div id="SPINNER_LOADING_CONTAINER">
                    <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                        ${Array(8).fill('<div></div>').join('')}
                    </div>
                </div>
                <div id="SPINNER_LOADING_ICON">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    `;

    let currentPage = 1;
    let sortBy = 'level';
    let sortOrder = 'asc';
    let selectedRoleIds = new Set();


    // ==================== Initialization ====================
    document.addEventListener('DOMContentLoaded', async () => {
        await loadRoles();
        setupFilterInputs();
    });

    function setupFilterInputs() {
        FILTER_INPUT_IDS.forEach(inputId => {
            const input = document.getElementById(inputId);
            input?.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    input.closest('form')?.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                }
            });
        });
    }

    // ==================== Filter Handlers ====================
    function resetFilters() {
        FILTER_INPUT_IDS.forEach(id => document.getElementById(id).value = '');
        loadRoles(1);
    }

    function handleSubmitFilter(event) {
        event?.preventDefault();
        loadRoles(1);
        return false;
    }

    function getFilterParams() {
        return {
            name: document.getElementById('nameFilter').value,
            levelFrom: document.getElementById('levelFrom').value,
            levelTo: document.getElementById('levelTo').value,
            createdFrom: document.getElementById('createdFrom').value,
            createdTo: document.getElementById('createdTo').value,
            blockStatus: document.getElementById('blockStatusFilter').value,
            itemPerPage: document.getElementById('itemPerPage').value || 50
        };
    }

    // ==================== Sort Handlers ====================
    function handleSort(column) {
        sortOrder = (sortBy === column) ? (sortOrder === 'asc' ? 'desc' : 'asc') : 'desc';
        sortBy = column;
        loadRoles(1);
    }

    function getSortIcon(column) {
        if (sortBy !== column) return '<i class="fas fa-sort text-gray-300 dark:text-gray-500 ml-1"></i>';
        return sortOrder === 'asc'
            ? '<i class="fas fa-sort-up text-white ml-1"></i>'
            : '<i class="fas fa-sort-down text-white ml-1"></i>';
    }

    function getHeaderClass(column) {
        const base = 'px-4 py-3 text-left sticky top-0 z-20 shadow-sm whitespace-normal break-words cursor-pointer hover:bg-blue-700 dark:hover:bg-gray-600 transition-colors select-none';
        const active = sortBy === column ? ' bg-blue-700 dark:bg-gray-600' : ' bg-blue-600 dark:bg-gray-700';
        return base + active;
    }

    function getCellClass(column) {
        const base = 'px-4 py-3 whitespace-normal break-words';
        return sortBy === column ? base + ' bg-blue-50 dark:bg-gray-700/40' : base;
    }

    // ==================== Data Loading ====================
    async function loadRoles(page = 1) {
        document.getElementById('rolesTable').innerHTML = SPINNER_HTML;

        currentPage = page;
        selectedRoleIds.clear();

        const filters = getFilterParams();
        const params = new URLSearchParams({
            page,
            per_page: filters.itemPerPage,
            ...(filters.name && { search: filters.name }),
            ...(filters.levelFrom && { level_from: filters.levelFrom }),
            ...(filters.levelTo && { level_to: filters.levelTo }),
            ...(filters.createdFrom && { created_at_from: filters.createdFrom }),
            ...(filters.createdTo && { created_at_to: filters.createdTo }),
            ...(filters.blockStatus !== '' && { is_block: filters.blockStatus }),
            ...(sortBy && { sort_by: sortBy }),
            ...(sortOrder && { order_by: sortOrder })
        });

        try {
            const res = await apiRequest(`/roles?${params}`);
            if (res.success) {
                renderRolesTable(res?.data?.data || []);
                renderPagination(res?.data || {});
                renderPaginationInfo(res?.data || {});
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
        const headerTable = createTableHeader();

        if (roles.length === 0) {
            tableContainer.innerHTML = createEmptyState(headerTable);
            return;
        }

        tableContainer.innerHTML = `
            <table class="w-full table-fixed border-separate border-spacing-0">
                ${headerTable}
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                    ${roles.map(createRoleRow).join('')}
                </tbody>
            </table>
        `;
        updateSelectAllCheckbox();
    }

    function createTableHeader() {
        return `
            <colgroup>
                <col class="w-[40px] 3xl:w-[60px]">
                <col class="w-[60px] 3xl:w-[80px]">
                <col class="w-[160px] 3xl:w-[180px]">
                <col class="">
                <col class="w-[80px] 3xl:w-[90px]">
                <col class="">
                <col class="w-[130px] 3xl:w-[150px]">
            </colgroup>
            <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                <tr>
                    <th class="px-4 py-3 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th onclick="handleSort('id')" class="${getHeaderClass('id')}">ID${getSortIcon('id')}</th>
                    <th onclick="handleSort('name')" class="${getHeaderClass('name')}">Tên${getSortIcon('name')}</th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                    <th onclick="handleSort('level')" class="${getHeaderClass('level')}">Hạng${getSortIcon('level')}</th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Permissions</th>
                    <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                </tr>
            </thead>
        `;
    }

    function createEmptyState(headerTable) {
        return `
            <table class="w-full table-fixed border-separate border-spacing-0">
                ${headerTable}
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                    <tr>
                        <td colspan="7" class="pt-40 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                    <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                </div>
                                <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Không tìm thấy vai trò</p>
                                <p class="text-gray-500 dark:text-gray-400">Hãy thử lại với các điều kiện lọc khác</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        `;
    }

    function createRoleRow(role) {
        const permissions = (role.permissions || []).map(p => {
            const permName = p.name || `${p.method} ${p.path}`;
            const tooltip = p.description ? `title="${p.description.replace(/"/g, '&quot;')}"` : '';
            return `<span ${tooltip} class="inline-flex items-center gap-1.5 px-2 py-0.5 font-medium rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 cursor-help">${permName}</span>`;
        }).join(' ') || '<span class="text-gray-400 dark:text-gray-500">-</span>';

        const isChecked = selectedRoleIds.has(role.id);
        const description = role.description || '<span class="text-gray-400 dark:text-gray-500">-</span>';

        // Xác định trạng thái khóa và style cho button
        const isBlocked = role.is_block || false;
        const blockButtonColor = isBlocked ? 'green' : 'orange';
        const blockButtonIcon = isBlocked ? 'fa-lock-open' : 'fa-lock';
        const blockButtonTitle = isBlocked ? 'Mở khóa vai trò' : 'Khóa vai trò';

        return `
            <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 ">
                <td class="px-4 py-3 text-center">
                    <input type="checkbox" class="role-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                        value="${role.id}" ${isChecked ? 'checked' : ''} onchange="toggleRoleSelection(${role.id}, this.checked)">
                </td>
                <td class="${getCellClass('id')} text-center">
                    <span class="text-gray-600 dark:text-gray-300">${role.id}</span>
                </td>
                <td class="${getCellClass('name')} text-gray-600 dark:text-gray-300 font-medium">
                    ${role.name}
                    ${isBlocked ? '<span class="ml-2 text-red-600 dark:text-red-300"><i class="fas fa-lock"></i></span>' : ''}
                </td>
                <td class="px-4 py-3">
                    <span class="text-gray-600 dark:text-gray-300 overflow-hidden text-ellipsis line-clamp-2">${description}</span>
                </td>
                <td class="${getCellClass('level')} text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 font-bold rounded-xl bg-gradient-to-r from-purple-500 to-purple-500 text-white">${role.level}</span>
                </td>
                <td class="px-4 py-3 align-top whitespace-normal break-words">
                    <div class="flex flex-wrap gap-1">${permissions}</div>
                </td>
                <td class="px-4 py-3 align-top">
                    <div class="flex flex-wrap items-center justify-end gap-2 overflow-x-auto">
                        ${createActionButton(`/admin/roles/${role.id}/show`, 'blue', 'fa-eye', 'Xem chi tiết')}
                        ${createActionButton(`/admin/roles/${role.id}/edit`, 'amber', 'fa-pen', 'Chỉnh sửa')}
                        <button onclick="openSingleDeleteModal(${role.id}, '${role.name}', '${role.description?.slice(0,30) || ''}')"
                            class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                            title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button onclick="openSingleBlockModal(${role.id}, '${role.name}', ${isBlocked}, '${(role.description || '').slice(0, 50).replace(/'/g, "\\'")}')"
                            class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-${blockButtonColor}-500 hover:border-${blockButtonColor}-600 text-${blockButtonColor}-500 hover:text-${blockButtonColor}-600 hover:bg-${blockButtonColor}-50 dark:hover:bg-${blockButtonColor}-900/20 transition-all duration-300"
                            title="${blockButtonTitle}">
                            <i class="fas ${blockButtonIcon}"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function createActionButton(href, color, icon, title) {
        return `
            <a href="${href}"
                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-${color}-500 hover:border-${color}-600 text-${color}-500 hover:text-${color}-600 hover:bg-${color}-50 dark:hover:bg-${color}-900/20 transition-all duration-300"
                title="${title}">
                <i class="fa-solid ${icon}"></i>
            </a>
        `;
    }

    // ==================== Pagination Rendering ====================
    function renderPaginationInfo(data = {}) {
        document.getElementById('paginationInfo').innerHTML = `
            Hiển thị <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.from || 0}</span>
            – <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.to || 0}</span>
            trong <span class="font-semibold text-gray-900 dark:text-gray-100">${data?.total || 0}</span>
        `;
    }

    function renderPagination(data) {
        const { current_page: current, last_page: last } = data;
        if (last <= 1) {
            document.getElementById('pagination').innerHTML = '';
            return;
        }

        const pages = generatePageNumbers(current, last);
        const html = [
            createPaginationButton('Trước', current > 1 ? current - 1 : null),
            ...pages.map(page => createPaginationButton(page, page, page === current)),
            createPaginationButton('Sau', current < last ? current + 1 : null)
        ].join('');

        document.getElementById('pagination').innerHTML = html;
    }

    function generatePageNumbers(current, last) {
        if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1);

        if (current <= 3) {
            return [1, 2, 3, '...', last];
        } else if (current >= last - 2) {
            return [1, '...', last - 2, last - 1, last];
        } else {
            return [1, '...', current - 1, current, current + 1, '...', last];
        }
    }

    function createPaginationButton(label, page, isActive = false) {
        if (label === '...') {
            return '<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>';
        }

        if (!page) {
            return `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">${label}</button>`;
        }

        if (isActive) {
            return `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${label}</button>`;
        }

        return `<button onclick="loadRoles(${page})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${label}</button>`;
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
        openSingleDeleteModalGeneric_Global({
            objectName: OBJECTNAMEMODAL.ROLE,
            idDelete: roleId,
            nameValue: roleName || '-',
            descValue: roleDescription || '',
            actionFuncCallback: () => handleDeleteRoles([roleId]),
            successFuncCallback: () => loadRoles(currentPage),
            failFuncCallback: () => {}
        });
    }

    async function openBulkDeleteModal() {
        const arrayIds = Array.from(selectedRoleIds);
        openBulkDeleteModalGeneric_Global({
            arrayIds,
            objectName: OBJECTNAMEMODAL.ROLE,
            actionFuncCallback: () => handleDeleteRoles(arrayIds),
            successFuncCallback: () => loadRoles(currentPage),
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
        openSingleBlockModalGeneric_Global({
            objectName: OBJECTNAMEMODAL.ROLE,
            idBlock: roleId,
            nameValue: roleName || '-',
            descValue: roleDescription,
            isCurrentlyBlocked: isCurrentlyBlocked,
            actionFuncCallback: () => handleBlockRoles([roleId], !isCurrentlyBlocked),
            successFuncCallback: async () => {
                const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
                showNotificationModel_Global(`Đã ${action} vai trò thành công`, 'success');
                await loadRoles(currentPage);
            },
            failFuncCallback: () => {
                const action = isCurrentlyBlocked ? 'mở khóa' : 'khóa';
                showNotificationModel_Global(`Không thể ${action} vai trò`, 'error');
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
