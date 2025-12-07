import 'package:flutter/material.dart';
import '../data/models/course_model.dart';
import '../data/repositories/course_repository.dart';

class CourseProvider extends ChangeNotifier {
  final CourseRepository _courseRepository;

  List<Course> _courses = [];
  bool _isLoading = false;
  bool _isLoadingCourseDetail = false;
  String? _error;
  Course? _selectedCourse;

  List<Course> get courses => _courses;
  bool get isLoadingAllCourses => _isLoading;
  bool get isLoadingCourseDetail => _isLoadingCourseDetail;
  String? get error => _error;
  Course? get selectedCourse => _selectedCourse;

  CourseProvider(this._courseRepository);

  Future<void> fetchCourses({String search = '', String sortBy = 'created_at', String sortOrder = 'desc'}) async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      _courses = await _courseRepository.getCourses(search: search, sortBy: sortBy, sortOrder: sortOrder);
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchCourseDetail(int id) async {
    _isLoadingCourseDetail = true;
    _error = null;
    _selectedCourse = null;
    notifyListeners();
    try {
      _selectedCourse = await _courseRepository.getCourseDetail(id);
    } catch (e) {
      _error = e.toString();
    } finally {
      _isLoadingCourseDetail = false;
      notifyListeners();
    }
  }
}
