@extends('admin.layout')

@section('title', 'Hồ sơ cá nhân')

@section('description', 'Thông tin hồ sơ cá nhân của bạn.')

@section('content')

<div class="w-full h-full flex flex-col overflow-hidden">

    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 overflow-auto p-6 sm:p-8">
        <div class="w-fit mx-auto mt-[20dvh]">
            <div id="SPINNER_LOADING">
                <div id="SPINNER_LOADING_CONTAINER">
                    <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                        <div></div><div></div><div></div><div></div>
                        <div></div><div></div><div></div><div></div>
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

    <div id="userInfoPanel" class="hidden flex flex-col items-stretch justify-start gap-6 p-4 sm:p-6">
        <div class="w-[70%] max-w-[800px] p-6 mx-auto flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            <div class="flex flex-row items-start justify-between">
                <div class="flex flex-row items-center gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-full shadow-sm">
                        <img id="userAvatar" src="" alt="Avatar" class="w-40 h-40 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl bg-gray-100">
                    </div>
                    <div class="flex flex-col items-start justify-center gap-2">
                        <span class="userEmail text-xl text-gray-500 dark:text-white text-center">-</span>
                        <h2 class="userFullname text-2xl font-bold text-gray-900 dark:text-white text-center">-</h2>
                        <span class="userRoles px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300  font-bold border border-blue-100 dark:border-blue-800">-</span>
                    </div>
                </div>
                <button type="button" onclick="handleGotoOtherPageOfProfile('EDIT')"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
                    </svg>
                    <span>Cập nhật</span>
                </button>
            </div>

            {{-- Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $infoField = function($label, $id) {
                        return <<<HTML
                        <div>
                            <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                            <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <p class="{$id}  text-gray-900 dark:text-gray-100 break-all">-</p>
                            </div>
                        </div>
                        HTML;
                    };
                @endphp


                {!! $infoField('ID người dùng', 'userId') !!}
                {!! $infoField('Email', 'userEmail') !!}
                {!! $infoField('Họ và tên', 'userFullname') !!}
                {!! $infoField('Vai trò', 'userRoles') !!}
                {!! $infoField('Ngày tạo', 'userCreatedAt') !!}
                {!! $infoField('Ngày sinh', 'userBirthday') !!}

            </div>
        </div>
        <div class="w-[70%] max-w-[800px] p-6 mx-auto flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <button type="button" onclick="openVerifyLogoutModal(event)"
                class="w-full group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 group-hover:bg-red-100 dark:group-hover:bg-red-900/40 flex items-center justify-center transition-colors text-red-500 dark:text-red-400">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M160 96c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 32C43 32 0 75 0 128L0 384c0 53 43 96 96 96l64 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l64 0zM502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 192 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l210.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"/>
                    </svg>
                </div>
                Đăng xuất
            </button>
        </div>
    </div>

</div>

<script>
    let userData = null;

    document.addEventListener('DOMContentLoaded', async () => {
        userData = await loadUserData();
        if (userData != null) {
            renderUserInfo();
        }
    });

    async function loadUserData() {
        try {
            const data = await apiRequest(`/auth/me`);
            if (data?.success) {
                return data.data;
            } else {
                NotificationModal.show(data?.message || 'Không thể tải thông tin người dùng', 'error');
                return null;
            }
        } catch (error) {
            NotificationModal.show(error.message || 'Đã xảy ra lỗi khi tải thông tin', 'error');
            return null;
        }
    }

    async function loadUserCourses() {
        try {
            const data = await apiRequest(`/student/courses`);
            if (data?.success) {
                return data.data;
            }
            return [];
        } catch (error) {
            console.warn('Could not load courses', error);
            return [];
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('userInfoPanel').classList.toggle('hidden', !detail);
    }

    function renderUserInfo() {
        toggleStates({ loading: false, detail: true, error: false });
        setValueById('userId', userData.id);
        setValueById('userEmail', userData.email);
        setValueById('userFullname', userData.fullname || 'Chưa có tên');
        setValueById('userRoles', userData.roles.map(r => r.name).join(', ') || 'Chưa có vai trò');
        setValueById('userCreatedAt', formatDate_Global(userData.created_at));

        // Format birthday
        let birthdayStr = 'Chưa cập nhật';
        if (userData.birthday) {
            const date = new Date(userData.birthday);
            if (!isNaN(date.getTime())) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                birthdayStr = `${day}/${month}/${year}`;
            }
        }
        setValueById('userBirthday', birthdayStr);

        // Set avatar preview
        const avatarEl = document.getElementById('userAvatar');
        avatarEl.src = userData.avatar_path || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(userData.fullname || 'User') + '&background=random';
    }

    function renderCourses() {
        const courses = Array.isArray(userCourses) ? userCourses : [];
        const coursesList = document.getElementById('coursesList');

        const lessonsTabCount = document.getElementById('lessonsTabCount');
        if (lessonsTabCount) {
            lessonsTabCount.textContent = courses.length;
            lessonsTabCount.classList.remove('hidden');
        }

        if (!courses.length) {
            coursesList.innerHTML = `<p class=" text-gray-500 dark:text-gray-400 italic">Bạn chưa tham gia khóa học nào</p>`;
        } else {
            coursesList.innerHTML = courses.map(c => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded bg-emerald-600 grid place-items-center">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6c-1.657-1-3.657-1.5-6-1.5V19c2.343 0 4.343.5 6 1.5M12 6c1.657-1 3.657-1.5 6-1.5V19c-2.343 0-4.343.5-6 1.5M12 6v14"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class=" font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(c.title ?? 'Không có tên')}</h4>
                            <p class=" text-gray-500 dark:text-gray-400 mt-1">ID: ${escapeHtml_Global(String(c.id ?? '-'))}</p>
                        </div>
                    </div>
                    ${c.description ? `
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class=" text-gray-500 dark:text-gray-400 truncate block">${escapeHtml_Global(String(c.description)).slice(0, 80)}${String(c.description).length > 80 ? '…' : ''}</span>
                        </div>` : ''
                    }
                </div>
            `).join('');
        }
    }

    function setValueById(id, value) {
        const el = document.getElementsByClassName(id);
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = value != null && value !== '' ? value : '-';
        }
    }

    const editProfileRouteSystemName = '{{ route('admin.profile.edit') }}';

    function handleGotoOtherPageOfProfile(userId = null, targetPage = 'EDIT') {
        let url = '';
        switch (targetPage) {
            case 'EDIT':
                url = editProfileRouteSystemName;
                break;
        }
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = url + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }

</script>
<script>
    async function openVerifyLogoutModal(event) {
        event.preventDefault();
        DeleteModal.openLogout({
            title: 'Xác nhận đăng xuất',
            message: 'Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?',
            actionFuncCallback: () => handleLogout(),
            successFuncCallback: () => handleGotoLoginPage(),
            failFuncCallback: () => {}
        });
    }

    function handleGotoLoginPage() {
        window.location.href = '/admin/login';
    }

    // Logout function
    async function handleLogout() {
        try {
            const response = await fetch(`/api/auth/logout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'include'
            });
            const data = await response.json();
            console.log(data);

            if (data.success) {
                return true;
            } else {
                return false;
            }
        } catch (error) {
            console.error('Logout error:', error);
            return false;
        }
    }
</script>
@endsection


