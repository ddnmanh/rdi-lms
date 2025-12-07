import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../../providers/auth_provider.dart';
import '../../../theme/app_colors.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _fullnameController = TextEditingController();
  final _birthdayController = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  DateTime? _selectedDate;
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _fullnameController.dispose();
    _birthdayController.dispose();
    super.dispose();
  }

  Future<void> _register() async {
    // Ẩn bàn phím
    FocusScope.of(context).unfocus();

    if (_formKey.currentState!.validate()) {
      final success = await context.read<AuthProvider>().register(
        _emailController.text,
        _passwordController.text,
        _fullnameController.text,
        _birthdayController.text,
      );
      if (success && mounted) {
        Navigator.pop(context);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Đăng ký thành công. Vui lòng đăng nhập.'),
            backgroundColor: Colors.green,
          ),
        );
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              context.read<AuthProvider>().error ?? 'Đăng ký thất bại',
            ),
            backgroundColor: Colors.red,
            behavior: SnackBarBehavior.floating,
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          ),
        );
      }
    }
  }

  void _showDatePicker() {
    final now = DateTime.now();
    final initial = _selectedDate ?? DateTime(2000, 1, 1);

    showCupertinoDialog(
      context: context,
      barrierDismissible: true,
      builder: (BuildContext dialogContext) {
        DateTime tempPickedDate = initial;

        return Center(
          child: Material(
            color: Colors.transparent,
            child: Container(
              width: MediaQuery.of(context).size.width * 0.9,
              decoration: BoxDecoration(
                color: CupertinoColors.systemBackground.resolveFrom(context),
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.18),
                    blurRadius: 30,
                    offset: const Offset(0, 18),
                  ),
                ],
              ),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const SizedBox(height: 12),
                  Text(
                    'Chọn ngày sinh',
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Divider(height: 1),
                  SizedBox(
                    height: 200,
                    child: CupertinoDatePicker(
                      initialDateTime: initial,
                      mode: CupertinoDatePickerMode.date,
                      use24hFormat: true,
                      maximumDate: now,
                      onDateTimeChanged: (DateTime newDate) {
                        tempPickedDate = newDate;
                      },
                    ),
                  ),
                  const Divider(height: 1),
                  Row(
                    children: [
                      Expanded(
                        child: CupertinoButton(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          onPressed: () {
                            Navigator.of(dialogContext).pop();
                          },
                          child: const Text(
                            'Hủy',
                            style: TextStyle(
                              fontSize: 16,
                              color: Colors.redAccent,
                            ),
                          ),
                        ),
                      ),
                      Container(
                        width: 1,
                        height: 44,
                        color: Colors.black.withOpacity(0.05),
                      ),
                      Expanded(
                        child: CupertinoButton(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          onPressed: () {
                            setState(() {
                              _selectedDate = tempPickedDate;
                              _birthdayController.text =
                                  DateFormat('yyyy-MM-dd').format(tempPickedDate);
                            });
                            Navigator.of(dialogContext).pop();
                          },
                          child: const Text(
                            'Xong',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                              color: AppColors.primary,
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required String placeholder,
    required IconData icon,
    bool obscureText = false,
    TextInputType? keyboardType,
    Widget? suffix,
    VoidCallback? onTap,
    bool readOnly = false,
  }) {
    // Style field đồng bộ với LoginScreen (iOS 18)
    const inputBorderRadius = BorderRadius.all(Radius.circular(14));
    const inputFillColor = Color(0xFFF5F5F7); // iOS System Gray 6

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: Colors.black54,
          ),
        ),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          obscureText: obscureText,
          keyboardType: keyboardType,
          readOnly: readOnly,
          onTap: onTap,
          style: const TextStyle(
            fontSize: 17,
            fontWeight: FontWeight.w500,
          ),
          decoration: InputDecoration(
            filled: true,
            fillColor: inputFillColor,
            hintText: placeholder,
            hintStyle: TextStyle(color: Colors.grey[400]),
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 18,
              vertical: 16,
            ),
            border: const OutlineInputBorder(
              borderRadius: inputBorderRadius,
              borderSide: BorderSide.none,
            ),
            focusedBorder: const OutlineInputBorder(
              borderRadius: inputBorderRadius,
              borderSide: BorderSide(
                color: AppColors.primary,
                width: 1.4,
              ),
            ),
            prefixIcon: Icon(icon, color: Colors.grey),
            suffixIcon: suffix,
          ),
          validator: (v) => v!.isEmpty ? 'Bắt buộc' : null,
        ),
        const SizedBox(height: 24),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final isLoading = context.watch<AuthProvider>().isLoading;

    // Styles theo phong cách iOS 18, đồng bộ với LoginScreen
    final theme = Theme.of(context);
    final textTheme = theme.textTheme;

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: SystemUiOverlayStyle.dark.copyWith(
        statusBarColor: Colors.transparent,
        statusBarIconBrightness: Brightness.dark,
        statusBarBrightness: Brightness.light,
      ),
      child: Scaffold(
        backgroundColor: Colors.transparent,
        body: Stack(
          children: [
            // Nền gradient nhẹ
            Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Color(0xFFF5F5F7),
                    Color(0xFFFFFFFF),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
              ),
            ),
            SafeArea(
              child: Center(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 16),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Thanh kéo nhỏ phía trên giống iOS sheet
                        // Align(
                        //   alignment: Alignment.center,
                        //   child: Container(
                        //     width: 36,
                        //     height: 4,
                        //     margin: const EdgeInsets.only(bottom: 24),
                        //     decoration: BoxDecoration(
                        //       color: Colors.black.withOpacity(0.08),
                        //       borderRadius: BorderRadius.circular(2),
                        //     ),
                        //   ),
                        // ),
                        // Tiêu đề
                        Text(
                          'Đăng ký',
                          style: textTheme.headlineMedium?.copyWith(
                                fontSize: 32,
                                fontWeight: FontWeight.w800,
                                letterSpacing: -0.5,
                                color: AppColors.black,
                              ) ??
                              const TextStyle(
                                fontSize: 32,
                                fontWeight: FontWeight.w800,
                                letterSpacing: -0.5,
                                color: AppColors.black,
                              ),
                        ),
                        const SizedBox(height: 6),
                        Text(
                          'Tạo tài khoản mới để bắt đầu học',
                          style: textTheme.bodyMedium?.copyWith(
                                fontSize: 15,
                                color: Colors.black54,
                                fontWeight: FontWeight.w500,
                              ) ??
                              const TextStyle(
                                fontSize: 15,
                                color: Colors.black54,
                                fontWeight: FontWeight.w500,
                              ),
                        ),
                        const SizedBox(height: 28),

                        // Card form
                        Container(
                          width: double.infinity,
                          padding: const EdgeInsets.fromLTRB(18, 20, 18, 18),
                          decoration: BoxDecoration(
                            color: Colors.white.withOpacity(0.96),
                            borderRadius: BorderRadius.circular(22),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.06),
                                offset: const Offset(0, 18),
                                blurRadius: 40,
                                spreadRadius: -6,
                              ),
                            ],
                            border: Border.all(
                              color: Colors.white.withOpacity(0.9),
                              width: 0.6,
                            ),
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              _buildTextField(
                                controller: _emailController,
                                label: 'Email',
                                placeholder: 'nhap@email.com',
                                icon: Icons.email_outlined,
                                keyboardType: TextInputType.emailAddress,
                              ),
                              _buildTextField(
                                controller: _passwordController,
                                label: 'Mật khẩu',
                                placeholder: '••••••••',
                                icon: Icons.lock_outline_rounded,
                                obscureText: _obscurePassword,
                                suffix: IconButton(
                                  icon: Icon(
                                    _obscurePassword
                                        ? Icons.visibility_off_outlined
                                        : Icons.visibility_outlined,
                                    color: Colors.grey,
                                  ),
                                  onPressed: () {
                                    setState(() {
                                      _obscurePassword = !_obscurePassword;
                                    });
                                  },
                                ),
                              ),
                              _buildTextField(
                                controller: _fullnameController,
                                label: 'Họ và tên',
                                placeholder: 'Nguyễn Văn A',
                                icon: Icons.person_outline_rounded,
                                keyboardType: TextInputType.name,
                              ),
                              _buildTextField(
                                controller: _birthdayController,
                                label: 'Ngày sinh',
                                placeholder: 'Chọn ngày sinh',
                                icon: Icons.calendar_today_outlined,
                                readOnly: true,
                                onTap: _showDatePicker,
                                suffix: const Icon(
                                  Icons.arrow_drop_down,
                                  color: Colors.grey,
                                ),
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: 24),

                        // Nút đăng ký
                        SizedBox(
                          width: double.infinity,
                          height: 56,
                          child: DecoratedBox(
                            decoration: BoxDecoration(
                              gradient: AppColors.primaryGradient,
                              borderRadius: BorderRadius.circular(20),
                              boxShadow: [
                                BoxShadow(
                                  color: AppColors.primary.withOpacity(0.35),
                                  blurRadius: 20,
                                  offset: const Offset(0, 12),
                                ),
                              ],
                            ),
                            child: ElevatedButton(
                              onPressed: isLoading ? null : _register,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.transparent,
                                shadowColor: Colors.transparent,
                                elevation: 0,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(20),
                                ),
                              ),
                              child: AnimatedSwitcher(
                                duration: const Duration(milliseconds: 200),
                                child: isLoading
                                    ? const SizedBox(
                                        key: ValueKey('loading_register'),
                                        height: 24,
                                        width: 24,
                                        child: CircularProgressIndicator(
                                          color: Colors.white,
                                          strokeWidth: 2.5,
                                        ),
                                      )
                                    : const Text(
                                        key: ValueKey('label_register'),
                                        'Đăng ký',
                                        style: TextStyle(
                                          fontSize: 17,
                                          fontWeight: FontWeight.w600,
                                          letterSpacing: 0.2,
                                          color: Colors.white,
                                        ),
                                      ),
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 18),

                        // Link chuyển sang đăng nhập
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              'Đã có tài khoản? ',
                              style: TextStyle(
                                color: Colors.black54,
                                fontSize: 14,
                              ),
                            ),
                            GestureDetector(
                              onTap: () => Navigator.pop(context),
                              child: const Text(
                                'Đăng nhập ngay',
                                style: TextStyle(
                                  color: AppColors.primary,
                                  fontWeight: FontWeight.w600,
                                  fontSize: 14,
                                ),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 10),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
