@extends('admin.layout')

@section('title', $mode === 'create' ? 'Thêm người dùng' : 'Chỉnh sửa người dùng')

@section('description', $mode === 'create' ? 'Thêm người dùng mới vào hệ thống' : 'Chỉnh sửa thông tin người dùng')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.users.list') }}"
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
    <form id="userForm" onsubmit="saveUser(event)" class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="userId" value="{{ $mode === 'edit' ? ($userId ?? '') : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-0.5">
                <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Email *</label>
                <input type="email" id="email" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Email sẽ được sử dụng để đăng nhập</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">
                    Password {{ $mode === 'create' ? '*' : '' }}
                </label>
                <input type="password" id="password" {{ $mode === 'create' ? 'required' : '' }}
                    placeholder="{{ $mode === 'edit' ? 'Để trống nếu không đổi mật khẩu' : '' }}"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">
                    {{ $mode === 'create' ? 'Mật khẩu tối thiểu 8 ký tự' : 'Chỉ điền nếu muốn thay đổi mật khẩu' }}
                </p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="fullname" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Họ và tên</label>
                <input type="text" id="fullname"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="birthday" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Ngày sinh</label>
                <input type="date" id="birthday"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="path_avatar" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Avatar URL</label>
                <input type="text" id="path_avatar"
                    placeholder="https://example.com/avatar.jpg"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">URL ảnh đại diện của người dùng</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3 mb-2">Vai trò *</label>
                <div id="rolesCheckboxes" class="flex flex-row flex-wrap gap-1">
                    <div class="flex items-center justify-center py-8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-3">
                                <i class="fas fa-spinner fa-spin text-xl text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Đang tải vai trò...</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Chọn ít nhất một vai trò cho người dùng</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.users.list') }}"
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
    let rolesList = [];
    const mode = '{{ $mode }}';
    const userId = @if($mode === 'edit' && isset($userId)) {{ $userId }} @else null @endif;

    document.addEventListener('DOMContentLoaded', async function() {
        await loadRoles();
        if (mode === 'edit' && userId) {
            await loadUserData(userId);
        }
    });

    async function loadRoles() {
        try {
            const data = await apiRequest('/roles?per_page=100');
            if (data.success) {
                rolesList = data.data.data || [];
                renderRolesCheckboxes();
            }
        } catch (error) {
            showAlert('Không thể tải danh sách vai trò: ' + error.message, 'error');
        }
    }

    function renderRolesCheckboxes(selectedRoleIds = []) {
        const container = document.getElementById('rolesCheckboxes');
        if (rolesList.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Không có vai trò nào</p>
                </div>
            `;
            return;
        }

        let html = '';
        rolesList.forEach(role => {
            const checked = selectedRoleIds.includes(role.id) ? 'checked' : '';
            html += `
                <label class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors">
                    <input type="checkbox" name="role_ids[]" value="${role.id}" ${checked}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${role.name}</span>
                </label>
            `;
        });
        container.innerHTML = html;
    }

    async function loadUserData(id) {
        try {
            const data = await apiRequest(`/users/${id}`);
            if (data.success) {
                const user = data.data;
                document.getElementById('email').value = user.email || '';
                document.getElementById('fullname').value = user.fullname || '';
                document.getElementById('birthday').value = user.birthday ? user.birthday.split('T')[0] : '';
                document.getElementById('path_avatar').value = user.path_avatar || '';

                const userRoleIds = (user.roles || []).map(r => r.id);
                renderRolesCheckboxes(userRoleIds);
            }
        } catch (error) {
            showAlert('Không thể tải thông tin người dùng: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.users.list') }}';
            }, 2000);
        }
    }

    async function saveUser(event) {
        event.preventDefault();
        const userIdValue = document.getElementById('userId').value;
        const formData = {
            email: document.getElementById('email').value,
            fullname: document.getElementById('fullname').value,
            birthday: document.getElementById('birthday').value || null,
            path_avatar: document.getElementById('path_avatar').value || null,
        };

        const password = document.getElementById('password').value;
        if (password) {
            formData.password = password;
        }

        const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]:checked');
        console.log(roleCheckboxes);

        if (roleCheckboxes.length === 0) {
            showAlert('Vui lòng chọn ít nhất một vai trò', 'error');
            return;
        }
        formData.role_ids = Array.from(roleCheckboxes).map(cb => parseInt(cb.value));

        try {
            let data;
            if (userIdValue) {
                // Edit mode
                data = await apiRequest(`/users/${userIdValue}`, {
                    method: 'PUT',
                    body: JSON.stringify(formData)
                });
            } else {
                // Create mode
                if (!password) {
                    showAlert('Password là bắt buộc khi tạo mới', 'error');
                    return;
                }
                data = await apiRequest('/users', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
            }

            if (data.success) {
                showAlert(data.message || 'Lưu thành công', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('admin.users.list') }}';
                }, 1000);
            }
        } catch (error) {
            showAlert(error.message, 'error');
        }
    }
</script>
@endsection

