import 'dart:io';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:LMS/theme/ios_widgets.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../providers/auth_provider.dart';
import '../../../core/constants/api_constants.dart';
import '../../widgets/notification_modal.dart';
import '../../widgets/header_navigation_bar.dart';

class UpdateProfileScreen extends StatefulWidget {
  const UpdateProfileScreen({super.key});

  @override
  State<UpdateProfileScreen> createState() => _UpdateProfileScreenState();
}

class _UpdateProfileScreenState extends State<UpdateProfileScreen> {
  final _fullnameController = TextEditingController();
  final _formKey = GlobalKey<FormState>();
  final ImagePicker _imagePicker = ImagePicker();

  DateTime? _selectedBirthday;
  File? _selectedImage;
  String? _existingAvatarUrl;

  @override
  void initState() {
    super.initState();
    final user = context.read<AuthProvider>().user;
    if (user != null) {
      _fullnameController.text = user.fullname;
      if (user.birthday != null && user.birthday!.isNotEmpty) {
        try {
          _selectedBirthday = DateTime.parse(user.birthday!);
        } catch (_) {}
      }
      _existingAvatarUrl = user.avatarPath;
    }
  }

  @override
  void dispose() {
    _fullnameController.dispose();
    super.dispose();
  }

  // Thay đổi DatePicker sang phong cách iOS nhưng hiển thị dạng Dialog ở giữa
  void _selectBirthday() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          backgroundColor: Colors.white,
          insetPadding: const EdgeInsets.symmetric(horizontal: 24),
          child: SizedBox(
            height: 300,
            child: Column(
              children: [
                // Header
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      TextButton(
                        onPressed: () => Navigator.of(context).pop(),
                        child: const Text(
                          'Hủy',
                          style: TextStyle(fontSize: 16, color: Colors.red),
                        ),
                      ),
                      const Text(
                        'Chọn ngày sinh',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      TextButton(
                        onPressed: () {
                          if (_selectedBirthday == null) {
                            setState(() {
                              _selectedBirthday = DateTime(2000, 1, 1);
                            });
                          }
                          Navigator.of(context).pop();
                        },
                        child: Text(
                          'Xong',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: Theme.of(context).primaryColor,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const Divider(height: 1, thickness: 0.5),
                // Picker
                Expanded(
                  child: CupertinoDatePicker(
                    initialDateTime: _selectedBirthday ?? DateTime(2000, 1, 1),
                    mode: CupertinoDatePickerMode.date,
                    use24hFormat: true,
                    maximumDate: DateTime.now(),
                    minimumDate: DateTime(1900),
                    onDateTimeChanged: (DateTime newDate) {
                      setState(() {
                        _selectedBirthday = newDate;
                      });
                    },
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Future<void> _pickImage() async {
    showModalBottomSheet(
      context: context,
      backgroundColor: Colors.transparent, // Để bo góc đẹp hơn theo kiểu iOS
      builder: (context) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Column(
                  children: [
                    ListTile(
                      title: const Center(
                          child: Text('Chọn từ thư viện',
                              style: TextStyle(
                                  fontSize: 17, color: Colors.blue))),
                      onTap: () {
                        Navigator.pop(context);
                        _getImage(ImageSource.gallery);
                      },
                    ),
                    const Divider(height: 1, thickness: 0.5),
                    ListTile(
                      title: const Center(
                          child: Text('Chụp ảnh mới',
                              style: TextStyle(
                                  fontSize: 17, color: Colors.blue))),
                      onTap: () {
                        Navigator.pop(context);
                        _getImage(ImageSource.camera);
                      },
                    ),
                    if (_selectedImage != null) ...[
                      const Divider(height: 1, thickness: 0.5),
                      ListTile(
                        title: const Center(
                            child: Text('Xóa ảnh',
                                style: TextStyle(
                                    fontSize: 17, color: Colors.red))),
                        onTap: () {
                          Navigator.pop(context);
                          setState(() {
                            _selectedImage = null;
                            _existingAvatarUrl = null;
                          });
                        },
                      ),
                    ],
                  ],
                ),
              ),
              const SizedBox(height: 8),
              InkWell(
                onTap: () => Navigator.pop(context),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: const Center(
                      child: Text('Hủy',
                          style: TextStyle(
                              fontSize: 17,
                              fontWeight: FontWeight.w600,
                              color: Colors.blue))),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _getImage(ImageSource source) async {
    try {
      final XFile? pickedFile = await _imagePicker.pickImage(
        source: source,
        maxWidth: 800,
        maxHeight: 800,
        imageQuality: 85,
      );
      if (pickedFile != null) {
        setState(() {
          _selectedImage = File(pickedFile.path);
          _existingAvatarUrl = null;
        });
      }
    } catch (e) {
      if (mounted) {
        NotificationModal.show(
          context,
          title: 'Không thể chọn ảnh',
          description: e.toString(),
          icon: Icons.error_outline,
          accentColor: Colors.red,
        );
      }
    }
  }

  Future<void> _updateProfile() async {
    if (_formKey.currentState!.validate()) {
      if (_selectedBirthday == null) {
        NotificationModal.show(
          context,
          title: 'Thiếu thông tin',
          description: 'Vui lòng chọn ngày sinh',
          icon: Icons.calendar_today,
          accentColor: Colors.orange,
        );
        return;
      }

      final birthdayStr = DateFormat('yyyy-MM-dd').format(_selectedBirthday!);

      final success = await context.read<AuthProvider>().updateProfile(
            _fullnameController.text,
            birthdayStr,
            _selectedImage,
          );
      if (success && mounted) {
        await NotificationModal.show(
          context,
          title: 'Thành công',
          description: 'Cập nhật hồ sơ thành công',
          icon: Icons.check_circle_outline,
          accentColor: Colors.green,
          barrierDismissible: false,
        );
        if (mounted) {
          Navigator.pop(context);
        }
      } else if (mounted) {
        NotificationModal.show(
          context,
          title: 'Cập nhật thất bại',
          description: context.read<AuthProvider>().error ?? 'Đã có lỗi xảy ra',
          icon: Icons.error_outline,
          accentColor: Colors.red,
        );
      }
    }
  }

  Widget _buildAvatarPreview() {
    ImageProvider? imageProvider;

    if (_selectedImage != null) {
      imageProvider = FileImage(_selectedImage!);
    } else if (_existingAvatarUrl != null && _existingAvatarUrl!.isNotEmpty) {
      final fullUrl = _existingAvatarUrl!.startsWith('http')
          ? _existingAvatarUrl!
          : '${ApiConstants.baseUrl}${_existingAvatarUrl!}';
      imageProvider = CachedNetworkImageProvider(fullUrl);
    }

    return GestureDetector(
      onTap: _pickImage,
      child: Column(
        children: [
          Stack(
            children: [
              Container(
                width: 120,
                height: 120,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: const Color(0xFFE5E5EA), // iOS systemFill
                  image: imageProvider != null
                      ? DecorationImage(
                          image: imageProvider,
                          fit: BoxFit.cover,
                        )
                      : null,
                ),
                child: imageProvider == null
                    ? const Icon(
                        Icons.person,
                        size: 60,
                        color: Colors.grey,
                      )
                    : null,
              ),
              Positioned(
                bottom: 0,
                right: 0,
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Theme.of(context).primaryColor,
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.white, width: 3),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.1),
                        blurRadius: 4,
                        offset: const Offset(0, 2),
                      ),
                    ],
                  ),
                  child: const Icon(
                    Icons.camera_alt_rounded,
                    color: Colors.white,
                    size: 18,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            'Thay đổi ảnh',
            style: TextStyle(
              color: Theme.of(context).primaryColor,
              fontSize: 15,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final isLoading = context.watch<AuthProvider>().isLoading;

    return Scaffold(
      // Màu nền xám nhạt kiểu iOS Grouped
      backgroundColor: const Color(0xFFF2F2F7),
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: const EdgeInsets.only(
                top: 120, left: 0, right: 0, bottom: 34),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const SizedBox(height: 20),
                  _buildAvatarPreview(),
                  const SizedBox(height: 32),

                  // Grouped Inputs Section
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Container(
                      decoration: BoxDecoration(
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withOpacity(0.05),
                            blurRadius: 10,
                            offset: const Offset(0, 1),
                          ),
                        ],
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: IOSGroupedList(
                        margin: EdgeInsets.zero,
                        children: [
                          // Fullname Input
                          IOSListTile(
                            title: 'Họ tên',
                            padding: const EdgeInsets.symmetric(
                                horizontal: 20, vertical: 14),
                            trailing: SizedBox(
                              width: 200,
                              child: TextFormField(
                                controller: _fullnameController,
                                textAlign: TextAlign.right,
                                style: const TextStyle(
                                    fontSize: 16, color: Colors.black),
                                decoration: const InputDecoration.collapsed(
                                  hintText: 'Nhập họ tên',
                                  hintStyle: TextStyle(color: Colors.grey),
                                ),
                                validator: (v) =>
                                    v!.isEmpty ? 'Vui lòng nhập họ tên' : null,
                              ),
                            ),
                          ),

                          IOSListTile(
                            title: 'Ngày sinh',
                            padding: const EdgeInsets.symmetric(
                                horizontal: 20, vertical: 14),
                            trailing: Text(
                              _selectedBirthday != null
                                  ? DateFormat('dd/MM/yyyy')
                                      .format(_selectedBirthday!)
                                  : 'Chọn ngày',
                              style: TextStyle(
                                fontSize: 16,
                                color: _selectedBirthday != null
                                    ? Theme.of(context).primaryColor
                                    : Colors.grey,
                              ),
                            ),
                            onTap: _selectBirthday,
                          ),
                        ],
                      ),
                    ),
                  ),

                  const SizedBox(height: 12),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 32),
                    child: Text(
                      'Thông tin này sẽ được hiển thị trên hồ sơ cá nhân của bạn.',
                      style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 32),

                  // Update Button
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: ElevatedButton(
                      onPressed: isLoading ? null : _updateProfile,
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12), // iOS style rounded
                        ),
                        backgroundColor: Theme.of(context).primaryColor,
                        foregroundColor: Colors.white,
                      ),
                      child: isLoading
                          ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor: AlwaysStoppedAnimation(Colors.white),
                              ),
                            )
                          : const Text(
                              'Lưu thay đổi',
                              style: TextStyle(
                                fontSize: 17,
                                fontWeight: FontWeight.w600,
                                color: Colors.white,
                              ),
                            ),
                    ),
                  ),
                  const SizedBox(height: 50), // Bottom padding
                ],
              ),
            ),
          ),
          HeaderNavigationBar(
            title: 'Chỉnh sửa hồ sơ',
            isVisible: true,
            backgroundColor: const Color(0xFFF2F2F7), // Match background
            leading: HeaderNavigationBarBackButton(
              label: 'Hủy',
              onPressed: () => Navigator.pop(context),
            ),
            titleStyle: Theme.of(context).textTheme.titleMedium?.copyWith(
                  fontWeight: FontWeight.w600,
                  color: Colors.black,
                ),
          ),
        ],
      ),
    );
  }
}
