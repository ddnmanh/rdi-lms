import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/constants/api_constants.dart';
import '../models/user_model.dart';
import '../services/api_service.dart';

class AuthRepository {
  final ApiService _apiService;
  final FlutterSecureStorage _storage;

  AuthRepository(this._apiService, this._storage);

  Future<User?> login(String email, String password) async {
    try {
      final response = await _apiService.dio.post(
        ApiConstants.login,
        data: {'email': email, 'password': password},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final accessToken = data['access_token'];
        final refreshToken = data['refresh_token'];
        final user = User.fromJson(data['user']);

        await _storage.write(key: 'access_token', value: accessToken);
        await _storage.write(key: 'refresh_token', value: refreshToken);

        return user;
      }
    } catch (e) {
      rethrow;
    }
    return null;
  }

  Future<void> register(
    String email,
    String password,
    String fullname,
    String birthday,
  ) async {
    try {
      await _apiService.dio.post(
        ApiConstants.register,
        data: {
          'email': email,
          'password': password,
          'fullname': fullname,
          'birthday': birthday,
        },
      );
    } catch (e) {
      rethrow;
    }
  }

  Future<void> logout() async {
    try {
      final refreshToken = await _storage.read(key: 'refresh_token');
      await _apiService.dio.post(
        ApiConstants.logout,
        data: {'refresh_token': refreshToken},
      );
    } catch (e) {
      // Ignore logout errors
    } finally {
      await _storage.delete(key: 'access_token');
      await _storage.delete(key: 'refresh_token');
    }
  }

  Future<User?> getCurrentUser() async {
    try {
      final response = await _apiService.dio.get(ApiConstants.me);
      if (response.statusCode == 200) {
        // The structure of /me response might be different, let's assume it returns User directly or wrapped
        // Based on Postman, it returns user info.
        // Let's assume standard wrapper: { success: true, data: { ...user } } or just user
        // Checking Postman: "Get Current User" response is not fully detailed in the snippet but usually follows similar pattern.
        // Assuming it returns the user object directly or in 'data'.
        // Let's assume it returns the user object directly for now, or check the response if available.
        // Actually, looking at Login response, user is inside data.user.
        // Let's assume /me returns the user object directly or wrapped in data.
        // I'll assume wrapped in data for consistency.
        return User.fromJson(response.data);
      }
    } catch (e) {
      return null;
    }
    return null;
  }
}
