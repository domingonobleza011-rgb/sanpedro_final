<?php
/**
 * poll_stats.php
 * Lightweight JSON endpoint for dashboard live counters.
 * Replaces the old SSE (sse_stats.php) approach.
 *
 * Called by live-stats.js every 60 seconds via fetch().
 * One short HTTP request per minute instead of a persistent connection.
 */

error_reporting(0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
require_once 'classes/conn.php';         // provides $conn (PDO)
require_once 'classes/resident.class.php';
require_once 'classes/staff.class.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

try {
    $residentbmis = new ResidentBMIS();
    $staffbmis    = new StaffBMIS();

    // ── Resident counts ───────────────────────────────────────
    $res_male   = (int) $residentbmis->count_male_resident();
    $res_female = (int) $residentbmis->count_female_resident();
    $res_head   = (int) $residentbmis->count_head_resident();
    $res_member = (int) $residentbmis->count_member_resident();
    $res_voter  = (int) $residentbmis->count_voters();
    $res_senior = (int) $residentbmis->count_resident_senior();
    $res_pwd    = (int) $residentbmis->count_pwd();

    // ── Staff counts ──────────────────────────────────────────
    $staff_total  = (int) $staffbmis->count_staff();
    $staff_male   = (int) $staffbmis->count_mstaff();
    $staff_female = (int) $staffbmis->count_fstaff();

    // ── Complaint counts ──────────────────────────────────────
    $cmp_pending  = (int) $conn->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'")->fetchColumn();
    $cmp_resolved = (int) $conn->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'")->fetchColumn();
    $cmp_total    = $cmp_pending + $cmp_resolved;

    // ── Messages (unread / all) ───────────────────────────────
    $msg_count = (int) $conn->query("SELECT COUNT(*) FROM admin_messages")->fetchColumn();

    // ── Pending ID uploads ────────────────────────────────────
    $id_pending = (int) $conn->query("SELECT COUNT(*) FROM admin_messages WHERE status='pending'")->fetchColumn();

    echo json_encode([
        'res_male'    => $res_male,
        'res_female'  => $res_female,
        'res_head'    => $res_head,
        'res_member'  => $res_member,
        'res_voter'   => $res_voter,
        'res_senior'  => $res_senior,
        'res_pwd'     => $res_pwd,
        'staff_total' => $staff_total,
        'staff_male'  => $staff_male,
        'staff_female'=> $staff_female,
        'cmp_total'   => $cmp_total,
        'cmp_pending' => $cmp_pending,
        'cmp_resolved'=> $cmp_resolved,
        'msg_count'   => $msg_count,
        'id_pending'  => $id_pending,
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'stats unavailable']);
}
