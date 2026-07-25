# Meta App Events release configuration

The SDK stays disabled unless the release is built with `--dart-define=META_APP_EVENTS_ENABLED=true`.

1. In Meta for Developers, add Android package `com.techybugs.gdlive` and iOS bundle ID `com.techybugs.gdlive`, then link the Meta app to the production Business Portfolio and ad account.
2. Copy `android/meta.properties.example` to `android/meta.properties` and set the Meta App ID and Client Token.
3. Copy `ios/Flutter/Meta.xcconfig.example` to `ios/Flutter/Meta.xcconfig` and set the same values.
4. Build the release with `--dart-define=META_APP_EVENTS_ENABLED=true`.
5. Verify app launch, registration, login, ATT consent, and a confirmed Razorpay recharge in Meta App Ads Helper and `/admin/meta-app-events`.

The Laravel environment can also expose non-secret integration health in the admin Setup & Health tab:

```dotenv
META_APP_ID=
META_CLIENT_TOKEN=
META_AD_ACCOUNT_ID=
META_BUSINESS_ID=
```

Do not put the Meta App Secret in any mobile configuration file. The client token is expected by the native SDK; the app secret stays in Meta/server-only tooling.
