import 'package:flutter_dotenv/flutter_dotenv.dart';

class ApiConstants {
  /// Base URL từ biến môi trường
  /// Mặc định: 'https://rdi.sotech.io.vn' nếu không tìm thấy trong .env
  static String get baseUrl =>
      dotenv.env['BASE_URL'] ?? 'https://rdi.sotech.io.vn';

  // Auth
  static const String login = '/api/auth/login';
  static const String register = '/api/auth/register';
  static const String logout = '/api/auth/logout';
  static const String refresh = '/api/auth/refresh';
  static const String me = '/api/auth/me';
  static const String updateProfile = '/api/auth/update-profile';

  // Courses
  static const String courses = '/api/student/courses';
  static const String courseDetail = '/api/student/courses/'; // + id

  // Lessons
  static const String streamLesson = '/api/lessons/'; // + id + /stream
  static const String hlsSignature =
      '/api/lessons/'; // + id + /hls-signature (deprecated)
  static const String autoSignature = '/api/lessons/'; // + id + /auto-signature

  /// Lấy chi tiết lesson
  static String getLessonDetail(int lessonId) =>
      '/api/student/lessons/$lessonId';

  // Progress
  static const String studentProgress = '/api/student/progress';

  /// Lấy endpoint HLS signature cho lesson (deprecated)
  static String getHlsSignatureUrl(int lessonId) =>
      '/api/lessons/$lessonId/hls-signature';

  /// Lấy endpoint auto signature cho lesson (hỗ trợ cả HLS và MP4)
  static String getAutoSignatureUrl(int lessonId) =>
      '/api/lessons/$lessonId/auto-signature';

  /// Lấy danh sách ghi chú của lesson
  static String getLessonNotes(int lessonId) =>
      '/api/student/lessons/$lessonId/notes?sort_by=duration_at&order_by=asc';

  // Quizzes
  /// Lấy danh sách quiz trong video
  static String getLessonQuizzes(int lessonId) =>
      '/api/student/lessons/$lessonId/quiz';

  /// Lấy thông tin về kết quả làm quiz của sinh viên
  static String getQuizStatus(int lessonId) =>
      '/api/student/lessons/$lessonId/quiz-status';

  /// Nộp bài quiz
  static String submitQuiz(int quizId) => '/api/student/quizzes/$quizId/submit';
}
