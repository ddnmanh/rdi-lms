@extends('admin.layout')

@section('title', 'Chi tiết người dùng')
@section('description', 'Xem thông tin chi tiết của người dùng')

@section('content')
<div class="min-h-full flex flex-col gap-4 2xl:gap-6">
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
                    <a id="editButton" href="#"
                       class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3.5 py-2 text-sm font-semibold text-white hover:bg-amber-600 focus:outline-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.313 3 21l1.687-4.5L16.862 3.487z"/>
                        </svg>
                        <span>Chỉnh sửa</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading State (Flat Skeleton) --}}
    <div id="loadingState" class="flex-1 p-6 sm:p-8">
        <div class="mx-auto max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800">
                    <div class="mx-auto flex flex-col items-center gap-4">
                        <div class="h-28 w-28 rounded-full bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-4 w-40 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                        <div class="h-3 w-52 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="h-14 rounded-lg bg-gray-100 dark:bg-gray-900 animate-pulse"></div>
                        <div class="h-14 rounded-lg bg-gray-100 dark:bg-gray-900 animate-pulse"></div>
                    </div>
                </div>
                <div class="lg:col-span-2 grid gap-6">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800 h-40 animate-pulse"></div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-6 bg-white dark:bg-gray-800 h-40 animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Detail Card (Flat) --}}
    <div id="userDetailCard" class="hidden flex-1 w-full max-w-6xl mx-auto flex-col">
        {{-- Personal Information --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-blue-600 grid place-items-center">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.88 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Thông tin cá nhân</h3>
            </div>

            <div class="p-6 flex flex-col xl:flex-row items-start justify-start gap-5">
                {{-- Avatar / Summary --}}
                <div class="w-[300px] mx-auto lg:col-span-1">
                    <div class="relative mb-4">
                        <img id="userAvatar" src="" alt="Avatar"
                            class="w-full object-cover border-4 border-white/70"
                            onerror="this.src='https://ui-avatars.com/api/?name=' + encodeURIComponent(document.getElementById('userFullname').textContent || 'User') + '&background=3b82f6&color=fff&size=128'">
                    </div>
                </div>
                <div class="w-full flex-1 grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $infoField = function($label, $id, $hint = null) {
                            $hintHtml = $hint ? '<p class="text-[11px] leading-4 text-gray-500 dark:text-gray-400">'.$hint.'</p>' : '';
                            return <<<HTML
                            <div class="space-y-2">
                                <label class="block text-[11px] font-semibold text-gray-500 dark:text-gray-400 tracking-wider uppercase">{$label}</label>
                                <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                                    <p id="{$id}" class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">-</p>
                                    {$hintHtml}
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
            </div>
        </div>

        {{-- Courses --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800 mt-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
                <div class="h-8 w-8 rounded bg-green-600 grid place-items-center">
                    <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0112 21c-4.418 0-8.268-2.388-10.16-5.422L12 14z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Khóa học</h3>
            </div>
            <div class="p-6">
                <div id="coursesList" class="space-y-2">
                    <div class="w-full flex justify-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
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

        const avatarEl = document.getElementById('userAvatar');
        if (user.path_avatar) {
            avatarEl.src = user.path_avatar;
        } else {
            const name = user.fullname || user.email || 'User';
            avatarEl.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=3b82f6&color=fff&size=128`;
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
        const el = document.getElementById(id);
        if (el) el.textContent = value != null && value !== '' ? value : '-';
    }

    function setDate(id, raw, opts = {}) {
        const el = document.getElementById(id);
        if (!el) return;
        if (!raw) { el.textContent = '-'; return; }
        const dt = new Date(raw);
        if (Number.isNaN(dt.getTime())) { el.textContent = '-'; return; }
        el.textContent = opts.dateOnly
            ? dt.toLocaleDateString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric' })
            : dt.toLocaleString('vi-VN', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
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
