{{-- Lessons Tab --}}
<div class="tab-panel hidden h-full flex flex-col items-stretch justify-start" data-tab-content="tab-lessons">
    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
        <div class="flex items-center gap-3"></div>
        <a id="manageLessonsButton" href="#"
            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
            <i class="fa-solid fa-pen"></i>
            <span>Quản lý bài học</span>
        </a>
    </div>
    <div class="flex-1 min-h-0 p-6 pt-0">
        <div id="lessonsList" class="space-y-4 h-full overflow-y-auto">

        </div>
    </div>
</div>

<script>
    function renderLessons() {
        const lessonsList = document.getElementById('lessonsList');
        if (!lessonsData_MainShow.length) {
            lessonsList.innerHTML = `<p class=" text-gray-500 dark:text-gray-400 italic">Khóa học chưa có bài học nào</p>`;
        } else {
            lessonsList.innerHTML = lessonsData_MainShow.map(lesson => `
                <div class="group p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 hover:bg-gray-50/50 dark:hover:bg-gray-700/30 flex flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="">
                            <span>${lesson.display_order}</span>
                        </div>
                        <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                            <img src="${lesson.thumbnail_path}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class=" font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(lesson.title ?? 'Không có tên')}</h4>
                            <p class= text-gray-500 dark:text-gray-400 mt-1">${escapeHtml_Global(String(lesson.description ?? '-'))}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 flex-1 min-w-0">

                    </div>

                    <div class="flex flex-row items-stretch">

                        <div class="px-4 flex flex-col items-center border-r border-gray-100 dark:border-gray-700/50">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 truncate">Tham gia</span>
                            <span class="text-lg font-bold text-blue-700 dark:text-blue-300">${lessonUserWatchedCounts_MainShow[lesson.id] ?? 0}</span>
                        </div>

                        <div class="px-4 flex flex-col items-center last:border-r-0 border-r border-gray-100 dark:border-gray-700/50">
                            <span class="text-xs font-medium text-green-600 dark:text-green-400">Hoàn thành</span>
                            <span class="text-lg font-bold text-green-700 dark:text-green-300">${lessonUserFinishedCounts_MainShow[lesson.id] ?? 0}</span>
                        </div>

                    </div>
                </div>
            `).join('');
        }
    }
</script>
