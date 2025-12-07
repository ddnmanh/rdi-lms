import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/api_constants.dart';
import '../../../providers/auth_provider.dart';
import '../../../providers/course_provider.dart';
import '../../../theme/ios_widgets.dart';
import '../../../theme/app_colors.dart';
import '../../widgets/header_bar.dart';
import '../course/course_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  @override
  void initState() {
    super.initState();
    // Đảm bảo courses đã được load khi vào HomeScreen
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CourseProvider>().ensureCoursesLoaded();
    });
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final courseProvider = context.watch<CourseProvider>();

    return Scaffold(
      body: Stack(
        children: [
          CustomScrollView(
            slivers: [
              const SliverToBoxAdapter(child: SizedBox(height: 50)),

              // iOS-style large title
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 16, 0, 8),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Xin chào,',
                        style: Theme.of(context).textTheme.headlineMedium
                            ?.copyWith(color: const Color(0xFF999999)),
                      ),
                      Text(
                        user?.fullname ?? 'Sinh viên',
                        style: Theme.of(context).textTheme.displayLarge,
                      ),
                    ],
                  ),
                ),
              ),

              // Welcome message
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(20, 0, 20, 24),
                  child: Text(
                    'Sắp hoàn thành rồi, học mỗi ngày một chút nhé!',
                    style: Theme.of(
                      context,
                    ).textTheme.bodyLarge?.copyWith(color: const Color(0xFF666666)),
                  ),
                ),
              ),

              // Quick stats cards
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Row(
                    children: [
                      Expanded(
                        child: _buildStatCard(
                          context,
                          icon: Icons.book_rounded,
                          title: 'Khóa học',
                          value: '${courseProvider.totalCourses}',
                          color: AppColors.primaryStart,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildStatCard(
                          context,
                          icon: Icons.emoji_events_rounded,
                          title: 'Hoàn thành',
                          value: '${courseProvider.completedCourses}',
                          color: AppColors.primaryEnd,
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 10)),

              // Section header
              SliverToBoxAdapter(child: IOSSectionHeader(title: 'Truy cập nhanh')),

              // Quick action cards
              SliverToBoxAdapter(
                child: Column(
                  children: [
                    // Tiếp tục khóa học - chỉ hiển thị nếu có khóa học gần đây
                    if (courseProvider.recentCourse != null)
                      _buildContinueCourseCard(context, courseProvider),

                    // Tiếp tục bài học - hiển thị lesson vừa xem
                    if (courseProvider.recentLesson != null)
                      _buildContinueLessonCard(context, courseProvider),

                    // Card "Xem tất cả khóa học" khi chưa có khóa học gần đây
                    if (courseProvider.recentCourse == null && courseProvider.hasCourses)
                      IOSCard(
                        onTap: () {
                          // Navigate to course tab (index 1)
                          // Có thể dùng callback hoặc Navigator
                        },
                        child: Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(12),
                              decoration: BoxDecoration(
                                gradient: AppColors.primaryGradient,
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: const Icon(
                                Icons.school_rounded,
                                color: Colors.white,
                                size: 28,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Bắt đầu học ngay',
                                    style: Theme.of(context).textTheme.bodyLarge
                                        ?.copyWith(fontWeight: FontWeight.w600),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    'Chọn một khóa học để bắt đầu',
                                    style: Theme.of(context).textTheme.bodySmall
                                        ?.copyWith(color: const Color(0xFF999999)),
                                  ),
                                ],
                              ),
                            ),
                            const Icon(
                              Icons.chevron_right_rounded,
                              color: Color(0xFFCCCCCC),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 10)),

              // Recent activity section
              SliverToBoxAdapter(
                child: IOSSectionHeader(title: 'Hoạt động gần đây'),
              ),

              // SliverToBoxAdapter(
              //   child: IOSGroupedList(
              //     children: [
              //       IOSListTile(
              //         leading: Container(
              //           padding: const EdgeInsets.all(8),
              //           decoration: BoxDecoration(
              //             color: AppColors.primaryStart.withOpacity(0.1),
              //             borderRadius: BorderRadius.circular(8),
              //           ),
              //           child: const Icon(
              //             Icons.check_circle_rounded,
              //             color: AppColors.primaryStart,
              //             size: 24,
              //           ),
              //         ),
              //         title: 'Completed Lesson 5',
              //         subtitle: 'Introduction to Flutter',
              //         trailing: const Text(
              //           '2h ago',
              //           style: TextStyle(color: Color(0xFF999999), fontSize: 13),
              //         ),
              //       ),
              //       IOSListTile(
              //         leading: Container(
              //           padding: const EdgeInsets.all(8),
              //           decoration: BoxDecoration(
              //             color: AppColors.primaryEnd.withOpacity(0.1),
              //             borderRadius: BorderRadius.circular(8),
              //           ),
              //           child: const Icon(
              //             Icons.star_rounded,
              //             color: AppColors.primaryEnd,
              //             size: 24,
              //           ),
              //         ),
              //         title: 'Earned Achievement',
              //         subtitle: 'Week Streak Master',
              //         trailing: const Text(
              //           '1d ago',
              //           style: TextStyle(color: Color(0xFF999999), fontSize: 13),
              //         ),
              //       ),
              //     ],
              //   ),
              // ),

              const SliverToBoxAdapter(child: SizedBox(height: 120)),
            ],
          ),
          const HeaderBar(),
        ],
      ),
    );
  }

  Widget _buildStatCard(
    BuildContext context, {
    required IconData icon,
    required String title,
    required String value,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 16),
          Text(value, style: Theme.of(context).textTheme.displayMedium),
          const SizedBox(height: 4),
          Text(
            title,
            style: Theme.of(
              context,
            ).textTheme.bodySmall?.copyWith(color: const Color(0xFF999999)),
          ),
        ],
      ),
    );
  }

  /// Card tiếp tục khóa học gần đây
  Widget _buildContinueCourseCard(BuildContext context, CourseProvider courseProvider) {
    final recentCourse = courseProvider.recentCourse!;
    final progress = recentCourse.progress?.completionPercentage ?? 0;

    return IOSCard(
      onTap: () async {
        // Navigate đến CourseScreen → CourseDetailScreen
        await Navigator.push(
          context,
          CupertinoPageRoute(
            builder: (context) => CourseScreen(
              autoNavigateToCourseId: recentCourse.id,
            ),
          ),
        );
        // Refresh courses sau khi quay lại
        if (mounted) {
          // ignore: use_build_context_synchronously
          context.read<CourseProvider>().fetchCourses(
            sortBy: 'joined_at',
            sortOrder: 'desc',
            forceRefresh: true,
          );
        }
      },
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              gradient: AppColors.primaryGradient,
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.play_circle_outline_rounded,
              color: Colors.white,
              size: 28,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Tiếp tục khóa học',
                  style: Theme.of(context).textTheme.bodyLarge
                      ?.copyWith(fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 4),
                Text(
                  recentCourse.title,
                  style: Theme.of(context).textTheme.bodySmall
                      ?.copyWith(color: const Color(0xFF666666)),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 6),
                // Progress bar
                Row(
                  children: [
                    Expanded(
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                          value: progress / 100,
                          backgroundColor: const Color(0xFFF2F2F7),
                          valueColor: AlwaysStoppedAnimation<Color>(
                            AppColors.primary,
                          ),
                          minHeight: 4,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      '${progress.toStringAsFixed(0)}%',
                      style: Theme.of(context).textTheme.labelSmall?.copyWith(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          const Icon(
            Icons.chevron_right_rounded,
            color: Color(0xFFCCCCCC),
          ),
        ],
      ),
    );
  }

  /// Card tiếp tục bài học vừa xem
  Widget _buildContinueLessonCard(BuildContext context, CourseProvider courseProvider) {
    final recentLessonInfo = courseProvider.recentLesson!;
    final lesson = recentLessonInfo.lesson;
    final course = recentLessonInfo.course;
    final progress = lesson.progress.completionPercentage;

    return IOSCard(
      onTap: () async {
        // Navigate đến CourseScreen → CourseDetailScreen → VideoPlayerScreen
        // Flow đầy đủ: HomeScreen → CourseScreen → CourseDetailScreen → VideoPlayerScreen
        await Navigator.push(
          context,
          CupertinoPageRoute(
            builder: (context) => CourseScreen(
              autoNavigateToCourseId: course.id,
              autoPlayLessonId: lesson.id,
              autoPlayLessonTitle: lesson.title,
            ),
          ),
        );
        // Refresh courses sau khi quay lại
        if (mounted) {
          // ignore: use_build_context_synchronously
          context.read<CourseProvider>().fetchCourses(
            sortBy: 'joined_at',
            sortOrder: 'desc',
            forceRefresh: true,
          );
        }
      },
      child: Row(
        children: [
          // Thumbnail của lesson
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: SizedBox(
              width: 52,
              height: 52,
              child: lesson.thumbnailPath != null
                  ? Image.network(
                      '${ApiConstants.baseUrl}${lesson.thumbnailPath}',
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) => Container(
                        color: const Color(0xFFF2F2F7),
                        child: const Icon(
                          Icons.play_circle_outline_rounded,
                          color: AppColors.primary,
                          size: 28,
                        ),
                      ),
                    )
                  : Container(
                      color: const Color(0xFFF2F2F7),
                      child: const Icon(
                        Icons.play_circle_outline_rounded,
                        color: AppColors.primary,
                        size: 28,
                      ),
                    ),
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Tiếp tục bài học',
                  style: Theme.of(context).textTheme.bodyLarge
                      ?.copyWith(fontWeight: FontWeight.w600),
                ),
                const SizedBox(height: 2),
                Text(
                  lesson.title,
                  style: Theme.of(context).textTheme.bodySmall
                      ?.copyWith(color: const Color(0xFF666666)),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 2),
                Text(
                  course.title,
                  style: Theme.of(context).textTheme.labelSmall
                      ?.copyWith(color: const Color(0xFF999999)),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 6),
                // Progress bar
                Row(
                  children: [
                    Expanded(
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                          value: progress / 100,
                          backgroundColor: const Color(0xFFF2F2F7),
                          valueColor: const AlwaysStoppedAnimation<Color>(
                            CupertinoColors.activeBlue,
                          ),
                          minHeight: 4,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Text(
                      _formatDuration(lesson.progress.lastPosition),
                      style: Theme.of(context).textTheme.labelSmall?.copyWith(
                        color: CupertinoColors.activeBlue,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: CupertinoColors.activeBlue.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              CupertinoIcons.play_fill,
              color: CupertinoColors.activeBlue,
              size: 16,
            ),
          ),
        ],
      ),
    );
  }

  /// Format thời gian từ giây
  String _formatDuration(int seconds) {
    final minutes = seconds ~/ 60;
    final remainingSeconds = seconds % 60;
    return '${minutes.toString().padLeft(2, '0')}:${remainingSeconds.toString().padLeft(2, '0')}';
  }
}
