import 'dart:io';
import 'package:dio/dio.dart';
import '../../core/constants/api_constants.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class UserRepository {
  final ApiService _apiService;

  UserRepository(this._apiService);

  Future<User?> updateProfile(
    String fullname,
    String birthday,
    File? avatar,
  ) async {
    try {
      final formData = FormData.fromMap({
        'fullname': fullname,
        'birthday': birthday,
        if (avatar != null)
          'avatar': await MultipartFile.fromFile(
            avatar.path,
            filename: avatar.path.split('/').last,
          ),
      });

      final response = await _apiService.dio.put(
        ApiConstants.updateProfile,
        data: formData,
        options: Options(
          contentType: 'multipart/form-data',
        ),
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return User.fromJson(response.data['data']);
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }
}
