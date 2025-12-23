@extends('admin.layout')

@section('title', $mode === 'CREATE' ? 'Thêm Quiz Mới' : 'Chỉnh sửa Quiz')

@section('description', $mode === 'CREATE' ? 'Tạo bài tập trắc nghiệm mới cho bài học' : 'Cập nhật thông tin bài tập trắc nghiệm')

@section('content')

<div class="w-full max-w-[1800px] mx-auto h-full flex flex-col gap-4 3xl:gap-6">

    <!-- Header bar -->
    <div class="sticky top-0 z-10 p-3 3xl:p-4 bg-white dark:bg-gray-800 backdrop-blur-xl rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-row justify-between gap-4">
        <div class="flex items-center justify-start gap-3">
             <button
                type="button"
                onclick="handleGotoBackPage_Global()"
                class="group px-2.5 py-2.5 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-xl transition-all duration-300 font-medium flex items-center justify-center gap-2 cursor-pointer text-[18px]">
                <svg class="h-6" viewBox="0 0 320 512" fill="currentColor">
                    <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>
                </svg>
            </button>
            <div class="flex flex-col gap-0 min-w-0 flex-1">
                <h2
                    class="font-bold tracking-tight bg-gradient-to-r from-gray-900 to-gray-700 dark:from-gray-100 dark:to-gray-300 bg-clip-text text-transparent truncate">
                    @yield('title', 'Admin Panel')
                </h2>
                @hasSection('description')
                    <p class=" 3xl: text-gray-600 dark:text-gray-400 truncate">
                        @yield('description')
                    </p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
             <button type="button" onclick="openAddQuestionModal()"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium flex items-center gap-2 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm câu hỏi
            </button>
             <button type="button" onclick="saveQuiz()" id="saveQuizBtn"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-medium flex items-center gap-2 shadow-lg shadow-blue-500/30 transition-all active:scale-95">
                <span class="LOADING_IN_BTN hidden" id="saveQuizBtn_loading"></span>
                <span id="saveQuizBtn_text">Lưu Quiz</span>
            </button>
        </div>
    </div>

    <div class="flex-1 h-full min-h-0 min-w-0 flex flex-row items-start justify-start gap-4">

        <!-- Left Column: Quiz Info -->
        <div class="w-full max-w-[600px] h-full min-h-0 flex flex-col gap-4">

            <div class="flex items-center justify-between">
                <h3 class="ml-5 text-xl font-bold text-gray-900 dark:text-white">Danh sách câu hỏi</h3>
                <div></div>
            </div>

            <div class="min-h-0 h-full flex flex-col gap-4 overflow-y-auto">
                <div id="lessonInfoContainer" class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thuộc Bài học</h3>
                    <div id="lessonInfoContent">
                        <!-- Lesson info will be rendered here -->
                    </div>
                </div>

                <!-- Basic Info -->
                <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Thông tin cơ bản</h3>

                    <div>
                        <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Tiêu đề Quiz <span class="text-red-500">*</span></label>
                        <input type="text" id="quizTitle" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                        <textarea id="quizDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="opacity-50 text-gray-400 dark:text-gray-600">
                            <label class="ml-4 block  font-semibold  mb-1">Thời điểm bắt đầu (giây)</label>
                            <input disabled type="number" id="quizPauseAt" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                        </div>
                        <div>
                            <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Tỉ lệ cần đạt (%)</label>
                            <input type="number" id="quizPassingScore" min="0" max="100" value="70" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                        </div>
                    </div>

                    <div class="space-y-2 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="quizIsRequired" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class=" text-gray-700 dark:text-gray-300">Bắt buộc hoàn thành</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="quizIsActive" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class=" text-gray-700 dark:text-gray-300">Kích hoạt</span>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Questions -->
        <div class="w-full max-w-[1200px] h-full min-h-0 flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h3 class="ml-5 text-xl font-bold text-gray-900 dark:text-white">Danh sách câu hỏi</h3>
                <div></div>
            </div>

            <div id="questionsList" class="min-h-0 h-full flex flex-col gap-4 overflow-y-auto">
                <!-- Question items rendered here -->
                <div id="emptyQuestions" class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-dashed border-2 border-gray-200 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400">Chưa có câu hỏi nào</p>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Question Modal (Hidden) -->
<div id="questionModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
     <div class="flex flex-row items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
        <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/80" onclick="closeQuestionModal()"></div>

        <div class="px-6 inline-block w-full max-w-3xl text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
            <div class="py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 id="questionModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Thêm câu hỏi</h3>
                <button type="button" onclick="closeQuestionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="py-4 max-h-[70vh] overflow-y-auto" id="questionModalContent">
                <!-- Render single question form here -->
            </div>

            <div class="py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button type="button" onclick="closeQuestionModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg">Hủy</button>
                <button type="button" onclick="saveQuestionToLocal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu câu hỏi</button>
            </div>
        </div>
    </div>
</div>

<script>
    const mode = '{{ $mode }}';
    const quizId = @if($mode === 'EDIT_QUIZ' && isset($quizId)) {{ $quizId }} @else null @endif;
    let questionsData = [];
    let currentEditingIndex = -1; // -1 means adding new
    let lessonData = null;



    // --- Quiz Logic ---
    document.addEventListener('DOMContentLoaded', async function() {

        initializeUI();

        if (mode === 'EDIT_QUIZ' && quizId) {
            lessonData = await loadQuizData(quizId);

            if (lessonData != null)  {
                if (lessonData.questions) {
                    questionsData = lessonData.questions;
                    renderQuestionsList();
                }
                renderQuizInfo();
                renderLessonInfo();
            } else {
                NotificationModal.show('Không thể tải thông tin Quiz', 'error', handleGotoBackPage_Global);
            }
        }
    });

    function initializeUI() {
        if (mode === 'CREATE_QUIZ') {
            document.getElementById('lessonInfoContainer').classList.add('hidden');
        } else {
            document.getElementById('lessonInfoContainer').classList.remove('hidden');
        }
    }

    async function loadQuizData(id) {
        try {
            const res = await apiRequest(`/lesson-quizzes/${id}`);
            if (res?.success) {
                return res.data;
            } else {
                return null;
            }
        } catch (error) {
            console.error(error);
            return null;
        }
    }

    function renderQuizInfo() {
        document.getElementById('quizTitle').value = lessonData.title;
        document.getElementById('quizDescription').value = lessonData.description || '';
        document.getElementById('quizPauseAt').value = lessonData.start_at_seconds;
        document.getElementById('quizPassingScore').value = lessonData.passing_percent_score;
        document.getElementById('quizIsRequired').checked = lessonData.is_required;
        document.getElementById('quizIsActive').checked = lessonData.is_active;
    }

    function renderLessonInfo() {
        const container = document.getElementById('lessonInfoContent');

        if (!lessonData || !lessonData.lesson) {
            container.innerHTML = `
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-sm">Chưa có thông tin bài học</p>
                </div>
            `;
            return;
        }

        const lesson = lessonData.lesson;
        const duration = lesson.duration ? formatSecondsToHHMMSS_Local(lesson.duration, false) : '-';
        const courseName = (lesson.course && lesson.course.title) ? lesson.course.title : 'Không có khóa học';
        const thumbnail = lesson.thumbnail_path || '';

        container.innerHTML = `
            <div class="space-y-4">
                ${thumbnail ? `
                    <div class="relative rounded-xl overflow-hidden bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 border border-gray-200 dark:border-gray-700">
                        <img src="${escapeHtml_Local(thumbnail)}" alt="${escapeHtml_Local(lesson.title || '')}"
                            class="w-full h-32 object-cover"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hidden w-full h-32 items-center justify-center">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                ` : ''}

                <div class="space-y-3">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wider">Tiêu đề</span>
                        </div>
                        <div class="pl-6 font-bold text-gray-900 dark:text-white text-base leading-snug">
                            ${escapeHtml_Local(lesson.title || 'Không có tiêu đề')}
                        </div>
                    </div>

                    ${lesson.description ? `
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-wider">Mô tả</span>
                            </div>
                            <div class="pl-6 text-sm text-gray-700 dark:text-gray-300 leading-relaxed line-clamp-3">
                                ${escapeHtml_Local(lesson.description)}
                            </div>
                        </div>
                    ` : ''}
                </div>

                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-0.5">Thời lượng</div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                    ${duration}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-lg bg-purple-50/50 dark:bg-purple-900/10 border border-purple-100 dark:border-purple-900/30">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold mb-0.5">Khóa học</div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1">
                                    ${escapeHtml_Local(courseName)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function formatSecondsToHHMMSS_Local(seconds, showHours = true) {
        if (!seconds || seconds < 0) return '00:00';

        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;

        if (showHours || hrs > 0) {
            return `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        } else {
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }
    }

    // --- Questions Management ---
    function renderQuestionsList() {
        const container = document.getElementById('questionsList');
        const emptyState = document.getElementById('emptyQuestions');

        if (questionsData.length === 0) {
            emptyState.classList.remove('hidden');
            container.innerHTML = '';
            container.appendChild(emptyState);
            return;
        }

        emptyState.classList.add('hidden');

        // Keep emptyState as first child but hidden, append question items
        container.innerHTML = '';
        container.appendChild(emptyState);

        questionsData.forEach((q, index) => {
            const el = document.createElement('div');
            el.className = 'p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm transition-all hover:border-blue-300 dark:hover:border-blue-700';
            el.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700  font-bold rounded">Câu ${index + 1}</span>
                            <span class=" text-gray-500 uppercase font-semibold tracking-wider">${q.question_type === 'single_choice' ? 'Một đáp án' : 'Nhiều đáp án'}</span>
                            <span class=" text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">${q.points} điểm</span>
                        </div>
                        <div class="font-medium text-gray-900 dark:text-white mb-2 line-clamp-2">${escapeHtml_Local(q.question_text)}</div>

                        <div class="space-y-1 pl-1 border-l-2 border-gray-100 dark:border-gray-700">
                             ${(q.options || []).map(opt => `
                                <div class="flex items-center gap-2  ${opt.is_correct ? 'text-green-600 font-medium' : 'text-gray-500'}">
                                    ${opt.is_correct ?
                                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                                        '<svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>'}
                                    <span>${escapeHtml_Local(opt.option_text)}</span>
                                </div>
                             `).join('')}
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button type="button" onclick="editQuestion(${index})" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                         <button type="button" onclick="deleteQuestion(${index})" class="p-1.5 text-red-500 hover:bg-red-50 rounded transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(el);
        });
    }

    let tempQuestion = null;

    function openAddQuestionModal() {
        currentEditingIndex = -1;
        document.getElementById('questionModalTitle').innerText = 'Thêm câu hỏi mới';
        tempQuestion = {
            question_text: '',
            question_type: 'single_choice',
            points: 1,
            explanation: '',
            options: [
                { option_text: '', is_correct: true },
                { option_text: '', is_correct: false }
            ]
        };
        renderModalForm();
        document.getElementById('questionModal').classList.remove('hidden');
    }

    function editQuestion(index) {
        currentEditingIndex = index;
        document.getElementById('questionModalTitle').innerText = 'Chỉnh sửa câu hỏi';
        // Deep copy
        tempQuestion = JSON.parse(JSON.stringify(questions[index]));
        renderModalForm();
        document.getElementById('questionModal').classList.remove('hidden');
    }

    function deleteQuestion(index) {
        if(confirm('Bạn có chắc chắn muốn xóa câu hỏi này?')) {
            questions.splice(index, 1);
            renderQuestionsList();
        }
    }

    function closeQuestionModal() {
        document.getElementById('questionModal').classList.add('hidden');
    }

    function renderModalForm() {
        const container = document.getElementById('questionModalContent');
        const q = tempQuestion;

        container.innerHTML = `
            <div class="space-y-4 px-2">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Loại câu hỏi</label>
                        <select onchange="updateTempType(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                            <option value="single_choice" ${q.question_type === 'single_choice' ? 'selected' : ''}>Chọn 1 đáp án đúng</option>
                            <option value="multiple_choice" ${q.question_type === 'multiple_choice' ? 'selected' : ''}>Chọn nhiều đáp án</option>
                        </select>
                    </div>
                    <div>
                        <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Điểm</label>
                        <input type="number" min="1" value="${q.points}" onchange="tempQuestion.points = parseInt(this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                    </div>
                </div>

                <div>
                     <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Nội dung câu hỏi</label>
                     <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none" rows="3" oninput="tempQuestion.question_text = this.value">${escapeHtml_Local(q.question_text)}</textarea>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block  font-semibold text-gray-700 dark:text-gray-300">Danh sách đáp án</label>
                        <button onclick="addTempOption()" class=" text-blue-600 font-semibold hover:underline">+ Thêm đáp án</button>
                    </div>
                    <div class="space-y-2">
                        ${q.options.map((opt, i) => `
                            <div class="flex items-center gap-2">
                                <input type="${q.question_type === 'single_choice' ? 'radio' : 'checkbox'}"
                                    name="correct_opt"
                                    ${opt.is_correct ? 'checked' : ''}
                                    onchange="updateTempCorrect(${i})"
                                    class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer">
                                <input type="text" value="${escapeHtml_Local(opt.option_text)}" oninput="updateTempOptionText(${i}, this.value)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg " placeholder="Nhập đáp án...">
                                <button onclick="removeTempOption(${i})" class="text-red-400 hover:text-red-600 px-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </button>
                            </div>
                        `).join('')}
                    </div>
                </div>

                 <div>
                     <label class="ml-4 block  font-semibold text-blue-700 dark:text-gray-300 mb-1">Giải thích</label>
                     <textarea class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none" rows="2" oninput="tempQuestion.explanation = this.value">${escapeHtml_Local(q.explanation || '')}</textarea>
                </div>
            </div>
        `;
    }

    function updateTempType(type) {
        tempQuestion.question_type = type;
        // If single choice, ensure only one correct
        if (type === 'single_choice') {
            let found = false;
            tempQuestion.options.forEach(opt => {
                if (opt.is_correct && !found) found = true;
                else opt.is_correct = false;
            });
            if (!found && tempQuestion.options.length > 0) tempQuestion.options[0].is_correct = true;
        }
        renderModalForm();
    }

    function addTempOption() {
        if (tempQuestion.options.length >= 8) return;
        tempQuestion.options.push({ option_text: '', is_correct: false });
        renderModalForm();
    }

    function removeTempOption(index) {
        if (tempQuestion.options.length <= 2) return;
        tempQuestion.options.splice(index, 1);
        renderModalForm();
    }

    function updateTempOptionText(index, val) {
        tempQuestion.options[index].option_text = val;
    }

    function updateTempCorrect(index) {
        if (tempQuestion.question_type === 'single_choice') {
            tempQuestion.options.forEach((opt, i) => opt.is_correct = (i === index));
        } else {
            tempQuestion.options[index].is_correct = !tempQuestion.options[index].is_correct;
        }
        renderModalForm(); // To refresh radio buttons visually if needed, though they handle themselves mostly
    }

    function saveQuestionToLocal() {
        // Validate
        if (!tempQuestion.question_text.trim()) {
            NotificationModal.show('Vui lòng nhập nội dung câu hỏi', 'error');
            return;
        }
        const validOptions = tempQuestion.options.filter(o => o.option_text.trim()).length;
        if (validOptions < 2) {
             NotificationModal.show('Cần ít nhất 2 đáp án hợp lệ', 'error');
            return;
        }
        const hasCorrect = tempQuestion.options.some(o => o.is_correct);
        if (!hasCorrect) {
             NotificationModal.show('Cần ít nhất 1 đáp án đúng', 'error');
            return;
        }

        if (currentEditingIndex === -1) {
            questions.push(tempQuestion);
        } else {
            questions[currentEditingIndex] = tempQuestion;
        }

        renderQuestionsList();
        closeQuestionModal();
    }


    // --- Save Quiz ---
    async function saveQuiz() {
        // const lessonId = document.getElementById('lessonId').value;
        const title = document.getElementById('quizTitle').value;

        // if (!lessonId) {
        //     NotificationModal.show('Vui lòng chọn bài học', 'error');
        //     return;
        // }
        // if (!title.trim()) {
        //     NotificationModal.show('Vui lòng nhập tiêu đề Quiz', 'error');
        //     return;
        // }

        const payload = {
            // lesson_id: lessonId,
            title: title,
            description: document.getElementById('quizDescription').value,
            start_at_seconds: document.getElementById('quizPauseAt').value,
            passing_percent_score: document.getElementById('quizPassingScore').value,
            is_required: document.getElementById('quizIsRequired').checked,
            is_active: document.getElementById('quizIsActive').checked,
            questions: questions
        };

        const btn = document.getElementById('saveQuizBtn');
        const loading = document.getElementById('saveQuizBtn_loading');
        btn.disabled = true;
        loading.classList.remove('hidden');

        try {
            let url = '/lesson-quizzes/store'; // API route seems to be POST /lesson-quizzes? No, wait.
            // Let's check routes/api.php.
            // POST /lesson-quizzes is mapped to store? I need to verify route
            // The existing code used `apiRequest('/lesson-quizzes')` likely for store if not using `lessonId` prefix?
            // Actually `LessonQuizController::store` is mapped to `POST /lesson-quizzes` in most resource controllers.
            // Let me re-verify api routes.

            // Re-checking api.php:
            // routeWithPermission('post', '/', [LessonQuizController::class, 'store'], ... inside prefix('lesson-quizzes')
            // So it is POST /api/lesson-quizzes

            let method = 'POST';
            let apiUrl = '/lesson-quizzes';

            if (mode === 'EDIT_QUIZ' && quizId) {
                apiUrl = `/lesson-quizzes/${quizId}`; // Update uses POST/PUT?
                // Usually PUT for update. `routeWithPermission('put', '/{quizId}', ...)`
                 method = 'PUT';
            }

            const res = await apiRequest(apiUrl, {
                method: method,
                body: JSON.stringify(payload)
            });

            if (res.success) {
                NotificationModal.show('Lưu Quiz thành công!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("admin.quizzes.list") }}';
                }, 1000);
            } else {
                throw new Error(res.message);
            }

        } catch (error) {
            console.error(error);
            NotificationModal.show(error.message || 'Lỗi khi lưu Quiz', 'error');
            btn.disabled = false;
            loading.classList.add('hidden');
        }
    }

    function escapeHtml_Local(unsafe) {
        if (!unsafe) return "";
        return unsafe.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

</script>
@endsection
