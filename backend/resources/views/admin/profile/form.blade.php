@extends('admin.layout')

@section('title', 'Chỉnh sửa hồ sơ')

@section('description', 'Chỉnh sửa thông tin hồ sơ cá nhân của bạn.')

@section('content')
<div class="w-full max-w-[900px] my-10 mx-auto p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg text-[12px] 3xl:text-[14px]"> 
    <form class="space-y-6" onsubmit="handleUpdateUser(event)">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                    <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh đại diện</label>
                    <div
                        id="avatarDropZone"
                        class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div class="w-[120px] aspect-square rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
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
                            <div class="mt-2 text-gray-500 dark:text-gray-400">Hỗ trợ PNG, JPG, WEBP — Tối đa 10MB</div>
                        </div>
                    </div>
                </div>
            <div class="col-span-2">
                <label class="block ml-4  font-semibold text-blue-700 dark:text-gray-300 mb-2">Email</label>
                <input disabled name="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none opacity-50" placeholder="Nhập email" type="email" >
            </div>
            <div class="">
                <label class="block ml-4  font-semibold text-blue-700 dark:text-gray-300 mb-2">Họ tên</label>
                <input name="fullname" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none" placeholder="Nhập họ tên" type="text" >
            </div>
            <div>
                <label class="block ml-4  font-semibold text-blue-700 dark:text-gray-300 mb-2">Ngày sinh</label>
                <input name="birthday" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none" placeholder="Nhập ngày sinh" type="date" >
            </div>
        </div>
        <div class="flex items-center justify-end gap-3">
            <button type="button" class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">Hủy</button>
            <button type="submit" class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700">Cập nhật</button>
        </div>
    </form>
</div>

<script> 

    let userData = null;
    let existingUserAvatar = null;
    let avatarPreview = null;
    let isDragActive = false;

    document.addEventListener('DOMContentLoaded', async function() {
        userData = await loadUserData();
        if (userData) {
            existingUserAvatar = userData.avatar_path || null;
        }

        console.log(userData);
        
        renderUserData();
        initAvatarPreviewForm();
    });

    async function loadUserData() {
        try {
            const response = await fetch('/api/auth/me', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            let data = await response.json();

            if (!data.success) {
                throw new Error('Network response was not ok');
            }
            
            return data.data;
            
        } catch (error) {
            console.error('Error fetching user data:', error);
            return null;
        }
    }

    function renderUserData() {
        if (!userData) return;

        if (userData.avatar_path) {
            document.getElementById('avatarPreview').src = userData.avatar_path;
            document.getElementById('avatarPreview').classList.remove('hidden');
            document.getElementById('avatarPlaceholder').classList.add('hidden');
        } else {
            document.getElementById('avatarPreview').classList.add('hidden');
            document.getElementById('avatarPlaceholder').classList.remove('hidden');
        }

        document.querySelector('input[name="email"]').value = userData.email || '';
        document.querySelector('input[name="fullname"]').value = userData.fullname || '';
        let birthday = formatDateTimeLocal(userData.birthday);
        birthday = birthday ? birthday.split('T')[0] : '';
        document.querySelector('input[name="birthday"]').value = birthday;

    }

    function handleChoiceAvatarFile(file) {
        const MAX_SIZE = 2 * 1024 * 1024; // 2MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        console.log('Handle thumbnail file:', file);
        

        if (avatarPreview) {
            try {
                URL.revokeObjectURL(avatarPreview);
            } catch (e) {
                // noop
            }
        }

        if (!file) {
            const avatarEl = document.getElementById('avatarPreview');
            const placeholderEl = document.getElementById('avatarPlaceholder');
            const clearBtn = document.getElementById('btnClearNewAvatar');
            const input = document.getElementById('avatar');

            if (existingUserAvatar) {
                avatarEl.src = existingUserAvatar;
                avatarEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                avatarEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
            clearBtn.classList.add('hidden');
            if (input) input.value = '';
            avatarPreview = null;
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
        avatarPreview = previewUrl;

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

        // Default preview if no existing avatar
        if (!existingUserAvatar) {
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

                const files = e.dataTransfer.files;
                const file = files && files[0] ? files[0] : null;
                if (file && input) {
                    // Create a new DataTransfer to assign files to input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;
                    handleChoiceAvatarFile(file);
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleChoiceAvatarFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleChoiceAvatarFile(null);
            });
        }
    }

    async function handleUpdateUser(event) {
        event.preventDefault();
        try {
            // Implementation for updating user profile goes here
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('fullname', document.querySelector('input[name="fullname"]').value);
            formData.append('birthday', document.querySelector('input[name="birthday"]').value);
            const fileInput = document.getElementById('avatar');
            const avatarFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

            if (avatarFile) { 
                formData.append('avatar', avatarFile);
            }
            const response = await fetch('/api/auth/update-profile', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            if (data.success) {
                showNotificationModel_Global('Cập nhật hồ sơ thành công!', 'success', handleBackPrevPage); 
            } else {
                showNotificationModel_Global('Cập nhật hồ sơ thất bại. Vui lòng thử lại.', 'error');
            }
        } catch (error) {
            console.error('Error updating user profile:', error);
        }
    }

    function handleBackPrevPage() {
        window.location.href = '/admin/profile';
    }
    
</script>
@endsection

