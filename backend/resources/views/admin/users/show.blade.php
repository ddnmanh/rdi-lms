@extends('admin.layout')

@section('title', 'Chi tiết người dùng')
@section('description', 'Xem thông tin chi tiết của người dùng')

@section('content')
<div class="flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    {{-- <div class="sticky top-0 z-20">
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
    </div> --}}

    {{-- <div class="w-[100px] h-[50px] bg-red-500"></div> --}}


    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 p-6 sm:p-8">
        <div class="w-fit mx-auto mt-[20dvh]">
            <div id="SPINNER_LOADING">
                <div id="SPINNER_LOADING_CONTAINER">
                    <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                        <div></div> <div></div> <div></div> <div></div> <div></div> <div></div> <div></div> <div></div>
                    </div>
                </div>
                <div id="SPINNER_LOADING_ICON">
                    <i class="fas fa-graduation-cap"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- User Detail Card --}}
    <div id="userDetailCard" class="hidden w-full max-w-[1400px] mx-auto p-4 md:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <div></div>
            <a id="editButton" href="#"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z"/>
                </svg>
                <span>Chỉnh sửa</span>
            </a>
        </div>

        <div class="space-y-6">
            {{-- Personal Information Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Avatar Section --}}
                <div class="md:col-span-2">
                    <label class="block ml-4 text-sm font-semibold text-blue-700 dark:text-gray-300 mb-2">Ảnh đại diện</label>
                    <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                        <div class="w-[150px] aspect-square rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="userAvatar" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                            <svg id="avatarPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-8 w-8 text-gray-400">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-10 1.67-10 5v1h20v-1c0-3.33-6.67-5-10-5z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            {{-- <div class="text-sm text-gray-600 dark:text-gray-300">Ảnh đại diện của người dùng</div> --}}
                        </div>
                    </div>
                </div>

                {{-- Info Fields --}}
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

                {!! $infoField('ID', 'userId') !!}
                {!! $infoField('Email', 'userEmailDetail') !!}
                {!! $infoField('Họ và tên', 'userFullnameDetail') !!}
                {!! $infoField('Ngày sinh', 'userBirthday') !!}
                {!! $infoField('Vai trò', 'userRoles') !!}
                {!! $infoField('Ngày tạo', 'userCreatedAt') !!}
            </div>

            {{-- Courses Section --}}
            <div>
                <label class="block ml-4 text-sm font-semibold text-blue-700 dark:text-gray-300 mb-2">Khóa học</label>
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="p-6">
                        <div id="coursesList" class="space-y-2">
                            <div class="w-full flex justify-center py-8">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Error State --}}
    <div id="errorState" class="hidden flex-1 items-center justify-center min-h-[520px]">
        <div class="flex flex-col items-center text-center max-w-md px-6">
            <div class="relative mb-6">
                <div class="h-20 w-20 rounded-full bg-red-600 grid place-items-center">
                    <svg class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01"/>
                    </svg>
                </div>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Không thể tải thông tin</h3>
            <p id="errorMessage" class="text-sm text-gray-600 dark:text-gray-400 mb-6">-</p>
            <a href="{{ route('admin.users.list') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Quay lại danh sách</span>
            </a>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast" class="pointer-events-none fixed bottom-5 left-1/2 -translate-x-1/2 hidden">
    <div class="rounded bg-gray-900 text-white px-4 py-2 text-sm">Đã sao chép vào clipboard</div>
</div>

{{-- Scripts --}}
<script>
    const userId = {{ $userId }};

    document.addEventListener('DOMContentLoaded', async () => {
        await loadUserData();
    });

    async function loadUserData() {
        try {
            const data = await apiRequest(`/users/${userId}`);
            if (data?.success) {
                displayUserData(data.data);
            } else {
                showError(data?.message || 'Không thể tải thông tin người dùng');
            }
        } catch (error) {
            showError(error.message || 'Đã xảy ra lỗi khi tải thông tin');
        }
    }

    function toggleStates({ loading = false, detail = false, error = false }) {
        document.getElementById('loadingState').classList.toggle('hidden', !loading);
        document.getElementById('userDetailCard').classList.toggle('hidden', !detail);
        document.getElementById('errorState').classList.toggle('hidden', !error);
    }

    function displayUserData(user) {
        toggleStates({ loading: false, detail: true, error: false });
        document.getElementById('editButton').href = `/admin/users/${user.id}/edit`;
        setText('userId', user.id);
        setText('userEmail', user.email);
        setText('userEmailDetail', user.email);
        setText('userFullname', user.fullname || 'Chưa có tên');
        setText('userFullnameDetail', user.fullname || 'Chưa có tên');
        setText('userRoles', user.roles.map(r => r.name).join(', ') || 'Chưa có vai trò');

        const status = (user.status || '').toString().toLowerCase();
        const badge = document.getElementById('userStatusBadge');
        if (badge) {
            const map = {
                active: ['bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'Active', 'bg-emerald-500'],
                pending: ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'Pending', 'bg-amber-500'],
                banned: ['bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300', 'Banned', 'bg-rose-500'],
            };
            const [cls, label, dot] = map[status] || map['active'];
            badge.className = `inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold rounded-full ${cls}`;
            badge.innerHTML = `<span class="inline-block h-1.5 w-1.5 rounded-full ${dot}"></span><span>${label}</span>`;
        }

        // Set avatar preview
        const avatarEl = document.getElementById('userAvatar');
        const placeholderEl = document.getElementById('avatarPlaceholder');
        if (user.path_avatar) {
            avatarEl.src = user.path_avatar;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
        } else {
            const name = user.fullname || user.email || 'User';
            avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`;
            avatarEl.classList.remove('hidden');
            placeholderEl.classList.add('hidden');
        }

        setDate('userBirthday', user.birthday, { dateOnly: true });
        setDate('userCreatedAt', user.created_at);

        // const roles = Array.isArray(user.roles) ? user.roles : [];
        // const rolesList = document.getElementById('rolesList');
        // if (!roles.length) {
        //     rolesList.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 italic">Người dùng chưa có vai trò nào</p>`;
        // } else {
        //     rolesList.innerHTML = roles.map(r => `
        //         <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
        //             <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        //                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4zM5.5 21a6.5 6.5 0 0113 0"/>
        //             </svg>
        //             ${escapeHtml(r.name ?? 'Role')}
        //         </span>
        //     `).join('');
        // }

        const courses = Array.isArray(user.courses) ? user.courses : [];
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
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(c.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: ${escapeHtml(String(c.id ?? '-'))}</p>
                        </div>
                    </div>
                    ${c.description ? `
                        <div class="hidden md:block ml-4 max-w-xs">
                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate block">${escapeHtml(String(c.description)).slice(0, 80)}${String(c.description).length > 80 ? '…' : ''}</span>
                        </div>` : ''
                    }
                </div>
            `).join('');
        }
    }

    function showError(message) {
        toggleStates({ loading: false, detail: false, error: true });
        setText('errorMessage', message || 'Đã xảy ra lỗi');
    }

    function setText(id, value) {
        const el = document.getElementsByClassName(id);
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = value != null && value !== '' ? value : '-';
        }
    }

    function setDate(id, raw, opts = {}) {
        const el = document.getElementsByClassName(id);
        if (!el && el.length <1) return;
        if (!raw) { el.textContent = '-'; return; }
        const dt = new Date(raw);
        let data = '-'
        if (Number.isNaN(dt.getTime())) { data = '-'; return; }
        data = opts.dateOnly
            ? dt.toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' })
            : dt.toLocaleString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        for (let i=0; i<el.length; i++) {
            if (el[i]) el[i].textContent = data;
        }
    }

    function escapeHtml(str) {
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function showToast() {
        const t = document.getElementById('toast');
        if (!t) return;
        t.classList.remove('hidden');
        clearTimeout(t._hide);
        t._hide = setTimeout(() => t.classList.add('hidden'), 1600);
    }
</script>
@endsection
