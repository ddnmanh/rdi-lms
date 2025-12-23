import 'dart:ui';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import '../../../../data/models/lesson_model.dart';

class VideoPlayerControls extends StatelessWidget {
  final Animation<double> controlsFadeAnimation;
  final bool isLocked;
  final bool isFullscreen;
  final bool isPlaying;
  final bool isSeeking;
  final Duration position;
  final Duration seekPosition;
  final Duration duration;
  final Lesson? lesson;
  final VoidCallback onToggleLock;
  final VoidCallback onToggleFullscreen;
  final VoidCallback onPlayPause;
  final Function(int) onSeekRelative;
  final Function(bool, Duration) onSeekStart;
  final Function(Duration) onSeekUpdate;
  final Function(Duration) onSeekEnd;
  final VoidCallback onTap;
  final Function(TapDownDetails, double) onDoubleTapDown;

  const VideoPlayerControls({
    super.key,
    required this.controlsFadeAnimation,
    required this.isLocked,
    required this.isFullscreen,
    required this.isPlaying,
    required this.isSeeking,
    required this.position,
    required this.seekPosition,
    required this.duration,
    required this.lesson,
    required this.onToggleLock,
    required this.onToggleFullscreen,
    required this.onPlayPause,
    required this.onSeekRelative,
    required this.onSeekStart,
    required this.onSeekUpdate,
    required this.onSeekEnd,
    required this.onTap,
    required this.onDoubleTapDown,
  });

  // Colors
  static const Color _accentBlue = Color(0xFF0A84FF);
  static const Color _white = Color(0xFFFFFFFF);
  static const Color _white20 = Color(0x33FFFFFF);
  static const Color _white10 = Color(0x1AFFFFFF);
  static const Color _systemGray5 = Color(0xFF2C2C2E);

  @override
  Widget build(BuildContext context) {
    return FadeTransition(
      opacity: controlsFadeAnimation,
      child: GestureDetector(
        onTap: onTap,
        onDoubleTapDown: (details) =>
            onDoubleTapDown(details, MediaQuery.of(context).size.width),
        onDoubleTap: () {},
        behavior: HitTestBehavior.opaque,
        child: Container(
          decoration: BoxDecoration(
            color: Colors.black.withOpacity(0.15),
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
          child: isLocked
              ? _buildLockedControls(context)
              : _buildFullControls(context),
        ),
      ),
    );
  }

  Widget _buildLockedControls(BuildContext context) {
    Widget content = Stack(
      children: [
        Positioned(
          top: 12,
          left: 16,
          child: _buildGlassButton(
            icon: CupertinoIcons.lock_fill,
            label: 'Đã khóa',
            onTap: onToggleLock,
            isActive: true,
          ),
        ),
      ],
    );

    if (isFullscreen) {
      return SafeArea(child: content);
    }
    return content;
  }

  Widget _buildFullControls(BuildContext context) {
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

    if (isFullscreen) {
      return SafeArea(child: content);
    }

    return Padding(padding: const EdgeInsets.only(top: 8.0), child: content);
  }

  Widget _buildTopBar() {
    if (!isFullscreen) return const SizedBox(height: 48);

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          CupertinoButton(
            padding: EdgeInsets.zero,
            onPressed: onToggleFullscreen,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: const BoxDecoration(
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
          onTap: () => onSeekRelative(-15),
        ),
        const SizedBox(width: 40),
        _buildPlayButton(),
        const SizedBox(width: 40),
        _buildCircleButton(
          icon: CupertinoIcons.goforward_15,
          size: 26,
          buttonSize: 52,
          onTap: () => onSeekRelative(15),
        ),
      ],
    );
  }

  Widget _buildPlayButton() {
    return GestureDetector(
      onTap: onPlayPause,
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
              isPlaying ? CupertinoIcons.pause_fill : CupertinoIcons.play_fill,
              key: ValueKey(isPlaying),
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
    final currentPosition = isSeeking ? seekPosition : position;
    final totalDuration = duration.inMilliseconds > 0
        ? duration
        : Duration(seconds: lesson?.duration ?? 0);

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 0, 8, 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Expanded(child: _buildProgressBar(currentPosition, totalDuration)),
          CupertinoButton(
            padding: const EdgeInsets.all(8),
            onPressed: onToggleFullscreen,
            child: Icon(
              isFullscreen
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
            value: position.inMilliseconds.toDouble().clamp(0, maxMs),
            min: 0,
            max: maxMs > 0 ? maxMs : 1,
            onChangeStart: (v) =>
                onSeekStart(true, Duration(milliseconds: v.toInt())),
            onChanged: (v) => onSeekUpdate(Duration(milliseconds: v.toInt())),
            onChangeEnd: (v) => onSeekEnd(Duration(milliseconds: v.toInt())),
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

  String _formatDuration(Duration d) {
    String twoDigits(int n) => n.toString().padLeft(2, '0');
    final h = d.inHours;
    final m = d.inMinutes.remainder(60);
    final s = d.inSeconds.remainder(60);
    return h > 0
        ? '$h:${twoDigits(m)}:${twoDigits(s)}'
        : '${twoDigits(m)}:${twoDigits(s)}';
  }
}
