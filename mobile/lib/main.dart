import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:provider/provider.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:media_kit/media_kit.dart';

import 'data/services/api_service.dart';
import 'data/repositories/auth_repository.dart';
import 'data/repositories/course_repository.dart';
import 'data/repositories/user_repository.dart';
import 'providers/auth_provider.dart';
import 'providers/course_provider.dart';
import 'providers/navigation_provider.dart';
import 'presentation/screens/auth/login_screen.dart';
import 'presentation/screens/main/main_screen.dart';
import 'presentation/screens/splash/splash_screen.dart';
import 'theme/app_theme.dart';
import 'providers/theme_provider.dart';

Future<void> main() async {
  print("ST1: Khởi tạo WidgetsFlutterBinding");
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize MediaKit for video playback
  print("ST2: Khởi tạo MediaKit");
  MediaKit.ensureInitialized();

  // Load biến môi trường từ file .env
  print("ST3: Load biến môi trường");
  try {
    await dotenv.load(fileName: '.env');
  } catch (e) {
    // Nếu không tìm thấy file .env, sử dụng giá trị mặc định
    debugPrint('Warning: Could not load .env file: $e');
  }

  print("ST4: Khởi tạo ứng dụng");
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    final dio = Dio();
    final storage = const FlutterSecureStorage();
    final apiService = ApiService(dio, storage);
    final authRepository = AuthRepository(apiService, storage);
    final courseRepository = CourseRepository(apiService);
    final userRepository = UserRepository(apiService);

    return MultiProvider(
      providers: [
        ChangeNotifierProvider(
          create: (_) =>
              AuthProvider(authRepository, userRepository, storage)..loadUser(),
        ),
        ChangeNotifierProvider(create: (_) => CourseProvider(courseRepository)),
        ChangeNotifierProvider(create: (_) => NavigationProvider()),
        ChangeNotifierProvider(create: (_) => ThemeProvider()),
      ],
      child: Consumer<ThemeProvider>(
        builder: (context, themeProvider, child) {
          return MaterialApp(
            title: 'LMS App',
            debugShowCheckedModeBanner: false,
            theme: AppTheme.lightTheme,
            darkTheme: AppTheme.darkTheme,
            themeMode: themeProvider.themeMode,
            localizationsDelegates: const [
              GlobalMaterialLocalizations.delegate,
              GlobalWidgetsLocalizations.delegate,
              GlobalCupertinoLocalizations.delegate,
            ],
            supportedLocales: const [Locale('vi', 'VN'), Locale('en', 'US')],
            locale: const Locale('vi', 'VN'),
            home: const AuthWrapper(),
          );
        },
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    print("AuthWrapper: Building...");
    return Consumer<AuthProvider>(
      builder: (context, auth, _) {
        print(
          "AuthWrapper: isLoading=${auth.isLoading}, isInitializing=${auth.isInitializing}, isAuthenticated=${auth.isAuthenticated}",
        );
        if (auth.isInitializing) {
          print("AuthWrapper: Show SplashScreen");
          return const SplashScreen();
        }
        if (auth.isAuthenticated) {
          print("AuthWrapper: Show MainScreen");
          return const MainScreen();
        }
        print("AuthWrapper: Show LoginScreen");
        return const LoginScreen();
      },
    );
  }
}
