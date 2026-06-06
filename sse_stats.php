<?php
/**
 * sse_stats.php — Server-Sent Events endpoint
 * Streams live dashboard stats every 5 seconds.
 * No WebSocket library needed — works on any shared hosting.
 */

// ── Auth: only logged-in staff/admin may connect ──────────────
session_start();
if (empty($_SESSION['userdata'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// ── SSE headers ───────────────────────────────────────────────
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');   // disable nginx buffering
set_time_limit(0);
ignore_user_abort(true);

// ── DB connection ─────────────────────────────────────────────
$host   = 'localhost';
$dbname = 'bmis';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "event: error\ndata: " . json_encode(['msg' => 'DB error']) . "\n\n";
    flush();
    exit;
}

// ── Helper: fetch all stats in one go ─────────────────────────
function get_stats(PDO $pdo): array {
    $q = fn(string $sql) => (int)$pdo->query($sql)->fetchColumn();
    return [
        // Residents
        'res_male'     => $q("SELECT COUNT(*) FROM tbl_resident WHERE sex='Male'"),
        'res_female'   => $q("SELECT COUNT(*) FROM tbl_resident WHERE sex='Female'"),
        'res_head'     => $q("SELECT COUNT(*) FROM tbl_resident WHERE type='Head'"),
        'res_member'   => $q("SELECT COUNT(*) FROM tbl_resident WHERE type='Member'"),
        'res_voter'    => $q("SELECT COUNT(*) FROM tbl_resident WHERE voter='Yes'"),
        'res_senior'   => $q("SELECT COUNT(*) FROM tbl_resident WHERE senior_citizen='Yes'"),
        'res_pwd'      => $q("SELECT COUNT(*) FROM tbl_resident WHERE pwd='Yes'"),
        // Staff
        'staff_total'  => $q("SELECT COUNT(*) FROM tbl_staff"),
        'staff_male'   => $q("SELECT COUNT(*) FROM tbl_staff WHERE sex='Male'"),
        'staff_female' => $q("SELECT COUNT(*) FROM tbl_staff WHERE sex='Female'"),
        // Complaints
        'cmp_total'    => $q("SELECT COUNT(*) FROM tbl_complaints"),
        'cmp_pending'  => $q("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'"),
        'cmp_resolved' => $q("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'"),
        // Messages & ID uploads
        'msg_count'    => $q("SELECT COUNT(*) FROM admin_messages"),
        'id_pending'   => $q("SELECT COUNT(*) FROM tbl_id_uploads WHERE status='pending'"),
    ];
}

// ── Stream loop ───────────────────────────────────────────────
$last = [];

while (true) {
    if (connection_aborted()) break;

    try {
        $stats = get_stats($pdo);
    } catch (Throwable $e) {
        // DB dropped — try reconnecting next tick
        sleep(5);
        continue;
    }

    // Only push when something changed (saves bandwidth)
    if ($stats !== $last) {
        echo "event: stats\n";
        echo "data: " . json_encode($stats) . "\n\n";
        flush();
        $last = $stats;
    }

    // Keep-alive ping every loop so the browser doesn't time out
    echo ": ping\n\n";
    flush();

    sleep(5);
}
