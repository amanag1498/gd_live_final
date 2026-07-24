import 'package:firebase_core/firebase_core.dart';
import 'package:flutter/foundation.dart'
    show TargetPlatform, defaultTargetPlatform, kIsWeb;

/// Firebase config aligned to the old `gd_live_flutter` project.
///
/// Web values are inferred from the same GD Live Firebase project because the
/// old app does not include a dedicated web Firebase config file.
class DefaultFirebaseOptions {
  static FirebaseOptions get currentPlatform {
    if (kIsWeb) {
      return web;
    }

    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return android;
      case TargetPlatform.iOS:
        return ios;
      default:
        throw UnsupportedError(
          'DefaultFirebaseOptions are not configured for this platform.',
        );
    }
  }

  static const FirebaseOptions web = FirebaseOptions(
    apiKey: 'AIzaSyAPB7e9WnmEEOHrO5iwX3bSDxr-rYjubaU',
    appId: '1:826349753111:android:fd08204e4385905582dd52',
    messagingSenderId: '826349753111',
    projectId: 'gdlive-da4e9',
    authDomain: 'gdlive-da4e9.firebaseapp.com',
    storageBucket: 'gdlive-da4e9.appspot.com',
  );

  static const FirebaseOptions android = FirebaseOptions(
    apiKey: 'AIzaSyAPB7e9WnmEEOHrO5iwX3bSDxr-rYjubaU',
    appId: '1:826349753111:android:fd08204e4385905582dd52',
    messagingSenderId: '826349753111',
    projectId: 'gdlive-da4e9',
    storageBucket: 'gdlive-da4e9.firebasestorage.app',
  );

  static const FirebaseOptions ios = FirebaseOptions(
    apiKey: 'AIzaSyD_qcodhD4FfSIUrLNNi-BcfG69DYSaeCc',
    appId: '1:826349753111:ios:df019ad6bef75ac482dd52',
    messagingSenderId: '826349753111',
    projectId: 'gdlive-da4e9',
    storageBucket: 'gdlive-da4e9.firebasestorage.app',
    iosClientId:
        '826349753111-d8dkhcmk8eo4c2jtov4pl92srn09uin2.apps.googleusercontent.com',
    iosBundleId: 'com.techybugs.gdlive',
  );
}
