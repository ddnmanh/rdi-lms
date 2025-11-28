import 'package:flutter/material.dart';
import 'package:mobile/core/constants/api_constants.dart';
import 'package:mobile/data/models/course_model.dart';
import 'package:mobile/presentation/widgets/spinner_loading.dart';
import 'package:provider/provider.dart';
import '../../../providers/course_provider.dart';
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

class _CourseScreenState extends State<CourseScreen> with AutomaticKeepAliveClientMixin, ScrollTracking {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CourseProvider>().fetchCourses(sortBy: 'joined_at', sortOrder: 'desc');
    });
  }

  Future<void> _handleRefresh() async {
    await context.read<CourseProvider>().fetchCourses(sortBy: 'joined_at', sortOrder: 'desc');
  }

  // CourseScreen đang mixin AutomaticKeepAliveClientMixin, nên nó phải override getter wantKeepAlive.
  //Trả về true báo cho Flutter rằng widget con này muốn giữ trạng thái sống khi bị đưa ra khỏi cây
  //(ví dụ chuyển tab, đổi màn hình trong PageView).
  //Nhờ vậy _CourseScreenState không bị dispose, dữ liệu đã tải và vị trí cuộn trong CustomScrollView được giữ nguyên khi quay lại,
  //tránh gọi fetchCourses() lại hoặc reset UI.
  @override
  bool get wantKeepAlive => true;

  @override
  double get scrollThreshold => 20.0;


  @override
  Widget build(BuildContext context) {
    super.build(context);
    final courseProvider = context.watch<CourseProvider>();

    return Scaffold(
      body: courseProvider.isLoadingAllCourses
          ?
           SpinnerLoading(
            isLoading: true,
            child: Scaffold(
              appBar: AppBar(title: Text('Trang của tôi')),
              body: Center(child: Text('Nội dung trang')),
            ),
          )
          : courseProvider.courses.isEmpty
          ? _buildEmptyState(context)
          : Stack(
            children: [
                RefreshIndicator(
                  onRefresh: _handleRefresh,
                  child: CustomScrollView(
                    key: const PageStorageKey('course_screen_scroll_view'),
                    controller: scrollTrackingController,
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
                            padding: const EdgeInsets.symmetric(
                              horizontal: 16,
                              vertical: 12,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFFF2F2F7),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                const Icon(
                                  Icons.search,
                                  color: Color(0xFF999999),
                                  size: 20,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Tìm kiếm khóa học...',
                                  style: Theme.of(context).textTheme.bodyMedium
                                      ?.copyWith(
                                        color: const Color(0xFF999999),
                                      ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),

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
              ],
            ),
    );
  }

  Widget _buildCourseItem(BuildContext context, Course course) {
    return IOSCard(
        margin: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 8,
        ),
        padding: EdgeInsets.zero,
        onTap: () async => await Navigator.push(
          // ignore: use_build_context_synchronously
          context,
          MaterialPageRoute(
            builder: (context) => CourseDetailScreen(courseId: course.id),
          ),
        ),
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
                      const Icon(
                        Icons.play_circle_outline_rounded,
                        size: 16,
                        color: AppColors.primary,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'Bắt đầu học',
                        style: Theme.of(context)
                            .textTheme
                            .bodySmall
                            ?.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w600,
                            ),
                      ),
                      const Spacer(),
                      const Icon(
                        Icons.chevron_right_rounded,
                        size: 20,
                        color: Color(0xFFCCCCCC),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      );
  }

  Widget _buildEmptyState(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFFF2F2F7),
              shape: BoxShape.circle,
            ),
            child: Icon(
              Icons.school_rounded,
              size: 64,
              // ignore: deprecated_member_use
              color: AppColors.primary.withOpacity(0.5),
            ),
          ),
          Text(
            'Chưa có khóa học',
            style: Theme.of(context).textTheme.headlineMedium,
          ),
          const SizedBox(height: 8),
          Text(
            'Khóa học sẽ được cập nhật',
            style: Theme.of(
              context,
            ).textTheme.bodyMedium?.copyWith(color: const Color(0xFF999999)),
          ),
        ],
      ),
    );
  }

}
