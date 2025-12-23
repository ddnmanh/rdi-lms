import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../../../data/models/lesson_note_model.dart';

class VideoNotesSection extends StatelessWidget {
  final List<LessonNote> notes;
  final bool isLoading;
  final VoidCallback onAddNote;
  final Function(int) onSeekToNote;
  final ScrollController? scrollController;

  const VideoNotesSection({
    super.key,
    required this.notes,
    required this.isLoading,
    required this.onAddNote,
    required this.onSeekToNote,
    this.scrollController,
  });

  // Colors
  static const Color _accentBlue = Color(0xFF0A84FF);
  static const Color _systemGray5 = Color(0xFF2C2C2E);
  static const Color _white = Color(0xFFFFFFFF);
  static const Color _white10 = Color(0x1AFFFFFF);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      color: isDark ? _systemGray5 : const Color(0xFFF2F2F7),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildNotesHeader(context),
          Divider(height: 1, color: isDark ? _white10 : Colors.black12),
          Expanded(
            child: isLoading
                ? const Center(
                    child: CupertinoActivityIndicator(color: _accentBlue),
                  )
                : notes.isEmpty
                ? _buildEmptyNotes(context)
                : _buildNotesList(),
          ),
        ],
      ),
    );
  }

  Widget _buildNotesHeader(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
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
                '${notes.length} Ghi chú',
                style: TextStyle(
                  color: isDark ? _white : Colors.black,
                  fontSize: 17,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          CupertinoButton(
            padding: EdgeInsets.zero,
            onPressed: onAddNote,
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

  Widget _buildEmptyNotes(BuildContext context) {
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
      controller: scrollController,
      padding: const EdgeInsets.all(16),
      itemCount: notes.length,
      separatorBuilder: (context, index) => const SizedBox(height: 16),
      itemBuilder: (context, index) {
        return _buildNoteItem(context, notes[index]);
      },
    );
  }

  Widget _buildNoteItem(BuildContext context, LessonNote note) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: () {
        HapticFeedback.lightImpact();
        onSeekToNote(note.durationAt);
      },
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: isDark ? _systemGray5 : Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: isDark ? _white10 : Colors.black.withOpacity(0.05),
          ),
          boxShadow: !isDark
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
                    color: isDark ? _white.withOpacity(0.4) : Colors.black38,
                    fontSize: 11,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              note.content,
              style: TextStyle(
                color: isDark ? _white : Colors.black87,
                fontSize: 14,
                height: 1.4,
              ),
            ),
          ],
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

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      return '${date.day}/${date.month}/${date.year}';
    } catch (e) {
      return dateStr;
    }
  }
}
