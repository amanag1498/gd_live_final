class AppUrls {
  static const String apiHost = String.fromEnvironment(
    'APP_API_HOST',
    defaultValue: 'api.gdlive.in',
  );

  static const String socketHost = String.fromEnvironment(
    'APP_SOCKET_HOST',
    defaultValue: '31.97.233.109',
  );

  static const int apiPort = int.fromEnvironment(
    'APP_API_PORT',
    defaultValue: 443,
  );

  static const int wsPort = int.fromEnvironment(
    'APP_WS_PORT',
    defaultValue: 4001,
  );

  static const String apiScheme = String.fromEnvironment(
    'APP_API_SCHEME',
    defaultValue: 'https',
  );

  static const String socketScheme = String.fromEnvironment(
    'APP_SOCKET_SCHEME',
    defaultValue: 'ws',
  );

  static String get apiOrigin => _buildOrigin(
        apiScheme,
        apiHost,
        apiPort,
        omitDefaultPort: true,
      );

  static String get socketOrigin => _buildSocketOrigin(
        socketScheme,
        socketHost,
        wsPort,
      );

  static String get websiteOrigin => apiOrigin;

  static String get apiBase => '$apiOrigin/api';

  static String get wsPresence => '$socketOrigin/presence';
  static String get wsRooms => '$socketOrigin/rooms';
  static String get wsCalls => '$socketOrigin/calls';
  static String get wsGames => '$socketOrigin/games';

  static String get privacyPolicyUrl => '$websiteOrigin/privacy-policy';
  static String get termsOfServiceUrl => '$websiteOrigin/terms-of-service';
  static String get accountDeletionUrl => '$websiteOrigin/account-deletion';
  static String get supportUrl => websiteOrigin;

  static const String supportEmail = 'admin@gdlive.in';

  static String get deactivateAccountMailto =>
      'mailto:$supportEmail?subject=${Uri.encodeComponent('GD Live account deactivation request')}';

  static String _buildSocketOrigin(
    String scheme,
    String host,
    int port,
  ) {
    final normalizedScheme = scheme
        .trim()
        .toLowerCase()
        .replaceAll('://', '');

    final normalizedHost = host.trim();

    // socket_io_client 2.x uses Uri.port directly. If the URL omits the port,
    // Uri.port becomes 0 and the transport ends up dialing :0.
    final safePort = port <= 0
        ? ((normalizedScheme == 'ws' || normalizedScheme == 'http') ? 80 : 443)
        : port;

    return '$normalizedScheme://$normalizedHost:$safePort';
  }

  static String _buildOrigin(
    String scheme,
    String host,
    int port, {
    bool omitDefaultPort = true,
  }) {
    final normalizedScheme = scheme
        .trim()
        .toLowerCase()
        .replaceAll('://', '');

    final normalizedHost = host.trim();

    final safePort = port <= 0 ? 443 : port;

    final shouldOmitPort =
        omitDefaultPort &&
        ((normalizedScheme == 'https' && safePort == 443) ||
            (normalizedScheme == 'wss' && safePort == 443) ||
            (normalizedScheme == 'http' && safePort == 80) ||
            (normalizedScheme == 'ws' && safePort == 80));

    return shouldOmitPort
        ? '$normalizedScheme://$normalizedHost'
        : '$normalizedScheme://$normalizedHost:$safePort';
  }
}
