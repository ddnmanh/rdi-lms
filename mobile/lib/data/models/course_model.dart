import 'lesson_model.dart';

class Course {
  final int id;
  final String title;
  final String? description;
  final String? thumbnailPath;
  final String? startDate;
  final String? endDate;
  final List<Lesson> lessons;

  Course({
    required this.id,
    required this.title,
    this.description,
    this.thumbnailPath,
    this.startDate,
    this.endDate,
    this.lessons = const [],
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
    );
  }
}
