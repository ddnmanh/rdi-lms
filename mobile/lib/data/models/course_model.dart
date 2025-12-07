import 'lesson_model.dart';

class CourseProgress {
  final double completionPercentage;
  final int isPassed;
  final String? joinedAt;
  final String? updatedAt;

  CourseProgress({
    this.completionPercentage = 0.0,
    this.isPassed = 0,
    this.joinedAt,
    this.updatedAt,
  });

  factory CourseProgress.fromJson(Map<String, dynamic> json) {
    return CourseProgress(
      completionPercentage: (json['completion_percentage'] as num?)?.toDouble() ?? 0.0,
      isPassed: json['is_passed'] as int? ?? 0,
      joinedAt: json['joined_at'],
      updatedAt: json['updated_at'],
    );
  }
}

class Course {
  final int id;
  final String title;
  final String? description;
  final String? thumbnailPath;
  final String? startDate;
  final String? endDate;
  final List<Lesson> lessons;
  final double completionPercentage;
  final CourseProgress? progress;

  Course({
    required this.id,
    required this.title,
    this.description,
    this.thumbnailPath,
    this.startDate,
    this.endDate,
    this.lessons = const [],
    this.completionPercentage = 0.0,
    this.progress,
  });

  factory Course.fromJson(Map<String, dynamic> json) {
    var list = json['lessons'] as List? ?? [];
    List<Lesson> lessonsList = list.map((i) => Lesson.fromJson(i)).toList();

    return Course(
      id: json['id'],
      title: json['title'],
      description: json['description'],
      thumbnailPath: json['thumbnail_path'],
      startDate: json['start_date'],
      endDate: json['end_date'],
      lessons: lessonsList,
      completionPercentage: (json['completion_percentage'] as num?)?.toDouble() ?? 0.0,
      progress: json['progress'] != null
          ? CourseProgress.fromJson(json['progress'])
          : null,
    );
  }
}
