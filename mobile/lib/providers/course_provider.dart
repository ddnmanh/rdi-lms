import 'package:flutter/material.dart';
import '../data/models/course_model.dart';
import '../data/models/lesson_model.dart';
import '../data/repositories/course_repository.dart';

/// Class chứa thông tin lesson gần đây cùng với course của nó
class RecentLessonInfo {
  final Lesson lesson;
  final Course course;
  
  RecentLessonInfo({required this.lesson, required this.course});
}

class CourseProvider extends ChangeNotifier {
  final CourseRepository _courseRepository;

  List<Course> _courses = [];
  bool _isLoading = false;
  bool _isLoadingCourseDetail = false;
  String? _error;
  Course? _selectedCourse;
  DateTime? _lastFetchTime;
  String _lastSearchQuery = '';

  // === GETTERS CƠ BẢN ===
  List<Course> get courses => _courses;
  bool get isLoadingAllCourses => _isLoading;
  bool get isLoadingCourseDetail => _isLoadingCourseDetail;
  String? get error => _error;
  Course? get selectedCourse => _selectedCourse;

  // === GETTERS TIỆN ÍCH CHO CÁC TRANG KHÁC ===

  /// Tổng số khóa học
  int get totalCourses => _courses.length;

  /// Số khóa học đã hoàn thành (>= 80%)
  int get completedCourses => _courses.where((c) =>
    (c.progress?.completionPercentage ?? 0) >= 80
  ).length;

  /// Số khóa học đang học (> 0% và < 80%)
  int get inProgressCourses => _courses.where((c) {
    final progress = c.progress?.completionPercentage ?? 0;
    return progress > 0 && progress < 80;
  }).length;

  /// Khóa học gần đây nhất (đang học, chưa hoàn thành)
  Course? get recentCourse {
    final inProgress = _courses.where((c) {
      final progress = c.progress?.completionPercentage ?? 0;
      return progress > 0 && progress < 100;
    }).toList();

    if (inProgress.isEmpty) {
      // Nếu không có khóa đang học, trả về khóa đầu tiên chưa hoàn thành
      final notStarted = _courses.where((c) {
        final progress = c.progress?.completionPercentage ?? 0;
        return progress < 100;
      }).toList();
      return notStarted.isNotEmpty ? notStarted.first : null;
    }

    // Sắp xếp theo thời gian cập nhật gần nhất
    inProgress.sort((a, b) =>
      (b.progress?.updatedAt ?? '').compareTo(a.progress?.updatedAt ?? '')
    );
    return inProgress.first;
  }

  /// Kiểm tra xem có data không
  bool get hasCourses => _courses.isNotEmpty;

  /// Kiểm tra xem data có cũ không (> 5 phút)
  bool get isDataStale {
    if (_lastFetchTime == null) return true;
    return DateTime.now().difference(_lastFetchTime!) > const Duration(minutes: 5);
  }

  /// Tổng số bài học đã hoàn thành (từ tất cả khóa học có lessons)
  int get totalCompletedLessons {
    int count = 0;
    for (final course in _courses) {
      for (final lesson in course.lessons) {
        if (lesson.progress.completionPercentage >= 100) {
          count++;
        }
      }
    }
    return count;
  }

  /// Tổng số bài học
  int get totalLessons {
    int count = 0;
    for (final course in _courses) {
      count += course.lessons.length;
    }
    return count;
  }

  /// Bài học gần đây nhất (đang xem, chưa hoàn thành)
  RecentLessonInfo? get recentLesson {
    RecentLessonInfo? mostRecent;
    DateTime? mostRecentTime;
    
    for (final course in _courses) {
      for (final lesson in course.lessons) {
        // Chỉ lấy lesson đang xem (progress > 0 và < 100%)
        final progress = lesson.progress.completionPercentage;
        if (progress > 0 && progress < 100 && lesson.progress.lastWatchedAt != null) {
          final watchedAt = DateTime.tryParse(lesson.progress.lastWatchedAt!);
          if (watchedAt != null) {
            if (mostRecentTime == null || watchedAt.isAfter(mostRecentTime)) {
              mostRecentTime = watchedAt;
              mostRecent = RecentLessonInfo(lesson: lesson, course: course);
            }
          }
        }
      }
    }
    
    return mostRecent;
  }

  CourseProvider(this._courseRepository);

  /// Fetch courses - Cache thông minh
  Future<void> fetchCourses({
    String search = '',
    String sortBy = 'created_at',
    String sortOrder = 'desc',
    bool forceRefresh = false,
  }) async {
    // Nếu đang search khác với lần trước, cần fetch lại
    final isNewSearch = search != _lastSearchQuery;

    // Nếu đã có data, không cần refresh, data chưa cũ, và không phải search mới -> skip
    if (!forceRefresh && _courses.isNotEmpty && !isDataStale && !isNewSearch && search.isEmpty) {
      return;
    }

    _isLoading = true;
    _error = null;
    _lastSearchQuery = search;

    // Chỉ xóa courses khi search mới để tránh flicker
    if (isNewSearch) {
      _courses = [];
    }
    notifyListeners();

    try {
      _courses = await _courseRepository.getCourses(
        search: search,
        sortBy: sortBy,
        sortOrder: sortOrder
      );
      _lastFetchTime = DateTime.now();
    } catch (e) {
      _error = e.toString();
      // Giữ data cũ nếu có lỗi (không xóa)
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  /// Đảm bảo courses đã được load (dùng cho các trang khác)
  Future<void> ensureCoursesLoaded() async {
    if (_courses.isEmpty || isDataStale) {
      await fetchCourses(sortBy: 'joined_at', sortOrder: 'desc');
    }
  }

  /// Lấy course theo ID từ cache (không gọi API)
  Course? getCourseById(int id) {
    try {
      return _courses.firstWhere((c) => c.id == id);
    } catch (e) {
      return null;
    }
  }

  Future<void> fetchCourseDetail(int id) async {
    // Kiểm tra trong cache trước
    final cachedCourse = getCourseById(id);

    // Nếu có trong cache và có lessons, có thể dùng luôn (tùy business logic)
    // Ở đây vẫn fetch để đảm bảo data mới nhất cho chi tiết

    _isLoadingCourseDetail = true;
    _error = null;
    _selectedCourse = cachedCourse; // Hiển thị cache trước trong lúc loading
    notifyListeners();

    try {
      _selectedCourse = await _courseRepository.getCourseDetail(id);
    } catch (e) {
      _error = e.toString();
      // Giữ cached course nếu có lỗi
      if (cachedCourse == null) {
        _selectedCourse = null;
      }
    } finally {
      _isLoadingCourseDetail = false;
      notifyListeners();
    }
  }

  /// Clear cache (khi logout)
  void clearCache() {
    _courses = [];
    _selectedCourse = null;
    _lastFetchTime = null;
    _lastSearchQuery = '';
    _error = null;
    notifyListeners();
  }
}
