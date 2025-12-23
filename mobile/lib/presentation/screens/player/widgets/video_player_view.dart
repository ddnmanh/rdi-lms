import 'package:flutter/material.dart';
import 'package:media_kit_video/media_kit_video.dart';

class VideoPlayerView extends StatelessWidget {
  final VideoController controller;
  final BoxFit fit;
  final VoidCallback onTap;
  final void Function(TapDownDetails details, double screenWidth)
  onDoubleTapDown;

  const VideoPlayerView({
    super.key,
    required this.controller,
    required this.fit,
    required this.onTap,
    required this.onDoubleTapDown,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      onDoubleTapDown: (details) =>
          onDoubleTapDown(details, MediaQuery.of(context).size.width),
      onDoubleTap: () {}, // Required for onDoubleTapDown to work
      child: Container(
        color: Colors.black,
        child: Center(
          child: Video(
            controller: controller,
            controls: NoVideoControls,
            fit: fit,
          ),
        ),
      ),
    );
  }
}
