@extends('admin.layout')

@section('title', 'Dashboard')

@section('description', 'Tổng quan hệ thống')

@section('content')

<div class="w-full h-full flex flex-col items-stretch justify-start gap-2.5 3xl:gap-4 overflow-y-auto">

    <div class="grid grid-cols-6 gap-3 3xl:gap-4">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng số khóa học</h4>
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
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng số sinh viên</h4>
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
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng số bài học</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zM188.3 147.1c-7.6 4.2-12.3 12.3-12.3 20.9l0 176c0 8.7 4.7 16.7 12.3 20.9s16.8 4.1 24.3-.5l144-88c7.1-4.4 11.5-12.1 11.5-20.5s-4.4-16.1-11.5-20.5l-144-88c-7.4-4.5-16.7-4.7-24.3-.5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalLessons">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng số Quiz</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 280.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.8 7.6-7.9 7.6-13.5c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.6 12.5-18.2 19-30.7 14.4s-19-18.2-14.4-30.7l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalQuizzes">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Tổng thời lượng</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalDuration">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Lượt xem bài học</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M288 80c-65.2 0-118.8 29.6-159.9 67.7C89.6 183.5 63 226 49.4 256c13.6 30 40.2 72.5 78.6 108.3C169.2 402.4 222.8 432 288 432s118.8-29.6 159.9-67.7C486.4 328.5 513 286 526.6 256c-13.6-30-40.2-72.5-78.6-108.3C406.8 109.6 353.2 80 288 80zM95.4 112.6C142.5 68.8 207.2 32 288 32s145.5 36.8 192.6 80.6c12.3 11.8 12.3 31.2 0 43C433.5 200.4 368.8 237.2 288 237.2s-145.5-36.8-192.6-80.6c-12.3-11.8-12.3-31.2 0-43zM288 400c-80.8 0-145.5-36.8-192.6-80.6c-12.3-11.8-12.3-31.2 0-43C142.5 231.6 207.2 195 288 195s145.5 36.8 192.6 80.6c12.3 11.8 12.3 31.2 0 43C433.5 363.2 368.8 400 288 400z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalLessonViews">-</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="lessonsWatchedText">-</div>
        </div>
    </div>

    <div class="grid grid-cols-6 gap-3 3xl:gap-4">
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Lượt làm Quiz</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor">
                        <path d="M64 32C28.7 32 0 60.7 0 96L0 416c0 35.3 28.7 64 64 64l320 0c35.3 0 64-28.7 64-64l0-320c0-35.3-28.7-64-64-64L64 32zM200 344l-144 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l144 0c13.3 0 24 10.7 24 24s-10.7 24-24 24zm112-104c0 13.3-10.7 24-24 24L64 264c-13.3 0-24-10.7-24-24s10.7-24 24-24l224 0c13.3 0 24 10.7 24 24zm96-112c0 13.3-10.7 24-24 24l-192 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l192 0c13.3 0 24 10.7 24 24z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalQuizAttempts">-</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Điểm TB Quiz</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-yellow-500 to-yellow-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.7c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.6 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.7L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7 276.5 381.2c7-3.5 15.1-3.5 22 0l105.1 62.5-20.3-119.6c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1-118.3-17.5c-7.9-1.2-14.6-6.1-18.1-13.3L287.9 79z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="avgQuizScore">-</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">%</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">SV hoàn thành</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M256 512a256 256 0 1 0 0-512 256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalCompletedStudents">-</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hoàn thành &gt;80%</div>
        </div>
        <div class="bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 3xl:p-6">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">Thời gian đã học</h4>
                <div class="h-10 w-10 3xl:h-12 3xl:w-12 rounded-xl bg-gradient-to-br from-cyan-500 to-cyan-600 flex items-center justify-center text-white">
                    <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor">
                        <path d="M464 256A208 208 0 1 1 48 256a208 208 0 1 1 416 0zM0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM232 120l0 136c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2 280 120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl 3xl:text-3xl font-bold text-gray-900 dark:text-gray-100" id="totalWatchedDuration">-</div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="space-y-4 3xl:space-y-6">
        {{-- Top khóa học --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 3xl:gap-6">
            {{-- Biểu đồ: Top khóa học có nhiều học viên nhất --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Top khóa học</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Theo số học viên</span>
                </div>
                <div id="chartTopCourses" class="w-full min-h-[350px] h-[350px]"></div>
            </div>

            {{-- Biểu đồ: Thống kê bài học theo khóa học --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Số bài học theo khóa học</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Tổng số bài học</span>
                </div>
                <div id="chartLessonsByCourse" class="w-full min-h-[400px] h-[400px]"></div>
            </div>
        </div>

        {{-- Biểu đồ: Phân bổ số học viên theo khóa học --}}
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Phân bổ học viên</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Theo khóa học</span>
            </div>
            <div id="chartCourseDistribution" class="w-full min-h-[350px] h-[350px]"></div>
        </div>

        {{-- Biểu đồ: Thống kê hoạt động học tập --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 3xl:gap-6">
            {{-- Biểu đồ: Tỷ lệ hoàn thành khóa học --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tỷ lệ hoàn thành</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Sinh viên</span>
                </div>
                <div id="chartCompletionRate" class="w-full min-h-[350px] h-[350px]"></div>
            </div>

            {{-- Biểu đồ: Thống kê Quiz --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Thống kê Quiz</h3>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Điểm trung bình</span>
                </div>
                <div id="chartQuizStats" class="w-full min-h-[350px] h-[350px]"></div>
            </div>
        </div>

        <div class="w-full min-w-0 overflow-hidden">
            {{-- Biểu đồ: Timeline khóa học --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm overflow-hidden w-full">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Timeline khóa học</h3>
                    <div class="flex items-center gap-3 flex-wrap">
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                            <button id="timelineZoomOut" class="px-2 py-1 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors" title="Thu nhỏ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span id="timelineZoomLevel" class="px-2 text-xs font-medium text-gray-500 dark:text-gray-400 min-w-[50px] text-center">100%</span>
                            <button id="timelineZoomIn" class="px-2 py-1 text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors" title="Phóng to">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                            <button id="timelineZoomReset" class="px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors" title="Đặt lại">
                                Fit
                            </button>
                            <div class="w-px h-4 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                            <button id="timelineScrollToToday" class="px-2 py-1 text-xs font-medium text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30 rounded transition-colors flex items-center gap-1" title="Cuộn đến Hôm nay">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
                                Hôm nay
                            </button>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Thời gian bắt đầu và kết thúc</span>
                    </div>
                </div>
                
                {{-- Container chính với 2 phần: tên khóa học cố định + timeline cuộn --}}
                <div id="chartCourseTimeline" class="relative min-h-[500px] w-full overflow-hidden flex">
                    {{-- Cột tên khóa học cố định bên trái --}}
                    <div id="timelineNamesColumn" class="flex-shrink-0 bg-white dark:bg-gray-800 z-10 border-r border-gray-200 dark:border-gray-700" style="width: 200px;">
                        <div id="timelineNamesContainer" class="relative">
                            {{-- Tên khóa học sẽ được render ở đây bằng JavaScript --}}
                        </div>
                    </div>
                    
                    {{-- Phần timeline có thể cuộn ngang --}}
                    <div id="timelineScrollContainer" class="flex-1 min-w-0 overflow-x-auto overflow-y-hidden">
                        <div id="timelineCanvasContainer" class="relative">
                            <canvas id="timelineCanvas"></canvas>
                        </div>
                    </div>
                    
                    {{-- Tooltip --}}
                    <div id="timelineTooltip" class="fixed hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg p-3 z-50 pointer-events-none" style="min-width: 200px;"></div>
                </div>
                
                {{-- Chỉ báo cuộn --}}
                <div id="timelineScrollIndicator" class="hidden mt-2 text-center">
                    <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Kéo để xem thêm
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>
let chartTopCourses = null;
let chartCourseDistribution = null;
let chartLessonsByCourse = null;
let chartCompletionRate = null;
let chartQuizStats = null;
let timelineCanvas = null;
let timelineCtx = null;
let timelineData = null;
let timelineDateRange = null; // Lưu trữ thông tin date range
let timelineZoomLevel = 1.0; // Mức zoom mặc định (1 = 100%)
let timelineMinWidth = 0; // Chiều rộng tối thiểu dựa trên dữ liệu
let chartsInitialized = false;

// Format thời lượng từ giây sang giờ/phút
function formatDuration(seconds) {
    if (!seconds || seconds === 0) return '0 phút';

    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);

    if (hours > 0) {
        return `${hours} giờ ${minutes} phút`;
    }
    return `${minutes} phút`;
}

document.addEventListener('DOMContentLoaded', async function() {
    try {
        // Load overview stats
        const overviewData = await apiRequest('/reports/overview');

        if (overviewData.success) {
            // Update stats cards
            document.getElementById('totalCourses').textContent = overviewData.data.total_courses || 0;
            document.getElementById('totalStudents').textContent = overviewData.data.total_students || 0;
            document.getElementById('totalLessons').textContent = overviewData.data.total_lessons || 0;
            document.getElementById('totalQuizzes').textContent = overviewData.data.total_quizzes || 0;
            document.getElementById('totalDuration').textContent = formatDuration(overviewData.data.total_duration || 0);
            document.getElementById('totalLessonViews').textContent = (overviewData.data.total_lesson_views || 0).toLocaleString('vi-VN');
            document.getElementById('lessonsWatchedText').textContent = `${overviewData.data.lessons_watched || 0} bài học đã được xem`;
            document.getElementById('totalQuizAttempts').textContent = (overviewData.data.total_quiz_attempts || 0).toLocaleString('vi-VN');
            document.getElementById('avgQuizScore').textContent = (overviewData.data.avg_quiz_score || 0).toFixed(1);
            document.getElementById('totalCompletedStudents').textContent = overviewData.data.total_completed_students || 0;
            document.getElementById('totalWatchedDuration').textContent = formatDuration(overviewData.data.total_watched_duration || 0);

            // Render charts
            renderCharts(overviewData.data);
        }
    } catch (error) {
        console.error('Dashboard error:', error);
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'flex items-center justify-center pt-40';
        errorDiv.innerHTML = `
                <div class="text-center">
                    <p class="text-red-600 dark:text-red-400">${error.message}</p>
            </div>
        `;
        document.querySelector('.h-full').appendChild(errorDiv);
    }
});

function renderCharts(data) {
    if (chartsInitialized) {
        // Resize existing charts
        chartTopCourses?.resize();
        chartCourseDistribution?.resize();
        chartLessonsByCourse?.resize();
        chartCompletionRate?.resize();
        chartQuizStats?.resize();
        // Vẽ lại timeline với kích thước mới
        if (timelineData && timelineData.length > 0 && timelineCanvas) {
            const container = document.getElementById('chartCourseTimeline');
            if (container) {
                const paddingTop = 40;
                const paddingBottom = 60;
                const rowHeight = 40;
                const spacing = 8;
                const containerHeight = container.offsetHeight;
                const chartHeight = Math.max(containerHeight - paddingTop - paddingBottom, timelineData.length * (rowHeight + spacing));
                timelineCanvas.width = container.offsetWidth;
                timelineCanvas.height = paddingTop + chartHeight + paddingBottom;
                drawTimeline();
            }
        }
        return;
    }

    renderTopCoursesChart(data.courses || []);
    renderCourseDistributionChart(data.courses || []);
    renderLessonsByCourseChart(data.courses || []);
    renderCompletionRateChart(data);
    renderQuizStatsChart(data);
    renderCourseTimelineChart(data.courses || []);

    chartsInitialized = true;

    // Responsive charts khi resize window
    window.addEventListener('resize', () => {
        chartTopCourses?.resize();
        chartCourseDistribution?.resize();
        chartLessonsByCourse?.resize();
        chartCompletionRate?.resize();
        chartQuizStats?.resize();
        // Vẽ lại timeline với kích thước mới (hỗ trợ cuộn ngang và HiDPI)
        if (timelineData && timelineData.length > 0 && timelineCanvas && timelineCtx && timelineDateRange) {
            // Cập nhật lại timelineMinWidth dựa trên container mới
            const scrollContainer = document.getElementById('timelineScrollContainer');
            if (scrollContainer) {
                const containerVisibleWidth = scrollContainer.offsetWidth;
                const totalMonths = Math.ceil(timelineDateRange.totalTimeRange / (30 * 24 * 60 * 60 * 1000));
                const pixelsPerMonth = 120;
                const paddingRight = 80;
                const minChartWidth = totalMonths * pixelsPerMonth + paddingRight;
                timelineMinWidth = Math.max(containerVisibleWidth, minChartWidth);
            }
            redrawTimelineWithZoom();
        }
    });
}

// Biểu đồ: Top khóa học có nhiều học viên nhất
function renderTopCoursesChart(courses) {
    const chartDom = document.getElementById('chartTopCourses');
    if (!chartDom) return;

    chartTopCourses = echarts.init(chartDom);

    // Sắp xếp và lấy top 10
    const topCourses = [...courses]
        .sort((a, b) => (b.student_count || 0) - (a.student_count || 0))
        .slice(0, 10);

    const xAxisData = topCourses.map(course => {
        const title = course.title || 'Không có tiêu đề';
        return title.length > 20 ? title.substring(0, 20) + '...' : title;
    });
    const seriesData = topCourses.map(course => course.student_count || 0);

    const option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const param = params[0];
                const course = topCourses[param.dataIndex];
                return `${course.title || 'N/A'}<br/>${param.marker}Số học viên: <b>${param.value}</b>`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            top: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: xAxisData,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Số học viên',
            minInterval: 1
        },
        series: [{
            name: 'Số học viên',
            type: 'bar',
            data: seriesData,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#3b82f6' },
                    { offset: 1, color: '#60a5fa' }
                ])
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#2563eb' },
                        { offset: 1, color: '#3b82f6' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: '{c}'
            }
        }]
    };

    chartTopCourses.setOption(option);
}

// Biểu đồ: Phân bổ số học viên theo khóa học (Pie chart)
function renderCourseDistributionChart(courses) {
    const chartDom = document.getElementById('chartCourseDistribution');
    if (!chartDom) return;

    chartCourseDistribution = echarts.init(chartDom);

    // Lọc các khóa học có học viên
    const coursesWithStudents = courses.filter(c => (c.student_count || 0) > 0);

    if (coursesWithStudents.length === 0) {
        chartCourseDistribution.setOption({
            title: {
                text: 'Chưa có dữ liệu',
                left: 'center',
                top: 'center',
                textStyle: {
                    color: '#999',
                    fontSize: 14
                }
            }
        });
        return;
    }

    const data = coursesWithStudents.map(course => ({
        value: course.student_count || 0,
        name: course.title || 'Không có tiêu đề'
    }));

    // Tạo màu sắc gradient
    const colors = [
        '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981',
        '#06b6d4', '#6366f1', '#f43f5e', '#14b8a6', '#a855f7'
    ];

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return `${params.name}<br/>${params.marker}Số học viên: <b>${params.value}</b> (${params.percent}%)`;
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            top: 'center',
            formatter: function(name) {
                return name.length > 20 ? name.substring(0, 20) + '...' : name;
            }
        },
        series: [{
            name: 'Học viên',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['60%', '50%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: true,
                formatter: '{b}: {c}'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 16,
                    fontWeight: 'bold'
                }
            },
            data: data.map((item, index) => ({
                ...item,
                itemStyle: {
                    color: colors[index % colors.length]
                }
            }))
        }]
    };

    chartCourseDistribution.setOption(option);
}

// Biểu đồ: Số bài học theo khóa học
function renderLessonsByCourseChart(courses) {
    const chartDom = document.getElementById('chartLessonsByCourse');
    if (!chartDom) return;

    chartLessonsByCourse = echarts.init(chartDom);

    // Sắp xếp theo số bài học giảm dần
    const sortedCourses = [...courses]
        .filter(c => (c.lesson_count || 0) > 0)
        .sort((a, b) => (b.lesson_count || 0) - (a.lesson_count || 0));

    if (sortedCourses.length === 0) {
        chartLessonsByCourse.setOption({
            title: {
                text: 'Chưa có dữ liệu',
                left: 'center',
                top: 'center',
                textStyle: {
                    color: '#999',
                    fontSize: 14
                }
            }
        });
        return;
    }

    const xAxisData = sortedCourses.map(course => {
        const title = course.title || 'Không có tiêu đề';
        return title.length > 25 ? title.substring(0, 25) + '...' : title;
    });
    const seriesData = sortedCourses.map(course => course.lesson_count || 0);

    const option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            },
            formatter: function(params) {
                const param = params[0];
                const course = sortedCourses[param.dataIndex];
                return `${course.title || 'N/A'}<br/>${param.marker}Số bài học: <b>${param.value}</b>`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '15%',
            top: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: xAxisData,
            axisLabel: {
                rotate: 45,
                interval: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Số bài học',
            minInterval: 1
        },
        series: [{
            name: 'Số bài học',
            type: 'bar',
            data: seriesData,
            itemStyle: {
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#8b5cf6' },
                    { offset: 1, color: '#a78bfa' }
                ])
            },
            emphasis: {
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#7c3aed' },
                        { offset: 1, color: '#8b5cf6' }
                    ])
                }
            },
            label: {
                show: true,
                position: 'top',
                formatter: '{c}'
            }
        }]
    };

    chartLessonsByCourse.setOption(option);
}

// Biểu đồ: Tỷ lệ hoàn thành khóa học
function renderCompletionRateChart(data) {
    const chartDom = document.getElementById('chartCompletionRate');
    if (!chartDom) return;

    chartCompletionRate = echarts.init(chartDom);

    const totalStudents = data.total_students || 0;
    const completedStudents = data.total_completed_students || 0;
    const notCompletedStudents = totalStudents - completedStudents;

    if (totalStudents === 0) {
        chartCompletionRate.setOption({
            title: {
                text: 'Chưa có dữ liệu',
                left: 'center',
                top: 'center',
                textStyle: {
                    color: '#999',
                    fontSize: 14
                }
            }
        });
        return;
    }

    const option = {
        tooltip: {
            trigger: 'item',
            formatter: function(params) {
                return `${params.name}<br/>${params.marker}Số lượng: <b>${params.value}</b> (${params.percent}%)`;
            }
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            top: 'center'
        },
        series: [{
            name: 'Tỷ lệ hoàn thành',
            type: 'pie',
            radius: ['40%', '70%'],
            center: ['60%', '50%'],
            avoidLabelOverlap: false,
            itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
            },
            label: {
                show: true,
                formatter: '{b}: {c} ({d}%)'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: 16,
                    fontWeight: 'bold'
                }
            },
            data: [
                {
                    value: completedStudents,
                    name: 'Đã hoàn thành',
                    itemStyle: { color: '#10b981' }
                },
                {
                    value: notCompletedStudents,
                    name: 'Chưa hoàn thành',
                    itemStyle: { color: '#ef4444' }
                }
            ]
        }]
    };

    chartCompletionRate.setOption(option);
}

// Biểu đồ: Thống kê Quiz
function renderQuizStatsChart(data) {
    const chartDom = document.getElementById('chartQuizStats');
    if (!chartDom) return;

    chartQuizStats = echarts.init(chartDom);

    const totalQuizzes = data.total_quizzes || 0;
    const totalAttempts = data.total_quiz_attempts || 0;
    const avgScore = data.avg_quiz_score || 0;

    if (totalQuizzes === 0 && totalAttempts === 0) {
        chartQuizStats.setOption({
            title: {
                text: 'Chưa có dữ liệu',
                left: 'center',
                top: 'center',
                textStyle: {
                    color: '#999',
                    fontSize: 14
                }
            }
        });
        return;
    }

    // Tính toán các chỉ số
    const avgAttemptsPerQuiz = totalQuizzes > 0 ? (totalAttempts / totalQuizzes).toFixed(1) : 0;

    const option = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            top: '10%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: ['Tổng Quiz', 'Lượt làm', 'Điểm TB', 'TB lượt/Quiz'],
            axisLabel: {
                rotate: 0
            }
        },
        yAxis: {
            type: 'value',
            name: 'Giá trị'
        },
        series: [{
            name: 'Thống kê',
            type: 'bar',
            data: [
                {
                    value: totalQuizzes,
                    itemStyle: { color: '#f59e0b' }
                },
                {
                    value: totalAttempts,
                    itemStyle: { color: '#3b82f6' }
                },
                {
                    value: avgScore,
                    itemStyle: { color: '#10b981' }
                },
                {
                    value: parseFloat(avgAttemptsPerQuiz),
                    itemStyle: { color: '#8b5cf6' }
                }
            ],
            label: {
                show: true,
                position: 'top',
                formatter: function(params) {
                    if (params.dataIndex === 2) {
                        return params.value.toFixed(1) + '%';
                    }
                    if (params.dataIndex === 3) {
                        return params.value.toFixed(1);
                    }
                    return params.value;
                }
            }
        }]
    };

    chartQuizStats.setOption(option);
}

// Biểu đồ: Timeline khóa học (Gantt Chart) - Code thuần JavaScript
// Cột tên cố định + Timeline cuộn ngang + HiDPI support
function renderCourseTimelineChart(courses) {
    const mainContainer = document.getElementById('chartCourseTimeline');
    const namesContainer = document.getElementById('timelineNamesContainer');
    const scrollContainer = document.getElementById('timelineScrollContainer');
    const canvasContainer = document.getElementById('timelineCanvasContainer');
    
    if (!mainContainer || !namesContainer || !scrollContainer || !canvasContainer) return;

    timelineCanvas = document.getElementById('timelineCanvas');
    if (!timelineCanvas) return;

    timelineCtx = timelineCanvas.getContext('2d');

    // Lọc các khóa học có start_date và end_date
    const coursesWithDates = courses.filter(c => c.start_date && c.end_date);

    // Cấu hình layout
    const paddingTop = 40;
    const paddingBottom = 60;
    const rowHeight = 40;
    const barHeight = 24;
    const spacing = 8;
    const paddingRight = 80; // Khoảng trống bên phải cho label số ngày

    if (coursesWithDates.length === 0) {
        namesContainer.innerHTML = '';
        const displayWidth = scrollContainer.offsetWidth;
        const displayHeight = 500;
        canvasContainer.style.width = displayWidth + 'px';
        setupCanvasHiDPI(displayWidth, displayHeight);
        const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
        timelineCtx.fillStyle = isDarkMode ? '#1f2937' : '#ffffff';
        timelineCtx.fillRect(0, 0, displayWidth, displayHeight);
        timelineCtx.fillStyle = isDarkMode ? '#9ca3af' : '#6b7280';
        timelineCtx.font = 'bold 14px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        timelineCtx.textAlign = 'center';
        timelineCtx.textBaseline = 'middle';
        timelineCtx.fillText(
            'Chưa có khóa học nào có thời gian bắt đầu và kết thúc',
            displayWidth / 2,
            displayHeight / 2
        );
        return;
    }

    // Sắp xếp theo start_date
    coursesWithDates.sort((a, b) => {
        return new Date(a.start_date) - new Date(b.start_date);
    });

    // Tìm min và max date
    const allDates = coursesWithDates.flatMap(c => [new Date(c.start_date), new Date(c.end_date)]);
    const minDate = new Date(Math.min(...allDates.map(d => d.getTime())));
    const maxDate = new Date(Math.max(...allDates.map(d => d.getTime())));

    // Thêm padding nhỏ cho date range (để thanh không dính sát lề)
    const dateRange = maxDate.getTime() - minDate.getTime();
    const datePadding = Math.max(dateRange * 0.01, 43200000); // 1% hoặc tối thiểu 12 giờ
    const paddedMinDate = new Date(minDate.getTime() - datePadding);
    const paddedMaxDate = new Date(maxDate.getTime() + datePadding);

    const now = new Date();
    const totalTimeRange = paddedMaxDate.getTime() - paddedMinDate.getTime();

    // Lưu trữ date range để sử dụng trong drawTimeline
    timelineDateRange = {
        paddedMinDate: paddedMinDate,
        paddedMaxDate: paddedMaxDate,
        totalTimeRange: totalTimeRange,
        now: now
    };

    // Tính số tháng trong khoảng thời gian
    const totalMonths = Math.ceil(totalTimeRange / (30 * 24 * 60 * 60 * 1000));
    const pixelsPerMonth = 120; // 120px cho mỗi tháng
    
    // Tính chiều rộng tối thiểu dựa trên số tháng (căn sát lề trái)
    const minChartWidth = totalMonths * pixelsPerMonth + paddingRight;
    const containerVisibleWidth = scrollContainer.offsetWidth;
    
    // Chiều rộng canvas = max(container width, calculated width based on time range) * zoom
    timelineMinWidth = Math.max(containerVisibleWidth, minChartWidth);
    const canvasWidth = Math.round(timelineMinWidth * timelineZoomLevel);
    
    // Đặt chiều rộng cho canvas container
    canvasContainer.style.width = canvasWidth + 'px';

    // Tính chiều cao
    const chartHeight = coursesWithDates.length * (rowHeight + spacing);
    const canvasHeight = paddingTop + chartHeight + paddingBottom;

    // Setup canvas với HiDPI support
    setupCanvasHiDPI(canvasWidth, canvasHeight);

    // Render tên khóa học bằng HTML (cố định bên trái)
    renderCourseNames(coursesWithDates, paddingTop, rowHeight, spacing);

    // Hiển thị chỉ báo cuộn nếu có thể cuộn
    const scrollIndicator = document.getElementById('timelineScrollIndicator');
    if (scrollIndicator) {
        if (canvasWidth > containerVisibleWidth) {
            scrollIndicator.classList.remove('hidden');
        } else {
            scrollIndicator.classList.add('hidden');
        }
    }

    // Cập nhật zoom level display
    updateZoomLevelDisplay();

    // Lưu trữ dữ liệu gốc
    timelineData = coursesWithDates.map((course, index) => {
        const startDate = new Date(course.start_date);
        const endDate = new Date(course.end_date);
        const startTime = startDate.getTime();
        const endTime = endDate.getTime();

        // Xác định màu sắc
        let color = '#3b82f6'; // Sắp diễn ra
        let statusText = 'Sắp diễn ra';
        if (now >= startDate && now <= endDate) {
            color = '#10b981'; // Đang diễn ra
            statusText = 'Đang diễn ra';
        } else if (now > endDate) {
            color = '#6b7280'; // Đã kết thúc
            statusText = 'Đã kết thúc';
        }

        const days = Math.ceil((endTime - startTime) / (1000 * 60 * 60 * 24));

        return {
            course: course,
            startDate: startDate,
            endDate: endDate,
            days: days,
            color: color,
            statusText: statusText,
            rowIndex: index,
            startTime: startTime,
            endTime: endTime
        };
    });

    // Vẽ biểu đồ
    drawTimeline();

    // Thêm event listeners (chỉ thêm 1 lần)
    if (!timelineCanvas.hasAttribute('data-listeners-added')) {
        timelineCanvas.addEventListener('mousemove', handleTimelineMouseMove);
        timelineCanvas.addEventListener('mouseleave', handleTimelineMouseLeave);
        timelineCanvas.addEventListener('click', handleTimelineClick);
        timelineCanvas.setAttribute('data-listeners-added', 'true');
    }

    // Setup zoom controls (chỉ 1 lần)
    setupTimelineZoomControls();

    // Tự động cuộn đến mốc "Hôm nay" sau khi render
    setTimeout(() => {
        scrollToToday();
    }, 100);
}

// Tự động cuộn timeline đến vị trí "Hôm nay"
function scrollToToday() {
    const scrollContainer = document.getElementById('timelineScrollContainer');
    const canvasContainer = document.getElementById('timelineCanvasContainer');
    if (!scrollContainer || !canvasContainer || !timelineDateRange) return;

    const { paddedMinDate, totalTimeRange, now } = timelineDateRange;
    const canvasWidth = canvasContainer.offsetWidth;
    const containerWidth = scrollContainer.offsetWidth;
    
    const paddingLeft = 10;
    const paddingRight = 80;
    const chartWidth = canvasWidth - paddingLeft - paddingRight;
    
    // Tính vị trí X của "Hôm nay" trên canvas
    const todayX = paddingLeft + ((now.getTime() - paddedMinDate.getTime()) / totalTimeRange) * chartWidth;
    
    // Kiểm tra xem "Hôm nay" có nằm trong timeline không
    if (todayX >= paddingLeft && todayX <= paddingLeft + chartWidth) {
        // Cuộn sao cho "Hôm nay" nằm ở giữa màn hình (hoặc 1/3 từ trái)
        const targetScrollLeft = Math.max(0, todayX - (containerWidth / 3));
        
        // Cuộn mượt đến vị trí
        scrollContainer.scrollTo({
            left: targetScrollLeft,
            behavior: 'smooth'
        });
    }
}

// Render tên khóa học bằng HTML (cố định bên trái)
function renderCourseNames(courses, paddingTop, rowHeight, spacing) {
    const namesContainer = document.getElementById('timelineNamesContainer');
    if (!namesContainer) return;

    const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    let html = `<div style="height: ${paddingTop}px;"></div>`; // Padding top để align với canvas
    
    courses.forEach((course, index) => {
        const title = course.title || 'Không có tiêu đề';
        const displayTitle = title.length > 22 ? title.substring(0, 22) + '...' : title;
        
        html += `
            <div class="flex items-center justify-end pr-3" style="height: ${rowHeight}px; margin-bottom: ${spacing}px;" title="${title}">
                <span class="text-sm font-semibold ${isDarkMode ? 'text-gray-100' : 'text-gray-900'} truncate text-right">
                    ${displayTitle}
                </span>
            </div>
        `;
    });
    
    namesContainer.innerHTML = html;
}

// Cập nhật hiển thị zoom level
function updateZoomLevelDisplay() {
    const zoomLevelEl = document.getElementById('timelineZoomLevel');
    if (zoomLevelEl) {
        zoomLevelEl.textContent = Math.round(timelineZoomLevel * 100) + '%';
    }
}

// Setup zoom controls
function setupTimelineZoomControls() {
    const zoomInBtn = document.getElementById('timelineZoomIn');
    const zoomOutBtn = document.getElementById('timelineZoomOut');
    const zoomResetBtn = document.getElementById('timelineZoomReset');

    if (zoomInBtn && !zoomInBtn.hasAttribute('data-listener-added')) {
        zoomInBtn.addEventListener('click', () => {
            if (timelineZoomLevel < 3.0) {
                timelineZoomLevel = Math.min(3.0, timelineZoomLevel + 0.25);
                redrawTimelineWithZoom();
            }
        });
        zoomInBtn.setAttribute('data-listener-added', 'true');
    }

    if (zoomOutBtn && !zoomOutBtn.hasAttribute('data-listener-added')) {
        zoomOutBtn.addEventListener('click', () => {
            if (timelineZoomLevel > 0.5) {
                timelineZoomLevel = Math.max(0.5, timelineZoomLevel - 0.25);
                redrawTimelineWithZoom();
            }
        });
        zoomOutBtn.setAttribute('data-listener-added', 'true');
    }

    if (zoomResetBtn && !zoomResetBtn.hasAttribute('data-listener-added')) {
        zoomResetBtn.addEventListener('click', () => {
            timelineZoomLevel = 1.0;
            redrawTimelineWithZoom();
            // Cuộn về đầu
            const scrollContainer = document.getElementById('timelineScrollContainer');
            if (scrollContainer) scrollContainer.scrollLeft = 0;
        });
        zoomResetBtn.setAttribute('data-listener-added', 'true');
    }

    // Nút cuộn đến "Hôm nay"
    const scrollTodayBtn = document.getElementById('timelineScrollToToday');
    if (scrollTodayBtn && !scrollTodayBtn.hasAttribute('data-listener-added')) {
        scrollTodayBtn.addEventListener('click', () => {
            scrollToToday();
        });
        scrollTodayBtn.setAttribute('data-listener-added', 'true');
    }
}

// Vẽ lại timeline với zoom mới
function redrawTimelineWithZoom() {
    const scrollContainer = document.getElementById('timelineScrollContainer');
    const canvasContainer = document.getElementById('timelineCanvasContainer');
    if (!canvasContainer || !scrollContainer || !timelineData) return;

    const paddingTop = 40;
    const paddingBottom = 60;
    const rowHeight = 40;
    const spacing = 8;

    // Tính chiều rộng mới
    const canvasWidth = Math.round(timelineMinWidth * timelineZoomLevel);
    canvasContainer.style.width = canvasWidth + 'px';

    // Tính toán kích thước
    const chartHeight = timelineData.length * (rowHeight + spacing);
    const canvasHeight = paddingTop + chartHeight + paddingBottom;

    // Setup canvas với HiDPI support
    setupCanvasHiDPI(canvasWidth, canvasHeight);

    // Cập nhật chỉ báo cuộn
    const scrollIndicator = document.getElementById('timelineScrollIndicator');
    if (scrollIndicator) {
        if (canvasWidth > scrollContainer.offsetWidth) {
            scrollIndicator.classList.remove('hidden');
        } else {
            scrollIndicator.classList.add('hidden');
        }
    }

    // Cập nhật zoom level display
    updateZoomLevelDisplay();

    // Vẽ lại
    drawTimeline();
}

// Hàm thiết lập canvas cho màn hình HiDPI/Retina
function setupCanvasHiDPI(displayWidth, displayHeight) {
    const dpr = window.devicePixelRatio || 1;

    // Đặt kích thước thực của canvas (scaled)
    timelineCanvas.width = displayWidth * dpr;
    timelineCanvas.height = displayHeight * dpr;

    // Đặt kích thước hiển thị CSS
    timelineCanvas.style.width = displayWidth + 'px';
    timelineCanvas.style.height = displayHeight + 'px';

    // Scale context để vẽ ở đúng kích thước
    timelineCtx.scale(dpr, dpr);
}

function drawTimeline() {
    if (!timelineCtx || !timelineData || timelineData.length === 0 || !timelineDateRange) return;

    const canvasContainer = document.getElementById('timelineCanvasContainer');
    if (!canvasContainer) return;
    
    const canvasWidth = canvasContainer.offsetWidth;
    const paddingLeft = 10; // Căn sát lề trái
    const paddingRight = 80; // Khoảng trống cho label số ngày
    const paddingTop = 40;
    const paddingBottom = 60;
    const rowHeight = 40;
    const spacing = 8;
    const chartWidth = canvasWidth - paddingLeft - paddingRight;
    const chartHeight = timelineData.length * (rowHeight + spacing);
    const canvasDisplayHeight = paddingTop + chartHeight + paddingBottom;

    // Clear canvas
    timelineCtx.save();
    timelineCtx.setTransform(1, 0, 0, 1, 0, 0); // Reset transform
    timelineCtx.clearRect(0, 0, timelineCanvas.width, timelineCanvas.height);
    timelineCtx.restore();

    // Sử dụng date range đã lưu
    const { paddedMinDate, paddedMaxDate, totalTimeRange, now } = timelineDateRange;

    // Vẽ background
    const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    timelineCtx.fillStyle = isDarkMode ? '#1f2937' : '#ffffff';
    timelineCtx.fillRect(0, 0, canvasWidth, canvasDisplayHeight);

    // Vẽ grid lines (đường kẻ dọc)
    timelineCtx.strokeStyle = isDarkMode ? '#374151' : '#e5e7eb';
    timelineCtx.lineWidth = 1;
    timelineCtx.setLineDash([5, 5]);

    const numGridLines = Math.max(4, Math.floor(chartWidth / 100)); // Grid lines động theo chiều rộng
    for (let i = 0; i <= numGridLines; i++) {
        const x = paddingLeft + (i / numGridLines) * chartWidth;
        timelineCtx.beginPath();
        timelineCtx.moveTo(x, paddingTop);
        timelineCtx.lineTo(x, paddingTop + chartHeight);
        timelineCtx.stroke();
    }

    timelineCtx.setLineDash([]);

    // Font chất lượng cao cho văn bản
    const fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

    // Vẽ đường "Hôm nay"
    const todayX = paddingLeft + ((now.getTime() - paddedMinDate.getTime()) / totalTimeRange) * chartWidth;
    if (todayX >= paddingLeft && todayX <= paddingLeft + chartWidth) {
        timelineCtx.strokeStyle = '#ef4444';
        timelineCtx.lineWidth = 2;
        timelineCtx.setLineDash([5, 5]);
        timelineCtx.beginPath();
        timelineCtx.moveTo(todayX, paddingTop);
        timelineCtx.lineTo(todayX, paddingTop + chartHeight);
        timelineCtx.stroke();
        timelineCtx.setLineDash([]);

        // Vẽ label "Hôm nay" với nền
        timelineCtx.fillStyle = isDarkMode ? '#1f2937' : '#ffffff';
        timelineCtx.beginPath();
        timelineCtx.roundRect(todayX - 32, paddingTop - 27, 64, 22, 4);
        timelineCtx.fill();
        timelineCtx.strokeStyle = '#ef4444';
        timelineCtx.lineWidth = 1;
        timelineCtx.stroke();
        
        timelineCtx.fillStyle = '#ef4444';
        timelineCtx.font = `600 12px ${fontFamily}`;
        timelineCtx.textAlign = 'center';
        timelineCtx.textBaseline = 'middle';
        timelineCtx.fillText('Hôm nay', todayX, paddingTop - 16);
    }

    // Vẽ thanh timeline (không vẽ tên khóa học - đã render bằng HTML)
    const barHeight = 24;
    timelineData.forEach((data, index) => {
        const y = paddingTop + index * (rowHeight + spacing) + rowHeight / 2;

        // Tính toán vị trí (căn sát lề trái)
        const startX = paddingLeft + ((data.startTime - paddedMinDate.getTime()) / totalTimeRange) * chartWidth;
        const endX = paddingLeft + ((data.endTime - paddedMinDate.getTime()) / totalTimeRange) * chartWidth;
        const width = Math.max(4, endX - startX); // Tối thiểu 4px để dễ nhìn thấy

        // Vẽ thanh timeline với rounded corners
        const barY = y - barHeight / 2;
        timelineCtx.fillStyle = data.color;
        timelineCtx.beginPath();
        timelineCtx.roundRect(startX, barY, width, barHeight, 4);
        timelineCtx.fill();

        // Vẽ label số ngày (luôn hiển thị bên phải thanh)
        timelineCtx.fillStyle = isDarkMode ? '#e5e7eb' : '#374151';
        timelineCtx.font = `500 11px ${fontFamily}`;
        timelineCtx.textAlign = 'left';
        timelineCtx.textBaseline = 'middle';
        timelineCtx.fillText(`${data.days}d`, endX + 6, y);

        // Lưu lại vị trí để xử lý hover
        data.startX = startX;
        data.endX = endX;
        data.y = y;
        data.width = width;
        data.height = barHeight;
    });

    // Vẽ trục X (thời gian)
    timelineCtx.strokeStyle = isDarkMode ? '#6b7280' : '#9ca3af';
    timelineCtx.lineWidth = 1;
    timelineCtx.beginPath();
    timelineCtx.moveTo(paddingLeft, paddingTop + chartHeight);
    timelineCtx.lineTo(paddingLeft + chartWidth, paddingTop + chartHeight);
    timelineCtx.stroke();

    // Vẽ labels cho trục X
    timelineCtx.fillStyle = isDarkMode ? '#e5e7eb' : '#374151';
    timelineCtx.font = `500 11px ${fontFamily}`;
    timelineCtx.textAlign = 'center';
    timelineCtx.textBaseline = 'top';

    // Số lượng labels động theo chiều rộng
    const numLabels = Math.max(4, Math.floor(chartWidth / 120));
    for (let i = 0; i <= numLabels; i++) {
        const time = paddedMinDate.getTime() + (i / numLabels) * totalTimeRange;
        const date = new Date(time);
        const x = paddingLeft + (i / numLabels) * chartWidth;
        const label = date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: '2-digit'
        });
        timelineCtx.fillText(label, x, paddingTop + chartHeight + 8);
    }
}

function handleTimelineMouseMove(e) {
    if (!timelineCtx || !timelineData) return;

    const rect = timelineCanvas.getBoundingClientRect();
    const dpr = window.devicePixelRatio || 1;
    // Tính toán vị trí chuột trên canvas (tính cả scale)
    const x = (e.clientX - rect.left);
    const y = (e.clientY - rect.top);

    const tooltip = document.getElementById('timelineTooltip');
    let hoveredData = null;

    // Tìm khóa học đang hover
    const barHeight = 24;
    for (const data of timelineData) {
        if (data.startX && data.endX && data.y) {
            if (x >= data.startX && x <= data.endX &&
                y >= data.y - barHeight / 2 && y <= data.y + barHeight / 2) {
                hoveredData = data;
                break;
            }
        }
    }

    if (hoveredData) {
        // Hiển thị tooltip
        const formatDate = (date) => {
            return date.toLocaleDateString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        };

        const statusColor = hoveredData.statusText === 'Sắp diễn ra' ? '#3b82f6' :
                           hoveredData.statusText === 'Đang diễn ra' ? '#10b981' : '#6b7280';

        tooltip.innerHTML = `
            <div class="font-semibold mb-2 pb-2 border-b border-gray-200 dark:border-gray-700">${hoveredData.course.title}</div>
            <div class="text-xs space-y-1">
                <div>Bắt đầu: <b>${formatDate(hoveredData.startDate)}</b></div>
                <div>Kết thúc: <b>${formatDate(hoveredData.endDate)}</b></div>
                <div>Thời lượng: <b>${hoveredData.days} ngày</b></div>
                <div class="mt-2">Trạng thái: <span style="color: ${statusColor}; font-weight: bold;">${hoveredData.statusText}</span></div>
            </div>
        `;
        tooltip.classList.remove('hidden');
        // Sử dụng fixed positioning với client coordinates
        tooltip.style.left = `${e.clientX + 15}px`;
        tooltip.style.top = `${e.clientY + 15}px`;

        // Thay đổi cursor
        timelineCanvas.style.cursor = 'pointer';

        // Vẽ lại với highlight
        drawTimeline();
        const paddingTop = 40;
        const rowHeight = 40;
        const spacing = 8;
        const barHeight = 24;
        const barY = paddingTop + hoveredData.rowIndex * (rowHeight + spacing) + (rowHeight - barHeight) / 2;
        timelineCtx.strokeStyle = hoveredData.color;
        timelineCtx.lineWidth = 2;
        timelineCtx.strokeRect(hoveredData.startX - 1, barY - 1, hoveredData.width + 2, barHeight + 2);
    } else {
        tooltip.classList.add('hidden');
        timelineCanvas.style.cursor = 'default';
        drawTimeline();
    }
}

function handleTimelineMouseLeave() {
    const tooltip = document.getElementById('timelineTooltip');
    tooltip.classList.add('hidden');
    timelineCanvas.style.cursor = 'default';
    drawTimeline();
}

function handleTimelineClick(e) {
    if (!timelineData) return;

    const rect = timelineCanvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    // Tìm khóa học được click
    const barHeight = 24;
    for (const data of timelineData) {
        if (data.startX && data.endX && data.y) {
            if (x >= data.startX && x <= data.endX &&
                y >= data.y - barHeight / 2 && y <= data.y + barHeight / 2) {
                // Có thể thêm hành động khi click vào khóa học
                console.log('Clicked course:', data.course.title);
                break;
            }
        }
    }
}


</script>
@endsection

