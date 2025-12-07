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
  static const String hlsSignature = '/api/lessons/'; // + id + /hls-signature

  /// Lấy endpoint HLS signature cho lesson
  static String getHlsSignatureUrl(int lessonId) => '/api/lessons/$lessonId/hls-signature';
}
