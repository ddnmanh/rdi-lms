import 'dart:ui';

import 'package:flutter/material.dart';

/// Widget overlay blur cho status bar, có thể tái sử dụng cho nhiều màn hình
///
/// Mặc định luôn hiển thị với chiều cao bằng status bar.
/// Khi [expanded] = true, sẽ mở rộng thêm chiều cao để hiển thị [child].
class HeaderBar extends StatelessWidget {
  /// Độ mờ của gradient trên cùng (0.0 - 1.0)
  final double topOpacity;

  /// Độ mờ của gradient dưới cùng (0.0 - 1.0)
  final double bottomOpacity;

  /// Màu nền cho gradient (mặc định là scaffoldBackgroundColor)
  final Color? backgroundColor;

  /// Độ blur (sigmaX và sigmaY)
  final double blurSigma;

  /// Widget con bên trong overlay (tùy chọn) - chỉ hiển thị khi expanded = true
  final Widget? child;

  /// Padding cho widget con
  final EdgeInsets? padding;

  /// Có bỏ qua tương tác người dùng không
  final bool ignoringPointer;

  /// Chiều cao tùy chỉnh (nếu null sẽ dùng status bar height)
  final double? height;

  /// Có đang ở trạng thái mở rộng (ví dụ: sau khi cuộn) không
  final bool expanded;

  /// Chiều cao cộng thêm khi ở trạng thái mở rộng
  final double expandedExtraHeight;

  /// Duration cho animation chiều cao
  final Duration animationDuration;

  /// Duration cho animation fade của child
  final Duration childFadeDuration;

  const HeaderBar({
    super.key,
    this.topOpacity = 0.5,
    this.bottomOpacity = 0.1,
    this.backgroundColor,
    this.blurSigma = 20.0,
    this.child,
    this.padding,
    this.ignoringPointer = true,
    this.height,
    this.expanded = false,
    this.expandedExtraHeight = 36.0,
    this.animationDuration = const Duration(milliseconds: 300),
    this.childFadeDuration = const Duration(milliseconds: 200),
  });

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final statusBarHeight = MediaQuery.of(context).padding.top;
    final baseHeight = height ?? statusBarHeight;
    final overlayHeight =
        expanded ? baseHeight + expandedExtraHeight : baseHeight;

    final bgColor = backgroundColor ?? theme.scaffoldBackgroundColor;

    return Positioned(
      top: 0,
      left: 0,
      right: 0,
      child: IgnorePointer(
        ignoring: ignoringPointer,
        child: ClipRect(
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: blurSigma, sigmaY: blurSigma),
            child: AnimatedContainer(
              duration: animationDuration,
              curve: Curves.easeOut,
              height: overlayHeight,
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
              ),
              child: child != null
                  ? Stack(
                      children: [
                        Positioned(
                          left: 0,
                          right: 0,
                          bottom: 0,
                          child: AnimatedOpacity(
                            duration: childFadeDuration,
                            opacity: expanded ? 1.0 : 0.0,
                            child: AnimatedSlide(
                              duration: animationDuration,
                              curve: Curves.easeOut,
                              offset: expanded ? Offset.zero : const Offset(0, -0.3),
                              child: Padding(
                                padding: padding ?? EdgeInsets.zero,
                                child: child,
                              ),
                            ),
                          ),
                        ),
                      ],
                    )
                  : null,
            ),
          ),
        ),
      ),
    );
  }
}

