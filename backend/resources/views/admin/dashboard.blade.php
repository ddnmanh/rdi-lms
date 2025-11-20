@extends('admin.layout')

@section('title', 'Dashboard')

@section('description', 'Tổng quan hệ thống')

@section('content') 

<div class="h-full flex flex-col items-stretch justify-start gap-2.5 2xl:gap-4">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 2xl:gap-4">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số khóa học</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-book text-white text-sm  "></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalCourses">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số sinh viên</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm  "></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalStudents">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số bài học</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-play-circle text-white text-sm  "></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalLessons">-</div>
        </div>
    </div>


</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Load overview stats
        const overviewData = await apiRequest('/reports/overview');

        if (overviewData.success) {
            document.getElementById('totalCourses').textContent = overviewData.data.total_courses || 0;
            document.getElementById('totalStudents').textContent = overviewData.data.total_students || 0;
            document.getElementById('totalLessons').textContent = overviewData.data.total_lessons || 0;

        }
    } catch (error) {
        document.getElementById('coursesList').innerHTML = `
            <div class="flex items-center justify-center pt-40">
                <div class="text-center">
                    <p class="text-red-600 dark:text-red-400">${error.message}</p>
                </div>
            </div>
        `;
        console.error('Dashboard error:', error);
    }
});
</script>
@endsection

