@extends('admin.layout')

@section('title', 'Dashboard')

@section('description', 'Tổng quan hệ thống')

@section('content')
<style>
    /* Custom scrollbar overlay - đè lên header, không chiếm chỗ */
    .table-scroll-container {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }

    /* Webkit browsers (Chrome, Safari, Edge) */
    .table-scroll-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-scroll-container::-webkit-scrollbar-track {
        background: transparent;
    }

    .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 4px;
        border: 2px solid transparent;
        background-clip: padding-box;
    }

    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }

    /* Dark mode */
    .dark .table-scroll-container {
        scrollbar-color: rgba(75, 85, 99, 0.5) transparent;
    }

    .dark .table-scroll-container::-webkit-scrollbar-thumb {
        background-color: rgba(75, 85, 99, 0.5);
    }

    .dark .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background-color: rgba(75, 85, 99, 0.7);
    }
</style>

<div class="h-full flex flex-col items-stretch justify-start gap-2.5 2xl:gap-4">
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 2xl:gap-4">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm 2xl:text-base font-medium text-gray-600 dark:text-gray-400">Tổng số khóa học</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                    <i class="fas fa-book text-white text-sm 2xl:text-base"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalCourses">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm 2xl:text-base font-medium text-gray-600 dark:text-gray-400">Tổng số sinh viên</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center">
                    <i class="fas fa-users text-white text-sm 2xl:text-base"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalStudents">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 2xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm 2xl:text-base font-medium text-gray-600 dark:text-gray-400">Tổng số bài học</h4>
                <div class="h-10 w-10 2xl:h-12 2xl:w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                    <i class="fas fa-play-circle text-white text-sm 2xl:text-base"></i>
                </div>
            </div>
            <div class="text-2xl 2xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalLessons">-</div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="flex-1 flex flex-col items-stretch justify-start bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-4 2xl:px-6 py-3 2xl:py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg 2xl:text-xl font-semibold text-gray-900 dark:text-gray-100">Danh sách khóa học</h3>
        </div>

        <div class="table-scroll-container w-full overflow-x-hidden h-full overflow-y-auto">
            <div id="coursesList">
                <div class="pt-40 flex flex-col items-center justify-center gap-2">
                    <div id="SPINNER_LOADING">
                        <div id="SPINNER_LOADING_LDS_ROLLER">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    <div class="text-sm text-[#9ca3af80] dark:text-[#9ca3af80]">Đang tải dữ liệu...</div>
                </div>
            </div>
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

            // Render courses list
            const coursesList = document.getElementById('coursesList');
            if (overviewData.data.courses && overviewData.data.courses.length > 0) {
                let html = `
                    <table class="w-full table-fixed border-separate border-spacing-0 text-sm">
                        <colgroup>
                            <col class="w-[10%]">
                            <col class="w-[60%]">
                            <col class="w-[30%]">
                        </colgroup>
                        <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">ID</th>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Tên khóa học</th>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Số sinh viên</th>
                            </tr>
                        </thead>
                        <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                `;
                
                overviewData.data.courses.forEach(course => {
                    html += `
                        <tr class="border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-center">
                                <span class="text-gray-600 dark:text-gray-300">${course.id}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                ${course.title || '-'}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                ${course.student_count || 0}
                            </td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table>';
                coursesList.innerHTML = html;
            } else {
                coursesList.innerHTML = `
                    <table class="w-full table-fixed border-separate border-spacing-0 text-sm">
                        <colgroup>
                            <col class="w-[10%]">
                            <col class="w-[60%]">
                            <col class="w-[30%]">
                        </colgroup>
                        <thead class="text-white dark:text-gray-200 [&>tr>th]:border-b [&>tr>th]:border-gray-200 dark:[&>tr>th]:border-gray-500 [&>tr>th:not(:first-child)]:border-l [&>tr>th:not(:first-child)]:border-gray-200 dark:[&>tr>th:not(:first-child)]:border-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">ID</th>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Tên khóa học</th>
                                <th class="px-4 py-3 text-left sticky top-0 z-20 bg-blue-600 dark:bg-gray-700 shadow-sm whitespace-normal break-words">Số sinh viên</th>
                            </tr>
                        </thead>
                        <tbody class="[&>tr:not(:first-child)>td]:border-t [&>tr:not(:first-child)>td]:border-gray-200 dark:[&>tr:not(:first-child)>td]:border-gray-700">
                            <tr>
                                <td colspan="3" class="pt-40 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center mb-4 shadow-lg">
                                            <i class="fas fa-inbox text-3xl text-gray-400 dark:text-gray-500"></i>
                                        </div>
                                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">Chưa có khóa học nào</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Hệ thống chưa có khóa học nào được tạo</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                `;
            }
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

