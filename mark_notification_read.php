<?php
/**
 * mark_notification_read.php
 * Marks resident->admin message notifications as read.
 * Called from the notification bell dropdown (dashboard_sidebar_start.php).
 */
error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
require_once('classes/conn.php');

// Only allow a same-app relative page (e.g. "admn_dashboard.php" or "admn_messages.php?foo=1")
// as the redirect target, to avoid open-redirect issues.
$redirect = $_POST['redirect_to'] ?? 'admn_dashboard.php';
if (!preg_match('/^[a-zA-Z0-9_\-]+\.php(\?[^\s]*)?$/', $redirect)) {
    $redirect = 'admn_dashboard.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['mark_all_read'])) {
            // Mark every unread resident message as read
            $conn->exec("UPDATE admin_messages SET status = 'read' WHERE status = 'unread'");
        } elseif (isset($_POST['mark_read_id'])) {
            // Mark a single message as read
            $id = (int) $_POST['mark_read_id'];
            $stmt = $conn->prepare("UPDATE admin_messages SET status = 'read' WHERE id_admin_msg = ?");
            $stmt->execute([$id]);
        }
    } catch (Throwable $e) {
        // Fail silently — the notification will simply still appear as unread.
        error_log('mark_notification_read error: ' . $e->getMessage());
    }
}

header("Location: $redirect");
exit();
