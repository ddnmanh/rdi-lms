@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm vai trò' : 'Chỉnh sửa vai trò')

@section('description', $mode === 'CREATE' ? 'Thêm vai trò mới vào hệ thống' : 'Chỉnh sửa thông tin vai trò')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 3xl:gap-6">

    {{-- Form Card --}}
    <form id="roleForm" onsubmit="saveRole(event)"
        class="w-full max-w-[1800px] h-full mx-auto p-6 flex flex-col justify-start gap-3 3xl:gap-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">

        <input type="hidden" id="roleId" value="{{ $mode === 'EDIT' ? ($roleId ?? '') : '' }}">

        <!-- MAIN FORM WRAPPER -->
        <div class="flex-1 flex flex-col items-stretch justify-start gap-3 3xl:gap-4 min-h-0">

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
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </span>
                            <input type="text" id="permissionSearch" placeholder="Tìm theo tên, mô tả ..."
                                class="w-full px-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                            <span id="permissionSearchClearBtn" class="w-[15px] 3xl:w-[20px] aspect-square rounded-full absolute top-[50%] right-3 translate-y-[-50%] flex items-center text-gray-400 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 cursor-pointer flex justify-center">
                                <i class="fa-solid fa-times text-sm text-white dark:text-gray-700"></i>
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
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- footer -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.roles.list') }}"
                class="px-4 py-2.5  font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                Hủy
            </a>
            <button type="submit" id="submitBtn"
                class="px-4 py-2.5  font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Lưu</span>
            </button>
        </div>

    </form>

</div>


<script>
    let permissionsList = [];
    const mode = '{{ $mode }}';
    const roleId = @if($mode === 'EDIT' && isset($roleId)) {{ $roleId }} @else null @endif;
    let permissionFilterText = '';
    let selectedPermissionIds = new Set();

    document.addEventListener('DOMContentLoaded', async function() {
        await loadPermissions();
        if (mode === 'EDIT' && roleId) {
            await loadRoleData(roleId);
        }
        const searchInput = document.getElementById('permissionSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                permissionFilterText = (e.target.value || '').toLowerCase().trim();
                renderPermissionsCheckboxes(Array.from(selectedPermissionIds), permissionFilterText);
            });
        }

        const permissionSearchClearBtn = document.getElementById('permissionSearchClearBtn');
        if (permissionSearchClearBtn) {
            permissionSearchClearBtn.addEventListener('click', function() {
                const searchInput = document.getElementById('permissionSearch');
                if (searchInput) {
                    searchInput.value = '';
                    permissionFilterText = '';
                    renderPermissionsCheckboxes(Array.from(selectedPermissionIds), permissionFilterText);
                }
            });
        }

        // Listen for checkbox changes to update select all state and selectedPermissionIds
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'permission_ids[]') {
                const permissionId = parseInt(e.target.value);
                if (e.target.checked) {
                    selectedPermissionIds.add(permissionId);
                } else {
                    selectedPermissionIds.delete(permissionId);
                }
                updateSelectAllCheckbox();
            }
        });
    });

    async function loadPermissions() {
        try {
            // Try to get permissions from roles
            const rolesData = await apiRequest('/roles?per_page=1000');
            if (rolesData.success && rolesData.data.data) {
                const allPermissions = new Map();
                rolesData.data.data.forEach(role => {
                    if (role.permissions) {
                        role.permissions.forEach(perm => {
                            if (!allPermissions.has(perm.id)) {
                                allPermissions.set(perm.id, perm);
                            }
                        });
                    }
                });
                permissionsList = Array.from(allPermissions.values());
                renderPermissionsCheckboxes([], permissionFilterText);
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải danh sách permissions: ' + error.message, 'error', handleBackPrevPage);
        }
    }

    // Hàm tạo màu sắc dựa trên tên group
    function getGroupColor(groupName) {
        if (!groupName) return { bg: 'bg-gray-100 dark:bg-gray-700', border: 'border-gray-300 dark:border-gray-600', borderLeft: 'border-l-gray-300 dark:border-l-gray-600', text: 'text-gray-700 dark:text-gray-300', badge: 'bg-gray-500' };

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

        // Tạo hash từ tên group để chọn màu nhất quán
        let hash = 0;
        for (let i = 0; i < groupName.length; i++) {
            hash = groupName.charCodeAt(i) + ((hash << 5) - hash);
        }
        const index = Math.abs(hash) % colors.length;
        return colors[index];
    }

    function renderPermissionsCheckboxes(selectedPermissionIds = [], filterText = '') {
        const container = document.getElementById('permissionsCheckboxes');
        if (permissionsList.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class=" text-gray-600 dark:text-gray-400">Không có permissions nào</p>
                </div>
            `;
            return;
        }

        const normalizedFilter = (filterText || '').toLowerCase().trim();
        const filtered = normalizedFilter
            ? permissionsList.filter(perm => {
                const permName = (perm.name || `${perm.method || ''} ${perm.path || ''}`).toLowerCase();
                const permDesc = (perm.description || '').toLowerCase();
                const permGroup = (perm.group || '').toLowerCase();
                return permName.includes(normalizedFilter) || permDesc.includes(normalizedFilter) || permGroup.includes(normalizedFilter);
            })
            : permissionsList;

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class=" text-gray-600 dark:text-gray-400">Không tìm thấy permission phù hợp</p>
                </div>
            `;
            return;
        }

        // Nhóm permissions theo group
        const grouped = {};
        filtered.forEach(perm => {
            const groupName = perm.group || 'Khác';
            if (!grouped[groupName]) {
                grouped[groupName] = [];
            }
            grouped[groupName].push(perm);
        });

        // Sắp xếp các nhóm theo tên
        const sortedGroups = Object.keys(grouped).sort((a, b) => {
            if (a === 'Khác') return 1;
            if (b === 'Khác') return -1;
            return a.localeCompare(b);
        });

        // Tạo table HTML
        let html = `
            <div class="overflow-x-auto h-full">
                <table class="w-full table-fixed border-separate border-spacing-0">
                    <colgroup>
                        <col class="w-[40px] 3xl:w-[60px]">
                        <col class="w-[60px] 3xl:w-[80px]">
                        <col class="">
                        <col class="">
                        <col class="w-[80px] 3xl:w-[100px]">
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
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        `;

        sortedGroups.forEach(groupName => {
            const groupPermissions = grouped[groupName];
            const color = getGroupColor(groupName);

            // Group header row
            html += `
                <tr class="${color.bg} ${color.borderLeft}">
                    <td colspan="6" class="px-4 py-2.5">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full ${color.badge}"></span>
                            <span class=" font-semibold ${color.text}">${groupName}</span>
                            <span class=" ${color.text} opacity-70"></span>
                        </div>
                    </td>
                </tr>
            `;

            // Permission rows
            groupPermissions.forEach(perm => {
                const id = perm.id;
                const checked = selectedPermissionIds.includes(perm.id) ? 'checked' : '';
                const permName = perm.name || `${perm.method} ${perm.path}`;
                const permDescription = perm.description || '';
                const permMethod = perm.method || '-';
                const permPath = perm.path || '-';
                const tooltip = permDescription ? `title="${permDescription.replace(/"/g, '&quot;')}"` : '';

                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 w-[50px]">
                            <input type="checkbox" name="permission_ids[]" value="${perm.id}" ${checked}
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </td>
                        <td class="px-4 py-3 w-[50px]">
                            <span class="text-gray-600 dark:text-gray-400 truncate block" title="${id}">${id}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span ${tooltip} class="font-medium text-gray-900 dark:text-white cursor-help truncate block">${permName}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-gray-600 dark:text-gray-400 truncate block" title="${(permDescription || '-').replace(/"/g, '&quot;')}">${permDescription || '-'}</span>
                        </td>
                        <td class="px-4 py-3 w-[100px]">
                            <span class="inline-flex items-center px-2 py-1  font-semibold rounded text-xs ${
                                permMethod === 'GET' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                permMethod === 'POST' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                permMethod === 'PUT' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                permMethod === 'PATCH' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300' :
                                permMethod === 'DELETE' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                            }">${permMethod}</span>
                        </td>
                        <td class="px-4 py-3 w-[270px]">
                            <code class=" text-gray-600 dark:text-gray-400 font-mono truncate block" title="${permPath}">${permPath}</code>
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

        // Update select all checkbox state
        updateSelectAllCheckbox();
    }

    function toggleAllPermissions(checked) {
        const checkboxes = document.querySelectorAll('input[name="permission_ids[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = checked;
            const permissionId = parseInt(checkbox.value);
            if (checked) {
                selectedPermissionIds.add(permissionId);
            } else {
                selectedPermissionIds.delete(permissionId);
            }
        });
    }

    function updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('selectAllPermissions');
        if (!selectAllCheckbox) return;

        const checkboxes = document.querySelectorAll('input[name="permission_ids[]"]');
        const checkedCount = document.querySelectorAll('input[name="permission_ids[]"]:checked').length;

        if (checkedCount === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCount === checkboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        }
    }

    async function loadRoleData(id) {
        try {
            const data = await apiRequest(`/roles/${id}`);
            if (data.success) {
                const role = data.data;
                document.getElementById('name').value = role.name || '';
                document.getElementById('description').value = role.description || '';
                document.getElementById('level').value = role.level || '';

                const rolePermissionIds = (role.permissions || []).map(p => p.id);
                // Update selectedPermissionIds Set
                selectedPermissionIds = new Set(rolePermissionIds);
                renderPermissionsCheckboxes(rolePermissionIds, permissionFilterText);
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải thông tin vai trò: ' + error.message, 'error', handleBackPrevPage); 
        }
    }

    async function saveRole(event) {
        event.preventDefault();

        renderInputErrors_Global(null, true); // Clear previous errors
        const submitBtn = document.getElementById('submitBtn');

        // Disable submit button
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';
        }

        const roleIdValue = document.getElementById('roleId').value;
        const formData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value || null,
            level: parseInt(document.getElementById('level').value),
        };

        const permissionCheckboxes = document.querySelectorAll('input[name="permission_ids[]"]:checked');
        formData.permission_ids = Array.from(permissionCheckboxes).map(cb => parseInt(cb.value));

        try {
            let method = roleIdValue ? 'PUT' : 'POST';
            let url = roleIdValue ? `/api/roles/${roleIdValue}` : '/api/roles';
            let response = await fetch(url, {
                method: method,
                credentials: 'include',
                body: JSON.stringify(formData),
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }); 

            const data = await response.json();

            if (response.ok) {
                showNotificationModel_Global(mode === 'EDIT' ? 'Cập nhật thông tin vai trò thành công' : 'Tạo vai trò thành công', 'success', handleBackPrevPage);
            } else if (response.status === 422) {
                let errorsField = data.errors || null; 
                renderInputErrors_Global(errorsField);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }
        } catch (error) {
            showNotificationModel_Global(error.message || 'Thao tác thất bại', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = mode === 'CREATE' ? 'Tạo' : 'Cập nhật';
            }
        }
    }

    function handleBackPrevPage() {
        window.location.href = '{{ route('admin.roles.list') }}';
    }
</script>
@endsection



