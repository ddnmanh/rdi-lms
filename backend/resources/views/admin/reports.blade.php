@extends('admin.layout')

@section('title', 'Báo cáo')

@section('content')
<div class="page-header">
    <h2>Báo cáo</h2>
    <p>Thống kê và báo cáo hệ thống</p>
</div>

<div class="card">
    <div class="card-header">
        <h3>Thống kê tổng quan</h3>
    </div>
    <div id="overviewStats">
        <div class="loading">
            <div class="spinner"></div>
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Thống kê khóa học</h3>
    </div>
    <div class="filters">
        <select id="courseSelect" onchange="loadCourseStatistics()">
            <option value="">Chọn khóa học</option>
        </select>
    </div>
    <div id="courseStats">
        <p>Vui lòng chọn khóa học để xem thống kê</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách sinh viên trong khóa học</h3>
    </div>
    <div class="filters">
        <select id="courseSelectForStudents" onchange="loadCourseStudents()">
            <option value="">Chọn khóa học</option>
        </select>
        <input type="text" id="studentSearch" placeholder="Tìm kiếm sinh viên..." onkeyup="loadCourseStudents()">
        <button class="btn btn-secondary" onclick="loadCourseStudents()">Tìm kiếm</button>
    </div>
    <div id="courseStudentsTable">
        <p>Vui lòng chọn khóa học để xem danh sách sinh viên</p>
    </div>
    <div class="pagination" id="pagination"></div>
</div>

<script>
let coursesList = [];
let currentPage = 1;

document.addEventListener('DOMContentLoaded', async function() {
    await loadCourses();
    await loadOverview();
});

async function loadCourses() {
    try {
        const data = await apiRequest('/courses?per_page=100');
        if (data.success) {
            coursesList = data.data.data || [];
            const courseSelect = document.getElementById('courseSelect');
            const courseSelectForStudents = document.getElementById('courseSelectForStudents');
            
            coursesList.forEach(course => {
                const option1 = document.createElement('option');
                option1.value = course.id;
                option1.textContent = course.title;
                courseSelect.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = course.id;
                option2.textContent = course.title;
                courseSelectForStudents.appendChild(option2);
            });
        }
    } catch (error) {
        console.error('Error loading courses:', error);
    }
}

async function loadOverview() {
    try {
        const data = await apiRequest('/reports/overview');
        if (data.success) {
            const stats = data.data;
            let html = '<div class="stats-grid">';
            html += `<div class="stat-card">
                <h4>Tổng số khóa học</h4>
                <div class="value">${stats.total_courses}</div>
            </div>`;
            html += `<div class="stat-card">
                <h4>Tổng số sinh viên</h4>
                <div class="value">${stats.total_students}</div>
            </div>`;
            html += `<div class="stat-card">
                <h4>Tổng số bài học</h4>
                <div class="value">${stats.total_lessons}</div>
            </div>`;
            html += '</div>';
            
            if (stats.courses && stats.courses.length > 0) {
                html += '<h4 style="margin-top: 20px;">Danh sách khóa học:</h4>';
                html += '<table class="table"><thead><tr><th>ID</th><th>Tên khóa học</th><th>Số sinh viên</th></tr></thead><tbody>';
                stats.courses.forEach(course => {
                    html += `<tr>
                        <td>${course.id}</td>
                        <td>${course.title}</td>
                        <td>${course.student_count}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
            }
            
            document.getElementById('overviewStats').innerHTML = html;
        }
    } catch (error) {
        document.getElementById('overviewStats').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

async function loadCourseStatistics() {
    const courseId = document.getElementById('courseSelect').value;
    if (!courseId) {
        document.getElementById('courseStats').innerHTML = '<p>Vui lòng chọn khóa học để xem thống kê</p>';
        return;
    }

    try {
        const data = await apiRequest(`/reports/courses/${courseId}/statistics`);
        if (data.success) {
            const stats = data.data;
            let html = '<div class="stats-grid">';
            html += `<div class="stat-card">
                <h4>Tên khóa học</h4>
                <div class="value" style="font-size: 18px;">${stats.course_title}</div>
            </div>`;
            html += `<div class="stat-card">
                <h4>Tổng số sinh viên</h4>
                <div class="value">${stats.total_students}</div>
            </div>`;
            html += `<div class="stat-card">
                <h4>Tổng số bài học</h4>
                <div class="value">${stats.total_lessons}</div>
            </div>`;
            html += `<div class="stat-card">
                <h4>Tổng thời lượng (giây)</h4>
                <div class="value">${stats.total_duration}</div>
            </div>`;
            html += '</div>';
            
            document.getElementById('courseStats').innerHTML = html;
        }
    } catch (error) {
        document.getElementById('courseStats').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

async function loadCourseStudents(page = 1) {
    currentPage = page;
    const courseId = document.getElementById('courseSelectForStudents').value;
    const search = document.getElementById('studentSearch').value;

    if (!courseId) {
        document.getElementById('courseStudentsTable').innerHTML = '<p>Vui lòng chọn khóa học để xem danh sách sinh viên</p>';
        return;
    }

    try {
        let url = `/reports/courses/${courseId}/students?page=${page}&per_page=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;

        const data = await apiRequest(url);
        
        if (data.success) {
            renderCourseStudentsTable(data.data);
            renderPagination(data.data);
        }
    } catch (error) {
        document.getElementById('courseStudentsTable').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
    }
}

function renderCourseStudentsTable(paginationData) {
    const students = paginationData.items || [];
    let html = '<table class="table"><thead><tr><th>ID</th><th>Email</th><th>Fullname</th><th>Tiến độ (%)</th><th>Thời gian xem (giây)</th><th>Hoàn thành</th></tr></thead><tbody>';
    
    if (students.length === 0) {
        html += '<tr><td colspan="6" style="text-align: center;">Không có dữ liệu</td></tr>';
    } else {
        students.forEach(student => {
            const isCompleted = student.is_completed ? 'Có' : 'Không';
            html += `<tr>
                <td>${student.user_id}</td>
                <td>${student.email}</td>
                <td>${student.fullname || '-'}</td>
                <td>${student.overall_percentage}%</td>
                <td>${student.total_watched_duration}</td>
                <td>${isCompleted}</td>
            </tr>`;
        });
    }
    
    html += '</tbody></table>';
    document.getElementById('courseStudentsTable').innerHTML = html;
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
    html += `<button ${current === 1 ? 'disabled' : ''} onclick="loadCourseStudents(${current - 1})">Trước</button>`;
    html += `<span class="page-info">Trang ${current} / ${last}</span>`;
    html += `<button ${current === last ? 'disabled' : ''} onclick="loadCourseStudents(${current + 1})">Sau</button>`;
    
    pagination.innerHTML = html;
}
</script>
@endsection

