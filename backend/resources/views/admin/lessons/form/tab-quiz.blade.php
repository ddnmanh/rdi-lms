<div class="h-full flex flex-col gap-4">
    <div class="w-full px-3 flex flex-row items-start justify-between gap-10 flex-shrink-0">
        <div class="flex-1">
            {{-- Video Timeline with Quiz Markers --}}
            <div id="quizTimelineContainer" class="w-full flex-shrink-0 hidden">
                <div class="relative">
                    <div id="quizTimelineTrack" class="relative h-8 bg-gray-200 dark:bg-gray-700 rounded-full overflow-visible">
                        {{-- Timeline markers sẽ được render ở đây --}}
                    </div>
                    <div id="quizTimelineLabels" class="mt-2 flex justify-between  text-gray-500 dark:text-gray-400">
                        <span>0:00</span>
                        <span id="videoDurationEndLabel">0:00</span>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" onclick="openQuizModal()"
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-medium flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                <path d="M256 64c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 160-160 0c-17.7 0-32 14.3-32 32s14.3 32 32 32l160 0 0 160c0 17.7 14.3 32 32 32s32-14.3 32-32l0-160 160 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-160 0 0-160z"/>
            </svg>
            <span>Thêm Quiz</span>
        </button>
    </div>

    {{-- Video Timeline with Quiz Markers --}}
    <div id="quizTimelineContainer" class="w-full flex-shrink-0 p-4 hidden">
        <div class="relative">
            <div id="quizTimelineTrack" class="relative h-8 bg-gray-200 dark:bg-gray-700 rounded-full overflow-visible">
                {{-- Timeline markers sẽ được render ở đây --}}
            </div>
            <div id="quizTimelineLabels" class="mt-2 flex justify-between  text-gray-500 dark:text-gray-400">
                <span>0:00</span>
                <span id="videoDurationEndLabel">0:00</span>
            </div>
        </div>
    </div>

    <!-- Quiz List -->
    <div id="quizList" class="w-full p-2 flex-1 min-h-0 space-y-3 overflow-y-auto">
        <!-- Quiz items will be rendered here -->
    </div>

    <!-- Empty State -->
    <div id="quizEmptyState"
        class="hidden flex flex-1 min-h-0 items-center justify-center p-8 text-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
        <div>
            <svg class="mx-auto w-12 h-12 text-gray-400" viewBox="0 0 512 512" fill="currentColor">
                <path
                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
            </svg>
            <p class="mt-4 text-gray-500 dark:text-gray-400">Chưa có quiz nào</p>
            <p class=" text-gray-400 dark:text-gray-500">Nhấn "Thêm Quiz" để tạo bài tập trắc nghiệm</p>
        </div>
    </div>
</div>

<x-quiz-modal id="lessonQuizModal" title="Cấu hình Quiz cho bài học" />

<script>
    // ========================================
    // QUẢN LÝ QUIZ TRONG LESSON
    // ========================================

    let quizzes = []; // Danh sách quiz của lesson

    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo QuizModal component
        QuizModal.init('lessonQuizModal', {
            onSave: async (payload, quizId) => {
                payload.lesson_id = lessonId;
                let method = quizId ? 'PUT' : 'POST';
                let url = quizId ? `/lesson-quizzes/${quizId}` : '/lesson-quizzes';

                const res = await apiRequest(url, {
                    method: method,
                    body: JSON.stringify(payload)
                });

                if (res.success) {
                    NotificationModal.show('Lưu Quiz thành công!', 'success');
                    QuizModal.close('lessonQuizModal');
                    await loadQuizzes(); // Refresh danh sách
                } else {
                    throw new Error(res.message);
                }
            }
        });

        // Load ban đầu nếu ở mode EDIT
        if (typeof mode !== 'undefined' && mode === 'EDIT' && typeof lessonId !== 'undefined' && lessonId) {
            loadQuizzes();
        }
    });

    /**
     * Load danh sách quiz của lesson
     */
    async function loadQuizzes() {
        if (!lessonId) return;

        try {
            const data = await apiRequest(`/lesson-quizzes/lesson/${lessonId}`);
            if (data?.success) {
                quizzes = data.data || [];
                renderQuizList();
            }
        } catch (error) {
            console.error('Error loading quizzes:', error);
        }
    }

    /**
     * Mở modal thêm quiz mới
     */
    function openQuizModal() {
        QuizModal.open('lessonQuizModal', {
            mode: 'CREATE',
            pauseAt: 0,
            lessonData: typeof window.lessonData !== 'undefined' ? window.lessonData : null
        });
    }

    /**
     * Mở modal chỉnh sửa thông tin quiz
     */
    async function openEditQuizModal(quizId) {
        QuizModal.open('lessonQuizModal', {
            quizId: quizId,
            lessonData: typeof window.lessonData !== 'undefined' ? window.lessonData : null
        });
    }

    /**
     * Render danh sách quiz ra UI
     */
    function renderQuizList() {
        const listEl = document.getElementById('quizList');
        const emptyEl = document.getElementById('quizEmptyState');
        const tabCountEl = document.getElementById('quizTabCount');

        if (!listEl || !emptyEl) return;

        // Update tab count
        if (tabCountEl) tabCountEl.textContent = quizzes.length;

        if (quizzes.length === 0) {
            listEl.innerHTML = '';
            emptyEl.classList.remove('hidden');
            renderQuizTimeline(); // Ẩn timeline nếu không có quiz
            return;
        }

        emptyEl.classList.add('hidden');
        renderQuizTimeline();

        listEl.innerHTML = quizzes.map((quiz, index) => {
            const duration = window.lessonData?.duration || 1;
            const isOverVideoDuration = (quiz.start_at_seconds / duration) * 100 > 100;

            return `
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-900/40 transition-all ${isOverVideoDuration ? 'bg-red-50 dark:bg-red-900/40 border-red-200' : ''}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="font-bold text-blue-700 dark:text-blue-300">#${index + 1}</span>
                                <span class="font-medium text-gray-900 dark:text-white truncate">
                                    ${escapeHtml_Local(quiz.title || `Quiz #${index + 1}`)}
                                </span>
                                ${quiz.is_active
                                    ? '<span class="px-2 py-0.5 text-[10px] bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300 rounded-full font-bold uppercase tracking-wider">Active</span>'
                                    : '<span class="px-2 py-0.5 text-[10px] bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300 rounded-full uppercase tracking-wider">Inactive</span>'
                                }
                                ${quiz.is_required
                                    ? '<span class="px-2 py-0.5 text-[10px] bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300 rounded-full font-bold uppercase tracking-wider">Bắt buộc</span>'
                                    : ''
                                }
                            </div>
                            <div class="flex items-center gap-4  text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 512 512" fill="currentColor"><path d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z"/></svg>
                                    ${quiz.start_at_seconds ? formatSecondsToHHMMSS_Global(quiz.start_at_seconds, false) : '0:00'}
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 512 512" fill="currentColor"><path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM169.8 165.3c7.9-22.3 29.1-37.3 52.8-37.3h58.3c34.9 0 63.1 28.3 63.1 63.1c0 22.6-12.1 43.5-31.7 54.8L280 264.4c-.2 13-10.9 23.6-24 23.6c-13.3 0-24-10.7-24-24V250.5c0-8.6 4.6-16.5 12.1-20.8l44.3-25.4c4.7-2.7 7.6-7.7 7.6-13.1c0-8.4-6.8-15.1-15.1-15.1H222.6c-3.4 0-6.4 2.1-7.5 5.3l-.4 1.2c-4.4 12.5-18.2 19-30.6 14.6s-19-18.2-14.6-30.6l.4-1.2zM224 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/></svg>
                                    ${quiz.questions?.length || 0} câu hỏi
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-green-500" viewBox="0 0 512 512" fill="currentColor"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>
                                    Cần đạt: ${quiz.passing_percent_score}%
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openEditQuizModal(${quiz.id})"
                                class="inline-flex size-8 items-center justify-center rounded-md border border-amber-500 text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all"
                                title="Chỉnh sửa">
                                <svg class="w-4 h-4" viewBox="0 0 512 512" fill="currentColor"><path d="M352.9 21.2L308 66.1 445.9 204 490.8 159.1C504.4 145.6 512 127.2 512 108s-7.6-37.6-21.2-51.1L455.1 21.2C441.6 7.6 423.2 0 404 0s-37.6 7.6-51.1 21.2zM274.1 100L58.9 315.1c-10.7 10.7-18.5 24.1-22.6 38.7L.9 481.6c-2.3 8.3 0 17.3 6.2 23.4s15.1 8.5 23.4 6.2l127.8-35.5c14.6-4.1 27.9-11.8 38.7-22.6L412 237.9 274.1 100z"/></svg>
                            </button>
                            <button type="button" onclick="deleteQuiz(${quiz.id})"
                                class="inline-flex size-8 items-center justify-center rounded-md border border-red-500 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all"
                                title="Xóa">
                                <svg class="w-4 h-4" viewBox="0 0 448 512" fill="currentColor"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                            </button>
                             <button type="button" onclick="toggleQuizQuestions(${quiz.id})"
                                class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                <span id="toggleIconQuiz${quiz.id}" class="transition-transform duration-300">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 448 512" fill="currentColor"><path d="M201.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L224 338.7 54.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div id="quizQuestionsDetail${quiz.id}" class="hidden mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        ${(quiz.questions && quiz.questions.length > 0) ? quiz.questions.map((q, qIdx) => `
                            <div class="py-3 last:pb-0 border-b last:border-0 border-gray-100 dark:border-gray-800">
                                <div class="font-medium text-gray-900 dark:text-gray-100 flex gap-2">
                                    <span class="text-blue-500 font-bold shrink-0">${qIdx + 1}.</span>
                                    <span>${escapeHtml_Local(q.question_text)}</span>
                                    <span class="px-2 py-0.5 text-[10px] bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300 rounded-full font-bold uppercase tracking-wider">
                                        ${q.points} điểm
                                    </span>
                                </div>
                                <div class="mt-2 pl-6 space-y-1">
                                    ${(q.options || []).map(opt => `
                                        <div class="flex items-center gap-2  ${opt.is_correct ? 'text-green-600 font-bold' : 'text-gray-500'}">
                                            <div class="w-4 h-4 flex items-center justify-center shrink-0">
                                                ${opt.is_correct ? '<svg class="w-3 h-3" viewBox="0 0 448 512" fill="currentColor"><path d="M470.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L192 338.7 425.4 105.4c12.5-12.5 32.8-12.5 45.3 0z"/></svg>' : '•'}
                                            </div>
                                            <span>${escapeHtml_Local(opt.option_text)}</span>
                                        </div>
                                    `).join('')}
                                    ${q.explanation ? `
                                        <p class="mt-2  text-gray-500 dark:text-gray-400 italic">
                                            <svg class="inline w-3 h-3 mr-1" viewBox="0 0 512 512" fill="currentColor">
                                                <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM216 336h24V272H216c-13.3 0-24-10.7-24-24s10.7-24 24-24h48c13.3 0 24 10.7 24 24v88h8c13.3 0 24 10.7 24 24s-10.7 24-24 24H216c-13.3 0-24-10.7-24-24s10.7-24 24-24zm40-208a32 32 0 1 1 0 64 32 32 0 1 1 0-64z"/>
                                            </svg>
                                            ${escapeHtml_Global(q.explanation)}
                                        </p>
                                    ` : ''}
                                </div>
                            </div>
                        `).join('') : '<div class="text-center py-2 text-gray-400 italic">Chưa có câu hỏi</div>'}
                    </div>
                </div>
            `;
        }).join('');


    }

    /**
     * Delete quiz
     */
    async function deleteQuiz(quizId) {
        const quiz = quizzes.find(q => q.id === quizId);
        if (!quiz) return;

        DeleteModal.openSingle({
            objectName: 'Quiz',
            title: 'Xóa Quiz',
            nameValue: quiz.title || `Quiz #${quizzes.indexOf(quiz) + 1}`,
            descValue: `${quiz.questions?.length || 0} câu hỏi`,
            actionFuncCallback: async () => {
                const response = await apiRequest(`/lesson-quizzes/${quizId}`, { method: 'DELETE' });
                return response?.success;
            },
            successFuncCallback: async () => {
                NotificationModal.show('Đã xóa quiz thành công', 'success');
                await loadQuizzes();
            }
        });
    }

    /**
     * Render timeline markers for quizzes
     */
    function renderQuizTimeline() {
        const timelineContainer = document.getElementById('quizTimelineContainer');
        const timelineTrack = document.getElementById('quizTimelineTrack');
        const durationEndLabel = document.getElementById('videoDurationEndLabel');

        if (!timelineContainer || !timelineTrack) return;

        const videoDuration = window.lessonData?.duration || 0;
        const validQuizzes = quizzes.filter(q => q.start_at_seconds != null && q.start_at_seconds > 0);

        if (videoDuration <= 0 || validQuizzes.length === 0) {
            timelineContainer.classList.add('hidden');
            return;
        }

        timelineContainer.classList.remove('hidden');
        durationEndLabel.textContent = formatSecondsToHHMMSS_Global(videoDuration, false);

        timelineTrack.innerHTML = validQuizzes.map((quiz) => {
            const positionPercent = (quiz.start_at_seconds / videoDuration) * 100;
            if (positionPercent > 100) return '';

            const displayIndex = quizzes.findIndex(q => q.id === quiz.id) + 1;

            return `
                <div
                    class="absolute top-1/2 -translate-y-1/2 -translate-x-1/2 group cursor-pointer"
                    style="left: ${positionPercent}%"
                    data-quiz-index="${displayIndex-1}"
                >
                    <div class="w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white dark:border-gray-800 shadow-sm transition-transform group-hover:scale-125"></div>
                    <div class="absolute top-0 left-0 translate-x-[calc(-100%-4px)] translate-y-[-25%] hidden group-hover:block z-20">
                        <div class="bg-gray-900 text-white text-[10px] px-2 py-1 rounded whitespace-nowrap shadow-xl">
                            #${displayIndex}: ${formatSecondsToHHMMSS_Global(quiz.start_at_seconds, false)}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        // Thêm click handler để scroll đến quiz tương ứng
        timelineTrack.querySelectorAll('[data-quiz-index]').forEach(marker => {
            marker.addEventListener('click', (e) => {
                const quizIndex = parseInt(marker.getAttribute('data-quiz-index'));
                const quizElements = document.querySelectorAll('#quizList > div');
                if (quizElements[quizIndex]) {
                    quizElements[quizIndex].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Highlight quiz card
                    quizElements[quizIndex].classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
                    setTimeout(() => {
                        quizElements[quizIndex].classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
                    }, 1000);
                }
            });
        });
    }

    function toggleQuizQuestions(quizId) {
        const detailEl = document.getElementById(`quizQuestionsDetail${quizId}`);
        const iconEl = document.getElementById(`toggleIconQuiz${quizId}`);
        if (!detailEl || !iconEl) return;
        const isHidden = detailEl.classList.contains('hidden');
        if (isHidden) {
            detailEl.classList.remove('hidden');
            iconEl.classList.add('rotate-180');
        } else {
            detailEl.classList.add('hidden');
            iconEl.classList.remove('rotate-180');
        }
    }

    function escapeHtml_Local(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
</script>
