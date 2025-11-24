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
                    class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-tab-target="TAB_USER_INFO">
                    <i class="fa-regular fa-bookmark"></i>
                    <span>Thông tin người dùng</span>
                </button>
                <button type="button"
                        class="tab-trigger inline-flex items-center gap-2 rounded-t-xl px-5 py-2.5 font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-white/70 dark:hover:bg-gray-800/90 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        data-tab-target="TAB_COURSES">
                    <i class="fa-regular fa-clipboard"></i>
                    <span>Khóa học</span>
                    <span id="lessonsTabCount" class="hidden rounded-full bg-gray-200 dark:bg-gray-800 px-2 py-0.5  font-semibold text-gray-600 dark:text-gray-300"></span>
                </button>
            </div>

            {{-- Content area --}}
            <div class="flex-1 min-h-0 h-full bg-white dark:bg-gray-800 rounded-b-xl overflow-hidden">
                {{-- user Information --}}
                <div class="tab-panel h-full max-h-full overflow-y-auto" data-tab-content="TAB_USER_INFO">
                    
                    {{-- <div class="w-[70%] max-w-[800px] p-6 mx-auto translate-y-[25%] flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"> --}}
                    <div class="p-10 pt-20 flex flex-col items-stretch justify-start gap-6 bg-white dark:bg-gray-800 overflow-hidden">

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
                            <a id="editButton" href="#"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5 font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
                                <i class="fa-solid fa-pen"></i>
                                <span>Chỉnh sửa</span>
                            </a>
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
                <div class="tab-panel hidden h-full flex flex-col items-stretch justify-start" data-tab-content="TAB_COURSES">
                    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
                        <div class="flex items-center gap-3"></div>
                        <a id="manageCoursesButton" href="#"
                            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
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

</script>
@endsection
