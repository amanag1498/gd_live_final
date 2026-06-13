# GD Remake Workspace Context

All changes in this workspace will be reviewed by Claude Code.

## Objective

This workspace is being converted from Talkee/Talkieo branding and product structure to `GD Live`.

Use this file as the default standing context for future work in `/Users/amanagarwal/Desktop/gd_remake`.

## Project Map

- `gd_live_flutter`
  - Old Flutter app to reuse for GD Live UI, branding patterns, and Play Store-facing details.
  - Treat this as the visual reference app.

- `liveapp flutter`
  - Active Flutter app that should be renamed and aligned to `gd_live_flutter`.
  - Rename package/app identity, branding, visible strings, assets, and internal naming as needed for GD Live.
  - Target naming should match `gd_live_flutter` conventions where practical.

- `gd_live_backend`
  - Laravel backend to be converted into the GD Live backend.
  - Rename Talkee/Talkieo branding and product references to GD Live where they are part of first-party app code, config, routes, views, docs, or admin UI.

- `gd_live_server`
  - Presence/live websocket server to be converted into the GD Live server.
  - Rename first-party service naming and branding accordingly.

## Rename Rules

- Preferred product name: `GD Live`
- Preferred code/package style: `gd_live`
- Replace legacy first-party references such as:
  - `Talkee`
  - `talkee`
  - `Talkieo`
  - `talkieo`
  - `liveapp` when it is a first-party app identity rather than a generic internal folder name

## Scope Rules

- Safe to change freely in first-party project code.
- Do not blindly rename inside third-party or generated content such as:
  - `vendor/`
  - `node_modules/`
  - `.dart_tool/`
  - `build/`
  - platform-generated files unless the app identity truly requires it
- Do not rename copied SDK/plugin code unless it is actually part of the shipped GD Live app and the rename is necessary.

## Working Preference

- Default to analyzing impact before broad rename sweeps.
- Prefer screen-by-screen or flow-by-flow changes for Flutter UI work.
- Preserve the original source apps conceptually by treating each folder as its own migration surface.
- When renaming, keep behavior intact first, then reduce functionality deliberately.

## Migration Intent

The intended end state is:

1. `gd_live_flutter` remains the reference for GD Live UI and store-facing details.
2. `liveapp flutter` becomes the main GD Live Flutter app, renamed consistently.
3. `gd_live_backend` is the GD Live backend with GD branding throughout first-party code.
4. `gd_live_server` is the GD Live websocket/presence server.

## Default Assumptions For Future Tasks

- If a rename request is ambiguous, prefer `GD Live` for user-facing text and `gd_live` for code identifiers.
- If a string appears in third-party dependencies, leave it alone unless there is a concrete runtime reason to change it.
- If a change risks breaking package IDs, bundle IDs, routes, APIs, or deployed integrations, analyze first and change in controlled steps.
