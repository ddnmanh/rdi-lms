import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:LMS/core/constants/api_constants.dart';
import 'package:LMS/core/utils/scroll_tracking.dart';
import 'package:LMS/data/models/course_model.dart';
import 'package:LMS/data/models/lesson_model.dart';
import 'package:LMS/presentation/widgets/header_navigation_bar.dart';
import 'package:LMS/presentation/widgets/notification_modal.dart';
import 'package:LMS/presentation/widgets/spinner_loading.dart';
import 'package:LMS/theme/ios_widgets.dart';
import 'package:provider/provider.dart';
import '../../../providers/course_provider.dart';
import '../../../theme/app_colors.dart';
import '../player/video_player_screen.dart';

class CourseDetailScreen extends StatefulWidget {
  final int courseId;
  final int? autoPlayLessonId; // Tự động mở video player với lesson này
  final String? autoPlayLessonTitle; // Title hiển thị trong lúc loading

  const CourseDetailScreen({
    super.key,
    required this.courseId,
    this.autoPlayLessonId,
    this.autoPlayLessonTitle,
  });

  @override
  State<CourseDetailScreen> createState() => _CourseDetailScreenState();
}

class _CourseDetailScreenState extends State<CourseDetailScreen>
    with ScrollTracking {
  bool _hasStartedLoading = false;
  bool _hasShownError = false;
  bool _hasAutoPlayedLesson = false; // Đảm bảo chỉ auto-play 1 lần

  @override
  double get scrollThreshold => 50.0;

  // iOS 18 colors
  static const Color _backgroundColor = Color(0xFFF2F2F7);
  static const Color _cardColor = Colors.white;
  static const Color _secondaryLabelColor = Color(0xFF8E8E93);
  static const Color _separatorColor = Color(0xFFC6C6C8);
  static const Color _systemBlue = Color(0xFF007AFF);

  @override
  void initState() {
    super.initState();
    _loadCourseDetail();
  }

  /// Load chi tiết khóa học - luôn tải mới khi truy cập
  void _loadCourseDetail() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;

      setState(() {
        _hasStartedLoading = true;
        _hasShownError = false;
      });

      context.read<CourseProvider>().fetchCourseDetail(widget.courseId);
    });
  }

  @override
  Widget build(BuildContext context) {
    final courseProvider = context.watch<CourseProvider>();
    final isLoading = courseProvider.isLoadingCourseDetail;
    final error = courseProvider.error;

    final course = courseProvider.selectedCourse?.id == widget.courseId
        ? courseProvider.selectedCourse
        : null;

    _handleErrorState(isLoading, error, course);

    // Auto-play lesson nếu có autoPlayLessonId và course đã load xong
    _handleAutoPlayLesson(isLoading, course);

    return SpinnerLoading(
      isLoading: isLoading,
      child: CupertinoPageScaffold(
        backgroundColor: _backgroundColor,
        child: Material(
          type: MaterialType.transparency,
          child: _buildBody(isLoading, course),
        ),
      ),
    );
  }

  /// Xử lý hiển thị thông báo lỗi
  void _handleErrorState(bool isLoading, String? error, Course? course) {
    final shouldShowError =
        _hasStartedLoading &&
        !isLoading &&
        error != null &&
        course == null &&
        !_hasShownError;

    if (shouldShowError) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted && !_hasShownError) {
          _hasShownError = true;
          _showErrorNotification();
        }
      });
    }
  }

  /// Xử lý auto-play lesson khi course load xong
  void _handleAutoPlayLesson(bool isLoading, Course? course) {
    // Chỉ auto-play khi:
    // - Có autoPlayLessonId
    // - Course đã load xong (không loading)
    // - Course có data
    // - Chưa auto-play lần nào
    if (widget.autoPlayLessonId != null &&
        !isLoading &&
        course != null &&
        !_hasAutoPlayedLesson) {
      _hasAutoPlayedLesson = true;

      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted) {
          // Tìm lesson trong course
          final lesson = course.lessons.where((l) => l.id == widget.autoPlayLessonId).firstOrNull;

          _navigateToVideoPlayer(
            lesson ?? Lesson(
              id: widget.autoPlayLessonId!,
              courseId: widget.courseId,
              title: widget.autoPlayLessonTitle ?? '',
              duration: 0,
              displayOrder: 0,
            ),
          );
        }
      });
    }
  }

  /// Hiển thị thông báo lỗi và quay về trang trước
  Future<void> _showErrorNotification() async {
    await NotificationModal.show(
      context,
      title: 'Không thể tải khóa học',
      description:
          'Đã xảy ra lỗi khi tải thông tin khóa học. Vui lòng thử lại sau.',
      primaryButtonText: 'Quay lại',
      icon: CupertinoIcons.exclamationmark_circle,
      accentColor: CupertinoColors.systemRed,
      barrierDismissible: false,
    );

    if (mounted) {
      Navigator.of(context).pop();
    }
  }

  Future<void> _handleRefresh() async {
    await context.read<CourseProvider>().fetchCourseDetail(widget.courseId);
  }

  /// Build body
  Widget _buildBody(bool isLoading, Course? course) {
    final title = isLoading
        ? 'Đang tải...'
        : (course?.title ?? 'Chi tiết khóa học');

    return Stack(
      children: [
        RefreshIndicator(
          onRefresh: _handleRefresh,
          child: CustomScrollView(
            controller: scrollTrackingController,
            physics: const BouncingScrollPhysics(
              parent: AlwaysScrollableScrollPhysics(),
            ),
            slivers: [
              // Vùng đệm phía trên cho nội dung tránh bị ẩn bởi navigation bar
              SliverToBoxAdapter(
                child: SizedBox(
                  height:
                      HeaderNavigationBar().barHeight +
                      MediaQuery.of(context).padding.top +
                      10,
                ),
              ),
              ..._buildSliversContent(isLoading, course),
            ],
          ),
        ),
        HeaderNavigationBar(
          title: title,
          isVisible: isScrollOverThreshold,
          backgroundColor: _backgroundColor,
          leading: HeaderNavigationBarBackButton(
            color: _systemBlue,
            label: 'K.Học',
            onPressed: () => Navigator.pop(context),
          ),
          titleStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.w600,
            color: Colors.black,
          ),
        ),
      ],
    );
  }

  /// Build nội dung chi tiết khóa học (Slivers)
  List<Widget> _buildSliversContent(bool isLoading, Course? course) {
    // Khi đang loading, SpinnerLoading sẽ hiển thị overlay
    // Hiển thị container trống để giữ layout
    if (isLoading) {
      return [const SliverFillRemaining(child: SizedBox.shrink())];
    }

    // Khi không có dữ liệu
    if (course == null) {
      return [
        SliverFillRemaining(
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  CupertinoIcons.doc_text,
                  size: 48,
                  color: _secondaryLabelColor,
                ),
                const SizedBox(height: 16),
                const Text(
                  'Không có dữ liệu',
                  style: TextStyle(color: Color(0xFF8E8E93), fontSize: 17),
                ),
              ],
            ),
          ),
        ),
      ];
    }

    return [
      // Thumbnail với hero image
      if (course.thumbnailPath != null)
        SliverToBoxAdapter(child: _buildCourseThumbnail(course)),

      // Thông tin khóa học
      SliverToBoxAdapter(child: _buildCourseInfoCard(course)),

      // Thống kê khóa học
      SliverToBoxAdapter(child: _buildCourseStats(course)),

      // Header cho danh sách bài học
      SliverToBoxAdapter(child: IOSSectionHeader(title: 'Bài học')),

      // Danh sách bài học
      _buildLessonList(course),

      // Bottom padding
      const SliverToBoxAdapter(child: SizedBox(height: 40)),
    ];
  }

  /// Build thumbnail khóa học với iOS 18 style
  Widget _buildCourseThumbnail(Course course) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 8, 16, 0),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            // ignore: deprecated_member_use
            color: Colors.black.withOpacity(0.1),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(20),
        child: AspectRatio(
          aspectRatio: 16 / 9,
          child: Image.network(
            '${ApiConstants.baseUrl}${course.thumbnailPath!}',
            fit: BoxFit.cover,
            errorBuilder: (context, error, stackTrace) => Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    // ignore: deprecated_member_use
                    AppColors.primaryStart.withOpacity(0.8),
                    // ignore: deprecated_member_use
                    AppColors.primaryEnd.withOpacity(0.8),
                  ],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
              child: Center(
                child: Icon(
                  CupertinoIcons.photo,
                  size: 48,
                  // ignore: deprecated_member_use
                  color: Colors.white.withOpacity(0.8),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  /// Card thông tin khóa học
  Widget _buildCourseInfoCard(Course course) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: _cardColor,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            // ignore: deprecated_member_use
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            course.title,
            style: const TextStyle(
              fontSize: 22,
              fontWeight: FontWeight.w700,
              letterSpacing: -0.4,
              color: Colors.black,
            ),
          ),
          if (course.description != null && course.description!.isNotEmpty) ...[
            const SizedBox(height: 12),
            Text(
              course.description!,
              style: TextStyle(
                fontSize: 15,
                color: _secondaryLabelColor,
                height: 1.4,
              ),
            ),
          ],
        ],
      ),
    );
  }

  /// Build thống kê khóa học với iOS 18 style
  Widget _buildCourseStats(Course course) {
    final totalDuration = course.lessons.fold<int>(
      0,
      (sum, lesson) => sum + lesson.duration,
    );

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
      decoration: BoxDecoration(
        color: _cardColor,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            // ignore: deprecated_member_use
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          _buildCourseStatItem(
            icon: CupertinoIcons.play_circle,
            value: '${course.lessons.length}',
            label: 'Bài học',
            color: _systemBlue,
          ),
          Container(
            height: 40,
            width: 1,
            // ignore: deprecated_member_use
            color: _separatorColor.withOpacity(0.5),
          ),
          _buildCourseStatItem(
            icon: CupertinoIcons.clock,
            value: _formatTotalDuration(totalDuration),
            label: 'Tổng thời gian',
            color: AppColors.primaryStart,
          ),
        ],
      ),
    );
  }

  /// Single stat item
  Widget _buildCourseStatItem({
    required IconData icon,
    required String value,
    required String label,
    required Color color,
  }) {
    return Expanded(
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              // ignore: deprecated_member_use
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                value,
                style: const TextStyle(
                  fontSize: 17,
                  fontWeight: FontWeight.w600,
                  color: Colors.black,
                ),
              ),
              Text(
                label,
                style: TextStyle(fontSize: 13, color: _secondaryLabelColor),
              ),
            ],
          ),
        ],
      ),
    );
  }

  /// Build danh sách bài học với iOS 18 grouped style
  Widget _buildLessonList(Course course) {
    if (course.lessons.isEmpty) {
      return SliverToBoxAdapter(
        child: Container(
          margin: const EdgeInsets.symmetric(horizontal: 16),
          padding: const EdgeInsets.all(40),
          decoration: BoxDecoration(
            color: _cardColor,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Center(
            child: Column(
              children: [
                Icon(
                  CupertinoIcons.videocam_circle,
                  size: 48,
                  // ignore: deprecated_member_use
                  color: _secondaryLabelColor.withOpacity(0.5),
                ),
                const SizedBox(height: 12),
                const Text(
                  'Chưa có bài học nào',
                  style: TextStyle(fontSize: 15, color: _secondaryLabelColor),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return SliverToBoxAdapter(
      child: IOSGroupedList(
        children: [
          for (int i = 0; i < course.lessons.length; i++) ...[
            CupertinoButton(
              padding: EdgeInsets.zero,
              onPressed: () => _navigateToVideoPlayer(course.lessons[i]),
              child: Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 14,
                ),
                child: Row(
                  children: [
                    // Lesson Thumbnail
                    ClipRRect(
                      borderRadius: BorderRadius.circular(8),
                      child: Image.network(
                        '${ApiConstants.baseUrl}${course.lessons[i].thumbnailPath}',
                        width: 64,
                        height: 36,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) => Container(
                          width: 64,
                          height: 48,
                          decoration: BoxDecoration(
                            color: _secondaryLabelColor,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Icon(
                            CupertinoIcons.photo,
                            size: 20,
                            // ignore: deprecated_member_use
                            color: Colors.white.withOpacity(0.6),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    // Lesson info
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            course.lessons[i].title,
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w500,
                              color: Colors.black,
                            ),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 6),
                          // Progress bar và thông tin
                          Row(
                            children: [
                              Icon(
                                CupertinoIcons.clock,
                                size: 14,
                                color: _secondaryLabelColor,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                _formatDuration(course.lessons[i].duration),
                                style: TextStyle(
                                  fontSize: 13,
                                  color: _secondaryLabelColor,
                                ),
                              ),
                              const SizedBox(width: 12),
                            ],
                          ),
                        ],
                      ),
                    ),
                    // Play/Check icon
                    _buildLessonProgressWithPlay(
                      completionPercentage:
                          course.lessons[i].progress.completionPercentage,
                      lesson: course.lessons[i],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildLessonProgressWithPlay({
    required double completionPercentage,
    required Lesson lesson,
    double iconSize = 16,
    double contentPadding = 6,
    double strokeWidth = 2, // Độ dày của thanh progress
  }) {
    final double progress = (completionPercentage / 100).clamp(0.0, 1.0);
    final bool isCompleted = completionPercentage >= 100;

    final Color activeColor = isCompleted
        ? CupertinoColors.systemGreen
        : CupertinoColors.activeBlue;

    return Stack(
      alignment: Alignment.center,
      children: [
        Positioned.fill(
          child: CircularProgressIndicator(
            value: progress,
            strokeWidth: strokeWidth,
            // ignore: deprecated_member_use
            backgroundColor: activeColor.withOpacity(0.1),
            valueColor: AlwaysStoppedAnimation<Color>(activeColor),
          ),
        ),

        Container(
          // Ở đây mình +2 để tạo một khoảng trắng nhỏ (gap) cho thoáng mắt.
          margin: EdgeInsets.all(strokeWidth/2),
          padding: EdgeInsets.all(contentPadding),
          decoration: BoxDecoration(
            // ignore: deprecated_member_use
            color: activeColor.withOpacity(0.1),
            shape: BoxShape.circle,
          ),
          child: Icon(
            isCompleted ? Icons.check : CupertinoIcons.play_fill,
            size: iconSize,
            color: activeColor,
          ),
        ),
      ],
    );
  }

  /// Format thời lượng từ giây sang mm:ss
  String _formatDuration(int seconds) {
    final minutes = seconds ~/ 60;
    final remainingSeconds = seconds % 60;
    return '${minutes.toString().padLeft(2, '0')}:${remainingSeconds.toString().padLeft(2, '0')}';
  }

  /// Format tổng thời lượng
  String _formatTotalDuration(int seconds) {
    if (seconds < 60) {
      return '${seconds}s';
    }
    final hours = seconds ~/ 3600;
    final minutes = (seconds % 3600) ~/ 60;

    if (hours > 0) {
      return '${hours}h ${minutes}m';
    }
    return '${minutes} phút';
  }

  /// Chuyển đến màn hình xem video
  void _navigateToVideoPlayer(Lesson lesson) async {
    await Navigator.push(
      context,
      CupertinoPageRoute(
        builder: (context) => VideoPlayerScreen(
          lessonId: lesson.id,
          initialTitle: lesson.title,
        ),
      ),
    );

    // Refresh course detail sau khi xem video để cập nhật tiến độ
    if (mounted) {
      context.read<CourseProvider>().fetchCourseDetail(widget.courseId);
    }
  }
}
