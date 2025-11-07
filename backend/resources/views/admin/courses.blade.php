@extends('admin.layout')

@section('title', 'Quản lý Courses')

@section('content')
<div class="page-header">
    <h2>Quản lý Courses</h2>
    <p>Quản lý khóa học trong hệ thống</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách Courses</h3>
        <button class="btn btn-primary" onclick="openCreateModal()">Thêm Course</button>
    </div>

    <div class="filters">
        <input type="text" id="searchInput" placeholder="Tìm kiếm..." onkeyup="loadCourses()">
        <select id="dateRangeFilter" onchange="loadCourses()">
            <option value="">Tất cả</option>
            <option value="active">Đang diễn ra</option>
            <option value="upcoming">Sắp diễn ra</option>
            <option value="past">Đã kết thúc</option>
        </select>
        <button class="btn btn-secondary" onclick="loadCourses()">Tìm kiếm</button>
    </div>

    <div id="coursesTable">
        <div class="loading">
            <div class="spinner"></div>
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>

    <div class="pagination" id="pagination"></div>
</div>

<!-- Create/Edit Modal -->
<div class="modal" id="courseModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm Course</h3>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form id="courseForm" onsubmit="saveCourse(event)">
            <input type="hidden" id="courseId">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="description" class="form-control" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label>Start Date</label>
                <input type="datetime-local" id="start_date" class="form-control">
            </div>
            <div class="form-group">
                <label>End Date</label>
                <input type="datetime-local" id="end_date" class="form-control">
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

document.addEventListener('DOMContentLoaded', function() {
    loadCourses();
});

async function loadCourses(page = 1) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const dateRange = document.getElementById('dateRangeFilter').value;

    try {
        let url = `/courses?page=${page}&per_page=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (dateRange) url += `&date_range=${dateRange}`;

        const data = await apiRequest(url);

        if (data.success) {
            renderCoursesTable(data.data);
            renderPagination(data.data);
        }
    } catch (error) {
        document.getElementById('coursesTable').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

function renderCoursesTable(paginationData) {
    const courses = paginationData.data || [];
    let html = '<table class="table"><thead><tr><th>ID</th><th>Title</th><th>Description</th><th>Start Date</th><th>End Date</th><th>Số bài học</th><th>Thao tác</th></tr></thead><tbody>';

    if (courses.length === 0) {
        html += '<tr><td colspan="7" style="text-align: center;">Không có dữ liệu</td></tr>';
    } else {
        courses.forEach(course => {
            const lessonsCount = (course.lessons || []).length;
            html += `<tr>
                <td>${course.id}</td>
                <td>${course.title}</td>
                <td>${course.description ? (course.description.substring(0, 50) + '...') : '-'}</td>
                <td>${formatDate(course.start_date)}</td>
                <td>${formatDate(course.end_date)}</td>
                <td>${lessonsCount}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editCourse(${course.id})">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCourse(${course.id})">Xóa</button>
                </td>
            </tr>`;
        });
    }

    html += '</tbody></table>';
    document.getElementById('coursesTable').innerHTML = html;
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
    html += `<button ${current === 1 ? 'disabled' : ''} onclick="loadCourses(${current - 1})">Trước</button>`;
    html += `<span class="page-info">Trang ${current} / ${last}</span>`;
    html += `<button ${current === last ? 'disabled' : ''} onclick="loadCourses(${current + 1})">Sau</button>`;

    pagination.innerHTML = html;
}

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Thêm Course';
    document.getElementById('courseForm').reset();
    document.getElementById('courseId').value = '';
    document.getElementById('courseModal').classList.add('active');
}

async function editCourse(id) {
    try {
        const data = await apiRequest(`/courses/${id}`);
        if (data.success) {
            const course = data.data;
            document.getElementById('modalTitle').textContent = 'Sửa Course';
            document.getElementById('courseId').value = course.id;
            document.getElementById('title').value = course.title;
            document.getElementById('description').value = course.description || '';

            if (course.start_date) {
                const startDate = new Date(course.start_date);
                document.getElementById('start_date').value = startDate.toISOString().slice(0, 16);
            }
            if (course.end_date) {
                const endDate = new Date(course.end_date);
                document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);
            }

            document.getElementById('courseModal').classList.add('active');
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function saveCourse(event) {
    event.preventDefault();
    const courseId = document.getElementById('courseId').value;
    const formData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value || null,
        start_date: document.getElementById('start_date').value || null,
        end_date: document.getElementById('end_date').value || null,
    };

    try {
        let data;
        if (courseId) {
            data = await apiRequest(`/courses/${courseId}`, {
                method: 'PUT',
                body: JSON.stringify(formData)
            });
        } else {
            data = await apiRequest('/courses', {
                method: 'POST',
                body: JSON.stringify(formData)
            });
        }

        if (data.success) {
            showAlert(data.message || 'Lưu thành công');
            closeModal();
            loadCourses(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

async function deleteCourse(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa course này?')) return;

    try {
        const data = await apiRequest(`/courses/${id}`, {
            method: 'DELETE'
        });

        if (data.success) {
            showAlert(data.message || 'Xóa thành công');
            loadCourses(currentPage);
        }
    } catch (error) {
        showAlert(error.message, 'error');
    }
}

function closeModal() {
    document.getElementById('courseModal').classList.remove('active');
}
</script>
@endsection

