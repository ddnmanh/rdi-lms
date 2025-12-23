@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm vai trò' : 'Chỉnh sửa vai trò')

@section('description', $mode === 'CREATE' ? 'Thêm vai trò mới vào hệ thống' : 'Chỉnh sửa thông tin vai trò')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4 overflow-hidden">

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
    </div>

    {{-- Form Card --}}
    <form id="roleForm" onsubmit="saveRole(event)" class="w-full max-w-[1800px] flex-1 min-h-0 mx-auto p-6 flex flex-col justify-start gap-3 3xl:gap-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <input type="hidden" id="roleId" value="{{ $mode === 'EDIT' ? ($roleId ?? '') : '' }}">

        <!-- MAIN FORM WRAPPER -->
        <div class="flex-1 flex flex-col items-stretch justify-start gap-3 3xl:gap-4 min-h-0 overflow-y-auto">

            <div class="flex flex-row justify-between items-start gap-4 3xl:gap-6">
                <!-- field: name -->
                <div class="flex-1 ">
                    <label name="name_LABEL" for="name" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Tên <span class="text-red-500">*</span></label>
                    <input type="text" id="name" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <span id="name_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                <!-- field: level -->
                <div class="flex-1 ">
                    <label name="level_LABEL" for="level" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Level <span class="text-red-500">*</span> (1-255) <span class="text-xs font-normal text-gray-500 dark:text-gray-400">Level càng thấp thì quyền hạn càng lớn, 1-20 có thể vào trang quản lý</span></label>
                    <input type="number" id="level" min="1" max="255" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    <span id="level_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>
            </div>


            <!-- field: description -->
            <div class="">
                <label for="description" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                <input type="text" id="description"
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
            </div>

            <!-- Permissions (TỰ CO GIÃN) -->
            <div class="flex-1 flex flex-col gap-0.5 min-h-0">

                <!-- Container co giãn theo chiều cao -->
                <div class="py-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between gap-3">
                        <div class="hidden">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Danh sách permissions</h2>
                            <p class=" text-gray-500 dark:text-gray-400">Hiển thị chi tiết từng API/đường dẫn đã được gán
                            </p>
                        </div>
                        <div class="relative w-full max-w-[450px]">
                            <span class="absolute top-[50%] left-3 translate-y-[-50%] flex items-center text-gray-400">
                                <svg class="w-4" viewBox="0 0 512 512" fill="currentColor">
                                    <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                                </svg>
                            </span>
                            <input type="text" id="permissionSearch" placeholder="Tìm theo tên, mô tả ..."
                                class="w-full px-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                            <span id="permissionSearchClearBtn" class="w-[15px] 3xl:w-[20px] aspect-square  text-sm text-white dark:text-gray-700 rounded-full absolute top-[50%] right-3 translate-y-[-50%] flex items-center text-gray-400 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 cursor-pointer flex justify-center overflow-hidden">
                                <svg class="w-2" viewBox="0 0 384 512" fill="currentColor">
                                    <path d="M55.1 73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L147.2 256 9.9 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192.5 301.3 329.9 438.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.8 256 375.1 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192.5 210.7 55.1 73.4z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex-1 min-h-0 rounded-lg relative table-scroll-container">
                    <div id="permissionsCheckboxes" class="flex-1 h-full overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800">
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


        <!-- footer -->
        <div class="flex items-center justify-end gap-3">
            <button
                type="button"
                onclick="handleGotoBackPage_Global()"
                class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                Hủy
            </button>
            <button type="submit" id="submitBtn" class="px-4 py-2.5  font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <span>Lưu</span>
            </button>
        </div>

    </form>

</div>


<script>
    // ===== STATE =====
    const mode = '{{ $mode }}';
    const roleId = @if($mode === 'EDIT' && isset($roleId)) {{ $roleId }} @else null @endif;
    let permissionsData = [];
    let permissionFilterText = '';
    let selectedPermissionIds = new Set();

    // ===== COLOR CONFIGURATION =====
    const GROUP_COLORS = [
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

    const METHOD_COLORS = {
        'GET': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'POST': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'PUT': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        'PATCH': 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
        'DELETE': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
    };

    // ===== INITIALIZATION =====
    document.addEventListener('DOMContentLoaded', async () => {

        // Lấy danh sách permissions
        permissionsData = await loadPermissions();
        if (permissionsData && permissionsData.length > 0) {
            renderPermissionsCheckboxes([], permissionFilterText);
        } else {
            NotificationModal.show(`Không thể tải danh sách permissions`, 'error', handleBackPrevPage);
        }

        // Nếu là chỉnh sửa vai trò thì tải thông tin vai trò
        if (mode === 'EDIT' && roleId) {
            roleData = await loadRoleData();
            if (roleData) {
                fillDataRoleToForm(roleData);
                const rolePermissionIds = (roleData?.permissions || []).map(p => p.id);
                selectedPermissionIds = new Set(rolePermissionIds);
                renderPermissionsCheckboxes(rolePermissionIds, permissionFilterText);
            } else {
                NotificationModal.show(`Không thể tải thông tin vai trò cần chỉnh sửa`, 'error', handleBackPrevPage);
            }
        }

        setupEventListeners();
    });

    function setupEventListeners() {
        // Search input
        document.getElementById('permissionSearch')?.addEventListener('input', e => {
            permissionFilterText = e.target.value.toLowerCase().trim();
            renderPermissionsCheckboxes(Array.from(selectedPermissionIds), permissionFilterText);
        });

        // Clear search button
        document.getElementById('permissionSearchClearBtn')?.addEventListener('click', () => {
            const searchInput = document.getElementById('permissionSearch');
            if (searchInput) {
                searchInput.value = '';
                permissionFilterText = '';
                renderPermissionsCheckboxes(Array.from(selectedPermissionIds), permissionFilterText);
            }
        });

        // Checkbox changes
        document.addEventListener('change', e => {
            if (e.target?.name === 'permission_ids[]') {
                const permissionId = parseInt(e.target.value);
                e.target.checked ? selectedPermissionIds.add(permissionId) : selectedPermissionIds.delete(permissionId);
                updateSelectAllCheckbox();
            }
        });
    }

    // ===== PERMISSIONS LOADING =====
    async function loadPermissions() {
        try {
            const res = await apiRequest('/permissions?search=&sort_by=id&order_by=asc');

            if (res && res?.success && res?.data?.data && res?.data?.data.length > 0) {
                return res.data?.data || [];
            } else {
                return null;
            }
        } catch (error) {
            return null;
        }
    }

    // ===== ROLE DATA LOADING =====
    async function loadRoleData() {
        try {
            const res = await apiRequest(`/roles/${roleId}`);
            if (res && res?.success && res?.data) {
                return res?.data || null;
            } else {
                return null
            }
        } catch (error) {
            return null;
        }
    }

    // ===== FILL DATA ROLE TO FORM =====
    function fillDataRoleToForm(role) {
        document.getElementById('name').value = role.name || '';
        document.getElementById('description').value = role.description || '';
        document.getElementById('level').value = role.level || '';
    }

    // ===== UTILITY FUNCTIONS =====
    function getGroupColor(groupName) {
        if (!groupName) return { bg: 'bg-gray-100 dark:bg-gray-700', border: 'border-gray-300 dark:border-gray-600', borderLeft: 'border-l-gray-300 dark:border-l-gray-600', text: 'text-gray-700 dark:text-gray-300', badge: 'bg-gray-500' };

        const hash = groupName.split('').reduce((acc, char) => char.charCodeAt(0) + ((acc << 5) - acc), 0);
        return GROUP_COLORS[Math.abs(hash) % GROUP_COLORS.length];
    }

    function escapeHtml(text) {
        return text.replace(/"/g, '&quot;');
    }

    function getMethodColor(method) {
        return METHOD_COLORS[method] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }

    function removeVietnameseTones(str) {
        str = str.toLowerCase();
        str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
        str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
        str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
        str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
        str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
        str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
        str = str.replace(/đ/g, 'd');
        return str;
    }

    function filterPermissions(filterText) {
        if (!filterText) return permissionsData;


        const normalized = removeVietnameseTones(filterText.toLowerCase().trim());

        return permissionsData.filter(perm => {
            const name = removeVietnameseTones((perm.name || `${perm.method || ''} ${perm.path || ''}`).toLowerCase());
            const desc = removeVietnameseTones((perm.description || '').toLowerCase());
            const group = removeVietnameseTones((perm.group || '').toLowerCase());
            return name.includes(normalized) || desc.includes(normalized) || group.includes(normalized);
        });
    }

    function groupAndSortPermissions(permissions) {
        const grouped = permissions.reduce((acc, perm) => {
            const groupName = perm.group || 'Khác';
            if (!acc[groupName]) acc[groupName] = [];
            acc[groupName].push(perm);
            return acc;
        }, {});

        return Object.keys(grouped).sort((a, b) => {
            if (a === 'Khác') return 1;
            if (b === 'Khác') return -1;
            return a.localeCompare(b);
        }).map(key => ({ name: key, permissions: grouped[key] }));
    }

    function createEmptyState(message) {
        return `<div class="flex items-center justify-center py-8">
                    <p class="text-gray-600 dark:text-gray-400">${message}</p>
                </div>`;
    }

    function createGroupHeaderRow(groupName, color) {
        return `<tr class="${color.bg} ${color.borderLeft}">
                    <td colspan="6" class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full ${color.badge}"></span>
                            <span class="font-semibold ${color.text}">${groupName}</span>
                        </div>
                    </td>
                </tr>`;
    }

    function createPermissionRow(perm, isChecked) {
        const name = perm.name || `${perm.method} ${perm.path}`;
        const desc = perm.description || '';
        const method = perm.method || '-';
        const path = perm.path || '-';
        const tooltip = desc ? `title="${escapeHtml(desc)}"` : '';

        return `<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 w-[50px]">
                        <input type="checkbox" name="permission_ids[]" value="${perm.id}" ${isChecked ? 'checked' : ''}
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    </td>
                    <td class="px-4 py-3 w-[50px]">
                        <span class="text-gray-600 dark:text-gray-400 truncate block" title="${perm.id}">${perm.id}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span ${tooltip} class="font-medium text-gray-900 dark:text-white cursor-help truncate block">${name}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-600 dark:text-gray-400 truncate block" title="${escapeHtml(desc || '-')}">${desc || '-'}</span>
                    </td>
                    <td class="px-4 py-3 w-[100px]">
                        <span class="inline-flex items-center px-2 py-1 font-semibold rounded text-xs ${getMethodColor(method)}">${method}</span>
                    </td>
                    <td class="px-4 py-3 w-[270px]">
                        <code class="text-gray-600 dark:text-gray-400 font-mono truncate block" title="${path}">${path}</code>
                    </td>
                </tr>`;
    }

    // ===== RENDER FUNCTIONS =====
    function renderPermissionsCheckboxes(selectedPermissionIds = [], filterText = '') {
        const container = document.getElementById('permissionsCheckboxes');

        if (permissionsData.length === 0) {
            container.innerHTML = createEmptyState('Không có permissions nào');
            return;
        }

        const filtered = filterPermissions(filterText);
        if (filtered.length === 0) {
            container.innerHTML = createEmptyState('Không tìm thấy permission phù hợp');
            return;
        }

        const groups = groupAndSortPermissions(filtered);
        const tableBody = groups.map(({ name, permissions }) => {
            const color = getGroupColor(name);
            const headerRow = createGroupHeaderRow(name, color);
            const permissionRows = permissions.map(perm =>
                createPermissionRow(perm, selectedPermissionIds.includes(perm.id))
            ).join('');
            return headerRow + permissionRows;
        }).join('');

        container.innerHTML = `
            <div class="overflow-x-auto h-full">
                <table class="w-full table-fixed border-separate border-spacing-0">
                    <colgroup>
                        <col class="w-[40px] 3xl:w-[60px]">
                        <col class="w-[60px] 3xl:w-[80px]">
                        <col class="">
                        <col class="">
                        <col class="w-[90px] 3xl:w-[100px]">
                        <col class="">
                    </colgroup>
                    <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                        <tr>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">
                                <input type="checkbox" id="selectAllPermissions"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                    onchange="toggleAllPermissions(this.checked)">
                            </th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">ID</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Tên</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Mô tả</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Method</th>
                            <th scope="col" class="px-4 py-3 sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Path</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">${tableBody}</tbody>
                </table>
            </div>
        `;

        updateSelectAllCheckbox();
    }

    function toggleAllPermissions(checked) {
        document.querySelectorAll('input[name="permission_ids[]"]').forEach(checkbox => {
            checkbox.checked = checked;
            const permissionId = parseInt(checkbox.value);
            checked ? selectedPermissionIds.add(permissionId) : selectedPermissionIds.delete(permissionId);
        });
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAllPermissions');
        if (!selectAllCheckbox) return;

        const total = document.querySelectorAll('input[name="permission_ids[]"]').length;
        const checked = document.querySelectorAll('input[name="permission_ids[]"]:checked').length;

        selectAllCheckbox.checked = checked > 0 && checked === total;
        selectAllCheckbox.indeterminate = checked > 0 && checked < total;
    }

    // ===== DATA OPERATIONS =====
    async function saveRole(event) {
        event.preventDefault();
        renderInputErrors_Global(null, true);

        const submitBtn = document.getElementById('submitBtn');
        const roleIdValue = document.getElementById('roleId').value;

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';

            const formData = {
                name: document.getElementById('name').value,
                description: document.getElementById('description').value || null,
                level: parseInt(document.getElementById('level').value),
                permission_ids: Array.from(selectedPermissionIds)
            };

            const response = await fetch(
                roleIdValue ? `/api/roles/${roleIdValue}` : '/api/roles',
                {
                    method: roleIdValue ? 'PUT' : 'POST',
                    credentials: 'include',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(formData)
                }
            );

            const data = await response.json();

            if (response.ok) {
                NotificationModal.show(
                    mode === 'EDIT' ? 'Cập nhật thông tin vai trò thành công' : 'Tạo vai trò thành công',
                    'success',
                    handleBackPrevPage
                );
            } else if (response.status === 422) {
                renderInputErrors_Global(data.errors || null);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            NotificationModal.show(error.message || 'Thao tác thất bại', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = mode === 'CREATE' ? 'Tạo' : 'Cập nhật';
        }
    }

    function handleBackPrevPage() {
        window.location.href = '{{ route('admin.roles.list') }}';
    }
</script>
@endsection



