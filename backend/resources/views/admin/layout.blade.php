<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') · LMS</title>

    <link rel="stylesheet" href="{{ mix('css/app.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/layout.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/loading.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/loading-in-btn.css') }}" />
    <link rel="stylesheet" href="{{ mix('css/table.css') }}" />
    <script src="{{ mix('js/app.js') }}"></script>

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
                toggleBtn && (toggleBtn.innerHTML = `
                    <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                        <path d="M311.1 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L243.2 256 73.9 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                    </svg>
                `);
                // Đóng tất cả submenu khi sidebar collapsed
                const details = sidebar.querySelectorAll('details');
                details.forEach(detail => detail.removeAttribute('open'));
            } else {
                sidebar.classList.remove('collapsed');
                toggleBtn && (toggleBtn.innerHTML = `
                    <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                        <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                    </svg>
                `);
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
                    `
                        <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                            <path d="M311.1 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L243.2 256 73.9 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                        </svg>
                    ` :
                    `
                        <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                        </svg>
                    `;
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
                    `
                        <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                            <path d="M311.1 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L243.2 256 73.9 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>
                        </svg>
                    ` :
                    `
                        <svg class="w-4 h-4" viewBox="0 0 320 512" fill="currentColor">
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                        </svg>
                    `;
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

</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 flex flex-row items-stretch">

    <!-- Sidebar -->
    <aside id="sidebar" class="shrink-0 w-[180px] 3xl:w-[250px] h-screen bg-white dark:bg-gray-900 backdrop-blur-xl border-r border-gray-200 dark:border-gray-700/60 overflow-hidden transition-all duration-300 z-40 flex flex-col">
        <!-- Sidebar Header -->
        <div class="h-[62.5px] 3xl:h-[71px] sidebar-item flex flex-col items-center !justify-between flex-shrink-0">
            <div class="w-full"></div>
            <a href="{{ route('admin.dashboard') }}"
                class="group flex items-center gap-3 hover:opacity-90 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 text-white">
                    <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                    </svg>
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
                class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 512 512"  fill="currentColor">
                        <path d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM288 96a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM256 416c35.3 0 64-28.7 64-64 0-16.2-6-31.1-16-42.3l69.5-138.9c5.9-11.9 1.1-26.3-10.7-32.2s-26.3-1.1-32.2 10.7L261.1 288.2c-1.7-.1-3.4-.2-5.1-.2-35.3 0-64 28.7-64 64s28.7 64 64 64zM176 144a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM96 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm352-32a32 32 0 1 0 -64 0 32 32 0 1 0 64 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Dashboard</span>
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
                class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.users.*') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Người dùng</span>
            </a>
            <a href="{{ route('admin.roles.list') }}"
                class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.roles.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.roles.*') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M224 248a120 120 0 1 0 0-240 120 120 0 1 0 0 240zm-29.7 56C95.8 304 16 383.8 16 482.3 16 498.7 29.3 512 45.7 512l251.5 0C261 469.4 240 414.5 240 356.4l0-31.1c0-7.3 1-14.5 2.9-21.3l-48.6 0zm251 184.5l-13.3 6.3 0-188.1 96 32 0 19.6c0 55.8-32.2 106.5-82.7 130.3zM421.9 259.5l-112 37.3c-13.1 4.4-21.9 16.6-21.9 30.4l0 31.1c0 74.4 43 142.1 110.2 173.7l18.5 8.7c4.8 2.2 10 3.4 15.2 3.4s10.5-1.2 15.2-3.4l18.5-8.7C533 500.3 576 432.6 576 358.2l0-31.1c0-13.8-8.8-26-21.9-30.4l-112-37.3c-6.6-2.2-13.7-2.2-20.2 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Phân quyền</span>
            </a>
            <a href="{{ route('admin.courses.list') }}"
                class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.courses.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.courses.*') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Khóa học</span>
            </a>
            <a href="{{ route('admin.lessons.list') }}"
                class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.lessons.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div
                    class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.lessons.*') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM188.3 147.1c-7.6 4.2-12.3 12.3-12.3 20.9l0 176c0 8.7 4.7 16.7 12.3 20.9s16.8 4.1 24.3-.5l144-88c7.1-4.4 11.5-12.1 11.5-20.5s-4.4-16.1-11.5-20.5l-144-88c-7.4-4.5-16.7-4.7-24.3-.5z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Bài học</span>
            </a>

            @php $reportsOpen = request()->routeIs('admin.reports.*'); @endphp
            <details class="group" {{ $reportsOpen ? 'open' : '' }}>
                <summary
                    class="sidebar-item list-none group flex items-center gap-3 px-2 py-2 rounded-xl cursor-pointer transition-all duration-300 {{ $reportsOpen ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                    <div class="h-7 w-7 rounded-lg {{ $reportsOpen ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                        <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M64 64c0-17.7-14.3-32-32-32S0 46.3 0 64L0 400c0 44.2 35.8 80 80 80l400 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L80 416c-8.8 0-16-7.2-16-16L64 64zm406.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L320 210.7 262.6 153.4c-12.5-12.5-32.8-12.5-45.3 0l-96 96c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l73.4-73.4 57.4 57.4c12.5 12.5 32.8 12.5 45.3 0l128-128z"/>
                        </svg>
                    </div>
                    <span class="sidebar-text">Báo cáo</span>
                    <label class="ml-auto text-xs {{ $reportsOpen ? 'text-white' : 'text-gray-500 dark:text-gray-400' }} group-open:rotate-180 transition-transform">
                        <svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M201.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 338.7 54.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/>
                        </svg>
                    </label>
                </summary>
                <div class="mt-2 p-2 rounded-xl space-y-2">
                    <a href="{{ route('admin.reports.index') }}" class="group/item pl-10 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.index') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <span class="font-medium">Tổng quan</span>
                    </a>
                    <a href="{{ route('admin.reports.students') }}" class="group/item pl-10 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.students') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <span class="font-medium">Học viên</span>
                    </a>
                    <a href="{{ route('admin.reports.courses') }}" class="group/item pl-10 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.courses') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
                        <span class="font-medium">Khóa học</span>
                    </a>
                    <a href="{{ route('admin.reports.activities') }}" class="group/item pl-10 flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm transition-all duration-200 {{ request()->routeIs('admin.reports.activities') ? 'bg-white text-indigo-700 ring-1 ring-indigo-200 dark:bg-gray-800/70 dark:text-indigo-300 dark:ring-indigo-800' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-800/60' }}">
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
            <a href="{{ route('admin.profile.show') }}" class="sidebar-item group px-3 py-2.5 flex items-center gap-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.profile.*') ? 'active bg-gradient-to-r from-indigo-500 via-violet-500 to-fuchsia-500 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20' }}">
                <div class="h-7 w-7 rounded-lg {{ request()->routeIs('admin.profile.*') ? 'text-white bg-white/20' : 'text-gray-600 dark:text-gray-400 bg-slate-100 dark:bg-slate-900/30' }} flex items-center justify-center transition-all duration-300">
                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l448 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zm80 256l64 0c44.2 0 80 35.8 80 80 0 8.8-7.2 16-16 16L80 384c-8.8 0-16-7.2-16-16 0-44.2 35.8-80 80-80zm-24-96a56 56 0 1 1 112 0 56 56 0 1 1 -112 0zm240-48l112 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-112 0c-13.3 0-24-10.7-24-24s10.7-24 24-24zm0 96l112 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-112 0c-13.3 0-24-10.7-24-24s10.7-24 24-24z"/>
                    </svg>
                </div>
                <span class="sidebar-text">Hồ sơ</span>
            </a>

        </nav>

        <!-- Sidebar Footer -->
        <div class="mt-auto border-t border-gray-200/60 dark:border-gray-700/60 flex-shrink-0 bg-gradient-to-t from-gray-50/50 to-transparent dark:from-gray-900/50">
            <!-- Toggle Buttons -->
            <div class="p-3 flex items-center justify-center gap-2">
                <button id="sidebarToggle" onclick="toggleSidebarCollapse()" class="flex items-center justify-center w-11 h-11 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-900/20 dark:hover:to-purple-900/20 transition-all duration-300 hover:scale-110"></button>
            </div>
        </div>
    </aside>

    <div class="w-full h-full relative">
        <!-- Top Nav -->
        <nav class="hidden absolute top-0 left-0 right-0 py-3 bg-white dark:bg-gray-900 backdrop-blur-xl border-b border-gray-200 dark:border-gray-700/60 transition-all duration-300 z-[110]">
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
            </div>
        </nav>

        <!-- Main -->
        <main class="z-[100] h-[100dvh] p-3 md:p-4 transition-all duration-300 bg-gray-100 dark:bg-gray-900 overflow-y-auto">
            @yield('content')
        </main>

        @include('components.notification-modal')
        @include('components.delete-modal')
        @include('components.block-modal')
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


    </script>

    @stack('scripts')
</body>
</html>
