@extends('admin.layout')

@section('title', 'Chi tiết người dùng')
@section('description', 'Xem thông tin chi tiết của người dùng')

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
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- User Detail Card (Tabs) --}}
    <div id="userDetailCard" class="hidden w-full max-w-[1400px] h-full mx-auto min-h-0"> {{-- cho phép co giãn & cuộn --}}
        <div class="h-full flex flex-col items-stretch justify-start">
            {{-- Tabs header --}}
            <div class="relative bg-transparent">
                <button type="button"
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-900/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_USER_INFO">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Thông tin người dùng</span>
                </button>
                <button type="button"
                        class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-900/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        data-tab-target="TAB_COURSES">
                    <i class="fa-regular fa-clipboard"></i>
                    <span>Khóa học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-900 rounded-b-xl overflow-hidden">
                {{-- user Information --}}
                <div class="tab-panel max-h-full overflow-y-auto" data-tab-content="TAB_USER_INFO">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none rounded-b-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="editButton" href="#"
                           class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                            <i class="fa-solid fa-pen"></i>
                            <span>Chỉnh sửa</span>
                        </a>
                    </div>

                    <div class="space-y-6 p-6">
                        {{-- Personal Information Section --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Avatar Section --}}
                            <div class="col-span-1 row-span-5">
                                <label class="block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-2">Ảnh đại diện</label>
                                <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                                    <div class="rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                        <img id="userAvatar" src="" alt="Avatar" class="w-[300px] aspect-square object-cover">
                                    </div>
                                    <div class="flex-1">
                                        {{-- <div class="text-sm text-gray-600 dark:text-gray-300">Ảnh đại diện của người dùng</div> --}}
                                    </div>
                                </div>
                            </div>

                            @php
                                $infoField = function($label, $id) {
                                    return <<<HTML
                                    <div>
                                        <label class="ml-4 block text-sm font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                                        <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                            <p class="{$id} text-sm text-gray-900 dark:text-gray-100 break-all">-</p>
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
                        </div>
                    </div> 
                </div>

                {{-- Courses --}}
                <div class="tab-panel hidden h-full flex flex-col items-stretch justify-start" data-tab-content="TAB_COURSES">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="manageCoursesButton" href="#"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                            <i class="fa-solid fa-pen"></i>
                            <span>Quản lý khóa học</span>
                        </a>
                    </div>
                    <div class="flex-1 min-h-0 p-6 pt-0">
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
            button.classList.toggle('dark:bg-gray-900', isActive);
            button.classList.toggle('border-gray-200', isActive);
            button.classList.toggle('dark:border-gray-700', isActive);
            button.classList.toggle('border-transparent', !isActive);
            button.classList.toggle('border-b-0', isActive);
            button.classList.toggle('text-gray-900', isActive);
            button.classList.toggle('dark:text-gray-100', isActive);
            button.classList.toggle('text-gray-500', !isActive);
            button.classList.toggle('dark:text-gray-400', !isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
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

    async function loadUserData() {
        try {
            const data = await apiRequest(`/users/${userId}`);
            if (data?.success) {
                return data.data;
            } else {
                showNotificationModel_Global(data?.message || 'Không thể tải thông tin người dùng', 'error');
                return null;
            }
        } catch (error) {
            showNotificationModel_Global(error.message || 'Đã xảy ra lỗi khi tải thông tin', 'error');
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
        document.getElementById('editButton').href = `/admin/users/${userData.id}/edit`;
        setValueById('userId', userData.id);
        setValueById('userEmail', userData.email);
        setValueById('userEmailDetail', userData.email);
        setValueById('userFullname', userData.fullname || 'Chưa có tên');
        setValueById('userFullnameDetail', userData.fullname || 'Chưa có tên');
        setValueById('userRoles', userData.roles.map(r => r.name).join(', ') || 'Chưa có vai trò');
        setValueById('userBirthday', formatDate_Global(userData.birthday));
        setValueById('userCreatedAt', formatDate_Global(userData.created_at));

        // const status = (userData.status || '').toString().toLowerCase();
        // const badge = document.getElementById('userStatusBadge');
        // if (badge) {
        //     const map = {
        //         active: ['bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'Active', 'bg-emerald-500'],
        //         pending: ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'Pending', 'bg-amber-500'],
        //         banned: ['bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300', 'Banned', 'bg-rose-500'],
        //     };
        //     const [cls, label, dot] = map[status] || map['active'];
        //     badge.className = `inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-full ${cls}`;
        //     badge.innerHTML = `<span class="inline-block h-1.5 w-1.5 rounded-full ${dot}"></span><span>${label}</span>`;
        // }

        // Set avatar preview
        const avatarEl = document.getElementById('userAvatar');
        avatarEl.src = userData.avatar_path || '';  
    } 

    function renderCourses() {
        const courses = Array.isArray(userData.courses) ? userData.courses : [];
        const coursesList = document.getElementById('coursesList');
        if (!courses.length) {
            coursesList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Người dùng chưa tham gia khóa học nào</p>`;
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
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(c.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ${escapeHtml_Global(String(c.id ?? '-'))}</p>
                        </div>
                    </div>
                    ${c.description ? `
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">${escapeHtml_Global(String(c.description)).slice(0, 80)}${String(c.description).length > 80 ? '…' : ''}</span>
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

</script>
@endsection
