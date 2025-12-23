class LessonNote {
  final int id;
  final int lessonId;
  final int userId;
  final int durationAt;
  final String content;
  final String createdAt;
  final String updatedAt;

  LessonNote({
    required this.id,
    required this.lessonId,
    required this.userId,
    required this.durationAt,
    required this.content,
    required this.createdAt,
    required this.updatedAt,
  });

  factory LessonNote.fromJson(Map<String, dynamic> json) {
    return LessonNote(
      id: json['id'],
      lessonId: json['lesson_id'],
      userId: json['user_id'],
      durationAt: json['duration_at'] ?? 0,
      content: json['content'] ?? '',
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'lesson_id': lessonId,
      'user_id': userId,
      'duration_at': durationAt,
      'content': content,
      'created_at': createdAt,
      'updated_at': updatedAt,
    };
  }
}
