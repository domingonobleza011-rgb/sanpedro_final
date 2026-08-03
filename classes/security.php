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
function bmis_require_admin_dashboard(): array {
    $u = bmis_require_login();

    $is_admin_role = in_array($u['role'], ['administrator', 'Admin'], true);

    $admin_dashboard_positions = [
        'Punong Barangay',
        'Secretary',
        'Treasurer',
        'Clerk',
        'Book Keeper',
        'Committee on Appropriation',
        'Committee on Health',
        'Committee on Women and Children',
        'Committee on Education',
        'Committee on Peace and Order',
        'Committee on Infrastructure',
        'Committee on Ways and Means',
        'Committee on Agriculture',
        'Committee on Tourism',
        'IPMRR Representative',
    ];

    $is_allowed_position = in_array($u['position'] ?? '', $admin_dashboard_positions, true);

    if (!$is_admin_role && !$is_allowed_position) {
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
// Backed by `tbl_login_attempts` (see add_login_attempts_table.sql) rather
// than $_SESSION, because a session-based counter resets the moment an
// attacker drops their cookie — it does not actually stop repeated guesses.
define('BMIS_MAX_LOGIN_ATTEMPTS', 5);
define('BMIS_LOCKOUT_SECONDS',    900); // 15 minutes

function bmis_record_failed_login(PDO $conn, string $identity): void {
    $hash = hash('sha256', strtolower(trim($identity)));
    $stmt = $conn->prepare("SELECT attempt_count, first_attempt FROM tbl_login_attempts WHERE identity_hash = ?");
    $stmt->execute([$hash]);
    $row = $stmt->fetch();

    if (!$row) {
        $conn->prepare("INSERT INTO tbl_login_attempts (identity_hash, attempt_count, first_attempt, last_attempt) VALUES (?, 1, NOW(), NOW())")
             ->execute([$hash]);
        return;
    }

    // If the previous lockout window has fully expired, start a fresh count.
    $first = strtotime($row['first_attempt']);
    if ((time() - $first) > BMIS_LOCKOUT_SECONDS) {
        $conn->prepare("UPDATE tbl_login_attempts SET attempt_count = 1, first_attempt = NOW(), last_attempt = NOW() WHERE identity_hash = ?")
             ->execute([$hash]);
        return;
    }

    $conn->prepare("UPDATE tbl_login_attempts SET attempt_count = attempt_count + 1, last_attempt = NOW() WHERE identity_hash = ?")
         ->execute([$hash]);
}

function bmis_is_locked_out(PDO $conn, string $identity): bool {
    $hash = hash('sha256', strtolower(trim($identity)));
    $stmt = $conn->prepare("SELECT attempt_count, first_attempt FROM tbl_login_attempts WHERE identity_hash = ?");
    $stmt->execute([$hash]);
    $row = $stmt->fetch();
    if (!$row) return false;

    if ($row['attempt_count'] < BMIS_MAX_LOGIN_ATTEMPTS) return false;

    if ((time() - strtotime($row['first_attempt'])) > BMIS_LOCKOUT_SECONDS) {
        // Lockout window expired — clear it so the next attempt starts clean.
        $conn->prepare("DELETE FROM tbl_login_attempts WHERE identity_hash = ?")->execute([$hash]);
        return false;
    }
    return true;
}

function bmis_reset_login_attempts(PDO $conn, string $identity): void {
    $hash = hash('sha256', strtolower(trim($identity)));
    $conn->prepare("DELETE FROM tbl_login_attempts WHERE identity_hash = ?")->execute([$hash]);
}

function bmis_lockout_seconds_remaining(PDO $conn, string $identity): int {
    $hash = hash('sha256', strtolower(trim($identity)));
    $stmt = $conn->prepare("SELECT first_attempt FROM tbl_login_attempts WHERE identity_hash = ?");
    $stmt->execute([$hash]);
    $row = $stmt->fetch();
    if (!$row) return 0;
    $elapsed = time() - strtotime($row['first_attempt']);
    return max(0, BMIS_LOCKOUT_SECONDS - $elapsed);
}

// ─── 6. Session Fixation Prevention ─────────────────────────────────────────
function bmis_regenerate_session(): void {
    bmis_session_start();
    session_regenerate_id(true);
}

// ─── 7. Secure File Upload Validation ───────────────────────────────────────
// The extension check alone is NOT enough — $_FILES[...]['type'] and the
// original filename are both attacker-controlled. A file can be named
// "id.jpg" and still be a PHP webshell, so the real MIME type is sniffed
// from the file's content with finfo before it's ever trusted, and the file
// is always saved under a random name (never the attacker's original name)
// into a directory that has a .htaccess disabling script execution.
$BMIS_ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$BMIS_ALLOWED_IMAGE_EXTS  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$BMIS_ALLOWED_DOC_TYPES   = ['application/pdf'];
$BMIS_ALLOWED_DOC_EXTS    = ['pdf'];
$BMIS_MAX_UPLOAD_BYTES    = 5 * 1024 * 1024; // 5 MB

/**
 * Validate an uploaded file against a real, content-sniffed whitelist.
 * @param array $file       One entry from $_FILES.
 * @param bool  $allow_pdf  Also allow application/pdf (used for valid-ID uploads).
 */
function bmis_validate_image_upload(array $file, bool $allow_pdf = false): array {
    global $BMIS_ALLOWED_IMAGE_TYPES, $BMIS_ALLOWED_IMAGE_EXTS,
           $BMIS_ALLOWED_DOC_TYPES, $BMIS_ALLOWED_DOC_EXTS, $BMIS_MAX_UPLOAD_BYTES;

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'msg' => 'Upload error code: ' . ($file['error'] ?? 'none')];
    }
    if ($file['size'] > $BMIS_MAX_UPLOAD_BYTES) {
        return ['ok' => false, 'msg' => 'File too large (max 5 MB).'];
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'msg' => 'Invalid upload.'];
    }

    $allowed_types = $BMIS_ALLOWED_IMAGE_TYPES;
    $allowed_exts  = $BMIS_ALLOWED_IMAGE_EXTS;
    if ($allow_pdf) {
        $allowed_types = array_merge($allowed_types, $BMIS_ALLOWED_DOC_TYPES);
        $allowed_exts  = array_merge($allowed_exts, $BMIS_ALLOWED_DOC_EXTS);
    }

    // Check MIME type with finfo, sniffed from content — not the
    // browser-supplied $file['type'], which is trivially spoofed.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed_types, true)) {
        $label = $allow_pdf ? 'JPG, PNG, GIF, WebP, or PDF' : 'JPG, PNG, GIF, WebP';
        return ['ok' => false, 'msg' => "Invalid file type. Only $label allowed."];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts, true)) {
        return ['ok' => false, 'msg' => 'Invalid file extension.'];
    }

    // Generate a safe, random filename — the original filename is never used.
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
     . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://kit.fontawesome.com https://ka-f.fontawesome.com https://stackpath.bootstrapcdn.com https://maxcdn.bootstrapcdn.com https://ajax.googleapis.com https://www.gstatic.com https://cdnjs.cloudflare.com blob:; "
     . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://ka-f.fontawesome.com https://stackpath.bootstrapcdn.com https://maxcdn.bootstrapcdn.com; "
     . "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com https://kit.fontawesome.com https://ka-f.fontawesome.com https://cdn.jsdelivr.net; "
     . "img-src 'self' data: blob:; "
     . "connect-src 'self' https://fcm.googleapis.com https://oauth2.googleapis.com https://www.googleapis.com https://cdn.jsdelivr.net https://ka-f.fontawesome.com https://www.gstatic.com https://stackpath.bootstrapcdn.com https://firebaseinstallations.googleapis.com https://fcmregistrations.googleapis.com https://cdnjs.cloudflare.com; "
     . "worker-src 'self' blob:; "
     . "frame-src https://www.google.com;");
}

