<?php
// ── Sentry Error Monitoring Setup ────────────────────────────────────────
// 1. Sign up free at https://sentry.io
// 2. Create a new project, select "PHP" as the platform
// 3. Copy the DSN it gives you and paste it below
// 4. Run: composer require sentry/sentry
//
// That's it — once initialized, Sentry automatically captures:
//   - Uncaught exceptions
//   - PHP fatal errors
//   - PHP warnings/notices (respecting your error_reporting level)
// No need to add try/catch blocks everywhere; this is app-wide.

$sentry_dsn = 'https://30d2154a735ea6368668ca6343eb94a5@o4511732268924928.ingest.us.sentry.io/4511732272791552';

if (!empty($sentry_dsn) && class_exists('\Sentry\ClientBuilder')) {
    \Sentry\init([
        'dsn' => $sentry_dsn,

        // Set this to your live domain name so you can tell prod errors
        // apart from Laragon/localhost testing errors in the Sentry dashboard.
        'environment' => (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false) ? 'local' : 'production',

        // Percentage of requests to trace for performance monitoring (0.0–1.0).
        // 0 = errors only, no performance data. Keep at 0 unless you want that.
        'traces_sample_rate' => 0,
    ]);
}
