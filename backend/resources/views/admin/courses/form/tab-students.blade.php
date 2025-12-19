{{-- Students Tab --}}
<div class="tab-panel h-full flex flex-col overflow-hidden hidden" data-tab-content="tab-students">
    <div class="flex-1 min-h-0 overflow-hidden">
        <form id="studentsForm" onsubmit="saveStudents(event)" class="h-full p-6 flex flex-col">
            {{-- <div class="p-6 pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Quản lý sinh viên</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Thêm hoặc gỡ sinh viên khỏi khóa học</p>
            </div> --}}

            <div class="flex-1 min-h-0">
                <div class="h-full grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-4">
                    <div class="pt-1 flex flex-col min-h-0 h-full">
                        {{-- Header với label và nút toggle filter --}}
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-2">
                            <label class="ml-2 font-semibold text-blue-700 dark:text-gray-300">Danh sách người dùng</label>
                            <button
                                type="button"
                                id="toggleStudentFiltersBtn"
                                onclick="toggleStudentFilters()"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>Bộ lọc</span>
                                <svg id="filterArrowIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Panel bộ lọc (ẩn mặc định) --}}
                        <div id="studentFiltersPanel" class="hidden mb-3">
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl space-y-3">
                                {{-- Search --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tìm kiếm</label>
                                    <input
                                        id="availableStudentsSearch"
                                        type="text"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                        placeholder="Tên hoặc email..."
                                    >
                                </div>

                                {{-- Role filter --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Vai trò</label>
                                    @include('components.select', [
                                        'id' => 'availableStudentsRoleFilter',
                                        'placeholder' => 'Chọn vai trò...',
                                        'searchable' => true,
                                    ])
                                </div>

                                {{-- Date range filter --}}
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Từ ngày</label>
                                        <input
                                            id="availableStudentsDateFrom"
                                            type="date"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Đến ngày</label>
                                        <input
                                            id="availableStudentsDateTo"
                                            type="date"
                                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                        >
                                    </div>
                                </div>

                                {{-- Action buttons --}}
                                <div class="flex items-center gap-2 pt-1">
                                    <button
                                        type="button"
                                        onclick="applyStudentFilters()"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition"
                                    >
                                        Áp dụng
                                    </button>
                                    <button
                                        type="button"
                                        onclick="clearStudentFilters()"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition"
                                    >
                                        Xóa lọc
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Active filters badges --}}
                        <div id="activeFiltersBadges" class="hidden flex-wrap gap-2 mb-2 px-2"></div>

                        {{-- Select all toolbar for available students --}}
                        <div class="flex items-center justify-between px-3 py-2 mb-2 bg-gray-100 dark:bg-gray-700/50 rounded-lg">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="selectAllAvailableStudents"
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    onchange="toggleSelectAllAvailable(this.checked)"
                                >
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Chọn tất cả</span>
                            </label>
                            <span id="availableStudentsSelectedCount" class="text-xs text-gray-500 dark:text-gray-400">
                                Đã chọn: <span class="font-semibold text-blue-600 dark:text-blue-400">0</span>
                            </span>
                        </div>

                        <div id="availableStudentsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                <div id="SPINNER_LOADING_STUDENTS">
                                    <div id="SPINNER_LOADING">
                                    <div id="SPINNER_LOADING_CONTAINER">
                                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
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
                        </div>
                    </div>

                    <div class="flex flex-col items-stretch justify-center gap-3 w-[60px] 3xl:w-[80px] h-full">
                        <button
                            type="button"
                            id="moveStudentsToCourseBtn"
                            class="px-3 py-2 w-full sm:w-auto rounded-md font-semibold text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300 disabled:cursor-not-allowed transition"
                            onclick="moveSelectedStudentsToEnrolled()"
                            disabled
                        >
                            Thêm
                        </button>
                        <button
                            type="button"
                            id="removeStudentsFromCourseBtn"
                            class="px-3 py-2 w-full sm:w-auto rounded-md font-semibold text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/30 disabled:bg-gray-200 disabled:text-gray-400 disabled:dark:bg-gray-700/40 disabled:dark:text-gray-500 disabled:cursor-not-allowed transition"
                            onclick="moveSelectedStudentsToAvailable()"
                            disabled
                        >
                            Gỡ
                        </button>
                    </div>

                    <div class="pt-1 flex flex-col min-h-0 h-full">
                        {{-- Header với label và nút toggle filter --}}
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-2">
                            <label class="ml-2 font-semibold text-blue-700 dark:text-gray-300">Học viên của khóa học</label>
                            <button
                                type="button"
                                id="toggleEnrolledFiltersBtn"
                                onclick="toggleEnrolledFilters()"
                                class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>Bộ lọc</span>
                                <svg id="enrolledFilterArrowIcon" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Panel bộ lọc enrolled (ẩn mặc định) --}}
                        <div id="enrolledFiltersPanel" class="hidden mb-3">
                            <div class="p-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl space-y-3">
                                {{-- Search --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tìm kiếm</label>
                                    <input
                                        id="enrolledStudentsSearch"
                                        type="text"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                                        placeholder="Tên hoặc email..."
                                    >
                                </div>

                                {{-- Role filter --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Vai trò</label>
                                    @include('components.select', [
                                        'id' => 'enrolledStudentsRoleFilter',
                                        'placeholder' => 'Chọn vai trò...',
                                        'searchable' => true,
                                    ])
                                </div>

                                {{-- Action buttons --}}
                                <div class="flex items-center gap-2 pt-1">
                                    <button
                                        type="button"
                                        onclick="applyEnrolledFilters()"
                                        class="flex-1 px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition"
                                    >
                                        Áp dụng
                                    </button>
                                    <button
                                        type="button"
                                        onclick="clearEnrolledFilters()"
                                        class="px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition"
                                    >
                                        Xóa lọc
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Active enrolled filters badges --}}
                        <div id="activeEnrolledFiltersBadges" class="hidden flex-wrap gap-2 mb-2"></div>

                        {{-- Select all toolbar for enrolled students --}}
                        <div class="flex items-center justify-between px-3 py-2 mb-2 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    id="selectAllEnrolledStudents"
                                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                    onchange="toggleSelectAllEnrolled(this.checked)"
                                >
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Chọn tất cả</span>
                            </label>
                            <span id="enrolledStudentsSelectedCount" class="text-xs text-gray-500 dark:text-gray-400">
                                Đã chọn: <span class="font-semibold text-green-600 dark:text-green-400">0</span>
                            </span>
                        </div>

                        <div id="enrolledStudentsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800">
                            <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                <p>Chưa có học viên trong khóa học</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="enrolledStudentsCount">0</span> sinh viên được chọn
                    </div>
                    <button
                        type="submit"
                        id="saveStudentsBtn"
                        class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-500"
                    >
                        Lưu danh sách sinh viên
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Students Tab JavaScript --}}
<script>
    // ===== STUDENTS TAB FUNCTIONS =====
    let selectedAvailableStudentIds = new Set();
    let selectedEnrolledStudentIds = new Set();
    let availableStudentsSearchTerm = '';
    let enrolledStudentsSearchTerm = '';

    // Filter state for available students
    let studentFiltersVisible = false;
    let availableRolesList = [];
    let studentFilters = {
        search: '',
        role_name: '',
        created_at_from: '',
        created_at_to: ''
    };
    let isLoadingStudents = false;

    // Filter state for enrolled students
    let enrolledFiltersVisible = false;
    let enrolledFilters = {
        search: '',
        role_name: ''
    };

    document.addEventListener('DOMContentLoaded', () => {
        Select.init('availableStudentsRoleFilter', {
            options: [],
            searchable: true,
        });
        Select.init('enrolledStudentsRoleFilter', {
            options: [],
            searchable: true,
        });
    });

    async function initStudentsSection() {
        try {
            hydrateEnrolledStudentsFromCourse();
            await Promise.all([
                loadRolesForFilter(),
                loadAvailableStudents()
            ]);
            renderAvailableStudents();
            renderEnrolledStudents();
            initStudentSearchHandlers();
            updateStudentActionButtons();
        } catch (error) {
            console.error(error);
            const container = document.getElementById('availableStudentsListBox');
            if (container) {
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full text-red-500 dark:text-red-400 text-center px-4">
                        ${escapeHtml_Global(error.message || 'Không thể tải danh sách sinh viên')}
                    </div>
                `;
            }
            NotificationModal.show('Không thể tải danh sách sinh viên: ' + (error.message || ''), 'error');
        }
    }

    function hydrateEnrolledStudentsFromCourse() {
        if (mode === 'EDIT_COURSE' && courseData && Array.isArray(courseData.users)) {
            enrolledStudentsList = courseData.users.map(normalizeStudent);
        } else {
            enrolledStudentsList = Array.isArray(enrolledStudentsList) ? enrolledStudentsList.map(normalizeStudent) : [];
        }

        sortStudentsInPlace(enrolledStudentsList);
        initialEnrolledStudentIds = new Set(enrolledStudentsList.map(student => student.id));
    }

    async function loadRolesForFilter() {
        try {
            const data = await RoleProvider.handleGetRoles({ per_page: 1000 });
            availableRolesList = data?.data?.data || [];
            renderRoleFilterOptions();
        } catch (error) {
            console.error('Failed to load roles:', error);
        }
    }

    function renderRoleFilterOptions() {
        const roleOptions = availableRolesList.map(role => ({ value: role.name, label: role.name }));
        Select.setOptions('availableStudentsRoleFilter', roleOptions);
        Select.setOptions('enrolledStudentsRoleFilter', roleOptions);
    }

    async function loadAvailableStudents() {
        if (isLoadingStudents) return;
        isLoadingStudents = true;

        // Show loading state
        const container = document.getElementById('availableStudentsListBox');
        if (container) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                    <div id="SPINNER_LOADING_STUDENTS">
                        <div id="SPINNER_LOADING">
                            <div id="SPINNER_LOADING_CONTAINER">
                                <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                    <div></div>
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
            `;
        }

        try {
            const queryParams = {
                per_page: 2000
            };

            // Apply filters
            if (studentFilters.search) {
                queryParams.search = studentFilters.search;
            }
            if (studentFilters.role_name) {
                queryParams.role_name = studentFilters.role_name;
            }
            if (studentFilters.created_at_from) {
                queryParams.created_at_from = studentFilters.created_at_from;
            }
            if (studentFilters.created_at_to) {
                queryParams.created_at_to = studentFilters.created_at_to;
            }

            const data = await UserProvider.handleGetUsers(queryParams);
        const apiStudents = data?.data?.data || [];

        const enrolledIds = new Set(enrolledStudentsList.map(student => student.id));
        availableStudentsList = apiStudents
            .map(normalizeStudent)
            .filter(student => !enrolledIds.has(student.id));

        sortStudentsInPlace(availableStudentsList);
        } finally {
            isLoadingStudents = false;
        }
    }

    function toggleStudentFilters() {
        studentFiltersVisible = !studentFiltersVisible;
        const panel = document.getElementById('studentFiltersPanel');
        const arrowIcon = document.getElementById('filterArrowIcon');

        if (panel) {
            panel.classList.toggle('hidden', !studentFiltersVisible);
        }
        if (arrowIcon) {
            arrowIcon.style.transform = studentFiltersVisible ? 'rotate(180deg)' : '';
        }
    }

    async function applyStudentFilters() {
        // Get filter values
        const searchInput = document.getElementById('availableStudentsSearch');
        const roleSelect = document.getElementById('availableStudentsRoleFilter');
        const dateFromInput = document.getElementById('availableStudentsDateFrom');
        const dateToInput = document.getElementById('availableStudentsDateTo');

        studentFilters.search = searchInput?.value?.trim() || '';
        studentFilters.role_name = roleSelect?.value || '';
        studentFilters.created_at_from = dateFromInput?.value || '';
        studentFilters.created_at_to = dateToInput?.value || '';

        // Clear local search term since we're using API filter
        availableStudentsSearchTerm = '';

        // Update badges
        renderActiveFiltersBadges();

        // Reload students with new filters
        await loadAvailableStudents();
        renderAvailableStudents();
    }

    function clearStudentFilters() {
        // Reset filter state
        studentFilters = {
            search: '',
            role_name: '',
            created_at_from: '',
            created_at_to: ''
        };

        // Clear form inputs
        const searchInput = document.getElementById('availableStudentsSearch');
        const roleSelect = document.getElementById('availableStudentsRoleFilter');
        const dateFromInput = document.getElementById('availableStudentsDateFrom');
        const dateToInput = document.getElementById('availableStudentsDateTo');

        if (searchInput) searchInput.value = '';
        if (roleSelect) roleSelect.value = '';
        if (dateFromInput) dateFromInput.value = '';
        if (dateToInput) dateToInput.value = '';

        // Update badges
        renderActiveFiltersBadges();

        // Reload
        applyStudentFilters();
    }

    function renderActiveFiltersBadges() {
        const container = document.getElementById('activeFiltersBadges');
        if (!container) return;

        const badges = [];

        if (studentFilters.search) {
            badges.push(createFilterBadge('Tìm kiếm', studentFilters.search, () => {
                studentFilters.search = '';
                document.getElementById('availableStudentsSearch').value = '';
                applyStudentFilters();
            }));
        }

        if (studentFilters.role_name) {
            badges.push(createFilterBadge('Vai trò', studentFilters.role_name, () => {
                studentFilters.role_name = '';
                document.getElementById('availableStudentsRoleFilter').value = '';
                applyStudentFilters();
            }));
        }

        if (studentFilters.created_at_from) {
            badges.push(createFilterBadge('Từ ngày', formatDateForDisplay(studentFilters.created_at_from), () => {
                studentFilters.created_at_from = '';
                document.getElementById('availableStudentsDateFrom').value = '';
                applyStudentFilters();
            }));
        }

        if (studentFilters.created_at_to) {
            badges.push(createFilterBadge('Đến ngày', formatDateForDisplay(studentFilters.created_at_to), () => {
                studentFilters.created_at_to = '';
                document.getElementById('availableStudentsDateTo').value = '';
                applyStudentFilters();
            }));
        }

        if (badges.length > 0) {
            container.innerHTML = badges.join('');
            container.classList.remove('hidden');
            container.classList.add('flex');
        } else {
            container.innerHTML = '';
            container.classList.add('hidden');
            container.classList.remove('flex');
        }
    }

    function createFilterBadge(label, value, onRemove) {
        const id = `filter-badge-${label.replace(/\s/g, '-')}-${Date.now()}`;
        // Store callback in window for onclick
        window[`removeFilter_${id}`] = onRemove;

        return `
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full">
                <span class="text-blue-500 dark:text-blue-400">${escapeHtml_Global(label)}:</span>
                ${escapeHtml_Global(value)}
                <button type="button" onclick="window.removeFilter_${id}()" class="ml-0.5 hover:text-blue-900 dark:hover:text-blue-100">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </span>
        `;
    }

    function formatDateForDisplay(dateStr) {
        if (!dateStr) return '';
        const date = new Date(dateStr);
        return date.toLocaleDateString('vi-VN');
    }

    // ===== ENROLLED STUDENTS FILTER FUNCTIONS =====
    function toggleEnrolledFilters() {
        enrolledFiltersVisible = !enrolledFiltersVisible;
        const panel = document.getElementById('enrolledFiltersPanel');
        const arrowIcon = document.getElementById('enrolledFilterArrowIcon');

        if (panel) {
            panel.classList.toggle('hidden', !enrolledFiltersVisible);
        }
        if (arrowIcon) {
            arrowIcon.style.transform = enrolledFiltersVisible ? 'rotate(180deg)' : '';
        }
    }

    function applyEnrolledFilters() {
        // Get filter values
        const searchInput = document.getElementById('enrolledStudentsSearch');
        const roleValue = Select.getValue('enrolledStudentsRoleFilter');

        enrolledFilters.search = searchInput?.value?.trim() || '';
        enrolledFilters.role_name = roleValue || '';

        // Update badges
        renderActiveEnrolledFiltersBadges();

        // Re-render enrolled students with filters
        renderEnrolledStudents();
    }

    function clearEnrolledFilters() {
        // Reset filter state
        enrolledFilters = {
            search: '',
            role_name: ''
        };

        // Clear form inputs
        const searchInput = document.getElementById('enrolledStudentsSearch');
        if (searchInput) searchInput.value = '';
        Select.setValue('enrolledStudentsRoleFilter', '');

        // Update badges
        renderActiveEnrolledFiltersBadges();

        // Re-render
        renderEnrolledStudents();
    }

    function renderActiveEnrolledFiltersBadges() {
        const container = document.getElementById('activeEnrolledFiltersBadges');
        if (!container) return;

        const badges = [];

        if (enrolledFilters.search) {
            badges.push(createEnrolledFilterBadge('Tìm kiếm', enrolledFilters.search, () => {
                enrolledFilters.search = '';
                document.getElementById('enrolledStudentsSearch').value = '';
                renderEnrolledStudents();
                renderActiveEnrolledFiltersBadges();
            }));
        }

        if (enrolledFilters.role_name) {
            badges.push(createEnrolledFilterBadge('Vai trò', enrolledFilters.role_name, () => {
                enrolledFilters.role_name = '';
                Select.setValue('enrolledStudentsRoleFilter', '');
                renderEnrolledStudents();
                renderActiveEnrolledFiltersBadges();
            }));
        }

        if (badges.length > 0) {
            container.innerHTML = badges.join('');
            container.classList.remove('hidden');
            container.classList.add('flex');
        } else {
            container.innerHTML = '';
            container.classList.add('hidden');
            container.classList.remove('flex');
        }
    }

    function createEnrolledFilterBadge(label, value, onRemove) {
        const id = `enrolled-filter-badge-${label.replace(/\s/g, '-')}-${Date.now()}`;
        window[`removeFilter_${id}`] = onRemove;

        return `
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full">
                <span class="text-green-500 dark:text-green-400">${escapeHtml_Global(label)}:</span>
                ${escapeHtml_Global(value)}
                <button type="button" onclick="window.removeFilter_${id}()" class="ml-0.5 hover:text-green-900 dark:hover:text-green-100">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </span>
        `;
    }

    function initStudentSearchHandlers() {
        // Enter key to apply filters in available search input
        const availableInput = document.getElementById('availableStudentsSearch');
        if (availableInput && !availableInput.dataset.initialized) {
            availableInput.dataset.initialized = 'true';
            availableInput.addEventListener('keypress', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    applyStudentFilters();
                }
            });
        }

        // Enter key to apply filters in enrolled search input
        const enrolledInput = document.getElementById('enrolledStudentsSearch');
        if (enrolledInput && !enrolledInput.dataset.initialized) {
            enrolledInput.dataset.initialized = 'true';
            enrolledInput.addEventListener('keypress', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    applyEnrolledFilters();
                }
            });
        }
    }

    function renderAvailableStudents() {
        const container = document.getElementById('availableStudentsListBox');
        if (!container) return;

        // Hide spinner
        const spinner = container.querySelector('#SPINNER_LOADING_STUDENTS');
        if (spinner) spinner.style.display = 'none';

        // Use local search if available, otherwise show all (already filtered by API)
        const searchTerm = availableStudentsSearchTerm.trim().toLowerCase();
        const filteredStudents = searchTerm
            ? availableStudentsList.filter(student => studentMatchesSearch(student, searchTerm))
            : availableStudentsList;

        const hasActiveFilters = studentFilters.search || studentFilters.role_name || studentFilters.created_at_from || studentFilters.created_at_to;

        if (filteredStudents.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 text-center px-4">
                    ${hasActiveFilters || searchTerm ? 'Không tìm thấy người dùng phù hợp với bộ lọc' : 'Không còn người dùng nào để thêm'}
                </div>
            `;
            return;
        }

        container.innerHTML = filteredStudents.map(student => {
            const isSelected = selectedAvailableStudentIds.has(student.id);
            const roleNames = (student.roles || []).map(r => r.name).join(', ') || 'Chưa có vai trò';

            return `
                <label class="selectable-item flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 cursor-pointer ${isSelected ? 'selected' : ''}">
                    <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        onchange="toggleStudentSelection('available', ${student.id}, this.checked)" ${isSelected ? 'checked' : ''}>
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="h-9 w-9 overflow-hidden flex-shrink-0 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 grid place-items-center font-semibold uppercase">
                            <img src="${student.avatar_path}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(student.fullname || student.email || 'Không có tên')}</p>
                                <span class="flex-shrink-0 px-1.5 py-0.5 text-[10px] font-medium bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded">${escapeHtml_Global(roleNames)}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${escapeHtml_Global(student.email || '')}</p>
                        </div>
                    </div>
                </label>
            `;
        }).join('');
    }

    function renderEnrolledStudents() {
        const container = document.getElementById('enrolledStudentsListBox');
        if (!container) return;

        // Apply filters
        const filteredStudents = enrolledStudentsList.filter(student => {
            // Search filter
            if (enrolledFilters.search) {
                const searchTerm = enrolledFilters.search.toLowerCase();
                const fullname = (student.fullname || '').toLowerCase();
                const email = (student.email || '').toLowerCase();
                if (!fullname.includes(searchTerm) && !email.includes(searchTerm)) {
                    return false;
                }
            }

            // Role filter
            if (enrolledFilters.role_name) {
                const studentRoles = (student.roles || []).map(r => r.name);
                if (!studentRoles.includes(enrolledFilters.role_name)) {
                    return false;
                }
            }

            return true;
        });

        const hasActiveFilters = enrolledFilters.search || enrolledFilters.role_name;

        if (filteredStudents.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500 text-center px-4">
                    ${enrolledStudentsList.length === 0 ? 'Chưa có học viên trong khóa học' : (hasActiveFilters ? 'Không tìm thấy học viên phù hợp với bộ lọc' : 'Không tìm thấy học viên phù hợp')}
                </div>
            `;
            updateTabBadge('studentsTabCount', enrolledStudentsList.length);
            updateEnrolledStudentsCount(enrolledStudentsList.length);
            return;
        }

        container.innerHTML = filteredStudents.map(student => {
            const isSelected = selectedEnrolledStudentIds.has(student.id);

            return `
                <label class="selectable-item flex items-center gap-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-3 cursor-pointer ${isSelected ? 'selected' : ''}">
                    <input type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        onchange="toggleStudentSelection('enrolled', ${student.id}, this.checked)" ${isSelected ? 'checked' : ''}>
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="h-9 w-9 overflow-hidden flex-shrink-0 rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300 grid place-items-center font-semibold uppercase">
                            <img src="${student.avatar_path}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(student.fullname || student.email || 'Không có tên')}</p>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${escapeHtml_Global(student.email || '')}</p>
                        </div>
                    </div>
                </label>
            `;
        }).join('');

        updateTabBadge('studentsTabCount', enrolledStudentsList.length);
        updateEnrolledStudentsCount(enrolledStudentsList.length);
    }

    function toggleStudentSelection(listType, studentId, isChecked) {
        const numericId = Number(studentId);
        if (!Number.isFinite(numericId)) {
            updateStudentActionButtons();
            return;
        }
        const targetSet = listType === 'available' ? selectedAvailableStudentIds : selectedEnrolledStudentIds;

        if (isChecked) {
            targetSet.add(numericId);
        } else {
            targetSet.delete(numericId);
        }

        updateStudentActionButtons();
    }

    function moveSelectedStudentsToEnrolled() {
        if (selectedAvailableStudentIds.size === 0) return;

        const idsToMove = new Set(selectedAvailableStudentIds);
        const movingStudents = [];

        availableStudentsList = availableStudentsList.filter(student => {
            if (idsToMove.has(student.id)) {
                movingStudents.push(student);
                return false;
            }
            return true;
        });

        const existingEnrolledIds = new Set(enrolledStudentsList.map(student => student.id));
        movingStudents.forEach(student => {
            if (!existingEnrolledIds.has(student.id)) {
                enrolledStudentsList.push(student);
            }
        });

        sortStudentsInPlace(enrolledStudentsList);
        selectedAvailableStudentIds.clear();
        renderAvailableStudents();
        renderEnrolledStudents();
        updateStudentActionButtons();
    }

    function moveSelectedStudentsToAvailable() {
        if (selectedEnrolledStudentIds.size === 0) return;

        const idsToMove = new Set(selectedEnrolledStudentIds);
        const movingStudents = [];

        enrolledStudentsList = enrolledStudentsList.filter(student => {
            if (idsToMove.has(student.id)) {
                movingStudents.push(student);
                return false;
            }
            return true;
        });

        const existingAvailableIds = new Set(availableStudentsList.map(student => student.id));
        movingStudents.forEach(student => {
            if (!existingAvailableIds.has(student.id)) {
                availableStudentsList.push(student);
            }
        });

        sortStudentsInPlace(availableStudentsList);
        selectedEnrolledStudentIds.clear();
        renderAvailableStudents();
        renderEnrolledStudents();
        updateStudentActionButtons();
    }

    function updateStudentActionButtons() {
        const addBtn = document.getElementById('moveStudentsToCourseBtn');
        if (addBtn) {
            addBtn.disabled = selectedAvailableStudentIds.size === 0;
        }

        const removeBtn = document.getElementById('removeStudentsFromCourseBtn');
        if (removeBtn) {
            removeBtn.disabled = selectedEnrolledStudentIds.size === 0;
        }

        // Update selected counts
        updateAvailableSelectedCount();
        updateEnrolledSelectedCount();

        // Update "select all" checkbox states
        updateSelectAllCheckboxStates();
    }

    function updateAvailableSelectedCount() {
        const countEl = document.querySelector('#availableStudentsSelectedCount span');
        if (countEl) {
            countEl.textContent = selectedAvailableStudentIds.size;
        }
    }

    function updateEnrolledSelectedCount() {
        const countEl = document.querySelector('#enrolledStudentsSelectedCount span');
        if (countEl) {
            countEl.textContent = selectedEnrolledStudentIds.size;
        }
    }

    function updateSelectAllCheckboxStates() {
        // Available students checkbox
        const availableCheckbox = document.getElementById('selectAllAvailableStudents');
        if (availableCheckbox && availableStudentsList.length > 0) {
            const allSelected = availableStudentsList.every(s => selectedAvailableStudentIds.has(s.id));
            const someSelected = availableStudentsList.some(s => selectedAvailableStudentIds.has(s.id));
            availableCheckbox.checked = allSelected;
            availableCheckbox.indeterminate = someSelected && !allSelected;
        } else if (availableCheckbox) {
            availableCheckbox.checked = false;
            availableCheckbox.indeterminate = false;
        }

        // Enrolled students checkbox
        const enrolledCheckbox = document.getElementById('selectAllEnrolledStudents');
        if (enrolledCheckbox && enrolledStudentsList.length > 0) {
            const allSelected = enrolledStudentsList.every(s => selectedEnrolledStudentIds.has(s.id));
            const someSelected = enrolledStudentsList.some(s => selectedEnrolledStudentIds.has(s.id));
            enrolledCheckbox.checked = allSelected;
            enrolledCheckbox.indeterminate = someSelected && !allSelected;
        } else if (enrolledCheckbox) {
            enrolledCheckbox.checked = false;
            enrolledCheckbox.indeterminate = false;
        }
    }

    function toggleSelectAllAvailable(checked) {
        const container = document.getElementById('availableStudentsListBox');
        if (!container) return;

        if (checked) {
            // Select all available students
            availableStudentsList.forEach(student => {
                selectedAvailableStudentIds.add(student.id);
            });
        } else {
            // Deselect all
            selectedAvailableStudentIds.clear();
        }

        // Update checkboxes directly without re-rendering
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        const labels = container.querySelectorAll('label.selectable-item');

        checkboxes.forEach(cb => {
            cb.checked = checked;
        });

        labels.forEach(label => {
            if (checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });

        updateStudentActionButtons();
    }

    function toggleSelectAllEnrolled(checked) {
        const container = document.getElementById('enrolledStudentsListBox');
        if (!container) return;

        if (checked) {
            // Select all enrolled students
            enrolledStudentsList.forEach(student => {
                selectedEnrolledStudentIds.add(student.id);
            });
        } else {
            // Deselect all
            selectedEnrolledStudentIds.clear();
        }

        // Update checkboxes directly without re-rendering
        const checkboxes = container.querySelectorAll('input[type="checkbox"]');
        const labels = container.querySelectorAll('label.selectable-item');

        checkboxes.forEach(cb => {
            cb.checked = checked;
        });

        labels.forEach(label => {
            if (checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });

        updateStudentActionButtons();
    }

    function updateEnrolledStudentsCount(count) {
        const el = document.getElementById('enrolledStudentsCount');
        if (el) el.textContent = count;
    }

    async function saveStudents(event) {
        event.preventDefault();

        if (!courseId) {
            NotificationModal.show('Vui lòng lưu thông tin cơ bản trước', 'warning');
            return;
        }

        const submitBtn = document.getElementById('saveStudentsBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang lưu...';
        }

        try {
            // Lấy danh sách ID hiện tại (sau khi user thao tác thêm/gỡ)
            const currentIds = new Set(
                enrolledStudentsList
                    .map(student => student.id)
                    .filter(id => Number.isInteger(id))
            );

            // Tính toán những user được thêm mới (có trong currentIds nhưng không có trong initialEnrolledStudentIds)
            const addedIds = Array.from(currentIds).filter(id => !initialEnrolledStudentIds.has(id));

            // Tính toán những user bị gỡ ra (có trong initialEnrolledStudentIds nhưng không có trong currentIds)
            const removedIds = Array.from(initialEnrolledStudentIds).filter(id => !currentIds.has(id));

            // Kiểm tra có thay đổi không
            if (addedIds.length === 0 && removedIds.length === 0) {
                NotificationModal.show('Danh sách sinh viên không có thay đổi', 'info');
                return;
            }

            // Gọi API remove nếu có user bị gỡ
            if (removedIds.length > 0) {
                await CourseProvider.handleRemoveUsersFromCourse(courseId, {
                    user_ids: removedIds
                });
            }

            // Gọi API add nếu có user mới được thêm
            if (addedIds.length > 0) {
                await CourseProvider.handleAddUsersToCourse(courseId, {
                    user_ids: addedIds
                });
            }

            // Cập nhật lại initialEnrolledStudentIds sau khi lưu thành công
            initialEnrolledStudentIds = new Set(currentIds);

            // Hiển thị thông báo chi tiết
            const messages = [];
            if (addedIds.length > 0) messages.push(`Thêm ${addedIds.length} sinh viên`);
            if (removedIds.length > 0) messages.push(`Gỡ ${removedIds.length} sinh viên`);
            NotificationModal.show(messages.join(', ') + ' thành công', 'success');
        } catch (error) {
            NotificationModal.show('Lưu danh sách sinh viên thất bại: ' + error.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Lưu danh sách sinh viên';
            }
        }
    }

    function studentMatchesSearch(student, term) {
        if (!term) return true;
        const fullname = (student.fullname || '').toLowerCase();
        const email = (student.email || '').toLowerCase();
        return fullname.includes(term) || email.includes(term);
    }
</script>

