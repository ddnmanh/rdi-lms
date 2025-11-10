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
            const main = document.querySelector('main');
            const nav = document.querySelector('nav');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
                if (nav && window.innerWidth >= 1024) {
                    nav.style.left = '80px';
                }
                toggleBtn && (toggleBtn.innerHTML = '<i class="fas fa-chevron-right text-xl"></i>');
            } else {
                sidebar.classList.remove('collapsed');
                main.classList.remove('sidebar-collapsed');
                if (nav && window.innerWidth >= 1024) {
                    nav.style.left = '16rem';
                }
                toggleBtn && (toggleBtn.innerHTML = '<i class="fas fa-chevron-left text-xl"></i>');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            const main = document.querySelector('main');
            const nav = document.querySelector('nav');
            const toggleBtn = document.getElementById('sidebarToggle');
            sidebarCollapsed = !sidebarCollapsed;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
            sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed');
            if (nav && window.innerWidth >= 1024) {
                nav.style.left = sidebarCollapsed ? '80px' : '16rem';
            }
            if (toggleBtn) {
                toggleBtn.innerHTML = sidebarCollapsed ?
                    '<i class="fas fa-chevron-right text-xl"></i>' :
                    '<i class="fas fa-chevron-left text-xl"></i>';
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
        });
    </script>

    <style>
        /* ===== Font Family ===== */
        html,
        body {
            font-family: 'UTM Avo', sans-serif;
        }

        /* ===== Sidebar micro-interactions ===== */
        #sidebar {
            transition: width .3s cubic-bezier(.4, 0, .2, 1);
        }

        #sidebar.collapsed {
            width: 80px;
        }

        #sidebar.collapsed .sidebar-text {
            display: none;
        }

        #sidebar.collapsed .sidebar-group-label {
            display: none;
        }

        #sidebar.collapsed .sidebar-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar-item {
            position: relative;
            transition: transform .3s cubic-bezier(.4, 0, .2, 1), background-color .3s ease, box-shadow .3s ease;
        }

        .sidebar-item:not(.active):hover {
            transform: translateX(4px);
        }

        #sidebar.collapsed .sidebar-item:not(.active):hover {
            transform: scale(1.08);
        }

        .sidebar-item.active {
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .sidebar-text {
            transition: opacity .3s ease;
            white-space: nowrap;
        }

        .sidebar-tooltip {
            display: none;
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            color: #111827;
            padding: 10px 14px;
            border-radius: 12px;
            white-space: nowrap;
            z-index: 1000;
            font-size: 12px;
            font-weight: 600;
            pointer-events: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dark .sidebar-tooltip {
            background: rgba(31, 41, 55, 0.95);
            color: #f3f4f6;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-tooltip::before {
            content: '';
            position: absolute;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 8px solid transparent;
            border-right-color: rgba(255, 255, 255, 0.95);
        }

        .dark .sidebar-tooltip::before {
            border-right-color: rgba(31, 41, 55, 0.95);
        }

        #sidebar.collapsed .sidebar-item:hover .sidebar-tooltip {
            display: block;
            animation: fadeInTooltip .18s ease;
        }

        @keyframes fadeInTooltip {
            from {
                opacity: 0;
                transform: translateY(-50%) translateX(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }

        main.sidebar-collapsed {
            margin-left: 80px;
        }

        /* Top Nav adjustment when sidebar collapsed */
        body:has(#sidebar.collapsed) nav {
            left: 80px;
        }

        @media (max-width: 1023px) {
            main.sidebar-collapsed {
                margin-left: 0;
            }

            #sidebar.collapsed {
                width: 256px;
            }
            nav {
                left: 0 !important;
                right: 0;
            }
        }

        /* Modern scrollbars */
        *::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        *::-webkit-scrollbar-track {
            background: transparent;
        }

        *::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.4), rgba(139, 92, 246, 0.4));
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }



        *::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.6), rgba(139, 92, 246, 0.6));
            background-clip: padding-box;
        }

        .dark *::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.5), rgba(139, 92, 246, 0.5));
            background-clip: padding-box;
        }

        .dark *::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, rgba(99, 102, 241, 0.7), rgba(139, 92, 246, 0.7));
            background-clip: padding-box;
        }


        html,
        body,
        main {
            overflow-x: hidden;
            max-width: 100%;
            font-size: 12px;
            @media (min-width: 1536px) {
                font-size: 14px;
            }
            @media (min-width: 1920px) {
                font-size: 16px;
            }
            @media (min-width: 2560px) {
                font-size: 18px;
            }
            @media (min-width: 3840px) {
                font-size: 20px;
            }
            @media (min-width: 4096px) {
                font-size: 22px;
            }
            @media (min-width: 4352px) {
                font-size: 24px;
            }
            @media (min-width: 4608px) {
                font-size: 26px;
            }
        }
    </style>

    {{-- Spinner Loading --}}
    <style>
        #SPINNER_LOADING {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /*******************\
            Loading Roller
        \*******************/

        @keyframes SPINNER_LOADING_LDS_ROLLER_keyframes {
            0% {
                transform: rotate(90deg);
            }

            100% {
                transform: rotate(450deg);
            }
        }

        #SPINNER_LOADING_LDS_ROLLER {
            position: relative;
            display: inline-block;
            height: 64px;
            width: 64px;

            div {
                animation: SPINNER_LOADING_LDS_ROLLER_keyframes 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
                transform-origin: 32px 32px;

                &:after {
                    position: absolute;
                    display: block;
                    background: #9ca3af80;
                    border-radius: 50%;
                    content: " ";
                    margin: -3px 0 0 -3px;
                    height: 6px;
                    width: 6px;
                }

                &:nth-child(1) {
                    animation-delay: -0.036s;

                    &:after {
                        top: 50px;
                        left: 50px;
                    }
                }

                &:nth-child(2) {
                    animation-delay: -0.072s;

                    &:after {
                        top: 54px;
                        left: 45px;
                    }
                }

                &:nth-child(3) {
                    animation-delay: -0.108s;

                    &:after {
                        top: 57px;
                        left: 39px;
                    }
                }

                &:nth-child(4) {
                    animation-delay: -0.144s;

                    &:after {
                        top: 58px;
                        left: 32px;
                    }
                }

                &:nth-child(5) {
                    animation-delay: -0.18s;

                    &:after {
                        top: 57px;
                        left: 25px;
                    }
                }

                &:nth-child(6) {
                    animation-delay: -0.216s;

                    &:after {
                        top: 54px;
                        left: 19px;
                    }
                }

                &:nth-child(7) {
                    animation-delay: -0.252s;

                    &:after {
                        top: 50px;
                        left: 14px;
                    }
                }

                &:nth-child(8) {
                    animation-delay: -0.288s;

                    &:after {
                        top: 45px;
                        left: 10px;
                    }
                }
            }
        }
    </style>

    @stack('styles')

</head>



<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Top Nav -->
    <nav class="fixed top-0 left-64 right-0 z-50 min-h-16 py-3 bg-white dark:bg-gray-900 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700/60 transition-all duration-300">
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
                <button onclick="toggleTheme()"
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
                </button>
                <div class="relative">
                    <details class="group">
                        <summary
                            class="list-none flex items-center gap-2.5 cursor-pointer select-none rounded-xl p-1.5 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-105">
                            <div
                                class="h-9 w-9 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 grid place-items-center text-white text-sm font-bold shadow-lg shadow-indigo-500/30 group-hover:scale-110 transition-transform duration-300">
                                {{ strtoupper(substr(optional(request()->user())->name ?? 'A', 0, 1)) }}</div>
                            <span class="hidden sm:block text-sm font-semibold text-gray-700 dark:text-gray-200">{{ optional(request()->user())->name ?? 'Admin' }}</span>
                            <i class="fas fa-caret-down text-xs text-gray-500 dark:text-gray-400 group-open:rotate-180 transition-transform duration-300"></i>
                        </summary>

                        <div
                            class="absolute right-0 mt-2 w-64 rounded-2xl bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl shadow-gray-900/10 dark:shadow-gray-900/30 border border-gray-200/50 dark:border-gray-700/50 overflow-hidden">
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
                                <button type="button" onclick="handleLogout(event)"
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

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 w-64 h-screen bg-white dark:bg-gray-900 backdrop-blur-xl border-r border-gray-200 dark:border-gray-700/60 overflow-hidden transition-all duration-300 z-40 lg:translate-x-0 -translate-x-full flex flex-col">
        <!-- Sidebar Header -->
        <div class="h-16 sidebar-item flex items-center px-4 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-3 hover:opacity-90 transition-all duration-300">
                <div
                    class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
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

            <a href="{{ route('admin.reports') }}"
                class="sidebar-item group flex items-center gap-3 px-2 py-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.reports.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-chart-line sidebar-icon {{ request()->routeIs('admin.reports.*') ? 'text-white' : 'text-gray-600 dark:text-gray-400' }} text-sm"></i>
                </div>
                <span class="sidebar-text">Báo cáo</span>
                {{-- <span class="sidebar-tooltip z-50">Báo cáo</span> --}}
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div
            class="mt-auto border-t border-gray-200/60 dark:border-gray-700/60 flex-shrink-0 bg-gradient-to-t from-gray-50/50 to-transparent dark:from-gray-900/50">
            <!-- Toggle Buttons -->
            <div class="p-3 flex items-center justify-center gap-2">
                <button onclick="toggleSidebar()"
                    class="lg:hidden flex items-center justify-center w-11 h-11 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <button id="sidebarToggle" onclick="toggleSidebarCollapse()"
                    class="hidden lg:flex items-center justify-center w-11 h-11 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110">
                    <i class="fas fa-chevron-left text-lg"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" onclick="toggleSidebar()"
        class="fixed top-0 left-0 right-0 bottom-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden transition-opacity duration-300">
    </div>

    <!-- Main -->
    <main class="h-[calc(100dvh-55px)] mt-[55px] p-2 md:p-6 transition-all duration-300 lg:ml-64 bg-gray-100 dark:bg-gray-900 overflow-y-auto">
        @yield('content')
    </main>

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

        // Logout function
        async function handleLogout(event) {
            event.preventDefault();
            if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE_URL}/auth/logout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include'
                });

                // Dù có lỗi hay không, vẫn redirect về login
                window.location.href = '/admin/login';
            } catch (error) {
                console.error('Logout error:', error);
                // Vẫn redirect về login dù có lỗi
                window.location.href = '/admin/login';
            }
        }

        // Show alert
        function showAlert(message, type = 'success') {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.textContent = message;
            document.body.insertBefore(alert, document.body.firstChild);
            setTimeout(() => {
                alert.remove();
            }, 3000);
        }

        // ===== Timezone Utilities =====
        // Tự động nhận biết timezone của client
        function getClientTimezone() {
            try {
                return Intl.DateTimeFormat().resolvedOptions().timeZone;
            } catch (e) {
                // Fallback: tính toán offset
                const offset = -new Date().getTimezoneOffset();
                const hours = Math.floor(Math.abs(offset) / 60);
                const minutes = Math.abs(offset) % 60;
                const sign = offset >= 0 ? '+' : '-';
                return `UTC${sign}${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
            }
        }

        // Chuyển đổi UTC datetime từ Laravel sang local time format cho datetime-local input
        // Tự động sử dụng timezone của client
        function formatDateTimeLocal(utcDateTimeString) {
            if (!utcDateTimeString) return '';

            // Parse UTC datetime từ Laravel (ISO 8601 với Z hoặc +00:00)
            const date = new Date(utcDateTimeString);
            if (isNaN(date.getTime())) return '';

            // JavaScript Date tự động chuyển UTC sang local time của client
            // Format: YYYY-MM-DDTHH:mm (local time, không có timezone)
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }

        // [DEPRECATED] Chuyển đổi local time từ datetime-local input sang UTC format
        // Lưu ý: Function này đã không còn được sử dụng.
        // Thay vào đó, client nên gửi local time + timezone, server sẽ chuyển đổi sang UTC.
        // Giữ lại function này để tương thích ngược nếu có code cũ đang sử dụng.
        // function convertLocalToUTC(localDateTimeString) {
        //     if (!localDateTimeString) return null;

        //     // datetime-local input trả về "YYYY-MM-DDTHH:mm" (local time, không có timezone)
        //     // Tạo Date object - JavaScript sẽ coi như local time của client
        //     const localDate = new Date(localDateTimeString);
        //     if (isNaN(localDate.getTime())) return null;

        //     // Chuyển sang UTC format (ISO 8601 với Z) để Laravel xử lý đúng
        //     return localDate.toISOString();
        // }

        // Format date - hiển thị theo timezone của client
        // Laravel lưu và trả về datetime ở UTC, JavaScript tự động chuyển sang local time
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';

            // Laravel trả về datetime ở UTC (có timezone Z hoặc +00:00)
            // JavaScript Date tự động parse và chuyển đổi sang local time của client
            // Sử dụng local time methods để hiển thị đúng theo timezone của client
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    </script>
    @stack('scripts')
</body>
</html>
