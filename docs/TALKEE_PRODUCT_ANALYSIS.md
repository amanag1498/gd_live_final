# Talkee Product Analysis

## Scope and Method

This document is a product analysis of the Talkee app based on the current codebase, not on marketing copy or assumed requirements.

Repositories analyzed:

- `liveapp flutter`
- `liveapp laravel`
- `live-presence-ws nodejs`
- sibling comparison repo for gap analysis: `../gd live`

Evidence used:

- Flutter screen/view/controller inventory
- Laravel API routes, web routes, models, services, admin/agency flows
- Node websocket/presence server behavior

Interpretation rules used in this document:

- `Confirmed`: directly implemented or clearly enforced in code
- `Inferred`: strongly suggested by route/model/service structure, but not always fully exposed in one place
- `Not found`: no clear implementation was found in the current Talkee codebase

Talkee is not a minimal live streaming app. It is a role-aware social live platform with:

- public audio and video live rooms
- subscription-gated live-room access for viewers
- host applications and host-agency enrollment
- private audio/video calls with coin billing
- gifts, entry effects, profile frames, themes, levels, and rankings
- PK battles between live video rooms
- separate agency treasury/wallet accounting
- admin and agency reporting, payout, moderation, and audit workflows
- live-room games (Teen Patti and Greedy) attached to room experience

---

## 1. User Roles

### Viewer / User

Status:

- Confirmed as default role assigned on account creation
- Every new user gets a wallet automatically

Permissions:

- sign up / log in
- edit profile
- follow hosts
- purchase coins
- buy subscriptions
- buy themes, profile frames, entry packs where eligible
- join live rooms if subscription access is active
- join video rooms as viewer
- join audio rooms as listener
- send gifts in rooms
- request speaker seat
- join private calls as caller
- play room games if globally enabled and individually unlocked
- create host or agency applications
- file user reports
- request unblock from hosts

Responsibilities:

- maintain wallet balance for paid actions
- follow room and moderation rules
- hold an active subscription to join live rooms

Earning opportunities:

- none as a normal viewer in the current wallet model
- viewers can spend coins, rank on user leaderboards, and level up by spend

Restrictions:

- cannot create/manage rooms unless host/admin
- cannot moderate rooms unless host/admin
- cannot apply for agency if already host or agency
- cannot apply for host if already host or agency
- cannot join live rooms without active subscription, except owner/admin cases

### Host

Status:

- Confirmed as Laravel role plus `hosts` profile record
- may optionally belong to an agency

Permissions:

- create/start/schedule/end live rooms
- run audio or video rooms
- manage seat requests and speakers
- start and participate in PK battles in video rooms
- receive gifts in live rooms
- receive private audio/video calls
- block, kick, mute, and review unblock requests for their room/community
- toggle host live/call availability
- enroll into an agency
- access host call reports and scheduled live features

Responsibilities:

- manage room quality and moderation
- maintain host profile metadata
- manage speaking participants
- maintain call availability and rates

Earning opportunities:

- live-room gift earnings
- private audio call earnings
- private video call earnings
- agency payout reports if attached to an agency

Restrictions:

- only hosts/admins can manage rooms
- hosts cannot moderate other hosts’ rooms
- PK is video-only
- hosts cannot block or kick themselves
- host revenue is split by configured percentages, not full gross

### Agency

Status:

- Confirmed as Laravel role plus `agencies` entity owned by a user
- agency can have many hosts
- agency now has its own separate treasury wallet

Permissions:

- access agency dashboard
- view/manage hosts under agency
- see room, call, PK, and payout reports
- credit users from agency treasury wallet
- view agency wallet ledger and transfer history
- export agency payout reports after admin publication

Responsibilities:

- recruit and attach hosts
- manage agency treasury if self-service is used
- track agency-host performance and payouts

Earning opportunities:

- agency share of host earnings from gifts/calls
- treasury flow is operational; it is not direct consumer revenue

Restrictions:

- agency payout reports are visible only after admin publishes them
- agency wallet can only distribute within balance
- agency is distinct from admin; it does not own global settings

### Admin

Status:

- Confirmed as Laravel role

Permissions:

- full user, host, agency, wallet, gift, report, moderation, settings, game, payout, and audit management
- approve/reject host and agency applications
- publish agency payout reports
- mark agency payout reports paid
- manage app settings and feature flags
- manage gifts, banners, themes, profile frames, subscription plans, recharge plans, games
- run reconciliation and reporting flows
- override moderation and unblock decisions
- fund agency treasury wallets
- credit/debit user wallets directly

Responsibilities:

- platform operations
- policy enforcement
- financial audit and payout review
- content and feature configuration

Earning opportunities:

- not a wallet-earning role; admin manages platform operations

Restrictions:

- admin workflows are heavily audited
- some report/pay flows enforce explicit publish-before-visible behavior

### Moderator

Status:

- Partially present
- no standalone persistent moderator account role was found
- realtime ingest supports a room participant role named `moderator`, but only admins are allowed to use it in that path

Practical interpretation:

- moderator is currently more of a room/runtime concept than a first-class business role
- admin acts as the effective moderator layer

---

## 2. User Flow

### Signup / Login

Confirmed flow:

- Firebase-backed auth is initialized in Flutter
- Laravel stores Firebase-linked users plus role/profile/wallet data
- user is assigned default `user` role and wallet on creation
- login also updates streak/theme/referral-related progression logic

### Onboarding

Confirmed:

- Flutter has onboarding/login/signup flows
- app bootstraps app config, auth token, and runtime gate checks

Inferred:

- onboarding is lightweight compared to the core social/live features

### Profile Setup

Confirmed:

- edit profile
- avatar upload/capture
- profile frames inventory/equip
- theme preference
- follower/following data
- profile-level host/agency/application status cards

### Becoming a Host

Confirmed:

- normal users can submit host application
- pending host requests block duplicate submissions
- admin receives application notifications
- once approved, role/profile are attached and host surfaces unlock

### Joining Agencies

Confirmed:

- host must already be a host to enroll to an agency
- host submits enrollment request to agency
- duplicate pending requests are blocked
- admin is notified

### Purchasing Coins

Confirmed:

- recharge plans are exposed via API
- payment order created, typically through Razorpay
- wallet credited after verify step
- recharge orders and wallet ledger are both recorded

### Gifting

Confirmed:

- user joins room
- opens gift catalog
- selects active gift and quantity
- wallet is debited
- room gift record and gift earning ledger are created
- room realtime gift event is broadcast
- gift also updates PK score if battle is active

### Subscriptions

Confirmed:

- user sees subscription plans
- purchase spends coins, not direct cash
- active subscription period extends from current expiry if already active
- subscription purchase impacts leaderboards
- app also supports a welcome-tip flow for complimentary signup subscriptions

### Private Calls

Confirmed:

- users can request audio/video calls to hosts
- host availability and rates matter
- call session has request, accept, reject, end, token, and history flows
- billing is processed after call end based on billable minutes

### PK Battles

Confirmed:

- host invites another live video room
- target host accepts/rejects
- active PK scores are driven by gifts during battle

---

## 3. Host Flow

### Host Registration

Confirmed:

- user applies for host
- admin reviews
- upon approval, user gets host role/profile

### Room Creation

Confirmed:

- host/admin can create or start rooms
- room types: audio, video
- supports scheduled and immediate start
- room title, topic, language, lock state, max speakers, max participants
- host gets LiveKit token and room metadata

### Room Management

Confirmed:

- start live
- heartbeat updates room activity
- end room
- scheduled reminders
- follow-based live start notifications

### Seat Management

Confirmed:

- listeners/viewers can request seat
- host/admin can accept/reject
- speaker caps enforced
- host can remove/mute speakers
- during active PK, seat promotions are locked

### PK Creation

Confirmed:

- host-owned room can invite another live video room
- cannot invite own room
- blocked host pairs are rejected
- only invited host can accept

### Earning Flow

Confirmed:

- gift gross is split into host, agency, and platform shares
- call gross is split into host, agency, and platform shares
- splits use configured percentages or host/agency overrides where present
- gift/call earnings are tracked in separate ledgers and reporting

Important note:

- current consumer wallet balance is not the same thing as host payout wallet
- host earnings are tracked through ledgers and payout reporting, not directly by crediting the host’s consumer wallet on every event

### Redeem Flow

Partially present:

- old GD Live has a simpler redeem/check-redeem pattern
- Talkee has richer reporting and payout flows, especially for agencies
- a direct host self-redeem approval flow is not the primary financial pattern in current Talkee

Practical interpretation:

- Talkee currently leans more toward admin/agency payout reporting and accounting than direct cashout-from-consumer-wallet behavior

---

## 4. Agency Flow

### Agency Creation

Confirmed:

- normal users can apply for agency
- admin approves and creates agency role/entity
- agency has owner user, payout settings, notes, and blocking state

### Agency Recruitment

Confirmed:

- hosts can enroll into agencies
- agencies have host lists and agency-scoped reporting

### Host Management

Confirmed:

- agency dashboard includes hosts, calls, rooms, PK battles, payouts
- admin can manage agencies and their hosts

### Commissions

Confirmed:

- agency share exists in gifts and call splits
- agency-level payout reporting aggregates host performance

### Reporting

Confirmed:

- agency dashboards include reports
- admin has agency reports, agency payout drafts, CSV export, publish, mark paid
- agency sees only published payout reports

### Payouts

Confirmed:

- admin generates payout drafts per agency/date range
- admin can edit draft rows online
- saved draft rows become CSV export source of truth
- admin can approve, publish to agency, mark paid, or delete incorrect unpaid reports
- agency can only see/export reports after publication

---

## 5. Wallet System

## Core Wallet Concepts

### Coins

Confirmed:

- primary spend currency for users
- used for:
  - gifts
  - subscriptions
  - entry packs
  - profile frame purchases
  - private calls
  - games

### Diamonds / Beans

Not found as first-class persisted wallet currencies in current Talkee.

Interpretation:

- Talkee currently models the visible economy directly in coins and payout ledgers
- “diamonds/beans” style dual consumer currency does not appear to be the active Talkee implementation

### Earnings Currency

Confirmed:

- host/agency earnings are represented in earning ledgers and payout reports as coin-derived values
- platform retains revenue share in coins

### Recharge Process

Confirmed:

- admin-defined recharge plans
- payment order creation
- gateway verification
- wallet transaction creation
- user wallet balance increment
- recharge audit and anomaly checking

### Conversion Process

Partially present:

- coin-to-payout conversion exists implicitly through payout reports and earning splits
- direct end-user bean/diamond conversion mechanic is not the dominant current model

### Redeem Process

Partially present:

- agency/admin payout workflow is clearly implemented
- direct consumer cashout flow is not the central current pattern in Talkee code

## Wallet Participants

### User Wallet

- one wallet per user
- consumer balance for spending and recharge
- ledger in `wallet_transactions`

### Agency Wallet

- separate treasury balance
- not mixed with user recharge wallet
- ledger in `agency_wallet_transactions`
- transfers bridged to user wallet via `agency_coin_transfers`

### Platform Revenue

- tracked as residual share on gifts/calls/games, not as a user wallet

## Complete Wallet Flow Diagram

```text
Money (Razorpay / payment gateway)
    -> PaymentOrder
    -> Verify payment
    -> User Wallet credit (recharge ledger)
    -> User spends coins on:
         - Gifts
         - Private calls
         - Subscription purchase
         - Entry pack purchase
         - Profile frame purchase
         - Games

Gift spend
    -> User wallet debit
    -> LiveRoomGift record
    -> Gift earning ledger
    -> Split into:
         - Host earning
         - Agency earning
         - Platform revenue
    -> If PK active: PK score increment

Private call spend
    -> User wallet debit after billing
    -> Call earning ledger
    -> Split into:
         - Host earning
         - Agency earning
         - Platform revenue

Subscription purchase
    -> User wallet debit
    -> UserSubscription active/extended

Entry pack / cosmetic purchase
    -> User wallet debit
    -> Ownership/unlock record

Agency treasury load
    -> Admin loads AgencyWallet
    -> AgencyWallet credit
    -> Agency treasury ledger

Agency treasury credit to user
    -> AgencyWallet debit
    -> User wallet credit
    -> Transfer bridge record
    -> Admin audit / actor attribution
```

---

## 6. Gifts

### Gift Categories

Confirmed:

- gifts are catalog-driven, active/inactive, sorted, and coin-valued
- each gift has:
  - name
  - coin value
  - asset URL
  - type
  - animation tier
  - animation duration

### Gift Animations

Confirmed:

- gift payload includes `gift_type`, `animation_tier`, `animation_duration_ms`
- Flutter has dedicated gift animation overlays in live rooms

### Gift Values

Confirmed:

- each gift has explicit coin cost
- quantity multiplies total spend

### Gift Leaderboard Effects

Confirmed:

- gifting updates:
  - user leaderboard
  - host leaderboard
  - agency leaderboard

### Gift Contribution Tracking

Confirmed:

- room gift records
- gift earning ledger
- sender, host, agency, room, quantity, total coins
- PK events and supporter standings in live room experience

## How Gifting Affects Host Earnings

Confirmed:

- sender wallet is debited
- host/agency/platform split is calculated
- host share uses host payout percentage or configured default

## How Gifting Affects PK Battles

Confirmed:

- each gift during active PK creates PK score events
- room A or room B score increments by gift total coins

## How Gifting Affects Rankings

Confirmed:

- sender contributes to user rankings
- host receives host-side gross contribution
- agency receives agency-side gross contribution in leaderboards

---

## 7. Live Rooms

### Public Live Rooms

Confirmed:

- discovery/list endpoints for live rooms
- rooms can be scheduled or live

### Audio Rooms

Confirmed:

- separate audio room page and API index
- role model: host, speaker, listener

### Video Rooms

Confirmed:

- separate video room page and API list
- role model: host, speaker, viewer

### Co-host Seats

Confirmed:

- speaker seat requests and promotion system
- not a separate “co-host object,” but effectively a co-host/speaker layer

### Locked Rooms

Confirmed:

- rooms have `is_locked`
- seat requests and some joins are blocked when locked

### Entry Rooms

Partially present:

- “entry pack” exists as a cosmetic/entry effect system
- not found as a separate room type

### VIP Rooms

Not found as a dedicated room type.

But:

- VIP/premium state exists
- subscription access gates live-room entry globally
- themed/VIP presentation exists in room metadata and cosmetics

## Room Lifecycle

Confirmed:

1. create or schedule room
2. optional reminders/follower notifications
3. start room
4. host joins with host token
5. audience joins with viewer/listener roles
6. seat requests / speakers / gifts / PK / chat / moderation run live
7. heartbeat updates activity
8. room ends manually or by termination
9. PK auto-ends if room ends

## Room Permissions

Confirmed:

- host/admin can manage room
- normal users need active subscription to join
- host blocks override join attempts
- PK is limited to video rooms

## Room Moderation

Confirmed:

- mute
- kick
- block
- moderation history
- unblock requests
- reports
- websocket moderation snapshot/cache

---

## 8. PK Battles

### How PK Starts

Confirmed:

- host in live video room invites another live video room
- target host accepts or rejects

### Scoring Mechanism

Confirmed:

- score is coin-weighted gift value during active battle
- battle tracks `score_a` and `score_b`

### Timer

Confirmed:

- admin-configurable default duration in app settings
- enforced as battle `duration_seconds`

### Winner Selection

Confirmed:

- battle tracks `winner_room_id`
- winner resolved when battle completes based on scores

### Penalties / Rewards

Confirmed:

- speaker promotions are locked during active PK
- active speakers may be demoted/prepared when PK starts

Not clearly implemented:

- formal “punishment” gimmicks beyond winner state and UI overlays

### Gift Contribution During PK

Confirmed:

- gifts remain sendable
- gifts update room experience and PK scoreboard simultaneously
- Flutter also tracks top supporters by side for winner/supporter UI

---

## 9. Subscriptions

### Creator Subscriptions

Not found as a host-specific creator membership sold by individual hosts.

### Premium Subscriptions

Confirmed:

- platform subscription plans exist
- purchase uses coins
- status, starts_at, ends_at, and plan metadata are tracked

### Perks

Confirmed / inferred:

- required to join live rooms
- drives VIP/premium state in room metadata
- influences visual status and access

### Badges

Inferred:

- VIP/premium state affects metadata and likely visual badges
- not all badge rendering is centralized as one model

### Exclusive Rooms

Not found as a separate dedicated room type.

What exists instead:

- global live-room access requires active subscription

### Monthly Benefits

Confirmed:

- duration-based plans
- active status window
- welcome-tip flow for gifted subscription onboarding

---

## 10. Premium Features

### Entry Effects

Confirmed:

- purchasable entry packs
- activatable per user
- entry effect triggered on room join/start presence

### Profile Frames

Confirmed:

- inventory
- equip
- purchasable frames
- reward/unlock frames
- expiry support

### Badges

Confirmed / inferred:

- levels carry badge icon/color metadata
- VIP status is surfaced in room metadata

### VIP Levels

Partially present:

- “VIP/premium” state exists via roles/subscriptions/theme metadata
- numeric VIP tier system separate from user level was not found as a dedicated model

### Achievements

Partially present:

- levels, streaks, goal milestones, profile frame rewards, leaderboard rewards
- not found as one generic achievements engine

### Cosmetic Items

Confirmed:

- themes
- profile frames
- entry packs
- room environment effects

---

## 11. Ranking System

### Daily Rankings

Confirmed internally:

- `leaderboard_daily_stats` exists and is used as aggregation base

### Weekly Rankings

Confirmed and surfaced in app:

- users weekly
- hosts weekly
- agencies weekly

### Monthly Rankings

Not found as a distinct public API/output period.

### Host Rankings

Confirmed:

- host rankings aggregate gift and call coins

### User Rankings

Confirmed:

- weekly uses gifts + calls + subscriptions + entry purchases
- all-time includes lifetime spend progression

### Agency Rankings

Confirmed:

- weekly and all-time agency boards exist

---

## 12. Notification System

## Delivery Model

Confirmed:

- persisted user notifications
- realtime Redis notifications
- optional FCM push delivery
- unread count and mark-read endpoints

## Notification Types Confirmed

- host live started
- scheduled live created
- scheduled live started
- application/admin workflow outcomes
- level up
- profile frame unlocks
- likely other operational/admin notifications based on `NotifyUser` usage

## Follow Notifications

Confirmed:

- host followers can opt into online notifications
- scheduled/live start notifications exist

## Room Notifications

Confirmed:

- reminders for scheduled rooms
- room start notifications

## Subscription Notifications

Partially present:

- welcome subscription tip flow exists
- full recurring subscription notification suite is limited

## Gift Notifications

Partially present:

- room gift activity is realtime in-room
- a separate inbox notification for every gift is not clearly the main pattern

## PK Notifications

Partially present:

- PK realtime events are broadcast
- inbox notifications specifically for PK were not found as a major persisted channel

---

## 13. Moderation System

### Mute

Confirmed:

- speaker mute by host is supported in room workflows

### Kick

Confirmed:

- host/admin can remove users from room
- kicked user events are broadcast

### Block

Confirmed:

- host can block user
- blocked users cannot join/send gifts/interact with that host’s room

### Report

Confirmed:

- users can report other users
- duplicate report throttling exists
- admin can review reports

### Room Moderation

Confirmed:

- host-side moderation APIs
- websocket moderation snapshot
- room-specific checks

### Host Moderation

Confirmed:

- host block list
- moderation history
- unblock request review

---

## 14. Admin Features

Confirmed admin feature areas:

- user management
- host management
- agency management
- wallet management
- agency wallet/treasury management
- gift management
- recharge plans and recharge audit
- banners
- subscriptions
- themes
- profile frames
- entry packs
- app settings
- call settings and call reporting
- live room settings
- PK battle review/history
- moderation rules, reports, history, analytics
- leaderboards and reports
- game configuration and user reports
- agency payout draft workflow

Specific high-value admin flows:

- exact-id user search behavior
- agency payout draft editor, CSV export, publish, delete, mark paid
- game access per user
- company profit reporting for games
- recharge audit with GST segregation

---

## 15. Database Domain Model

Major business entities confirmed:

- Users
- Roles / permissions
- Wallets
- WalletTransactions
- PaymentOrders
- RechargePlans
- SubscriptionPlans
- UserSubscriptions
- Agencies
- AgencyRequests
- AgencyWallets
- AgencyWalletTransactions
- AgencyCoinTransfers
- Hosts
- HostRequests
- HostEnrollRequests
- HostFollowers
- HostAvailabilities
- HostPhotos
- LiveRooms
- LiveRoomParticipants
- LiveRoomSeatRequests
- LiveRoomReminders
- LiveRoomGifts
- LiveRoomGiftEarningLedgers
- LiveRoomPkBattles
- LiveRoomPkEvents
- CallSessions
- CallEarningLedgers
- Gifts
- Themes
- UserThemeUnlocks
- UserThemePreferences
- EntryPacks
- UserEntryPacks
- ProfileFrames
- UserProfileFrames
- UserLevels
- UserLevelHistories
- LevelSpendEvents
- LeaderboardDailyStats
- TeenPattiRounds
- TeenPattiBets
- TeenPattiPayouts
- GreedyRounds
- GreedyBets
- GreedyPayouts
- UserNotifications
- ModerationRules
- ModerationActions
- HostUserBlocks
- RoomUserKicks
- UnblockRequests
- UserReports
- DevicePushTokens
- DeviceBlocks
- DeviceEntitlements
- Banners
- BannerEvents
- AppSettings
- LiveRoomAdminAudits
- AdminActionAudits
- UserGameAccesses

---

## 16. API Domain Model

Major backend modules likely required and present:

- Auth / Firebase session bootstrap
- App config / runtime gate
- User profile / public profile
- Host follow / social graph
- Applications: host, agency, host enrollment
- Wallet and recharge
- Payment gateway verification
- Notifications
- Subscriptions
- Themes / cosmetics
- Profile frames
- Entry packs
- Live room discovery and lifecycle
- Live room seat management
- Live room gifts
- Live room reminders
- Live room PK battles
- Live room moderation
- Private calling
- Leaderboards / dashboard
- Games: Teen Patti, Greedy
- Agency reporting and payout
- Agency treasury wallet
- Admin analytics and audits

---

## 17. Realtime Domain Model

Talkee’s realtime model spans Flutter, Laravel, and Node.

Likely / confirmed realtime event families:

- room connect / disconnect
- room lifecycle snapshot / created / live / ended
- participant join / leave
- audience count updates
- seat request created / accepted / rejected / removed / cancelled
- speaker added / removed
- room gift event
- gift animation payloads
- room chat messages / system messages
- moderation snapshot invalidation
- blocked / kicked events
- PK invite sent / received
- PK accepted / rejected / cancelled / started / score updated / ended / expired
- entry effect events
- user notification realtime push
- internal game snapshots for room games

---

## 18. Revenue Model

## Coin Purchases

Confirmed:

- users buy recharge plans with money
- gateway verification credits coins
- primary top-line consumer spend

## Subscriptions

Confirmed:

- users buy subscription plans with coins
- platform captures coin spend as part of retention/access model

## Gifting

Confirmed:

- user coin spend
- split into host, agency, platform shares
- major creator economy path

## VIP / Premium Memberships

Confirmed in product terms:

- subscription unlocks premium access behaviors
- themes, entry packs, frames add monetized premium layers

## Agency Commissions

Confirmed:

- agency receives configured share of host-driven earnings
- separate treasury flow also exists for agency-to-user distribution

## Platform Revenue

Confirmed:

- platform retains residual share from:
  - room gifts
  - private calls
  - consumer coin purchases
  - subscriptions
  - cosmetics
  - games

---

## 19. Screens Inventory

## Flutter App Surfaces Confirmed

### Auth

- onboarding
- login
- signup
- reset/forgot password flows

### Home / Discovery

- Home shell
- live page
- video rooms page
- settings page
- dashboard / leaderboard page

### Live

- live preflight sheet
- start live page
- backstage page
- audio room page
- video room page
- room chat overlay
- gift sheet
- PK overlay
- entry effect overlay
- public profile sheet from rooms

### Calls

- incoming call screen
- outgoing call screen
- active call screen
- live users screen
- call history

### Wallet

- recharge bottom sheet
- wallet history / ledger

### Profile

- profile page
- edit profile
- blocked users
- moderation history
- unblock requests
- following
- followers
- scheduled lives

### Notifications

- notifications inbox

### Applications

- apply host
- apply agency
- enroll agency
- my applications

### Subscriptions / Premium

- subscriptions page
- choose plan sheet
- theme center
- entry pack catalog

### Games

- Teen Patti panel
- Greedy panel

## Web Surfaces Confirmed

### Admin

- users
- hosts
- agencies
- wallets
- reports
- games
- moderation
- settings
- recharge audits
- agency payout reports
- agency wallet reports

### Agency

- dashboard
- hosts
- calls
- rooms
- PK
- wallet
- payout reports
- profile

### Host

- dashboard
- enroll requests
- calls

---

## 20. Gap Analysis: Talkee vs Current GD Live Project

This comparison is based on the sibling repos:

- Talkee: current repo analyzed in this document
- GD Live: `../gd live/gd_live_flutter` and `../gd live/gd_live_laravel`

## High-Level Assessment

Talkee is a much more structured second-generation product than GD Live.

GD Live appears to be an older, flatter live app with:

- direct room creation and gifting
- simpler wallet fields
- simpler recharge integration
- simpler follow/follower flows
- games
- private/public call screens
- basic agency reporting

Talkee adds formal product layers that GD Live either lacks or implements only partially:

- stricter role model
- subscriptions as access control
- formal host/agency application workflows
- formal earning ledgers and payout reports
- structured moderation
- app settings/feature flags
- premium cosmetics system
- richer realtime room model

## Features Already Present in GD Live

- login/signup
- home/live discovery
- profile/settings
- followers/followings
- wallet/recharge
- gifts
- live rooms
- private calls
- games including Teen Patti and other game variants
- banners
- basic agency-associated reporting

## Features Partially Present in GD Live

- agency support
  - present as user/agency relationships and reports, but much less formal
- subscriptions / entries
  - present through older `entries` and `subscriptions`, but simpler than Talkee
- wallet
  - present, but simpler and less auditable
- calls/live
  - present, but less role-aware and less configurable

## Features Missing or Significantly Weaker in GD Live

- role-aware admin / agency / host dashboards
- separate agency treasury wallet
- linked transfer accounting between agency and user wallets
- agency payout draft review/publish workflow
- publish gating for agency payout visibility
- formal moderation suite with unblock requests and admin review
- room seat management model with audio listener/speaker and video viewer/speaker separation
- subscription-gated live room entry
- richer PK workflow
- theme unlock system
- profile frame shop/reward system
- entry effect ownership and realtime triggers
- structured leaderboard system across users/hosts/agencies
- recharge audit and anomaly reporting
- per-user game access controls
- company-profit style game reporting

## Features in Talkee That Need Redesign or Careful Translation If Ported Into GD Live

- wallet architecture
  - GD Live appears to use direct user wallet/coin fields more heavily
  - Talkee uses wallet ledgers, payment orders, and agency treasury separation
- role architecture
  - GD Live is flatter; Talkee expects formal roles and permissions
- room access
  - Talkee subscription gate for joining live rooms is a major product rule
- payout/reporting
  - Talkee’s agency payout workflow is far more operationally mature
- moderation
  - Talkee’s room/block/report model is significantly broader

## Practical Gap Summary

### Already aligned enough to reuse conceptually

- live streaming core
- gifting as a central monetization loop
- recharge and wallet as consumer entry point
- games attached to live experience
- private calling

### Partially aligned and would need redesign

- agency workflows
- payout/redeem/accounting
- subscription/entry monetization
- leaderboard and level systems

### Largely absent in GD Live and would need fresh implementation

- modern admin operations
- agency treasury
- publishable payout workflow
- structured moderation
- premium cosmetics stack
- per-user game enablement
- subscription-gated room access

---

## Final Product Summary

Talkee is best understood as a multi-role live social platform with five strong product pillars:

1. Live audio/video rooms with seat management and moderation
2. A spend economy centered on coins, gifts, calls, subscriptions, and games
3. Host/agency monetization with structured reporting and payouts
4. Premium identity and cosmetic systems
5. Admin-controlled operational tooling with audits, settings, and gating

Compared with GD Live, Talkee is materially more mature in:

- financial accounting
- role separation
- moderation
- admin tooling
- premium systems
- payout governance

Talkee is therefore not just “GD Live with more screens.” It is a more formalized product/business system with stronger operational controls and clearer monetization domains.
