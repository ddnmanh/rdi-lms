@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm người dùng' : 'Chỉnh sửa người dùng')

@section('description', $mode === 'CREATE' ? 'Thêm người dùng mới vào hệ thống' : 'Chỉnh sửa thông tin người dùng')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 3xl:gap-6"> 

    {{-- Form Card --}}
    <form id="userForm" onsubmit="saveUser(event)" class="w-full max-w-[1400px] mx-auto p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <input type="hidden" id="userId" value="{{ $mode === 'EDIT' ? ($userId ?? '') : '' }}">
        <input type="hidden" id="avatar_path" value="">

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Avatar Section with Drag & Drop --}}
                <div class="md:col-span-2">
                    <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh đại diện</label>
                    <div
                        id="avatarDropZone"
                        class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div class="w-[150px] aspect-square rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="avatarPreview" alt="avatar preview" class="h-full w-full object-cover hidden">
                            <svg id="avatarPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z" />
                            </svg>
                        </div>

                        <div class="flex-1">
                            <div class=" text-gray-600 dark:text-gray-300">Kéo & thả ảnh vào đây, hoặc</div>
                            <div class="mt-2 flex items-center gap-3">
                                <label for="avatar" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ">
                                    Chọn ảnh
                                </label>
                                <button
                                    id="btnClearNewAvatar"
                                    type="button"
                                    class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700 "
                                >
                                    Xóa ảnh mới
                                </button>
                                <input
                                    id="avatar"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                />
                            </div>
                            <div class="mt-2  text-gray-500 dark:text-gray-400">Hỗ trợ PNG, JPG, WEBP — Tối đa 10MB</div>
                        </div>
                    </div>
                </div>

                {{-- Fullname --}}
                <div class="">
                    <label name="fullname_LABEL"  for="fullname" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        id="fullname"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                        placeholder="Nhập họ tên"
                    />
                    <span id="fullname_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                {{-- Email --}}
                <div>
                    <label name="email_LABEL" for="email" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Email <span class="text-red-500">*</span></label>
                    <input
                        type="email"
                        id="email"
                        required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                        placeholder="Nhập email"
                    />
                    <span id="email_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                {{-- Password --}}
                <div>
                    <label name="password_LABEL" for="password" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">
                        Password {!! $mode === 'CREATE' ? '<span class="text-red-500">*</span>' : '' !!}
                    </label>
                    <input
                        type="password"
                        id="password"
                        {{ $mode === 'CREATE' ? 'required' : '' }}
                        placeholder="{{ $mode === 'EDIT' ? 'Để trống nếu không đổi mật khẩu' : 'Nhập mật khẩu' }}"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                    />
                    <span id="password_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                {{-- Birthday --}}
                <div>
                    <label for="birthday" class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Ngày sinh</label>
                    <input
                        type="date"
                        id="birthday"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                    />
                </div>

                {{-- Roles --}}
                <div class="md:col-span-2">
                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Vai trò <span class="text-red-500">*</span></label>
                    
                    {{-- Custom Multi-Select with Search --}}
                    <div class="relative">
                        {{-- Selected Items Display --}}
                        <div id="selectedRolesContainer" class="min-h-[44px] w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 cursor-pointer flex flex-wrap gap-2 items-center">
                            <div id="selectedRolesList" class="flex flex-wrap gap-2 flex-1">
                                <span class="text-gray-400 dark:text-gray-500 text-sm">Đang tải vai trò...</span>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        {{-- Dropdown Menu --}}
                        <div id="rolesDropdown" class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-80 overflow-hidden flex flex-col">
                            {{-- Search Input --}}
                            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="roleSearchInput" 
                                        placeholder="Tìm kiếm vai trò..."
                                        class="w-full px-3 py-2 pl-9 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                    />
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- Options List --}}
                            <div id="rolesOptionsList" class="overflow-y-auto flex-1">
                                <div class="flex items-center justify-center py-8">
                                    <div class="h-8 w-8 rounded-full border-2 border-blue-500 border-t-transparent animate-spin"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <p class="ml-4 text-sm text-gray-500 dark:text-gray-400 mt-1">Chọn ít nhất một vai trò cho người dùng</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    onclick="handleBack()"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                >
                    Hủy
                </button>
                <button
                    type="submit"
                    id="submitBtn"
                    class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                    {{ $mode === 'CREATE' ? 'Tạo' : 'Cập nhật' }}
                </button>
            </div>
        </div>
    </form>

</div>

<script>
    let rolesList = [];
    let selectedRoles = [];
    let isDropdownOpen = false;
    const mode = '{{ $mode }}'; // CREATE or EDIT
    const userId = @if($mode === 'EDIT' && isset($userId)) {{ $userId }} @else null @endif;
    let existingThumbnail = null;
    let thumbnailPreview = null;
    let isDragActive = false;

    document.addEventListener('DOMContentLoaded', async function() {
        // 1. Tạo một mảng chứa các promise bắt buộc phải chạy
        const promises = [loadRoles()];

        // 2. Nếu thỏa điều kiện, thêm promise thứ 2 vào mảng
        if (mode === 'EDIT' && userId) {
            promises.push(loadUserData(userId));
        }

        // 3. Chạy tất cả cùng lúc
        await Promise.all(promises);

        if (rolesList.length > 0) {
            renderRolesOptions();
            updateSelectedRolesDisplay();
        }

        initAvatarPreviewForm();
        initRoleSelector();
    });

    function handleBack() {
        window.location.href = '{{ route('admin.users.list') }}';
    }

    async function loadRoles() {
        try {
            const data = await apiRequest('/roles?sort_by=level&order_by=asc&page=1&per_page=1000');
            if (data.success) {
                rolesList = data.data.data || [];
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải danh sách vai trò: ' + error.message, 'error');
        }
    }

    function initRoleSelector() {
        const container = document.getElementById('selectedRolesContainer');
        const dropdown = document.getElementById('rolesDropdown');
        const searchInput = document.getElementById('roleSearchInput');

        // Toggle dropdown
        container.addEventListener('click', (e) => {
            if (e.target.closest('.role-tag-remove')) return;
            toggleDropdown();
        });

        // Search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            filterRoles(searchTerm);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#selectedRolesContainer') && !e.target.closest('#rolesDropdown')) {
                closeDropdown();
            }
        });

        // Prevent dropdown close when clicking inside
        dropdown.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    }

    function toggleDropdown() {
        const dropdown = document.getElementById('rolesDropdown');
        isDropdownOpen = !isDropdownOpen;
        
        if (isDropdownOpen) {
            dropdown.classList.remove('hidden');
            document.getElementById('roleSearchInput').focus();
        } else {
            dropdown.classList.add('hidden');
        }
    }

    function closeDropdown() {
        const dropdown = document.getElementById('rolesDropdown');
        isDropdownOpen = false;
        dropdown.classList.add('hidden');
        document.getElementById('roleSearchInput').value = '';
        filterRoles('');
    }

    function filterRoles(searchTerm) {
        const optionsList = document.getElementById('rolesOptionsList');
        // Lọc roles: loại bỏ những role đã được chọn
        const filteredRoles = rolesList.filter(role => {
            const isNotSelected = !selectedRoles.some(r => r.id === role.id);
            const matchesSearch = role.name.toLowerCase().includes(searchTerm);
            return isNotSelected && matchesSearch;
        });

        if (filteredRoles.length === 0) {
            optionsList.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">Không tìm thấy vai trò</p>
                </div>
            `;
            return;
        }

        let html = '';
        filteredRoles.forEach(role => {
            html += `
                <div onclick="toggleRole(${role.id})" 
                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer transition-colors">
                    <div class="w-5 h-5 flex items-center justify-center">
                        <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded"></div>
                    </div>
                    <span class="text-gray-700 dark:text-gray-300">${role.name}</span>
                </div>
            `;
        });
        optionsList.innerHTML = html;
    }

    function renderRolesOptions() {
        filterRoles('');
    }

    function toggleRole(roleId) {
        const role = rolesList.find(r => r.id === roleId);
        if (!role) return;

        const index = selectedRoles.findIndex(r => r.id === roleId);
        if (index > -1) {
            selectedRoles.splice(index, 1);
        } else {
            selectedRoles.push(role);
        }

        // Clear search input và reset danh sách
        const searchInput = document.getElementById('roleSearchInput');
        if (searchInput) {
            searchInput.value = '';
        }

        updateSelectedRolesDisplay();
        renderRolesOptions();
    }

    function removeRole(roleId) {
        selectedRoles = selectedRoles.filter(r => r.id !== roleId);
        updateSelectedRolesDisplay();
        renderRolesOptions();
    }

    function updateSelectedRolesDisplay() {
        const container = document.getElementById('selectedRolesList');
        
        if (selectedRoles.length === 0) {
            container.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">Chọn vai trò...</span>';
            return;
        }

        let html = '';
        selectedRoles.forEach(role => {
            html += `
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">
                    ${role.name}
                    <button type="button" onclick="event.stopPropagation(); removeRole(${role.id})" class="role-tag-remove hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
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
                document.getElementById('avatar_path').value = user.avatar_path || '';

                // Set avatar preview
                const avatarEl = document.getElementById('avatarPreview');
                const placeholderEl = document.getElementById('avatarPlaceholder');
                if (user.avatar_path) {
                    avatarEl.src = user.avatar_path;
                    avatarEl.classList.remove('hidden');
                    placeholderEl.classList.add('hidden');
                    existingThumbnail = user.avatar_path;
                } else {
                    const name = user.fullname || user.email || 'User';
                    avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`;
                    avatarEl.classList.remove('hidden');
                    placeholderEl.classList.add('hidden');
                }

                // Set selected roles
                selectedRoles = user.roles || [];
                updateSelectedRolesDisplay();
                renderRolesOptions();
            }
        } catch (error) {
            showNotificationModel_Global('Không thể tải thông tin người dùng: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.users.list') }}';
            }, 2000);
        }
    }

    function handleThumbnailFile(file) {
        const MAX_SIZE = 2 * 1024 * 1024; // 2MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (thumbnailPreview) {
            try {
                URL.revokeObjectURL(thumbnailPreview);
            } catch (e) {
                // noop
            }
        }

        if (!file) {
            const avatarEl = document.getElementById('avatarPreview');
            const placeholderEl = document.getElementById('avatarPlaceholder');
            const clearBtn = document.getElementById('btnClearNewAvatar');
            const input = document.getElementById('avatar');

            if (existingThumbnail) {
                avatarEl.src = existingThumbnail;
                avatarEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                avatarEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
            clearBtn.classList.add('hidden');
            if (input) input.value = '';
            thumbnailPreview = null;
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            showNotificationModel_Global('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            showNotificationModel_Global('Ảnh quá lớn. Kích thước tối đa 2MB.', 'error');
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        thumbnailPreview = previewUrl;

        const avatarEl = document.getElementById('avatarPreview');
        const placeholderEl = document.getElementById('avatarPlaceholder');
        const clearBtn = document.getElementById('btnClearNewAvatar');

        avatarEl.src = previewUrl;
        avatarEl.classList.remove('hidden');
        placeholderEl.classList.add('hidden');
        clearBtn.classList.remove('hidden');
    }

    function initAvatarPreviewForm() {
        const dropZone = document.getElementById('avatarDropZone');
        const input = document.getElementById('avatar');
        const clearBtn = document.getElementById('btnClearNewAvatar');
        const avatarEl = document.getElementById('avatarPreview');
        const placeholderEl = document.getElementById('avatarPlaceholder');

        // Default preview for create mode
        if (!existingThumbnail && mode === 'CREATE') {
            avatarEl.classList.add('hidden');
            placeholderEl.classList.remove('hidden');
        }

        // Drag and drop handlers
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                isDragActive = true;
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
            });

            dropZone.addEventListener('dragleave', () => {
                isDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                isDragActive = false;
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');

                const file = e.dataTransfer.files && e.dataTransfer.files[0] ? e.dataTransfer.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleThumbnailFile(null);
            });
        }
    }

    async function saveUser(event) {
        event.preventDefault();
        renderInputErrors_Global(null, true); // Clear previous errors

        const userIdValue = document.getElementById('userId').value;
        const submitBtn = document.getElementById('submitBtn');

        // Disable submit button
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';
        }

        const email = document.getElementById('email').value;
        const fullname = document.getElementById('fullname').value;
        const birthday = document.getElementById('birthday').value || null;
        const avatarPath = document.getElementById('avatar_path').value || null;
        const password = document.getElementById('password').value;
        const fileInput = document.getElementById('avatar');
        const avatarFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

        if (selectedRoles.length === 0) {
            showNotificationModel_Global('Vui lòng chọn ít nhất một vai trò', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = mode === 'CREATE' ? 'Tạo' : 'Cập nhật';
            }
            return;
        }
        const roleIds = selectedRoles.map(r => r.id);

        const url = mode === 'EDIT' ? `/users/${userIdValue}` : '/users';
        const method = mode === 'EDIT' ? 'PUT' : 'POST';

        try { 
            const fd = new FormData();
            if (mode === 'EDIT') {
                fd.append('_method', 'PUT');
            }
            fd.append('email', email);
            if (fullname) fd.append('fullname', fullname);
            if (birthday) fd.append('birthday', birthday);
            if (existingThumbnail) fd.append('existingThumbnail', existingThumbnail);
            if (avatarPath) fd.append('avatar_path', avatarPath);
            if (password) fd.append('password', password);
            roleIds.forEach(id => fd.append('role_ids[]', id));
            if (avatarFile) fd.append('avatar', avatarFile);

            const response = await fetch(`/api${url}`, {
                method: 'POST',
                body: fd,
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showNotificationModel_Global(mode === 'EDIT' ? 'Cập nhật thông tin người dùng thành công' : 'Tạo người dùng thành công', 'success', handleBack);
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

</script>
@endsection

