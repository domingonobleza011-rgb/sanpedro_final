<?php
/**
 * Security Helper — Barangay San Pedro BMIS
 * Centralizes session management, CSRF, rate limiting, and auth guards.
 */

// ─── 1. Session Hardening ────────────────────────────────────────────────────
function bmis_session_start() {
    if (session_status() === PHP_SESSION_NONE) {
        // Secure session cookie settings
        session_set_cookie_params([
            'lifetime' => 0,                    // Browser session only
            'path'     => '/',
            'domain'   => '',
            'secure'   => isset($_SERVER['HTTPS']), // HTTPS-only when available
            'httponly' => true,                  // Inaccessible to JavaScript
            'samesite' => 'Strict',              // CSRF mitigation
        ]);
        session_start();
    }
}

// ─── 2. CSRF Token ───────────────────────────────────────────────────────────
function bmis_csrf_token(): string {
    bmis_session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function bmis_csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(bmis_csrf_token(), ENT_QUOTES) . '">';
}

function bmis_verify_csrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals(bmis_csrf_token(), $token)) {
        http_response_code(403);
        die('Request validation failed. Please go back and try again.');
    }
}

// ─── 3. Output Escaping ──────────────────────────────────────────────────────
function e(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ─── 4. Auth Guards ──────────────────────────────────────────────────────────
function bmis_require_login(): array {
    bmis_session_start();
    if (empty($_SESSION['userdata'])) {
        // Build an absolute URL so the redirect works no matter how deep the page is
        $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $script   = $_SERVER['SCRIPT_NAME'] ?? '';
        $base_dir = rtrim(dirname($script), '/\\');
        $login    = $scheme . '://' . $host . $base_dir . '/index.php';
        header('Location: ' . $login);
        exit();
    }
    return $_SESSION['userdata'];
}

function bmis_require_admin(): array {
    $u = bmis_require_login();
    if ($u['role'] !== 'administrator' && $u['role'] !== 'Admin') {
        http_response_code(403);
        die('Access denied.');
    }
    return $u;
}

function bmis_require_staff_or_admin(): array {
    $u = bmis_require_login();
    $allowed = ['administrator', 'Admin', 'user'];
    if (!in_array($u['role'], $allowed, true)) {
        http_response_code(403);
        die('Access denied.');
    }
    return $u;
}

function bmis_require_resident(): array {
    $u = bmis_require_login();
    if ($u['role'] !== 'resident') {
        http_response_code(403);
        die('Access denied.');
    }
    return $u;
}

// ─── 5. Brute-force / Rate Limiting ─────────────────────────────────────────
define('BMIS_MAX_LOGIN_ATTEMPTS', 5);
define('BMIS_LOCKOUT_SECONDS',    900); // 15 minutes

function bmis_record_failed_login(string $identity): void {
    bmis_session_start();
    $key = 'login_fail_' . md5($identity);
    if (empty($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'first' => time()];
    }
    $_SESSION[$key]['count']++;
}

function bmis_is_locked_out(string $identity): bool {
    bmis_session_start();
    $key = 'login_fail_' . md5($identity);
    if (empty($_SESSION[$key])) return false;
    $data = $_SESSION[$key];
    if ($data['count'] < BMIS_MAX_LOGIN_ATTEMPTS) return false;
    if ((time() - $data['first']) > BMIS_LOCKOUT_SECONDS) {
        unset($_SESSION[$key]); // Lockout expired
        return false;
    }
    return true;
}

function bmis_reset_login_attempts(string $identity): void {
    bmis_session_start();
    $key = 'login_fail_' . md5($identity);
    unset($_SESSION[$key]);
}

function bmis_lockout_seconds_remaining(string $identity): int {
    bmis_session_start();
    $key = 'login_fail_' . md5($identity);
    if (empty($_SESSION[$key])) return 0;
    $elapsed = time() - $_SESSION[$key]['first'];
    return max(0, BMIS_LOCKOUT_SECONDS - $elapsed);
}

// ─── 6. Session Fixation Prevention ─────────────────────────────────────────
function bmis_regenerate_session(): void {
    bmis_session_start();
    session_regenerate_id(true);
}

// ─── 7. Secure File Upload Validation ───────────────────────────────────────
$BMIS_ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$BMIS_ALLOWED_IMAGE_EXTS  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$BMIS_MAX_UPLOAD_BYTES    = 5 * 1024 * 1024; // 5 MB

function bmis_validate_image_upload(array $file): array {
    global $BMIS_ALLOWED_IMAGE_TYPES, $BMIS_ALLOWED_IMAGE_EXTS, $BMIS_MAX_UPLOAD_BYTES;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'msg' => 'Upload error code: ' . $file['error']];
    }
    if ($file['size'] > $BMIS_MAX_UPLOAD_BYTES) {
        return ['ok' => false, 'msg' => 'File too large (max 5 MB).'];
    }

    // Check MIME type with finfo (not the browser-supplied type)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $BMIS_ALLOWED_IMAGE_TYPES, true)) {
        return ['ok' => false, 'msg' => 'Invalid file type. Only JPG, PNG, GIF, WebP allowed.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $BMIS_ALLOWED_IMAGE_EXTS, true)) {
        return ['ok' => false, 'msg' => 'Invalid file extension.'];
    }

    // Generate a safe, random filename
    $safe_name = bin2hex(random_bytes(16)) . '.' . $ext;
    return ['ok' => true, 'safe_name' => $safe_name, 'mime' => $mime];
}

// ─── 8. Security HTTP Headers ────────────────────────────────────────────────
function bmis_set_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    header("Content-Security-Policy: default-src 'self'; "
         . "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://code.jquery.com https://kit.fontawesome.com https://ka-f.fontawesome.com; "
         . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://ka-f.fontawesome.com; "
         // Added https://cdn.jsdelivr.net here so Bootstrap Icons can load the actual font files
         . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://kit.fontawesome.com https://ka-f.fontawesome.com https://cdn.jsdelivr.net; "
         . "img-src 'self' data: blob:; "
         . "frame-src https://www.google.com;");
}
