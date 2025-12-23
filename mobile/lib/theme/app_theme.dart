import 'package:flutter/material.dart';
import 'package:flutter/cupertino.dart';
import 'app_colors.dart';

class AppTheme {
  // iOS-inspired text styles (similar to SF Pro)
  static TextTheme get _textTheme {
    const String fontFamily = 'BeVietnamPro';
    return const TextTheme(
      // Large Title - iOS style
      displayLarge: TextStyle(
        fontFamily: fontFamily,
        fontSize: 34,
        fontWeight: FontWeight.w700,
        letterSpacing: 0.37,
        height: 1.2,
      ),
      // Title 1
      displayMedium: TextStyle(
        fontFamily: fontFamily,
        fontSize: 28,
        fontWeight: FontWeight.w700,
        letterSpacing: 0.36,
        height: 1.3,
      ),
      // Title 2
      displaySmall: TextStyle(
        fontFamily: fontFamily,
        fontSize: 22,
        fontWeight: FontWeight.w600,
        letterSpacing: 0.35,
        height: 1.3,
      ),
      // Title 3
      headlineMedium: TextStyle(
        fontFamily: fontFamily,
        fontSize: 20,
        fontWeight: FontWeight.w600,
        letterSpacing: 0.38,
        height: 1.3,
      ),
      // Headline
      headlineSmall: TextStyle(
        fontFamily: fontFamily,
        fontSize: 17,
        fontWeight: FontWeight.w600,
        letterSpacing: -0.41,
        height: 1.3,
      ),
      // Body
      bodyLarge: TextStyle(
        fontFamily: fontFamily,
        fontSize: 17,
        fontWeight: FontWeight.w400,
        letterSpacing: -0.41,
        height: 1.4,
      ),
      // Callout
      bodyMedium: TextStyle(
        fontFamily: fontFamily,
        fontSize: 16,
        fontWeight: FontWeight.w400,
        letterSpacing: -0.32,
        height: 1.4,
      ),
      // Subheadline
      bodySmall: TextStyle(
        fontFamily: fontFamily,
        fontSize: 15,
        fontWeight: FontWeight.w400,
        letterSpacing: -0.24,
        height: 1.4,
      ),
      // Footnote
      labelLarge: TextStyle(
        fontFamily: fontFamily,
        fontSize: 13,
        fontWeight: FontWeight.w400,
        letterSpacing: -0.08,
        height: 1.3,
      ),
      // Caption 1
      labelMedium: TextStyle(
        fontFamily: fontFamily,
        fontSize: 12,
        fontWeight: FontWeight.w400,
        letterSpacing: 0,
        height: 1.3,
      ),
      // Caption 2
      labelSmall: TextStyle(
        fontFamily: fontFamily,
        fontSize: 11,
        fontWeight: FontWeight.w400,
        letterSpacing: 0.07,
        height: 1.3,
      ),
    );
  }

  static ThemeData get lightTheme {
    return ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        primary: AppColors.primary,
        secondary: AppColors.primaryEnd,
        brightness: Brightness.light,
        background: const Color(0xFFF2F2F7), // iOS background color
        surface: Colors.white,
      ),
      useMaterial3: true,
      fontFamily: 'BeVietnamPro',
      textTheme: _textTheme,
      scaffoldBackgroundColor: const Color(0xFFF2F2F7),

      // AppBar theme - iOS style
      appBarTheme: AppBarTheme(
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false, // iOS typically left-aligns
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.black,
        titleTextStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 34,
          fontWeight: FontWeight.w700,
          letterSpacing: 0.37,
          color: Colors.black,
        ),
      ),

      // Bottom Navigation Bar theme - iOS style
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        type: BottomNavigationBarType.fixed,
        selectedItemColor: AppColors.primary,
        unselectedItemColor: const Color(0xFF999999),
        showSelectedLabels: true,
        showUnselectedLabels: true,
        elevation: 0,
        backgroundColor: Colors.white.withOpacity(0.95),
        selectedLabelStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 10,
          fontWeight: FontWeight.w500,
          letterSpacing: 0.12,
        ),
        unselectedLabelStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 10,
          fontWeight: FontWeight.w500,
          letterSpacing: 0.12,
        ),
      ),

      // Elevated Button theme - iOS style
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          shadowColor: Colors.transparent,
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          textStyle: const TextStyle(
            fontFamily: 'BeVietnamPro',
            fontSize: 17,
            fontWeight: FontWeight.w600,
            letterSpacing: -0.41,
          ),
        ),
      ),

      // Card theme - iOS style with subtle shadow
      cardTheme: CardThemeData(
        elevation: 0,
        color: Colors.white,
        shadowColor: Colors.black.withOpacity(0.05),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      ),

      // List Tile theme - iOS style
      listTileTheme: ListTileThemeData(
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),

      // Input decoration theme - iOS style
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: const Color(0xFFF2F2F7),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 16,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: AppColors.primary.withOpacity(0.5),
            width: 1,
          ),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Colors.red, width: 1),
        ),
        hintStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 17,
          fontWeight: FontWeight.w400,
          color: Color(0xFF999999),
        ),
      ),

      // Divider theme - iOS style
      dividerTheme: const DividerThemeData(
        thickness: 0.5,
        color: Color(0xFFE5E5EA),
        space: 1,
      ),
    );
  }

  static ThemeData get darkTheme {
    return ThemeData(
      colorScheme: ColorScheme.fromSeed(
        seedColor: AppColors.primary,
        primary: AppColors.primary,
        secondary: AppColors.primaryEnd,
        brightness: Brightness.dark,
        background: const Color(0xFF000000), // iOS dark background
        surface: const Color(0xFF1C1C1E), // iOS dark surface (gray 6)
      ),
      useMaterial3: true,
      fontFamily: 'BeVietnamPro',
      textTheme: _textTheme.apply(
        bodyColor: Colors.white,
        displayColor: Colors.white,
      ),
      scaffoldBackgroundColor: const Color(0xFF000000),

      // AppBar theme - iOS style
      appBarTheme: const AppBarTheme(
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        backgroundColor: Colors.transparent,
        foregroundColor: Colors.white,
        titleTextStyle: TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 34,
          fontWeight: FontWeight.w700,
          letterSpacing: 0.37,
          color: Colors.white,
        ),
      ),

      // Bottom Navigation Bar theme - iOS style
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        type: BottomNavigationBarType.fixed,
        selectedItemColor: AppColors.primary,
        unselectedItemColor: const Color(0xFF8E8E93),
        showSelectedLabels: true,
        showUnselectedLabels: true,
        elevation: 0,
        backgroundColor: const Color(0xFF1C1C1E).withOpacity(0.95),
        selectedLabelStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 10,
          fontWeight: FontWeight.w500,
          letterSpacing: 0.12,
        ),
        unselectedLabelStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 10,
          fontWeight: FontWeight.w500,
          letterSpacing: 0.12,
        ),
      ),

      // Elevated Button theme - iOS style
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: Colors.white,
          elevation: 0,
          shadowColor: Colors.transparent,
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          textStyle: const TextStyle(
            fontFamily: 'BeVietnamPro',
            fontSize: 17,
            fontWeight: FontWeight.w600,
            letterSpacing: -0.41,
          ),
        ),
      ),

      // Card theme - iOS style
      cardTheme: CardThemeData(
        elevation: 0,
        color: const Color(0xFF1C1C1E),
        shadowColor: Colors.black.withOpacity(0.2),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      ),

      // List Tile theme
      listTileTheme: ListTileThemeData(
        contentPadding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ),

      // Input decoration theme
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: const Color(0xFF1C1C1E),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: 16,
          vertical: 16,
        ),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: AppColors.primary.withOpacity(0.5),
            width: 1,
          ),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: const BorderSide(color: Colors.red, width: 1),
        ),
        hintStyle: const TextStyle(
          fontFamily: 'BeVietnamPro',
          fontSize: 17,
          fontWeight: FontWeight.w400,
          color: Color(0xFF8E8E93),
        ),
      ),

      // Divider theme
      dividerTheme: const DividerThemeData(
        thickness: 0.5,
        color: Color(0xFF38383A),
        space: 1,
      ),
    );
  }
}
