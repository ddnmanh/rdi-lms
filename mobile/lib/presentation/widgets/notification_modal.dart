import 'package:flutter/material.dart';
import '../../theme/app_colors.dart';

// Hướng dẫn sử dụng:
// await NotificationModal.show(
//   context,
//   title: 'Tải thất bại',
//   description: 'Không thể tải khóa học',
//   primaryButtonText: 'Thử lại',
//   onPrimaryPressed: () {
//     Navigator.of(context, rootNavigator: true).pop();
//     _onCourseTap(context, course);
//   },
//   secondaryButtonText: 'Đóng',
//   onSecondaryPressed: () {
//     Navigator.of(context, rootNavigator: true).pop();
//   },
// );
// Ví dụ sử dụng trong course_screen.dart:
// _onCourseTap(context, course);
// _onCourseTap(context, course);

/// Modal phong cách iOS dùng để hiển thị thông báo/confirm nhẹ.
class NotificationModal extends StatelessWidget {
  final String title;
  final String? description;
  final String primaryButtonText;
  final VoidCallback? onPrimaryPressed;
  final String? secondaryButtonText;
  final VoidCallback? onSecondaryPressed;
  final IconData icon;
  final Color accentColor;

  const NotificationModal({
    super.key,
    required this.title,
    this.description,
    this.primaryButtonText = 'Đã hiểu',
    this.onPrimaryPressed,
    this.secondaryButtonText,
    this.onSecondaryPressed,
    this.icon = Icons.info_rounded,
    this.accentColor = AppColors.primary,
  });

  /// Helper tiện gọi thẳng `NotificationModal.show(...)` thay vì `showDialog`.
  static Future<void> show(
    BuildContext context, {
    required String title,
    String? description,
    String primaryButtonText = 'Đã hiểu',
    VoidCallback? onPrimaryPressed,
    String? secondaryButtonText,
    VoidCallback? onSecondaryPressed,
    IconData icon = Icons.info_rounded,
    Color accentColor = AppColors.primary,
    bool barrierDismissible = true,
  }) {
    return showDialog<void>(
      context: context,
      barrierDismissible: barrierDismissible,
      builder: (_) => NotificationModal(
        title: title,
        description: description,
        primaryButtonText: primaryButtonText,
        onPrimaryPressed: onPrimaryPressed,
        secondaryButtonText: secondaryButtonText,
        onSecondaryPressed: onSecondaryPressed,
        icon: icon,
        accentColor: accentColor,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      insetPadding: const EdgeInsets.symmetric(horizontal: 32, vertical: 24),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(24, 28, 24, 16),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            _buildIcon(),
            const SizedBox(height: 16),
            Text(
              title,
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.w600,
                  ),
              textAlign: TextAlign.center,
            ),
            if (description != null && description!.trim().isNotEmpty) ...[
              const SizedBox(height: 8),
              Text(
                description!,
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                      color: const Color(0xFF6B7280),
                    ),
                textAlign: TextAlign.center,
              ),
            ],
            const SizedBox(height: 24),
            _buildButtons(context),
          ],
        ),
      ),
    );
  }

  Widget _buildIcon() {
    return Container(
      width: 56,
      height: 56,
      decoration: BoxDecoration(
        color: accentColor.withOpacity(0.12),
        shape: BoxShape.circle,
      ),
      child: Icon(
        icon,
        color: accentColor,
        size: 28,
      ),
    );
  }

  Widget _buildButtons(BuildContext context) {
    final buttons = <Widget>[];

    if (secondaryButtonText != null) {
      buttons.add(
        Expanded(
          child: OutlinedButton(
            onPressed: () {
              Navigator.of(context).maybePop();
              onSecondaryPressed?.call();
            },
            style: OutlinedButton.styleFrom(
              foregroundColor: const Color(0xFF4B5563),
              side: const BorderSide(color: Color(0xFFE5E7EB)),
            ),
            child: Text(secondaryButtonText!),
          ),
        ),
      );
    }

    if (buttons.isNotEmpty) {
      buttons.add(const SizedBox(width: 12));
    }

    buttons.add(
      Expanded(
        child: ElevatedButton(
          onPressed: () {
            Navigator.of(context).maybePop();
            onPrimaryPressed?.call();
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: accentColor,
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(vertical: 14),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          child: Text(primaryButtonText),
        ),
      ),
    );

    return Row(children: buttons);
  }
}

