import 'dart:math' as math;
import 'package:flutter/material.dart';

/// Widget spinner loading có thể phủ lên một trang/màn hình
///
/// Cách sử dụng:
/// ```dart
/// // Ví dụ 1: Wrap một Scaffold hoặc Container
/// SpinnerLoading(
///   isLoading: true,
///   child: Scaffold(
///     appBar: AppBar(title: Text('Trang của tôi')),
///     body: Center(child: Text('Nội dung trang')),
///   ),
/// )
///
/// // Ví dụ 2: Sử dụng với state management
/// SpinnerLoading(
///   isLoading: _isLoading,
///   child: ListView(
///     children: [/* ... */],
///   ),
/// )
///
/// // Ví dụ 3: Tùy chỉnh màu và icon
/// SpinnerLoading(
///   isLoading: isLoading,
///   size: 80,
///   color: Colors.blue,
///   icon: Icons.refresh,
///   child: YourWidget(),
/// )
/// ```
class SpinnerLoading extends StatefulWidget {
  /// Widget con (trang/nội dung cần được phủ lên)
  final Widget child;

  /// Có đang loading không
  final bool isLoading;

  /// Kích thước tổng thể của spinner (tương đương 64px trong CSS)
  final double size;

  /// Màu chấm + icon (mặc định xám giống #9ca3af80)
  final Color color;

  /// Icon ở giữa (mặc định Icons.school – mũ tốt nghiệp)
  final IconData icon;

  /// Màu nền overlay khi loading (mặc định trắng mờ)
  final Color overlayColor;

  const SpinnerLoading({
    Key? key,
    required this.child,
    required this.isLoading,
    this.size = 64,
    this.color = const Color(0x809CA3AF), // #9ca3af80
    this.icon = Icons.school,
    this.overlayColor = const Color(0x80FFFFFF), // Trắng mờ
  }) : super(key: key);

  @override
  State<SpinnerLoading> createState() => _SpinnerLoadingState();
}

class _SpinnerLoadingState extends State<SpinnerLoading>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    // duration = 1.2s giống CSS
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    );
    if (widget.isLoading) {
      _controller.repeat();
    }
  }

  @override
  void didUpdateWidget(SpinnerLoading oldWidget) {
    super.didUpdateWidget(oldWidget);
    // Điều khiển animation dựa trên trạng thái isLoading
    if (widget.isLoading && !oldWidget.isLoading) {
      _controller.repeat();
    } else if (!widget.isLoading && oldWidget.isLoading) {
      _controller.stop();
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        // Nội dung chính (trang)
        widget.child,

        // Overlay loading khi đang loading
        if (widget.isLoading)
          Positioned.fill(
            child: Container(
              color: widget.overlayColor,
              child: Center(
                child: _SpinnerWidget(
                  controller: _controller,
                  size: widget.size,
                  color: widget.color,
                  icon: widget.icon,
                ),
              ),
            ),
          ),
      ],
    );
  }
}

/// Widget spinner riêng biệt để tái sử dụng
class _SpinnerWidget extends StatelessWidget {
  final AnimationController controller;
  final double size;
  final Color color;
  final IconData icon;

  const _SpinnerWidget({
    Key? key,
    required this.controller,
    required this.size,
    required this.color,
    required this.icon,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final dotCount = 8;
    final dotSize = 6.0;
    final radius = size / 2 - dotSize;

    return SizedBox(
      width: size,
      height: size,
      child: Stack(
        alignment: Alignment.center,
        children: [
          // Phần vòng tròn 8 chấm quay
          RotationTransition(
            turns: Tween<double>(begin: 0.25, end: 1.25) // 90deg -> 450deg
                .animate(CurvedAnimation(
              parent: controller,
              curve: Curves.easeInOut, // gần giống cubic-bezier
            )),
            child: Stack(
              alignment: Alignment.center,
              children: List.generate(dotCount, (index) {
                // Góc cho từng chấm
                final angle = 2 * math.pi * index / dotCount;

                return Transform.translate(
                  offset: Offset(
                    radius * math.cos(angle),
                    radius * math.sin(angle),
                  ),
                  child: Container(
                    width: dotSize,
                    height: dotSize,
                    decoration: BoxDecoration(
                      color: color,
                      shape: BoxShape.circle,
                    ),
                  ),
                );
              }),
            ),
          ),

          // Icon ở giữa (tương đương #SPINNER_LOADING_ICON)
          IgnorePointer(
            child: Icon(
              icon,
              size: size * 0.3, // ~20px nếu size=64
              color: color,
            ),
          ),
        ],
      ),
    );
  }
}
