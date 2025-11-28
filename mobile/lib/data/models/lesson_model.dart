class Lesson {
  final int id;
  final int courseId;
  final String title;
  final String? description;
  final String? thumbnailPath;
  final int duration;
  final String? videoPath;
  final int displayOrder;

  Lesson({
    required this.id,
    required this.courseId,
    required this.title,
    this.description,
    this.thumbnailPath,
    required this.duration,
    this.videoPath,
    required this.displayOrder,
  });

  factory Lesson.fromJson(Map<String, dynamic> json) {
    return Lesson(
      id: json['id'],
      courseId: json['course_id'],
      title: json['title'],
      description: json['description'],
      thumbnailPath: json['thumbnail_path'],
      duration: json['duration'] ?? 0,
      videoPath: json['video_path'],
      displayOrder: json['display_order'] ?? 0,
    );
  }
}
