import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:LMS/core/constants/api_constants.dart';
import 'package:LMS/data/models/course_model.dart';
import 'package:LMS/presentation/widgets/spinner_loading.dart';
import 'package:provider/provider.dart';
import '../../../providers/course_provider.dart';
import '../../../providers/navigation_provider.dart';
import '../../../theme/ios_widgets.dart';
import '../../../theme/app_colors.dart';
import '../../../core/utils/scroll_tracking.dart';
import '../../widgets/header_bar.dart';
import 'course_detail_screen.dart';

class CourseScreen extends StatefulWidget {
  const CourseScreen({super.key});

  @override
  State<CourseScreen> createState() => _CourseScreenState();
}

class _CourseScreenState extends State<CourseScreen> with ScrollTracking {
  final TextEditingController _searchController = TextEditingController();
  final FocusNode _searchFocusNode = FocusNode();
  int? _lastNavigatedCourseId; // Theo dõi course đã navigate để tránh navigate lặp

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CourseProvider>().fetchCourses(
        sortBy: 'joined_at',
        sortOrder: 'desc',
        forceRefresh: true, // Force refresh khi vào trang
      );
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    _searchFocusNode.dispose();
    super.dispose();
  }

  void _onSearchChanged(String query) {
    setState(() {}); // Rebuild để cập nhật nút clear/search
  }

  void _performSearch({bool isAcceptEmptySearch = false}) {
    FocusScope.of(context).unfocus(); // Ẩn bàn phím
    if (_searchController.text.isNotEmpty || isAcceptEmptySearch) {
      context.read<CourseProvider>().fetchCourses(
        search: _searchController.text,
        sortBy: 'joined_at',
        sortOrder: 'desc',
      );
    }
  }

  Future<void> _handleRefresh() async {
    await context.read<CourseProvider>().fetchCourses(
      search: _searchController.text,
      sortBy: 'joined_at',
      sortOrder: 'desc',
      forceRefresh: true, // Buộc fetch lại khi user chủ động refresh
    );
  }

  /// Xử lý auto-navigation đến CourseDetailScreen từ NavigationProvider
  void _handleAutoNavigation(CourseProvider courseProvider, NavigationProvider navProvider) {
    final pending = navProvider.pendingCourseNavigation;

    // Chỉ auto-navigate khi:
    // - Có pendingCourseNavigation
    // - Courses đã load xong (không loading)
    // - Có courses data
    // - Chưa navigate đến course này (tránh navigate lặp)
    if (pending != null &&
        !courseProvider.isLoadingAllCourses &&
        courseProvider.courses.isNotEmpty &&
        _lastNavigatedCourseId != pending.courseId) {

      _lastNavigatedCourseId = pending.courseId;

      // Clear pending trước khi navigate
      navProvider.clearPendingNavigation();

      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted) {
          Navigator.push(
            context,
            CupertinoPageRoute(
              builder: (context) => CourseDetailScreen(
                courseId: pending.courseId,
                autoPlayLessonId: pending.lessonId,
                autoPlayLessonTitle: pending.lessonTitle,
              ),
            ),
          ).then((_) {
            // Reset để có thể navigate lại nếu cần
            _lastNavigatedCourseId = null;

            // Refresh courses khi quay lại
            if (mounted) {
              context.read<CourseProvider>().fetchCourses(
                search: _searchController.text,
                sortBy: 'joined_at',
                sortOrder: 'desc',
                forceRefresh: true,
              );
            }
          });
        }
      });
    }
  }

  @override
  double get scrollThreshold => 20.0;


  @override
  Widget build(BuildContext context) {
    final courseProvider = context.watch<CourseProvider>();
    final navProvider = context.watch<NavigationProvider>();

    // Auto-navigate đến CourseDetailScreen nếu có pendingCourseNavigation
    _handleAutoNavigation(courseProvider, navProvider);

    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Scaffold(
        body: Stack(
            children: [
                RefreshIndicator(
                  onRefresh: _handleRefresh,
                  child: CustomScrollView(
                    controller: scrollTrackingController,
                    physics: const BouncingScrollPhysics(
                      parent: AlwaysScrollableScrollPhysics(),
                    ),
                    slivers: [
                      const SliverToBoxAdapter(child: SizedBox(height: 50)),

                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
                          child: AnimatedOpacity(
                            duration: const Duration(milliseconds: 400),
                            opacity: isScrollOverThreshold ? 0 : 1,
                            child: Text(
                              'Khóa học',
                              style: Theme.of(context).textTheme.displayLarge,
                            ),
                          ),
                        ),
                      ),

                      // Search bar
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(0.08),
                                  blurRadius: 10,
                                  offset: const Offset(0, 2),
                                ),
                              ],
                            ),
                            child: TextField(
                              controller: _searchController,
                              focusNode: _searchFocusNode,
                              onChanged: _onSearchChanged,
                              onSubmitted: (_) => _performSearch(),
                              textInputAction: TextInputAction.search,
                              decoration: InputDecoration(
                                hintText: 'Tìm kiếm khóa học...',
                                hintStyle:  const TextStyle(
                                  color: Color(0xFF999999)
                                ),
                                prefixIconConstraints: BoxConstraints(
                                  minWidth: 32, // Giảm vùng bao prefixIcon xuống 32 thay vì 48
                                  minHeight: 32,
                                ),
                                prefixIcon: Container(
                                  padding: const EdgeInsets.only(left: 12, right: 0),
                                  child: const Icon(
                                    Icons.search,
                                    color: Color(0xFF999999),
                                    size: 20,
                                  ),
                                ),
                                suffixIcon: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    if (_searchController.text.isNotEmpty)
                                      IconButton(
                                        icon: const Icon(
                                          Icons.clear,
                                          color: Color(0xFF999999),
                                          size: 20,
                                        ),
                                        onPressed: () {
                                          _searchController.clear();
                                          _onSearchChanged('');
                                          _performSearch(isAcceptEmptySearch: true); // Reset về danh sách gốc
                                        },
                                      ),
                                    Container(
                                      margin: const EdgeInsets.only(right: 4),
                                      child: IconButton(
                                        icon: Icon(
                                          Icons.arrow_forward_rounded,
                                          color: AppColors.primary,
                                          size: 22,
                                        ),
                                        onPressed: _performSearch,
                                      ),
                                    ),
                                  ],
                                ),
                                filled: true,
                                fillColor: Colors.white,
                                contentPadding: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 14,
                                ),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: BorderSide.none,
                                ),
                                enabledBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: BorderSide.none,
                                ),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(12),
                                  borderSide: BorderSide(
                                    color: AppColors.primary,
                                    width: 1.5,
                                  ),
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),

                      if (courseProvider.courses.isEmpty && !courseProvider.isLoadingAllCourses)
                        SliverFillRemaining(
                          hasScrollBody: false,
                          child: _buildEmptyState(context),
                        )
                      else if (!courseProvider.isLoadingAllCourses)
                        // Course list
                        SliverPadding(
                          padding: const EdgeInsets.only(bottom: 100),
                          sliver: SliverList(
                            delegate: SliverChildBuilderDelegate((context, index) {
                              return _buildCourseItem(context, courseProvider.courses[index]);
                            }, childCount: courseProvider.courses.length),
                          ),
                        ),
                    ],
                  ),
                ),
                HeaderBar(
                  topOpacity: 1,
                  bottomOpacity: 0,
                  expanded: isScrollOverThreshold,
                  expandedExtraHeight: 30,
                  ignoringPointer: !isScrollOverThreshold,
                  padding: const EdgeInsets.only(bottom: 10),
                  child: Text(
                    'Khóa học',
                    style: Theme.of(context).textTheme.displayLarge?.copyWith(
                          fontSize: 18,
                          fontWeight: FontWeight.w600,
                        ),
                    textAlign: TextAlign.center,
                  ),
                ),
                // Loading overlay
                if (courseProvider.isLoadingAllCourses)
                  Positioned.fill(
                    child: SpinnerLoading(
                      isLoading: true,
                      child: const SizedBox.shrink(),
                    ),
                  ),
              ],
            ),
      ),
    );
  }

  Widget _buildCourseItem(BuildContext context, Course course) {

    final completePercentage = course.progress?.completionPercentage ?? 0.0;

    return IOSCard(
        margin: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 8,
        ),
        padding: EdgeInsets.zero,
        onTap: () async {
          _searchFocusNode.unfocus();
          await Navigator.push(
            // ignore: use_build_context_synchronously
            context,
            CupertinoPageRoute(
              builder: (context) => CourseDetailScreen(courseId: course.id),
            ),
          );
          // Refresh courses list sau khi quay lại để cập nhật tiến độ
          if (mounted) {
            // ignore: use_build_context_synchronously
            context.read<CourseProvider>().fetchCourses(
              search: _searchController.text,
              sortBy: 'joined_at',
              sortOrder: 'desc',
              forceRefresh: true,
            );
          }
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Course header with gradient
            ClipRRect(
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(16),
                topRight: Radius.circular(16),
              ),
              child: Container(
                height: 140,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      AppColors.primaryStart,
                      AppColors.primaryEnd,
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: course.thumbnailPath != null
                    ? Image.network(
                        '${ApiConstants.baseUrl}${course.thumbnailPath!}',
                        fit: BoxFit.cover,
                        width: double.infinity,
                      )
                    : const SizedBox.shrink(),
              ),
            ),
            // Course details
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment:
                    CrossAxisAlignment.start,
                children: [
                  Text(
                    course.title,
                    style: Theme.of(
                      context,
                    ).textTheme.headlineSmall,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (course.description != null &&
                      course.description!.isNotEmpty) ...[
                    const SizedBox(height: 8),
                    Text(
                      course.description!,
                      style: Theme.of(context)
                          .textTheme
                          .bodyMedium
                          ?.copyWith(
                            color: const Color(
                              0xFF666666,
                            ),
                          ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Icon(
                        completePercentage > 80 ? Icons.check_circle_rounded : Icons.play_circle_outline_rounded,
                        size: 16,
                        color: completePercentage > 0
                          ? completePercentage > 80 ? CupertinoColors.systemGreen : CupertinoColors.systemBlue
                          : CupertinoColors.systemOrange,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        completePercentage > 0
                            ? '${completePercentage.toStringAsFixed(0)}% đã học'
                            : 'Bắt đầu học',
                        style: Theme.of(context)
                            .textTheme
                            .bodySmall
                            ?.copyWith(
                              color: completePercentage > 0
                                  ? completePercentage > 80 ? CupertinoColors.systemGreen : CupertinoColors.systemBlue
                                  : CupertinoColors.systemOrange,
                              fontWeight: FontWeight.w600,
                            ),
                      ),
                      const Spacer(),
                      _buildCourseTimeInfo(course),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      );
  }

  Widget _buildCourseTimeInfo(Course course) {
    final now = DateTime.now();
    DateTime? startDate;
    DateTime? endDate;

    if (course.startDate != null) {
      startDate = DateTime.tryParse(course.startDate!);
    }
    if (course.endDate != null) {
      endDate = DateTime.tryParse(course.endDate!);
    }

    String text;
    Color color;
    IconData icon;

    if (startDate != null && now.isBefore(startDate)) {
      // Khóa học chưa bắt đầu - luôn màu xanh dương
      final diff = startDate.difference(now);
      text = _formatDuration(diff, prefix: 'Bắt đầu trong ', suffix: '');
      color = CupertinoColors.systemBlue;
      icon = Icons.play_circle_fill;
    } else if (endDate != null && now.isBefore(endDate)) {
      // Khóa học đang diễn ra
      final diff = endDate.difference(now);

      if (diff.inDays < 3) {
        // < 3 ngày: màu đỏ, hiển thị cả ngày và giờ
        text = _formatDurationWithDayAndHour(diff, prefix: 'Còn ');
        color = CupertinoColors.systemRed;
      } else if (diff.inDays < 15) {
        // < 15 ngày: màu cam
        text = _formatDuration(diff, prefix: 'Còn ', suffix: '');
        color = CupertinoColors.systemOrange;
      } else {
        // >= 15 ngày: màu xanh lá cây
        text = _formatDuration(diff, prefix: 'Còn ', suffix: '');
        color = CupertinoColors.systemGreen;
      }
      icon = Icons.timer_rounded;
    } else if (endDate != null) {
      // Khóa học đã kết thúc
      text = 'Đã kết thúc';
      color = CupertinoColors.systemGrey;
      icon = Icons.check_circle_outline_rounded;
    } else {
      // Không có thông tin thời gian
      return const Icon(
        Icons.chevron_right_rounded,
        size: 20,
        color: Color(0xFFCCCCCC),
      );
    }

    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: color),
        const SizedBox(width: 4),
        Text(
          text,
          style: TextStyle(
            fontSize: 12,
            color: color,
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  String _formatDuration(Duration diff, {String prefix = '', String suffix = ''}) {
    if (diff.inDays > 30) {
      final months = (diff.inDays / 30).floor();
      return '$prefix$months tháng$suffix';
    } else if (diff.inDays > 0) {
      return '$prefix${diff.inDays} ngày$suffix';
    } else if (diff.inHours > 0) {
      return '$prefix${diff.inHours} giờ$suffix';
    } else if (diff.inMinutes > 0) {
      return '$prefix${diff.inMinutes} phút$suffix';
    } else {
      return '${prefix}Sắp đến$suffix';
    }
  }

  String _formatDurationWithDayAndHour(Duration diff, {String prefix = ''}) {
    final days = diff.inDays;
    final hours = diff.inHours % 24;

    if (days > 0 && hours > 0) {
      return '$prefix$days ngày $hours giờ';
    } else if (days > 0) {
      return '$prefix$days ngày';
    } else if (hours > 0) {
      return '$prefix$hours giờ';
    } else if (diff.inMinutes > 0) {
      return '$prefix${diff.inMinutes} phút';
    } else {
      return '${prefix}Sắp hết';
    }
  }

  Widget _buildEmptyState(BuildContext context) {
    // Offset để bù lại phần header phía trên (title + search bar)
    // giúp nội dung căn giữa thực sự trên màn hình
    const headerOffset = 100.0;

    return Center(
      child: Padding(
        padding: const EdgeInsets.only(bottom: headerOffset),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 80,
              height: 80,
              margin: const EdgeInsets.only(bottom: 16),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Color(0xFFF3F4F6), // gray-100
                    Color(0xFFE5E7EB), // gray-200
                  ],
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.1),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ],
              ),
              child: const Icon(
                Icons.inbox_rounded,
                size: 30,
                color: Color(0xFF9CA3AF), // gray-400
              ),
            ),
            Text(
              'Không có khóa học',
              style: Theme.of(context).textTheme.headlineMedium,
            ),
            const SizedBox(height: 8),
            Text(
              'Liên hệ giảng viên để được hỗ trợ',
              style: Theme.of(
                context,
              ).textTheme.bodyMedium?.copyWith(color: const Color(0xFF999999)),
            ),
          ],
        ),
      ),
    );
  }

}
