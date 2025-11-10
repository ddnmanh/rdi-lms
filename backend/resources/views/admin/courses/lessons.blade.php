@extends('admin.layout')

@section('title', 'Quản lý bài học')
@section('description', 'Thêm và quản lý bài học cho khóa học')

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
                    <button onclick="openAddLessonModal()"
                       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white hover:bg-blue-700 focus:outline-none">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Thêm bài học</span>
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

    {{-- Lessons List --}}
    <div class="w-full max-w-6xl mx-auto bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center gap-3">
            <div class="h-8 w-8 rounded bg-green-600 grid place-items-center">
                <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">Danh sách bài học</h3>
        </div>

        <div id="lessonsList" class="p-6">
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <div class="h-8 w-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Đang tải...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add/Edit Lesson Modal --}}
<div id="lessonModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-900 dark:text-gray-100">Thêm bài học</h3>
            <button onclick="closeLessonModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="lessonForm" onsubmit="saveLesson(event)" class="p-6">
            <input type="hidden" id="lessonId">
            <div class="space-y-6">
                <div class="flex flex-col gap-0.5">
                    <label for="title" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Tiêu đề *</label>
                    <input type="text" id="title" required
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                </div>

                <div class="flex flex-col gap-0.5">
                    <label for="description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Mô tả</label>
                    <textarea id="description" rows="3"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-0.5">
                        <label for="duration" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Thời lượng (giây) *</label>
                        <input type="number" id="duration" min="1" required
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    </div>

                    <div class="flex flex-col gap-0.5">
                        <label for="display_order" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Thứ tự hiển thị</label>
                        <input type="number" id="display_order" min="0" value="0"
                            class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                    </div>
                </div>

                <div class="flex flex-col gap-0.5">
                    <label for="video_url" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1 ml-3">Video URL *</label>
                    <input type="url" id="video_url" required
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 outline-none">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeLessonModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-300">
                    Hủy
                </button>
                <button type="submit"
                    class="px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Lưu</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const courseId = {{ $courseId }};
    let courseData = null;

    document.addEventListener('DOMContentLoaded', async () => {
        await loadCourseData();
        await loadLessons();
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

    async function loadLessons() {
        try {
            const data = await apiRequest(`/lessons?course_id=${courseId}&per_page=100`);
            if (data?.success) {
                renderLessons(data.data.data || []);
            }
        } catch (error) {
            document.getElementById('lessonsList').innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <p class="text-red-600 dark:text-red-400">${escapeHtml(error.message)}</p>
                    </div>
                </div>
            `;
        }
    }

    function renderLessons(lessons) {
        const container = document.getElementById('lessonsList');
        if (!lessons.length) {
            container.innerHTML = `
                <div class="flex items-center justify-center py-12">
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Khóa học chưa có bài học nào</p>
                </div>
            `;
            return;
        }

        container.innerHTML = lessons.map(lesson => {
            const durationMinutes = Math.floor(lesson.duration / 60);
            const durationSeconds = lesson.duration % 60;
            const durationFormatted = `${durationMinutes}:${durationSeconds.toString().padStart(2, '0')}`;

            return `
                <div class="group flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4 hover:bg-gray-100 dark:hover:bg-gray-800 mb-2">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="h-10 w-10 flex-shrink-0 rounded bg-green-600 grid place-items-center">
                            <svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate">${escapeHtml(lesson.title ?? 'Không có tên')}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Thời lượng: ${durationFormatted} | Thứ tự: ${escapeHtml(String(lesson.display_order ?? '-'))}
                            </p>
                            ${lesson.description ? `<p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">${escapeHtml(String(lesson.description))}</p>` : ''}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 ml-4">
                        <button onclick="editLesson(${lesson.id})"
                            class="px-3 py-1.5 text-xs font-semibold text-amber-600 bg-amber-50 dark:bg-amber-900/20 rounded-lg hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-all duration-300">
                            Sửa
                        </button>
                        <button onclick="deleteLesson(${lesson.id})"
                            class="px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-all duration-300">
                            Xóa
                        </button>
                    </div>
                </div>
            `;
        }).join('');
    }

    function openAddLessonModal() {
        document.getElementById('modalTitle').textContent = 'Thêm bài học';
        document.getElementById('lessonForm').reset();
        document.getElementById('lessonId').value = '';
        document.getElementById('display_order').value = '0';
        document.getElementById('lessonModal').classList.remove('hidden');
        document.getElementById('lessonModal').classList.add('flex');
    }

    function closeLessonModal() {
        document.getElementById('lessonModal').classList.add('hidden');
        document.getElementById('lessonModal').classList.remove('flex');
    }

    async function editLesson(id) {
        try {
            const data = await apiRequest(`/lessons/${id}`);
            if (data?.success) {
                const lesson = data.data;
                document.getElementById('modalTitle').textContent = 'Chỉnh sửa bài học';
                document.getElementById('lessonId').value = lesson.id;
                document.getElementById('title').value = lesson.title || '';
                document.getElementById('description').value = lesson.description || '';
                document.getElementById('duration').value = lesson.duration || '';
                document.getElementById('display_order').value = lesson.display_order || '0';
                document.getElementById('video_url').value = lesson.video_url || '';
                document.getElementById('lessonModal').classList.remove('hidden');
                document.getElementById('lessonModal').classList.add('flex');
            }
        } catch (error) {
            // showAlert('Không thể tải thông tin bài học: ' + error.message, 'error');
        }
    }

    async function deleteLesson(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa bài học này?')) return;

        try {
            const data = await apiRequest(`/lessons/${id}`, {
                method: 'DELETE'
            });

            if (data?.success) {
                // showAlert(data.message || 'Xóa bài học thành công', 'success');
                await loadLessons();
            }
        } catch (error) {
            // showAlert(error.message, 'error');
        }
    }

    async function saveLesson(event) {
        event.preventDefault();
        const lessonId = document.getElementById('lessonId').value;
        const title = document.getElementById('title').value.trim();
        const description = document.getElementById('description').value.trim();
        const duration = parseInt(document.getElementById('duration').value);
        const displayOrder = parseInt(document.getElementById('display_order').value) || 0;
        const videoUrl = document.getElementById('video_url').value.trim();

        if (!title) {
            // showAlert('Vui lòng nhập tiêu đề bài học', 'error');
            return;
        }

        if (!duration || duration < 1) {
            // showAlert('Vui lòng nhập thời lượng hợp lệ (ít nhất 1 giây)', 'error');
            return;
        }

        if (!videoUrl) {
            // showAlert('Vui lòng nhập URL video', 'error');
            return;
        }

        const formData = {
            course_id: courseId,
            title: title,
            description: description || null,
            duration: duration,
            video_url: videoUrl,
            display_order: displayOrder,
        };

        try {
            let data;
            if (lessonId) {
                data = await apiRequest(`/lessons/${lessonId}`, {
                    method: 'PUT',
                    body: JSON.stringify(formData)
                });
            } else {
                data = await apiRequest('/lessons', {
                    method: 'POST',
                    body: JSON.stringify(formData)
                });
            }

            if (data?.success) {
                // showAlert(data.message || 'Lưu thành công', 'success');
                closeLessonModal();
                await loadLessons();
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

