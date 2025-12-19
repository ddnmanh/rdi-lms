{{--
    ============================================
    COMPONENT: Quiz Modal
    ============================================

    Mô tả:
    Component modal để quản lý Quiz (tạo mới, chỉnh sửa) và các câu hỏi trong Quiz.

    Cách sử dụng:
    --------------

    1. Include component trong Blade template:
       @include('components.quiz-modal', [
           'id' => 'myQuizModal',        // ID duy nhất cho modal (mặc định: 'quiz_modal')
           'title' => 'Quản lý Quiz'     // Tiêu đề modal (mặc định: 'Quản lý Quiz')
       ])

    Hoặc sử dụng component syntax:
       <x-quiz-modal id="myQuizModal" title="Quản lý Quiz" />

    2. Khởi tạo QuizModal trong JavaScript:
       QuizModal.init('myQuizModal', {
           onSave: async (payload, quizId) => {
               // Xử lý lưu quiz
               // payload: dữ liệu quiz cần lưu
               // quizId: ID quiz nếu đang edit (null nếu tạo mới)

               const method = quizId ? 'PUT' : 'POST';
               const url = quizId ? `/lesson-quizzes/${quizId}` : '/lesson-quizzes';

               const res = await apiRequest(url, {
                   method: method,
                   body: JSON.stringify(payload)
               });

               if (res.success) {
                   NotificationModal.show('Lưu Quiz thành công!', 'success');
                   QuizModal.close('myQuizModal');
                   // Reload danh sách quiz nếu cần
               }
           },
           onClose: () => {
               // Callback khi đóng modal (tùy chọn)
           },
           mode: 'CREATE'  // 'CREATE' hoặc 'EDIT' (mặc định: 'CREATE')
       });

    3. Mở modal để tạo Quiz mới:
       QuizModal.open('myQuizModal', {
           mode: 'CREATE',
           pauseAt: 0,                    // Thời điểm bắt đầu (giây)
           lessonData: {                  // Thông tin lesson (tùy chọn)
               id: 1,
               title: 'Bài học 1',
               description: 'Mô tả bài học',
               duration: 3600,
               thumbnail_path: '/path/to/thumbnail.jpg',
               course: {
                   title: 'Khóa học 1'
               }
           },
           initialData: {                 // Dữ liệu ban đầu (tùy chọn)
               title: 'Quiz mẫu',
               description: 'Mô tả quiz',
               questions: [...]
           }
       });

    4. Mở modal để chỉnh sửa Quiz:
       QuizModal.open('myQuizModal', {
           quizId: 123,                   // ID của quiz cần chỉnh sửa
           lessonData: {...}              // Thông tin lesson (tùy chọn)
       });

    5. Đóng modal:
       QuizModal.close('myQuizModal');

    6. Lưu Quiz (thủ công, nếu không dùng callback):
       QuizModal.save('myQuizModal');

    Các phương thức khác:
    ---------------------

    - QuizModal.addQuestion(id)
      Thêm câu hỏi mới vào quiz

    - QuizModal.editQuestion(id, index)
      Chỉnh sửa câu hỏi tại vị trí index

    - QuizModal.deleteQuestion(id, index)
      Xóa câu hỏi tại vị trí index

    - QuizModal.getInstance(id)
      Lấy instance của modal để truy cập state và elements

    Cấu trúc payload khi lưu:
    --------------------------
    {
        title: string,                    // Bắt buộc
        description: string,              // Tùy chọn
        start_at_seconds: number,         // Thời điểm bắt đầu (giây)
        passing_percent_score: number,    // Tỉ lệ cần đạt (%)
        is_required: boolean,            // Bắt buộc hoàn thành
        is_active: boolean,              // Kích hoạt
        lesson_id: number,               // ID bài học (thêm vào callback onSave)
        questions: [                     // Danh sách câu hỏi
            {
                question_text: string,
                question_type: 'single_choice' | 'multiple_choice',
                points: number,
                explanation: string,
                options: [
                    {
                        option_text: string,
                        is_correct: boolean
                    }
                ]
            }
        ]
    }

    Ví dụ hoàn chỉnh:
    -----------------

    @include('components.quiz-modal', ['id' => 'lessonQuizModal'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo modal
            QuizModal.init('lessonQuizModal', {
                onSave: async (payload, quizId) => {
                    payload.lesson_id = {{ $lessonId }};
                    const method = quizId ? 'PUT' : 'POST';
                    const url = quizId ? `/lesson-quizzes/${quizId}` : '/lesson-quizzes';
                    const res = await apiRequest(url, {
                        method: method,
                        body: JSON.stringify(payload)
                    });
                    if (res.success) {
                        NotificationModal.show('Lưu Quiz thành công!', 'success');
                        QuizModal.close('lessonQuizModal');
                        loadQuizzes(); // Reload danh sách
                    }
                }
            });

            // Mở modal tạo mới
            document.getElementById('btnCreateQuiz').onclick = () => {
                QuizModal.open('lessonQuizModal', {
                    mode: 'CREATE',
                    pauseAt: 0,
                    lessonData: window.lessonData
                });
            };

            // Mở modal chỉnh sửa
            function editQuiz(quizId) {
                QuizModal.open('lessonQuizModal', {
                    quizId: quizId,
                    lessonData: window.lessonData
                });
            }
        });
    </script>

    Lưu ý:
    ------
    - Component tự động xử lý validation câu hỏi (tối thiểu 2 đáp án, ít nhất 1 đáp án đúng)
    - Component hỗ trợ dark mode
    - Component có scrollbar tùy chỉnh cho các vùng scroll
    - Modal có thể hiển thị thông tin lesson nếu truyền lessonData
--}}

@php
    $id = $id ?? 'quiz_modal';
    $title = $title ?? 'Quản lý Quiz';
@endphp

<div id="{{ $id }}_container" class="hidden fixed inset-0 z-[100] overflow-hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 w-full h-full p-8 overflow-hidden bg-black/50 backdrop-blur-sm transition-opacity flex items-center justify-center">
        <div class="w-full max-w-[1800px] h-full mx-auto px-6 rounded-xl flex flex-col gap-4 3xl:gap-6 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 id="{{ $id }}_mainTitle" class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                <button type="button" onclick="QuizModal.close('{{ $id }}')"
                    class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5" viewBox="0 0 384 512" fill="currentColor">
                        <path d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 min-h-0 flex flex-row items-start justify-start gap-4">

                <!-- Left Column: Quiz Info -->
                <div class="w-full max-w-[600px] h-full min-h-0 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="ml-5 text-xl font-bold text-gray-900 dark:text-white">Thông tin Quiz</h3>
                    </div>

                    <div class="min-h-0 h-full flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Basic Info -->
                        <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Thông tin cơ bản</h3>

                            <div>
                                <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Tiêu đề Quiz <span class="text-red-500">*</span></label>
                                <input type="text" id="{{ $id }}_quizTitle" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                            </div>

                            <div>
                                <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Mô tả</label>
                                <textarea id="{{ $id }}_quizDescription" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none resize-none"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Thời điểm bắt đầu (giây)</label>
                                    <input type="number" id="{{ $id }}_quizPauseAt" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                                </div>
                                <div>
                                    <label class="ml-4 block font-semibold text-blue-700 dark:text-gray-300 mb-1">Tỉ lệ cần đạt (%)</label>
                                    <input type="number" id="{{ $id }}_quizPassingScore" min="0" max="100" value="70" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none">
                                </div>
                            </div>

                            <div class="space-y-2 pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="{{ $id }}_quizIsRequired" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700 dark:text-gray-300">Bắt buộc hoàn thành</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="{{ $id }}_quizIsActive" checked class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="text-gray-700 dark:text-gray-300">Kích hoạt</span>
                                </label>
                            </div>
                        </div>

                        <!-- Lesson Context Info (Optional) -->
                        <div id="{{ $id }}_lessonInfoWrapper" class="hidden" style="display: none !important;">
                             <div class="p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Thông tin bài học</h3>
                                <div id="{{ $id }}_lessonInfoContent"></div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Questions -->
                <div class="flex-1 h-full min-h-0 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="ml-5 text-xl font-bold text-gray-900 dark:text-white">Danh sách câu hỏi</h3>
                        <button type="button" onclick="QuizModal.addQuestion('{{ $id }}')"
                            class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg font-medium flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Thêm câu hỏi</span>
                        </button>
                    </div>

                    <div id="{{ $id }}_questionsList" class="min-h-0 h-full flex flex-col gap-4 overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Empty State -->
                        <div id="{{ $id }}_emptyQuestions" class="text-center py-10 bg-gray-50 dark:bg-gray-800/50 rounded-xl border-dashed border-2 border-gray-200 dark:border-gray-700">
                             <p class="text-gray-500 dark:text-gray-400">Chưa có câu hỏi nào</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button type="button" onclick="QuizModal.close('{{ $id }}')"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium">
                    Hủy
                </button>
                <button type="button" onclick="QuizModal.save('{{ $id }}')" id="{{ $id }}_saveBtn"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-medium flex items-center gap-2">
                    <span class="LOADING_IN_BTN hidden" id="{{ $id }}_saveBtn_loading"></span>
                    <span id="{{ $id }}_saveBtn_text">Lưu Quiz</span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Question Edit Modal (Internal to Quiz Component) -->
<div id="{{ $id }}_questionModal" class="hidden fixed inset-0 z-[110] overflow-y-auto">
     <div class="flex flex-row items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
        <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/80" onclick="QuizModal.closeQuestionModal('{{ $id }}')"></div>

        <div class="px-6 inline-block w-full max-w-3xl text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
            <div class="py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 id="{{ $id }}_questionModalTitle" class="text-lg font-semibold text-gray-900 dark:text-white">Thêm câu hỏi</h3>
                <button type="button" onclick="QuizModal.closeQuestionModal('{{ $id }}')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="py-4 max-h-[70vh] overflow-y-auto custom-scrollbar" id="{{ $id }}_questionModalContent">
                <!-- Render single question form here -->
            </div>

            <div class="py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-3">
                <button type="button" onclick="QuizModal.closeQuestionModal('{{ $id }}')" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg">Hủy</button>
                <button type="button" onclick="QuizModal.confirmSaveQuestion('{{ $id }}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Lưu câu hỏi</button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    const QuizModal = (function() {
        const instances = {};

        function init(id, config = {}) {
            const instance = {
                id: id,
                config: {
                    onSave: config.onSave || null,
                    onClose: config.onClose || null,
                    mode: config.mode || 'CREATE', // CREATE or EDIT
                },
                state: {
                    quizId: null,
                    questions: [],
                    currentEditingIndex: -1,
                    tempQuestion: null,
                    quizData: null,
                },
                elements: {
                    container: document.getElementById(`${id}_container`),
                    mainTitle: document.getElementById(`${id}_mainTitle`),
                    quizTitle: document.getElementById(`${id}_quizTitle`),
                    quizDescription: document.getElementById(`${id}_quizDescription`),
                    quizPauseAt: document.getElementById(`${id}_quizPauseAt`),
                    quizPassingScore: document.getElementById(`${id}_quizPassingScore`),
                    quizIsRequired: document.getElementById(`${id}_quizIsRequired`),
                    quizIsActive: document.getElementById(`${id}_quizIsActive`),
                    questionsList: document.getElementById(`${id}_questionsList`),
                    emptyQuestions: document.getElementById(`${id}_emptyQuestions`),
                    saveBtn: document.getElementById(`${id}_saveBtn`),
                    saveBtnText: document.getElementById(`${id}_saveBtn_text`),
                    saveBtnLoading: document.getElementById(`${id}_saveBtn_loading`),
                    // Question modal
                    questionModal: document.getElementById(`${id}_questionModal`),
                    questionModalTitle: document.getElementById(`${id}_questionModalTitle`),
                    questionModalContent: document.getElementById(`${id}_questionModalContent`),
                    // Lesson info
                    lessonInfoWrapper: document.getElementById(`${id}_lessonInfoWrapper`),
                    lessonInfoContent: document.getElementById(`${id}_lessonInfoContent`),
                }
            };

            instances[id] = instance;
            return instance;
        }

        async function open(id, options = {}) {
            const inst = instances[id];
            if (!inst) return;

            inst.state.quizId = options.quizId || null;
            inst.config.mode = options.mode || (inst.state.quizId ? 'EDIT' : 'CREATE');

            // Initial UI reset
            if (inst.elements.mainTitle) inst.elements.mainTitle.textContent = inst.config.mode === 'EDIT' ? 'Chỉnh sửa Quiz' : 'Thêm Quiz mới';
            if (inst.elements.saveBtnText) inst.elements.saveBtnText.textContent = inst.config.mode === 'EDIT' ? 'Lưu cập nhật' : 'Tạo Quiz';
            if (inst.elements.container) inst.elements.container.classList.remove('hidden');

            // Reset state
            inst.state.questions = [];
            inst.state.quizData = null;

            if (inst.state.quizId) {
                await loadQuizData(inst);
            } else if (options.initialData) {
                inst.state.quizData = options.initialData;
                inst.state.questions = options.initialData.questions || [];
                renderQuizInfo(inst);
                renderQuestionsList(inst);
            } else {
                // Reset form to defaults
                inst.elements.quizTitle.value = '';
                inst.elements.quizDescription.value = '';
                inst.elements.quizPauseAt.value = options.pauseAt || 0;
                inst.elements.quizPassingScore.value = 70;
                inst.elements.quizIsRequired.checked = true;
                inst.elements.quizIsActive.checked = true;
                renderQuestionsList(inst);

                // If initialData.lesson exists even in CREATE mode
                if (options.lessonData) {
                    renderLessonInfo(inst, options.lessonData);
                }
            }
        }

        function close(id) {
            const inst = instances[id];
            if (!inst) return;
            inst.elements.container.classList.add('hidden');
            if (inst.config.onClose) inst.config.onClose();
        }

        async function loadQuizData(inst) {
            try {
                const res = await apiRequest(`/lesson-quizzes/${inst.state.quizId}`);
                if (res?.success) {
                    inst.state.quizData = res.data;
                    inst.state.questions = res.data.questions || [];
                    renderQuizInfo(inst);
                    renderQuestionsList(inst);
                    if (res.data.lesson) {
                        renderLessonInfo(inst, res.data.lesson);
                    }
                } else {
                    NotificationModal.show('Không thể tải thông tin Quiz', 'error');
                    close(inst.id);
                }
            } catch (error) {
                console.error(error);
                NotificationModal.show('Lỗi kết nối', 'error');
                close(inst.id);
            }
        }

        function renderQuizInfo(inst) {
            const data = inst.state.quizData;
            inst.elements.quizTitle.value = data.title || '';
            inst.elements.quizDescription.value = data.description || '';
            inst.elements.quizPauseAt.value = data.start_at_seconds || 0;
            inst.elements.quizPassingScore.value = data.passing_percent_score || 70;
            inst.elements.quizIsRequired.checked = !!data.is_required;
            inst.elements.quizIsActive.checked = !!data.is_active;
        }

        function renderLessonInfo(inst, lesson) {
            if (!lesson) {
                inst.elements.lessonInfoWrapper.classList.add('hidden');
                return;
            }
            inst.elements.lessonInfoWrapper.classList.remove('hidden');
            const duration = lesson.duration ? formatSeconds(lesson.duration) : '-';
            const courseName = lesson.course?.title || 'Không có khóa học';
            const thumbnail = lesson.thumbnail_path || '';

            inst.elements.lessonInfoContent.innerHTML = `
                <div class="space-y-4">
                    ${thumbnail ? `<img src="${escape(thumbnail)}" class="w-full h-32 object-cover rounded-xl border">` : ''}
                    <div class="space-y-2">
                        <div class="text-xs text-gray-500 uppercase font-bold">Bài học</div>
                        <div class="font-bold text-gray-900 dark:text-white">${escape(lesson.title)}</div>
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="text-xs bg-blue-50 dark:bg-blue-900/30 p-2 rounded-lg">
                                <div class="text-gray-500 mb-0.5">Thời lượng</div>
                                <div class="font-bold">${duration}</div>
                            </div>
                            <div class="text-xs bg-purple-50 dark:bg-purple-900/30 p-2 rounded-lg">
                                <div class="text-gray-500 mb-0.5">Khóa học</div>
                                <div class="font-bold truncate">${escape(courseName)}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderQuestionsList(inst) {
            const list = inst.elements.questionsList;
            const empty = inst.elements.emptyQuestions;
            const questions = inst.state.questions;

            // Clear except empty state
            const items = list.querySelectorAll('.question-item');
            items.forEach(el => el.remove());

            if (questions.length === 0) {
                empty.classList.remove('hidden');
                return;
            }
            empty.classList.add('hidden');

            questions.forEach((q, idx) => {
                const el = document.createElement('div');
                el.className = 'question-item p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-blue-300 transition-all';
                el.innerHTML = `
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2 text-xs">
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 font-bold rounded">Câu ${idx + 1}</span>
                                <span class="text-gray-500 uppercase">${q.question_type === 'single_choice' ? '1 đáp án' : 'Nhiều đáp án'}</span>
                                <span class="text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded">${q.points} điểm</span>
                            </div>
                            <div class="font-medium text-gray-900 dark:text-white mb-2 line-clamp-2">${escape(q.question_text)}</div>
                            <div class="space-y-1 pl-1 border-l-2 border-gray-100 dark:border-gray-700">
                                ${(q.options || []).map(opt => `
                                    <div class="flex items-center gap-2 text-sm ${opt.is_correct ? 'text-green-600 font-medium' : 'text-gray-500'}">
                                        <svg class="w-3 h-3 ${opt.is_correct ? '' : 'text-gray-300'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            ${opt.is_correct ? '<path d="M5 13l4 4L19 7" stroke-width="2" />' : '<path d="M6 12h12" stroke-width="2" />'}
                                        </svg>
                                        <span>${escape(opt.option_text)}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <button type="button" onclick="QuizModal.editQuestion('${inst.id}', ${idx})" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button type="button" onclick="QuizModal.deleteQuestion('${inst.id}', ${idx})" class="p-1.5 text-red-500 hover:bg-red-50 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"></path></svg>
                            </button>
                        </div>
                    </div>
                `;
                list.appendChild(el);
            });
        }

        // --- Question Detail Modal Logic ---

        function addQuestion(id) {
            const inst = instances[id];
            inst.state.currentEditingIndex = -1;
            inst.state.tempQuestion = {
                question_text: '',
                question_type: 'single_choice',
                points: 1,
                explanation: '',
                options: [
                    { option_text: '', is_correct: true },
                    { option_text: '', is_correct: false }
                ]
            };
            renderQuestionModal(inst);
            inst.elements.questionModal.classList.remove('hidden');
        }

        function editQuestion(id, index) {
            const inst = instances[id];
            inst.state.currentEditingIndex = index;
            // Deep copy
            inst.state.tempQuestion = JSON.parse(JSON.stringify(inst.state.questions[index]));
            renderQuestionModal(inst);
            inst.elements.questionModal.classList.remove('hidden');
        }

        function deleteQuestion(id, index) {
            if (confirm('Xóa câu hỏi này?')) {
                instances[id].state.questions.splice(index, 1);
                renderQuestionsList(instances[id]);
            }
        }

        function renderQuestionModal(inst) {
            const q = inst.state.tempQuestion;
            inst.elements.questionModalTitle.textContent = inst.state.currentEditingIndex === -1 ? 'Thêm câu hỏi' : 'Chỉnh sửa câu hỏi';

            inst.elements.questionModalContent.innerHTML = `
                <div class="space-y-4 px-2">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-blue-700 dark:text-gray-300 mb-1">Loại câu hỏi</label>
                            <select onchange="QuizModal.updateTempType('${inst.id}', this.value)" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 outline-none">
                                <option value="single_choice" ${q.question_type === 'single_choice' ? 'selected' : ''}>Chọn 1 đáp án</option>
                                <option value="multiple_choice" ${q.question_type === 'multiple_choice' ? 'selected' : ''}>Chọn nhiều đáp án</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-blue-700 dark:text-gray-300 mb-1">Điểm</label>
                            <input type="number" min="1" value="${q.points}" onchange="QuizModal.getInstance('${inst.id}').state.tempQuestion.points = parseInt(this.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-blue-700 dark:text-gray-300 mb-1">Nội dung câu hỏi</label>
                        <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none" rows="3" oninput="QuizModal.getInstance('${inst.id}').state.tempQuestion.question_text = this.value">${escape(q.question_text)}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-semibold text-gray-700 dark:text-gray-300">Danh sách đáp án</label>
                            <button onclick="QuizModal.addOption('${inst.id}')" class="text-blue-600 font-semibold text-sm hover:underline">+ Thêm đáp án</button>
                        </div>
                        <div class="space-y-2">
                            ${q.options.map((opt, i) => `
                                <div class="flex items-center gap-2">
                                    <input type="${q.question_type === 'single_choice' ? 'radio' : 'checkbox'}"
                                        name="${inst.id}_correct_opt"
                                        ${opt.is_correct ? 'checked' : ''}
                                        onchange="QuizModal.updateOptionCorrect('${inst.id}', ${i})"
                                        class="w-4 h-4 text-green-600">
                                    <input type="text" value="${escape(opt.option_text)}" oninput="QuizModal.updateOptionText('${inst.id}', ${i}, this.value)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Nhập đáp án...">
                                    <button onclick="QuizModal.removeOption('${inst.id}', ${i})" class="text-red-400 p-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                                    </button>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    <div>
                         <label class="block font-semibold text-blue-700 dark:text-gray-300 mb-1">Giải thích (tùy chọn)</label>
                         <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none" rows="2" oninput="QuizModal.getInstance('${inst.id}').state.tempQuestion.explanation = this.value">${escape(q.explanation || '')}</textarea>
                    </div>
                </div>
            `;
        }

        // --- Save & Callback ---

        async function save(id) {
            const inst = instances[id];
            const title = inst.elements.quizTitle.value.trim();
            if (!title) {
                NotificationModal.show('Vui lòng nhập tiêu đề Quiz', 'error');
                return;
            }

            const payload = {
                title: title,
                description: inst.elements.quizDescription.value,
                start_at_seconds: inst.elements.quizPauseAt.value,
                passing_percent_score: inst.elements.quizPassingScore.value,
                is_required: inst.elements.quizIsRequired.checked,
                is_active: inst.elements.quizIsActive.checked,
                questions: inst.state.questions
            };

            // Toggle loading
            inst.elements.saveBtn.disabled = true;
            inst.elements.saveBtnLoading.classList.remove('hidden');

            try {
                if (inst.config.onSave) {
                    await inst.config.onSave(payload, inst.state.quizId);
                } else {
                    // Default API save if no callback
                    let method = inst.state.quizId ? 'PUT' : 'POST';
                    let url = inst.state.quizId ? `/lesson-quizzes/${inst.state.quizId}` : '/lesson-quizzes';
                    const res = await apiRequest(url, {
                        method: method,
                        body: JSON.stringify(payload)
                    });
                    if (res.success) {
                        NotificationModal.show('Lưu Quiz thành công!', 'success');
                        close(id);
                    } else {
                        throw new Error(res.message);
                    }
                }
            } catch (error) {
                console.error(error);
                NotificationModal.show(error.message || 'Lỗi khi lưu Quiz', 'error');
            } finally {
                inst.elements.saveBtn.disabled = false;
                inst.elements.saveBtnLoading.classList.add('hidden');
            }
        }

        // --- Helpers ---
        function formatSeconds(s) {
            const h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
            return (h > 0 ? h + ':' : '') + String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        }
        function escape(s) {
            if (!s) return '';
            return s.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
        }

        return {
            init,
            open,
            close,
            save,
            addQuestion,
            editQuestion,
            deleteQuestion,
            getInstance: (id) => instances[id],
            // Temp question helpers
            closeQuestionModal: (id) => instances[id].elements.questionModal.classList.add('hidden'),
            confirmSaveQuestion: (id) => {
                const inst = instances[id];
                const q = inst.state.tempQuestion;
                if (!q.question_text.trim()) return NotificationModal.show('Nhập câu hỏi', 'error');
                if (q.options.filter(o => o.option_text.trim()).length < 2) return NotificationModal.show('Cần ít nhất 2 đáp án', 'error');
                if (!q.options.some(o => o.is_correct)) return NotificationModal.show('Cần 1 đáp án đúng', 'error');

                if (inst.state.currentEditingIndex === -1) inst.state.questions.push(q);
                else inst.state.questions[inst.state.currentEditingIndex] = q;

                renderQuestionsList(inst);
                inst.elements.questionModal.classList.add('hidden');
            },
            updateTempType: (id, type) => {
                const inst = instances[id];
                inst.state.tempQuestion.question_type = type;
                if (type === 'single_choice') {
                    let done = false;
                    inst.state.tempQuestion.options.forEach(o => { if (o.is_correct && !done) done = true; else o.is_correct = false; });
                    if (!done && inst.state.tempQuestion.options.length) inst.state.tempQuestion.options[0].is_correct = true;
                }
                renderQuestionModal(inst);
            },
            addOption: (id) => {
                const inst = instances[id];
                if (inst.state.tempQuestion.options.length < 8) {
                    inst.state.tempQuestion.options.push({ option_text: '', is_correct: false });
                    renderQuestionModal(inst);
                }
            },
            removeOption: (id, i) => {
                const inst = instances[id];
                if (inst.state.tempQuestion.options.length > 2) {
                    inst.state.tempQuestion.options.splice(i, 1);
                    renderQuestionModal(inst);
                }
            },
            updateOptionText: (id, i, val) => { instances[id].state.tempQuestion.options[i].option_text = val; },
            updateOptionCorrect: (id, index) => {
                const inst = instances[id];
                const q = inst.state.tempQuestion;
                if (q.question_type === 'single_choice') {
                    q.options.forEach((o, i) => o.is_correct = (i === index));
                } else {
                    q.options[index].is_correct = !q.options[index].is_correct;
                }
                renderQuestionModal(inst);
            }
        };
    })();
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
</style>
@endpush
@endonce
