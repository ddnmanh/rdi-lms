import 'dart:async';
import 'dart:ui';
import 'package:dio/dio.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:media_kit/media_kit.dart';
import 'package:media_kit_video/media_kit_video.dart';
import '../../../core/constants/api_constants.dart';
import '../../../data/models/hls_signature_model.dart';
import '../../../data/models/lesson_model.dart';

class VideoPlayerScreen extends StatefulWidget {
  final Lesson lesson;

  const VideoPlayerScreen({super.key, required this.lesson});

  @override
  State<VideoPlayerScreen> createState() => _VideoPlayerScreenState();
}

class _VideoPlayerScreenState extends State<VideoPlayerScreen>
    with WidgetsBindingObserver, TickerProviderStateMixin {
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

  // New features state
  bool _isFullscreen = false;
  bool _isLocked = false;
  double _playbackSpeed = 1.0;
  BoxFit _videoFit = BoxFit.contain;
  int? _doubleTapSeekDirection; // -1 left, 1 right, null none
  Timer? _hideControlsTimer;
  Timer? _doubleTapTimer;

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
  static const Color _systemGray1 = Color(0xFF8E8E93);
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
      if (mounted && !_isSeeking) setState(() => _position = position);
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

      final hlsSignature = await _fetchHlsSignature();

      if (hlsSignature == null) {
        throw Exception('Không thể lấy được URL video HLS');
      }

      final streamUrl = hlsSignature.fullStreamUrl;
      debugPrint('HLS Stream URL: $streamUrl');

      await _player.open(Media(streamUrl), play: false);

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

  Future<HlsSignature?> _fetchHlsSignature() async {
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
      ApiConstants.getHlsSignatureUrl(widget.lesson.id),
    );

    if (response.statusCode == 200 && response.data['success'] == true) {
      return HlsSignature.fromJson(response.data['data']);
    }
    throw Exception(response.data['message'] ?? 'Lỗi không xác định');
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
        : Duration(seconds: widget.lesson.duration);
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
    if (_isPlaying && !_isLocked) {
      _hideControlsTimer = Timer(const Duration(seconds: 4), () {
        if (mounted && _isPlaying) _hideControls();
      });
    }
  }

  // ===== BOTTOM SHEETS =====

  void _showSettingsSheet() {
    HapticFeedback.mediumImpact();
    showCupertinoModalPopup(
      context: context,
      builder: (context) => _buildSettingsSheet(),
    );
  }

  void _showSpeedSheet() {
    HapticFeedback.mediumImpact();
    showCupertinoModalPopup(
      context: context,
      builder: (context) => _buildSpeedSheet(),
    );
  }

  void _showFitSheet() {
    HapticFeedback.mediumImpact();
    showCupertinoModalPopup(
      context: context,
      builder: (context) => _buildFitSheet(),
    );
  }

  @override
  void dispose() {
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
    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.light,
      child: Scaffold(
        backgroundColor: _systemBlack,
        body: _buildBody(),
      ),
    );
  }

  Widget _buildBody() {
    if (_error != null) return _buildErrorState();
    if (_isLoading) return _buildLoadingState();

    return Stack(
      fit: StackFit.expand,
      children: [
        // Video layer
        _buildVideoLayer(),
        // Double tap seek indicator
        if (_doubleTapSeekDirection != null) _buildSeekIndicator(),
        // Controls layer
        if (_showControls) _buildControlsLayer(),
        // Buffering overlay
        if (_isBuffering && !_isLoading) _buildBufferingIndicator(),
      ],
    );
  }

  Widget _buildVideoLayer() {
    return GestureDetector(
      onTap: _onVideoTap,
      onDoubleTapDown: (details) => _handleDoubleTap(
        details,
        MediaQuery.of(context).size.width,
      ),
      onDoubleTap: () {}, // Required for onDoubleTapDown to work
      child: Container(
        color: _systemBlack,
        child: Center(
          child: AspectRatio(
            aspectRatio: 16 / 9,
            child: Video(
              controller: _videoController,
              controls: NoVideoControls,
              fit: _videoFit,
            ),
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
      child: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              _systemBlack.withOpacity(0.7),
              Colors.transparent,
              Colors.transparent,
              _systemBlack.withOpacity(0.85),
            ],
            stops: const [0.0, 0.2, 0.6, 1.0],
          ),
        ),
        child: _isLocked ? _buildLockedControls() : _buildFullControls(),
      ),
    );
  }

  Widget _buildLockedControls() {
    return SafeArea(
      child: Stack(
        children: [
          // Lock button only
          Positioned(
            top: 8,
            left: 16,
            child: _buildGlassButton(
              icon: CupertinoIcons.lock_fill,
              label: 'Đã khóa',
              onTap: _toggleLock,
              isActive: true,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFullControls() {
    final safeArea = MediaQuery.of(context).padding;

    return SafeArea(
      child: Column(
        children: [
          // Top bar
          _buildTopBar(),
          const Spacer(),
          // Center controls
          _buildCenterControls(),
          const Spacer(),
          // Bottom controls
          _buildBottomControls(safeArea),
        ],
      ),
    );
  }

  Widget _buildTopBar() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      child: Row(
        children: [
          // Back button
          CupertinoButton(
            padding: const EdgeInsets.all(12),
            onPressed: () {
              HapticFeedback.lightImpact();
              Navigator.pop(context);
            },
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: _systemGray6.withOpacity(0.8),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                CupertinoIcons.xmark,
                color: _white,
                size: 18,
              ),
            ),
          ),
          const Spacer(),
          // Title (only in portrait)
          if (!_isFullscreen)
            Expanded(
              flex: 3,
              child: Text(
                widget.lesson.title,
                textAlign: TextAlign.center,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: _white,
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  letterSpacing: -0.4,
                ),
              ),
            ),
          const Spacer(),
          // Settings button
          CupertinoButton(
            padding: const EdgeInsets.all(12),
            onPressed: _showSettingsSheet,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: _systemGray6.withOpacity(0.8),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                CupertinoIcons.ellipsis,
                color: _white,
                size: 18,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCenterControls() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        // Rewind
        _buildCircleButton(
          icon: CupertinoIcons.gobackward_15,
          size: 28,
          buttonSize: 56,
          onTap: () => _seekRelative(-15),
        ),
        const SizedBox(width: 48),
        // Play/Pause
        _buildPlayButton(),
        const SizedBox(width: 48),
        // Forward
        _buildCircleButton(
          icon: CupertinoIcons.goforward_15,
          size: 28,
          buttonSize: 56,
          onTap: () => _seekRelative(15),
        ),
      ],
    );
  }

  Widget _buildPlayButton() {
    return GestureDetector(
      onTap: _togglePlayPause,
      child: Container(
        width: 80,
        height: 80,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: _white.withOpacity(0.2),
          border: Border.all(color: _white40, width: 2),
        ),
        child: Center(
          child: AnimatedSwitcher(
            duration: const Duration(milliseconds: 150),
            child: Icon(
              _isPlaying ? CupertinoIcons.pause_fill : CupertinoIcons.play_fill,
              key: ValueKey(_isPlaying),
              color: _white,
              size: 36,
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
          color: _white10,
        ),
        child: Icon(icon, color: _white, size: size),
      ),
    );
  }

  Widget _buildBottomControls(EdgeInsets safeArea) {
    final position = _isSeeking ? _seekPosition : _position;
    final duration = _duration.inMilliseconds > 0
        ? _duration
        : Duration(seconds: widget.lesson.duration);

    return Padding(
      padding: EdgeInsets.fromLTRB(16, 0, 16, safeArea.bottom > 0 ? 8 : 16),
      child: Column(
        children: [
          // Progress bar
          _buildProgressBar(position, duration),
          const SizedBox(height: 16),
          // Bottom action row
          _buildBottomActionRow(),
        ],
      ),
    );
  }

  Widget _buildProgressBar(Duration position, Duration duration) {
    final maxMs = duration.inMilliseconds.toDouble();

    return Column(
      children: [
        // Slider
        SliderTheme(
          data: SliderThemeData(
            trackHeight: 4,
            activeTrackColor: _white,
            inactiveTrackColor: _white20,
            thumbColor: _white,
            thumbShape: const RoundSliderThumbShape(enabledThumbRadius: 7),
            overlayColor: _white20,
            overlayShape: const RoundSliderOverlayShape(overlayRadius: 16),
          ),
          child: Slider(
            value: (_isSeeking ? _seekPosition : position)
                .inMilliseconds
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
        const SizedBox(height: 4),
        // Time labels
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                _formatDuration(position),
                style: const TextStyle(
                  color: _white60,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  fontFeatures: [FontFeature.tabularFigures()],
                ),
              ),
              Text(
                '-${_formatDuration(duration - position)}',
                style: const TextStyle(
                  color: _white60,
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  fontFeatures: [FontFeature.tabularFigures()],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildBottomActionRow() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        // Left actions
        Row(
          children: [
            _buildGlassButton(
              icon: _isLocked ? CupertinoIcons.lock_fill : CupertinoIcons.lock_open,
              onTap: _toggleLock,
              isActive: _isLocked,
            ),
            const SizedBox(width: 12),
            _buildGlassButton(
              icon: CupertinoIcons.speedometer,
              label: '${_playbackSpeed}x',
              onTap: _showSpeedSheet,
              isActive: _playbackSpeed != 1.0,
            ),
          ],
        ),
        // Right actions
        Row(
          children: [
            _buildGlassButton(
              icon: CupertinoIcons.rectangle_expand_vertical,
              onTap: _showFitSheet,
            ),
            const SizedBox(width: 12),
            _buildGlassButton(
              icon: _isFullscreen
                  ? CupertinoIcons.fullscreen_exit
                  : CupertinoIcons.fullscreen,
              onTap: _toggleFullscreen,
              isActive: _isFullscreen,
            ),
          ],
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
                Icon(
                  icon,
                  color: isActive ? _accentBlue : _white,
                  size: 18,
                ),
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

  // ===== BOTTOM SHEETS =====

  Widget _buildSettingsSheet() {
    return CupertinoActionSheet(
      actions: [
        CupertinoActionSheetAction(
          onPressed: () {
            Navigator.pop(context);
            _showSpeedSheet();
          },
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(CupertinoIcons.speedometer, size: 22),
              const SizedBox(width: 12),
              const Text('Tốc độ phát'),
              const Spacer(),
              Text(
                '${_playbackSpeed}x',
                style: TextStyle(color: _systemGray1),
              ),
              const SizedBox(width: 8),
              Icon(CupertinoIcons.chevron_right, size: 16, color: _systemGray1),
            ],
          ),
        ),
        CupertinoActionSheetAction(
          onPressed: () {
            Navigator.pop(context);
            _showFitSheet();
          },
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(CupertinoIcons.rectangle_expand_vertical, size: 22),
              const SizedBox(width: 12),
              const Text('Tỷ lệ màn hình'),
              const Spacer(),
              Text(
                _fitOptions[_videoFit]!,
                style: const TextStyle(color: _systemGray1),
              ),
              const SizedBox(width: 8),
              const Icon(CupertinoIcons.chevron_right, size: 16, color: _systemGray1),
            ],
          ),
        ),
        CupertinoActionSheetAction(
          onPressed: () {
            Navigator.pop(context);
            _toggleLock();
          },
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                _isLocked ? CupertinoIcons.lock_fill : CupertinoIcons.lock_open,
                size: 22,
              ),
              const SizedBox(width: 12),
              Text(_isLocked ? 'Mở khóa màn hình' : 'Khóa màn hình'),
            ],
          ),
        ),
      ],
      cancelButton: CupertinoActionSheetAction(
        isDefaultAction: true,
        onPressed: () => Navigator.pop(context),
        child: const Text('Hủy'),
      ),
    );
  }

  Widget _buildSpeedSheet() {
    return CupertinoActionSheet(
      title: const Text('Tốc độ phát'),
      message: const Text('Chọn tốc độ phát video'),
      actions: _speedOptions.map((speed) {
        final isSelected = _playbackSpeed == speed;
        return CupertinoActionSheetAction(
          onPressed: () => _setPlaybackSpeed(speed),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (isSelected)
                const Icon(CupertinoIcons.checkmark_alt, size: 20)
              else
                const SizedBox(width: 20),
              const SizedBox(width: 12),
              Text(
                speed == 1.0 ? 'Bình thường' : '${speed}x',
                style: TextStyle(
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                ),
              ),
            ],
          ),
        );
      }).toList(),
      cancelButton: CupertinoActionSheetAction(
        isDefaultAction: true,
        onPressed: () => Navigator.pop(context),
        child: const Text('Hủy'),
      ),
    );
  }

  Widget _buildFitSheet() {
    return CupertinoActionSheet(
      title: const Text('Tỷ lệ màn hình'),
      message: const Text('Chọn cách hiển thị video'),
      actions: _fitOptions.entries.map((entry) {
        final isSelected = _videoFit == entry.key;
        return CupertinoActionSheetAction(
          onPressed: () => _setVideoFit(entry.key),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (isSelected)
                const Icon(CupertinoIcons.checkmark_alt, size: 20)
              else
                const SizedBox(width: 20),
              const SizedBox(width: 12),
              Text(
                entry.value,
                style: TextStyle(
                  fontWeight: isSelected ? FontWeight.w600 : FontWeight.normal,
                ),
              ),
            ],
          ),
        );
      }).toList(),
      cancelButton: CupertinoActionSheetAction(
        isDefaultAction: true,
        onPressed: () => Navigator.pop(context),
        child: const Text('Hủy'),
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
                child: CupertinoActivityIndicator(
                  color: _white,
                  radius: 16,
                ),
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
                widget.lesson.title,
                style: const TextStyle(
                  color: _white40,
                  fontSize: 14,
                ),
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
            style: const TextStyle(
              color: _white60,
              fontSize: 15,
              height: 1.4,
            ),
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
                  style: TextStyle(
                    fontWeight: FontWeight.w600,
                    color: _white,
                  ),
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
    return h > 0 ? '$h:${twoDigits(m)}:${twoDigits(s)}' : '${twoDigits(m)}:${twoDigits(s)}';
  }
}

extension DurationClamp on Duration {
  Duration clamp(Duration min, Duration max) {
    if (this < min) return min;
    if (this > max) return max;
    return this;
  }
}

