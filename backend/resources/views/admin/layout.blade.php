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
    <link rel="stylesheet" href="{{ mix('css/loading-in-btn.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/table.css') }}" />

    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    <script>
        tailwind.config = {
            theme: {
                screens: {
                    sm: "640px",
                    md: "768px",
                    lg: "1024px",
                    xl: "1280px",
                    "3xl": "1900px",
                }
            }
        }
    </script>


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

        // ===== User Menu Dropdown =====
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-menu-dropdown');
            const arrow = document.getElementById('user-menu-arrow');

            if (dropdown.classList.contains('hidden')) {
                // Open
                dropdown.classList.remove('hidden');
                // Small delay to allow transition to work
                setTimeout(() => {
                    dropdown.classList.remove('opacity-0', 'scale-95');
                    dropdown.classList.add('opacity-100', 'scale-100');
                }, 10);
                arrow.classList.add('rotate-180');
            } else {
                // Close
                dropdown.classList.remove('opacity-100', 'scale-100');
                dropdown.classList.add('opacity-0', 'scale-95');
                arrow.classList.remove('rotate-180');
                setTimeout(() => {
                    dropdown.classList.add('hidden');
                }, 200); // Match duration-200
            }
        }

        // Close when clicking outside
        document.addEventListener('click', function(event) {
            const container = document.getElementById('user-menu-container');
            const dropdown = document.getElementById('user-menu-dropdown');
            const arrow = document.getElementById('user-menu-arrow');

            if (container && !container.contains(event.target)) {
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.remove('opacity-100', 'scale-100');
                    dropdown.classList.add('opacity-0', 'scale-95');
                    if (arrow) arrow.classList.remove('rotate-180');
                    setTimeout(() => {
                        dropdown.classList.add('hidden');
                    }, 200);
                }
            }
        });

    </script>

    @stack('styles')
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-row items-stretch">

    @include('admin.util')

    <!-- Sidebar -->
    <aside id="sidebar" class="shrink-0 w-[180px] 3xl:w-[250px] h-screen bg-white dark:bg-gray-900 backdrop-blur-xl border-r border-gray-200 dark:border-gray-700/60 overflow-hidden transition-all duration-300 z-40 flex flex-col">
        <!-- Sidebar Header -->
        <div class="h-[62.5px] 3xl:h-[71px] sidebar-item flex flex-col items-center !justify-between flex-shrink-0">
            <div class="w-full"></div>
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
            <div class="w-full px-3">
                <hr class="w-full border-t border-gray-200 dark:border-gray-700/60" />
            </div>
        </div>

        <nav class="mt-6 px-3 space-y-1.5 flex-1 overflow-y-auto">
            {{-- Group: Tổng quan --}}
            <div class="px-3 pb-2 pt-3">
                <div class="sidebar-group-label text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">
                    Tổng quan
                </div>
            </div>
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
            <div class="px-3 pb-2 pt-3">
                <div class="sidebar-group-label text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">
                    Quản lý
                </div>
                <div class="sidebar-group-separator w-full">
                    <hr class="border-gray-200 dark:border-gray-700/60" />
                </div>
            </div>
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
                <span class="sidebar-text">Phân quyền</span>
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

            {{-- Group: Cá nhân --}}
            <div class="px-3 pb-2 pt-3">
                <div class="sidebar-group-label text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">
                    Cá nhân
                </div>
                <div class="sidebar-group-separator w-full">
                    <hr class="border-gray-200 dark:border-gray-700/60" />
                </div>
            </div>
            <a href="{{ route('admin.profile.show') }}" class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.profile.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.profile.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i
                        class="fas fa-address-card sidebar-icon {{ request()->routeIs('admin.profile.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Hồ sơ</span>
                {{-- <span class="sidebar-tooltip z-50">Bài học</span> --}}
            </a>

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
                        class="font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                        @yield('title', 'Admin Panel')
                    </h2>
                    @hasSection('description')
                        <p class="text-xs 3xl:text-sm text-gray-600 dark:text-gray-400 truncate">
                            @yield('description')
                        </p>
                    @endif
                </div>
                <!-- Actions Section -->
                <div class="flex items-center gap-2 md:gap-3 flex-shrink-0">
                    <div class="relative" id="user-menu-container">
                        <button type="button" onclick="toggleUserMenu()"
                            class="flex items-center gap-2.5 cursor-pointer select-none rounded-full p-1 pl-1.5 pr-2.5 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-300 border border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                            <div class="relative">
                                <img src="{{ optional(request()->user())->avatar_path }}"
                                    alt="Avatar"
                                    class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                <div
                                    class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full">
                                </div>
                            </div>
                            <div class="hidden md:flex flex-col items-start text-left">
                                <span
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-none">{{ optional(request()->user())->fullname ?? 'User' }}</span>
                                <span
                                    class="text-[10px] font-medium text-gray-500 dark:text-gray-400 leading-none mt-1">{{ optional(request()->user())->email ?? 'Member' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-[10px] text-gray-400 dark:text-gray-500 ml-1 transition-transform duration-300"
                                id="user-menu-arrow"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="user-menu-dropdown"
                            class="hidden absolute right-0 mt-2 w-72 origin-top-right rounded-2xl bg-white dark:bg-gray-800 shadow-2xl shadow-gray-900/10 dark:shadow-gray-900/30 ring-1 ring-black ring-opacity-5 focus:outline-none transform transition-all duration-200 opacity-0 scale-95 z-50">

                            <!-- Header -->
                            <div
                                class="px-5 py-4 border-b border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Truy cập nhanh</p>
                                {{-- <p class="text-sm font-bold text-gray-900 dark:text-white mt-1 truncate">
                                    {{ optional(request()->user())->email }}</p> --}}
                            </div>

                            <!-- Menu Items -->
                            <div class="p-2 space-y-1">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 flex items-center justify-center transition-colors">
                                        <i
                                            class="fas fa-tachometer-alt text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400"></i>
                                    </div>
                                    Trang chủ
                                </a>

                                <a href="{{ route('admin.profile.show') }}"
                                    class="group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all duration-200">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/40 flex items-center justify-center transition-colors">
                                        <i
                                            class="fas fa-user-cog text-gray-500 dark:text-gray-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400"></i>
                                    </div>
                                    Hồ sơ cá nhân
                                </a>
                            </div>

                            <div class="h-px bg-gray-100 dark:bg-gray-700/50 mx-2"></div>

                            <div class="p-2">
                                <button type="button" onclick="openVerifyLogoutModal(event)"
                                    class="w-full group flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 group-hover:bg-red-100 dark:group-hover:bg-red-900/40 flex items-center justify-center transition-colors">
                                        <i class="fas fa-sign-out-alt text-red-500 dark:text-red-400"></i>
                                    </div>
                                    Đăng xuất
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main -->
        <main class="z-[100] h-[calc(100dvh-62.5px)] 3xl:h-[calc(100dvh-71px)] mt-[62.5px] 3xl:mt-[71px] p-2 md:p-6 transition-all duration-300 bg-gray-100 dark:bg-gray-900 overflow-y-auto">
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

    @stack('scripts')
</body>
</html>
