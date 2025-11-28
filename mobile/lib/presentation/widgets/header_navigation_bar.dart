import 'dart:ui';

import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';

/// Widget navigation bar với blur effect, hiển thị title dựa trên scroll state.
///
/// Widget này được thiết kế để sử dụng như overlay trên CustomScrollView,
/// kết hợp với [ScrollTracking] mixin để theo dõi trạng thái cuộn.
///
/// ## Cách sử dụng:
/// ```dart
/// class _MyScreenState extends State<MyScreen> with ScrollTracking {
///   @override
///   Widget build(BuildContext context) {
///     return Stack(
///       children: [
///         CustomScrollView(
///           controller: scrollTrackingController,
///           slivers: [...],
///         ),
///         HeaderNavigationBar(
///           title: 'Tiêu đề',
///           isVisible: isScrollOverThreshold,
///         ),
///       ],
///     );
///   }
/// }
/// ```
class HeaderNavigationBar extends StatelessWidget {
  /// Title hiển thị trên navigation bar
  final String? title;

  /// Widget title tùy chỉnh (ưu tiên hơn [title])
  final Widget? titleWidget;

  /// Widget bên trái (ví dụ: back button)
  final Widget? leading;

  /// Widget bên phải (ví dụ: action buttons)
  final Widget? trailing;

  /// Có hiển thị title hay không (thường dựa vào isScrollOverThreshold)
  final bool isVisible;

  /// Độ mờ của gradient trên cùng (0.0 - 1.0)
  final double topOpacity;

  /// Độ mờ của gradient dưới cùng (0.0 - 1.0)
  final double bottomOpacity;

  /// Màu nền cho gradient (mặc định là scaffoldBackgroundColor)
  final Color? backgroundColor;

  /// Độ blur (sigmaX và sigmaY)
  final double blurSigma;

  /// Chiều cao của navigation bar content (không bao gồm status bar)
  final double barHeight;

  /// Duration cho animation chiều cao
  final Duration animationDuration;

  /// Duration cho animation fade của title
  final Duration fadeDuration;

  /// Border bottom khi visible
  final Border? border;

  /// TextStyle cho title
  final TextStyle? titleStyle;

  /// Alignment cho title
  final MainAxisAlignment titleAlignment;

  const HeaderNavigationBar({
    super.key,
    this.title,
    this.titleWidget,
    this.leading,
    this.trailing,
    this.isVisible = false,
    this.topOpacity = 0.85,
    this.bottomOpacity = 0.7,
    this.backgroundColor,
    this.blurSigma = 20.0,
    this.barHeight = 44.0,
    this.animationDuration = const Duration(milliseconds: 250),
    this.fadeDuration = const Duration(milliseconds: 200),
    this.border,
    this.titleStyle,
    this.titleAlignment = MainAxisAlignment.center,
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final statusBarHeight = MediaQuery.of(context).padding.top;
    final totalHeight = statusBarHeight + barHeight;

    final bgColor = backgroundColor ?? theme.scaffoldBackgroundColor;

    // Xác định title widget cuối cùng
    final effectiveTitleWidget = titleWidget ??
        (title != null
            ? Text(
                title ?? '',
                style: titleStyle ??
                    theme.textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.w600,
                    ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                textAlign: titleAlignment == MainAxisAlignment.center ? TextAlign.center : TextAlign.start,
              )
            : null);

    return Positioned(
      top: 0,
      left: 0,
      right: 0,
      child: ClipRect(
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: blurSigma, sigmaY: blurSigma),
          child: Container(
            height: totalHeight,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  // ignore: deprecated_member_use
                  bgColor.withOpacity(topOpacity),
                  // ignore: deprecated_member_use
                  bgColor.withOpacity(bottomOpacity),
                ],
              ),
              border: isVisible ? border : null,
            ),
            child: _buildContent(context, statusBarHeight, effectiveTitleWidget),
          ),
        ),
      ),
    );
  }

  Widget? _buildContent(
    BuildContext context,
    double statusBarHeight,
    Widget? effectiveTitleWidget,
  ) {
    if (effectiveTitleWidget == null && leading == null && trailing == null) {
      return null;
    }

    return Stack(
      children: [
        // Content area (bên dưới status bar)
        Positioned(
          left: 0,
          right: 0,
          top: statusBarHeight,
          height: barHeight,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 8.0),
            child: Stack(
              alignment: Alignment.center,
              children: [
                // Leading - luôn hiển thị
                if (leading != null)
                  Align(
                    alignment: Alignment.centerLeft,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        leading!,
                        const SizedBox(width: 8),
                      ],
                    ),
                  ),

                // Title - chỉ ẩn/hiện title
                if (effectiveTitleWidget != null)
                  Align(
                    alignment: titleAlignment == MainAxisAlignment.center
                        ? Alignment.center
                        : Alignment.centerLeft,
                    child: Padding(
                      padding: titleAlignment == MainAxisAlignment.center
                          ? const EdgeInsets.symmetric(horizontal: 80.0)
                          : EdgeInsets.only(left: leading != null ? 80.0 : 0),
                      child: AnimatedOpacity(
                        duration: fadeDuration,
                        opacity: isVisible ? 1.0 : 0.0,
                        child: AnimatedSlide(
                          duration: animationDuration,
                          curve: Curves.easeOutCubic,
                          offset: isVisible ? Offset.zero : const Offset(0, -0.3),
                          child: effectiveTitleWidget,
                        ),
                      ),
                    ),
                  ),

                // Trailing - luôn hiển thị
                if (trailing != null)
                  Align(
                    alignment: Alignment.centerRight,
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const SizedBox(width: 8),
                        trailing!,
                      ],
                    ),
                  ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

/// Tiện ích để tạo leading button với style iOS
class HeaderNavigationBarBackButton extends StatelessWidget {
  final VoidCallback? onPressed;
  final Color? color;
  final String? label;

  const HeaderNavigationBarBackButton({
    super.key,
    this.onPressed,
    this.color,
    this.label,
  });

  @override
  Widget build(BuildContext context) {
    final effectiveColor = color ?? Color(0xFF007AFF);

    return GestureDetector(
      onTap: onPressed ?? () => Navigator.of(context).maybePop(),
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8.0, vertical: 8.0),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              CupertinoIcons.chevron_left,
              size: 22,
              color: effectiveColor,
            ),
            if (label != null) ...[
              const SizedBox(width: 0),
              Text(
                label!,
                style: TextStyle(
                  color: effectiveColor,
                  fontSize: 16,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

/// Tiện ích để tạo action button với style phù hợp
class HeaderNavigationBarAction extends StatelessWidget {
  final VoidCallback? onPressed;
  final Widget icon;
  final Color? color;

  const HeaderNavigationBarAction({
    super.key,
    this.onPressed,
    required this.icon,
    this.color,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onPressed,
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8.0, vertical: 8.0),
        child: icon,
      ),
    );
  }
}

