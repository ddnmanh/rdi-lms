{{-- Statistics Tab --}}
<div class="tab-panel p-6 hidden h-full flex flex-col items-stretch justify-start" data-tab-content="tab-statistics">
    <div class="flex-1 min-h-0">
        <div class="space-y-2 h-full overflow-y-auto overflow-x-hidden pr-1">
            {{-- Thống kê tổng quan --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Tổng quan</h3>
                    {{-- <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Cập nhật thời gian thực</span> --}}
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/40 backdrop-blur p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30">
                                    <i class="fa-solid fa-book text-purple-600 dark:text-purple-400"></i>
                                </span>
                                <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">Tổng bài học</span>
                            </div>
                            {{-- <span class="text-xs text-gray-400">All</span> --}}
                        </div>
                        <div id="statTotalLessons" class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">0</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/40 backdrop-blur p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30">
                                    <i class="fa-solid fa-users text-blue-600 dark:text-blue-400"></i>
                                </span>
                                <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">Tổng học viên</span>
                            </div>
                        </div>
                        <div id="statTotalUsers" class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">0</div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/40 backdrop-blur p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                                    <i class="fa-solid fa-check-circle text-green-600 dark:text-green-400"></i>
                                </span>
                                <span class="text-sm font-semibold text-green-700 dark:text-green-300">Sinh viên đạt</span>
                            </div>
                        </div>
                        <div id="statCompletedUsers" class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">0</div>
                        <span class="text-xs text-gray-400">Hoàn thành trên 80% tiến độ khóa học</span>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/40 backdrop-blur p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                                    <i class="fa-solid fa-chart-line text-amber-600 dark:text-amber-400"></i>
                                </span>
                                <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">Tiến độ SV hoàn thành khóa học</span>
                            </div>
                            {{-- <span class="text-xs text-gray-400">Avg</span> --}}
                        </div>
                        <div id="statAvgProgress" class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100">0%</div>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/40 backdrop-blur p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span id="statCourseTimeIcon" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30">
                                    <i class="fa-solid fa-calendar-days text-amber-600 dark:text-amber-400"></i>
                                </span>
                                <span id="statCourseTimeLabel" class="text-sm font-semibold text-amber-700 dark:text-amber-300">Thời gian khóa học</span>
                            </div>
                            <span id="statCourseStatusBadge" class="text-xs px-2 py-1 rounded-full font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                Đang tải...
                            </span>
                        </div>
                        <div id="statCourseTimeValue" class="text-3xl font-extrabold tracking-tight text-gray-900 dark:text-gray-100 mb-2">--</div>
                        <div class="mt-3 space-y-2">
                            {{-- Progress bar thời gian --}}
                            <div class="h-2 w-full rounded-full bg-amber-100 dark:bg-gray-700 overflow-hidden">
                                <div id="statCourseTimeProgressBar" class="h-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 dark:from-amber-500 dark:to-amber-600 transition-all duration-500" style="width: 0%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                                <span id="statCourseStartDate">--</span>
                                <span id="statCourseTimeProgress">0%</span>
                                <span id="statCourseEndDate">--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biểu đồ thống kê --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Bài học</h3>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    {{-- Biểu đồ: Phân bổ thời lượng bài học --}}
                    <div class="xl:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-clock text-cyan-500 mr-2"></i>
                                Phân bổ thời lượng bài học
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Phút</span>
                        </div>
                        <div id="chartLessonDuration" class="w-full min-h-[400px] h-[400px]"></div>
                    </div>

                    {{-- Biểu đồ: Bài học theo sinh viên tham gia --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-chart-column text-blue-500 mr-2"></i>
                                Bài học theo sinh viên tham gia
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Số SV đã xem</span>
                        </div>
                        <div id="chartLessonWatched" class="w-full min-h-[400px] h-[400px]"></div>
                    </div>

                    {{-- Biểu đồ: Bài học theo sinh viên hoàn thành --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-chart-column text-green-500 mr-2"></i>
                                Bài học theo sinh viên hoàn thành
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Số SV hoàn thành</span>
                        </div>
                        <div id="chartLessonCompleted" class="w-full min-h-[400px] h-[400px]"></div>
                    </div>

                </div>
            </div>

            {{-- Biểu đồ thống kê --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Sinh viên</h3>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                    {{-- Biểu đồ: Phân bổ tiến độ học viên --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-chart-pie text-purple-500 mr-2"></i>
                                Phân bổ tiến độ học viên
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Theo %</span>
                        </div>
                        <div id="chartProgressDistribution" class="w-full min-h-[400px] h-[400px]"></div>
                    </div>

                    {{-- Biểu đồ: Top học viên xuất sắc --}}
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                <i class="fa-solid fa-ranking-star text-amber-500 mr-2"></i>
                                Top học viên xuất sắc
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Top 10</span>
                        </div>
                        <div id="chartTopStudents" class="w-full min-h-[400px] h-[400px]"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>

    function renderStatistics() {
        // Tính toán thống kê tổng quan
        const completedUsers = usersData_MainShow.filter(u => u.course_progress?.completion_percentage > 80).length;
        const avgProgress = usersData_MainShow.length > 0
            ? (usersData_MainShow.reduce((sum, u) => sum + (u.course_progress?.completion_percentage || 0), 0) / usersData_MainShow.length).toFixed(2)
            : 0;

        document.getElementById('statTotalLessons').innerText = lessonsData_MainShow.length;
        document.getElementById('statTotalUsers').innerText = usersData_MainShow.length;
        document.getElementById('statCompletedUsers').innerText = completedUsers;
        document.getElementById('statAvgProgress').innerText = `${avgProgress}%`;

        renderCourseTimeStatistics();
    }


    function renderCourseTimeStatistics() {
        const startDate = courseData_MainShow.start_date ? new Date(courseData_MainShow.start_date) : null;
        const endDate = courseData_MainShow.end_date ? new Date(courseData_MainShow.end_date) : null;
        const now = new Date();

        const iconEl = document.querySelector('#statCourseTimeIcon i');
        const labelEl = document.getElementById('statCourseTimeLabel');
        const valueEl = document.getElementById('statCourseTimeValue');
        const statusBadgeEl = document.getElementById('statCourseStatusBadge');
        const progressBarEl = document.getElementById('statCourseTimeProgressBar');
        const startDateEl = document.getElementById('statCourseStartDate');
        const endDateEl = document.getElementById('statCourseEndDate');
        const timeProgressEl = document.getElementById('statCourseTimeProgress');

        if (!startDate || !endDate) {
            valueEl.textContent = 'Chưa xác định';
            statusBadgeEl.textContent = 'N/A';
            statusBadgeEl.className = 'text-xs px-2 py-1 rounded-full font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300';
            startDateEl.textContent = '--';
            endDateEl.textContent = '--';
            timeProgressEl.textContent = '0%';
            progressBarEl.style.width = '0%';
            return;
        }

        // Format ngày
        const formatDateShort = (date) => {
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        };

        startDateEl.textContent = formatDateShort(startDate);
        endDateEl.textContent = formatDateShort(endDate);

        // Tính tổng thời lượng khóa học (ms)
        const totalDuration = endDate - startDate;
        const totalDays = Math.ceil(totalDuration / (1000 * 60 * 60 * 24));

        // Xác định trạng thái và tính toán
        let status, statusText, statusClass, timeValue, iconClass, labelText, progressPercent;

        if (now < startDate) {
            // Sắp diễn ra
            status = 'upcoming';
            statusText = 'Sắp diễn ra';
            statusClass = 'text-xs px-2 py-1 rounded-full font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';

            const msUntilStart = startDate - now;
            const daysUntilStart = Math.floor(msUntilStart / (1000 * 60 * 60 * 24));
            const hoursRemaining = Math.floor((msUntilStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            if (daysUntilStart === 0 && hoursRemaining === 0) {
                timeValue = 'Hôm nay';
            } else if (daysUntilStart < 3) {
                // Hiển thị cả ngày và giờ khi nhỏ hơn 3 ngày
                if (daysUntilStart === 0) {
                    timeValue = `${hoursRemaining} giờ`;
                } else {
                    timeValue = `${daysUntilStart} ngày ${hoursRemaining} giờ`;
                }
            } else if (daysUntilStart < 7) {
                timeValue = `${daysUntilStart} ngày nữa`;
            } else if (daysUntilStart < 30) {
                const weeks = Math.floor(daysUntilStart / 7);
                timeValue = `${weeks} tuần nữa`;
            } else {
                const months = Math.floor(daysUntilStart / 30);
                timeValue = `${months} tháng nữa`;
            }

            labelText = 'Bắt đầu sau';
            iconClass = 'fa-solid fa-hourglass-start';
            progressPercent = 0;

        } else if (now >= startDate && now <= endDate) {
            // Đang diễn ra
            status = 'active';
            statusText = 'Đang diễn ra';
            statusClass = 'text-xs px-2 py-1 rounded-full font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';

            const msRemaining = endDate - now;
            const daysRemaining = Math.floor(msRemaining / (1000 * 60 * 60 * 24));
            const hoursRemaining = Math.floor((msRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            if (daysRemaining === 0 && hoursRemaining === 0) {
                timeValue = 'Hôm nay';
            } else if (daysRemaining < 3) {
                // Hiển thị cả ngày và giờ khi nhỏ hơn 3 ngày
                if (daysRemaining === 0) {
                    timeValue = `${hoursRemaining} giờ`;
                } else {
                    timeValue = `${daysRemaining} ngày ${hoursRemaining} giờ`;
                }
            } else if (daysRemaining < 7) {
                timeValue = `${daysRemaining} ngày`;
            } else if (daysRemaining < 30) {
                const weeks = Math.floor(daysRemaining / 7);
                timeValue = `${weeks} tuần`;
            } else {
                const months = Math.floor(daysRemaining / 30);
                timeValue = `${months} tháng`;
            }

            labelText = 'Còn lại';
            iconClass = 'fa-solid fa-clock';

            // Tính % tiến độ thời gian
            const elapsed = now - startDate;
            progressPercent = Math.min(100, Math.max(0, (elapsed / totalDuration) * 100));

        } else {
            // Đã kết thúc
            status = 'past';
            statusText = 'Đã kết thúc';
            statusClass = 'text-xs px-2 py-1 rounded-full font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';

            const daysSinceEnd = Math.floor((now - endDate) / (1000 * 60 * 60 * 24));
            if (daysSinceEnd === 0) {
                timeValue = 'Hôm nay';
            } else if (daysSinceEnd === 1) {
                timeValue = '1 ngày trước';
            } else if (daysSinceEnd < 7) {
                timeValue = `${daysSinceEnd} ngày trước`;
            } else if (daysSinceEnd < 30) {
                const weeks = Math.floor(daysSinceEnd / 7);
                timeValue = `${weeks} tuần trước`;
            } else {
                const months = Math.floor(daysSinceEnd / 30);
                timeValue = `${months} tháng trước`;
            }

            labelText = 'Đã kết thúc';
            iconClass = 'fa-solid fa-calendar-check';
            progressPercent = 100;
        }

        // Update UI
        iconEl.className = `${iconClass} text-amber-600 dark:text-amber-400`;
        labelEl.textContent = labelText;
        valueEl.textContent = timeValue;
        statusBadgeEl.textContent = statusText;
        statusBadgeEl.className = statusClass;
        progressBarEl.style.width = `${progressPercent.toFixed(1)}%`;
        timeProgressEl.textContent = `${progressPercent.toFixed(1)}%`;
    }

    // Khởi tạo các biểu đồ
    let chartLessonWatched = null;
    let chartLessonCompleted = null;
    let chartProgressDistribution = null;
    let chartTopStudents = null;
    let chartLessonDuration = null;
    let chartsInitialized = false; // Flag để chỉ khởi tạo 1 lần
    // Render tất cả các biểu đồ sau khi có dữ liệu
    function renderCharts() {
        if (chartsInitialized) {
            // Nếu đã khởi tạo rồi, chỉ cần resize
            chartLessonWatched?.resize();
            chartLessonCompleted?.resize();
            chartProgressDistribution?.resize();
            chartTopStudents?.resize();
            chartLessonDuration?.resize();
            return;
        }

        renderChartLessonWatched();
        renderChartLessonCompleted();
        renderChartProgressDistribution();
        renderChartTopStudents();
        renderChartLessonDuration();
        chartsInitialized = true;

        // Responsive charts khi resize window
        window.addEventListener('resize', () => {
            chartLessonWatched?.resize();
            chartLessonCompleted?.resize();
            chartProgressDistribution?.resize();
            chartTopStudents?.resize();
            chartLessonDuration?.resize();
        });
    }

    // Biểu đồ: Bài học theo sinh viên tham gia
    function renderChartLessonWatched() {
        const chartDom = document.getElementById('chartLessonWatched');
        if (!chartDom) return;

        chartLessonWatched = echarts.init(chartDom);

        const sortedLessons = [...lessonsData_MainShow].sort((a, b) => a.display_order - b.display_order);

        const xAxisData = sortedLessons.map(lesson => `${lesson.display_order}. ${lesson.title}`);
        const seriesData = sortedLessons.map(lesson => lessonUserWatchedCounts_MainShow[lesson.id] || 0);

        const option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params) {
                    const param = params[0];
                    return `${param.name}<br/>${param.marker}Số SV tham gia: <b>${param.value}</b>`;
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '10%',
                top: '10%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: xAxisData,
                axisLabel: {
                    rotate: 45,
                    interval: 0,
                    formatter: function(value) {
                        return value.length > 20 ? value.substring(0, 20) + '...' : value;
                    }
                }
            },
            yAxis: {
                type: 'value',
                name: 'Số sinh viên',
                minInterval: 1
            },
            series: [{
                name: 'Sinh viên tham gia',
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

        chartLessonWatched.setOption(option);
    }

    // Biểu đồ: Bài học theo sinh viên hoàn thành
    function renderChartLessonCompleted() {
        const chartDom = document.getElementById('chartLessonCompleted');
        if (!chartDom) return;

        chartLessonCompleted = echarts.init(chartDom);

        const sortedLessons = [...lessonsData_MainShow].sort((a, b) => a.display_order - b.display_order);

        const xAxisData = sortedLessons.map(lesson => `${lesson.display_order}. ${lesson.title}`);
        const seriesData = sortedLessons.map(lesson => lessonUserFinishedCounts_MainShow[lesson.id] || 0);

        const option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params) {
                    const param = params[0];
                    return `${param.name}<br/>${param.marker}Số SV hoàn thành: <b>${param.value}</b>`;
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '10%',
                top: '10%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: xAxisData,
                axisLabel: {
                    rotate: 45,
                    interval: 0,
                    formatter: function(value) {
                        return value.length > 20 ? value.substring(0, 20) + '...' : value;
                    }
                }
            },
            yAxis: {
                type: 'value',
                name: 'Số sinh viên',
                minInterval: 1
            },
            series: [{
                name: 'Sinh viên hoàn thành',
                type: 'bar',
                data: seriesData,
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#10b981' },
                        { offset: 1, color: '#34d399' }
                    ])
                },
                emphasis: {
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: '#059669' },
                            { offset: 1, color: '#10b981' }
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

        chartLessonCompleted.setOption(option);
    }

    // Biểu đồ: Phân bổ tiến độ học viên
    function renderChartProgressDistribution() {
        const chartDom = document.getElementById('chartProgressDistribution');
        if (!chartDom) return;

        chartProgressDistribution = echarts.init(chartDom);

        // Phân loại theo khoảng tiến độ
        const ranges = [
            { name: '0-30%', min: 0, max: 30, count: 0, color: '#ef4444' },
            { name: '30-60%', min: 30.01, max: 60, count: 0, color: '#f6b639' },
            { name: '60-80%', min: 60.01, max: 80, count: 0, color: '#3b82f6' },
            { name: '80-100%', min: 80.01, max: 100, count: 0, color: '#10b981' }
        ];

        usersData_MainShow.forEach(user => {
            const progress = user.course_progress?.completion_percentage || 0;
            const range = ranges.find(r => progress >= r.min && progress <= r.max);
            if (range) range.count++;
        });

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
                top: 'center'
            },
            series: [{
                name: 'Tiến độ',
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
                data: ranges.map(range => ({
                    value: range.count,
                    name: range.name,
                    itemStyle: { color: range.color }
                }))
            }]
        };

        chartProgressDistribution.setOption(option);
    }

    // Biểu đồ: Top học viên xuất sắc
    function renderChartTopStudents() {
        const chartDom = document.getElementById('chartTopStudents');
        if (!chartDom) return;

        chartTopStudents = echarts.init(chartDom);

        // Sắp xếp theo tiến độ giảm dần và lấy top 10
        const topUsers = [...usersData_MainShow]
            .sort((a, b) => {
                const progressA = a.course_progress?.completion_percentage || 0;
                const progressB = b.course_progress?.completion_percentage || 0;
                return progressB - progressA;
            })
            .slice(0, 10);

        const yAxisData = topUsers.map(user => user.fullname || user.email || 'N/A');

        const seriesData = topUsers.map(user => {
            const progress = user.course_progress?.completion_percentage || 0;
            return {
                value: progress,
                itemStyle: {
                    color: progress > 80 ? '#10b981' : progress > 60 ? '#3b82f6' : progress > 30 ? '#f6b639' : '#ef4444'
                }
            };
        });

        const option = {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params) {
                    const param = params[0];
                    return `${param.name}<br/>${param.marker}Tiến độ: <b>${param.value.toFixed(2)}%</b>`;
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                top: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'value',
                name: 'Tiến độ (%)',
                max: 100,
                axisLabel: {
                    formatter: '{value}%'
                }
            },
            yAxis: {
                type: 'category',
                data: yAxisData,
                axisLabel: {
                    formatter: function(value) {
                        // return value.length > 15 ? value.substring(0, 15) + '...' : value;
                        return value;
                    }
                }
            },
            series: [{
                name: 'Tiến độ',
                type: 'bar',
                data: seriesData,
                label: {
                    show: true,
                    position: 'right',
                    formatter: function(params) {
                        return params.value.toFixed(1) + '%';
                    }
                },
                barWidth: '60%'
            }]
        };
        chartTopStudents.setOption(option);
    }

    // Biểu đồ: Phân bổ thời lượng bài học
    function renderChartLessonDuration() {
        const chartDom = document.getElementById('chartLessonDuration');
        if (!chartDom) return;

        chartLessonDuration = echarts.init(chartDom);

        const sortedLessons = [...lessonsData_MainShow]
            .filter(lesson => lesson.duration && lesson.duration > 0)
            .sort((a, b) => a.display_order - b.display_order);

        if (sortedLessons.length === 0) {
            chartLessonDuration.setOption({
                title: {
                    text: 'Không có dữ liệu thời lượng',
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

        // Chuyển đổi duration (giây) sang phút và làm tròn
        const data = sortedLessons.map(lesson => ({
            name: `${lesson.display_order}. ${lesson.title}`,
            value: Math.round(lesson.duration / 60) // Chuyển giây sang phút
        }));

        // Tạo màu sắc gradient cho các phần
        const colors = [
            '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981',
            '#06b6d4', '#6366f1', '#f43f5e', '#14b8a6', '#a855f7',
            '#eab308', '#fb7185', '#0ea5e9', '#f87171', '#22c55e',
            '#38bdf8', '#facc15', '#60a5fa', '#f472b6', '#bef264', '#fbbf24'
        ];

        const option = {
            tooltip: {
                trigger: 'item',
                formatter: function(params) {
                    const hours = Math.floor(params.value / 60);
                    const minutes = params.value % 60;
                    let timeStr = '';
                    if (hours > 0) {
                        timeStr = `${hours} giờ ${minutes} phút`;
                    } else {
                        timeStr = `${minutes} phút`;
                    }
                    return `${params.name}<br/>${params.marker}Thời lượng: <b>${timeStr}</b> (${params.percent}%)`;
                }
            },
            legend: {
                type: 'scroll',
                orient: 'vertical',
                left: 'left',
                top: 'center',
                formatter: function(name) {
                    return name.length > 25 ? name.substring(0, 25) + '...' : name;
                }
            },
            series: [{
                name: 'Thời lượng',
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
                    formatter: function(params) {
                        return `${params.name} phút`;
                    }
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

        chartLessonDuration.setOption(option);
    }




</script>
