import 'package:flutter/material.dart';

/// Thông tin navigation đến course/lesson
class PendingCourseNavigation {
  final int courseId;
  final int? lessonId;
  final String? lessonTitle;

  PendingCourseNavigation({
    required this.courseId,
    this.lessonId,
    this.lessonTitle,
  });
}

/// Provider quản lý navigation giữa các tabs
class NavigationProvider extends ChangeNotifier {
  int _selectedTabIndex = 0;
  PendingCourseNavigation? _pendingCourseNavigation;

  int get selectedTabIndex => _selectedTabIndex;
  PendingCourseNavigation? get pendingCourseNavigation => _pendingCourseNavigation;

  /// Chuyển tab
  void setSelectedTab(int index) {
    _selectedTabIndex = index;
    notifyListeners();
  }

  /// Navigate đến course (chuyển sang tab Course và auto-navigate)
  void navigateToCourse({
    required int courseId,
    int? lessonId,
    String? lessonTitle,
  }) {
    _pendingCourseNavigation = PendingCourseNavigation(
      courseId: courseId,
      lessonId: lessonId,
      lessonTitle: lessonTitle,
    );
    _selectedTabIndex = 1; // Tab Course
    notifyListeners();
  }

  /// Clear pending navigation sau khi đã xử lý
  void clearPendingNavigation() {
    _pendingCourseNavigation = null;
    // Không notify vì không cần rebuild UI
  }

  /// Reset về tab Home
  void resetToHome() {
    _selectedTabIndex = 0;
    _pendingCourseNavigation = null;
    notifyListeners();
  }
}

