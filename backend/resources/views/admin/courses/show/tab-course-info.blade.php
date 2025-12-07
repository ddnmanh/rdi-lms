{{-- Course Information Tab --}}
<div class="tab-panel h-full flex flex-col items-stretch justify-start" data-tab-content="tab-course-info">
    <div class="px-6 py-4 flex items-center justify-between gap-3 rounded-t-none">
        <div class="flex items-center gap-3"></div>
        <a id="editButton" href="#"
            class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-4 py-2.5  font-semibold text-white hover:bg-amber-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 shadow-sm transition-all duration-300">
            <i class="fa-solid fa-pen"></i>
            <span>Chỉnh sửa</span>
        </a>
    </div>

    <div class="flex-1 min-h-0 h-full p-6 pt-0">
        <div class="w-full h-full overflow-y-auto">
            {{-- Personal Information Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 items-start gap-6 h-fit ">
                {{-- Avatar Section --}}
                <div class="col-span-1">
                    <label class="block  font-semibold text-blue-700 dark:text-gray-300 mb-2">Ảnh bìa</label>
                    <div class="flex items-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                        <div class="w-[300px] aspect-video rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="courseThumbnail" src="" alt="Avatar" class="h-full w-full object-cover hidden">
                        </div>
                        <div class="flex-1">
                            {{-- <div class=" text-gray-600 dark:text-gray-300">Ảnh bìa của người dùng</div> --}}
                        </div>
                    </div>
                </div>

                <div class="col-span-1 flex flex-col items-stretch h-full">
                    <label class="font-semibold text-blue-700 dark:text-gray-300 mb-2">Người tạo</label>
                    <div class="flex-1 flex flex-col items-center justify-center gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40">
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <img id="creatorAvatar" src="" alt="Creator Avatar" class="h-full w-full object-cover hidden">
                        </div>
                        <div class="text-center">
                            <div id="creatorName" class="font-semibold text-gray-900 dark:text-gray-100 mb-1"></div>
                            <div id="creatorEmail" class="text-sm text-gray-500 dark:text-gray-400"></div>
                        </div>
                    </div>
                </div>

                @php
                    $infoField = function($label, $id) {
                        return <<<HTML
                        <div>
                            <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">{$label}</label>
                            <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <p class="{$id}  text-gray-900 dark:text-gray-100 break-all">-</p>
                            </div>
                        </div>
                        HTML;
                    };
                @endphp

                {!! $infoField('ID', 'courseId') !!}
                {!! $infoField('Tiêu đề', 'courseTitle') !!}
                {!! $infoField('Ngày bắt đầu', 'courseStartDate') !!}
                {!! $infoField('Ngày kết thúc', 'courseEndDate') !!}
                {!! $infoField('Số bài học', 'courseLessonsCount') !!}
                {!! $infoField('Số học viên', 'courseUsersCount') !!}
                {!! $infoField('Ngày tạo', 'courseCreatedAt') !!}
                {!! $infoField('Ngày cập nhật', 'courseUpdatedAt') !!}

                <div class="col-span-2">
                    <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                    <div class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <p class="courseDescription  text-gray-900 dark:text-gray-100 break-all">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    
</div>

<script>
    function renderCourse() {
        const pageTitleEl = document.getElementById('coursePageTitle');
        if (pageTitleEl) pageTitleEl.textContent = courseData_MainShow.title || 'Chi tiết khóa học';
        document.getElementById('editButton').href = `/admin/courses/${courseData_MainShow.id}/edit`;
        document.getElementById('manageLessonsButton').href = `/admin/courses/${courseData_MainShow.id}/edit`;
        document.getElementById('manageUsersButton').href = `/admin/courses/${courseData_MainShow.id}/edit`;
        setText('courseId', courseData_MainShow.id);
        setText('courseTitle', courseData_MainShow.title || 'Chưa có tiêu đề');
        setText('courseDescription', courseData_MainShow.description || 'Chưa có mô tả');
        setDate('courseStartDate', courseData_MainShow.start_date);
        setDate('courseEndDate', courseData_MainShow.end_date);
        setDate('courseCreatedAt', courseData_MainShow.created_at);
        setDate('courseUpdatedAt', courseData_MainShow.updated_at);

        // Hiển thị thumbnail
        const thumbnailEl = document.getElementById('courseThumbnail');
        if (thumbnailEl) {
            if (courseData_MainShow.thumbnail_path) {
                thumbnailEl.src = courseData_MainShow.thumbnail_path;
                thumbnailEl.classList.remove('hidden');
            } else {
                thumbnailEl.classList.add('hidden');
            }
        }

        // Render Creator Info
        if (typeof userCreator_MainShow !== 'undefined' && userCreator_MainShow) {
             const creatorAvatarEl = document.getElementById('creatorAvatar');
             const creatorNameEl = document.getElementById('creatorName');
             const creatorEmailEl = document.getElementById('creatorEmail');

             if (creatorAvatarEl) {
                 if (userCreator_MainShow.avatar_path) {
                     creatorAvatarEl.src = userCreator_MainShow.avatar_path;
                     creatorAvatarEl.classList.remove('hidden');
                 } else {
                     creatorAvatarEl.classList.add('hidden');
                 }
             }
             if (creatorNameEl) creatorNameEl.textContent = userCreator_MainShow.fullname || 'N/A';
             if (creatorEmailEl) creatorEmailEl.textContent = userCreator_MainShow.email || 'N/A';
        }
    }    
</script>