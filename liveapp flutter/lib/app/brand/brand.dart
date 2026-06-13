import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

const kGdLivePrimary = Color(0xFF06B430);
const kGdLiveBg = Color(0xFFF2FBF4);
const kGdLiveGold = Color(0xFFFFCC00);
const kGdLiveBrandKey = 'midnight';
const kDefaultBrandKey = kGdLiveBrandKey;

const _rSm = 12.0;
const _rMd = 16.0;
const _rLg = 22.0;
const _rXl = 28.0;

@immutable
class BrandTokens {
  const BrandTokens({
    required this.backgroundGradient,
    required this.cardGradient,
    required this.glassColor,
    required this.borderColor,
    required this.glowColor,
    required this.primaryButtonGradient,
    required this.chipColor,
    required this.textPrimary,
    required this.textSecondary,
    required this.dangerColor,
    required this.successColor,
  });

  final List<Color> backgroundGradient;
  final List<Color> cardGradient;
  final Color glassColor;
  final Color borderColor;
  final Color glowColor;
  final List<Color> primaryButtonGradient;
  final Color chipColor;
  final Color textPrimary;
  final Color textSecondary;
  final Color dangerColor;
  final Color successColor;
}

const gdLiveBrandTokens = BrandTokens(
  backgroundGradient: [Color(0xFFFFFFFF), Color(0xFFA8E6A1)],
  cardGradient: [Color(0xFFE9FCE5), Color(0xFFCFF5D1)],
  glassColor: Color(0xF3F6FFF2),
  borderColor: Color(0x5E86D79A),
  glowColor: Color(0x6643E97B),
  primaryButtonGradient: [Color(0xFF06B430), Color(0xFF43E97B)],
  chipColor: Color(0xFFDDF6E1),
  textPrimary: Color(0xFF15351C),
  textSecondary: Color(0xFF587561),
  dangerColor: Color(0xFFE35D6A),
  successColor: Color(0xFF2F9E44),
);

const auroraBrandTokens = BrandTokens(
  backgroundGradient: [Color(0xFF08111F), Color(0xFF0C2A38)],
  cardGradient: [Color(0xFF0F2031), Color(0xFF11354A)],
  glassColor: Color(0xFF13283B),
  borderColor: Color(0x4D43B3A7),
  glowColor: Color(0x6641E0C0),
  primaryButtonGradient: [Color(0xFF29D6B7), Color(0xFF1A9EE2)],
  chipColor: Color(0xFF163348),
  textPrimary: Colors.white,
  textSecondary: Color(0xFFC3E9E4),
  dangerColor: Color(0xFFFF7A90),
  successColor: Color(0xFF4FE0B4),
);

const Map<String, BrandTokens> _brandTokenRegistry = {
  kGdLiveBrandKey: gdLiveBrandTokens,
  'aurora': auroraBrandTokens,
};

const Map<String, String> _brandAliases = {
  'gd_live': kGdLiveBrandKey,
  'gdlive': kGdLiveBrandKey,
  'default': kGdLiveBrandKey,
  'purple': kGdLiveBrandKey,
  'night': kGdLiveBrandKey,
  'teal': 'aurora',
};

BrandTokens getBrandTokens([String? brandKey]) {
  return _brandTokenRegistry[normalizeBrandVariant(brandKey)] ??
      gdLiveBrandTokens;
}

String normalizeBrandVariant([String? brandKey]) {
  final normalized = brandKey?.trim().toLowerCase();
  if (normalized == null || normalized.isEmpty) {
    return kDefaultBrandKey;
  }
  final alias = _brandAliases[normalized] ?? normalized;
  if (_brandTokenRegistry.containsKey(alias)) {
    return alias;
  }
  return kDefaultBrandKey;
}

List<String> get availableBrandKeys =>
    _brandTokenRegistry.keys.toList(growable: false);

ThemeData gdLiveLightTheme([String? brandKey]) {
  final tokens = getBrandTokens(brandKey);
  final scheme = ColorScheme.fromSeed(
    seedColor: tokens.primaryButtonGradient.first,
    primary: tokens.primaryButtonGradient.first,
    brightness: Brightness.light,
    background: kGdLiveBg,
  );

  return _baseTheme(
    scheme,
    tokens,
    isDark: false,
  ).copyWith(scaffoldBackgroundColor: kGdLiveBg);
}

ThemeData gdLiveDarkTheme([String? brandKey]) {
  final tokens = getBrandTokens(brandKey);
  final scheme = ColorScheme.fromSeed(
    seedColor: tokens.primaryButtonGradient.first,
    brightness: Brightness.dark,
  );

  return _baseTheme(
    scheme,
    tokens,
    isDark: true,
  ).copyWith(
    scaffoldBackgroundColor: tokens.backgroundGradient.first,
  );
}

ThemeData _baseTheme(
  ColorScheme colorScheme,
  BrandTokens tokens, {
  required bool isDark,
}) {
  final baseText =
      isDark ? Typography.whiteMountainView : Typography.blackMountainView;
  final baseTypography = GoogleFonts.plusJakartaSansTextTheme(baseText);
  final textTheme = baseTypography.copyWith(
    displayLarge: baseTypography.displayLarge?.copyWith(fontWeight: FontWeight.w800),
    displayMedium: baseTypography.displayMedium?.copyWith(fontWeight: FontWeight.w800),
    displaySmall: baseTypography.displaySmall?.copyWith(fontWeight: FontWeight.w800),
    headlineLarge: baseTypography.headlineLarge?.copyWith(fontWeight: FontWeight.w800),
    headlineMedium: baseTypography.headlineMedium?.copyWith(fontWeight: FontWeight.w800),
    headlineSmall: baseTypography.headlineSmall?.copyWith(fontWeight: FontWeight.w800),
    titleLarge: baseTypography.titleLarge?.copyWith(fontWeight: FontWeight.w700),
    titleMedium: baseTypography.titleMedium?.copyWith(fontWeight: FontWeight.w700),
    titleSmall: baseTypography.titleSmall?.copyWith(fontWeight: FontWeight.w700),
    labelLarge: baseTypography.labelLarge?.copyWith(
      fontWeight: FontWeight.w700,
      letterSpacing: .2,
    ),
    labelMedium: baseTypography.labelMedium?.copyWith(fontWeight: FontWeight.w700),
    labelSmall: baseTypography.labelSmall?.copyWith(fontWeight: FontWeight.w700),
    bodyLarge: baseTypography.bodyLarge?.copyWith(height: 1.25),
    bodyMedium: baseTypography.bodyMedium?.copyWith(height: 1.25),
    bodySmall: baseTypography.bodySmall?.copyWith(height: 1.25),
  );

  return ThemeData(
    useMaterial3: true,
    colorScheme: colorScheme,
    visualDensity: VisualDensity.adaptivePlatformDensity,
    fontFamily: GoogleFonts.plusJakartaSans().fontFamily,
    textTheme: textTheme,
    appBarTheme: AppBarTheme(
      backgroundColor: Colors.transparent,
      elevation: 0,
      scrolledUnderElevation: 0,
      surfaceTintColor: Colors.transparent,
      centerTitle: false,
      titleTextStyle: textTheme.titleLarge?.copyWith(
        color: isDark ? Colors.white : const Color(0xFF201339),
        fontWeight: FontWeight.w800,
      ),
      iconTheme: IconThemeData(
        color: isDark ? Colors.white : const Color(0xFF2B164D),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: isDark ? colorScheme.surface.withOpacity(.95) : Colors.white,
      hintStyle: TextStyle(
        color: isDark ? Colors.white70 : const Color(0xFF6B5A8F),
      ),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(_rMd),
        borderSide: BorderSide(
          color: isDark ? tokens.borderColor : const Color(0xFFE6DFF4),
        ),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(_rMd),
        borderSide: BorderSide(
          color: isDark ? tokens.borderColor : const Color(0xFFE6DFF4),
        ),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(_rMd),
        borderSide: BorderSide(color: colorScheme.primary, width: 1.6),
      ),
    ),
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        backgroundColor: colorScheme.primary,
        foregroundColor: Colors.white,
        elevation: isDark ? 1.5 : 0,
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(_rLg),
        ),
        textStyle: textTheme.labelLarge,
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor:
            isDark
                ? colorScheme.secondaryContainer
                : colorScheme.primaryContainer,
        foregroundColor: isDark ? Colors.white : colorScheme.onPrimaryContainer,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(_rLg),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        textStyle: textTheme.labelLarge,
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: colorScheme.primary,
        side: BorderSide(color: colorScheme.primary.withOpacity(.5)),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(_rLg),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 14),
        textStyle: textTheme.labelLarge,
      ),
    ),
    chipTheme: ChipThemeData(
      backgroundColor:
          isDark ? tokens.chipColor : const Color(0xFFEDE5FA),
      labelStyle: textTheme.labelLarge!.copyWith(
        color: isDark ? Colors.white : const Color(0xFF3E256D),
      ),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(_rSm)),
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
      side: BorderSide(
        color: isDark ? Colors.white12 : const Color(0xFFE3D9F6),
      ),
      iconTheme: IconThemeData(
        color:
            isDark ? Colors.white : tokens.primaryButtonGradient.first,
      ),
    ),
    navigationBarTheme: NavigationBarThemeData(
      backgroundColor: (isDark ? const Color(0xFF151026) : Colors.white)
          .withOpacity(0.72),
      indicatorColor: tokens.primaryButtonGradient.first.withOpacity(.15),
      labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
      iconTheme: WidgetStateProperty.resolveWith((states) {
        final selected = states.contains(WidgetState.selected);
        return IconThemeData(
          color:
              selected
                  ? tokens.primaryButtonGradient.first
                  : (isDark ? Colors.white70 : const Color(0xFF5F4B8E)),
          size: selected ? 26 : 24,
        );
      }),
      labelTextStyle: WidgetStateProperty.all(
        textTheme.labelMedium?.copyWith(
          color: isDark ? Colors.white : const Color(0xFF3D276B),
          fontWeight: FontWeight.w700,
        ),
      ),
      height: 68,
    ),
    bottomSheetTheme: BottomSheetThemeData(
      backgroundColor: (isDark ? colorScheme.surface : Colors.white).withOpacity(.92),
      modalBackgroundColor:
          (isDark ? colorScheme.surface : Colors.white).withOpacity(.96),
      elevation: 0,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(_rXl)),
      ),
      showDragHandle: true,
      dragHandleColor: isDark ? Colors.white30 : const Color(0xFFBCA9E7),
    ),
    snackBarTheme: SnackBarThemeData(
      behavior: SnackBarBehavior.floating,
      backgroundColor:
          isDark ? const Color(0xFF1D1631).withOpacity(.96) : Colors.white,
      contentTextStyle: textTheme.bodyMedium?.copyWith(
        color: isDark ? Colors.white : const Color(0xFF2B184D),
        fontWeight: FontWeight.w600,
      ),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(_rMd)),
      actionTextColor: tokens.primaryButtonGradient.first,
      elevation: isDark ? 1.5 : 0,
    ),
    dividerTheme: DividerThemeData(
      color: isDark ? Colors.white12 : const Color(0xFFE8E0F7),
      thickness: 1,
      space: 24,
    ),
    sliderTheme: SliderThemeData(
      activeTrackColor: colorScheme.primary,
      inactiveTrackColor: colorScheme.primary.withOpacity(.25),
      thumbColor: colorScheme.primary,
      overlayColor: colorScheme.primary.withOpacity(.12),
      trackHeight: 4,
      valueIndicatorColor: colorScheme.primary,
      valueIndicatorTextStyle: textTheme.labelSmall?.copyWith(
        color: Colors.white,
      ),
    ),
    switchTheme: SwitchThemeData(
      thumbColor: WidgetStateProperty.resolveWith(
        (s) =>
            s.contains(WidgetState.selected)
                ? colorScheme.primary
                : (isDark ? Colors.white70 : Colors.white),
      ),
      trackColor: WidgetStateProperty.resolveWith(
        (s) =>
            s.contains(WidgetState.selected)
                ? colorScheme.primary.withOpacity(.45)
                : (isDark ? Colors.white24 : const Color(0xFFE1D7F6)),
      ),
    ),
    progressIndicatorTheme: ProgressIndicatorThemeData(
      color: colorScheme.primary,
      linearTrackColor: colorScheme.primary.withOpacity(.2),
      circularTrackColor: colorScheme.primary.withOpacity(.18),
    ),
  );
}
