import 'package:flutter/material.dart';

class AppColors {
  // Primary gradient colors
  static const Color primaryStart = Color.fromARGB(255, 78, 73, 201);
  static const Color primaryEnd = Color.fromARGB(255, 224, 93, 247);

  // Primary color (for single color usage)
  static const Color primary = Color.fromARGB(255, 78, 73, 201);

  // Primary gradient
  static const LinearGradient primaryGradient = LinearGradient(
    colors: [primaryStart, primaryEnd],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );

  // Horizontal gradient
  static const LinearGradient primaryGradientHorizontal = LinearGradient(
    colors: [primaryStart, primaryEnd],
    begin: Alignment.centerLeft,
    end: Alignment.centerRight,
  );

  // Vertical gradient
  static const LinearGradient primaryGradientVertical = LinearGradient(
    colors: [primaryStart, primaryEnd],
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
  );

  // Additional colors
  static const Color white = Colors.white;
  static const Color black = Colors.black;
  static const Color grey = Color(0xFF9E9E9E);
  static const Color lightGrey = Color(0xFFE0E0E0);
  static const Color background = Color(0xFFF5F5F5);

  static Color? get cardBackground => null;
}
