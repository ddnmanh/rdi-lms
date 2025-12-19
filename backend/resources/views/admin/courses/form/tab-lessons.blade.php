{{-- Lessons Tab --}}
<div class="tab-panel h-full flex flex-col overflow-hidden hidden" data-tab-content="tab-lessons">
    <div class="flex-1 min-h-0 overflow-hidden">
        <form id="lessonsForm" onsubmit="saveLessons(event)" class="h-full p-6 flex flex-col">
            {{-- <div class="p-6 pb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Quản lý bài học</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kéo thả để sắp xếp thứ tự bài học trong khóa học</p>
            </div> --}}

            <div class="flex-1 min-h-0">
                <div class="h-full rounded-lg relative table-scroll-container flex flex-row items-stretch gap-4">
                    <div class="flex-1 min-h-0 flex flex-col items-stretch justify-start">
                        <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-2">Bài học tự do</label>
                        <div id="orphanedLessonsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 drop-zone" data-drop-zone="orphaned">
                            <div class="w-fit mx-auto mt-[100px]">
                                <div id="SPINNER_LOADING_ORPHANED">
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

                    <div class="flex flex-col items-center justify-center w-[60px] 3xl:w-[80px]">
                        <svg class="w-4 text-gray-400" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.3 288 480 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-370.7 0 105.4-105.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/>
                        </svg>
                        <svg class="w-4 mt-2 text-gray-400" viewBox="0 0 512 512" fill="currentColor">
                            <path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-105.4 105.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-h-0 flex flex-col items-stretch justify-start">
                        <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-2">Bài học của khóa học</label>
                        <div id="parentedLessonsListBox" class="flex-1 min-h-0 p-2 space-y-2 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 drop-zone" data-drop-zone="parented">
                            <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                                <p>Không có bài học trong khóa học</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="lessonsCount">0</span> bài học được chọn
                    </div>
                    <button
                        type="submit"
                        id="saveLessonsBtn"
                        class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-500"
                    >
                        Lưu danh sách bài học
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Lessons Tab JavaScript --}}
<script>
    // ===== LESSONS TAB FUNCTIONS =====
    let selectedLessonIds = new Set();
    let draggedElement = null;
    let draggedLessonData = null;
    let draggedFromListType = null;
    let dropIndicator = null;

    async function loadOrphanedLessons() {
        try {
            const data = await apiRequest(`/lessons?orphaned=true`);
            if (data.success) {
                orphanedLessonsList = [...data.data?.data];
                renderOrphanedLessons();
            }
        } catch (error) {
            NotificationModal.show('Lỗi khi tải bài học: ' + error.message, 'error');
        }
    }

    async function loadParentedLessons() {
        try {
            const data = await apiRequest(`/lessons?course_id=${courseId}`);
            if (data.success) {
                parentedLessonsList = [...data.data?.data];
                renderParentedLessons();
            }
        } catch (error) {
            NotificationModal.show('Lỗi khi tải bài học: ' + error.message, 'error');
        }
    }

    function renderOrphanedLessons() {
        const container = document.getElementById('orphanedLessonsListBox');
        if (!container) return;

        // Hide spinner
        const spinner = container.querySelector('#SPINNER_LOADING_ORPHANED');
        if (spinner) spinner.style.display = 'none';

        if (orphanedLessonsList.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                    <p>Không có bài học tự do</p>
                </div>
            `;
            return;
        }

        container.innerHTML = orphanedLessonsList.map(lesson => `
            <div class="draggable-item flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-move transition-all"
                draggable="true" data-lesson-id="${lesson.id}" data-list-type="orphaned">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                        <img src="${lesson.thumbnail_path}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(lesson.title ?? 'Không có tên')}</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">ID: ${lesson.id ?? '-'}</p>
                    </div>
                </div>
            </div>
        `).join('');

        attachDragListeners(container);
    }

    function renderParentedLessons() {
        const container = document.getElementById('parentedLessonsListBox');
        if (!container) return;

        if (parentedLessonsList.length === 0) {
            container.innerHTML = `
                <div class="flex items-center justify-center h-full text-gray-400 dark:text-gray-500">
                    <p>Không có bài học trong khóa học</p>
                </div>
            `;
            updateTabBadge('lessonsTabCount', 0);
            updateLessonsCount(0);
            return;
        }

        container.innerHTML = parentedLessonsList.map(lesson => `
            <div class="draggable-item flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-move transition-all"
                draggable="true" data-lesson-id="${lesson.id}" data-list-type="parented">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <div class="flex-shrink-0 rounded-lg bg-gray-200 dark:bg-gray-700 grid place-items-center">
                        <img src="${lesson.thumbnail_path}" alt="" class="w-[70px] aspect-video object-cover rounded-lg">
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml_Global(lesson.title ?? 'Không có tên')}</h4>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">ID: ${lesson.id ?? '-'} | Thứ tự: ${lesson.display_order ?? '-'}</p>
                    </div>
                </div>
            </div>
        `).join('');

        attachDragListeners(container);
        updateTabBadge('lessonsTabCount', parentedLessonsList.length);
        updateLessonsCount(parentedLessonsList.length);
    }

    function attachDragListeners(container) {
        const items = container.querySelectorAll('.draggable-item');
        items.forEach((item, index) => {
            item.addEventListener('dragstart', (e) => handleDragStart(e, index));
            item.addEventListener('dragend', handleDragEnd);
            item.addEventListener('dragover', handleItemDragOver);
            item.addEventListener('dragleave', handleItemDragLeave);
        });
    }

    function handleItemDragOver(e) {
        e.preventDefault();
        e.stopPropagation();

        const item = e.currentTarget;
        if (!item || item === draggedElement) return;

        const rect = item.getBoundingClientRect();
        const mouseY = e.clientY;
        const itemMiddle = rect.top + rect.height / 2;
        const zone = item.parentElement;

        // Remove all old indicators
        const existingIndicators = zone.querySelectorAll('.drop-indicator');
        existingIndicators.forEach(indicator => {
            indicator.remove();
        });

        // Create new indicator
        const indicator = document.createElement('div');
        indicator.className = 'drop-indicator active';

        // Insert indicator before or after item based on mouse position
        if (mouseY < itemMiddle) {
            item.parentElement.insertBefore(indicator, item);
        } else {
            item.parentElement.insertBefore(indicator, item.nextSibling);
        }
    }

    function handleItemDragLeave(e) {
        if (!e.currentTarget.contains(e.relatedTarget)) {
            const indicator = e.currentTarget.parentElement.querySelector('.drop-indicator');
            if (indicator) {
                indicator.remove();
            }
        }
    }

    function handleDragStart(e, index) {
        draggedElement = e.target.closest('.draggable-item');
        if (!draggedElement) return;

        const lessonId = parseInt(draggedElement.getAttribute('data-lesson-id'));
        draggedFromListType = draggedElement.getAttribute('data-list-type');

        if (draggedFromListType === 'orphaned') {
            draggedLessonData = orphanedLessonsList.find(lesson => lesson.id === lessonId);
        } else {
            draggedLessonData = parentedLessonsList.find(lesson => lesson.id === lessonId);
        }

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', draggedElement.outerHTML);
    }

    function handleDragEnd(e) {
        if (draggedElement) {
            draggedElement.classList.remove('dragging', 'opacity-50', 'scale-95');
        }
        draggedElement = null;
        draggedLessonData = null;
        draggedFromListType = null;

        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.classList.remove('drag-over');
        });
        document.querySelectorAll('.drop-indicator').forEach(indicator => {
            indicator.remove();
        });
    }

    function initDropZones() {
        const orphanedZone = document.getElementById('orphanedLessonsListBox');
        const parentedZone = document.getElementById('parentedLessonsListBox');

        [orphanedZone, parentedZone].forEach(zone => {
            if (!zone) return;

            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.stopPropagation();
                e.dataTransfer.dropEffect = 'move';
                zone.classList.add('drag-over');

                // If drag over empty area (not an item), show indicator at the end
                const target = e.target;
                if (!target.closest('.draggable-item')) {
                    // Remove old indicators
                    const existingIndicators = zone.querySelectorAll('.drop-indicator');
                    existingIndicators.forEach(indicator => {
                        indicator.remove();
                    });

                    // Create indicator at end of list
                    const indicator = document.createElement('div');
                    indicator.className = 'drop-indicator active';
                    zone.appendChild(indicator);
                }
            });

            zone.addEventListener('dragleave', (e) => {
                if (!zone.contains(e.relatedTarget)) {
                    zone.classList.remove('drag-over');
                    // Remove indicators when leaving zone
                    const indicators = zone.querySelectorAll('.drop-indicator');
                    indicators.forEach(indicator => {
                        indicator.remove();
                    });
                }
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('drag-over');

                if (!draggedLessonData || !draggedFromListType) return;

                const targetListType = zone.getAttribute('data-drop-zone');

                // Find drop position based on indicator or mouse position
                let dropIndex = 0;
                const indicator = zone.querySelector('.drop-indicator');

                if (indicator) {
                    const allChildren = Array.from(zone.children);
                    const indicatorIndex = allChildren.indexOf(indicator);
                    let itemCount = 0;
                    for (let i = 0; i < indicatorIndex; i++) {
                        if (allChildren[i].classList.contains('draggable-item')) {
                            itemCount++;
                        }
                    }
                    dropIndex = itemCount;
                } else {
                    dropIndex = zone.querySelectorAll('.draggable-item').length;
                }

                // Remove all drop indicators
                document.querySelectorAll('.drop-indicator').forEach(indicator => {
                    indicator.remove();
                });

                if (draggedFromListType !== targetListType) {
                    // Move between lists
                    if (draggedFromListType === 'orphaned') {
                        orphanedLessonsList = orphanedLessonsList.filter(lesson => lesson.id !== draggedLessonData.id);
                        parentedLessonsList.splice(dropIndex, 0, draggedLessonData);
                    } else {
                        parentedLessonsList = parentedLessonsList.filter(lesson => lesson.id !== draggedLessonData.id);
                        orphanedLessonsList.splice(dropIndex, 0, draggedLessonData);
                    }
                } else {
                    // Reorder within same list
                    const sourceList = draggedFromListType === 'orphaned' ? orphanedLessonsList : parentedLessonsList;
                    const oldIndex = sourceList.findIndex(lesson => lesson.id === draggedLessonData.id);

                    if (oldIndex !== -1 && oldIndex !== dropIndex) {
                        sourceList.splice(oldIndex, 1);
                        const newIndex = dropIndex > oldIndex ? dropIndex - 1 : dropIndex;
                        sourceList.splice(newIndex, 0, draggedLessonData);
                    }
                }

                // Update display orders
                parentedLessonsList.forEach((lesson, index) => {
                    lesson.display_order = index + 1;
                });

                renderOrphanedLessons();
                renderParentedLessons();
            });
        });
    }

    function updateLessonsCount(count) {
        const el = document.getElementById('lessonsCount');
        if (el) el.textContent = count;
    }

    async function saveLessons(event) {
        event.preventDefault();

        if (!courseId) {
            NotificationModal.show('Vui lòng lưu thông tin cơ bản trước', 'warning');
            return;
        }

        const submitBtn = document.getElementById('saveLessonsBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang lưu...';
        }

        try {
            const lessons_ids_add = parentedLessonsList.map(lesson => ({
                id: lesson.id,
                display_order: lesson.display_order
            }));

            if (lessons_ids_add.length > 0) {
                await CourseProvider.handleAddLessonsToCourse(courseId, {
                    lessons: lessons_ids_add
                });
            }

            const lessons_ids_remove = orphanedLessonsList.filter(lesson => lesson.course_id !== null)?.map(lesson => lesson.id);
            if (lessons_ids_remove.length > 0) {
                await CourseProvider.handleRemoveLessonsFromCourse(courseId, {
                    lesson_ids: lessons_ids_remove
                });
            }

            NotificationModal.show('Lưu bài học thành công', 'success');
        } catch (error) {
            NotificationModal.show('Lưu bài học thất bại: ' + error.message, 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Lưu bài học';
            }
        }
    }
</script>
