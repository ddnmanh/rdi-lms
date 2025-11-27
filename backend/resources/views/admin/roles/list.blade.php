@extends('admin.layout')

@section('title', 'Quản lý Roles')

@section('description', 'Quản lý tất cả vai trò trong hệ thống')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 2xl:gap-4">
    <div class="flex flex-col items-stretch justify-start gap-2.5">
        {{-- Actions Bar --}}
        <div class="flex items-center justify-start gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.roles.create') }}"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                    <span>Thêm vai trò</span>
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 2xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Name Filter --}}
                <div class="flex-1 min-w-40 max-w-80 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="nameFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                        Tên
                    </label>
                    <input type="text" id="nameFilter" placeholder="Tìm theo tên..."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>

                {{-- Level From Filter --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="levelFrom" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                        Level
                    </label>
                    <div class="flex items-center gap-1">
                        <input type="number" id="levelFrom" min="1" max="255" placeholder="1"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <i class="fa-solid fa-minus text-gray-300 dark:text-gray-500"></i>
                        <input type="number" id="levelTo" min="1" max="255" placeholder="255"
                            class="w-full px-4 py-1.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
                </div>

                {{-- Created From Date --}}
                <div class="flex flex-col items-stretch justify-start gap-0.5">
                    <label for="createdFrom" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                        Ngày tạo
                    </label>
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
            <div id="rolesTable">
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

        {{-- Pagination Footer --}}
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                {{-- Info --}}
                <span id="paginationInfo" class=" text-gray-600 dark:text-gray-400 text-left"></span>

                {{-- Items per page selector --}}
                <div class="flex items-center justify-center gap-2">
                    <label for="itemPerPage" class=" text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>
                    <select id="itemPerPage" onchange="loadRoles(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100  focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Custom Pagination --}}
                <div id="pagination" class="flex items-center justify-end gap-1  2xl:text-base"></div>
            </div>
        </div>
    </div>
</div>


<script>
    let currentPage = 1;
    let deleteRoleId = null;
    let sortBy = 'level';
    let sortOrder = 'asc';
    let selectedRoleIds = new Set();

    document.addEventListener('DOMContentLoaded', async function() {
        await loadRoles();

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['nameFilter', 'levelFrom', 'levelTo', 'createdFrom', 'createdTo'];
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
        document.getElementById('nameFilter').value = '';
        document.getElementById('levelFrom').value = '';
        document.getElementById('levelTo').value = '';
        document.getElementById('createdFrom').value = '';
        document.getElementById('createdTo').value = '';
        loadRoles(1);
    }

    function handleSubmitFilter(event) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        loadRoles(1);
        return false;
    }

    function handleSort(column) {
        if (sortBy === column) {
            sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        } else {
            sortBy = column;
            sortOrder = 'desc';
        }
        loadRoles(1);
    }

    function getSortIcon(column) {
        if (sortBy !== column) {
            return '<i class="fas fa-sort text-gray-300 dark:text-gray-500  ml-1"></i>';
        } else if (sortOrder === 'asc') {
            return '<i class="fas fa-sort-up text-white  ml-1"></i>';
        } else {
            return '<i class="fas fa-sort-down text-white  ml-1"></i>';
        }
    }

    function getHeaderClass(column) {
        const baseClass = 'px-4 py-3 text-left sticky top-0 z-20 shadow-sm whitespace-normal break-words cursor-pointer hover:bg-blue-700 dark:hover:bg-gray-600 transition-colors select-none';
        if (sortBy === column) {
            return baseClass + ' bg-blue-700 dark:bg-gray-600';
        } else {
            return baseClass + ' bg-blue-600 dark:bg-gray-700';
        }
    }

    function getCellClass(column) {
        const baseClass = 'px-4 py-3 whitespace-normal break-words';
        if (sortBy === column) {
            return baseClass + ' bg-blue-50 dark:bg-gray-700/40';
        } else {
            return baseClass;
        }
    }

    function renderTableLoading() {
        const tableContainer = document.getElementById('rolesTable');
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
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                </div>
            </div>
        `;
    }

    async function loadRoles(page = 1) {
        renderTableLoading();

        currentPage = page;
        // Clear selections khi đổi trang hoặc lọc
        selectedRoleIds.clear();
        const name = document.getElementById('nameFilter').value;
        const levelFrom = document.getElementById('levelFrom').value;
        const levelTo = document.getElementById('levelTo').value;
        const createdFrom = document.getElementById('createdFrom').value;
        const createdTo = document.getElementById('createdTo').value;
        const itemPerPage = document.getElementById('itemPerPage').value || 50;

        try {
            let url = `/roles?page=${page}&per_page=${itemPerPage}`;
            if (name) url += `&search=${encodeURIComponent(name)}`;
            if (levelFrom) url += `&level_from=${levelFrom}`;
            if (levelTo) url += `&level_to=${levelTo}`;
            if (createdFrom) url += `&created_at_from=${createdFrom}`;
            if (createdTo) url += `&created_at_to=${createdTo}`;
            if (sortBy) url += `&sort_by=${sortBy}`;
            if (sortOrder) url += `&order_by=${sortOrder}`;

            const res = await apiRequest(url);

            if (res.success) {
                renderRolesTable(res?.data?.data || []);
                renderPagination(res?.data || {});
                renderPaginationInfo(res?.data || {});
            }
        } catch (error) {
            document.getElementById('rolesTable').innerHTML = `
                <div class="flex items-center justify-center pt-40">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${error.message}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderRolesTable(roles = []) {
        const tableContainer = document.getElementById('rolesTable');

        if (roles.length === 0) {
            tableContainer.innerHTML = `
                <table class="w-full table-fixed border-separate border-spacing-0 ">
                    <colgroup>
                        <col class="w-[40px] 2xl:w-[80px]">
                        <col class="w-[60px] 2xl:w-[120px]">
                        <col class="w-[100px] 2xl:w-[200px]">
                        <col class="w-[140px] 2xl:w-[240px]">
                        <col class="w-[80px] 2xl:w-[160px]">
                        <col class="">
                        <col class="w-[120px] 2xl:w-[240px]">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th class="px-4 py-3 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </th>
                            <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                                ID${getSortIcon('id')}
                            </th>
                            <th onclick="handleSort('name')" class="${getHeaderClass('name')}">
                                Tên${getSortIcon('name')}
                            </th>
                            <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                            <th onclick="handleSort('level')" class="${getHeaderClass('level')}">
                                Hạng${getSortIcon('level')}
                            </th>
                            <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Permissions</th>
                            <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                        </tr>
                    </thead>
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
            return;
        }

        let html = `
            <table class="w-full table-fixed border-separate border-spacing-0 ">
                <colgroup>
                    <col class="w-[40px] 2xl:w-[80px]">
                    <col class="w-[60px] 2xl:w-[120px]">
                    <col class="w-[100px] 2xl:w-[200px]">
                    <col class="w-[140px] 2xl:w-[240px]">
                    <col class="w-[80px] 2xl:w-[160px]">
                    <col class="">
                    <col class="w-[120px] 2xl:w-[240px]">
                </colgroup>
                <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-center sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this.checked)" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </th>
                        <th onclick="handleSort('id')" class="${getHeaderClass('id')}">
                            ID${getSortIcon('id')}
                        </th>
                        <th onclick="handleSort('name')" class="${getHeaderClass('name')}">
                            Tên${getSortIcon('name')}
                        </th>
                        <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                        <th onclick="handleSort('level')" class="${getHeaderClass('level')}">
                            Hạng${getSortIcon('level')}
                        </th>
                        <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Permissions</th>
                        <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
        `;

        roles.forEach(role => {
            const permissions = (role.permissions || []).map(p => {
                const permName = p.name || `${p.method} ${p.path}`;
                const permDescription = p.description || '';
                const tooltip = permDescription ? `title="${permDescription.replace(/"/g, '&quot;')}"` : '';
                return `<span ${tooltip} class="inline-flex items-center gap-1.5 px-2 py-0.5  font-medium rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 cursor-help">${permName}</span>`;
            }).join(' ') || '<span class="text-gray-400 dark:text-gray-500">-</span>';

            const isChecked = selectedRoleIds.has(role.id);
            html += `
                <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox"
                            class="role-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            value="${role.id}"
                            ${isChecked ? 'checked' : ''}
                            onchange="toggleRoleSelection(${role.id}, this.checked)">
                    </td>
                    <td class="${getCellClass('id')} text-center">
                        <span class="text-gray-600 dark:text-gray-300">${role.id}</span>
                    </td>
                    <td class="${getCellClass('name')} text-gray-600 dark:text-gray-300 font-medium">
                        ${role.name}
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-600 dark:text-gray-300 overflow-hidden text-ellipsis line-clamp-2">${role.description || '<span class="text-gray-400 dark:text-gray-500">-</span>'}</span>
                    </td>
                    <td class="${getCellClass('level')} text-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1  font-bold rounded-xl bg-gradient-to-r from-purple-500 to-purple-500 text-white">${role.level}</span>
                    </td>
                    <td class="px-4 py-3 align-top whitespace-normal break-words">
                        <div class="flex flex-wrap gap-1">${permissions}</div>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-nowrap items-center justify-end gap-2 overflow-x-auto">
                            <a href="/admin/roles/${role.id}/show"
                                class="inline-flex shrink-0 size-7 2xl:size-8 items-center justify-center rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                title="Xem chi tiết">
                                <i class="fas fa-eye  2xl:"></i>
                            </a>
                            <a href="/admin/roles/${role.id}/edit"
                                class="inline-flex shrink-0 size-7 2xl:size-8 items-center justify-center rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                title="Chỉnh sửa">
                                <i class="fa-solid fa-pen  "></i>
                            </a>
                            <button onclick="openSingleDeleteModal(${role.id}, '${role.name}', '${role.description?.slice(0,30)}')"
                                class="inline-flex shrink-0 size-7 2xl:size-8 items-center justify-center rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                                title="Xóa">
                                <i class="fas fa-trash  "></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
        tableContainer.innerHTML = html;
        updateSelectAllCheckbox();
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
            html += `<button onclick="loadRoles(${current - 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Trước</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Trước</button>`;
        }

        // Page Numbers
        if (last <= 7) {
            for (let i = 1; i <= last; i++) {
                if (i === current) {
                    html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                } else {
                    html += `<button onclick="loadRoles(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                }
            }
        } else {
            if (current <= 3) {
                for (let i = 1; i <= 3; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadRoles(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadRoles(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            } else if (current >= last - 2) {
                html += `<button onclick="loadRoles(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = last - 2; i <= last; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadRoles(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
            } else {
                html += `<button onclick="loadRoles(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = current - 1; i <= current + 1; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadRoles(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadRoles(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            }
        }

        // Next Button
        if (current < last) {
            html += `<button onclick="loadRoles(${current + 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Sau</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Sau</button>`;
        }
        pagination.innerHTML = html;
    }

    function toggleRoleSelection(roleId, checked) {
        if (checked) {
            selectedRoleIds.add(roleId);
        } else {
            selectedRoleIds.delete(roleId);
        }
        updateSelectAllCheckbox();
    }

    function toggleSelectAll(checked) {
        const checkboxes = document.querySelectorAll('.role-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const roleId = parseInt(checkbox.value);
            if (checked) {
                selectedRoleIds.add(roleId);
            } else {
                selectedRoleIds.delete(roleId);
            }
        });
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAll');
        if (!selectAllCheckbox) return;

        const checkboxes = document.querySelectorAll('.role-checkbox');
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

    // Xử lý khi xóa một mục
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

    // Xử lý khi xóa nhiều mục cùng lúc
    async function openBulkDeleteModal() {
        openBulkDeleteModalGeneric_Global({
            arrayIds: Array.from(selectedRoleIds),
            objectName: OBJECTNAMEMODAL.ROLE,
            actionFuncCallback: () => handleDeleteRoles(Array.from(selectedRoleIds)),
            successFuncCallback: () => loadRoles(currentPage),
            failFuncCallback: () => {}
        });
    }

    async function handleDeleteRoles(arrayIds = []) {
        try {
            const data = await apiRequest(`/roles/`, {
                method: 'DELETE',
                body: JSON.stringify({
                    role_ids: [...arrayIds]
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
