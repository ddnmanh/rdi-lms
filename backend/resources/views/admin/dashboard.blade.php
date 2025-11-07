@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h2>Dashboard</h2>
    <p>Tổng quan hệ thống</p>
</div>

<div class="stats-grid" id="statsGrid">
    <div class="stat-card">
        <h4>Tổng số khóa học</h4>
        <div class="value" id="totalCourses">-</div>
    </div>
    <div class="stat-card">
        <h4>Tổng số sinh viên</h4>
        <div class="value" id="totalStudents">-</div>
    </div>
    <div class="stat-card">
        <h4>Tổng số bài học</h4>
        <div class="value" id="totalLessons">-</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Danh sách khóa học</h3>
    </div>
    <div id="coursesList">
        <div class="loading">
            <div class="spinner"></div>
            <p>Đang tải dữ liệu...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Load overview stats
        const overviewData = await apiRequest('/reports/overview');

        if (overviewData.success) {
            document.getElementById('totalCourses').textContent = overviewData.data.total_courses;
            document.getElementById('totalStudents').textContent = overviewData.data.total_students;
            document.getElementById('totalLessons').textContent = overviewData.data.total_lessons;

            // Render courses list
            const coursesList = document.getElementById('coursesList');
            if (overviewData.data.courses && overviewData.data.courses.length > 0) {
                let html = '<table class="table"><thead><tr><th>ID</th><th>Tên khóa học</th><th>Số sinh viên</th></tr></thead><tbody>';
                overviewData.data.courses.forEach(course => {
                    html += `<tr>
                        <td>${course.id}</td>
                        <td>${course.title}</td>
                        <td>${course.student_count}</td>
                    </tr>`;
                });
                html += '</tbody></table>';
                coursesList.innerHTML = html;
            } else {
                coursesList.innerHTML = '<p>Chưa có khóa học nào</p>';
            }
        }
    } catch (error) {
        document.getElementById('coursesList').innerHTML = `<div class="alert alert-error">${error.message}</div>`;
        showAlert('Không thể tải dữ liệu dashboard', 'error');
    }
});
</script>
@endsection

