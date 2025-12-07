{{-- Users Tab --}}
<div class="tab-panel hidden h-full flex flex-col items-stretch justify-start" data-tab-content="tab-users">
    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
        <div class="flex items-center gap-3"></div>
        <a id="manageUsersButton" href="#"
            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
            <i class="fa-solid fa-pen"></i>
            <span>Quản lý sinh viên</span>
        </a>
    </div>
    <div class="flex-1 min-h-0 p-6 pt-0">
        <div id="usersList" class="px-1 space-y-3 h-full overflow-auto">

        </div>
    </div>
</div>

<script>
    function renderUsers() {
        // Sắp xếp học viên theo tiến độ giảm dần
        usersData_MainShow.sort((a, b) => {
            const progressA = a.course_progress?.completion_percentage || 0;
            const progressB = b.course_progress?.completion_percentage || 0;
            return progressB - progressA;
        });

        const totalLessons = lessonsData_MainShow.length;

        const usersList = document.getElementById('usersList');
        if (!usersData_MainShow.length) {
            usersList.innerHTML = `<p class=" text-gray-500 dark:text-gray-400 italic">Khóa học chưa có học viên nào</p>`;
        } else {
            usersList.innerHTML = usersData_MainShow.map((user, idx) => {
                const progress = user.course_progress || {};
                const lessonViews = Array.isArray(user.lesson_views) ? user.lesson_views : [];
                const viewedLessonsCount = lessonViews.length;
                const isPassed = progress.is_passed === 1;
                const completionPercentage = progress.completion_percentage || 0;

                // Sắp xếp lesson views theo thời gian xem gần nhất
                const sortedLessonViews = [...lessonViews].sort((a, b) => {
                    return new Date(b.last_watched_at || 0) - new Date(a.last_watched_at || 0);
                });

                // Tạo danh sách chi tiết bài học đã xem
                const lessonDetailsHtml = sortedLessonViews.length > 0
                    ? sortedLessonViews.map(view => {
                        const lesson = lessonsData_MainShow.find(l => l.id === view.lesson_id);
                        const lessonTitle = lesson ? lesson.title : `Bài học #${view.lesson_id}`;
                        const firstWatched = view.created_at ? new Date(view.created_at).toLocaleString('vi-VN', {
                            day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) : '-';
                        const lastWatched = view.last_watched_at ? new Date(view.last_watched_at).toLocaleString('vi-VN', {
                            day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                        }) : '-';
                        const viewPercent = view.completion_percentage || 0;

                        return `
                            <div class="relative py-3 first:pt-0 last:pb-0 last:border-0 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="${escapeHtml_Global(lessonTitle)}">
                                            ${escapeHtml_Global(lessonTitle)}
                                        </p>
                                        <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1" title="Lần đầu xem">
                                                <i class="fa-regular fa-calendar text-gray-400"></i> ${firstWatched}
                                            </span>
                                            <span class="flex items-center gap-1" title="Lần cuối xem">
                                                <i class="fa-regular fa-clock text-gray-400"></i> ${lastWatched}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col items-end min-w-[60px]">
                                            <span class="text-xs font-bold ${viewPercent >= 100 ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400'}">
                                                ${viewPercent.toFixed(0)}%
                                            </span>
                                            <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full mt-1 overflow-hidden">
                                                <div class="h-full rounded-full ${viewPercent >= 100 ? 'bg-green-500' : 'bg-amber-500'}" style="width: ${Math.min(viewPercent, 100)}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')
                    : `<div class="text-center py-4 text-sm text-gray-500 dark:text-gray-400 italic">Chưa xem bài học nào</div>`;


                const bgColorStatusCss = completionPercentage > 80 ? 'bg-green-500' : completionPercentage >= 30 ? 'bg-blue-500' : 'bg-red-500';
                const textColorStatusCss = completionPercentage > 80 ? 'text-green-600 dark:text-green-400' : completionPercentage >= 30 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400';

                const hexColorStatus = completionPercentage > 80
                    ? '#10b981'
                    : completionPercentage > 50
                        ? '#3b82f6'
                        : completionPercentage > 30
                            ? '#f59e0b'
                            : '#ef4444'


                return `
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                        <div class="p-5 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <div class="flex flex-row items-center justify-between gap-4">
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                            <img src="${user.avatar_path}" alt="" class="w-full h-full object-cover">
                                        </div>
                                        ${isPassed
                                            ? `<div class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-800 rounded-full p-0.5">
                                                <i class="fa-solid fa-circle-check text-green-500 text-base"></i>
                                            </div>`
                                            : ''
                                        }
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-gray-900 dark:text-gray-100 truncate text-base">
                                                ${escapeHtml_Global(user.fullname || user.email || 'Không có tên')}
                                            </h4>
                                        </div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">${escapeHtml_Global(user.email || '')}</p>
                                    </div>
                                </div>

                                <div class="flex flex-row item-center">

                                    <div class="px-6 flex items-center gap-3 flex-shrink-0 border-r border-gray-100 dark:border-gray-700/50">
                                        <div class="relative w-10 h-10 flex-shrink-0">
                                            <div class="w-full h-full rounded-full flex items-center justify-center"
                                                style="background: conic-gradient(
                                                    ${hexColorStatus}
                                                    ${completionPercentage * 3.6}deg,
                                                    rgb(229 231 235 / 0.3) 0deg
                                                );">
                                                <div class="w-[calc(100%-6px)] aspect-square rounded-full bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                                                    <span class="text-xs font-semibold text-[${hexColorStatus}]">${Math.round(completionPercentage, 1)}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="px-6 flex items-center justify-between sm:justify-center sm:flex-col sm:items-center border-r border-gray-100 dark:border-gray-700/50 px-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400 sm:mb-1">Bài học</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                            ${viewedLessonsCount} <span class="text-gray-400 font-normal">/ ${totalLessons}</span>
                                        </span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        ${viewedLessonsCount === 0 ? 'disabled' : ''}
                                        onclick="toggleUserDetail(${idx})"
                                        type="button" class="w-8 h-8 cursor-pointer rounded-full flex items-center justify-center transition-colors ${viewedLessonsCount === 0 ? 'cursor-not-allowed bg-gray-50 dark:bg-gray-800 text-gray-400' : 'hover:bg-gray-200 dark:hover:bg-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300'}"
                                    >
                                        <i id="toggleIconDesktop${idx}" class="fa-solid fa-chevron-down ${viewedLessonsCount === 0 ? 'text-gray-200 dark:text-gray-700' : 'text-gray-500 dark:text-gray-400'} text-xs transition-transform duration-300"></i>
                                    </button>
                                </div>

                            </div>

                        </div>

                        <div id="userDetail${idx}" class="hidden p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
                            ${lessonDetailsHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }
    }


    function toggleUserDetail(idx) {
        const detail = document.getElementById(`userDetail${idx}`);
        const iconMobile = document.getElementById(`toggleIcon${idx}`);
        const iconDesktop = document.getElementById(`toggleIconDesktop${idx}`);

        if (detail) {
            detail.classList.toggle('hidden');
            if (iconMobile) iconMobile.classList.toggle('rotate-180');
            if (iconDesktop) iconDesktop.classList.toggle('rotate-180');
        }
    }

    function formatDuration(seconds) {
        if (!seconds || seconds < 0) return '0s';
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = Math.floor(seconds % 60);
        if (h > 0) return `${h}h ${m}m ${s}s`;
        if (m > 0) return `${m}m ${s}s`;
        return `${s}s`;
    }
</script>
