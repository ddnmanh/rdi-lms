import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import '../../../../data/models/quiz_model.dart';

class QuizOverlay extends StatefulWidget {
  final Quiz quiz;
  final Function(Map<int, List<int>>)
  onClose; // Return current answers on close
  final VoidCallback? onRetry;
  final Function(List<Map<String, dynamic>>) onSubmit;
  final bool isSubmitting;
  final String? error;
  final bool? isPassed;
  final Map<int, List<int>>? initialAnswers;

  const QuizOverlay({
    Key? key,
    required this.quiz,
    required this.onClose,
    this.onRetry,
    required this.onSubmit,
    this.isSubmitting = false,
    this.error,
    this.isPassed,
    this.initialAnswers,
  }) : super(key: key);

  @override
  State<QuizOverlay> createState() => _QuizOverlayState();
}

class _QuizOverlayState extends State<QuizOverlay> {
  PageController _pageController = PageController();
  final Map<int, List<int>> _answers = {}; // QuestionID -> List<OptionID>
  int _currentIndex = 0;

  @override
  void initState() {
    super.initState();
    if (widget.initialAnswers != null) {
      _answers.addAll(widget.initialAnswers!);
    }
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  bool get _canSubmit {
    // Check if all questions are answered
    for (var q in widget.quiz.questions) {
      if (!_answers.containsKey(q.id) || _answers[q.id]!.isEmpty) {
        return false;
      }
    }
    return true;
  }

  @override
  void didUpdateWidget(QuizOverlay oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.isPassed == null && oldWidget.isPassed != null) {
      // Reset for retry
      setState(() {
        _answers.clear();
        _currentIndex = 0;
        _pageController =
            PageController(); // Recreate or just rely on new build
        // Actually, just changing _currentIndex is enough for the state
        // The PageView will be rebuilt since it wasn't in the tree
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    // Result View
    if (widget.isPassed != null) {
      return _buildResultView();
    }

    return Container(
      color: Colors.black.withOpacity(0.9),
      child: SafeArea(
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: PageView.builder(
                controller: _pageController,
                physics:
                    const NeverScrollableScrollPhysics(), // Require next button
                itemCount: widget.quiz.questions.length,
                onPageChanged: (index) {
                  setState(() => _currentIndex = index);
                },
                itemBuilder: (context, index) {
                  return _buildQuestionView(widget.quiz.questions[index]);
                },
              ),
            ),
            _buildFooter(),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Row(
        children: [
          // Always show close button to allow review/exit
          CloseButton(
            color: Colors.white,
            onPressed: () => widget.onClose(_answers),
          ),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  widget.quiz.title,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Text(
                  'Câu hỏi ${_currentIndex + 1}/${widget.quiz.questions.length}',
                  style: const TextStyle(color: Colors.grey),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildQuestionView(Question question) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            question.questionText,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 16,
              fontWeight: FontWeight.w500,
            ),
          ),
          if (question.isMultipleChoice)
            const Padding(
              padding: EdgeInsets.only(top: 8.0),
              child: Text(
                '(Chọn nhiều đáp án)',
                style: TextStyle(color: Colors.blueAccent, fontSize: 12),
              ),
            ),
          const SizedBox(height: 20),
          ...question.options.map(
            (option) => _buildOptionItem(question, option),
          ),
        ],
      ),
    );
  }

  Widget _buildOptionItem(Question question, Option option) {
    final selectedOptions = _answers[question.id] ?? [];
    final isSelected = selectedOptions.contains(option.id);

    return GestureDetector(
      onTap: () {
        setState(() {
          if (question.isSingleChoice) {
            _answers[question.id] = [option.id];
          } else {
            if (isSelected) {
              selectedOptions.remove(option.id);
            } else {
              selectedOptions.add(option.id);
            }
            _answers[question.id] = selectedOptions;
          }
        });
      },
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: isSelected ? Colors.blue.withOpacity(0.2) : Colors.white10,
          border: Border.all(
            color: isSelected ? Colors.blue : Colors.transparent,
          ),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          children: [
            Expanded(
              child: Text(
                option.optionText,
                style: TextStyle(
                  color: isSelected ? Colors.white : Colors.white70,
                ),
              ),
            ),
            if (isSelected)
              const Icon(Icons.check_circle, color: Colors.blue, size: 20),
          ],
        ),
      ),
    );
  }

  Widget _buildFooter() {
    final isLastQuestion = _currentIndex == widget.quiz.questions.length - 1;

    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          if (_currentIndex > 0)
            TextButton(
              onPressed: () {
                _pageController.previousPage(
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeInOut,
                );
              },
              child: const Text('Quay lại'),
            )
          else
            const SizedBox(),
          ElevatedButton(
            onPressed: () {
              if (isLastQuestion) {
                if (_canSubmit) {
                  _handleSubmit();
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Vui lòng trả lời hết câu hỏi'),
                    ),
                  );
                }
              } else {
                _pageController.nextPage(
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeInOut,
                );
              }
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: _canSubmit || !isLastQuestion
                  ? Colors.blue
                  : Colors.grey,
            ),
            child: widget.isSubmitting
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white,
                    ),
                  )
                : Text(isLastQuestion ? 'Nộp bài' : 'Tiếp theo'),
          ),
        ],
      ),
    );
  }

  void _handleSubmit() {
    final List<Map<String, dynamic>> submitData = [];
    _answers.forEach((qId, options) {
      submitData.add({"question_id": qId, "selected_option_ids": options});
    });
    widget.onSubmit(submitData);
  }

  Widget _buildResultView() {
    final passed = widget.isPassed == true;
    return Container(
      color: Colors.black.withOpacity(0.95),
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            passed ? Icons.check_circle_outline : Icons.error_outline,
            color: passed ? Colors.green : Colors.red,
            size: 80,
          ),
          const SizedBox(height: 24),
          Text(
            passed ? 'Chúc mừng!' : 'Chưa đạt yêu cầu',
            style: const TextStyle(
              color: Colors.white,
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            passed
                ? 'Bạn đã vượt qua bài kiểm tra. Hãy tiếp tục bài học.'
                : 'Bạn cần đạt ${widget.quiz.passingPercentScore}% để qua bài này.',
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.white70, fontSize: 16),
          ),
          const SizedBox(height: 32),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (!passed)
                TextButton(
                  onPressed: () => widget.onClose(_answers),
                  child: const Text('Xem lại kiến thức (Thoát)'),
                ),
              if (!passed) const SizedBox(width: 16),
              ElevatedButton(
                onPressed: () {
                  if (passed) {
                    widget.onClose(_answers);
                  } else {
                    widget.onRetry?.call();
                  }
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: passed ? Colors.green : Colors.blue,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 32,
                    vertical: 12,
                  ),
                ),
                child: Text(passed ? 'Tiếp tục học' : 'Làm lại'),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
