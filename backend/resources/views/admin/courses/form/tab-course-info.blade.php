{{-- Course Information Tab --}}
<div class="tab-panel h-full flex flex-col overflow-hidden" data-tab-content="tab-course-info">
    <div class="flex-1 min-h-0 overflow-y-auto">
        <form id="courseInfoForm" onsubmit="saveCourseInfo(event)" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Thumbnail Preview + Upload --}}
                <div class="col-span-1 md:col-span-2 row-span-2">
                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Ảnh thumbnail</label>
                    <div
                        id="thumbnailDropZone"
                        class="flex flex-row flex-wrap items-center gap-4 p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg"
                    >
                        <div class="w-[150px] aspect-video rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="thumbnailPreview" alt="thumbnail preview" class="h-full w-full object-cover hidden">
                            <svg id="thumbnailPlaceholder" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-12 w-12 text-gray-400">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54-1.96-2.36L6.5 17h11l-3.54-4.71z"/>
                            </svg>
                        </div>

                        <div class="flex-1">
                            <div class="text-gray-600 dark:text-gray-300">Kéo & thả ảnh vào đây, hoặc</div>
                            <div class="mt-2 flex items-center gap-3">
                                <label for="thumbnail" class="px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Chọn ảnh
                                </label>
                                <button
                                    id="btnClearNewThumbnail"
                                    type="button"
                                    class="hidden px-3 py-2 rounded-md border border-red-300 text-red-600 hover:bg-red-50 dark:border-red-600 dark:hover:bg-gray-700"
                                >
                                    Xóa ảnh mới
                                </button>
                                <input
                                    id="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                />
                            </div>
                            <div class="mt-2 text-gray-500 dark:text-gray-400">Hỗ trợ PNG, JPG, WEBP — Tối đa 10MB</div>
                        </div>
                    </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label name="title_LABEL" for="title" class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">
                        Tiêu đề <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                        placeholder="Nhập tiêu đề khóa học">
                    <span id="title_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                <div class="col-span-1">
                    <label name="start_date_LABEL" for="start_date" class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Ngày bắt đầu</label>
                    @include('components.datetime-picker', [
                        'id' => 'start_date',
                        'placeholder' => 'Thời gian bắt đầu...',
                    ])
                    <span id="start_date_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                <div class="col-span-1">
                    <label name="end_date_LABEL" for="end_date" class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Ngày kết thúc</label>
                    @include('components.datetime-picker', [
                        'id' => 'end_date',
                        'placeholder' => 'Thời gian kết thúc...',
                    ])
                    <span id="end_date_MSG" class="ml-4 text-sm mt-1 italic hidden"></span>
                </div>

                <div class="md:col-span-4">
                    <label for="description" class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                    <textarea id="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"
                        placeholder="Nhập mô tả khóa học"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button
                    type="button"
                    onclick="handleGotoBackPage_Global()"
                    class="px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200"
                >
                    Hủy
                </button>
                <button
                    type="submit"
                    id="saveCourseInfoBtn"
                    class="px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                    Lưu thông tin cơ bản
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Course Info Tab JavaScript --}}
<script>
    // ===== COURSE INFO TAB FUNCTIONS =====
    function handleThumbnailFile(file) {
        const MAX_SIZE = 10 * 1024 * 1024; // 10MB
        const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (thumbnailPreview) {
            try {
                URL.revokeObjectURL(thumbnailPreview);
            } catch (e) {
                // noop
            }
        }

        if (!file) {
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailPlaceholder');
            const clearBtn = document.getElementById('btnClearNewThumbnail');
            const input = document.getElementById('thumbnail');

            if (existingThumbnail) {
                thumbnailEl.src = existingThumbnail;
                thumbnailEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
            clearBtn.classList.add('hidden');
            if (input) input.value = '';
            thumbnailPreview = null;
            return;
        }

        if (!ALLOWED_TYPES.includes(file.type)) {
            NotificationModal.show('Định dạng không hỗ trợ. Hãy chọn ảnh PNG, JPG, WEBP hoặc GIF.', 'error');
            return;
        }

        if (file.size > MAX_SIZE) {
            NotificationModal.show('Ảnh quá lớn. Kích thước tối đa 10MB.', 'error');
            return;
        }

        const previewUrl = URL.createObjectURL(file);
        thumbnailPreview = previewUrl;

        const thumbnailEl = document.getElementById('thumbnailPreview');
        const placeholderEl = document.getElementById('thumbnailPlaceholder');
        const clearBtn = document.getElementById('btnClearNewThumbnail');

        thumbnailEl.src = previewUrl;
        thumbnailEl.classList.remove('hidden');
        placeholderEl.classList.add('hidden');
        clearBtn.classList.remove('hidden');
    }

    function initThumbnailPreview() {
        const dropZone = document.getElementById('thumbnailDropZone');
        const input = document.getElementById('thumbnail');
        const clearBtn = document.getElementById('btnClearNewThumbnail');
        const thumbnailEl = document.getElementById('thumbnailPreview');
        const placeholderEl = document.getElementById('thumbnailPlaceholder');

        // Default preview for create mode
        if (!existingThumbnail && mode === 'CREATE_COURSE') {
            thumbnailEl.classList.add('hidden');
            placeholderEl.classList.remove('hidden');
        }

        // Drag and drop handlers
        if (dropZone) {
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-gray-300', 'dark:border-gray-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-gray-700');
                dropZone.classList.add('border-gray-300', 'dark:border-gray-600');

                const file = e.dataTransfer.files && e.dataTransfer.files[0] ? e.dataTransfer.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                    if (input) input.files = e.dataTransfer.files;
                }
            });
        }

        // File input change handler
        if (input) {
            input.addEventListener('change', (e) => {
                const file = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (file) {
                    handleThumbnailFile(file);
                }
            });
        }

        // Clear button handler
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                handleThumbnailFile(null);
            });
        }
    }

    function renderCourseInfo() {
        try {
            document.getElementById('title').value = courseData.title || '';
            document.getElementById('description').value = courseData.description || '';
            DateTimePicker.setValue('start_date', courseData.start_date);
            DateTimePicker.setValue('end_date', courseData.end_date);

            // Load thumbnail
            const thumbnailEl = document.getElementById('thumbnailPreview');
            const placeholderEl = document.getElementById('thumbnailPlaceholder');
            if (courseData.thumbnail_path) {
                existingThumbnail = courseData.thumbnail_path;
                thumbnailEl.src = courseData.thumbnail_path;
                thumbnailEl.classList.remove('hidden');
                placeholderEl.classList.add('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
                placeholderEl.classList.remove('hidden');
            }
        } catch (error) {
            NotificationModal.show('Không thể tải thông tin khóa học: ' + error.message, 'error', handleGotoBackPage_Global);
        }
    }

    async function saveCourseInfo(event) {
        event.preventDefault();
        renderInputErrors_Global(null, true); // Clear previous errors

        const submitBtn = document.getElementById('saveCourseInfoBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang lưu...';
        }

        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const startDate = DateTimePicker.getValue('start_date');
        const endDate = DateTimePicker.getValue('end_date');
        const fileInput = document.getElementById('thumbnail');
        const thumbnailFile = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;

        // Validation
        if (!title) {
            renderInputErrors_Global({
                title: ['Vui lòng nhập tiêu đề khóa học']
            });
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Lưu thông tin cơ bản';
            }
            return;
        }

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (end < start) {
                renderInputErrors_Global({
                    end_date: ['Ngày kết thúc phải sau ngày bắt đầu']
                });
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Lưu thông tin cơ bản';
                }
                return;
            }
        }

        try {
            const fd = new FormData();
            if (mode == 'EDIT_COURSE') {
                fd.append('_method', 'PUT');
            }
            fd.append('title', title);
            if (description) fd.append('description', description);
            if (startDate) fd.append('start_date', startDate);
            if (endDate) fd.append('end_date', endDate);
            fd.append('timezone', getClientTimezone_Global());
            if (existingThumbnail) fd.append('existingThumbnail', existingThumbnail);
            if (thumbnailFile) fd.append('thumbnail', thumbnailFile);

            let data = null;
            if (mode == 'EDIT_COURSE') {
                data = await CourseProvider.handleUpdateCourse(courseId, fd);
            } else {
                data = await CourseProvider.handleCreateCourse(fd);
            }
            
            if (data.success) {
                courseId = data.data?.id ?? courseId;
                if (data.data) {
                    courseData = data.data;
                }
                NotificationModal.show('Lưu thông tin cơ bản thành công', 'success');
            } else if (response.status === 422) {
                let errorsField = data.errors || null;
                renderInputErrors_Global(errorsField);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }

        } catch (error) {
            NotificationModal.show(error.message || 'Lưu thông tin thất bại', 'error');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Lưu thông tin cơ bản';
            }
        }
    }
</script>
