# GD Live App Store release handoff

Prepared for the first App Store submission of GD Live.

## Seller and payment account

- Membership type: `Individual`
- Intended Account Holder and public seller: `Deepak Sharma`
- Apple Account login: `amanagarwal1498@icloud.com`
- App Store Connect currently shows the individual legal entity as `Aman Agarwal`, while the Apple Account display name is `DEEPAK SHARMA`.
- The legal-entity name is locked in App Store Connect. Apple must approve the membership/legal-name correction before the Paid Apps Agreement can be completed under Deepak Sharma.
- After Apple approves the correction, Paid Apps Agreement, tax information, and App Store proceeds must be completed under Deepak Sharma's individual membership.
- No organization conversion is required for paid apps or Apple In-App Purchases.

The Apple Account email and display name do not determine the seller. The verified membership legal entity determines the seller and contracting party. Do not accept paid agreements or submit tax information under the wrong individual.

## Build identity

- App name: `GD Live`
- Bundle ID: `com.techybugs.gdlive`
- Version: `1.0.0`
- Build: `65`
- Apple team: `9BQZB27JWV`
- Minimum iOS version: `15.0`
- Signed IPA: `liveapp flutter/build/ios/ipa/app-store-1.0.0+65/GD Live.ipa`
- SHA-256: `24088bcd8c4738e9e4a8786b3cfdedbd945b25800c57e8222706c34c227235aa`
- Privacy policy: `https://api.gdlive.in/privacy-policy`
- Account deletion: `https://api.gdlive.in/account-deletion`
- Support URL: `https://api.gdlive.in`
- Support email: `admin@gdlive.in`

The IPA was exported with Apple Distribution signing for team `9BQZB27JWV`. Its embedded App Store provisioning profile and signature both identify `com.techybugs.gdlive`.

## Suggested App Store metadata

- Name: `GD Live`
- Subtitle: `Live Video & Creator Rooms`
- Promotional text: `Meet creators, join interactive live rooms, chat with the community, and support the hosts you enjoy on GD Live.`
- Keywords: `live,video,creator,chat,stream,rooms,community,host,gifts,social`
- Category: `Social Networking`
- Secondary category: `Entertainment`

### Description

GD Live brings creators and audiences together in interactive live video rooms.

Discover live hosts, join conversations, chat with the community, and follow the creators you enjoy. Eligible creators can apply to host their own rooms and build their audience with real-time video and audience interaction.

Features include:

- Live creator video rooms
- Real-time community chat and reactions
- Voice and video calling experiences
- Creator profiles and discovery
- Virtual gifts purchased with GD Coins
- Reporting, blocking, and account safety controls
- Sign in with Apple and Google
- In-app account deletion

GD Coins are optional consumable digital items purchased through Apple In-App Purchase. Coins do not represent cash, cannot be withdrawn by ordinary users, and are used only for eligible digital experiences inside GD Live.

## Apple In-App Purchases

Create each item as a **Consumable** under this app. Use the closest supported App Store price point for each intended India price and let App Store Connect calculate other storefronts. The backend records Apple's authoritative localized transaction price.

| Reference name | Product ID | Coins | Intended India price |
| --- | --- | ---: | ---: |
| GD Coins 1000 | `com.techybugs.gdlive.coins.1000` | 1,000 | INR 100 |
| GD Coins 3000 | `com.techybugs.gdlive.coins.3000` | 3,000 | INR 300 |
| GD Coins 6000 | `com.techybugs.gdlive.coins.6000` | 6,000 | INR 600 |
| GD Coins 10000 | `com.techybugs.gdlive.coins.10000` | 10,000 | INR 1,000 |
| GD Coins 20000 | `com.techybugs.gdlive.coins.20000` | 20,000 | INR 2,000 |

Suggested localization for each product:

- Display name: use the reference name from the table.
- Description: `A consumable pack of {coin count} GD Coins for virtual gifts and eligible digital experiences in GD Live.`
- Review screenshot: show the Wallet recharge sheet with the selected pack and Apple price visible.

For the first submission, attach all five IAP products to version `1.0.0` before sending it to review.

## App Store server configuration

Configure App Store Server Notifications V2 for production:

- Production URL: `https://api.gdlive.in/api/payments/apple/notifications`
- Version: `V2`

Before releasing the build, deploy the backend changes and configure these production values without committing the private key:

- `APPLE_IAP_ENABLED=true`
- `APPLE_IAP_ENVIRONMENT=production`
- `APPLE_IAP_BUNDLE_ID=com.techybugs.gdlive`
- `APPLE_IAP_ISSUER_ID=<App Store Connect issuer ID>`
- `APPLE_IAP_KEY_ID=<In-App Purchase key ID>`
- `APPLE_IAP_PRIVATE_KEY_PATH=<absolute server path to the .p8 key>`

Store the `.p8` key outside the repository with access limited to the application service account. Run the production migrations and verify the authenticated iOS wallet summary says Apple In-App Purchase is ready before submitting.

## App privacy answers to verify in App Store Connect

The following is a conservative code-based disclosure map. Confirm it against production retention and every enabled third-party service while completing the questionnaire.

- Contact Info: email address; name.
- Identifiers: user ID, Firebase identifier, device/push token, and advertising identifier when ATT permission is granted.
- Purchases: purchase history and Apple transaction identifiers.
- User Content: profile photos, live audio/video, chat/messages, reports, and support content.
- Usage Data: product interaction and advertising/marketing attribution events.
- Diagnostics: operational logs and connection diagnostics, if retained in production.
- Coarse Location: profile city/country supplied by the user, if Apple classifies the collected profile value as location data.

Meta attribution is present and requests App Tracking Transparency authorization. Mark data used for tracking only where it is actually linked across third-party apps or sites; the answer must reflect the production Meta configuration, not merely the permission prompt.

## Review notes

Use and adapt this text in App Review Information:

> GD Live is a live creator community. Users sign in with Apple or Google, browse creator profiles, and join live rooms. Optional GD Coins are consumable digital items sold only through Apple In-App Purchase on iOS. Open Wallet from Settings or the main wallet entry, select a coin pack, and confirm the StoreKit purchase. Coins can be used for virtual gifts and eligible digital experiences inside the app. The app includes reporting and blocking controls. Permanent account deletion is available at Settings > Account Deletion. The iOS release has cash/UPI checkout disabled and does not link to an external payment method for digital goods.

Provide Apple with a stable review account that can enter a live room and reach Wallet without requiring a one-time code. Put the credentials only in App Store Connect review notes, never in this repository. If a live host is not normally available around the clock, provide a deterministic test room or exact review window and timezone.

## Portal checklist

- Confirm the app record uses bundle ID `com.techybugs.gdlive` and SKU chosen by the account holder.
- Accept the Paid Apps agreement and complete tax and banking information.
- Obtain Apple approval to change the individual membership legal entity from Aman Agarwal to Deepak Sharma, or explicitly decide to publish under Aman Agarwal instead.
- After the legal identity is correct, complete the Paid Apps Agreement, individual tax forms, and banking information in App Store Connect Business.
- Upload required iPhone and iPad screenshots because the binary supports both device families.
- Complete age rating, content rights, encryption/export compliance, and advertising identifier questions truthfully.
- For age rating, disclose user-generated content, messaging/chat, live audio/video, and moderation controls. iOS gambling-style room games are disabled in the current production feature configuration.
- Add the privacy policy URL and complete App Privacy disclosures.
- Create and localize all five consumable IAP products, add review screenshots, and attach them to version `1.0.0`.
- Upload build `65`, wait for processing, select it for version `1.0.0`, and resolve any processing warnings.
- Add the review account and review notes, then submit the app and its first IAP products together.

## Release gates

Do not submit for review until all of these are true:

1. The account-deletion and Apple IAP backend changes are deployed to production.
2. Production Apple IAP credentials and the V2 notification URL are configured and verified.
3. The five App Store products exactly match the product IDs returned by the production recharge API.
4. Review credentials, screenshots, privacy answers, age-rating answers, agreements, tax, and banking are complete.
5. Build `65` has uploaded and finished processing without blocking validation errors.
