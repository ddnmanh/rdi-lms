@extends('admin.layout')

@section('title', $mode === 'create' ? 'Thêm bài học' : 'Chỉnh sửa bài học')

@section('description', $mode === 'create' ? 'Thêm bài học mới vào hệ thống' : 'Chỉnh sửa thông tin bài học')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.lessons.list') }}"
                       class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="h-4 w-4 -ml-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Quay lại</span>
                    </a>
                </div>
                <div class="flex items-center gap-2">
                </div>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <form id="lessonForm" onsubmit="saveLesson(event)" class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="lessonId" value="{{ $mode === 'edit' ? ($lessonId ?? '') : '' }}">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-0.5">
                <label for="course_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">
                    Khóa học <span class="text-red-500">*</span>
                </label>
                <select id="course_id" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    <option value="">Chọn khóa học</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Chọn khóa học mà bài học này thuộc về</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">
                    Tiêu đề <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Tiêu đề bài học sẽ hiển thị trong danh sách</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Mô tả</label>
                <textarea id="description" rows="4"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none resize-none"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Mô tả chi tiết về bài học</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-0.5">
                    <label for="duration" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">
                        Thời lượng (giây) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="duration" min="1" required
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Thời lượng bài học tính bằng giây</p>
                </div>

                <div class="flex flex-col gap-0.5">
                    <label for="display_order" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Thứ tự hiển thị</label>
                    <input type="number" id="display_order" min="0" value="0"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Thứ tự hiển thị bài học trong khóa học</p>
                </div>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="video_url" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">
                    Video URL <span class="text-red-500">*</span>
                </label>
                <input type="url" id="video_url" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Đường dẫn đến video bài học</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.lessons.list') }}"
                class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                Hủy
            </a>
            <button type="submit"
                class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span>Lưu</span>
            </button>
        </div>
    </form>

</div>

{{-- Alert Notification Modal --}}
<div id="alertModal" class="fixed inset-0 z-[100] overflow-y-auto overflow-x-hidden" style="display: none;">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeAlertModal()"></div>

    {{-- Modal Container --}}
    <div class="relative flex min-h-full items-center justify-center p-4 z-10">
        <div class="relative w-full max-w-xl p-5 flex flex-col items-stretch justify-start gap-4 transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-200 dark:border-gray-700 transition-all"
            onclick="event.stopPropagation()">
            {{-- Modal Header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div id="alertIcon" class="flex h-12 w-12 items-center justify-center rounded-xl">
                        <i id="alertIconClass" class="text-xl"></i>
                    </div>
                    <div>
                        <h3 id="alertTitle" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Thông báo
                        </h3>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Body --}}
            <div class="">
                <p id="alertMessage" class="text-gray-700 dark:text-gray-300">
                </p>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            {{-- Modal Footer --}}
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAlertModal()"
                    id="alertButton"
                    class="px-4 py-2.5 text-sm font-semibold text-white rounded-xl transition-all duration-300 flex items-center gap-2">
                    <span>Đóng</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const mode = '{{ $mode }}';
    const lessonId = @if($mode === 'edit' && isset($lessonId)) {{ $lessonId }} @else null @endif;

    document.addEventListener('DOMContentLoaded', async function() {
        await loadCourses();
        if (mode === 'edit' && lessonId) {
            await loadLessonData(lessonId);
        }
    });

    async function loadCourses() {
        try {
            const data = await apiRequest('/courses?per_page=100');
            if (data.success) {
                const courses = data.data.data || [];
                const courseSelect = document.getElementById('course_id');

                // Clear existing options except first one
                courseSelect.innerHTML = '<option value="">Chọn khóa học</option>';

                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = course.title;
                    courseSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            showNotificationModel('Không thể tải danh sách khóa học: ' + error.message, 'error');
        }
    }

    async function loadLessonData(id) {
        try {
            const data = await apiRequest(`/lessons/${id}`);
            if (data.success) {
                const lesson = data.data;
                document.getElementById('course_id').value = lesson.course_id || '';
                document.getElementById('title').value = lesson.title || '';
                document.getElementById('description').value = lesson.description || '';
                document.getElementById('duration').value = lesson.duration || '';
                document.getElementById('video_url').value = lesson.video_url || '';
                document.getElementById('display_order').value = lesson.display_order || 0;
            }
        } catch (error) {
            showNotificationModel('Không thể tải thông tin bài học: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.lessons.list') }}';
            }, 2000);
        }
    }

    async function saveLesson(event) {
        event.preventDefault();
        const lessonIdValue = document.getElementById('lessonId').value;
        const courseId = document.getElementById('course_id').value;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const duration = document.getElementById('duration').value;
        const videoUrl = document.getElementById('video_url').value.trim();
        const displayOrder = document.getElementById('display_order').value || 0;

        // Validation
        if (!courseId) {
            showNotificationModel('Vui lòng chọn khóa học', 'error');
            return;
        }

        if (!title) {
            showNotificationModel('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

        if (!duration || parseInt(duration) < 1) {
            showNotificationModel('Vui lòng nhập thời lượng hợp lệ (ít nhất 1 giây)', 'error');
            return;
        }

        if (!videoUrl) {
            showNotificationModel('Vui lòng nhập URL video', 'error');
            return;
        }

        const formData = {
            course_id: parseInt(courseId),
            title: title,
            description: description || null,
            duration: parseInt(duration),
            video_url: videoUrl,
            display_order: parseInt(displayOrder) || 0,
        };

        try {
            let data;
            if (lessonIdValue) {
                // Edit mode
                data = await apiRequest(`/lessons/${lessonIdValue}`, {
                    method: 'PUT',
                    body: JSON.stringify(formData)
                });
            } else {
                // Create mode
                data = await apiRequest('/lessons', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
            }

            if (data.success) {
                showNotificationModel(data.message || 'Lưu thành công', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('admin.lessons.list') }}';
                }, 1000);
            }
        } catch (error) {
            showNotificationModel(error.message, 'error');
        }
    }

    // Alert Modal Functions
    function showNotificationModel(message, type = 'success') {
        const alertModal = document.getElementById('alertModal');
        const alertIcon = document.getElementById('alertIcon');
        const alertIconClass = document.getElementById('alertIconClass');
        const alertTitle = document.getElementById('alertTitle');
        const alertMessage = document.getElementById('alertMessage');
        const alertButton = document.getElementById('alertButton');

        // Set message
        alertMessage.textContent = message;

        // Set type-specific styles
        switch (type) {
            case 'success':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 dark:bg-green-900/20';
                alertIconClass.className = 'fas fa-check-circle text-xl text-green-600 dark:text-green-400';
                alertTitle.textContent = 'Thành công';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-xl hover:bg-green-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'error':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 dark:bg-red-900/20';
                alertIconClass.className = 'fas fa-exclamation-circle text-xl text-red-600 dark:text-red-400';
                alertTitle.textContent = 'Lỗi';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'warning':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/20';
                alertIconClass.className = 'fas fa-exclamation-triangle text-xl text-amber-600 dark:text-amber-400';
                alertTitle.textContent = 'Cảnh báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition-all duration-300 flex items-center gap-2';
                break;
            case 'info':
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 dark:bg-blue-900/20';
                alertIconClass.className = 'fas fa-info-circle text-xl text-blue-600 dark:text-blue-400';
                alertTitle.textContent = 'Thông tin';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2';
                break;
            default:
                alertIcon.className = 'flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700';
                alertIconClass.className = 'fas fa-bell text-xl text-gray-600 dark:text-gray-400';
                alertTitle.textContent = 'Thông báo';
                alertButton.className = 'px-4 py-2.5 text-sm font-semibold text-white bg-gray-600 rounded-xl hover:bg-gray-700 transition-all duration-300 flex items-center gap-2';
        }

        // Show modal
        alertModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeAlertModal() {
        const alertModal = document.getElementById('alertModal');
        alertModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const alertModal = document.getElementById('alertModal');
            if (alertModal && alertModal.style.display !== 'none') {
                closeAlertModal();
            }
        }
    });
</script>
@endsection

