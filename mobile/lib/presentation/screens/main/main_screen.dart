import 'package:flutter/material.dart';
import 'dart:ui';
import '../home/home_screen.dart';
import '../course/course_screen.dart';
import '../statistical/statistical_screen.dart';
import '../notification/notification_screen.dart';
import '../profile/profile_screen.dart';
import '../../widgets/modern_bottom_nav_bar.dart';
import '../../../theme/app_colors.dart';

class MainScreen extends StatefulWidget {
  const MainScreen({super.key});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _selectedIndex = 0;

  final List<Widget> _screens = [
    const HomeScreen(),
    const CourseScreen(),
    const StatisticalScreen(),
    const NotificationScreen(),
    const ProfileScreen(),
  ];

  final List<BottomNavItem> _navItems = [
    BottomNavItem(
      icon: Icons.home_rounded,
      activeIcon: Icons.home_rounded,
      label: 'Trang chủ',
      color: AppColors.primaryStart,
    ),
    BottomNavItem(
      icon: Icons.book_rounded,
      activeIcon: Icons.book_rounded,
      label: 'Khóa học',
      color: const Color(0xFF9C4DFF),
    ),
    BottomNavItem(
      icon: Icons.bar_chart_rounded,
      activeIcon: Icons.bar_chart_rounded,
      label: 'Thống kê',
      color: AppColors.primaryEnd,
    ),
    BottomNavItem(
      icon: Icons.notifications_rounded,
      activeIcon: Icons.notifications_rounded,
      label: 'Thông báo',
      color: const Color(0xFFFF6B9D),
    ),
    BottomNavItem(
      icon: Icons.person_rounded,
      activeIcon: Icons.person_rounded,
      label: 'Cá nhân',
      color: const Color(0xFF6B66FF),
    ),
  ];

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBody: true,
      body: IndexedStack(index: _selectedIndex, children: _screens),
      bottomNavigationBar: ModernBottomNavBar(
        currentIndex: _selectedIndex,
        onTap: _onItemTapped,
        items: _navItems,
      ),
    );
  }
}
