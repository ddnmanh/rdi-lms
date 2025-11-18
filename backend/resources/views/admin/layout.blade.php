<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') · LMS</title>

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/layout.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/loading.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/table.css') }}" />

    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    <script>
        // ===== Theme (Light/Dark) =====
        const THEME_KEY = 'lms-theme';
        function applyTheme(theme) {
            const html = document.documentElement;
            if (theme === 'dark') html.classList.add('dark');
            else html.classList.remove('dark');
            localStorage.setItem(THEME_KEY, theme);
        }

        function toggleTheme() {
            const current = localStorage.getItem(THEME_KEY) || 'light';
            applyTheme(current === 'light' ? 'dark' : 'light');
        }

        (function initTheme() {
            const saved = localStorage.getItem(THEME_KEY);
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            applyTheme(saved || (prefersDark ? 'dark' : 'light'));
        })();

        // ===== Sidebar (Collapse + Mobile) =====
        let sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        function initSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                toggleBtn && (toggleBtn.innerHTML = '<i class="fas fa-chevron-right text-xl"></i>');
                // Đóng tất cả submenu khi sidebar collapsed
                const details = sidebar.querySelectorAll('details');
                details.forEach(detail => detail.removeAttribute('open'));
            } else {
                sidebar.classList.remove('collapsed');
                toggleBtn && (toggleBtn.innerHTML = '<i class="fas fa-chevron-left text-xl"></i>');
            }
        }

        // Tự động chuyển trạng thái sidebar khi gọi hàm
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            sidebar.classList.toggle('collapsed');
            if (toggleBtn) {
                toggleBtn.innerHTML = sidebarCollapsed ?
                    '<i class="fas fa-chevron-right text-xl"></i>' :
                    '<i class="fas fa-chevron-left text-xl"></i>';
            }
            // Đóng tất cả submenu khi sidebar collapsed
            if (sidebarCollapsed) {
                const details = sidebar.querySelectorAll('details');
                details.forEach(detail => detail.removeAttribute('open'));
            }
        }

        // Hàm chủ động chuyển trạng thái sidebar
        function handleSidebarCollapse(status = 'EXPAND') { // 'COLLAPSE' | 'EXPAND'
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if ((status === 'COLLAPSE' && sidebarCollapsed) || (status === 'EXPAND' && !sidebarCollapsed)) {
                return; // Không thay đổi gì
            }

            if (status === 'COLLAPSE') {
                sidebarCollapsed = true;
            } else if (status === 'EXPAND') {
                sidebarCollapsed = false;
            }

            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            sidebar.classList.toggle('collapsed');
            if (toggleBtn) {
                toggleBtn.innerHTML = sidebarCollapsed ?
                    '<i class="fas fa-chevron-right text-xl"></i>' :
                    '<i class="fas fa-chevron-left text-xl"></i>';
            }
            // Đóng tất cả submenu khi sidebar collapsed
            if (sidebarCollapsed) {
                const details = sidebar.querySelectorAll('details');
                details.forEach(detail => detail.removeAttribute('open'));
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initSidebar();
            // Quick keyboard: Ctrl+B toggle collapse
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'b') {
                    e.preventDefault();
                    toggleSidebarCollapse();
                }
            });
            // Ngăn submenu mở khi sidebar collapsed
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.addEventListener('click', (e) => {
                    if (sidebar.classList.contains('collapsed')) {
                        const details = e.target.closest('details');
                        if (details && e.target.closest('summary')) {
                            e.preventDefault();
                            details.removeAttribute('open');
                        }
                    }
                });
            }

            // Lắng nghe sự kiện resize để tự động collapse/expand sidebar
            window.addEventListener('resize', () => {
                const width = window.innerWidth;
                if (width < 1580) {
                    handleSidebarCollapse('COLLAPSE');
                } else {
                    handleSidebarCollapse('EXPAND');
                }
            });
        });


        

    </script>

    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-row items-stretch">

    @include('admin.util')

    <!-- Sidebar -->
    <aside id="sidebar" class="shrink-0 w-[180px] 2xl:w-[230px] h-screen bg-white dark:bg-gray-900 backdrop-blur-xl border-r border-gray-200 dark:border-gray-700/60 overflow-hidden transition-all duration-300 z-40 flex flex-col">
        <!-- Sidebar Header -->
        <div class="h-16 sidebar-item flex items-center px-4 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-3 hover:opacity-90 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                    <i class="fas fa-graduation-cap text-white text-lg"></i>
                </div>
                <span
                    class="sidebar-text font-bold text-lg bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">
                    LMS Admin
                </span>
            </a>
        </div>

        <nav class="mt-6 px-3 space-y-1.5 flex-1 overflow-y-auto">
            {{-- Group: Tổng quan --}}
            <div
                class="sidebar-group-label px-3 pb-2 pt-3 text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">
                Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-tachometer-alt sidebar-icon {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Dashboard</span>
                {{-- <span class="sidebar-tooltip z-50">Dashboard</span> --}}
            </a>

            {{-- Group: Quản lý --}}
            <div
                class="sidebar-group-label mt-4 px-3 pb-2 pt-3 text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">
                Quản lý</div>
            <a href="{{ route('admin.users.list') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-users sidebar-icon {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Người dùng</span>
                {{-- <span class="sidebar-tooltip z-50">Người dùng</span> --}}
            </a>
            <a href="{{ route('admin.roles.list') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.roles.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-user-shield sidebar-icon {{ request()->routeIs('admin.roles.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Vai trò</span>
                {{-- <span class="sidebar-tooltip z-50">Vai trò</span> --}}
            </a>
            <a href="{{ route('admin.courses.list') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.courses.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.courses.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-book sidebar-icon {{ request()->routeIs('admin.courses.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Khóa học</span>
                {{-- <span class="sidebar-tooltip z-50">Khóa học</span> --}}
            </a>
            <a href="{{ route('admin.lessons.list') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.lessons.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.lessons.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-play-circle sidebar-icon {{ request()->routeIs('admin.lessons.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Bài học</span>
                {{-- <span class="sidebar-tooltip z-50">Bài học</span> --}}
            </a>

            @php $reportsOpen = request()->routeIs('admin.reports.*'); @endphp
            <details class="group" {{ $reportsOpen ? 'open' : '' }}>
                <summary
                    class="sidebar-item list-none group flex items-center gap-3 px-2 py-2 rounded-xl cursor-pointer transition-all duration-300 {{ $reportsOpen ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                    <div
                        class="h-7 w-7 rounded-lg {{ $reportsOpen ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                        <i class="fas fa-chart-line sidebar-icon {{ $reportsOpen ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                    </div>
                    <span class="sidebar-text">Báo cáo</span>
                    <i class="fa-solid fa-chevron-down ml-auto text-xs {{ $reportsOpen ? 'text-white' : 'text-gray-500 dark:text-gray-400' }} group-open:rotate-180 transition-transform"></i>
                </summary>
                <div class="mt-2 p-2 rounded-xl space-y-2">
                    <a href="{{ route('admin.reports.index') }}"
                        class="group/item pl-8 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.index') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <i class="fas fa-gauge text-[12px] {{ request()->routeIs('admin.reports.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span class="font-medium">Tổng quan</span>
                    </a>
                    <a href="{{ route('admin.reports.students') }}"
                        class="group/item pl-8 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.students') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <i class="fas fa-user-graduate text-[12px] {{ request()->routeIs('admin.reports.students') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span class="font-medium">Học viên</span>
                    </a>
                    <a href="{{ route('admin.reports.courses') }}"
                        class="group/item pl-8 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.courses') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <i class="fas fa-book-open text-[12px] {{ request()->routeIs('admin.reports.courses') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span class="font-medium">Khóa học</span>
                    </a>
                    <a href="{{ route('admin.reports.activities') }}"
                        class="group/item pl-8 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.activities') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <i class="fas fa-bolt text-[12px] {{ request()->routeIs('admin.reports.activities') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400' }}"></i>
                        <span class="font-medium">Hoạt động</span>
                    </a>
                </div>
            </details>

        </nav>

        <!-- Sidebar Footer -->
        <div
            class="mt-auto border-t border-gray-200/60 dark:border-gray-700/60 flex-shrink-0 bg-gradient-to-t from-gray-50/50 to-transparent dark:from-gray-900/50">
            <!-- Toggle Buttons -->
            <div class="p-3 flex items-center justify-center gap-2"> 
                <button id="sidebarToggle" onclick="toggleSidebarCollapse()"
                    class="flex items-center justify-center w-11 h-11 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110">
                    <i class="fas fa-chevron-left text-lg"></i>
                </button>
            </div>
        </div>
    </aside>

    <div class="w-full h-full relative">
        <!-- Top Nav -->
        <nav class="absolute top-0 left-0 right-0 py-3 bg-white dark:bg-gray-900 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700/60 transition-all duration-300 z-[110]">
            <div class="h-full w-full flex items-center justify-between px-4 sm:px-6">
                <!-- Title Section -->
                <div class="flex flex-col gap-0 min-w-0 flex-1">
                    <h2
                        class="text-lg sm:text-xl font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                        @yield('title', 'Admin Panel')
                    </h2>
                    @hasSection('description')
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 truncate">
                            @yield('description')
                        </p>
                    @endif
                </div>
                <!-- Actions Section -->
                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    {{-- <button onclick="toggleTheme()"
                        class="group relative p-2.5 rounded-xl hover:bg-gradient-to-br hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110"
                        title="Chuyển đổi theme">
                        <i class="fas fa-moon dark:hidden text-lg text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                        <i class="fas fa-sun hidden dark:inline text-lg text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                    </button>
                    <button
                        class="group relative p-2.5 rounded-xl hover:bg-gradient-to-br hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110"
                        title="Thông báo">
                        <i class="fas fa-bell text-lg text-gray-700 dark:text-gray-300 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></i>
                        <span class="absolute -top-1 -right-1 inline-flex h-5 min-w-[20px] px-1.5 items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-pink-500 text-[10px] font-bold text-white shadow-lg shadow-red-500/40 animate-pulse">3</span>
                    </button> --}}
                    <div class="relative">
                        <details class="group">
                            <summary class="list-none flex items-center gap-2.5 cursor-pointer select-none rounded-xl p-1.5 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-105">
                                <div class="group-hover:scale-110 transition-transform duration-300">
                                    <img src="{{ optional(request()->user())->path_avatar }}" alt="Avatar" class="w-9 aspect-square rounded-full object-cover">
                                </div>
                                <i class="fas fa-caret-down text-xs text-gray-500 dark:text-gray-400 group-open:rotate-180 transition-transform duration-300"></i>
                            </summary>

                            <div
                                class="z-[1000] absolute right-0 mt-2 w-64 rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl shadow-gray-900/10 dark:shadow-gray-900/30 border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
                                <div
                                    class="px-4 py-3.5 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/20 dark:to-purple-900/20 border-b border-gray-200/50 dark:border-gray-700/50">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Đăng nhập với</p>
                                    <p class="text-xs font-semibold text-gray-900 dark:text-gray-100 mt-0.5 truncate">{{ optional(request()->user())->email ?? 'user@example.com' }}</p>
                                </div>
                                <div class="py-1.5">
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="group/item flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-200">
                                        <i class="fas fa-tachometer-alt text-indigo-600 dark:text-indigo-400"></i>
                                        <span class="font-medium">Trang chủ</span>
                                        <i
                                            class="fas fa-chevron-right ml-auto text-xs opacity-0 group-hover/item:opacity-100 group-hover/item:translate-x-1 transition-all"></i>
                                    </a>
                                    <button type="button" onclick="openVerifyLogoutModal(event)"
                                        class="w-full text-left flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-gradient-to-r hover:from-red-50 hover:to-pink-50 dark:hover:from-red-900/20 dark:hover:to-pink-900/20 transition-all duration-200">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </div>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main -->
        <main class="z-[100] h-[calc(100dvh-55px)] 2xl:h-[calc(100dvh-65px)] mt-[55px] 2xl:mt-[64px] p-2 md:p-6 transition-all duration-300 bg-gray-100 dark:bg-gray-900 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script>
        // API Base URL
        const API_BASE_URL = '/api';

        // Get cookie value by name
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
            return null;
        }

        // API Helper - sử dụng cookie thay vì localStorage
        async function apiRequest(url, options = {}) {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            };

            try {
                const response = await fetch(`${API_BASE_URL}${url}`, {
                    ...options,
                    headers,
                    credentials: 'include' // Quan trọng: gửi cookie
                });

                // Nếu token hết hạn (401), thử refresh
                if (response.status === 401) {
                    const refreshToken = getCookie('refresh_token');
                    if (refreshToken) {
                        try {
                            const refreshResponse = await fetch(`${API_BASE_URL}/auth/refresh`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                credentials: 'include'
                            });

                            if (refreshResponse.ok) {
                                // Token đã được refresh, thử lại request ban đầu
                                return await apiRequest(url, options);
                            } else {
                                // Refresh token cũng hết hạn, redirect về login
                                window.location.href = '/admin/login';
                                throw new Error('Phiên đăng nhập đã hết hạn');
                            }
                        } catch (refreshError) {
                            window.location.href = '/admin/login';
                            throw new Error('Phiên đăng nhập đã hết hạn');
                        }
                    } else {
                        window.location.href = '/admin/login';
                        throw new Error('Chưa đăng nhập');
                    }
                }

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Có lỗi xảy ra');
                }
                return data;
            } catch (error) {
                console.error('API Error:', error);
                throw error;
            }
        }

        async function openVerifyLogoutModal(event) {
            event.preventDefault();
            openLogoutModalGeneric_Global({ 
                title: 'Xác nhận đăng xuất',
                message: 'Bạn có chắc chắn muốn đăng xuất khỏi hệ thống không?',
                deleteFuncCallback: () => handleLogout(),
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

    @stack('scripts')
</body>
</html>
