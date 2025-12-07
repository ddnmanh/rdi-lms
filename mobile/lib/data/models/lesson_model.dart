class LessonProgress {
  final int watchedDuration;
  final int lastPosition;
  final String? lastWatchedAt;
  final double completionPercentage;

  LessonProgress({
    this.watchedDuration = 0,
    this.lastPosition = 0,
    this.lastWatchedAt,
    this.completionPercentage = 0.0,
  });

  factory LessonProgress.fromJson(Map<String, dynamic>? json) {
    if (json == null) return LessonProgress();
    return LessonProgress(
      watchedDuration: json['watched_duration'] ?? 0,
      lastPosition: json['last_position'] ?? 0,
      lastWatchedAt: json['last_watched_at'],
      completionPercentage: (json['completion_percentage'] ?? 0.0).toDouble(),
    );
  }

  @override
  String toString() {
    return 'LessonProgress(watchedDuration: ${watchedDuration}s, '
        'lastPosition: ${lastPosition}s, lastWatchedAt: $lastWatchedAt, '
        'completionPercentage: ${completionPercentage.toStringAsFixed(2)}%)';
  }
}

class Lesson {
  final int id;
  final int courseId;
  final String title;
  final String? description;
  final String? thumbnailPath;
  final int duration;
  final String? videoPath;
  final int displayOrder;
  final LessonProgress progress;

  Lesson({
    required this.id,
    required this.courseId,
    required this.title,
    this.description,
    this.thumbnailPath,
    required this.duration,
    this.videoPath,
    required this.displayOrder,
    LessonProgress? progress,
  }) : progress = progress ?? LessonProgress();

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
      progress: LessonProgress.fromJson(json['progress']),
    );
  }

  @override
  String toString() {
    return 'Lesson(id: $id, courseId: $courseId, title: $title, '
        'description: $description, duration: ${duration}s, '
        'videoPath: $videoPath, thumbnailPath: $thumbnailPath, '
        'displayOrder: $displayOrder, progress: $progress)';
  }
}
