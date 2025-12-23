@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm người dùng' : 'Chỉnh sửa người dùng')

@section('description', $mode === 'CREATE' ? 'Thêm người dùng mới vào hệ thống' : 'Chỉnh sửa thông tin người dùng')

@section('content')
<div class="h-full w-full max-w-[1600px] mx-auto flex flex-col items-stretch justify-start gap-4 3xl:gap-6">

    {{-- Header Bar --}}
    <div class="p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">
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
    <form id="userForm" onsubmit="saveUser(event)" class="w-full mx-auto p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
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
                    @include('components.date-picker', [
                        'id' => 'birthday',
                        'placeholder' => 'Chọn ngày sinh...'
                    ])
                </div>

                {{-- Roles --}}
                <div class="md:col-span-2">
                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Vai trò <span class="text-red-500">*</span></label>

                    {{-- Custom Multi-Select with Search --}}
                    @include('components.multi-select', [
                        'id' => 'roles',
                        'placeholder' => 'Chọn vai trò...',
                        'loadingText' => 'Đang tải vai trò...',
                        'searchPlaceholder' => 'Tìm kiếm vai trò...',
                        'emptyText' => 'Không tìm thấy vai trò'
                    ])

                    <p class="ml-4 text-sm text-gray-500 dark:text-gray-400 mt-1">Chọn ít nhất một vai trò cho người dùng</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    onclick="handleGotoBackPage_Global()"
                    class="px-4 py-2.5 font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
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
    const mode = '{{ $mode }}'; // CREATE or EDIT
    const userId = @if($mode === 'EDIT' && isset($userId)) {{ $userId }} @else null @endif;
    let userData = null;
    let existingThumbnail = null;
    let thumbnailPreview = null;
    let isDragActive = false;

    const prevPageUrl = new URLSearchParams(window.location.search).get('prev_page_url');

    document.addEventListener('DOMContentLoaded', async function() {

        // Load user data nếu là EDIT mode
        if (mode === 'EDIT' && userId) {
            userData = await loadUserData(userId);
            if (userData == null) {
                NotificationModal.show('Không thể tải thông tin người dùng', 'error', handleGotoBackPage_Global);
                return;
            } else {
                fillUserDataToForm();
            }
        }

        // Khởi tạo MultiSelect cho roles
        MultiSelect.init('roles', {
            loadData: async () => {
                try {
                    const data = await RoleProvider.handleGetRoles({
                        sort_by: 'level',
                        order_by: 'asc',
                        page: 1,
                        per_page: 1000
                    });
                    return data?.success ? (data?.data?.data || []) : [];
                } catch (error) {
                    NotificationModal.show('Không thể tải danh sách vai trò: ' + error.message, 'error');
                    return [];
                }
            },
            displayField: 'name',
            valueField: 'id',
            placeholder: 'Chọn vai trò...',
            emptyText: 'Không có vai trò phù hợp'
        });
        MultiSelect.setSelected('roles', userData?.roles || []);

        // Khởi tạo DatePicker cho birthday
        DatePicker.init('birthday', {
            minDate: DatePicker.addYears(DatePicker.today(), -120), // Không cho chọn trước năm 120 tuổi
            maxDate: DatePicker.addYears(DatePicker.today(), -18), // Không cho chọn ngày dưới 18 tuổi
            defaultValue: userData?.birthday || null
        });

        initAvatarPreviewForm();
    });

    async function loadUserData(id) {
        try {
            const data = await UserProvider.handleGetUserById(id);
            if (data?.success) {
                return data?.data;
            } else {
                return null;
            }
        } catch (error) {
            return null;
        }
    }

    function fillUserDataToForm() {
        document.getElementById('email').value = userData?.email || '';
        document.getElementById('fullname').value = userData?.fullname || '';
        document.getElementById('avatar_path').value = userData?.avatar_path || '';
        // Birthday sẽ được set trong DatePicker.init() với defaultValue

        // Set avatar preview
        const avatarEl = document.getElementById('avatarPreview');
        const placeholderEl = document.getElementById('avatarPlaceholder');
        if (userData.avatar_path) {
            avatarEl.src = userData?.avatar_path;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
            existingThumbnail = userData?.avatar_path;
        } else {
            const name = userData?.fullname || userData?.email || 'User';
            avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
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
            NotificationModal.show('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            NotificationModal.show('Ảnh quá lớn. Kích thước tối đa 2MB.', 'error');
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
        const birthday = DatePicker.getValue('birthday') || null;
        const avatarPath = document.getElementById('avatar_path').value || null;
        const password = document.getElementById('password').value;
        const fileInput = document.getElementById('avatar');
        const avatarFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

        const selectedRoles = MultiSelect.getSelected('roles');
        if (selectedRoles.length === 0) {
            NotificationModal.show('Vui lòng chọn ít nhất một vai trò', 'error');
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
                NotificationModal.show(mode === 'EDIT' ? 'Cập nhật thông tin người dùng thành công' : 'Tạo người dùng thành công', 'success', handleGotoBackPage_Global);
            } else if (response.status === 422) {
                let errorsField = data.errors || null;
                renderInputErrors_Global(errorsField);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

        } catch (error) {
            NotificationModal.show(error.message || 'Thao tác thất bại', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = mode === 'CREATE' ? 'Tạo' : 'Cập nhật';
            }
        }
    }

</script>
@endsection

