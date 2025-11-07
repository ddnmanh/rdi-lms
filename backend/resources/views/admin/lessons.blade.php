@extends('admin.layout')

@section('title', 'Quản lý Lessons')

@section('content')
<div class="page-header">
    <h2>Quản lý Lessons</h2>
    <p>Quản lý bài học trong hệ thống</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Lessons</h3>
        <button class="btn btn-primary" onclick="openCreateModal()">Thêm Lesson</button>
    </div>

    <div class="filters">
        <input type="text" id="searchInput" placeholder="Tìm kiếm..." onkeyup="loadLessons()">
        <select id="courseFilter" onchange="loadLessons()">
            <option value="">Tất cả courses</option>
        </select>
        <button class="btn btn-secondary" onclick="loadLessons()">Tìm kiếm</button>
    </div>

    <div id="lessonsTable">
        <div class="loading">
            <div class="spinner"></div>
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>

    <div class="pagination" id="pagination"></div>
</div>

<!-- Create/Edit Modal -->
<div class="modal" id="lessonModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm Lesson</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form id="lessonForm" onsubmit="saveLesson(event)">
            <input type="hidden" id="lessonId">
            <div class="form-group">
                <label>Course *</label>
                <select id="course_id" class="form-control" required>
                    <option value="">Chọn course</option>
                </select>
            </div>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Duration (giây) *</label>
                <input type="number" id="duration" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label>Video URL *</label>
                <input type="url" id="video_url" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" id="display_order" class="form-control" min="0" value="0">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentPage = 1;
let coursesList = [];

document.addEventListener('DOMContentLoaded', async function() {
    await loadCourses();
    await loadLessons();
});

async function loadCourses() {
    try {
        const data = await apiRequest('/courses?per_page=100');
        if (data.success) {
            coursesList = data.data.data || [];
            const courseFilter = document.getElementById('courseFilter');
            const courseSelect = document.getElementById('course_id');
            
            coursesList.forEach(course => {
                const option1 = document.createElement('option');
                option1.value = course.id;
                option1.textContent = course.title;
                courseFilter.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = course.id;
                option2.textContent = course.title;
                courseSelect.appendChild(option2);
            });
        }
    } catch (error) {
        console.error('Error loading courses:', error);
    }
}

async function loadLessons(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const courseId = document.getElementById('courseFilter').value;

    try {
        let url = `/lessons?page=${page}&per_page=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (courseId) url += `&course_id=${courseId}`;

        const data = await apiRequest(url);
        
        if (data.success) {
            renderLessonsTable(data.data);
            renderPagination(data.data);
        }
    } catch (error) {
        document.getElementById('lessonsTable').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

function renderLessonsTable(paginationData) {
    const lessons = paginationData.data || [];
    let html = '<table class="table"><thead><tr><th>ID</th><th>Course</th><th>Title</th><th>Duration</th><th>Display Order</th><th>Thao tác</th></tr></thead><tbody>';
    
    if (lessons.length === 0) {
        html += '<tr><td colspan="6" style="text-align: center;">Không có dữ liệu</td></tr>';
    } else {
        lessons.forEach(lesson => {
            const courseName = lesson.course ? lesson.course.title : '-';
            const durationMinutes = Math.floor(lesson.duration / 60);
            const durationSeconds = lesson.duration % 60;
            const durationFormatted = `${durationMinutes}:${durationSeconds.toString().padStart(2, '0')}`;
            
            html += `<tr>
                <td>${lesson.id}</td>
                <td>${courseName}</td>
                <td>${lesson.title}</td>
                <td>${durationFormatted}</td>
                <td>${lesson.display_order}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editLesson(${lesson.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLesson(${lesson.id})">Xóa</button>
                </td>
            </tr>`;
        });
    }
    
    html += '</tbody></table>';
    document.getElementById('lessonsTable').innerHTML = html;
}

function renderPagination(paginationData) {
    const pagination = document.getElementById('pagination');
    const current = paginationData.current_page;
    const last = paginationData.last_page;

    if (last <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let html = '';
    html += `<button ${current === 1 ? 'disabled' : ''} onclick="loadLessons(${current - 1})">Trước</button>`;
    html += `<span class="page-info">Trang ${current} / ${last}</span>`;
    html += `<button ${current === last ? 'disabled' : ''} onclick="loadLessons(${current + 1})">Sau</button>`;
    
    pagination.innerHTML = html;
}

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Lesson';
    document.getElementById('lessonForm').reset();
    document.getElementById('lessonId').value = '';
    document.getElementById('display_order').value = '0';
    document.getElementById('lessonModal').classList.add('active');
}

async function editLesson(id) {
    try {
        const data = await apiRequest(`/lessons/${id}`);
        if (data.success) {
            const lesson = data.data;
            document.getElementById('modalTitle').textContent = 'Sửa Lesson';
            document.getElementById('lessonId').value = lesson.id;
            document.getElementById('course_id').value = lesson.course_id;
            document.getElementById('title').value = lesson.title;
            document.getElementById('description').value = lesson.description || '';
            document.getElementById('duration').value = lesson.duration;
            document.getElementById('video_url').value = lesson.video_url;
            document.getElementById('display_order').value = lesson.display_order || 0;

            document.getElementById('lessonModal').classList.add('active');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function saveLesson(event) {
    event.preventDefault();
    const lessonId = document.getElementById('lessonId').value;
    const formData = {
        course_id: parseInt(document.getElementById('course_id').value),
        title: document.getElementById('title').value,
        description: document.getElementById('description').value || null,
        duration: parseInt(document.getElementById('duration').value),
        video_url: document.getElementById('video_url').value,
        display_order: parseInt(document.getElementById('display_order').value) || 0,
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

        if (data.success) {
            showAlert(data.message || 'Lưu thành công');
            closeModal();
            loadLessons(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function deleteLesson(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa lesson này?')) return;

    try {
        const data = await apiRequest(`/lessons/${id}`, {
            method: 'DELETE'
        });

        if (data.success) {
            showAlert(data.message || 'Xóa thành công');
            loadLessons(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

function closeModal() {
    document.getElementById('lessonModal').classList.remove('active');
}
</script>
@endsection

