import 'dart:async';
import 'dart:ui';
import 'package:dio/dio.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../../providers/theme_provider.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:media_kit/media_kit.dart';
import 'package:media_kit_video/media_kit_video.dart';
import '../../../core/constants/api_constants.dart';
import '../../../data/models/auto_signature_model.dart';
import '../../../data/models/lesson_model.dart';
import '../../../data/models/lesson_note_model.dart';
import 'package:LMS/presentation/widgets/header_navigation_bar.dart';
import 'package:LMS/core/utils/scroll_tracking.dart';

class VideoPlayerScreen extends StatefulWidget {
  final int lessonId;
  final String? initialTitle; // Optional: hiển thị title trong lúc loading

  const VideoPlayerScreen({
    super.key,
    required this.lessonId,
    this.initialTitle,
  });

  @override
  State<VideoPlayerScreen> createState() => _VideoPlayerScreenState();
}

class _VideoPlayerScreenState extends State<VideoPlayerScreen>
    with WidgetsBindingObserver, TickerProviderStateMixin, ScrollTracking {
  late final Player _player;
  late final VideoController _videoController;

  // State
  bool _isLoading = true;
  String? _error;
  String? _authToken;
  bool _showControls = true;
  bool _isPlaying = false;
  bool _isBuffering = false;
  Duration _position = Duration.zero;
  Duration _duration = Duration.zero;
  bool _isSeeking = false;
  Duration _seekPosition = Duration.zero;

  // Lesson data từ API (luôn tải lại khi mở screen)
  Lesson? _lesson;

  // Progress tracking
  Duration _maxWatchedPosition = Duration.zero;
  bool _isProgressSaved = false;

  // New features state
  bool _isFullscreen = false;
  bool _isLocked = false;
  double _playbackSpeed = 1.0;
  BoxFit _videoFit = BoxFit.contain;

  int? _doubleTapSeekDirection; // -1 left, 1 right, null none
  Timer? _hideControlsTimer;
  Timer? _doubleTapTimer;

  // Notes state
  List<LessonNote> _notes = [];
  bool _isLoadingNotes = false;

  // Animation controllers
  late AnimationController _controlsAnimController;
  late Animation<double> _controlsFadeAnimation;
  late AnimationController _seekAnimController;
  late Animation<double> _seekAnimation;

  // Playback speed options
  static const List<double> _speedOptions = [0.5, 0.75, 1.0, 1.25, 1.5, 2.0];

  // Video fit options
  static const Map<BoxFit, String> _fitOptions = {
    BoxFit.contain: 'Vừa màn hình',
    BoxFit.cover: 'Phủ kín',
    BoxFit.fill: 'Kéo giãn',
  };

  // iOS 18 Design System Colors
  static const Color _accentBlue = Color(0xFF0A84FF);
  static const Color _accentOrange = Color(0xFFFF9F0A);
  static const Color _systemBlack = Color(0xFF000000);
  static const Color _systemGray5 = Color(0xFF2C2C2E);
  static const Color _systemGray6 = Color(0xFF1C1C1E);
  static const Color _white = Color(0xFFFFFFFF);
  static const Color _white60 = Color(0x99FFFFFF);
  static const Color _white40 = Color(0x66FFFFFF);
  static const Color _white20 = Color(0x33FFFFFF);
  static const Color _white10 = Color(0x1AFFFFFF);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);

    // Initialize animations
    _controlsAnimController = AnimationController(
      duration: const Duration(milliseconds: 200),
      vsync: this,
    );
    _controlsFadeAnimation = CurvedAnimation(
      parent: _controlsAnimController,
      curve: Curves.easeOut,
    );
    _controlsAnimController.forward();

    _seekAnimController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _seekAnimation = CurvedAnimation(
      parent: _seekAnimController,
      curve: Curves.easeOutCubic,
    );

    // Initialize player
    _player = Player();
    _videoController = VideoController(_player);

    _setupOrientations();
    _setupListeners();
    _initializePlayer();
    _fetchNotes();
  }

  void _setupOrientations() {
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.landscapeLeft,
      DeviceOrientation.landscapeRight,
    ]);
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  }

  void _setupListeners() {
    _player.stream.playing.listen((playing) {
      if (mounted) setState(() => _isPlaying = playing);
    });

    _player.stream.position.listen((position) {
      if (mounted && !_isSeeking) {
        setState(() {
          _position = position;
          // Track maximum watched position
          if (position > _maxWatchedPosition) {
            _maxWatchedPosition = position;
          }
        });
      }
    });

    _player.stream.duration.listen((duration) {
      if (mounted) setState(() => _duration = duration);
    });

    _player.stream.buffering.listen((buffering) {
      if (mounted) setState(() => _isBuffering = buffering);
    });

    _player.stream.error.listen((error) {
      if (mounted && error.isNotEmpty) {
        setState(() {
          _error = error;
          _isLoading = false;
        });
      }
    });
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.paused) {
      _player.pause();
    }
  }

  Future<void> _initializePlayer() async {
    try {
      setState(() {
        _isLoading = true;
        _error = null;
      });

      const storage = FlutterSecureStorage();
      _authToken = await storage.read(key: 'access_token');

      if (_authToken == null || _authToken!.isEmpty) {
        throw Exception('Không tìm thấy token xác thực');
      }

      // Tải lesson detail từ API
      await _fetchLessonDetail();

      // Kiểm tra lesson đã được load thành công
      if (_lesson == null) {
        throw Exception('Không thể tải thông tin bài học');
      }

      final autoSignature = await _fetchAutoSignature();

      if (autoSignature == null) {
        throw Exception('Không thể lấy được URL video');
      }

      final streamUrl = autoSignature.fullStreamUrl;
      final videoType = autoSignature.type;
      debugPrint('Video Type: $videoType, Stream URL: $streamUrl');

      await _player.open(Media(streamUrl), play: false);

      // Tự động tua đến vị trí đã xem trước đó
      final savedWatchedDuration = _lesson!.progress.watchedDuration;
      debugPrint('Saved duration: $savedWatchedDuration');
      if (savedWatchedDuration > 0) {
        // Đợi video load xong (có duration) trước khi seek
        await _waitForVideoReady();
        if (mounted) {
          await _player.seek(Duration(seconds: savedWatchedDuration));
          debugPrint('Restored video position to: ${savedWatchedDuration}s');
        }
      }

      if (mounted) setState(() => _isLoading = false);
    } catch (e) {
      debugPrint('Video initialization error: $e');
      if (mounted) {
        setState(() {
          _error = _getErrorMessage(e);
          _isLoading = false;
        });
      }
    }
  }

  /// Fetch lesson detail từ API
  Future<void> _fetchLessonDetail() async {
    final dio = Dio()
      ..options.baseUrl = ApiConstants.baseUrl
      ..options.connectTimeout = const Duration(seconds: 10)
      ..options.receiveTimeout = const Duration(seconds: 10)
      ..options.headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_authToken',
      };

    final response = await dio.get(
      ApiConstants.getLessonDetail(widget.lessonId),
    );

    if (response.statusCode == 200 && response.data['success'] == true) {
      final lessonData = response.data['data'];
      if (mounted) {
        setState(() {
          _lesson = Lesson.fromJson(lessonData);
        });
      }
      debugPrint('Loaded lesson detail: ${_lesson!.title}');
      debugPrint('Progress: ${_lesson!.progress}');
    } else {
      throw Exception(response.data['message'] ?? 'Không thể tải bài học');
    }
  }

  /// Đợi video sẵn sàng (có duration) trước khi thực hiện các thao tác seek
  Future<void> _waitForVideoReady() async {
    // Nếu đã có duration thì return ngay
    if (_player.state.duration.inMilliseconds > 0) {
      return;
    }

    // Đợi duration stream emit giá trị > 0
    final completer = Completer<void>();
    StreamSubscription<Duration>? subscription;

    subscription = _player.stream.duration.listen((duration) {
      if (duration.inMilliseconds > 0 && !completer.isCompleted) {
        completer.complete();
        subscription?.cancel();
      }
    });

    // Timeout sau 10 giây
    return completer.future.timeout(
      const Duration(seconds: 10),
      onTimeout: () {
        subscription?.cancel();
        debugPrint('Timeout waiting for video ready');
      },
    );
  }

  Future<AutoSignature?> _fetchAutoSignature() async {
    final dio = Dio()
      ..options.baseUrl = ApiConstants.baseUrl
      ..options.connectTimeout = const Duration(seconds: 10)
      ..options.receiveTimeout = const Duration(seconds: 10)
      ..options.headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $_authToken',
      };

    final response = await dio.get(
      ApiConstants.getAutoSignatureUrl(widget.lessonId),
    );

    if (response.statusCode == 200 && response.data['success'] == true) {
      return AutoSignature.fromJson(response.data['data']);
    }
    throw Exception(response.data['message'] ?? 'Lỗi không xác định');
  }

  /// Fetch notes từ API
  Future<void> _fetchNotes() async {
    if (_authToken == null) {
      const storage = FlutterSecureStorage();
      _authToken = await storage.read(key: 'access_token');
    }

    if (_authToken == null) return;

    try {
      setState(() => _isLoadingNotes = true);
      final dio = Dio()
        ..options.baseUrl = ApiConstants.baseUrl
        ..options.connectTimeout = const Duration(seconds: 10)
        ..options.receiveTimeout = const Duration(seconds: 10)
        ..options.headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_authToken',
        };

      final response = await dio.get(
        ApiConstants.getLessonNotes(widget.lessonId),
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> notesData = response.data['data'];
        if (mounted) {
          setState(() {
            _notes = notesData.map((e) => LessonNote.fromJson(e)).toList();
            _isLoadingNotes = false;
          });
        }
      }
    } catch (e) {
      debugPrint('Error fetching notes: $e');
      if (mounted) setState(() => _isLoadingNotes = false);
    }
  }

  /// Lưu tiến độ xem video lên server
  Future<void> _saveProgress() async {
    // Prevent duplicate saves
    if (_isProgressSaved) return;
    _isProgressSaved = true;

    // Only save if user has watched something
    if (_maxWatchedPosition.inSeconds == 0 && _position.inSeconds == 0) {
      return;
    }

    try {
      final dio = Dio()
        ..options.baseUrl = ApiConstants.baseUrl
        ..options.connectTimeout = const Duration(seconds: 10)
        ..options.receiveTimeout = const Duration(seconds: 10)
        ..options.headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_authToken',
        };

      final watchedDuration = _maxWatchedPosition.inSeconds > 0
          ? _maxWatchedPosition.inSeconds
          : _position.inSeconds;
      final lastPosition = _position.inSeconds;

      debugPrint(_lesson?.toString() ?? 'Lesson is null');
      debugPrint(
        'Watched duration: $watchedDuration, Last position: $lastPosition',
      );
      debugPrint('Lesson ID: ${widget.lessonId}');
      debugPrint('Auth token: $_authToken');
      debugPrint(
        'Progress saved: watched=$watchedDuration, position=$lastPosition',
      );
      debugPrint('API URL: ${ApiConstants.studentProgress}');

      await dio.post(
        ApiConstants.studentProgress,
        data: {'lesson_id': widget.lessonId, 'last_position': lastPosition},
      );

      debugPrint(
        'Progress saved: watched=$watchedDuration, position=$lastPosition',
      );
    } catch (e) {
      debugPrint('Failed to save progress: $e');
    }
  }

  String _getErrorMessage(dynamic error) {
    final errorStr = error.toString().toLowerCase();
    if (errorStr.contains('network') || errorStr.contains('socket')) {
      return 'Không có kết nối mạng';
    } else if (errorStr.contains('401')) {
      return 'Phiên đăng nhập hết hạn';
    } else if (errorStr.contains('403')) {
      return 'Không có quyền truy cập';
    } else if (errorStr.contains('404')) {
      return 'Video không tồn tại';
    }
    return 'Đã xảy ra lỗi';
  }

  // ===== CONTROL ACTIONS =====

  void _togglePlayPause() {
    HapticFeedback.lightImpact();
    _player.playOrPause();
    _resetHideTimer();
  }

  void _seekRelative(int seconds) {
    HapticFeedback.selectionClick();
    final duration = _duration.inMilliseconds > 0
        ? _duration
        : Duration(seconds: _lesson?.duration ?? 0);
    var newPosition = _position + Duration(seconds: seconds);
    newPosition = newPosition.clamp(Duration.zero, duration);
    _player.seek(newPosition);
  }

  void _handleDoubleTap(TapDownDetails details, double screenWidth) {
    if (_isLocked) return;

    final tapX = details.localPosition.dx;
    final isLeft = tapX < screenWidth / 2;

    HapticFeedback.mediumImpact();

    setState(() {
      _doubleTapSeekDirection = isLeft ? -1 : 1;
    });

    _seekRelative(isLeft ? -10 : 10);
    _seekAnimController.forward(from: 0);

    _doubleTapTimer?.cancel();
    _doubleTapTimer = Timer(const Duration(milliseconds: 800), () {
      if (mounted) setState(() => _doubleTapSeekDirection = null);
    });
  }

  void _toggleFullscreen() {
    HapticFeedback.mediumImpact();
    setState(() => _isFullscreen = !_isFullscreen);

    if (_isFullscreen) {
      SystemChrome.setPreferredOrientations([
        DeviceOrientation.landscapeLeft,
        DeviceOrientation.landscapeRight,
      ]);
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
    } else {
      SystemChrome.setPreferredOrientations([
        DeviceOrientation.portraitUp,
        DeviceOrientation.landscapeLeft,
        DeviceOrientation.landscapeRight,
      ]);
      SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    }
  }

  void _toggleLock() {
    HapticFeedback.mediumImpact();
    setState(() => _isLocked = !_isLocked);
    if (_isLocked) {
      _hideControlsTimer?.cancel();
    } else {
      _resetHideTimer();
    }
  }

  void _setPlaybackSpeed(double speed) {
    HapticFeedback.selectionClick();
    setState(() => _playbackSpeed = speed);
    _player.setRate(speed);
    Navigator.pop(context);
  }

  void _setVideoFit(BoxFit fit) {
    HapticFeedback.selectionClick();
    setState(() => _videoFit = fit);
    Navigator.pop(context);
  }

  void _onVideoTap() {
    if (_isLocked) {
      _showControlsWithAnimation();
      _resetHideTimer();
      return;
    }

    if (_showControls) {
      _hideControls();
    } else {
      _showControlsWithAnimation();
      _resetHideTimer();
    }
  }

  void _hideControls() {
    _controlsAnimController.reverse().then((_) {
      if (mounted) setState(() => _showControls = false);
    });
  }

  void _showControlsWithAnimation() {
    setState(() => _showControls = true);
    _controlsAnimController.forward();
  }

  void _resetHideTimer() {
    _hideControlsTimer?.cancel();
    if (_isPlaying) {
      _hideControlsTimer = Timer(const Duration(seconds: 4), () {
        if (mounted && _isPlaying) _hideControls();
      });
    }
  }

  // ===== BOTTOM SHEETS =====

  @override
  void dispose() {
    // Save progress before disposing
    _saveProgress();

    WidgetsBinding.instance.removeObserver(this);
    _controlsAnimController.dispose();
    _seekAnimController.dispose();
    _hideControlsTimer?.cancel();
    _doubleTapTimer?.cancel();
    _player.dispose();

    SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp]);
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return PopScope(
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        await _saveProgress();
        if (mounted) Navigator.pop(context);
      },
      child: AnnotatedRegion<SystemUiOverlayStyle>(
        value: SystemUiOverlayStyle.light,
        child: Scaffold(
          backgroundColor: Theme.of(context).scaffoldBackgroundColor,
          body: Stack(
            children: [
              _buildBody(),
              if (!_isFullscreen)
                Consumer<ThemeProvider>(
                  builder: (context, themeProvider, child) {
                    final isDark = themeProvider.isDarkMode;
                    return HeaderNavigationBar(
                      title:
                          _lesson?.title ??
                          widget.initialTitle ??
                          'Đang tải...',
                      isVisible: isScrollOverThreshold,
                      backgroundColor: isDark
                          ? _systemGray6
                          : Colors.white.withOpacity(0.8),
                      leading: HeaderNavigationBarBackButton(
                        color: _accentBlue,
                        label: 'Chi tiết',
                        onPressed: () async {
                          await _saveProgress();
                          if (mounted) Navigator.pop(context);
                        },
                      ),
                      trailing: IconButton(
                        icon: Icon(
                          isDark
                              ? CupertinoIcons.sun_max_fill
                              : CupertinoIcons.moon_fill,
                          color: isDark ? _accentOrange : _accentBlue,
                          size: 24,
                        ),
                        onPressed: () {
                          HapticFeedback.mediumImpact();
                          themeProvider.toggleTheme();
                        },
                      ),
                      titleStyle: Theme.of(context).textTheme.titleMedium
                          ?.copyWith(
                            fontWeight: FontWeight.w600,
                            color: isDark ? Colors.white : Colors.black,
                          ),
                    );
                  },
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_error != null) return _buildErrorState();
    if (_isLoading) return _buildLoadingState();

    // Trong chế độ Fullscreen (Landscape), hiển thị video tràn màn hình
    if (_isFullscreen) {
      return Stack(
        fit: StackFit.expand,
        children: [
          _buildVideoLayer(),
          if (_doubleTapSeekDirection != null) _buildSeekIndicator(),
          if (_showControls) _buildControlsLayer(),
          if (_isBuffering && !_isLoading) _buildBufferingIndicator(),
        ],
      );
    }

    // Trong chế độ Portrait, chia màn hình: Video ở trên, Ghi chú ở dưới
    final headerHeight = 44.0 + MediaQuery.of(context).padding.top;

    return Column(
      children: [
        SizedBox(height: headerHeight),
        Stack(
          children: [
            AspectRatio(aspectRatio: 16 / 9, child: _buildVideoLayer()),
            if (_doubleTapSeekDirection != null) _buildSeekIndicator(),
            if (_showControls) Positioned.fill(child: _buildControlsLayer()),
            if (_isBuffering && !_isLoading)
              Positioned.fill(child: _buildBufferingIndicator()),
          ],
        ),
        Expanded(child: _buildNotesSection()),
      ],
    );
  }

  Widget _buildVideoLayer() {
    return GestureDetector(
      onTap: _onVideoTap,
      onDoubleTapDown: (details) =>
          _handleDoubleTap(details, MediaQuery.of(context).size.width),
      onDoubleTap: () {}, // Required for onDoubleTapDown to work
      child: Container(
        color: _systemBlack,
        child: Center(
          child: Video(
            controller: _videoController,
            controls: NoVideoControls,
            fit: _videoFit,
          ),
        ),
      ),
    );
  }

  Widget _buildSeekIndicator() {
    final isLeft = _doubleTapSeekDirection == -1;

    return Positioned(
      left: isLeft ? 40 : null,
      right: isLeft ? null : 40,
      top: 0,
      bottom: 0,
      child: FadeTransition(
        opacity: _seekAnimation,
        child: Center(
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              color: _systemGray6.withOpacity(0.9),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  isLeft
                      ? CupertinoIcons.gobackward_10
                      : CupertinoIcons.goforward_10,
                  color: _white,
                  size: 36,
                ),
                const SizedBox(height: 8),
                Text(
                  isLeft ? '-10s' : '+10s',
                  style: const TextStyle(
                    color: _white,
                    fontSize: 15,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildControlsLayer() {
    return FadeTransition(
      opacity: _controlsFadeAnimation,
      child: GestureDetector(
        onTap: _onVideoTap,
        onDoubleTapDown: (details) =>
            _handleDoubleTap(details, MediaQuery.of(context).size.width),
        onDoubleTap: () {}, // Required for onDoubleTapDown to work
        behavior: HitTestBehavior.opaque,
        child: Container(
          decoration: BoxDecoration(
            color: Colors.black.withOpacity(0.15), // Subtle global dimming
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [
                Colors.black.withOpacity(0.5),
                Colors.transparent,
                Colors.transparent,
                Colors.black.withOpacity(0.6),
              ],
              stops: const [0.0, 0.25, 0.75, 1.0],
            ),
          ),
          child: _isLocked ? _buildLockedControls() : _buildFullControls(),
        ),
      ),
    );
  }

  Widget _buildLockedControls() {
    Widget content = Stack(
      children: [
        Positioned(
          top: 12,
          left: 16,
          child: _buildGlassButton(
            icon: CupertinoIcons.lock_fill,
            label: 'Đã khóa',
            onTap: _toggleLock,
            isActive: true,
          ),
        ),
      ],
    );

    if (_isFullscreen) {
      return SafeArea(child: content);
    }
    return content;
  }

  Widget _buildFullControls() {
    final safeArea = MediaQuery.of(context).padding;

    Widget content = Column(
      children: [
        _buildTopBar(),
        const Spacer(),
        _buildCenterControls(),
        const Spacer(),
        _buildBottomControls(safeArea),
      ],
    );

    if (_isFullscreen) {
      return SafeArea(child: content);
    }

    return Padding(padding: const EdgeInsets.only(top: 8.0), child: content);
  }

  Widget _buildTopBar() {
    if (!_isFullscreen) return const SizedBox(height: 48);

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          CupertinoButton(
            padding: EdgeInsets.zero,
            onPressed: _toggleFullscreen,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.black26,
                shape: BoxShape.circle,
              ),
              child: const Icon(
                CupertinoIcons.fullscreen_exit,
                color: _white,
                size: 22,
                shadows: [
                  Shadow(
                    color: Colors.black54,
                    blurRadius: 4,
                    offset: Offset(0, 2),
                  ),
                ],
              ),
            ),
          ),
          const Spacer(),
        ],
      ),
    );
  }

  Widget _buildCenterControls() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        _buildCircleButton(
          icon: CupertinoIcons.gobackward_15,
          size: 26,
          buttonSize: 52,
          onTap: () => _seekRelative(-15),
        ),
        const SizedBox(width: 40),
        _buildPlayButton(),
        const SizedBox(width: 40),
        _buildCircleButton(
          icon: CupertinoIcons.goforward_15,
          size: 26,
          buttonSize: 52,
          onTap: () => _seekRelative(15),
        ),
      ],
    );
  }

  Widget _buildPlayButton() {
    return GestureDetector(
      onTap: _togglePlayPause,
      child: Container(
        width: 64,
        height: 64,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: _white.withOpacity(0.15),
          border: Border.all(color: _white20, width: 1.5),
        ),
        child: Center(
          child: AnimatedSwitcher(
            duration: const Duration(milliseconds: 150),
            child: Icon(
              _isPlaying ? CupertinoIcons.pause_fill : CupertinoIcons.play_fill,
              key: ValueKey(_isPlaying),
              color: _white,
              size: 32,
              shadows: const [
                Shadow(
                  color: Colors.black54,
                  blurRadius: 4,
                  offset: Offset(0, 2),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCircleButton({
    required IconData icon,
    required double size,
    required double buttonSize,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: buttonSize,
        height: buttonSize,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.black.withOpacity(0.3),
        ),
        child: Icon(
          icon,
          color: _white,
          size: size,
          shadows: const [
            Shadow(color: Colors.black54, blurRadius: 4, offset: Offset(0, 2)),
          ],
        ),
      ),
    );
  }

  Widget _buildBottomControls(EdgeInsets safeArea) {
    final position = _isSeeking ? _seekPosition : _position;
    final duration = _duration.inMilliseconds > 0
        ? _duration
        : Duration(seconds: _lesson?.duration ?? 0);

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 0, 8, 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Expanded(child: _buildProgressBar(position, duration)),
          CupertinoButton(
            padding: const EdgeInsets.all(8),
            onPressed: _toggleFullscreen,
            child: Icon(
              _isFullscreen
                  ? CupertinoIcons.fullscreen_exit
                  : CupertinoIcons.fullscreen,
              color: _white,
              size: 22,
              shadows: const [
                Shadow(
                  color: Colors.black54,
                  blurRadius: 4,
                  offset: Offset(0, 1),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildProgressBar(Duration position, Duration duration) {
    final maxMs = duration.inMilliseconds.toDouble();

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              _formatDuration(position),
              style: const TextStyle(
                color: _white,
                fontSize: 11,
                fontWeight: FontWeight.w600,
                fontFeatures: [FontFeature.tabularFigures()],
                shadows: [
                  Shadow(
                    color: Colors.black87,
                    blurRadius: 2,
                    offset: Offset(0, 1),
                  ),
                ],
              ),
            ),
            Text(
              _formatDuration(duration),
              style: const TextStyle(
                color: _white,
                fontSize: 11,
                fontWeight: FontWeight.w500,
                fontFeatures: [FontFeature.tabularFigures()],
                shadows: [
                  Shadow(
                    color: Colors.black87,
                    blurRadius: 2,
                    offset: Offset(0, 1),
                  ),
                ],
              ),
            ),
          ],
        ),
        SliderTheme(
          data: SliderThemeData(
            trackHeight: 3,
            activeTrackColor: _accentBlue,
            inactiveTrackColor: _white20,
            thumbColor: _white,
            thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 6),
            overlayColor: _accentBlue.withOpacity(0.2),
            overlayShape: const RoundSliderOverlayShape(overlayRadius: 12),
          ),
          child: Slider(
            value: (_isSeeking ? _seekPosition : position).inMilliseconds
                .toDouble()
                .clamp(0, maxMs),
            min: 0,
            max: maxMs > 0 ? maxMs : 1,
            onChangeStart: (v) => setState(() {
              _isSeeking = true;
              _seekPosition = Duration(milliseconds: v.toInt());
            }),
            onChanged: (v) => setState(() {
              _seekPosition = Duration(milliseconds: v.toInt());
            }),
            onChangeEnd: (v) {
              _player.seek(Duration(milliseconds: v.toInt()));
              setState(() => _isSeeking = false);
              _resetHideTimer();
            },
          ),
        ),
      ],
    );
  }

  Widget _buildGlassButton({
    required IconData icon,
    String? label,
    required VoidCallback onTap,
    bool isActive = false,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: ClipRRect(
        borderRadius: BorderRadius.circular(12),
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
          child: Container(
            padding: EdgeInsets.symmetric(
              horizontal: label != null ? 14 : 12,
              vertical: 10,
            ),
            decoration: BoxDecoration(
              color: isActive
                  ? _accentBlue.withOpacity(0.3)
                  : _systemGray5.withOpacity(0.6),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: isActive ? _accentBlue.withOpacity(0.5) : _white10,
                width: 1,
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(icon, color: isActive ? _accentBlue : _white, size: 18),
                if (label != null) ...[
                  const SizedBox(width: 6),
                  Text(
                    label,
                    style: TextStyle(
                      color: isActive ? _accentBlue : _white,
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ===== STATES =====

  Widget _buildLoadingState() {
    return Container(
      color: _systemBlack,
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: _systemGray6,
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Center(
                child: CupertinoActivityIndicator(color: _white, radius: 16),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Đang tải video...',
              style: TextStyle(
                color: _white60,
                fontSize: 16,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 48),
              child: Text(
                _lesson?.title ?? widget.initialTitle ?? '',
                style: const TextStyle(color: _white40, fontSize: 14),
                textAlign: TextAlign.center,
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBufferingIndicator() {
    return Center(
      child: Container(
        width: 64,
        height: 64,
        decoration: BoxDecoration(
          color: _systemGray6.withOpacity(0.9),
          borderRadius: BorderRadius.circular(16),
        ),
        child: const Center(
          child: CupertinoActivityIndicator(color: _white, radius: 14),
        ),
      ),
    );
  }

  Widget _buildErrorState() {
    return Container(
      width: double.infinity,
      color: _systemBlack,
      padding: const EdgeInsets.all(40),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: _systemGray6,
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Icon(
              CupertinoIcons.exclamationmark_triangle_fill,
              color: _accentOrange,
              size: 36,
            ),
          ),
          const SizedBox(height: 24),
          const Text(
            'Không thể phát video',
            style: TextStyle(
              color: _white,
              fontSize: 20,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Tải video thất bại, vui lòng thử lại sau.',
            textAlign: TextAlign.center,
            style: const TextStyle(color: _white60, fontSize: 15, height: 1.4),
          ),
          const SizedBox(height: 32),
          CupertinoButton(
            color: _accentBlue,
            borderRadius: BorderRadius.circular(14),
            onPressed: () {
              HapticFeedback.mediumImpact();
              _initializePlayer();
            },
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(CupertinoIcons.arrow_clockwise, size: 18, color: _white),
                SizedBox(width: 8),
                Text(
                  'Thử lại',
                  style: TextStyle(fontWeight: FontWeight.w600, color: _white),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          CupertinoButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Quay lại'),
          ),
        ],
      ),
    );
  }

  String _formatDuration(Duration d) {
    String twoDigits(int n) => n.toString().padLeft(2, '0');
    final h = d.inHours;
    final m = d.inMinutes.remainder(60);
    final s = d.inSeconds.remainder(60);
    return h > 0
        ? '$h:${twoDigits(m)}:${twoDigits(s)}'
        : '${twoDigits(m)}:${twoDigits(s)}';
  }

  // ===== NOTES UI =====

  Widget _buildNotesSection() {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      color: isDark ? _systemGray6 : const Color(0xFFF2F2F7),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildNotesHeader(),
          Divider(height: 1, color: isDark ? _white10 : Colors.black12),
          Expanded(
            child: _isLoadingNotes
                ? const Center(
                    child: CupertinoActivityIndicator(color: _accentBlue),
                  )
                : _notes.isEmpty
                ? _buildEmptyNotes()
                : _buildNotesList(),
          ),
        ],
      ),
    );
  }

  Widget _buildNotesHeader() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              const Icon(
                CupertinoIcons.doc_text_fill,
                color: _accentBlue,
                size: 20,
              ),
              const SizedBox(width: 8),
              Text(
                '${_notes.length} Ghi chú',
                style: TextStyle(
                  color: Theme.of(context).brightness == Brightness.dark
                      ? _white
                      : Colors.black,
                  fontSize: 17,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          CupertinoButton(
            padding: EdgeInsets.zero,
            onPressed: () {
              _player.pause();
              _showAddNoteModal();
            },
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: _accentBlue.withOpacity(0.15),
                borderRadius: BorderRadius.circular(100),
              ),
              child: const Row(
                children: [
                  Icon(CupertinoIcons.plus, color: _accentBlue, size: 14),
                  SizedBox(width: 4),
                  Text(
                    'Thêm',
                    style: TextStyle(
                      color: _accentBlue,
                      fontSize: 13,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyNotes() {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            CupertinoIcons.doc_plaintext,
            size: 48,
            color: isDark ? _white.withOpacity(0.1) : Colors.black12,
          ),
          const SizedBox(height: 16),
          Text(
            'Chưa có ghi chú nào',
            style: TextStyle(
              color: isDark ? _white.withOpacity(0.3) : Colors.black38,
              fontSize: 15,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildNotesList() {
    return ListView.separated(
      controller: scrollTrackingController,
      padding: const EdgeInsets.all(16),
      itemCount: _notes.length,
      separatorBuilder: (context, index) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        return _buildNoteItem(_notes[index]);
      },
    );
  }

  Widget _buildNoteItem(LessonNote note) {
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        _player.seek(Duration(seconds: note.durationAt));
        if (!_isPlaying) _player.play();
      },
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Theme.of(context).brightness == Brightness.dark
              ? _systemGray5
              : Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: Theme.of(context).brightness == Brightness.dark
                ? _white10
                : Colors.black.withOpacity(0.05),
          ),
          boxShadow: Theme.of(context).brightness == Brightness.light
              ? [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 10,
                    offset: const Offset(0, 4),
                  ),
                ]
              : null,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 8,
                    vertical: 2,
                  ),
                  decoration: BoxDecoration(
                    color: _accentBlue,
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Text(
                    _formatDuration(Duration(seconds: note.durationAt)),
                    style: const TextStyle(
                      color: _white,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                const Spacer(),
                Text(
                  _formatDate(note.createdAt),
                  style: TextStyle(
                    color: Theme.of(context).brightness == Brightness.dark
                        ? _white.withOpacity(0.4)
                        : Colors.black38,
                    fontSize: 11,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              note.content,
              style: TextStyle(
                color: Theme.of(context).brightness == Brightness.dark
                    ? _white
                    : Colors.black87,
                fontSize: 14,
                height: 1.4,
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return '${date.day}/${date.month}/${date.year}';
    } catch (e) {
      return dateStr;
    }
  }

  void _showAddNoteModal() {
    _player.pause();
    final textController = TextEditingController();
    final currentPosition = _position.inSeconds;

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      barrierColor: Colors.black54,
      builder: (context) => Padding(
        padding: EdgeInsets.only(
          bottom: MediaQuery.of(context).viewInsets.bottom,
        ),
        child: Container(
          decoration: const BoxDecoration(
            color: _systemGray6,
            borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
          ),
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Text(
                    'Thêm ghi chú',
                    style: TextStyle(
                      color: _white,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: _accentBlue.withOpacity(0.1),
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: Text(
                      _formatDuration(Duration(seconds: currentPosition)),
                      style: const TextStyle(
                        color: _accentBlue,
                        fontWeight: FontWeight.bold,
                        fontSize: 13,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
              CupertinoTextField(
                controller: textController,
                placeholder: 'Nhập nội dung ghi chú...',
                placeholderStyle: TextStyle(color: _white.withOpacity(0.3)),
                maxLines: 4,
                autofocus: true,
                style: const TextStyle(color: _white, fontSize: 16),
                decoration: BoxDecoration(
                  color: _systemGray5,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _white10),
                ),
                padding: const EdgeInsets.all(12),
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: CupertinoButton(
                      padding: EdgeInsets.zero,
                      onPressed: () {
                        Navigator.pop(context);
                        _player.play();
                      },
                      child: Container(
                        height: 48,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(
                          color: _white.withOpacity(0.05),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Text(
                          'Hủy',
                          style: TextStyle(color: _white60),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: CupertinoButton(
                      padding: EdgeInsets.zero,
                      onPressed: () {
                        if (textController.text.trim().isNotEmpty) {
                          _saveNote(textController.text, currentPosition);
                          Navigator.pop(context);
                        }
                      },
                      child: Container(
                        height: 48,
                        alignment: Alignment.center,
                        decoration: BoxDecoration(
                          color: _accentBlue,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: _accentBlue.withOpacity(0.3),
                              blurRadius: 8,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: const Text(
                          'Lưu',
                          style: TextStyle(
                            color: _white,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
            ],
          ),
        ),
      ),
    ).then((_) {
      // Logic for when the modal is dismissed by tapping outside
      // But we already have _player.play() in Cancel and Save success.
      // If they tap outside, should we resume?
      // User said: "khi lưu ghi chú thành công thì phát video"
      // Usually users expect play when they dismiss.
    });
  }

  Future<void> _saveNote(String content, int durationAt) async {
    try {
      final dio = Dio()
        ..options.baseUrl = ApiConstants.baseUrl
        ..options.headers = {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_authToken',
        };

      final response = await dio.post(
        '/api/student/notes',
        data: {
          'lesson_id': widget.lessonId,
          'duration_at': durationAt,
          'content': content,
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        _fetchNotes();
        _player.play();
      }
    } catch (e) {
      debugPrint('Error saving note: $e');
    }
  }
}

extension DurationClamp on Duration {
  Duration clamp(Duration min, Duration max) {
    if (this < min) return min;
    if (this > max) return max;
    return this;
  }
}
