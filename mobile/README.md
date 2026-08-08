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
testable without a device or the Android SDK.

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
