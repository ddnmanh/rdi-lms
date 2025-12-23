import 'package:flutter/material.dart';
import '../../../theme/ios_widgets.dart';
import '../../../theme/app_colors.dart';
import '../../../core/utils/scroll_tracking.dart';
import '../../widgets/header_bar.dart';


class NotificationScreen extends StatefulWidget {
  const NotificationScreen({super.key});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> with AutomaticKeepAliveClientMixin, ScrollTracking {

  @override
  bool get wantKeepAlive => true;

  @override
  double get scrollThreshold => 20.0;

  @override
  Widget build(BuildContext context) {
    super.build(context);
    final notifications = [
      _NotificationItem(
        title: 'Hoàn thành khóa học',
        message: 'Chúc mừng! Bạn đã hoàn thành Giới thiệu về Flutter',
        time: '2 giờ trước',
        icon: Icons.check_circle_rounded,
        color: Colors.green,
        isUnread: true,
      ),
      _NotificationItem(
        title: 'Khóa học mới có sẵn!',
        message: 'Khóa học Phát triển Flutter Nâng cao hiện đã có sẵn',
        time: '6 giờ trước',
        icon: Icons.school_rounded,
        color: AppColors.primaryStart,
        isUnread: true,
      ),
      _NotificationItem(
        title: 'Nhắc nhở',
        message: 'Tiếp tục chuỗi học tập của bạn hôm nay',
        time: '1 ngày trước',
        icon: Icons.notifications_rounded,
        color: Colors.blue,
        isUnread: false,
      ),
      _NotificationItem(
        title: 'Cập nhật khóa học',
        message: 'Bài học mới đã được thêm vào Lập trình Dart',
        time: '2 ngày trước',
        icon: Icons.update_rounded,
        color: AppColors.primaryEnd,
        isUnread: false,
      ),
      _NotificationItem(
        title: 'Khóa học mới có sẵn!',
        message: 'Khóa học Phát triển JavaScript và React.js từ Zero đến Hero',
        time: '4 ngày trước',
        icon: Icons.school_rounded,
        color: AppColors.primaryStart,
        isUnread: true,
      ),
      _NotificationItem(
        title: 'Cập nhật khóa học',
        message: 'Bài học mới đã được thêm vào Lập trình Dart',
        time: '4 ngày trước',
        icon: Icons.update_rounded,
        color: AppColors.primaryEnd,
        isUnread: false,
      ),
    ];

    Future<void> _handleRefresh() async {
      // TODO: Implement refresh logic
    }

    return Scaffold(
      body: Stack(
        children: [
          RefreshIndicator(
            onRefresh: _handleRefresh,
            child: CustomScrollView(
              key: const PageStorageKey('notification_screen_scroll_view'),
              controller: scrollTrackingController,
              slivers: [
                const SliverToBoxAdapter(child: SizedBox(height: 50)),

                // iOS-style large title
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
                    child: Row(
                      children: [
                        AnimatedOpacity(
                          duration: const Duration(milliseconds: 400),
                          opacity: isScrollOverThreshold ? 0 : 1,
                          child: Text(
                            'Thông báo',
                            style: Theme.of(context).textTheme.displayLarge,
                          ),
                        ),
                        const Spacer(),
                        TextButton(
                          onPressed: () {},
                          child: Text(
                            'Đánh dấu đã đọc',
                            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                              color: AppColors.primary,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    )
                  ),
                ),



                // Notifications list
                SliverList(
                  delegate: SliverChildBuilderDelegate((context, index) {
                    final notification = notifications[index];
                    return IOSCard(
                      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                      onTap: () {},
                      child: Row(
                        children: [
                          // Icon
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: notification.color.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Icon(
                              notification.icon,
                              color: notification.color,
                              size: 24,
                            ),
                          ),
                          const SizedBox(width: 16),
                          // Content
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    if (notification.isUnread)
                                      Container(
                                        width: 8,
                                        height: 8,
                                        margin: const EdgeInsets.only(right: 8),
                                        decoration: const BoxDecoration(
                                          color: AppColors.primary,
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                    Expanded(
                                      child: Text(
                                        notification.title,
                                        style: Theme.of(context).textTheme.bodyLarge
                                            ?.copyWith(
                                              fontWeight: notification.isUnread
                                                  ? FontWeight.w600
                                                  : FontWeight.w400,
                                            ),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  notification.message,
                                  style: Theme.of(context).textTheme.bodySmall
                                      ?.copyWith(color: const Color(0xFF666666)),
                                  maxLines: 2,
                                  overflow: TextOverflow.ellipsis,
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  notification.time,
                                  style: Theme.of(context).textTheme.labelLarge
                                      ?.copyWith(color: const Color(0xFF999999)),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    );
                  }, childCount: notifications.length),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 100)),
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
              'Thông báo',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                  ),
              textAlign: TextAlign.center,
            ),
          ),
        ],
      )
    );
  }
}

class _NotificationItem {
  final String title;
  final String message;
  final String time;
  final IconData icon;
  final Color color;
  final bool isUnread;

  _NotificationItem({
    required this.title,
    required this.message,
    required this.time,
    required this.icon,
    required this.color,
    required this.isUnread,
  });
}
