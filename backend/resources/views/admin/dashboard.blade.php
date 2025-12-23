@extends('admin.layout')

@section('title', 'Dashboard')

@section('description', 'Tổng quan hệ thống')

@section('content')

<div class="h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 3xl:gap-4">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số khóa học</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M384 512L96 512c-53 0-96-43-96-96L0 96C0 43 43 0 96 0L400 0c26.5 0 48 21.5 48 48l0 288c0 20.9-13.4 38.7-32 45.3l0 66.7c17.7 0 32 14.3 32 32s-14.3 32-32 32l-32 0zM96 384c-17.7 0-32 14.3-32 32s14.3 32 32 32l256 0 0-64-256 0zm32-232c0 13.3 10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0c-13.3 0-24 10.7-24 24zm24 72c-13.3 0-24 10.7-24 24s10.7 24 24 24l176 0c13.3 0 24-10.7 24-24s-10.7-24-24-24l-176 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalCourses">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số sinh viên</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 640 640" fill="currentColor">
                        <path d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalStudents">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm   font-medium text-gray-600 dark:text-gray-400">Tổng số bài học</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM188.3 147.1c-7.6 4.2-12.3 12.3-12.3 20.9l0 176c0 8.7 4.7 16.7 12.3 20.9s16.8 4.1 24.3-.5l144-88c7.1-4.4 11.5-12.1 11.5-20.5s-4.4-16.1-11.5-20.5l-144-88c-7.4-4.5-16.7-4.7-24.3-.5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalLessons">-</div>
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

