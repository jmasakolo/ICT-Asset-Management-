# Asset Intake mobile app

Ionic + React + Capacitor app for the ICT Asset Management API, replacing the
original mobile app whose source code was lost (only compiled `.apk` files
remained).

## Local development

```bash
npm install
cp .env.example .env   # edit VITE_API_BASE_URL if pointing at a local API
npm run dev
```

Runs as a plain web app in the browser — Capacitor plugins like
`@capacitor/preferences` fall back to browser storage, so the full app is
testable without a device, the Android SDK, or Xcode.

## Building the APK

The APK is built by `.github/workflows/mobile-apk.yml` on every push to
`mobile/**`, and published to `public/downloads/asset-intake-app.apk` on
`main` (live at `https://assets.techwesz.com/downloads/asset-intake-app.apk`
once deployed).

**Known limitation:** the workflow builds an unsigned **debug** APK
(`assembleDebug`), not a signed release build. This means:

- Installable only via manual sideload with "unknown sources" enabled
- Not distributable through the Play Store
- Android will refuse to install it over an existing install signed with a
  *different* debug keystore (e.g. a build from a different machine/CI run
  using a different debug key) — uninstall the existing app first if this
  happens

Release signing (generating a keystore, storing the signing secret in GitHub
Actions secrets, building with `assembleRelease`) is a deliberate scope cut
for v1 and would need to be added before any wider distribution.

## Android platform directory

`android/` is committed, not regenerated — Capacitor treats native platform
directories as source, not a build artifact, so hand-edits (icons, manifest,
SDK versions in `android/variables.gradle`) persist across builds. After
changing web code, sync it into the native project with:

```bash
npm run build
npx cap sync android
```

## Building for iOS

The iOS app shares 100% of `src/` with the Android app — same screens, same
API client, same login/auth flow. Nothing in this app's code is
Android-specific; the only Capacitor plugin in use (`@capacitor/preferences`)
has full iOS support built in.

`.github/workflows/mobile-ios.yml` builds it on `macos-latest` (Xcode and
CocoaPods come preinstalled there — this Mac only has the Xcode Command Line
Tools, not full Xcode.app, so CI is where the `ios/` platform actually gets
built and exercised) on every push to `mobile/**`. It produces an unsigned
build for the **iOS Simulator only**, uploaded as a workflow artifact
(`asset-intake-ios-simulator-build`) — there's no download URL for it the way
there is for the Android APK.

**Known limitation:** unlike Android's sideloadable debug APK, there's no
free path to a real-device iOS build. A build that runs on a physical iPhone
needs either:

- A free Apple ID + local Xcode signing (builds expire after 7 days, no CI —
  must be built and installed directly from a Mac with the device attached), or
- An Apple Developer Program account ($99/year) for TestFlight or ad-hoc
  distribution with proper provisioning profiles

Real-device distribution is a deliberate scope cut for v1 — Simulator
verification confirms the app builds and behaves correctly against the live
API without requiring an Apple Developer account.

## iOS platform directory

`ios/` is committed, not regenerated — same "native platform is source, not
a build artifact" convention as `android/`. It didn't exist locally when this
was set up (no Xcode/CocoaPods on this machine), so the CI workflow bootstraps
it (`npx cap add ios`) the first time it's missing and commits the result;
after that it behaves like `android/` — hand-edits persist, and web changes
get synced in with:

```bash
npm run build
npx cap sync ios
```

(the `sync` step itself still needs to run somewhere with CocoaPods
installed to fully take effect — e.g. in CI, or on a Mac with Xcode).
