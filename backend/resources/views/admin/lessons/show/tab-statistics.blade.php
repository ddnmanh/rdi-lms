{{-- Statistics Tab --}}
<div class="tab-panel p-6 hidden h-full flex flex-col items-stretch justify-start" data-tab-content="TAB_STATISTICS">
    <div class="flex-1 min-h-0">
        <div class="space-y-6 h-full overflow-y-auto overflow-x-hidden pr-2 pb-6">

            {{-- Loading State --}}
             <div id="statisticsLoading" class="hidden w-full h-[400px] flex items-center justify-center">
                <div id="SPINNER_LOADING">
                    <div id="SPINNER_LOADING_CONTAINER">
                        <div id="SPINNER_LOADING_CONTAINER_LDS_ROLLER">
                            <div></div><div></div><div></div><div></div>
                            <div></div><div></div><div></div><div></div>
                        </div>
                    </div>
                    <div id="SPINNER_LOADING_ICON">
                        <svg class="w-6 h-6" viewBox="0 0 640 640" fill="currentColor">
                            <path d="M80 259.8L289.2 345.9C299 349.9 309.4 352 320 352C330.6 352 341 349.9 350.8 345.9L593.2 246.1C602.2 242.4 608 233.7 608 224C608 214.3 602.2 205.6 593.2 201.9L350.8 102.1C341 98.1 330.6 96 320 96C309.4 96 299 98.1 289.2 102.1L46.8 201.9C37.8 205.6 32 214.3 32 224L32 520C32 533.3 42.7 544 56 544C69.3 544 80 533.3 80 520L80 259.8zM128 331.5L128 448C128 501 214 544 320 544C426 544 512 501 512 448L512 331.4L369.1 390.3C353.5 396.7 336.9 400 320 400C303.1 400 286.5 396.7 270.9 390.3L128 331.4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div id="statisticsContent" class="hidden space-y-6">
                 {{-- 1. Tổng quan (Cards) --}}
                 <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                     {{-- Cards keep the same --}}
                     <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                         <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tổng Quiz</div>
                         <div class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalQuizzes">0</div>
                     </div>
                     <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                         <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tổng Câu hỏi</div>
                         <div class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalQuestions">0</div>
                     </div>
                     <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                         <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Lượt làm bài</div>
                         <div class="text-2xl font-bold text-gray-900 dark:text-white" id="statTotalAttempts">0</div>
                     </div>
                     <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                         <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Điểm TB</div>
                         <div class="text-2xl font-bold text-gray-900 dark:text-white" id="statAvgScore">0</div>
                     </div>
                      <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                         <div class="text-sm text-gray-500 dark:text-gray-400 mb-1">Thời gian TB</div>
                         <div class="text-2xl font-bold text-gray-900 dark:text-white" id="statAvgDuration">00:00</div>
                     </div>
                 </div>

                {{-- Row 1: Quiz Stats (Charts 1 & 2) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- 1. Biểu đồ cột: Lượt làm vs Lượt đạt theo Quiz --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Lượt làm bài & Lượt đạt theo Quiz</h4>
                        <div id="chartQuizAttempts" class="w-full h-[350px]"></div>
                    </div>

                    {{-- 2. Biểu đồ tròn: Điểm trung bình các Quiz --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Điểm trung bình các Quiz</h4>
                        <div id="chartQuizScores" class="w-full h-[350px]"></div>
                    </div>
                </div>

                {{-- Row 2: Top Students (Chart 3) --}}
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                     <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Top Học viên xuất sắc</h4>
                     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                         <div class="lg:col-span-2">
                             <div id="chartTopStudents" class="w-full h-[350px]"></div>
                         </div>
                         <div class="overflow-y-auto max-h-[350px]">
                             <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                 <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0">
                                     <tr>
                                         <th class="px-4 py-2">Học viên</th>
                                         <th class="px-4 py-2 text-right">Điểm</th>
                                     </tr>
                                 </thead>
                                 <tbody id="tableTopStudentsMini"></tbody>
                             </table>
                         </div>
                     </div>
                </div>

                {{-- Row 3: Question Stats (Charts 4 & 5) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                     {{-- 4. Biểu đồ cột: Số lượng câu hỏi của từng Quiz --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Số lượng câu hỏi theo Quiz</h4>
                        <div id="chartQuestionsPerQuiz" class="w-full h-[350px]"></div>
                    </div>

                     {{-- 5. Biểu đồ: Thống kê lượt làm vs Lượt đạt (Đúng) của Quesiton --}}
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Hiệu suất trả lời câu hỏi (Top 10 phổ biến)</h4>
                        <div id="chartQuestionPerformance" class="w-full h-[350px]"></div>
                    </div>
                </div>
            </div>

            <div id="statisticsEmpty" class="hidden h-[400px] w-full flex flex-col items-center justify-center text-gray-500">
                 <svg class="w-16 h-16 mb-4 text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <p>Chưa có dữ liệu thống kê cho bài học này</p>
            </div>

        </div>
    </div>
</div>

{{-- ECharts --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<script>
    let chartQuizAttempts = null;
    let chartQuizScores = null;
    let chartTopStudents = null;
    let chartQuestionsPerQuiz = null;
    let chartQuestionPerformance = null;

    let statisticsLoaded = false;

    // Hàm được gọi từ parent khi tab change
    async function loadLessonStatistics() {
        if (statisticsLoaded) {
             resizeCharts();
             return;
        }

        const loadingEl = document.getElementById('statisticsLoading');
        const contentEl = document.getElementById('statisticsContent');
        const emptyEl = document.getElementById('statisticsEmpty');

        loadingEl.classList.remove('hidden');
        contentEl.classList.add('hidden');
        emptyEl.classList.add('hidden');

        try {
            const res = await apiRequest(`/lessons/${lessonId}/statistics`);
            if (res && res.success) {
                const data = res.data;

                console.log(data);


                // Update text stats
                document.getElementById('statTotalQuizzes').innerText = data.total_quizzes;
                document.getElementById('statTotalQuestions').innerText = data.total_questions;
                document.getElementById('statTotalAttempts').innerText = data.total_attempts;
                document.getElementById('statAvgScore').innerText = data.avg_score;

                const mins = Math.floor(data.avg_duration_seconds / 60);
                const secs = data.avg_duration_seconds % 60;
                document.getElementById('statAvgDuration').innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;

                if (data.total_quizzes === 0) {
                     loadingEl.classList.add('hidden');
                     emptyEl.classList.remove('hidden');
                     return;
                }

                contentEl.classList.remove('hidden');
                loadingEl.classList.add('hidden');

                // Render charts
                renderChart1_QuizAttempts(data.quiz_stats);
                renderChart2_QuizScores(data.quiz_stats);
                renderChart3_TopStudents(data.top_students);
                renderChart4_QuestionsPerQuiz(data.quiz_stats);
                renderChart5_QuestionPerformance(data.all_questions_stats);


                // Render Mini Table Top Students
                 renderTableTopStudentsMini(data.top_students);

                statisticsLoaded = true;

            } else {
                 loadingEl.classList.add('hidden');
                 NotificationModal.show('Lỗi', 'Không thể tải thống kê', 'error');
            }
        } catch (err) {
            loadingEl.classList.add('hidden');
            console.error(err);
             NotificationModal.show('Lỗi', 'Có lỗi xảy ra', 'error');
        }
    }

    // 1. Biểu đồ cột: Lượt làm quiz, lượt đạt quiz
    function renderChart1_QuizAttempts(quizStats) {
        const dom = document.getElementById('chartQuizAttempts');
        if(!dom) return;
        chartQuizAttempts = echarts.init(dom);

        const titles = quizStats.map(q => q.title);
        const attempts = quizStats.map(q => q.total_attempts);
        const passed = quizStats.map(q => q.passed_count);

        const option = {
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            legend: { bottom: '0%' },
            grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
            xAxis: { type: 'category', data: titles, axisLabel: { interval: 0, rotate: 30, overflow: 'break' } },
            yAxis: { type: 'value' },
            series: [
                { name: 'Lượt làm', type: 'bar', data: attempts, itemStyle: { color: '#3b82f6' } },
                { name: 'Lượt đạt', type: 'bar', data: passed, itemStyle: { color: '#10b981' } }
            ]
        };
        chartQuizAttempts.setOption(option);
    }

    // 2. Biểu đồ tròn biểu thị điểm của quiz
    function renderChart2_QuizScores(quizStats) {
        const dom = document.getElementById('chartQuizScores');
        if(!dom) return;
        chartQuizScores = echarts.init(dom);

        const data = quizStats.map(q => ({ value: q.avg_score, name: q.title }));

        const option = {
            tooltip: { trigger: 'item', formatter: '{b}: {c} điểm' },
            legend: { bottom: '0%' },
            series: [
                {
                    name: 'Điểm trung bình',
                    type: 'pie',
                    radius: ['40%', '70%'],
                    itemStyle: { borderRadius: 10, borderColor: '#fff', borderWidth: 2 },
                    data: data,
                     label: { show: false },
                    emphasis: { label: { show: true, fontSize: 14, fontWeight: 'bold' } }
                }
            ]
        };
        chartQuizScores.setOption(option);
    }

    // 3. Biểu đồ thống kê sinh viên có thành tích xuất sắc (Horizontal Bar)
    function renderChart3_TopStudents(topStudents) {
        const dom = document.getElementById('chartTopStudents');
        if(!dom) return;
        chartTopStudents = echarts.init(dom);

        // Reverse for horizontal bar to show highest on top
        const students = [...topStudents].reverse();
        const names = students.map(s => s.user ? s.user.fullname : 'N/A');
        const scores = students.map(s => s.percent_score_earned/10);

        const option = {
            tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
            grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
            xAxis: { type: 'value', max: 10 },
            yAxis: { type: 'category', data: names },
            series: [
                {
                    name: 'Điểm số',
                    type: 'bar',
                    data: scores,
                    itemStyle: { color: '#f59e0b' },
                    label: { show: true, position: 'right' }
                }
            ]
        };
        chartTopStudents.setOption(option);
    }

    function renderTableTopStudentsMini(students) {
         const tbody = document.getElementById('tableTopStudentsMini');
         if(!tbody) return;
         tbody.innerHTML = '';
         students.forEach(s => {
             const name = s.user ? s.user.fullname : 'N/A';
             const row = `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">${name}</td>
                    <td class="px-4 py-2 text-right font-bold text-amber-600">${Number(s.score_earned).toFixed(2)}</td>
                </tr>
             `;
             tbody.insertAdjacentHTML('beforeend', row);
         });
    }

    // 4. Biểu đồ biểu thị số lượng question của từng quiz
    function renderChart4_QuestionsPerQuiz(quizStats) {
        const dom = document.getElementById('chartQuestionsPerQuiz');
        if(!dom) return;
        chartQuestionsPerQuiz = echarts.init(dom);

        const titles = quizStats.map(q => q.title);
        const counts = quizStats.map(q => q.question_count); // requires backend update

        const option = {
             tooltip: { trigger: 'axis' },
             grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
             xAxis: { type: 'category', data: titles, axisLabel: { rotate: 30 } },
             yAxis: { type: 'value' },
             series: [
                 {
                     name: 'Số câu hỏi',
                     type: 'bar',
                     data: counts,
                     itemStyle: { color: '#8b5cf6' },
                     label: { show: true, position: 'top' }
                 }
             ]
        };
        chartQuestionsPerQuiz.setOption(option);
    }

    // 5. Biểu đồ thống kê lượt làm question và lượt đạt (chọn đúng)
    function renderChart5_QuestionPerformance(allQuestions) {
        const dom = document.getElementById('chartQuestionPerformance');
        if(!dom) return;
        chartQuestionPerformance = echarts.init(dom);

        // Sort by total answers desc, take top 10
        const topQuestions = [...allQuestions].sort((a,b) => b.total_answers - a.total_answers).slice(0, 10);

        const labels = topQuestions.map(q => 'Q' + q.id); // Use short ID or truncated text
        const totals = topQuestions.map(q => q.total_answers);
        const corrects = topQuestions.map(q => q.correct_count);

        const option = {
            tooltip: {
                trigger: 'axis',
                formatter: function(params) {
                     const idx = params[0].dataIndex;
                     const q = topQuestions[idx];
                     return `
                        <b>${q.short_question}</b><br/>
                        Tổng trả lời: ${q.total_answers}<br/>
                        Trả lời đúng: ${q.correct_count} (${q.correct_ratio}%)
                     `;
                }
            },
            legend: { bottom: '0%' },
            grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
            xAxis: { type: 'category', data: labels },
            yAxis: { type: 'value' },
            series: [
                { name: 'Tổng lượt trả lời', type: 'bar', data: totals, itemStyle: { color: '#9ca3af' } },
                { name: 'Trả lời đúng', type: 'bar', data: corrects, itemStyle: { color: '#ec4899' } }
            ]
        };
        chartQuestionPerformance.setOption(option);
    }

    function resizeCharts() {
        chartQuizAttempts?.resize();
        chartQuizScores?.resize();
        chartTopStudents?.resize();
        chartQuestionsPerQuiz?.resize();
        chartQuestionPerformance?.resize();
    }

    window.addEventListener('resize', resizeCharts);

</script>
