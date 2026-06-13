import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../app/brand/brand.dart';
import '../../../app/utils/avatar_url.dart';
import '../../../app/widgets/haptics.dart';
import '../../../services/api_client.dart';
import '../../../services/app_settings_service.dart';
import '../controllers/profile_controller.dart';
import '../services/avatar_image_picker_service.dart';

class EditProfilePage extends StatefulWidget {
  const EditProfilePage({super.key});

  @override
  State<EditProfilePage> createState() => _EditProfilePageState();
}

class _EditProfilePageState extends State<EditProfilePage> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nameCtl;
  late final TextEditingController _stageCtl;
  late final TextEditingController _phoneCtl;
  late final TextEditingController _countryCtl;
  late final TextEditingController _cityCtl;
  late final TextEditingController _bioCtl;
  final _avatarPicker = AvatarImagePickerService();
  ProfileController get controller => Get.find<ProfileController>();
  String? _localAvatarPath;
  bool _isPickingAvatar = false;

  @override
  void initState() {
    super.initState();
    final profile = controller.profile.value;
    _nameCtl = TextEditingController(text: profile?.name ?? controller.currentUser?.name ?? '');
    _stageCtl = TextEditingController(text: profile?.hostProfile?.stageName ?? '');
    _phoneCtl = TextEditingController(text: profile?.hostProfile?.contactPhone ?? '');
    _countryCtl = TextEditingController(text: profile?.hostProfile?.country ?? '');
    _cityCtl = TextEditingController(text: profile?.hostProfile?.city ?? '');
    _bioCtl = TextEditingController(text: profile?.hostProfile?.bio ?? '');
  }

  @override
  void dispose() {
    _nameCtl.dispose();
    _stageCtl.dispose();
    _phoneCtl.dispose();
    _countryCtl.dispose();
    _cityCtl.dispose();
    _bioCtl.dispose();
    super.dispose();
  }

  Future<void> _pickAvatar() async {
    if (_isPickingAvatar || controller.isUploadingAvatar.value) return;
    Haptics.light();
    setState(() => _isPickingAvatar = true);
    try {
      final path = await _avatarPicker.pickAndOptimizeFromGallery();
      if (!mounted || path == null || path.isEmpty) return;
      setState(() => _localAvatarPath = path);
      final ok = await controller.uploadAvatar(path);
      if (!mounted) return;
      if (ok) {
        Haptics.success();
        setState(() => _localAvatarPath = null);
        Get.snackbar(
          'Profile updated',
          'Avatar updated successfully.',
          snackPosition: SnackPosition.BOTTOM,
        );
      } else {
        Haptics.error();
        Get.snackbar(
          'Avatar upload failed',
          controller.error.value ?? 'Please try again.',
          snackPosition: SnackPosition.BOTTOM,
        );
      }
    } on AvatarImagePickerException catch (e) {
      if (!mounted) return;
      Haptics.error();
      Get.snackbar(
        'Image not supported',
        e.message,
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (e) {
      if (!mounted) return;
      Haptics.error();
      Get.snackbar(
        'Avatar upload failed',
        e.toString().replaceFirst('Exception: ', ''),
        snackPosition: SnackPosition.BOTTOM,
      );
    } finally {
      if (mounted) {
        setState(() => _isPickingAvatar = false);
      }
    }
  }

  Future<void> _submit() async {
    Haptics.medium();
    if (!_formKey.currentState!.validate()) return;
    final ok = await controller.saveProfile(
      name: _nameCtl.text.trim(),
      stageName: _stageCtl.text.trim().isEmpty ? null : _stageCtl.text.trim(),
      contactPhone: _phoneCtl.text.trim().isEmpty ? null : _phoneCtl.text.trim(),
      country: _countryCtl.text.trim().isEmpty ? null : _countryCtl.text.trim(),
      city: _cityCtl.text.trim().isEmpty ? null : _cityCtl.text.trim(),
      bio: _bioCtl.text.trim().isEmpty ? null : _bioCtl.text.trim(),
    );
    if (!mounted) return;
    if (ok) {
      Haptics.success();
      Get.back<void>();
      Get.snackbar('Profile updated', 'Your profile changes were saved.', snackPosition: SnackPosition.BOTTOM);
    } else {
      Haptics.error();
      Get.snackbar('Update failed', controller.error.value ?? 'Please try again.', snackPosition: SnackPosition.BOTTOM);
    }
  }

  @override
  Widget build(BuildContext context) {
    final api = Get.find<ApiClient>();
    final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);
    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      appBar: AppBar(
        titleSpacing: 0,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Update Profile',
              style: TextStyle(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w900,
                fontSize: 20,
              ),
            ),
            Text(
              'Keep your identity, host details, and bio current',
              style: TextStyle(
                color: tokens.textSecondary,
                fontWeight: FontWeight.w600,
                fontSize: 12,
              ),
            ),
          ],
        ),
        toolbarHeight: 68,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: tokens.textPrimary),
      ),
      body: Obx(() {
        final profile = controller.profile.value;
        final isHost = profile?.isHost == true && profile?.hostProfile != null;
        final remoteAvatarUrl = resolveAvatarUrl(api, profile?.avatarUrl);
        final isAvatarBusy = _isPickingAvatar || controller.isUploadingAvatar.value;
        final ImageProvider<Object>? avatarImage =
            _localAvatarPath != null
            ? FileImage(File(_localAvatarPath!))
            : (remoteAvatarUrl != null ? NetworkImage(remoteAvatarUrl) : null);
        return Form(
          key: _formKey,
          child: ListView(
            physics: const BouncingScrollPhysics(
              parent: AlwaysScrollableScrollPhysics(),
            ),
            padding: const EdgeInsets.fromLTRB(18, 12, 18, 24),
            children: [
              Container(
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Colors.white.withOpacity(.96),
                      const Color(0xFFF4FBF5).withOpacity(.98),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(28),
                  border: Border.all(color: tokens.borderColor.withOpacity(.35)),
                  boxShadow: [
                    BoxShadow(
                      color: tokens.primaryButtonGradient.first.withOpacity(.06),
                      blurRadius: 18,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    GestureDetector(
                      onTap: isAvatarBusy ? null : _pickAvatar,
                      child: Stack(
                        children: [
                          Container(
                            width: 88,
                            height: 88,
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 3),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withOpacity(.08),
                                  blurRadius: 16,
                                  offset: const Offset(0, 8),
                                ),
                              ],
                            ),
                            child: ClipOval(
                              child: avatarImage != null
                                  ? Image(
                                      image: avatarImage,
                                      fit: BoxFit.cover,
                                      errorBuilder: (_, __, ___) => _AvatarFallback(
                                        name: profile?.name ?? 'U',
                                        tokens: tokens,
                                      ),
                                    )
                                  : _AvatarFallback(
                                      name: profile?.name ?? 'U',
                                      tokens: tokens,
                                    ),
                            ),
                          ),
                          Positioned(
                            right: 2,
                            bottom: 2,
                            child: Container(
                              width: 28,
                              height: 28,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: tokens.primaryButtonGradient,
                                ),
                                shape: BoxShape.circle,
                                border: Border.all(color: Colors.white, width: 2),
                              ),
                              child: isAvatarBusy
                                  ? const Padding(
                                      padding: EdgeInsets.all(6),
                                      child: CircularProgressIndicator(
                                        strokeWidth: 2,
                                        color: Colors.white,
                                      ),
                                    )
                                  : const Icon(
                                      Icons.photo_library_rounded,
                                      size: 15,
                                      color: Colors.white,
                                    ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            profile?.name ?? 'Profile',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                              color: tokens.textPrimary,
                              fontSize: 18,
                              fontWeight: FontWeight.w900,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            isHost
                                ? 'Host profile active'
                                : 'Member profile active',
                            style: TextStyle(
                              color: tokens.textSecondary,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 12),
                          TextButton.icon(
                            style: TextButton.styleFrom(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 12,
                                vertical: 10,
                              ),
                              foregroundColor: tokens.primaryButtonGradient.first,
                              backgroundColor: tokens.primaryButtonGradient.first.withOpacity(.10),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16),
                              ),
                            ),
                            onPressed: isAvatarBusy ? null : _pickAvatar,
                            icon: isAvatarBusy
                                ? const SizedBox(
                                    width: 16,
                                    height: 16,
                                    child: CircularProgressIndicator(strokeWidth: 2),
                                  )
                                : const Icon(Icons.photo_library_rounded, size: 18),
                            label: Text(
                              controller.isUploadingAvatar.value
                                  ? 'Uploading'
                                  : _isPickingAvatar
                                      ? 'Preparing'
                                      : 'Change photo',
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 18),
              _SectionTitle(tokens: tokens, title: 'Basic details', subtitle: 'Name and public identity'),
              const SizedBox(height: 12),
              _GdInputField(
                controller: _nameCtl,
                label: 'Name',
                hint: 'Enter your name',
                helperText: 'This is shown on your public profile.',
                textInputAction: TextInputAction.next,
                tokens: tokens,
                validator: (value) => (value == null || value.trim().isEmpty) ? 'Name is required.' : null,
              ),
              if (isHost) ...[
                const SizedBox(height: 22),
                _SectionTitle(tokens: tokens, title: 'Host details', subtitle: 'Stage profile and contact information'),
                const SizedBox(height: 12),
                _GdInputField(
                  controller: _stageCtl,
                  label: 'Stage name',
                  hint: 'Enter your stage name',
                  helperText: 'Visible on host pages and live rooms.',
                  textInputAction: TextInputAction.next,
                  tokens: tokens,
                ),
                const SizedBox(height: 12),
                _GdInputField(
                  controller: _phoneCtl,
                  label: 'Phone',
                  hint: 'Phone number',
                  helperText: 'Used for host contact and verification.',
                  keyboardType: TextInputType.phone,
                  textInputAction: TextInputAction.next,
                  tokens: tokens,
                ),
                const SizedBox(height: 12),
                _GdInputField(
                  controller: _countryCtl,
                  label: 'Country',
                  hint: 'Country',
                  helperText: 'Shown with your host profile.',
                  textInputAction: TextInputAction.next,
                  tokens: tokens,
                ),
                const SizedBox(height: 12),
                _GdInputField(
                  controller: _cityCtl,
                  label: 'City',
                  hint: 'City',
                  helperText: 'Shown with your host profile.',
                  textInputAction: TextInputAction.next,
                  tokens: tokens,
                ),
                const SizedBox(height: 12),
                _GdInputField(
                  controller: _bioCtl,
                  label: 'Bio / About',
                  hint: 'Write a short bio',
                  helperText: 'Keep it short and useful. This appears on your profile.',
                  minLines: 4,
                  maxLines: 6,
                  tokens: tokens,
                ),
              ],
              const SizedBox(height: 18),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  style: FilledButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 15),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                    ),
                  ),
                  onPressed: controller.isSaving.value ? null : _submit,
                  child: controller.isSaving.value
                      ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                      : const Text(
                          'Save changes',
                          style: TextStyle(fontWeight: FontWeight.w800),
                        ),
                ),
              ),
              if (controller.error.value != null) ...[
                const SizedBox(height: 12),
                Text(
                  controller.error.value!,
                  style: const TextStyle(color: Colors.redAccent),
                ),
              ],
            ],
          ),
        );
      }),
    );
  }
}

class _AvatarFallback extends StatelessWidget {
  const _AvatarFallback({required this.name, required this.tokens});

  final String name;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    final initial = name.trim().isEmpty ? 'U' : name.trim()[0].toUpperCase();
    return Container(
      color: Colors.white,
      alignment: Alignment.center,
      child: Text(
        initial,
        style: TextStyle(
          color: tokens.primaryButtonGradient.first,
          fontSize: 30,
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  const _SectionTitle({
    required this.tokens,
    required this.title,
    required this.subtitle,
  });

  final BrandTokens tokens;
  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: TextStyle(
            color: tokens.textPrimary,
            fontSize: 17,
            fontWeight: FontWeight.w900,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          subtitle,
          style: TextStyle(
            color: tokens.textSecondary,
            fontSize: 12.5,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}

class _GdInputField extends StatelessWidget {
  const _GdInputField({
    required this.controller,
    required this.label,
    required this.hint,
    required this.tokens,
    this.helperText,
    this.textInputAction,
    this.keyboardType,
    this.minLines = 1,
    this.maxLines = 1,
    this.validator,
  });

  final TextEditingController controller;
  final String label;
  final String hint;
  final BrandTokens tokens;
  final String? helperText;
  final TextInputAction? textInputAction;
  final TextInputType? keyboardType;
  final int minLines;
  final int maxLines;
  final String? Function(String?)? validator;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextFormField(
          controller: controller,
          textInputAction: textInputAction,
          keyboardType: keyboardType,
          minLines: minLines,
          maxLines: maxLines,
          style: TextStyle(
            color: tokens.textPrimary,
            fontSize: 15,
            fontWeight: FontWeight.w700,
          ),
          cursorColor: tokens.primaryButtonGradient.first,
          validator: validator,
          decoration: InputDecoration(
            labelText: label,
            hintText: hint,
            filled: true,
            fillColor: Colors.white,
            labelStyle: TextStyle(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
            floatingLabelStyle: TextStyle(
              color: tokens.primaryButtonGradient.first,
              fontWeight: FontWeight.w800,
            ),
            hintStyle: TextStyle(
              color: tokens.textSecondary.withOpacity(.72),
              fontWeight: FontWeight.w500,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(18),
              borderSide: BorderSide(color: tokens.borderColor.withOpacity(.65)),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(18),
              borderSide: BorderSide(color: tokens.borderColor.withOpacity(.65)),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(18),
              borderSide: BorderSide(color: tokens.primaryButtonGradient.first, width: 1.6),
            ),
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          ),
        ),
        if (helperText != null) ...[
          const SizedBox(height: 6),
          Padding(
            padding: const EdgeInsets.only(left: 4),
            child: Text(
              helperText!,
              style: TextStyle(
                color: tokens.textSecondary,
                fontSize: 12.5,
                height: 1.35,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ],
    );
  }
}
