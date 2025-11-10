@extends('admin.layout')

@section('title', $mode === 'create' ? 'Thêm khóa học' : 'Chỉnh sửa khóa học')

@section('description', $mode === 'create' ? 'Thêm khóa học mới vào hệ thống' : 'Chỉnh sửa thông tin khóa học')

@section('content')
<div class="h-full flex flex-col items-stretch justify-start gap-4 2xl:gap-6">
    {{-- Top Bar / Breadcrumbs + Actions (Flat) --}}
    <div class="sticky top-0 z-20">
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.courses.list') }}"
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
    <form id="courseForm" onsubmit="saveCourse(event)" class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <input type="hidden" id="courseId" value="{{ $mode === 'edit' ? ($courseId ?? '') : '' }}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-0.5 md:col-span-2">
                <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Tiêu đề *</label>
                <input type="text" id="title" required
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Tên khóa học sẽ hiển thị trong danh sách</p>
            </div>

            <div class="flex flex-col gap-0.5 md:col-span-2">
                <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Mô tả</label>
                <textarea id="description" rows="4"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none resize-none"></textarea>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Mô tả chi tiết về khóa học</p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="start_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Ngày bắt đầu</label>
                <input type="datetime-local" id="start_date"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">
                    Ngày và giờ bắt đầu khóa học
                    <span id="timezoneIndicator" class="text-blue-600 dark:text-blue-400 font-medium"></span>
                </p>
            </div>

            <div class="flex flex-col gap-0.5">
                <label for="end_date" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Ngày kết thúc</label>
                <input type="datetime-local" id="end_date"
                    class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-3">Ngày và giờ kết thúc khóa học (phải sau ngày bắt đầu)</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('admin.courses.list') }}"
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

<script>
    const mode = '{{ $mode }}';
    const courseId = @if($mode === 'edit' && isset($courseId)) {{ $courseId }} @else null @endif;

    document.addEventListener('DOMContentLoaded', async function() {
        // Hiển thị timezone của client
        const timezoneIndicator = document.getElementById('timezoneIndicator');
        if (timezoneIndicator) {
            const clientTimezone = getClientTimezone();
            timezoneIndicator.textContent = `(Múi giờ: ${clientTimezone})`;
        }

        if (mode === 'edit' && courseId) {
            await loadCourseData(courseId);
        }
    });

    // Sử dụng các utility functions từ layout.blade.php:
    // - formatDateTimeLocal(): Chuyển UTC từ Laravel sang local time cho datetime-local input
    // - getClientTimezone(): Lấy timezone của client để gửi lên server
    // Lưu ý: Client gửi local time và timezone, server sẽ chuyển đổi sang UTC trước khi lưu
    async function loadCourseData(id) {
        try {
            const data = await apiRequest(`/courses/${id}`);
            if (data.success) {
                const course = data.data;
                document.getElementById('title').value = course.title || '';
                document.getElementById('description').value = course.description || '';
                document.getElementById('start_date').value = formatDateTimeLocal(course.start_date);
                document.getElementById('end_date').value = formatDateTimeLocal(course.end_date);
            }
        } catch (error) {
            showNotificationModel('Không thể tải thông tin khóa học: ' + error.message, 'error');
            setTimeout(() => {
                window.location.href = '{{ route('admin.courses.list') }}';
            }, 2000);
        }
    }

    async function saveCourse(event) {
        event.preventDefault();
        const courseIdValue = document.getElementById('courseId').value;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        // Validation
        if (!title) {
            showNotificationModel('Vui lòng nhập tiêu đề khóa học', 'error');
            return;
        }

        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            if (end < start) {
                showNotificationModel('Ngày kết thúc phải sau ngày bắt đầu', 'error');
                return;
            }
        }

        // Gửi local time và timezone lên server, để server chuyển đổi sang UTC
        const clientTimezone = getClientTimezone();
        const formData = {
            title: title,
            description: description || null,
            start_date: startDate || null,
            end_date: endDate || null,
            timezone: clientTimezone, // Gửi timezone để server biết cách chuyển đổi
        };

        try {
            let data;
            if (courseIdValue) {
                // Edit mode
                data = await apiRequest(`/courses/${courseIdValue}`, {
                    method: 'PUT',
                    body: JSON.stringify(formData)
                });
            } else {
                // Create mode
                data = await apiRequest('/courses', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
            }

            if (data.success) {
                showNotificationModel(data.message || 'Lưu thành công', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('admin.courses.list') }}';
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
@endsection

