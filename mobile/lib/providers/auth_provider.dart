import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../data/models/user_model.dart';
import '../data/repositories/auth_repository.dart';
import '../data/repositories/user_repository.dart';

class AuthProvider extends ChangeNotifier {
  final AuthRepository _authRepository;
  final UserRepository _userRepository;
  final FlutterSecureStorage _storage;

  User? _user;
  bool _isLoading = false;
  bool _isInitializing = true;
  String? _error;

  User? get user => _user;
  bool get isLoading => _isLoading;
  bool get isInitializing => _isInitializing;
  String? get error => _error;
  bool get isAuthenticated => _user != null;

  AuthProvider(this._authRepository, this._userRepository, this._storage);

  Future<void> loadUser() async {
    print("AuthProvider: Bắt đầu loadUser");
    _isInitializing = true;
    notifyListeners();
    try {
      print("AuthProvider: Đang đọc token từ storage...");
      final token = await _storage.read(key: 'access_token');
      print(
        "AuthProvider: Token: ${token != null ? 'Tìm thấy' : 'Không tìm thấy'}",
      );
      if (token != null) {
        print("AuthProvider: Đang lấy thông tin user...");
        _user = await _authRepository.getCurrentUser();
        print("AuthProvider: User: ${_user?.fullname ?? 'Unknown'}");
      }
    } catch (e) {
      print("AuthProvider: Lỗi khi load user: $e");
      _error = e.toString();
    } finally {
      print("AuthProvider: Kết thúc loadUser");
      _isInitializing = false;
      notifyListeners();
    }
  }

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      _user = await _authRepository.login(email, password);
      return true;
    } catch (e) {
      _error = e.toString();
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> register(
    String email,
    String password,
    String fullname,
    String birthday,
  ) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      await _authRepository.register(email, password, fullname, birthday);
      return true;
    } catch (e) {
      _error = e.toString();
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> updateProfile(
    String fullname,
    String birthday,
    File? avatar,
  ) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final updatedUser = await _userRepository.updateProfile(
        fullname,
        birthday,
        avatar,
      );
      if (updatedUser != null) {
        _user = updatedUser;
        return true;
      }
      return false;
    } catch (e) {
      _error = e.toString();
      return false;
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> logout() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      await _authRepository.logout();
      await _storage.delete(key: 'access_token');
      await _storage.delete(key: 'refresh_token');
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      _user = null;
      notifyListeners();
    }
  }
}
