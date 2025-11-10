@extends('admin.layout')

@section('title', 'Quản lý học viên')
@section('description', 'Thêm và quản lý học viên cho khóa học')

@section('content')
<div class="min-h-full flex flex-col gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.show', $courseId) }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Quay lại</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openAddUserModal()"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Thêm học viên</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Course Info --}}
    <div id="courseInfo" class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-blue-600 grid place-items-center">
                <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 id="courseTitle" class="text-lg font-bold text-gray-900 dark:text-gray-100">Đang tải...</h2>
                <p id="courseDescription" class="text-sm text-gray-500 dark:text-gray-400 mt-1">-</p>
            </div>
        </div>
    </div>

    {{-- Users List --}}
    <div class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
            <div class="h-8 w-8 rounded bg-purple-600 grid place-items-center">
                <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Danh sách học viên</h3>
        </div>

        <div id="usersList" class="p-6">
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add User Modal --}}
<div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Thêm học viên</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <div class="mb-4">
                <label for="userSearch" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tìm kiếm học viên</label>
                <input type="text" id="userSearch" placeholder="Nhập email hoặc tên..."
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none"
                    onkeyup="searchUsers()">
            </div>

            <div id="availableUsers" class="space-y-2 max-h-96 overflow-y-auto">
                <div class="flex items-center justify-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Nhập từ khóa để tìm kiếm học viên</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const courseId = {{ $courseId }};
    let courseData = null;
    let courseUsers = [];
    let allUsers = [];
    let searchTimeout = null;

    document.addEventListener('DOMContentLoaded', async () => {
        await loadCourseData();
        await loadCourseUsers();
    });

    async function loadCourseData() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data?.success) {
                courseData = data.data;
                document.getElementById('courseTitle').textContent = courseData.title || 'Khóa học';
                document.getElementById('courseDescription').textContent = courseData.description || '-';
            }
        } catch (error) {
            // showAlert('Không thể tải thông tin khóa học: ' + error.message, 'error');
        }
    }

    async function loadCourseUsers() {
        try {
            const data = await apiRequest(`/courses/${courseId}`);
            if (data?.success) {
                courseUsers = Array.isArray(data.data.users) ? data.data.users : [];
                renderUsers(courseUsers);
            }
        } catch (error) {
            document.getElementById('usersList').innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${escapeHtml(error.message)}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderUsers(users) {
        const container = document.getElementById('usersList');
        if (!users.length) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có học viên nào</p>
                </div>
            `;
            return;
        }

        container.innerHTML = users.map(user => {
            const initial = (user.fullname || user.email || 'U').charAt(0).toUpperCase();
            return `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 mb-2">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-purple-600 grid place-items-center text-white text-sm font-semibold">
                            ${escapeHtml(initial)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(user.fullname || user.email || 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">${escapeHtml(user.email || '')}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <button onclick="removeUser(${user.id})"
                            class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-300">
                            Xóa
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function openAddUserModal() {
        document.getElementById('userSearch').value = '';
        document.getElementById('availableUsers').innerHTML = `
            <div class="flex items-center justify-center py-8">
                <p class="text-sm text-gray-500 dark:text-gray-400">Nhập từ khóa để tìm kiếm học viên</p>
            </div>
        `;
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userModal').classList.remove('flex');
    }

    function searchUsers() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            const search = document.getElementById('userSearch').value.trim();
            if (!search) {
                document.getElementById('availableUsers').innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nhập từ khóa để tìm kiếm học viên</p>
                    </div>
                `;
                return;
            }

            try {
                const data = await apiRequest(`/users?search=${encodeURIComponent(search)}&per_page=20`);
                if (data?.success) {
                    allUsers = data.data.data || [];
                    renderAvailableUsers(allUsers);
                }
            } catch (error) {
                document.getElementById('availableUsers').innerHTML = `
                    <div class="flex items-center justify-center py-8">
                        <p class="text-sm text-red-600 dark:text-red-400">${escapeHtml(error.message)}</p>
                    </div>
                `;
            }
        }, 500);
    }

    function renderAvailableUsers(users) {
        const container = document.getElementById('availableUsers');
        if (!users.length) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Không tìm thấy học viên nào</p>
                </div>
            `;
            return;
        }

        // Filter out users already in course
        const courseUserIds = courseUsers.map(u => u.id);
        const availableUsers = users.filter(user => !courseUserIds.includes(user.id));

        if (!availableUsers.length) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tất cả học viên đã được thêm vào khóa học</p>
                </div>
            `;
            return;
        }

        container.innerHTML = availableUsers.map(user => {
            const initial = (user.fullname || user.email || 'U').charAt(0).toUpperCase();
            return `
                <div class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-purple-600 grid place-items-center text-white text-sm font-semibold">
                            ${escapeHtml(initial)}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(user.fullname || user.email || 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">${escapeHtml(user.email || '')}</p>
                        </div>
                    </div>
                    <button onclick="addUser(${user.id})"
                        class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all duration-300 ml-4">
                        Thêm
                    </button>
                </div>
            `;
        }).join('');
    }

    async function addUser(userId) {
        try {
            const data = await apiRequest(`/courses/${courseId}/users/add`, {
                method: 'POST',
                body: JSON.stringify({ user_id: userId })
            });

            if (data?.success) {
                // showAlert(data.message || 'Thêm học viên thành công', 'success');
                closeUserModal();
                await loadCourseUsers();
            }
        } catch (error) {
            // showAlert(error.message, 'error');
        }
    }

    async function removeUser(userId) {
        if (!confirm('Bạn có chắc chắn muốn xóa học viên này khỏi khóa học?')) return;

        try {
            const data = await apiRequest(`/courses/${courseId}/users/remove`, {
                method: 'POST',
                body: JSON.stringify({ user_id: userId })
            });

            if (data?.success) {
                // showAlert(data.message || 'Xóa học viên thành công', 'success');
                await loadCourseUsers();
            }
        } catch (error) {
            // showAlert(error.message, 'error');
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
</script>
@endsection

