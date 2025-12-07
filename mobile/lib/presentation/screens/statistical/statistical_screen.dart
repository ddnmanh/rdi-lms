import 'dart:math';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../providers/course_provider.dart';
import '../../../theme/ios_widgets.dart';
import '../../../theme/app_colors.dart';
import '../../widgets/header_bar.dart';
import '../../../core/utils/scroll_tracking.dart';

class StatisticalScreen extends StatefulWidget {
  const StatisticalScreen({super.key});

  @override
  State<StatisticalScreen> createState() => _StatisticalScreenState();
}

class _StatisticalScreenState extends State<StatisticalScreen> with ScrollTracking {

  @override
  double get scrollThreshold => 20.0;

  int _selectedPeriodIndex = 0;
  List<double> _mockChartData = [0.4, 0.6, 0.3, 0.8, 0.5, 0.2, 0.7];
  final Random _random = Random();

  // Tuần: chọn tuần cụ thể
  DateTime _selectedWeekStart = DateTime.now();

  // Tháng: chọn năm cụ thể
  int _selectedYear = DateTime.now().year;

  @override
  void initState() {
    super.initState();
    // Tính ngày đầu tuần (Thứ 2)
    _selectedWeekStart = _getWeekStart(DateTime.now());
    _generateMockData();

    // Đảm bảo courses đã được load
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CourseProvider>().ensureCoursesLoaded();
    });
  }

  DateTime _getWeekStart(DateTime date) {
    // Tính ngày thứ 2 của tuần
    int weekday = date.weekday; // 1 = Monday, 7 = Sunday
    return date.subtract(Duration(days: weekday - 1));
  }

  void _generateMockData() {
    setState(() {
      int dataPoints;
      // Tuần: 7 ngày, Tháng: 12 tháng, Năm: nhiều năm
      switch (_selectedPeriodIndex) {
        case 0: // Tuần - 7 ngày
          dataPoints = 7;
          break;
        case 1: // Tháng - 12 tháng
          dataPoints = 12;
          break;
        case 2: // Năm - 5 năm gần nhất
          dataPoints = 5;
          break;
        default:
          dataPoints = 7;
      }

      _mockChartData = List.generate(
        dataPoints,
        (index) => _random.nextDouble(),
      );
    });
  }


  Future<void> _handleRefresh() async {
    // TODO: Implement refresh logic
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          RefreshIndicator(
            onRefresh: _handleRefresh,
            child: CustomScrollView(
              key: const PageStorageKey('statistical_screen_scroll_view'),
              controller: scrollTrackingController,
              slivers: [

                const SliverToBoxAdapter(child: SizedBox(height: 50)),

                // iOS-style large title
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
                    child: AnimatedOpacity(
                      duration: const Duration(milliseconds: 400),
                      opacity: isScrollOverThreshold ? 0 : 1,
                      child: Text(
                        'Thống kê',
                        style: Theme.of(context).textTheme.displayLarge,
                      ),
                    ),
                  ),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 16)),

                // Summary cards
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Row(
                      children: [
                        Expanded(
                          child: _buildSummaryCard(
                            context,
                            title: 'Thời gian học',
                            value: '24h',
                            subtitle: 'Tuần này',
                            icon: Icons.access_time_rounded,
                            color: AppColors.primaryStart,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildSummaryCard(
                            context,
                            title: 'Kỷ lục',
                            value: '7 ngày',
                            subtitle: 'Đừng bỏ cuộc!',
                            icon: Icons.local_fire_department_rounded,
                            color: AppColors.primaryEnd,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 24)),

                // Time period selector
                SliverToBoxAdapter(
                  child: Container(
                    margin: const EdgeInsets.symmetric(horizontal: 16),
                    decoration: BoxDecoration(
                      color: Color.fromARGB(255, 255, 255, 255),
                      borderRadius: BorderRadius.circular(13),
                    ),
                    padding: const EdgeInsets.all(2),
                    child: IOSSegmentedControl(
                      children: const {0: 'Tuần', 1: 'Tháng', 2: 'Năm'},
                      selectedIndex: _selectedPeriodIndex,
                      onValueChanged: (index) {
                        setState(() {
                          _selectedPeriodIndex = index;
                        });
                        _generateMockData();
                      },
                    ),
                  ),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 24)),

                // Chart section
                SliverToBoxAdapter(
                  child: IOSCard(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Thời gian học',
                              style: Theme.of(context).textTheme.headlineSmall,
                            ),
                            if (_selectedPeriodIndex != 2) // Không hiển thị nút khi ở trạng thái năm
                              _buildTimeSelectorButton(context),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          _getChartSubtitle(),
                          style: Theme.of(context).textTheme.bodySmall?.copyWith(
                            color: const Color(0xFF999999),
                          ),
                        ),
                        const SizedBox(height: 32),
                        // Mock Chart
                        SizedBox(
                          height: 200,
                          child: _buildChart(),
                        ),
                      ],
                    ),
                  ),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 24)),

                // Section header
                const SliverToBoxAdapter(
                  child: IOSSectionHeader(title: 'Tiến trình khóa học'),
                ),

                // Course progress list - sử dụng data thực từ Provider
                SliverToBoxAdapter(
                  child: Consumer<CourseProvider>(
                    builder: (context, courseProvider, child) {
                      if (courseProvider.courses.isEmpty) {
                        return IOSGroupedList(
                          children: [
                            Padding(
                              padding: const EdgeInsets.all(20),
                              child: Center(
                                child: Text(
                                  'Chưa có khóa học nào',
                                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                                    color: const Color(0xFF999999),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        );
                      }

                      // Lấy tối đa 5 khóa học để hiển thị
                      final displayCourses = courseProvider.courses.take(5).toList();

                      return IOSGroupedList(
                        children: displayCourses.map((course) {
                          final progress = (course.progress?.completionPercentage ?? 0) / 100;
                          final completedLessons = course.lessons.where(
                            (l) => l.progress.completionPercentage >= 80
                          ).length;
                          final totalLessons = course.lessons.length;

                          return _buildCourseProgress(
                            context,
                            title: course.title,
                            progress: progress,
                            lessons: totalLessons > 0
                              ? '$completedLessons/$totalLessons bài học'
                              : 'Chưa có bài học',
                          );
                        }).toList(),
                      );
                    },
                  ),
                ),

                const SliverToBoxAdapter(child: SizedBox(height: 120)),
              ],
            ),
          ),
          HeaderBar(
            topOpacity: 1,
            bottomOpacity: 0,
            expanded: isScrollOverThreshold,
            expandedExtraHeight: 30,
            ignoringPointer: !isScrollOverThreshold,
            padding: const EdgeInsets.only(bottom: 10),
            child: Text(
              'Thống kê',
              style: Theme.of(context).textTheme.displayLarge?.copyWith(
                    fontSize: 18,
                    fontWeight: FontWeight.w600,
                  ),
              textAlign: TextAlign.center,
            ),
          ),
        ],
      )
    );
  }

  String _getChartSubtitle() {
    switch (_selectedPeriodIndex) {
      case 0: // Tuần
        final endDate = _selectedWeekStart.add(const Duration(days: 6));
        return 'Tuần ${_selectedWeekStart.day}/${_selectedWeekStart.month} - ${endDate.day}/${endDate.month}';
      case 1: // Tháng
        return 'Năm $_selectedYear';
      case 2: // Năm
        return 'Thống kê theo năm';
      default:
        return 'Số giờ học';
    }
  }

  Widget _buildTimeSelectorButton(BuildContext context) {
    return GestureDetector(
      onTap: () => _showTimeSelector(context),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: AppColors.primary.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: AppColors.primary.withOpacity(0.3),
            width: 1,
          ),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              Icons.calendar_today_rounded,
              size: 16,
              color: AppColors.primary,
            ),
            const SizedBox(width: 6),
            Text(
              _selectedPeriodIndex == 0
                ? 'Tuần ${_getWeekNumber()}'
                : '$_selectedYear',
              style: Theme.of(context).textTheme.labelLarge?.copyWith(
                color: AppColors.primary,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(width: 4),
            Icon(
              Icons.arrow_drop_down_rounded,
              size: 20,
              color: AppColors.primary,
            ),
          ],
        ),
      ),
    );
  }

  int _getWeekNumber() {
    // Tính số tuần trong năm
    final dayOfYear = int.parse(
      _selectedWeekStart.difference(DateTime(_selectedWeekStart.year, 1, 1)).inDays.toString(),
    );
    return (dayOfYear / 7).ceil() + 1;
  }

  void _showTimeSelector(BuildContext context) {
    if (_selectedPeriodIndex == 0) {
      // Chọn tuần
      _showWeekPicker(context);
    } else if (_selectedPeriodIndex == 1) {
      // Chọn năm
      _showYearPicker(context);
    }
  }

  void _showWeekPicker(BuildContext context) {
    // Tìm index của tuần hiện tại
    final currentYear = DateTime.now().year;
    int selectedIndex = 0;

    for (int i = 0; i < 52; i++) {
      final weekStart = DateTime(currentYear, 1, 1).add(Duration(days: i * 7));
      final normalizedWeekStart = _getWeekStart(weekStart);

      if (_selectedWeekStart.year == normalizedWeekStart.year &&
          _selectedWeekStart.month == normalizedWeekStart.month &&
          _selectedWeekStart.day == normalizedWeekStart.day) {
        selectedIndex = i;
        break;
      }
    }

    final scrollController = ScrollController(
      initialScrollOffset: selectedIndex * 72.0, // chiều cao ước tính của mỗi ListTile
    );

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: 300,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Hủy'),
                  ),
                  Text(
                    'Chọn tuần',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.pop(context);
                      _generateMockData();
                    },
                    child: const Text('Xong'),
                  ),
                ],
              ),
            ),
            Expanded(
              child: ListView.builder(
                controller: scrollController,
                itemCount: 52, // 52 tuần trong năm
                itemBuilder: (context, index) {
                  final weekStart = DateTime(currentYear, 1, 1)
                      .add(Duration(days: index * 7));
                  final weekEnd = weekStart.add(const Duration(days: 6));
                  final normalizedWeekStart = _getWeekStart(weekStart);
                  final isSelected = _selectedWeekStart.year == normalizedWeekStart.year &&
                      _selectedWeekStart.month == normalizedWeekStart.month &&
                      _selectedWeekStart.day == normalizedWeekStart.day;

                  return ListTile(
                    selected: isSelected,
                    selectedTileColor: AppColors.primary.withOpacity(0.1),
                    title: Text(
                      'Tuần ${index + 1}',
                      style: TextStyle(
                        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                        color: isSelected ? AppColors.primary : Colors.black,
                      ),
                    ),
                    subtitle: Text(
                      '${weekStart.day}/${weekStart.month} - ${weekEnd.day}/${weekEnd.month}',
                    ),
                    trailing: isSelected
                        ? const Icon(Icons.check_circle, color: AppColors.primary)
                        : null,
                    onTap: () {
                      setState(() {
                        _selectedWeekStart = _getWeekStart(weekStart);
                      });
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    ).whenComplete(() => scrollController.dispose());
  }

  void _showYearPicker(BuildContext context) {
    final currentYear = DateTime.now().year;
    final years = List.generate(10, (index) => currentYear - 9 + index);

    // Tìm index của năm đang được chọn
    final selectedIndex = years.indexOf(_selectedYear);

    final scrollController = ScrollController(
      initialScrollOffset: selectedIndex >= 0 ? selectedIndex * 72.0 : 0.0,
    );

    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent,
      builder: (context) => Container(
        height: 400,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
        ),
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Hủy'),
                  ),
                  Text(
                    'Chọn năm',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.pop(context);
                      _generateMockData();
                    },
                    child: const Text('Xong'),
                  ),
                ],
              ),
            ),
            Expanded(
              child: ListView.builder(
                controller: scrollController,
                itemCount: years.length,
                itemBuilder: (context, index) {
                  final year = years[index];
                  final isSelected = _selectedYear == year;

                  return ListTile(
                    selected: isSelected,
                    selectedTileColor: AppColors.primary.withOpacity(0.1),
                    title: Text(
                      'Năm $year',
                      style: TextStyle(
                        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                        color: isSelected ? AppColors.primary : Colors.black,
                        fontSize: 18,
                      ),
                    ),
                    trailing: isSelected
                        ? const Icon(Icons.check_circle, color: AppColors.primary)
                        : null,
                    onTap: () {
                      setState(() {
                        _selectedYear = year;
                      });
                    },
                  );
                },
              ),
            ),
          ],
        ),
      ),
    ).whenComplete(() => scrollController.dispose());
  }

  Widget _buildSummaryCard(
    BuildContext context, {
    required String title,
    required String value,
    required String subtitle,
    required IconData icon,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: color.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(height: 12),
          Text(
            value,
            style: Theme.of(
              context,
            ).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 4),
          Text(
            title,
            style: Theme.of(
              context,
            ).textTheme.labelLarge?.copyWith(color: const Color(0xFF999999)),
          ),
          const SizedBox(height: 2),
          Text(
            subtitle,
            style: Theme.of(
              context,
            ).textTheme.labelSmall?.copyWith(color: const Color(0xFFCCCCCC)),
          ),
        ],
      ),
    );
  }

  Widget _buildChart() {
    List<String> labels;

    switch (_selectedPeriodIndex) {
      case 0: // Tuần - 7 ngày trong tuần
        labels = ['Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'CN'];
        break;
      case 1: // Tháng - 12 tháng trong năm
        labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
        break;
      case 2: // Năm - các năm gần nhất
        final currentYear = DateTime.now().year;
        labels = List.generate(
          _mockChartData.length,
          (index) => '${currentYear - (_mockChartData.length - 1 - index)}',
        );
        break;
      default:
        labels = ['Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'CN'];
    }

    // Tất cả các view đều hiển thị bình thường
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.end,
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: List.generate(
          _mockChartData.length,
          (index) => Flexible(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 2),
              child: _buildGradientBar(
                context,
                labels[index],
                _mockChartData[index],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildGradientBar(
    BuildContext context,
    String label,
    double heightFactor,
  ) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.end,
      children: [
        Text(
          '${(heightFactor * 8).toStringAsFixed(1)}h',
          style: Theme.of(context).textTheme.labelSmall?.copyWith(
            color: const Color(0xFF999999),
            fontWeight: FontWeight.w600,
          ),
        ),
        const SizedBox(height: 8),
        Container(
          width: 32,
          height: 150 * heightFactor,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [AppColors.primaryEnd, AppColors.primaryStart],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
            borderRadius: BorderRadius.circular(6),
          ),
        ),
        const SizedBox(height: 8),
        Text(
          label,
          style: Theme.of(context).textTheme.labelMedium?.copyWith(
            color: const Color(0xFF666666),
            fontWeight: FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildCourseProgress(
    BuildContext context, {
    required String title,
    required double progress,
    required String lessons,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Expanded(
                child: Text(
                  title,
                  style: Theme.of(
                    context,
                  ).textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.w600),
                ),
              ),
              Text(
                '${(progress * 100).toInt()}%',
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  color: AppColors.primary,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: progress,
              backgroundColor: const Color(0xFFF2F2F7),
              valueColor: const AlwaysStoppedAnimation<Color>(
                AppColors.primary,
              ),
              minHeight: 8,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            lessons,
            style: Theme.of(
              context,
            ).textTheme.labelLarge?.copyWith(color: const Color(0xFF999999)),
          ),
        ],
      ),
    );
  }


}
