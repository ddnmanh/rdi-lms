import 'package:flutter/material.dart';

/// Mixin cung cấp logic scroll tracking để hiển thị/ẩn pinned header.
///
/// Sử dụng mixin này cho các StatefulWidget cần theo dõi scroll position
/// để hiển thị header cố định khi người dùng cuộn xuống.
///
/// ## Cách sử dụng:
/// ```dart
/// class _MyScreenState extends State<MyScreen>
///     with ScrollTracking {
///
///   @override
///   double get scrollThreshold => 30.0; // Tùy chỉnh ngưỡng scroll (mặc định: 20.0)
///
///   @override
///   Widget build(BuildContext context) {
///     return CustomScrollView(
///       controller: scrollController, // Sử dụng scrollController từ mixin
///       slivers: [
///         // ...
///       ],
///     );
///     // Sử dụng isScrollOverThreshold để kiểm tra trạng thái
///   }
/// }
/// ```
mixin ScrollTracking<T extends StatefulWidget> on State<T> {
  /// ScrollController được quản lý bởi mixin.
  /// Sử dụng controller này cho CustomScrollView hoặc ListView.
  late final ScrollController scrollTrackingController;

  /// Trạng thái cuộn vượt qua ngưỡng.
  /// `true` khi scroll offset >= [scrollThreshold].
  bool isScrollOverThreshold = false;

  /// Ngưỡng cuộn cần theo dõi
  /// Override getter này để tùy chỉnh ngưỡng.
  double get scrollThreshold => 20.0;

  /// Điều kiện để đổi trạng thái [isScrollOverThreshold] khi cuộn
  /// `true` khi scroll offset < [scrollThreshold].
  bool get overIsLessThanThreshold => false;


  /// Lưu vị trí cuộn gần nhất để trigger rebuild khi thay đổi.
  double _lastScrollOffset = 0.0;

  // Vị trí đang cuộn
  double get currentScrollPosition => _lastScrollOffset;

  @override
  void initState() {
    super.initState();
    scrollTrackingController = ScrollController()..addListener(_handleScrollChange);
  }

  @override
  void dispose() {
    scrollTrackingController.removeListener(_handleScrollChange);
    scrollTrackingController.dispose();
    super.dispose();
  }

  /// Xử lý sự kiện scroll và cập nhật [isScrollOverThreshold].
  void _handleScrollChange() {
    if (!scrollTrackingController.hasClients) return;

    final offset = scrollTrackingController.offset;
    final shouldShow = overIsLessThanThreshold ? offset < scrollThreshold : offset >= scrollThreshold;

    if (shouldShow != isScrollOverThreshold || offset != _lastScrollOffset) {
      setState(() {
        _lastScrollOffset = offset;
        isScrollOverThreshold = shouldShow;
      });
    }
  }

  /// Callback khi trạng thái pinned header thay đổi.
  /// Override method này nếu cần xử lý thêm logic khi trạng thái thay đổi.
  @protected
  void onPinnedHeaderStateChanged(bool isVisible) {}
}

