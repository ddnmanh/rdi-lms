@extends('admin.layout')

@section('title', 'Chi tiết người dùng')
@section('description', 'Xem thông tin chi tiết của người dùng')

@section('content')
<div class="w-full h-full flex flex-col gap-2 overflow-hidden">

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

        <div class="flex items-center justify-start gap-3">
            <button onclick="handleGotoEditUserPage()"
                type="button"
                class="group px-4 py-2.5 text-white bg-amber-500 hover:bg-amber-400 rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
        <path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/>
    </svg>
                <span>Chỉnh sửa</span>
            </button>
            {{-- <button onclick=""
                type="button"
                class="group px-4 py-2.5 bg-blue-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-blue-700">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"/>
                </svg>
                <span>Thêm người dùng</span>
            </button> --}}
            <button
                onclick="openSingleDeleteModal()"
                class="group px-4 py-2.5 bg-red-600 text-white rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 hover:bg-red-700">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
                    <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                </svg>
                <span>Xóa người dùng này</span>
            </button>
        </div>
    </div>

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

    {{-- User Detail Card (Tabs) --}}
    <div id="userDetailCard" class="hidden w-full h-full mx-auto min-h-0"> {{-- cho phép co giãn & cuộn --}}
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Tabs header --}}
            <div class="relative bg-transparent">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_USER_INFO">
                    <div>
                        <svg class="w-4 h-4" viewBox="0 0 384 512" fill="currentColor">
                            <path d="M0 64C0 28.7 28.7 0 64 0L320 0c35.3 0 64 28.7 64 64l0 417.1c0 25.6-28.5 40.8-49.8 26.6L192 412.8 49.8 507.7C28.5 521.9 0 506.6 0 481.1L0 64zM64 48c-8.8 0-16 7.2-16 16l0 387.2 117.4-78.2c16.1-10.7 37.1-10.7 53.2 0L336 451.2 336 64c0-8.8-7.2-16-16-16L64 48z"/>
                        </svg>
                    </div>
                    <span>Thông tin người dùng</span>
                </button>
                <button type="button"
                        class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        data-tab-target="TAB_COURSES">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/>
                    </svg>
                    <span>Khóa học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5  font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-800 rounded-b-xl overflow-hidden">
                {{-- user Information --}}
                <div class="tab-panel h-full max-h-full overflow-y-auto" data-tab-content="TAB_USER_INFO">

                    {{-- <div class="w-[70%] max-w-[800px] p-6 mx-auto translate-y-[25%] flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"> --}}
                    <div class="p-10 pt-10 flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 overflow-hidden">

                        <div class="flex flex-row items-start justify-between">
                            <div class="flex flex-row items-center gap-4">
                                <div class="bg-white dark:bg-gray-800 rounded-full shadow-sm">
                                    <img id="userAvatar" src="" alt="Avatar" class="w-40 h-40 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl bg-gray-100">
                                </div>
                                <div class="flex flex-col items-start justify-center gap-2">
                                    <span class="userEmail text-xl text-gray-500 dark:text-white text-center">-</span>
                                    <h2 class="userFullname text-3xl font-bold text-gray-900 dark:text-white text-center">-</h2>
                                    <span class="userRoles px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300  font-bold border border-blue-100 dark:border-blue-800">-</span>
                                </div>
                            </div>
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

                </div>

                {{-- Courses --}}
                <div class="tab-panel px-6 py-6 hidden h-full flex flex-col items-stretch justify-start" data-tab-content="TAB_COURSES">
                    <div class="flex-1 min-h-0">
                        <div id="coursesList" class="space-y-2 h-full overflow-y-auto">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function setupTabs() {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const firstTab = tabButtons[0]?.dataset.tabTarget;
        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });
        if (firstTab) {
            activateTab(firstTab);
        }
    }

    function activateTab(target) {
        const tabButtons = document.querySelectorAll('.tab-trigger');
        const tabPanels = document.querySelectorAll('.tab-panel');
        tabButtons.forEach(button => {
            const isActive = button.dataset.tabTarget === target;
            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('dark:bg-gray-800', isActive);
            button.classList.toggle('border-gray-200', isActive);
            button.classList.toggle('dark:border-gray-700', isActive);
            button.classList.toggle('border-transparent', !isActive);
            button.classList.toggle('border-b-0', isActive);
            button.classList.toggle('text-gray-900', isActive);
            button.classList.toggle('dark:text-gray-100', isActive);
            button.classList.toggle('text-gray-500', !isActive);
            button.classList.toggle('dark:text-gray-400', !isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            button.classList.toggle('hover:text-gray-900', !isActive);
            button.classList.toggle('dark:hover:text-gray-100', !isActive);
            button.classList.toggle('hover:bg-white/70', !isActive);
            button.classList.toggle('dark:hover:bg-gray-800/90', !isActive);
        });
        tabPanels.forEach(panel => {
            const isActive = panel.dataset.tabContent === target;
            panel.classList.toggle('hidden', !isActive);
        });
    }
</script>

<script>
    const userId = {{ $userId }};
    let userData = null;

    document.addEventListener('DOMContentLoaded', async () => {
        setupTabs();
        userData = await loadUserData();
        if (userData != null) {
            renderUserInfo();
            renderCourses();
        }
    });

    // Di chuyển đến trang chỉnh sửa người dùng, đồng thời gửi kèm url hiện tại
    function handleGotoEditUserPage() {
        const currentRoute = window.location.pathname + (window.location.search || '');
        window.location.href = '{{ route('admin.users.edit', ['id' => $userId]) }}' + '?prev_page_url=' + encodeURIComponent(currentRoute);
    }

    async function loadUserData() {
        try {
            const data = await UserProvider.handleGetUserById(userId);
            if (data?.success) {
                return data.data;
            } else {
                NotificationModal.show(data?.message || 'Không thể tải thông tin người dùng', 'error', handleGotoBackPage_Global);
                return null;
            }
        } catch (error) {
            NotificationModal.show(error.message || 'Đã xảy ra lỗi khi tải thông tin', 'error', handleGotoBackPage_Global);
            return null;
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('userDetailCard').classList.toggle('hidden', !detail);
        // document.getElementById('errorState').classList.toggle('hidden', !error);
    }

    function renderUserInfo() {
        toggleStates({ loading: false, detail: true, error: false });
        setValueById('userId', userData.id);
        setValueById('userEmail', userData.email);
        setValueById('userEmailDetail', userData.email);
        setValueById('userFullname', userData.fullname || 'Chưa có tên');
        setValueById('userFullnameDetail', userData.fullname || 'Chưa có tên');
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
        const courses = Array.isArray(userData.courses) ? userData.courses : [];
        const coursesList = document.getElementById('coursesList');
        if (!courses.length) {
            coursesList.innerHTML = `<p class=" text-gray-500 dark:text-gray-400 italic">Người dùng chưa tham gia khóa học nào</p>`;
        } else {
            coursesList.innerHTML = courses.map(c => `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-[80px] 3xl:w-[100px]">
                            <img src="${c.thumbnail_path}" class="w-full aspect-video m-auto object-cover rounded-lg bg-gray-200 dark:bg-gray-700" >
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class=" font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(c.title ?? 'Không có tên')}</h4>
                            <span class=" text-gray-500 dark:text-gray-400 truncate block">
                                ${
                                    c.description
                                    ? (escapeHtml_Global(String(c.description)).slice(0, 80) + (String(c.description).length > 80 ? '…' : ''))
                                    : '-'
                                }
                            </span>
                        </div>
                    </div>
                    ${c.progress ? `
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <div class="relative w-12 h-12 flex-shrink-0">
                                <div class="w-full h-full rounded-full flex items-center justify-center"
                                    style="background: conic-gradient(
                                        ${
                                            Math.min(Math.max(c.progress.completion_percentage || 0, 0), 100) > 80
                                                ? '#10b981'
                                                : Math.min(Math.max(c.progress.completion_percentage || 0, 0), 100) >= 50
                                                    ? '#3b82f6'
                                                    : '#f59e0b'
                                        } ${Math.min(Math.max(c.progress.completion_percentage || 0, 0), 100) * 3.6}deg,
                                        rgb(229 231 235 / 0.3) 0deg
                                    );">
                                    <div class="w-[calc(100%-7px)] aspect-square rounded-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                        <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">${Math.round(Math.min(Math.max(c.progress.completion_percentage || 0, 0), 100))}%</span>
                                    </div>
                                </div>
                            </div>
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

    // Xử lý xóa một người dùng
    async function openSingleDeleteModal(userId = userData.id, userName = userData.fullname || '-', userEmail = userData.email || '') {
        DeleteModal.openSingle({
            objectName: OBJECTNAMEMODAL.USER,
            idDelete: userId,
            nameValue: userName,
            descValue: userEmail,
            actionFuncCallback: async () => {
                const result = await UserProvider.handleDeleteUsers([userId]);
                return result.success;
            },
            successFuncCallback: async () => {
                NotificationModal.show(`Đã xóa người dùng thành công`, 'success', handleGotoBackPage_Global);
            },
            failFuncCallback: () => {
                NotificationModal.show(`Không thể xóa người dùng`, 'error');
            }
        });
    }

</script>
@endsection
