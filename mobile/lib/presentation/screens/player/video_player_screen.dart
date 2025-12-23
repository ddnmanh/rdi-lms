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

import 'widgets/video_player_view.dart';
import 'widgets/video_player_controls.dart';
import 'widgets/video_notes_section.dart';
import 'widgets/player_overlays.dart';
import 'widgets/quiz_overlay.dart';
import '../../../data/models/quiz_model.dart';
import '../../../data/repositories/quiz_repository.dart';
import '../../../data/services/api_service.dart';

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

  // Quiz state
  List<Quiz> _quizzes = [];
  QuizStatus? _quizStatus;
  Quiz? _activeQuiz;
  late final QuizRepository _quizRepository;
  bool _isSubmittingQuiz = false;
  String? _quizError;
  bool? _isQuizPassed;
  final Set<int> _passedQuizIds = {};
  final Map<int, Map<int, List<int>>> _quizDrafts =
      {}; // QuizID -> {QuestionID -> Options}

  // Animation controllers
  late AnimationController _controlsAnimController;
  late Animation<double> _controlsFadeAnimation;
  late AnimationController _seekAnimController;
  late Animation<double> _seekAnimation;

  // // Playback speed options and Video fit options removed as they handle in modals if needed,
  // // currently functionality handles minimal controls.
  // // If needed, can restore.

  // iOS 18 Design System Colors
  static const Color _accentBlue = Color(0xFF0A84FF);
  static const Color _accentOrange = Color(0xFFFF9F0A);
  static const Color _systemGray6 = Color(0xFF1C1C1E);
  // static const Color _white = Color(0xFFFFFFFF);

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

    // Initialize repository
    _quizRepository = QuizRepository(
      ApiService(Dio(), const FlutterSecureStorage()),
    );
    // In strict architecture, use Provider/GetIt. Here I instantiate locally as per existing pattern or reuse _apiService logic if possible.
    // However, _initializePlayer creates a local Dio. I should probably create a stored one or use a singleton if available.
    // Existing code uses `_apiService` in repository but here `fetchLessonDetail` uses new Dio().
    // I will use a helper to get dio with token later, or just init here.

    _setupOrientations();
    _setupListeners();
    _initializePlayer();
    _fetchNotes();
    _fetchQuizzes();
  }

  void _setupOrientations() {
    SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.landscapeLeft,
      DeviceOrientation.landscapeRight,
    ]);
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  }

  final List<StreamSubscription> _subscriptions = [];

  // ... (previous code)

  @override
  void dispose() {
    _saveProgress();

    for (final s in _subscriptions) {
      s.cancel();
    }

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

  void _setupListeners() {
    _subscriptions.add(
      _player.stream.playing.listen((playing) {
        if (mounted) setState(() => _isPlaying = playing);
      }),
    );

    _subscriptions.add(
      _player.stream.position.listen((position) {
        if (mounted && !_isSeeking) {
          setState(() {
            _position = position;
            // Track maximum watched position
            if (position > _maxWatchedPosition) {
              _maxWatchedPosition = position;
            }
          });
          _checkQuizTrigger(_position);
        }
      }),
    );

    _subscriptions.add(
      _player.stream.duration.listen((duration) {
        if (mounted) setState(() => _duration = duration);
      }),
    );

    _subscriptions.add(
      _player.stream.buffering.listen((buffering) {
        if (mounted) setState(() => _isBuffering = buffering);
      }),
    );

    _subscriptions.add(
      _player.stream.error.listen((error) {
        if (mounted && error.isNotEmpty) {
          setState(() {
            _error = error;
            _isLoading = false;
          });
        }
      }),
    );
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

  // ===== QUIZ LOGIC =====

  Future<void> _fetchQuizzes() async {
    try {
      final quizzes = await _quizRepository.getLessonQuizzes(widget.lessonId);
      final status = await _quizRepository.getQuizStatus(widget.lessonId);

      if (mounted) {
        setState(() {
          _quizzes = quizzes;
          _quizStatus = status;
          if (status != null) {
            for (var q in status.quizzes) {
              if (q.passed) _passedQuizIds.add(q.quizId);
            }
          }
        });
        _checkQuizTrigger(_position);
      }
    } catch (e) {
      debugPrint('Error fetching quizzes: $e');
    }
  }

  void _checkQuizTrigger(Duration position) {
    if (_activeQuiz != null) return; // Already showing a quiz
    if (_isLoading) return;

    final currentSeconds = position.inSeconds;

    for (var quiz in _quizzes) {
      // Trigger if we are at or past the start time
      // AND we haven't passed it yet
      // AND it's required (or we just want to show all quizzes?)
      // Requirement: "appear at specific timestamps... must pass ... to continue"
      // So checks:
      // 1. Time logic: strict equality might miss if we skip frames, so use range or "crossed boundary" logic?
      //    "Crossed boundary" is hard without storing last checks.
      //    Simple approach: `currentSeconds == quiz.startAtSeconds`.
      //    But players might jump from 299 to 301.
      //    Better: `currentSeconds >= quiz.startAtSeconds` AND `!_passedQuizIds.contains(quiz.id)`.
      //    But if I rewind, it triggers again? "already passed" check handles that.
      //    But if I haven't passed, and I'm at 400s (quiz at 300s), should it trigger? Yes, I shouldn't have been able to get there.
      //    Wait, if I seek past it?
      //    "Block seeking past" is in requirements "must pass ... to continue viewing content BEHIND that exercise".
      //    So if I seek to 500s and quiz is at 300s and not passed, I should be blocked.

      if (_passedQuizIds.contains(quiz.id)) continue;

      if (currentSeconds >= quiz.startAtSeconds) {
        // Trigger!
        _player.pause();

        // Seek back to exactly the start time if we overshot significantly?
        // Maybe just pause is enough. But if they seeked way past, maybe move them back?
        // Let's just prompt quiz.
        setState(() {
          _activeQuiz = quiz;
        });
        break; // Only trigger one at a time
      }
    }
  }

  Future<void> _submitQuiz(
    int quizId,
    List<Map<String, dynamic>> answers,
  ) async {
    setState(() {
      _isSubmittingQuiz = true;
      _quizError = null;
    });

    try {
      final success = await _quizRepository.submitQuiz(quizId, answers);
      if (success) {
        // Re-fetch status to get score/pass result?
        // Or assume success means passed?
        // The API returns void/bool in my repo, but real backend might return score.
        // Step 1: fetch status again to confirm pass/result detail.
        final status = await _quizRepository.getQuizStatus(widget.lessonId);

        bool passed = false;
        if (status != null) {
          final quizResult = status.quizzes.firstWhere(
            (q) => q.quizId == quizId,
            orElse: () => QuizResultStatus(
              quizId: quizId,
              title: '',
              startAtSeconds: 0,
              passingPercentScore: 0,
              passed: false,
            ),
          );
          passed = quizResult.passed;
        }

        if (mounted) {
          setState(() {
            _isSubmittingQuiz = false;
            _isQuizPassed = passed;
            if (passed) {
              _passedQuizIds.add(quizId);
              _quizStatus = status;
            }
            _quizDrafts.remove(
              quizId,
            ); // Clear draft on ANY submission (pass or fail)
          });
        }
      } else {
        setState(() {
          _isSubmittingQuiz = false;
          _quizError = 'Có lỗi khi nộp bài. Vui lòng thử lại.';
        });
      }
    } catch (e) {
      setState(() {
        _isSubmittingQuiz = false;
        _quizError = 'Lỗi kết nối: ${e.toString()}';
      });
    }
  }

  void _handleRetry() {
    setState(() {
      _isQuizPassed = null;
      _quizError = null;
    });
  }

  Future<void> _handleQuizClose(Map<int, List<int>> currentAnswers) async {
    final quiz = _activeQuiz;
    final wasRequired = quiz?.isRequired ?? false;
    final quizId = quiz?.id;
    final startAt = quiz?.startAtSeconds;

    // Only save draft if we haven't submitted (isPassed is null)
    // If _isQuizPassed != null, we are in result view, so don't overwrite draft.
    if (quizId != null && _isQuizPassed == null) {
      _quizDrafts[quizId] = currentAnswers;
    }

    setState(() {
      _activeQuiz = null;
      _isQuizPassed = null;
      _quizError = null;
    });

    if (!_isPlaying && (_lesson?.progress.isCompleted == false)) {
      bool passed = quizId != null && _passedQuizIds.contains(quizId);
      if (!wasRequired || passed) {
        await _player.play();
      } else {
        // "Xem lại kiến thức": Seek back 10 seconds from QUIZ START to allow review
        final anchor = startAt ?? _position.inSeconds;
        final backSeconds = anchor - 10;
        final backPosition = Duration(
          seconds: backSeconds < 0 ? 0 : backSeconds,
        );

        await _player.seek(backPosition);
        await _player.play();
      }
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
    if (newPosition < Duration.zero) {
      newPosition = Duration.zero;
    } else if (newPosition > duration) {
      newPosition = duration;
    }
    newPosition = _getSafeSeekPosition(newPosition);
    _player.seek(newPosition);
  }

  Duration _getSafeSeekPosition(Duration target) {
    // Filter only unpassed & required quizzes
    final blockingQuizzes = _quizzes
        .where((q) => q.isRequired && !_passedQuizIds.contains(q.id))
        .toList();

    if (blockingQuizzes.isEmpty) return target;

    // Sort by time
    blockingQuizzes.sort(
      (a, b) => a.startAtSeconds.compareTo(b.startAtSeconds),
    );

    for (var quiz in blockingQuizzes) {
      // If target is past the quiz, restrict to the quiz start
      if (target.inSeconds >= quiz.startAtSeconds) {
        return Duration(seconds: quiz.startAtSeconds);
      }
    }
    return target;
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

  // ===== SEEK BAR ACTIONS =====
  void _onSeekStart(bool seeking, Duration position) {
    setState(() {
      _isSeeking = seeking;
      _seekPosition = position;
    });
  }

  void _onSeekUpdate(Duration position) {
    setState(() {
      _seekPosition = position;
    });
  }

  void _onSeekEnd(Duration position) {
    final safePos = _getSafeSeekPosition(position);
    _player.seek(safePos);
    setState(() => _isSeeking = false);
    _resetHideTimer();
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
                        label: 'Quay lại',
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

              if (_activeQuiz != null)
                Positioned.fill(
                  child: QuizOverlay(
                    quiz: _activeQuiz!,
                    isSubmitting: _isSubmittingQuiz,
                    error: _quizError,
                    isPassed: _isQuizPassed,
                    initialAnswers: _quizDrafts[_activeQuiz!.id],
                    onClose: _handleQuizClose,
                    onRetry: _handleRetry,
                    onSubmit: (answers) =>
                        _submitQuiz(_activeQuiz!.id, answers),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_error != null) {
      return VideoErrorState(
        onRetry: () {
          HapticFeedback.mediumImpact();
          _initializePlayer();
        },
        onBack: () => Navigator.pop(context),
      );
    }
    if (_isLoading) {
      return VideoLoadingState(
        title: _lesson?.title ?? widget.initialTitle ?? '',
      );
    }

    // Trong chế độ Fullscreen (Landscape)
    if (_isFullscreen) {
      return Stack(
        fit: StackFit.expand,
        children: [
          _buildVideoView(),
          if (_doubleTapSeekDirection != null) _buildSeekIndicator(),
          if (_showControls) _buildControls(),
          if (_isBuffering && !_isLoading) const BufferingIndicator(),
        ],
      );
    }

    // Trong chế độ Portrait
    final headerHeight = 44.0 + MediaQuery.of(context).padding.top;

    return Column(
      children: [
        SizedBox(height: headerHeight),
        Stack(
          children: [
            AspectRatio(aspectRatio: 16 / 9, child: _buildVideoView()),
            if (_doubleTapSeekDirection != null) _buildSeekIndicator(),
            if (_showControls) Positioned.fill(child: _buildControls()),
            if (_isBuffering && !_isLoading)
              const Positioned.fill(child: BufferingIndicator()),
          ],
        ),
        Expanded(child: _buildNotes()),
      ],
    );
  }

  Widget _buildVideoView() {
    return VideoPlayerView(
      controller: _videoController,
      fit: _videoFit,
      onTap: _onVideoTap,
      onDoubleTapDown: _handleDoubleTap,
    );
  }

  Widget _buildSeekIndicator() {
    return SeekIndicator(
      animation: _seekAnimation,
      isLeft: _doubleTapSeekDirection == -1,
    );
  }

  Widget _buildControls() {
    return VideoPlayerControls(
      controlsFadeAnimation: _controlsFadeAnimation,
      isLocked: _isLocked,
      isFullscreen: _isFullscreen,
      isPlaying: _isPlaying,
      isSeeking: _isSeeking,
      position: _position,
      seekPosition: _seekPosition,
      duration: _duration,
      lesson: _lesson,
      onToggleLock: _toggleLock,
      onToggleFullscreen: _toggleFullscreen,
      onPlayPause: _togglePlayPause,
      onSeekRelative: _seekRelative,
      onSeekStart: _onSeekStart,
      onSeekUpdate: _onSeekUpdate,
      onSeekEnd: _onSeekEnd,
      onTap: _onVideoTap,
      onDoubleTapDown: _handleDoubleTap,
      quizzes: _quizzes,
    );
  }

  Widget _buildNotes() {
    return VideoNotesSection(
      notes: _notes,
      isLoading: _isLoadingNotes,
      onAddNote: () {
        _player.pause();
        _showAddNoteModal();
      },
      onSeekToNote: (seconds) {
        _player.seek(Duration(seconds: seconds));
        if (!_isPlaying) _player.play();
      },
      scrollController: scrollTrackingController,
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
                      color: Colors.white,
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
                placeholderStyle: TextStyle(
                  color: Colors.white.withOpacity(0.3),
                ),
                maxLines: 4,
                autofocus: true,
                style: const TextStyle(color: Colors.white, fontSize: 16),
                decoration: BoxDecoration(
                  color: const Color(0xFF2C2C2E), // _systemGray5
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0x1AFFFFFF)),
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
                          color: Colors.white.withOpacity(0.05),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Text(
                          'Hủy',
                          style: TextStyle(color: Color(0x99FFFFFF)),
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
                            color: Colors.white,
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
      // Handle play resume? Already done in actions.
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
