class Quiz {
  final int id;
  final int lessonId;
  final String title;
  final String description;
  final double passingPercentScore;
  final bool isRequired;
  final int startAtSeconds;
  final int maxQuestions;
  final bool isActive;
  final List<Question> questions;

  Quiz({
    required this.id,
    required this.lessonId,
    required this.title,
    required this.description,
    required this.passingPercentScore,
    required this.isRequired,
    required this.startAtSeconds,
    required this.maxQuestions,
    required this.isActive,
    required this.questions,
  });

  factory Quiz.fromJson(Map<String, dynamic> json) {
    return Quiz(
      id: json['id'],
      lessonId: json['lesson_id'],
      title: json['title'],
      description: json['description'] ?? '',
      passingPercentScore: (json['passing_percent_score'] as num).toDouble(),
      isRequired: json['is_required'] == true || json['is_required'] == 1,
      startAtSeconds: json['start_at_seconds'] ?? 0,
      maxQuestions: json['max_questions'] ?? 0,
      isActive: json['is_active'] == true || json['is_active'] == 1,
      questions:
          (json['questions'] as List<dynamic>?)
              ?.map((e) => Question.fromJson(e))
              .toList() ??
          [],
    );
  }
}

class Question {
  final int id;
  final int quizId;
  final String questionText;
  final String questionType;
  final int points;
  final int displayOrder;
  final String explanation;
  final List<Option> options;

  Question({
    required this.id,
    required this.quizId,
    required this.questionText,
    required this.questionType,
    required this.points,
    required this.displayOrder,
    required this.explanation,
    required this.options,
  });

  factory Question.fromJson(Map<String, dynamic> json) {
    return Question(
      id: json['id'],
      quizId: json['quiz_id'],
      questionText: json['question_text'],
      questionType: json['question_type'],
      points: json['points'],
      displayOrder: json['display_order'],
      explanation: json['explanation'] ?? '',
      options:
          (json['options'] as List<dynamic>?)
              ?.map((e) => Option.fromJson(e))
              .toList() ??
          [],
    );
  }

  bool get isSingleChoice => questionType == 'single_choice';
  bool get isMultipleChoice => questionType == 'multiple_choice';
}

class Option {
  final int id;
  final int questionId;
  final String optionText;
  final bool isCorrect;
  final int displayOrder;

  Option({
    required this.id,
    required this.questionId,
    required this.optionText,
    required this.isCorrect,
    required this.displayOrder,
  });

  factory Option.fromJson(Map<String, dynamic> json) {
    return Option(
      id: json['id'],
      questionId: json['question_id'],
      optionText: json['option_text'],
      isCorrect: json['is_correct'] == true || json['is_correct'] == 1,
      displayOrder: json['display_order'],
    );
  }
}

class QuizStatus {
  final String lessonId;
  final bool allPassed;
  final List<QuizResultStatus> quizzes;

  QuizStatus({
    required this.lessonId,
    required this.allPassed,
    required this.quizzes,
  });

  factory QuizStatus.fromJson(Map<String, dynamic> json) {
    return QuizStatus(
      lessonId: json['lesson_id'].toString(),
      allPassed: json['all_passed'] == true,
      quizzes:
          (json['quizzes'] as List<dynamic>?)
              ?.map((e) => QuizResultStatus.fromJson(e))
              .toList() ??
          [],
    );
  }
}

class QuizResultStatus {
  final int quizId;
  final String title;
  final int startAtSeconds;
  final double passingPercentScore;
  final bool passed;

  QuizResultStatus({
    required this.quizId,
    required this.title,
    required this.startAtSeconds,
    required this.passingPercentScore,
    required this.passed,
  });

  factory QuizResultStatus.fromJson(Map<String, dynamic> json) {
    return QuizResultStatus(
      quizId: json['quiz_id'],
      title: json['title'],
      startAtSeconds: json['start_at_seconds'] ?? 0,
      passingPercentScore: (json['passing_percent_score'] as num).toDouble(),
      passed: json['passed'] == true,
    );
  }
}
