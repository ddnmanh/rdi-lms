import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';

class SeekIndicator extends StatelessWidget {
  final Animation<double> animation;
  final bool isLeft;

  const SeekIndicator({
    super.key,
    required this.animation,
    required this.isLeft,
  });

  @override
  Widget build(BuildContext context) {
    return Positioned(
      left: isLeft ? 40 : null,
      right: isLeft ? null : 40,
      top: 0,
      bottom: 0,
      child: FadeTransition(
        opacity: animation,
        child: Center(
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: BoxDecoration(
              color: const Color(0xFF1C1C1E).withOpacity(0.9), // _systemGray6
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  isLeft
                      ? CupertinoIcons.gobackward_10
                      : CupertinoIcons.goforward_10,
                  color: Colors.white,
                  size: 36,
                ),
                const SizedBox(height: 8),
                Text(
                  isLeft ? '-10s' : '+10s',
                  style: const TextStyle(
                    color: Colors.white,
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
}

class BufferingIndicator extends StatelessWidget {
  const BufferingIndicator({super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        width: 64,
        height: 64,
        decoration: BoxDecoration(
          color: const Color(0xFF1C1C1E).withOpacity(0.9), // _systemGray6
          borderRadius: BorderRadius.circular(16),
        ),
        child: const Center(
          child: CupertinoActivityIndicator(color: Colors.white, radius: 14),
        ),
      ),
    );
  }
}

class VideoLoadingState extends StatelessWidget {
  final String title;

  const VideoLoadingState({super.key, required this.title});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.black,
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: const Color(0xFF1C1C1E),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Center(
                child: CupertinoActivityIndicator(
                  color: Colors.white,
                  radius: 16,
                ),
              ),
            ),
            const SizedBox(height: 24),
            const Text(
              'Đang tải video...',
              style: TextStyle(
                color: Color(0x99FFFFFF), // _white60
                fontSize: 16,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 48),
              child: Text(
                title,
                style: const TextStyle(
                  color: Color(0x66FFFFFF),
                  fontSize: 14,
                ), // _white40
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
}

class VideoErrorState extends StatelessWidget {
  final VoidCallback onRetry;
  final VoidCallback onBack;

  const VideoErrorState({
    super.key,
    required this.onRetry,
    required this.onBack,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      color: Colors.black,
      padding: const EdgeInsets.all(40),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: const Color(0xFF1C1C1E),
              borderRadius: BorderRadius.circular(20),
            ),
            child: const Icon(
              CupertinoIcons.exclamationmark_triangle_fill,
              color: Color(0xFFFF9F0A), // _accentOrange
              size: 36,
            ),
          ),
          const SizedBox(height: 24),
          const Text(
            'Không thể phát video',
            style: TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 12),
          const Text(
            'Tải video thất bại, vui lòng thử lại sau.',
            textAlign: TextAlign.center,
            style: TextStyle(
              color: Color(0x99FFFFFF),
              fontSize: 15,
              height: 1.4,
            ),
          ),
          const SizedBox(height: 32),
          CupertinoButton(
            color: const Color(0xFF0A84FF), // _accentBlue
            borderRadius: BorderRadius.circular(14),
            onPressed: onRetry,
            child: const Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(
                  CupertinoIcons.arrow_clockwise,
                  size: 18,
                  color: Colors.white,
                ),
                SizedBox(width: 8),
                Text(
                  'Thử lại',
                  style: TextStyle(
                    fontWeight: FontWeight.w600,
                    color: Colors.white,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          CupertinoButton(onPressed: onBack, child: const Text('Quay lại')),
        ],
      ),
    );
  }
}
