import '../../core/constants/api_constants.dart';
import '../models/course_model.dart';
import '../models/auto_signature_model.dart';
import '../services/api_service.dart';

class CourseRepository {
  final ApiService _apiService;

  CourseRepository(this._apiService);

  Future<List<Course>> getCourses({String search = '', String sortBy = 'created_at', String sortOrder = 'desc'}) async {
    try {

      List<String> params = [];
      if (search.isNotEmpty) {
        params.add('search=${Uri.encodeComponent(search.toLowerCase())}');
      }
      if (sortBy.isNotEmpty) {
        params.add('sort_by=${Uri.encodeComponent(sortBy.toLowerCase())}');
      }
      if (sortOrder.isNotEmpty) {
        params.add('sort_order=${Uri.encodeComponent(sortOrder.toUpperCase())}');
      }

      String paramsString = params.isNotEmpty ? '?${params.join('&')}' : '';

      final response = await _apiService.dio.get('${ApiConstants.courses}$paramsString');
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> list = response.data['data'];
        return list.map((e) => Course.fromJson(e)).toList();
      }
    } catch (e) {
      rethrow;
    }
    return [];
  }

  Future<Course?> getCourseDetail(int id) async {
    try {
      final response = await _apiService.dio.get(
        '${ApiConstants.courseDetail}$id',
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        // The Postman response for detail shows it returns the course object directly or in data?
        // "Get Student Course Detail" response:
        // { "success": true, "data": { ...course... } }
        // Wait, the snippet for "Get Student Course Detail" was cut off.
        // But "Get Student Courses" returns a list in "data".
        // I'll assume detail returns the course in "data".
        return Course.fromJson(response.data['data']);
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }

  /// Lấy auto signature cho lesson để stream video (hỗ trợ cả HLS và MP4)
  /// GET /api/lessons/{lessonId}/auto-signature
  Future<AutoSignature?> getAutoSignature(int lessonId) async {
    try {
      final response = await _apiService.dio.get(
        ApiConstants.getAutoSignatureUrl(lessonId),
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return AutoSignature.fromJson(response.data['data']);
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }

  /// Lấy HLS signature cho lesson (deprecated - sử dụng getAutoSignature thay thế)
  /// GET /api/lessons/{lessonId}/hls-signature
  @Deprecated('Sử dụng getAutoSignature() thay thế')
  Future<AutoSignature?> getHlsSignature(int lessonId) async {
    try {
      final response = await _apiService.dio.get(
        ApiConstants.getHlsSignatureUrl(lessonId),
      );
      if (response.statusCode == 200 && response.data['success'] == true) {
        return AutoSignature.fromJson(response.data['data']);
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }
}
