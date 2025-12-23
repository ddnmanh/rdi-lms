<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAttemptAnswer;
use App\Models\LessonQuizOption;
use App\Models\LessonQuizQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LessonQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Đang tạo dữ liệu quiz...');

        // 1. Lấy tất cả các bài học
        $lessons = Lesson::orderBy('id')->get();

        if ($lessons->isEmpty()) {
            $this->command->info('No lessons found, skipping LessonQuizSeeder.');
            return;
        }

        // 2. Lấy 5 học sinh đầu tiên (cố định)
        $students = User::whereHas('roles', function($query) {
            $query->where('name', 'STUDENT');
        })->orderBy('id')->take(5)->get();

        if ($students->isEmpty()) {
            $this->command->warn('⚠ Chưa có học sinh nào. Vui lòng chạy UserSeeder trước.');
            return;
        }

        // Lấy admin đầu tiên làm created_by
        $admin = User::whereHas('roles', function($query) {
            $query->where('name', 'ADMIN');
        })->orderBy('id')->first();

        if (!$admin) {
            $admin = User::orderBy('id')->first();
        }

        // ============================================
        // CẤU TRÚC DỮ LIỆU QUIZ THEO TỪNG LESSON
        // Key là title của lesson (phải khớp với LessonSeeder)
        // Mỗi lesson có thể có nhiều quiz (mảng quiz)
        // Mỗi quiz tối đa 4 câu hỏi
        // ============================================
        $quizzesByLesson = [
            'Tổng quan về React' => [
                // Quiz 1
                [
                    'title' => 'Components và Props',
                    'description' => 'Kiểm tra kiến thức về Components và Props trong React.',
                    'passing_percent_score' => 70,
                    'is_required' => true,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'Component trong React là gì?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'Component là các khối xây dựng độc lập, có thể tái sử dụng trong React. Chúng nhận input (props) và trả về React elements.',
                            'options' => [
                                ['option_text' => 'Là các hàm hoặc class trả về React elements', 'is_correct' => true],
                                ['option_text' => 'Là các biến JavaScript thông thường', 'is_correct' => false],
                                ['option_text' => 'Là các file CSS', 'is_correct' => false],
                                ['option_text' => 'Là các thư viện bên ngoài', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Props trong React dùng để làm gì?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'Props (properties) là cách để truyền dữ liệu từ component cha xuống component con. Props là read-only và không thể thay đổi bởi component con.',
                            'options' => [
                                ['option_text' => 'Truyền dữ liệu từ component cha xuống component con', 'is_correct' => true],
                                ['option_text' => 'Lưu trữ state của component', 'is_correct' => false],
                                ['option_text' => 'Xử lý events', 'is_correct' => false],
                                ['option_text' => 'Import các thư viện', 'is_correct' => false],
                            ],
                        ]
                    ],
                ],
                // Quiz 2
                [
                    'title' => 'State và Hooks',
                    'description' => 'Kiểm tra kiến thức về State và React Hooks.',
                    'passing_percent_score' => 70,
                    'is_required' => false,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'Hook useState() được dùng để làm gì?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'useState là một React Hook cho phép bạn thêm state vào functional components. Nó trả về một mảng với 2 phần tử: giá trị state hiện tại và hàm để cập nhật state.',
                            'options' => [
                                ['option_text' => 'Thêm state vào functional components', 'is_correct' => true],
                                ['option_text' => 'Tạo component mới', 'is_correct' => false],
                                ['option_text' => 'Import thư viện', 'is_correct' => false],
                                ['option_text' => 'Xử lý events', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Khi nào component sẽ re-render?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'Component sẽ re-render khi state hoặc props thay đổi. React tự động cập nhật UI khi có thay đổi.',
                            'options' => [
                                ['option_text' => 'Khi state hoặc props thay đổi', 'is_correct' => true],
                                ['option_text' => 'Mỗi giây một lần', 'is_correct' => false],
                                ['option_text' => 'Chỉ khi component được mount lần đầu', 'is_correct' => false],
                                ['option_text' => 'Không bao giờ re-render', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'State trong React khác với Props như thế nào? (Chọn nhiều đáp án)',
                            'question_type' => 'multiple_choice',
                            'points' => 25,
                            'explanation' => 'State là dữ liệu có thể thay đổi trong component, trong khi Props là read-only. State được quản lý bởi component, Props được truyền từ bên ngoài.',
                            'options' => [
                                ['option_text' => 'State có thể thay đổi, Props là read-only', 'is_correct' => true],
                                ['option_text' => 'State được quản lý bởi component, Props được truyền từ bên ngoài', 'is_correct' => true],
                                ['option_text' => 'State và Props giống hệt nhau', 'is_correct' => false],
                                ['option_text' => 'Props có thể thay đổi, State là read-only', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Có thể sử dụng useState nhiều lần trong một component không?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'Có thể sử dụng useState nhiều lần trong một component để quản lý nhiều state khác nhau.',
                            'options' => [
                                ['option_text' => 'Có, có thể sử dụng nhiều lần', 'is_correct' => true],
                                ['option_text' => 'Không, chỉ được dùng một lần', 'is_correct' => false],
                                ['option_text' => 'Chỉ được dùng trong class components', 'is_correct' => false],
                                ['option_text' => 'Không thể dùng useState', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
            'React Router' => [
                // Quiz 1
                [
                    'title' => 'Routing Cơ bản',
                    'description' => 'Kiểm tra kiến thức về routing cơ bản trong React Router.',
                    'passing_percent_score' => 70,
                    'is_required' => true,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'React Router được dùng để làm gì?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'React Router là thư viện routing cho React, cho phép tạo single-page applications với navigation giữa các views khác nhau.',
                            'options' => [
                                ['option_text' => 'Quản lý routing và navigation trong React app', 'is_correct' => true],
                                ['option_text' => 'Quản lý state của ứng dụng', 'is_correct' => false],
                                ['option_text' => 'Xử lý API calls', 'is_correct' => false],
                                ['option_text' => 'Styling components', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Component nào được dùng để định nghĩa routes trong React Router v6?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'Trong React Router v6, Routes và Route được dùng để định nghĩa routing. BrowserRouter là component wrapper.',
                            'options' => [
                                ['option_text' => 'Routes và Route', 'is_correct' => true],
                                ['option_text' => 'Router và Route', 'is_correct' => false],
                                ['option_text' => 'Switch và Route', 'is_correct' => false],
                                ['option_text' => 'Link và NavLink', 'is_correct' => false],
                            ],
                        ]
                    ],
                ],
                // Quiz 2
                [
                    'title' => 'Navigation và Dynamic Routes',
                    'description' => 'Kiểm tra kiến thức về navigation và dynamic routes.',
                    'passing_percent_score' => 70,
                    'is_required' => false,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'Dynamic route trong React Router được định nghĩa như thế nào?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'Dynamic routes sử dụng dấu : để định nghĩa parameter, ví dụ /user/:id. Giá trị có thể truy cập qua useParams hook.',
                            'options' => [
                                ['option_text' => 'Sử dụng dấu : trong path, ví dụ /user/:id', 'is_correct' => true],
                                ['option_text' => 'Sử dụng dấu * trong path', 'is_correct' => false],
                                ['option_text' => 'Sử dụng dấu ? trong path', 'is_correct' => false],
                                ['option_text' => 'Không thể tạo dynamic routes', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'useParams hook trả về gì?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'useParams trả về một object chứa các route parameters từ URL hiện tại.',
                            'options' => [
                                ['option_text' => 'Object chứa các route parameters', 'is_correct' => true],
                                ['option_text' => 'Function để navigate', 'is_correct' => false],
                                ['option_text' => 'Current location object', 'is_correct' => false],
                                ['option_text' => 'Array của routes', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Các cách để navigate trong React Router? (Chọn nhiều đáp án)',
                            'question_type' => 'multiple_choice',
                            'points' => 25,
                            'explanation' => 'Có nhiều cách để navigate: Link component cho user click, useNavigate hook cho programmatic navigation, và NavLink cho active states.',
                            'options' => [
                                ['option_text' => 'Sử dụng Link component', 'is_correct' => true],
                                ['option_text' => 'Sử dụng useNavigate hook', 'is_correct' => true],
                                ['option_text' => 'Sử dụng window.location', 'is_correct' => false],
                                ['option_text' => 'Sử dụng document.location', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'NavLink khác với Link như thế nào?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'NavLink giống Link nhưng có thêm khả năng tự động thêm class "active" khi route hiện tại khớp với link.',
                            'options' => [
                                ['option_text' => 'NavLink tự động thêm class "active" khi route khớp', 'is_correct' => true],
                                ['option_text' => 'NavLink không thể navigate', 'is_correct' => false],
                                ['option_text' => 'NavLink chỉ dùng cho external links', 'is_correct' => false],
                                ['option_text' => 'Không có sự khác biệt', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
            'Tích hợp API' => [
                // Quiz 1
                [
                    'title' => 'Fetch API và useEffect',
                    'description' => 'Kiểm tra kiến thức về fetch API và useEffect trong React.',
                    'passing_percent_score' => 70,
                    'is_required' => true,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'Cách tốt nhất để fetch data từ API trong React?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'useEffect hook được dùng để fetch data khi component mount. Fetch API hoặc axios có thể được sử dụng để gọi API.',
                            'options' => [
                                ['option_text' => 'Sử dụng useEffect với fetch hoặc axios', 'is_correct' => true],
                                ['option_text' => 'Gọi API trực tiếp trong render', 'is_correct' => false],
                                ['option_text' => 'Sử dụng useState để fetch', 'is_correct' => false],
                                ['option_text' => 'Không thể fetch API trong React', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Tại sao cần cleanup function trong useEffect khi fetch API?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'Cleanup function giúp cancel request nếu component unmount trước khi request hoàn thành, tránh memory leaks và warnings.',
                            'options' => [
                                ['option_text' => 'Để cancel request nếu component unmount', 'is_correct' => true],
                                ['option_text' => 'Để tăng tốc độ request', 'is_correct' => false],
                                ['option_text' => 'Để lưu trữ data', 'is_correct' => false],
                                ['option_text' => 'Không cần cleanup function', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'useEffect dependency array rỗng [] có nghĩa là gì?',
                            'question_type' => 'single_choice',
                            'points' => 20,
                            'explanation' => 'Dependency array rỗng [] có nghĩa là effect chỉ chạy một lần khi component mount, không chạy lại khi re-render.',
                            'options' => [
                                ['option_text' => 'Effect chỉ chạy một lần khi component mount', 'is_correct' => true],
                                ['option_text' => 'Effect chạy mỗi lần render', 'is_correct' => false],
                                ['option_text' => 'Effect không bao giờ chạy', 'is_correct' => false],
                                ['option_text' => 'Effect chạy khi state thay đổi', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
                // Quiz 2
                [
                    'title' => 'Error Handling và Loading States',
                    'description' => 'Kiểm tra kiến thức về xử lý lỗi và loading states.',
                    'passing_percent_score' => 70,
                    'is_required' => false,
                    'start_at_seconds' => null,
                    'max_questions' => 4,
                    'questions' => [
                        [
                            'question_text' => 'Các cách xử lý loading và error state khi fetch API? (Chọn nhiều đáp án)',
                            'question_type' => 'multiple_choice',
                            'points' => 25,
                            'explanation' => 'Cần quản lý loading state để hiển thị spinner, error state để hiển thị lỗi, và data state để hiển thị kết quả.',
                            'options' => [
                                ['option_text' => 'Sử dụng state để track loading và error', 'is_correct' => true],
                                ['option_text' => 'Hiển thị loading spinner khi đang fetch', 'is_correct' => true],
                                ['option_text' => 'Hiển thị error message nếu có lỗi', 'is_correct' => true],
                                ['option_text' => 'Không cần xử lý loading và error', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Async/await trong fetch API dùng để làm gì?',
                            'question_type' => 'single_choice',
                            'points' => 25,
                            'explanation' => 'Async/await giúp viết code bất đồng bộ một cách đồng bộ, dễ đọc hơn so với promises với .then().',
                            'options' => [
                                ['option_text' => 'Viết code bất đồng bộ một cách dễ đọc hơn', 'is_correct' => true],
                                ['option_text' => 'Làm code chạy nhanh hơn', 'is_correct' => false],
                                ['option_text' => 'Import thư viện', 'is_correct' => false],
                                ['option_text' => 'Tạo component', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ]
        ];

        // Template quiz mặc định cho các lesson khác (1 quiz)
        $defaultQuizTemplate = [
            [
                'title' => 'Bài kiểm tra kiến thức',
                'description' => 'Bài kiểm tra này giúp bạn củng cố kiến thức vừa học. Vui lòng hoàn thành để tiếp tục.',
                'passing_percent_score' => 60,
                'is_required' => true,
                'start_at_seconds' => null,
                'max_questions' => 4,
                'questions' => [
                    [
                        'question_text' => 'Đâu là đặc điểm chính của bài học này?',
                    'question_type' => 'single_choice',
                    'points' => 20,
                    'explanation' => 'Giải thích: Đây là kiến thức cơ bản cần nắm vững. Tham khảo tài liệu chính thức để biết thêm chi tiết.',
                    'options' => [
                        ['option_text' => 'Đây là đáp án chính xác', 'is_correct' => true],
                        ['option_text' => 'Đây là phương án sai', 'is_correct' => false],
                        ['option_text' => 'Không có đáp án nào đúng', 'is_correct' => false],
                        ['option_text' => 'Tất cả các đáp án trên đều đúng', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Kết quả của đoạn lệnh sau là gì?',
                    'question_type' => 'single_choice',
                    'points' => 20,
                    'explanation' => 'Giải thích: Hãy xem xét kỹ logic của đoạn code.',
                    'options' => [
                        ['option_text' => 'Đáp án A - Đúng', 'is_correct' => true],
                        ['option_text' => 'Đáp án B - Sai', 'is_correct' => false],
                        ['option_text' => 'Đáp án C - Sai', 'is_correct' => false],
                        ['option_text' => 'Đáp án D - Sai', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Tại sao chúng ta nên sử dụng kỹ thuật này? (Chọn nhiều đáp án)',
                    'question_type' => 'multiple_choice',
                    'points' => 20,
                    'explanation' => 'Giải thích: Kỹ thuật này có nhiều ưu điểm.',
                    'options' => [
                        ['option_text' => 'Ưu điểm 1', 'is_correct' => true],
                        ['option_text' => 'Ưu điểm 2', 'is_correct' => true],
                        ['option_text' => 'Nhược điểm', 'is_correct' => false],
                        ['option_text' => 'Không liên quan', 'is_correct' => false],
                    ],
                ],
                [
                    'question_text' => 'Hàm nào được dùng trong bài học này?',
                    'question_type' => 'single_choice',
                    'points' => 20,
                    'explanation' => 'Giải thích: Có nhiều hàm được sử dụng trong bài học.',
                    'options' => [
                        ['option_text' => 'Hàm chính xác', 'is_correct' => true],
                        ['option_text' => 'Hàm sai', 'is_correct' => false],
                        ['option_text' => 'Hàm không liên quan', 'is_correct' => false],
                        ['option_text' => 'Không có hàm nào', 'is_correct' => false],
                    ],
                ],
                ],
            ],
        ];

        DB::transaction(function () use ($lessons, $students, $admin, $quizzesByLesson, $defaultQuizTemplate) {
            foreach ($lessons as $lessonIndex => $lesson) {
                // Kiểm tra xem lesson có quiz riêng không
                // $quizzesByLesson có thể là mảng quiz hoặc mảng quiz đơn
                $quizzes = $quizzesByLesson[$lesson->title] ?? $defaultQuizTemplate;

                // Đảm bảo $quizzes là mảng các quiz
                if (!isset($quizzes[0]) || !is_array($quizzes[0])) {
                    $quizzes = [$quizzes]; // Wrap thành mảng nếu chỉ có 1 quiz
                }

                // Tạo từng quiz cho lesson
                foreach ($quizzes as $quizIndex => $quizTemplate) {
                    $quiz = LessonQuiz::firstOrCreate(
                        [
                            'lesson_id' => $lesson->id,
                            'title' => $quizTemplate['title'],
                        ],
                        [
                            'description' => $quizTemplate['description'],
                            'passing_percent_score' => $quizTemplate['passing_percent_score'],
                            'is_required' => $quizTemplate['is_required'],
                            'start_at_seconds' => $quizTemplate['start_at_seconds'],
                            'max_questions' => $quizTemplate['max_questions'],
                            'is_active' => true,
                            'created_by' => $admin->id,
                            'updated_by' => $admin->id,
                        ]
                    );

                    // Tạo các câu hỏi từ template
                    foreach ($quizTemplate['questions'] as $qIndex => $questionData) {
                        $question = LessonQuizQuestion::firstOrCreate(
                            [
                                'quiz_id' => $quiz->id,
                                'display_order' => $qIndex + 1,
                            ],
                            [
                                'question_text' => $questionData['question_text'],
                                'question_type' => $questionData['question_type'],
                                'points' => $questionData['points'],
                                'explanation' => $questionData['explanation'],
                            ]
                        );

                        // Tạo các lựa chọn từ template
                        foreach ($questionData['options'] as $oIndex => $optionData) {
                            LessonQuizOption::firstOrCreate(
                                [
                                    'question_id' => $question->id,
                                    'display_order' => $oIndex + 1,
                                ],
                                [
                                    'option_text' => $optionData['option_text'],
                                    'is_correct' => $optionData['is_correct'],
                                ]
                            );
                        }
                    }

                    // Tạo attempts cho học sinh (cố định: mỗi học sinh làm 60% quiz)
                    foreach ($students as $studentIndex => $student) {
                        // Quyết định học sinh có làm quiz này không (dựa trên index)
                        $shouldAttempt = (($lessonIndex * count($students) + $quizIndex * count($students) + $studentIndex) % 10) < 6; // 60%

                        if ($shouldAttempt) {
                            // Tính thời gian cố định
                            $daysAgo = ($lessonIndex * count($students) + $quizIndex * count($students) + $studentIndex) % 30;
                            $startedAt = Carbon::now()->subDays($daysAgo);
                            $minutesToComplete = 5 + (($lessonIndex * count($students) + $quizIndex * count($students) + $studentIndex) % 15); // 5-20 phút
                            $completedAt = $startedAt->copy()->addMinutes($minutesToComplete);

                            // Tính điểm cố định (70-90% đúng)
                            $questions = $quiz->questions()->orderBy('id')->get();
                            $maxPoints = $questions->sum('points');
                            $pointsEarnedTotal = (int)($maxPoints * (0.7 + (($lessonIndex * count($students) + $quizIndex * count($students) + $studentIndex) % 20) / 100)); // 70-90%

                            $attempt = LessonQuizAttempt::firstOrCreate(
                                [
                                    'quiz_id' => $quiz->id,
                                    'user_id' => $student->id,
                                ],
                                [
                                    'max_possible_score' => $maxPoints,
                                    'score_earned' => $pointsEarnedTotal,
                                    'percent_score_earned' => ($maxPoints > 0) ? ($pointsEarnedTotal / $maxPoints) * 10 : 0,
                                    'passed' => $pointsEarnedTotal >= ($maxPoints * ($quiz->passing_percent_score / 100)),
                                    'started_at' => $startedAt,
                                    'completed_at' => $completedAt,
                                ]
                            );

                            // Tạo answers cho từng câu hỏi
                            foreach ($questions as $qIdx => $q) {
                                // Quyết định đúng/sai cố định (80% đúng)
                                $isCorrect = (($lessonIndex * count($students) * 4 + $quizIndex * count($students) * 4 + $studentIndex * 4 + $qIdx) % 10) < 8;
                                $points = $isCorrect ? $q->points : 0;

                                // Chọn đáp án
                                $selectedIds = [];
                                if ($isCorrect) {
                                    $correctOption = $q->correctOptions()->first();
                                    if ($correctOption) {
                                        $selectedIds = [$correctOption->id];
                                    }
                                } else {
                                    // Chọn đáp án sai đầu tiên
                                    $wrongOption = $q->options()->where('is_correct', false)->first();
                                    if ($wrongOption) {
                                        $selectedIds = [$wrongOption->id];
                                    }
                                }

                                LessonQuizAttemptAnswer::firstOrCreate(
                                    [
                                        'attempt_id' => $attempt->id,
                                        'question_id' => $q->id,
                                    ],
                                    [
                                        'selected_option_ids' => $selectedIds,
                                        'is_correct' => $isCorrect,
                                        'score_earned' => $points,
                                    ]
                                );
                            }

                            // Cập nhật lại attempt với tổng điểm thực tế
                            $actualPoints = $attempt->answers()->sum('score_earned');
                            $attempt->update([
                                'score_earned' => $actualPoints,
                                'percent_score_earned' => ($maxPoints > 0) ? ($actualPoints / $maxPoints) * 10 : 0,
                                'passed' => $actualPoints >= ($maxPoints * ($quiz->passing_percent_score / 100)),
                            ]);
                        }
                    }
                }
            }
        });

        $this->command->info("✓ Hoàn thành! Đã tạo dữ liệu quiz.");
    }
}
