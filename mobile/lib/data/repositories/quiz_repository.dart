import '../../core/constants/api_constants.dart';
import '../models/quiz_model.dart';
import '../services/api_service.dart';

class QuizRepository {
  final ApiService _apiService;

  QuizRepository(this._apiService);

  /// Lấy danh sách quiz trong video
  Future<List<Quiz>> getLessonQuizzes(int lessonId) async {
    try {
      final response = await _apiService.dio.get(
        ApiConstants.getLessonQuizzes(lessonId),
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> list = response.data['data'];
        return list.map((e) => Quiz.fromJson(e)).toList();
      }
    } catch (e) {
      // Log or rethrow as needed
      rethrow;
    }
    return [];
  }

  /// Lấy thông tin về kết quả làm quiz của sinh viên
  Future<QuizStatus?> getQuizStatus(int lessonId) async {
    try {
      final response = await _apiService.dio.get(
        ApiConstants.getQuizStatus(lessonId),
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return QuizStatus.fromJson(response.data['data']);
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }

  /// Nộp bài quiz
  /// [answers] cấu trúc:
  /// [
  ///   {
  ///     "question_id": 115,
  ///     "selected_option_ids": [457]
  ///   },
  ///   ...
  /// ]
  Future<bool> submitQuiz(
    int quizId,
    List<Map<String, dynamic>> answers,
  ) async {
    try {
      final response = await _apiService.dio.post(
        ApiConstants.submitQuiz(quizId),
        data: {'answers': answers},
      );
      if (response.statusCode == 200 || response.statusCode == 201) {
        // Có thể API trả về success: true hoặc kết quả chi tiết
        // Kiểm tra response body nếu cần.
        // Document API mẫu không show response của submit, nhưng thường là success.
        // Giả sử cứ 200 OK là thành công.
        return true;
      }
    } catch (e) {
      rethrow;
    }
    return false;
  }
}
