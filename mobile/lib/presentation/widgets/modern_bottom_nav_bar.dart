import 'package:flutter/material.dart';
import 'dart:ui';

class ModernBottomNavBar extends StatelessWidget {
  final int currentIndex;
  final Function(int) onTap;
  final List<BottomNavItem> items;

  const ModernBottomNavBar({
    super.key,
    required this.currentIndex,
    required this.onTap,
    required this.items,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.fromLTRB(10, 0, 10, 20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        boxShadow: [
          BoxShadow(
            // ignore: deprecated_member_use
            color: Colors.black.withOpacity(0.3),
            blurRadius: 30,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(30),
        child: Material(
          type: MaterialType.transparency, // Làm Material trong suốt
          elevation: 10, // Áp dụng độ cao
          // ignore: deprecated_member_use
          shadowColor: Colors.black.withOpacity(0.3), // Áp dụng màu đổ bóng
          borderRadius: BorderRadius.circular(30),
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 20, sigmaY: 20),
            child: Container(
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.9),
                borderRadius: BorderRadius.circular(30),
                border: Border.all(
                  color: Colors.white.withOpacity(0.2),
                  width: 1.5,
                ),
              ),
              child: Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceAround,
                  children: List.generate(
                    items.length,
                    (index) => _buildNavItem(context, items[index], index),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(BuildContext context, BottomNavItem item, int index) {
    final isSelected = currentIndex == index;

    return Expanded(
      child: GestureDetector(
        onTap: () => onTap(index),
        behavior: HitTestBehavior.opaque,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeInOutCubic,
          padding: const EdgeInsets.symmetric(vertical: 6, horizontal: 10),
          decoration: BoxDecoration(
            gradient: isSelected
                ? LinearGradient(
                    colors: [
                      // ignore: deprecated_member_use
                      item.color.withOpacity(0.1),
                      // ignore: deprecated_member_use
                      item.color.withOpacity(0.05),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  )
                : null,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Stack(
                alignment: Alignment.center,
                children: [
                  // Vòng tròn nền có animation
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    curve: Curves.easeInOutCubic,
                    width: isSelected ? 34 : 0,
                    height: isSelected ? 34 : 0,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      // color: item.color.withOpacity(0.4),
                      // gradient: isSelected
                      //     ? LinearGradient(
                      //         colors: [
                      //           // ignore: deprecated_member_use
                      //           item.color.withOpacity(0.2),
                      //           // ignore: deprecated_member_use
                      //           item.color.withOpacity(0.05),
                      //         ],
                      //         begin: Alignment.topLeft,
                      //         end: Alignment.bottomRight,
                      //       )
                      //     : null,
                    ),
                  ),
                  // Icon với animation mượt mà
                  TweenAnimationBuilder<double>(
                    duration: const Duration(milliseconds: 300),
                    curve: Curves.easeInOutCubic,
                    tween: Tween<double>(begin: 0, end: isSelected ? 1.0 : 0.0),
                    builder: (context, value, child) {
                      return Icon(
                        isSelected ? item.activeIcon : item.icon,
                        color: Color.lerp(
                          const Color(0xFF999999),
                          item.color,
                          value,
                        ),
                        size: 22 + (6 * value), // Animation từ 22 đến 28
                      );
                    },
                  ),
                ],
              ),
              const SizedBox(height: 2),
              // Nhãn
              AnimatedDefaultTextStyle(
                duration: const Duration(milliseconds: 300),
                curve: Curves.easeInOutCubic,
                style: TextStyle(
                  fontSize: isSelected ? 8 : 8,
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.w500,
                  color: isSelected ? item.color : const Color(0xFF999999),
                  letterSpacing: 0.1,
                ),
                child: Text(item.label, maxLines: 1),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class BottomNavItem {
  final IconData icon;
  final IconData activeIcon;
  final String label;
  final Color color;

  BottomNavItem({
    required this.icon,
    required this.activeIcon,
    required this.label,
    required this.color,
  });
}
