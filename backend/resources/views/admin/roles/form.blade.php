@extends('admin.layout')

@section('title', $mode === 'create' ? 'Thêm vai trò' : 'Chỉnh sửa vai trò')

@section('description', $mode === 'create' ? 'Thêm vai trò mới vào hệ thống' : 'Chỉnh sửa thông tin vai trò')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.roles.list') }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Quay lại</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <form id="roleForm" onsubmit="saveRole(event)" class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="roleId" value="{{ $mode === 'edit' ? ($roleId ?? '') : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-0.5">
                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Tên *</label>
                <input type="text" id="name" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Tên vai trò sẽ được sử dụng để phân quyền</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="level" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Level * (1-255)</label>
                <input type="number" id="level" min="1" max="255" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Level càng thấp thì quyền hạn càng lớn</p>
            </div>

            <div class="flex flex-col gap-0.5 md:col-span-2">
                <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Mô tả</label>
                <input type="text" id="description"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                {{-- <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Mô tả về vai trò này</p> --}}
            </div>

            <div class="flex flex-col gap-0.5 md:col-span-2">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3 mb-2">Permissions</label>
                <div id="permissionsCheckboxes" class="max-h-96 overflow-y-auto p-4 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center justify-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-3">
                                <i class="fas fa-spinner fa-spin text-xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Đang tải permissions...</p>
                        </div>
                    </div>
                </div>
                {{-- <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Chọn các quyền cho vai trò này</p> --}}
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.roles.list') }}"
                class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                Hủy
            </a>
            <button type="submit"
                class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Lưu</span>
            </button>
        </div>
    </form>

</div>

<script>
    let permissionsList = [];
    const mode = '{{ $mode }}';
    const roleId = @if($mode === 'edit' && isset($roleId)) {{ $roleId }} @else null @endif;

    document.addEventListener('DOMContentLoaded', async function() {
        await loadPermissions();
        if (mode === 'edit' && roleId) {
            await loadRoleData(roleId);
        }
    });

    async function loadPermissions() {
        try {
            // Try to get permissions from roles
            const rolesData = await apiRequest('/roles?per_page=100');
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
                renderPermissionsCheckboxes();
            }
        } catch (error) {
            // showAlert('Không thể tải danh sách permissions: ' + error.message, 'error');
        }
    }

    function renderPermissionsCheckboxes(selectedPermissionIds = []) {
        const container = document.getElementById('permissionsCheckboxes');
        if (permissionsList.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Không có permissions nào</p>
                </div>
            `;
            return;
        }

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">';
        permissionsList.forEach(perm => {
            const checked = selectedPermissionIds.includes(perm.id) ? 'checked' : '';
            const permName = perm.name || `${perm.method} ${perm.path}`;
            const permDescription = perm.description || '';
            const tooltip = permDescription ? `title="${permDescription.replace(/"/g, '&quot;')}"` : '';
            html += `
                <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors border border-gray-200 dark:border-gray-600">
                    <input type="checkbox" name="permission_ids[]" value="${perm.id}" ${checked}
                        class="w-4 h-4 mt-0.5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <div class="flex-1 flex flex-col gap-1">
                        <span ${tooltip} class="text-sm font-medium text-gray-700 dark:text-gray-300 cursor-help">${permName}</span>
                        ${permDescription ? `<span class="text-xs text-gray-500 dark:text-gray-400">${permDescription}</span>` : ''}
                    </div>
                </label>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
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
                renderPermissionsCheckboxes(rolePermissionIds);
            }
        } catch (error) {
            // showAlert('Không thể tải thông tin vai trò: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.roles.list') }}';
            }, 2000);
        }
    }

    async function saveRole(event) {
        event.preventDefault();
        const roleIdValue = document.getElementById('roleId').value;
        const formData = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value || null,
            level: parseInt(document.getElementById('level').value),
        };

        const permissionCheckboxes = document.querySelectorAll('input[name="permission_ids[]"]:checked');
        formData.permission_ids = Array.from(permissionCheckboxes).map(cb => parseInt(cb.value));

        try {
            let data;
            if (roleIdValue) {
                // Edit mode
                data = await apiRequest(`/roles/${roleIdValue}`, {
                    method: 'PUT',
                    body: JSON.stringify(formData)
                });
            } else {
                // Create mode
                data = await apiRequest('/roles', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
            }

            if (data.success) {
                // showAlert(data.message || 'Lưu thành công', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('admin.roles.list') }}';
                }, 1000);
            }
        } catch (error) {
            // showAlert(error.message, 'error');
        }
    }
</script>
@endsection

