import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:LMS/core/constants/api_constants.dart';
import 'package:LMS/presentation/screens/notification/notification_screen.dart';
import 'package:provider/provider.dart';
import '../../../providers/auth_provider.dart';
import '../../../theme/ios_widgets.dart';
import '../../../theme/app_colors.dart';
import '../auth/login_screen.dart';
import 'update_profile_screen.dart';
import '../../../core/utils/scroll_tracking.dart';
import '../../widgets/header_bar.dart';

class ProfileScreen extends StatefulWidget {
  const ProfileScreen({super.key});

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}


class _ProfileScreenState extends State<ProfileScreen> with AutomaticKeepAliveClientMixin, ScrollTracking {
  @override
  bool get wantKeepAlive => true;

  @override
  double get scrollThreshold => 20.0;

  Future<void> _handleRefresh() async {
    // TODO: Implement refresh logic
  }


  @override
  Widget build(BuildContext context) {
    super.build(context);
    final user = context.watch<AuthProvider>().user;

    if (user == null) {
      return const Scaffold(body: Center(child: Text('Not logged in')));
    }

    return Scaffold(
      body: Stack(
        children: [
          CustomScrollView(
            key: const PageStorageKey('profile_screen_scroll_view'),
            controller: scrollTrackingController,
            slivers: [
              const SliverToBoxAdapter(child: SizedBox(height: 120)),
              // User info card
              SliverToBoxAdapter(
                child: Column(
                  children: [
                    // Avatar with gradient border
                    CircleAvatar(
                      radius: 50,
                      backgroundColor: const Color(0xFF999999),
                      backgroundImage: (user.avatarPath != null && user.avatarPath!.isNotEmpty)
                          ? NetworkImage('${ApiConstants.baseUrl}${user.avatarPath}')
                          : null,
                      child: (user.avatarPath != null && user.avatarPath!.isNotEmpty)
                          ? null
                          : Text(
                              user.fullname.isNotEmpty
                                  ? user.fullname[0].toUpperCase()
                                  : 'U',
                              style: const TextStyle(
                                fontSize: 40,
                                fontWeight: FontWeight.bold,
                                color: AppColors.primary,
                              ),
                            ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      user.fullname,
                      style: Theme.of(
                        context,
                      ).textTheme.headlineMedium?.copyWith(color: const Color.fromARGB(255, 24, 24, 24)),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      user.email,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        // ignore: deprecated_member_use
                        color: const Color(0xFF999999)
                      ),
                      textAlign: TextAlign.center,
                    ),
                    if (user.birthday != null) ...[
                      const SizedBox(height: 12),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          // ignore: deprecated_member_use
                          color: const Color(0xFF999999).withOpacity(0.2),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(
                              Icons.cake_outlined,
                              color: const Color(0xFF999999),
                              size: 16,
                            ),
                            const SizedBox(width: 6),
                            Text(
                              user.birthday!,
                              style: const TextStyle(
                                color: const Color(0xFF999999),
                                fontSize: 14,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ],
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 28)),

              // Section header
              // const SliverToBoxAdapter(
              //   child: IOSSectionHeader(title: 'Account Settings'),
              // ),

              // Settings list
              SliverToBoxAdapter(
                child: IOSGroupedList(
                  children: [
                    IOSListTile(
                      leading: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          // ignore: deprecated_member_use
                          color: AppColors.primary.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(
                          Icons.person_outline_rounded,
                          color: AppColors.primary,
                          size: 20,
                        ),
                      ),
                      title: 'Chỉnh sửa hồ sơ',
                      trailing: const Icon(
                        Icons.chevron_right_rounded,
                        color: Color(0xFFCCCCCC),
                      ),
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const UpdateProfileScreen(),
                          ),
                        );
                      },
                    ),
                    IOSListTile(
                      leading: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          // ignore: deprecated_member_use
                          color: Colors.blue.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(
                          Icons.language_rounded,
                          color: Colors.blue,
                          size: 20,
                        ),
                      ),
                      title: 'Ngôn ngữ',
                      subtitle: 'Tiếng Việt',
                      trailing: const Icon(
                        Icons.chevron_right_rounded,
                        color: Color(0xFFCCCCCC),
                      ),
                      onTap: () {},
                    ),
                  ],
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 28)),

              // Section header
              // const SliverToBoxAdapter(child: IOSSectionHeader(title: 'Hỗ trợ')),

              // Support list
              SliverToBoxAdapter(
                child: IOSGroupedList(
                  children: [
                    IOSListTile(
                      leading: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          // ignore: deprecated_member_use
                          color: Colors.green.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(
                          Icons.help_outline_rounded,
                          color: Colors.green,
                          size: 20,
                        ),
                      ),
                      title: 'Trung tâm trợ giúp',
                      trailing: const Icon(
                        Icons.chevron_right_rounded,
                        color: Color(0xFFCCCCCC),
                      ),
                      onTap: () {},
                    ),
                    IOSListTile(
                      leading: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          // ignore: deprecated_member_use
                          color: Colors.purple.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(
                          Icons.info_outline_rounded,
                          color: Colors.purple,
                          size: 20,
                        ),
                      ),
                      title: 'Thông tin thêm',
                      trailing: const Icon(
                        Icons.chevron_right_rounded,
                        color: Color(0xFFCCCCCC),
                      ),
                      onTap: () {},
                    ),
                  ],
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 28)),

              // Logout button
              SliverToBoxAdapter(
                child: Container(
                  margin: const EdgeInsets.symmetric(horizontal: 16),
                  child: IOSButton(
                    text: 'Đăng xuất',
                    filled: false,
                    color: Colors.red,
                    icon: Icons.logout_rounded,
                    onPressed: () async {
                      await context.read<AuthProvider>().logout();
                      // if (context.mounted) {
                      //   Navigator.of(context).pushAndRemoveUntil(
                      //     MaterialPageRoute(
                      //       builder: (context) => const LoginScreen(),
                      //     ),
                      //     (route) => false,
                      //   );
                      // }
                    },
                  ),
                ),
              ),

              const SliverToBoxAdapter(child: SizedBox(height: 140)),
            ],
          ),
          HeaderBar(
            topOpacity: 1,
            bottomOpacity: 0,
            expanded: isScrollOverThreshold,
            expandedExtraHeight: 30,
            ignoringPointer: !isScrollOverThreshold,
            padding: const EdgeInsets.only(bottom: 10),
            child: Text(
              'Hồ sơ',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                  ),
              textAlign: TextAlign.center,
            ),
          )
        ],
      )
    );
  }
}
