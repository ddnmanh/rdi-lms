@extends('admin.layout')

@section('title', 'Quản lý Người dùng')

@section('description', 'Quản lý tất cả người dùng trong hệ thống')

@section('content') 
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
    <div class="flex flex-col items-stretch justify-start gap-2.5">
        {{-- Actions Bar --}}
        <div class="flex items-center justify-start gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create') }}"
                    class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                    <i class="fas fa-plus"></i>
                    <span>Thêm người dùng</span>
                </a>
            </div>
            <button
                id="btnDeleteBulk"
                onclick="openBulkDeleteModal()"
                disabled
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700 opacity-50 cursor-not-allowed">
                <i class="fas fa-trash"></i>
                <span>Xóa <span id="selectedCount"></span> người dùng</span>
            </button>
        </div>

        {{-- Filter Section --}}
        <form onsubmit="return handleSubmitFilter(event)" class="max-w-1/2 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col lg:flex-row justify-start gap-4">
            <div class="flex flex-col lg:flex-row justify-start flex-wrap gap-4 flex-1">
                {{-- Role Filter --}}
                <div class="min-w-32 flex flex-col items-stretch justify-start gap-0.5">
                    <label for="roleFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300 ml-2">
                        Vai trò
                    </label>
                    <select id="roleFilter"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">Tất cả</option>
                    </select>
                </div>

                <div class="min-w-[500px] flex flex-wrap items-stretch justify-start gap-4">
                    {{-- Name Filter --}}
                    <div class="flex-1 flex flex-col items-stretch justify-start gap-0.5">
                        <label for="nameFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                            Tên
                        </label>
                        <input type="text" id="nameFilter" placeholder="Tìm theo tên..."
                            {{-- onkeyup="debounceLoadUsers()" --}}
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    </div>
    
                    {{-- Email Filter --}}
                    <div class="flex-1 flex flex-col items-stretch justify-start gap-0.5">
                        <label for="emailFilter" class="block ml-3 font-medium text-gray-300 dark:text-gray-300">
                            Email
                        </label>
                        <input type="text" id="emailFilter" placeholder="Tìm theo email..."
                            {{-- onkeyup="debounceLoadUsers()" --}}
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
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
            <div id="usersTable">

            </div>
        </div>

        {{-- Pagination Footer --}}
        <div class="px-3.5 py-2 border-t border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 sm:grid-cols-3 justify-between items-center gap-4">
                {{-- Info --}}
                <span id="paginationInfo" class=" text-gray-600 dark:text-gray-400 text-left"></span>

                {{-- Items per page selector --}}
                <div class="flex items-center justify-center gap-2">
                    <label for="itemPerPage" class="text-gray-600 dark:text-gray-400 whitespace-nowrap">Số mục mỗi trang</label>
                    <select id="itemPerPage" onchange="loadUsers(1)"
                        class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50" selected>50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                {{-- Custom Pagination --}}
                <div id="pagination" class="flex items-center justify-end gap-1"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let rolesList = [];
    let deleteUserId = null;
    let isBulkDelete = false;
    let debounceTimer = null;
    let sortBy = 'created_at';
    let sortOrder = 'desc';
    let selectedUserIds = new Set();

    document.addEventListener('DOMContentLoaded', async function() {
        await Promise.all([loadRoles(), loadUsers()]);

        // Ngăn chặn hành động mặc định của form khi nhấn enter ở các input
        const filterInputs = ['nameFilter', 'emailFilter', 'createdFrom', 'createdTo', 'roleFilter'];
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

    function debounceLoadUsers() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadUsers(1);
        }, 500);
    }

    function resetFilters() {
        document.getElementById('roleFilter').value = '';
        document.getElementById('nameFilter').value = '';
        document.getElementById('emailFilter').value = '';
        document.getElementById('createdFrom').value = '';
        document.getElementById('createdTo').value = '';
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
            const data = await apiRequest('/roles?per_page=100');
            if (data.success) {
                rolesList = data.data.data || [];
                const roleFilter = document.getElementById('roleFilter');
                rolesList.forEach(role => {
                    const option = document.createElement('option');
                    option.value = role.id;
                    option.textContent = role.name;
                    roleFilter.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading roles:', error);
        }
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
        loadUsers(1);
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
        const tableContainer = document.getElementById('usersTable');
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

    async function loadUsers(page = 1) {

        renderTableLoading();

        currentPage = page;
        // Clear selected users when changing page or filters
        selectedUserIds.clear();
        const roleId = document.getElementById('roleFilter').value;
        const name = document.getElementById('nameFilter').value;
        const email = document.getElementById('emailFilter').value;
        const createdFrom = document.getElementById('createdFrom').value;
        const createdTo = document.getElementById('createdTo').value;
        const itemPerPage = document.getElementById('itemPerPage').value || 50;

        try {
            let url = `/users?page=${page}&per_page=${itemPerPage}`;
            if (roleId) url += `&role_id=${roleId}`;
            if (name) url += `&search_fullname=${encodeURIComponent(name)}`;
            if (email) url += `&search_email=${encodeURIComponent(email)}`;
            if (createdFrom) url += `&created_at_from=${createdFrom}`;
            if (createdTo) url += `&created_at_to=${createdTo}`;
            if (sortBy) url += `&sort_by=${sortBy}`;
            if (sortOrder) url += `&order_by=${sortOrder}`;

            const res = await apiRequest(url);

            if (res.success) {
                renderUsersTable(res?.data?.data || []);
                renderPagination(res?.data || {});
                renderPaginationInfo(res?.data || {});
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

        const headTable = `
            <colgroup>
                <col class="w-[40px] 3xl:w-[80px]">
                <col class="w-[60px] 3xl:w-[110px]">
                <col class="w-[80px] 3xl:w-[110px]">
                <col class="w-[250px] 3xl:w-[400px]">
                <col class="">
                <col class="">
                <col class="w-[140px] 3xl:w-[160px]">
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
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Avatar</th>
                    <th onclick="handleSort('fullname')" class="${getHeaderClass('fullname')}">
                        Tên${getSortIcon('fullname')}
                    </th>
                    <th onclick="handleSort('email')" class="${getHeaderClass('email')}">
                        Email${getSortIcon('email')}
                    </th>
                    <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Vai trò</th>
                    <th onclick="handleSort('created_at')" class="${getHeaderClass('created_at')}">
                        Ngày tạo${getSortIcon('created_at')}
                    </th>
                    <th class="px-4 py-3 text-right sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Thao tác</th>
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
                                    <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                        <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
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
                <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
        `;

        users.forEach(user => {
            const roleBadge = user?.roles?.map(r => {
                if (r.name === 'ROOT') {
                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-red-500 to-red-500 text-white">${r.name}</span>`;
                } else if (r.name === 'ADMIN') {
                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-amber-500 to-amber-500 text-white">${r.name}</span>`;
                } else if (r.name === 'TEACHER') {
                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-500 text-white">${r.name}</span>`;
                } else if (r.name === 'STUDENT') {
                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-blue-500 to-blue-500 text-white">${r.name}</span>`;
                } else {
                    return `<span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-xl bg-gradient-to-r from-gray-500 to-gray-500 text-white">${r.name}</span>`;
                }
            }).join(' ');

            const isChecked = selectedUserIds.has(user.id);
            html += `
                <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <td class="px-4 py-3 text-center">
                        <input type="checkbox"
                            class="user-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            value="${user.id}"
                            ${isChecked ? 'checked' : ''}
                            onchange="toggleUserSelection(${user.id}, this.checked)">
                    </td>
                    <td class="${getCellClass('id')} text-center">
                        <span class="text-gray-600 dark:text-gray-300">${user.id}</span>
                    </td>
                    <td class="px-4 py-3 align-center whitespace-normal break-words">
                        <img src="${user.avatar_path ?? ''}" alt="" class="m-auto w-10 aspect-square object-cover rounded-full bg-gray-200 dark:bg-gray-700">
                    </td>
                    <td class="${getCellClass('fullname')} text-gray-600 dark:text-gray-300">
                        ${user.fullname || '-'}
                    </td>
                    <td class="${getCellClass('email')} text-gray-600 dark:text-gray-300 break-all [overflow-wrap:anywhere]">
                        ${user.email}
                    </td>
                    <td class="px-4 py-3 align-center whitespace-normal break-words">
                        ${roleBadge}
                    </td>
                    <td class="${getCellClass('created_at')} text-gray-600 dark:text-gray-300">
                        ${formatDate_Global(user.created_at)}
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-nowrap items-center justify-end gap-2 overflow-x-auto">
                            <a href="/admin/users/${user.id}"
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-blue-500 hover:border-blue-600 text-blue-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300"
                                title="Xem chi tiết">
                                <i class="fas fa-eye  3xl:"></i>
                            </a>
                            <a href="/admin/users/${user.id}/edit"
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-amber-500 hover:border-amber-600 text-amber-500 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all duration-300"
                                title="Chỉnh sửa">
                                <i class="fa-solid fa-pen  3xl:"></i>
                            </a>
                            <button onclick="openSingleDeleteModal(${user.id}, '${(user.fullname || '').replace(/'/g, "\\'")}', '${user.email.replace(/'/g, "\\'")}')"
                                class="inline-flex shrink-0 size-7 3xl:size-8 items-center justify-center rounded-md border border-red-500 hover:border-red-600 text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-300"
                                title="Xóa">
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
            html += `<button onclick="loadUsers(${current - 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">Trước</button>`;
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
                    html += `<button onclick="loadUsers(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
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
                        html += `<button onclick="loadUsers(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadUsers(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            } else if (current >= last - 2) {
                // Show first, ellipsis, last 3
                html += `<button onclick="loadUsers(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = last - 2; i <= last; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadUsers(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
            } else {
                // Show first, ellipsis, current-1, current, current+1, ellipsis, last
                html += `<button onclick="loadUsers(1)" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">1</button>`;
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                for (let i = current - 1; i <= current + 1; i++) {
                    if (i === current) {
                        html += `<button type="button" class="px-2.5 py-0.5 rounded-md border bg-blue-600 text-white border-blue-600">${i}</button>`;
                    } else {
                        html += `<button onclick="loadUsers(${i})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${i}</button>`;
                    }
                }
                html += `<span class="inline-flex items-center justify-center px-3 py-1 text-gray-400">...</span>`;
                html += `<button onclick="loadUsers(${last})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">${last}</button>`;
            }
        }

        // Next Button
        if (current < last) {
            html += `<button onclick="loadUsers(${current + 1})" class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Sau</button>`;
        } else {
            html += `<button type="button" disabled class="px-2.5 py-0.5 bg-white dark:bg-gray-600 rounded-md border border-gray-200 dark:border-gray-700 opacity-50 cursor-not-allowed">Sau</button>`;
        }
        pagination.innerHTML = html;
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
        openSingleDeleteModalGeneric_Global({
            objectName: OBJECTNAMEMODAL.USER,
            idDelete: userId,
            nameValue: userName || '-',
            descValue: userEmail || '',
            actionFuncCallback: () => handleDeleteUsers([userId]),
            successFuncCallback: () => loadUsers(currentPage),
            failFuncCallback: () => {}
        });
    }

    // Xử lý khi xóa nhiều mục cùng lúc
    async function openBulkDeleteModal() {
        openBulkDeleteModalGeneric_Global({
            arrayIds: Array.from(selectedUserIds),
            objectName: OBJECTNAMEMODAL.USER,
            actionFuncCallback: () => handleDeleteUsers(Array.from(selectedUserIds)),
            successFuncCallback: () => loadUsers(currentPage),
            failFuncCallback: () => {}
        });
    }

    async function handleDeleteUsers(arrayIds = []) {
        try {
            const data = await apiRequest(`/users/`, {
                method: 'DELETE',
                body: JSON.stringify({
                    user_ids: [...arrayIds]
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
