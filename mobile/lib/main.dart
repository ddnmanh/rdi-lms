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
import 'presentation/screens/auth/login_screen.dart';
import 'presentation/screens/main/main_screen.dart';
import 'presentation/screens/splash/splash_screen.dart';
import 'theme/app_theme.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize MediaKit for video playback
  MediaKit.ensureInitialized();

  // Load biến môi trường từ file .env
  try {
    await dotenv.load(fileName: '.env');
  } catch (e) {
    // Nếu không tìm thấy file .env, sử dụng giá trị mặc định
    debugPrint('Warning: Could not load .env file: $e');
  }

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
      ],
      child: MaterialApp(
        title: 'LMS App',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        localizationsDelegates: const [
          GlobalMaterialLocalizations.delegate,
          GlobalWidgetsLocalizations.delegate,
          GlobalCupertinoLocalizations.delegate,
        ],
        supportedLocales: const [
          Locale('vi', 'VN'),
          Locale('en', 'US'),
        ],
        locale: const Locale('vi', 'VN'),
        home: const AuthWrapper(),
      ),
    );
  }
}

class AuthWrapper extends StatelessWidget {
  const AuthWrapper({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, auth, _) {
        if (auth.isInitializing) {
          return const SplashScreen();
        }
        if (auth.isAuthenticated) {
          return const MainScreen();
        }
        return const LoginScreen();
      },
    );
  }
}
