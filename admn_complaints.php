<?php
/**
 * admn_complaints.php
 * -------------------------------------------------------
 * Admin page: view, resolve, mark pending, and delete
 * resident complaints.
 * Integrates with the existing Barangay System sidebar.
 * -------------------------------------------------------
 */

error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);
define('BMIS_ROLE_REQUIRED', 'admin');
require('secure_header.php');
// ---------- DB CONFIG — adjust to match your setup ----------
$host   = 'localhost';
$dbname = 'bmis';
$dbuser = 'root';
$dbpass = '';
// ------------------------------------------------------------

$pdo = null;
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// ---- Handle actions ----
$action_msg  = '';
$action_type = ''; // success | danger | warning

if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Resolve
    if (isset($_POST['action_resolve'])) {
        $id      = (int)$_POST['complaint_id'];
        $remarks = trim($_POST['admin_remarks'] ?? '');
        $stmt = $pdo->prepare("UPDATE tbl_complaints SET status='resolved', admin_remarks=:r, date_updated=NOW() WHERE id=:id");
        $stmt->execute([':r' => $remarks, ':id' => $id]);
        $action_msg  = 'Complaint #' . $id . ' has been marked as <strong>Resolved</strong>.';
        $action_type = 'success';
    }

    // Set Pending
    if (isset($_POST['action_pending'])) {
        $id = (int)$_POST['complaint_id'];
        $stmt = $pdo->prepare("UPDATE tbl_complaints SET status='pending', date_updated=NOW() WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $action_msg  = 'Complaint #' . $id . ' has been reverted to <strong>Pending</strong>.';
        $action_type = 'warning';
    }

    // Delete
    if (isset($_POST['action_delete'])) {
        $id = (int)$_POST['complaint_id'];
        // Also remove photo if it exists
        $row = $pdo->query("SELECT photo_path FROM tbl_complaints WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['photo_path'] && file_exists(__DIR__ . '/' . $row['photo_path'])) {
            unlink(__DIR__ . '/' . $row['photo_path']);
        }
        $pdo->exec("DELETE FROM tbl_complaints WHERE id=$id");
        $action_msg  = 'Complaint #' . $id . ' has been <strong>deleted</strong>.';
        $action_type = 'danger';
    }
}

// ---- Filters ----
$filter_status   = $_GET['status']   ?? 'all';
$filter_category = $_GET['category'] ?? '';
$filter_search   = trim($_GET['search'] ?? '');

$where    = [];
$params   = [];

if ($filter_status !== 'all') {
    $where[]  = 'status = :status';
    $params[':status'] = $filter_status;
}
if ($filter_category !== '') {
    $where[]  = 'category LIKE :cat';
    $params[':cat'] = '%' . $filter_category . '%';
}
if ($filter_search !== '') {
    $where[]  = '(full_name LIKE :s OR description LIKE :s2 OR location LIKE :s3)';
    $params[':s']  = '%' . $filter_search . '%';
    $params[':s2'] = '%' . $filter_search . '%';
    $params[':s3'] = '%' . $filter_search . '%';
}

$sql = "SELECT * FROM tbl_complaints";
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY date_submitted DESC';

$complaints = [];
$count_all = $count_pending = $count_resolved = 0;

if ($pdo) {
    // Counts (unfiltered)
    $count_all      = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints")->fetchColumn();
    $count_pending  = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'")->fetchColumn();
    $count_resolved = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'")->fetchColumn();

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Unique categories for filter
$all_categories = [];
if ($pdo) {
    $all_categories = $pdo->query("SELECT DISTINCT category FROM tbl_complaints ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
}
?>
<?php
// Try to include the sidebar — if it doesn't exist yet, we skip it gracefully
$sidebar_exists = file_exists(__DIR__ . '/dashboard_sidebar_start.php');
if ($sidebar_exists) include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Management – Barangay Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<style>
    :root {
        --primary:    #1e3a6e;
        --accent:     #f0a500;
        --pending-bg: #fff8ec;
        --pending-bd: #f0a500;
        --pending-tx: #7a5200;
        --resolve-bg: #eaf7ef;
        --resolve-bd: #27ae60;
        --resolve-tx: #145a30;
    }

    body { font-family: 'Segoe UI', system-ui, sans-serif; background: #f4f7fb; }

    /* ── Page Header ── */
    .page-header {
        background: linear-gradient(135deg, #1e3a6e 0%, #2a5298 100%);
        color: #fff;
        border-radius: 14px;
        padding: 28px 32px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 6px 24px rgba(30,58,110,0.18);
    }
    .page-header .header-icon {
        width: 60px; height: 60px; border-radius: 14px;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.75rem; flex-shrink: 0;
    }
    .page-header h2 { margin: 0; font-size: 1.6rem; font-weight: 700; }
    .page-header p  { margin: 4px 0 0; opacity: 0.8; font-size: 0.9rem; }

    /* ── Stat Cards ── */
    .stat-card {
        border-radius: 12px;
        padding: 20px 22px;
        display: flex; align-items: center; gap: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        border: 1.5px solid transparent;
        transition: transform .15s;
    }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card .stat-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
    }
    .stat-card .stat-val { font-size: 1.9rem; font-weight: 800; line-height: 1; }
    .stat-card .stat-lbl { font-size: 0.78rem; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; opacity: 0.65; margin-top: 3px; }

    .stat-all      { background:#fff;               border-color:#dde3ee; }
    .stat-all      .stat-icon { background:#eef2f9; color:var(--primary); }
    .stat-all      .stat-val  { color:var(--primary); }

    .stat-pending  { background:var(--pending-bg);  border-color:var(--pending-bd); }
    .stat-pending  .stat-icon { background:#ffe9b0; color:#c07800; }
    .stat-pending  .stat-val  { color:#c07800; }

    .stat-resolved { background:var(--resolve-bg);  border-color:var(--resolve-bd); }
    .stat-resolved .stat-icon { background:#c0f0d0; color:#1a7a40; }
    .stat-resolved .stat-val  { color:#1a7a40; }

    /* ── Filters ── */
    .filter-bar {
        background:#fff; border-radius:12px;
        padding:18px 22px; margin-bottom:22px;
        box-shadow:0 2px 10px rgba(0,0,0,0.06);
        display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
    }
    .filter-bar .form-control,
    .filter-bar .form-select {
        border-radius:8px; font-size:0.875rem;
        border:1.5px solid #dde3ee;
    }
    .filter-bar .form-control:focus,
    .filter-bar .form-select:focus {
        border-color:var(--primary);
        box-shadow:0 0 0 3px rgba(30,58,110,0.1);
    }
    .btn-filter {
        background:var(--primary); color:#fff;
        border-radius:8px; border:none;
        padding:8px 20px; font-size:0.875rem; font-weight:600;
    }
    .btn-filter:hover { background:#16326a; color:#fff; }
    .btn-filter-clear {
        background:#f0f2f6; color:#555;
        border-radius:8px; border:none;
        padding:8px 16px; font-size:0.875rem;
    }

    /* ── Complaint Cards ── */
    .complaint-list { display:flex; flex-direction:column; gap:16px; }

    .c-card {
        background:#fff; border-radius:14px;
        padding:22px 24px;
        box-shadow:0 2px 12px rgba(0,0,0,0.07);
        border-left:5px solid #dde3ee;
        transition:box-shadow .2s, border-color .2s;
    }
    .c-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.12); }
    .c-card.c-pending  { border-left-color:var(--pending-bd); }
    .c-card.c-resolved { border-left-color:var(--resolve-bd); }

    .c-card-header {
        display:flex; flex-wrap:wrap;
        align-items:flex-start; justify-content:space-between;
        gap:10px; margin-bottom:14px;
    }
    .c-id   { font-size:0.7rem; color:#aaa; font-weight:600; letter-spacing:.08em; text-transform:uppercase; }
    .c-name { font-size:1.05rem; font-weight:700; color:var(--primary); }
    .c-contact { font-size:0.82rem; color:#888; }

    .badge-pending  { background:var(--pending-bg); color:var(--pending-tx); border:1px solid var(--pending-bd); border-radius:6px; padding:4px 10px; font-size:0.75rem; font-weight:700; }
    .badge-resolved { background:var(--resolve-bg); color:var(--resolve-tx); border:1px solid var(--resolve-bd); border-radius:6px; padding:4px 10px; font-size:0.75rem; font-weight:700; }

    .c-meta { display:flex; flex-wrap:wrap; gap:16px; margin-bottom:12px; font-size:0.85rem; color:#555; }
    .c-meta span { display:flex; align-items:center; gap:5px; }

    .c-category-tag {
        display:inline-block;
        background:#eef2f9; color:var(--primary);
        border-radius:6px; padding:3px 10px;
        font-size:0.78rem; font-weight:600;
    }
    .c-description {
        font-size:0.9rem; color:#333;
        border-left:3px solid #dde3ee;
        padding-left:12px; margin:12px 0;
        line-height:1.55;
    }
    .c-remarks {
        background:#f8f9fa; border-radius:8px;
        padding:10px 14px; font-size:0.85rem;
        color:#444; margin-top:10px;
        border:1px solid #e8eaef;
    }
    .c-photo { margin:12px 0; }
    .c-photo img { max-height:160px; border-radius:8px; border:1px solid #dde3ee; cursor:pointer; }

    /* ── Action Buttons ── */
    .c-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:16px; }
    .btn-resolve {
        background:#27ae60; color:#fff; border:none;
        border-radius:8px; padding:7px 18px; font-size:0.85rem; font-weight:600;
        transition:background .15s;
    }
    .btn-resolve:hover { background:#1f8f4e; color:#fff; }
    .btn-set-pending {
        background:#f0a500; color:#fff; border:none;
        border-radius:8px; padding:7px 18px; font-size:0.85rem; font-weight:600;
    }
    .btn-set-pending:hover { background:#c88900; color:#fff; }
    .btn-delete-card {
        background:#e74c3c; color:#fff; border:none;
        border-radius:8px; padding:7px 18px; font-size:0.85rem; font-weight:600;
    }
    .btn-delete-card:hover { background:#c0392b; color:#fff; }
    .btn-view-detail {
        background:#f4f7fb; color:#444; border:1px solid #dde3ee;
        border-radius:8px; padding:7px 16px; font-size:0.85rem; font-weight:600;
    }

    /* ── Empty State ── */
    .empty-state {
        text-align:center; padding:60px 20px;
        color:#aaa;
    }
    .empty-state i { font-size:4rem; display:block; margin-bottom:16px; }

    /* ── Modal tweaks ── */
    .modal-header { background:var(--primary); color:#fff; border-radius:12px 12px 0 0; }
    .modal-header .btn-close { filter:invert(1); }
    .modal-footer { border-top:1px solid #eee; }

    /* ── Filter tab buttons ── */
    .filter-tabs { display:flex; gap:8px; margin-bottom:0; }
    .filter-tab {
        padding:7px 18px; border-radius:8px; border:1.5px solid #dde3ee;
        font-size:0.85rem; font-weight:600; cursor:pointer; background:#fff;
        color:#666; text-decoration:none; transition:all .15s;
    }
    .filter-tab:hover, .filter-tab.active { border-color:var(--primary); background:var(--primary); color:#fff; }
    .filter-tab.tab-pending.active  { border-color:var(--pending-bd); background:var(--pending-bg); color:var(--pending-tx); }
    .filter-tab.tab-resolved.active { border-color:var(--resolve-bd); background:var(--resolve-bg); color:var(--resolve-tx); }

    @media (max-width:600px) {
        .c-card { padding:16px; }
        .page-header { padding:20px; }
    }
    /* ============================================================
   BARANGAY SAN PEDRO — ADMIN DASHBOARD — IMPROVED CSS
   Extends sb-admin-2 with a refined navy + gold civic theme
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

/* ─── THEME TOKENS ──────────────────────────────────────────── */
:root {
    --navy:          #0f2d5a;
    --navy-mid:      #1a4480;
    --navy-light:    #2b5ea7;
    --navy-pale:     #e8eef7;
    --gold:          #c9943a;
    --gold-light:    #e8b86d;
    --gold-pale:     #fdf3e3;
    --teal:          #0d9488;
    --teal-pale:     #e0f2f0;
    --danger:        #dc2626;
    --danger-pale:   #fef2f2;
    --warning:       #d97706;
    --warning-pale:  #fffbeb;
    --success:       #059669;
    --success-pale:  #ecfdf5;
    --cream:         #f7f8fc;
    --white:         #ffffff;
    --text-dark:     #1a1a2e;
    --text-mid:      #4a5568;
    --text-light:    #718096;
    --border:        #e8ecf0;
    --shadow-sm:     0 2px 8px rgba(15,45,90,0.07);
    --shadow-md:     0 6px 24px rgba(15,45,90,0.11);
    --radius:        14px;
    --radius-sm:     10px;
    --transition:    0.22s cubic-bezier(0.4,0,0.2,1);
}

/* ─── GLOBAL ────────────────────────────────────────────────── */
body {
    font-family: 'DM Sans', -apple-system, sans-serif !important;
    background: var(--cream) !important;
    color: var(--text-dark) !important;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'DM Sans', sans-serif !important;
}

/* Section headings */
h4 {
    font-weight: 700 !important;
    font-size: 1.05rem !important;
    color: var(--navy) !important;
    letter-spacing: 0.2px;
    display: flex;
    align-items: center;
    gap: 10px;
}

h4::before {
    content: '';
    display: inline-block;
    width: 4px;
    height: 20px;
    background: linear-gradient(to bottom, var(--gold), var(--gold-light));
    border-radius: 4px;
    flex-shrink: 0;
}

hr {
    border-color: var(--border) !important;
    opacity: 1 !important;
    margin: 0.5rem 0 !important;
}

/* ─── SIDEBAR ───────────────────────────────────────────────── */
.sidebar {
    background: linear-gradient(180deg, var(--navy) 0%, var(--navy-mid) 60%, #153560 100%) !important;
    border-right: none !important;
    box-shadow: 4px 0 24px rgba(15,45,90,0.18);
}

.sidebar-brand {
    padding: 1.6rem 1rem 1.4rem !important;
    background: rgba(0,0,0,0.12) !important;
    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    gap: 10px;
}

.sidebar-brand-text {
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    color: rgba(255,255,255,0.95) !important;
    text-transform: none !important;
    line-height: 1.3;
}



.sidebar-divider {
    border-top-color: rgba(255,255,255,0.08) !important;
    margin: 0.6rem 1rem !important;
}

.sidebar-heading {
    font-size: 0.65rem !important;
    font-weight: 700 !important;
    letter-spacing: 1.8px !important;
    text-transform: uppercase !important;
    color: rgba(255,255,255,0.35) !important;
    padding: 0.8rem 1.2rem 0.4rem !important;
}

/* Sidebar nav links */
.sidebar .nav-item .nav-link {
    color: rgba(255,255,255,0.72) !important;
    font-size: 0.875rem !important;
    font-weight: 400 !important;
    padding: 10px 20px !important;
    border-radius: 0 !important;
    transition: all var(--transition) !important;
    display: flex;
    align-items: center;
    gap: 10px;
    border-left: 3px solid transparent;
}

.sidebar .nav-item .nav-link i,
.sidebar .nav-item .nav-link .bi {
    font-size: 0.95rem;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    color: rgba(255,255,255,0.5);
    transition: color var(--transition);
}

.sidebar .nav-item .nav-link:hover {
    color: var(--white) !important;
    background: rgba(255,255,255,0.07) !important;
    border-left-color: rgba(201,148,58,0.5) !important;
}

.sidebar .nav-item .nav-link:hover i,
.sidebar .nav-item .nav-link:hover .bi {
    color: var(--gold-light);
}

.sidebar .nav-item.active .nav-link,
.sidebar .nav-item .nav-link.active {
    color: var(--white) !important;
    background: rgba(201,148,58,0.15) !important;
    border-left-color: var(--gold) !important;
    font-weight: 500 !important;
}

/* ─── TOPBAR ────────────────────────────────────────────────── */
.topbar {
    background: var(--white) !important;
    box-shadow: 0 2px 16px rgba(15,45,90,0.08) !important;
    border-bottom: 1px solid var(--border) !important;
    padding: 0 20px !important;
    height: 60px;
    align-items: center;
}

.topbar .nav-item .nav-link {
    color: var(--text-mid) !important;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 8px 14px !important;
    border-radius: 8px;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.topbar .nav-item .nav-link:hover {
    background: var(--cream);
    color: var(--navy) !important;
}

/* Username badge in topbar */
.topbar .text-gray-800 {
    color: var(--text-dark) !important;
    font-weight: 500;
}

/* ─── CONTENT WRAPPER ───────────────────────────────────────── */
#content-wrapper {
    background: var(--cream) !important;
}

#content {
    padding-bottom: 2rem;
}

.container-fluid {
    padding: 1.5rem 2rem !important;
}

/* ─── STAT CARDS ────────────────────────────────────────────── */
/* Override sb-admin2 border-left cards */
.card {
    border: none !important;
    border-radius: var(--radius) !important;
    box-shadow: var(--shadow-sm) !important;
    transition: all var(--transition) !important;
    overflow: hidden;
    background: var(--white) !important;
}

.card:hover {
    box-shadow: var(--shadow-md) !important;
    transform: translateY(-3px);
}

.card-body {
    padding: 1.4rem 1.6rem !important;
}

/* Colored top accent instead of left border */
.card.border-left-primary {
    border-top: 3px solid var(--navy-light) !important;
    border-left: none !important;
}

.card.border-left-info {
    border-top: 3px solid var(--teal) !important;
    border-left: none !important;
}

.card.border-left-danger {
    border-top: 3px solid var(--danger) !important;
    border-left: none !important;
}

.card.border-left-warning {
    border-top: 3px solid var(--warning) !important;
    border-left: none !important;
}

.card.border-left-success {
    border-top: 3px solid var(--success) !important;
    border-left: none !important;
}

/* Tinted card backgrounds */
.card.border-left-primary .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--navy-pale)) !important; }
.card.border-left-info    .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--teal-pale))  !important; }
.card.border-left-danger  .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--danger-pale))!important; }
.card.border-left-warning .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--warning-pale))!important; }
.card.border-left-success .card-body { background: linear-gradient(135deg, var(--white) 60%, var(--success-pale))!important; }

/* Card labels */
.text-xs.font-weight-bold.text-primary {
    color: var(--navy-mid) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-info {
    color: var(--teal) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-danger {
    color: var(--danger) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-warning {
    color: var(--warning) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

.text-xs.font-weight-bold.text-success {
    color: var(--success) !important;
    font-size: 0.7rem !important;
    letter-spacing: 1.2px !important;
    font-weight: 700 !important;
}

/* Big number */
.h5.mb-0.font-weight-bold.text-dark {
    font-size: 1.8rem !important;
    font-weight: 800 !important;
    color: var(--text-dark) !important;
    line-height: 1.1;
    font-family: 'DM Sans', sans-serif !important;
}

/* View records link */
.card-body a {
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    color: var(--navy-mid) !important;
    text-decoration: none !important;
    letter-spacing: 0.3px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: gap var(--transition), opacity var(--transition);
}

.card.border-left-info .card-body a    { color: var(--teal)    !important; }
.card.border-left-danger .card-body a  { color: var(--danger)  !important; }
.card.border-left-warning .card-body a { color: var(--warning) !important; }
.card.border-left-success .card-body a { color: var(--success) !important; }

.card-body a:hover { opacity: 0.75; gap: 8px; }

.card-body a::after {
    content: '→';
    font-size: 0.85em;
}

/* Card icon */
.card-body .col-auto i,
.card-body .col-auto .bi {
    opacity: 0.18;
    font-size: 2.4rem !important;
    color: var(--text-dark) !important;
}

.card:hover .card-body .col-auto i,
.card:hover .card-body .col-auto .bi {
    opacity: 0.28;
}

/* ─── CARD SPACING ──────────────────────────────────────────── */
.card-upper-space {
    margin-top: 24px !important;
}

.card-row-gap {
    margin-top: 24px !important;
}

.row {
    row-gap: 0;
}

/* ─── SECTION SEPARATORS ────────────────────────────────────── */
.container-fluid > br + hr {
    border: none !important;
    height: 1px !important;
    background: linear-gradient(to right, transparent, var(--border), transparent) !important;
    margin: 1.5rem 0 !important;
}

/* ─── RESPONSIVE TABLES (other pages) ──────────────────────── */
.table {
    font-size: 0.875rem;
}

.table thead th {
    background: var(--navy);
    color: var(--white);
    font-weight: 600;
    letter-spacing: 0.5px;
    font-size: 0.78rem;
    text-transform: uppercase;
    border: none;
    padding: 12px 16px;
}

.table tbody tr:hover {
    background: var(--navy-pale);
}

.table td, .table th {
    border-color: var(--border);
    vertical-align: middle;
    padding: 10px 16px;
}

/* ─── BUTTONS ───────────────────────────────────────────────── */
.btn-primary {
    background: linear-gradient(135deg, var(--navy), var(--navy-light)) !important;
    border: none !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    letter-spacing: 0.3px;
    box-shadow: 0 3px 10px rgba(15,45,90,0.25) !important;
    transition: all var(--transition) !important;
}

.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(15,45,90,0.3) !important;
}

/* ─── PAGE HEADER (optional) ────────────────────────────────── */
.page-header {
    padding: 1.2rem 0 1.5rem;
    border-bottom: 1px solid var(--border);
    margin-bottom: 1.5rem;
}

.page-header h4 {
    font-size: 1.35rem !important;
}

/* ─── SECTION HEADER CHIPS ──────────────────────────────────── */
.section-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 1rem;
}

.section-label.resident { background: var(--navy-pale); color: var(--navy-mid); }
.section-label.staff    { background: var(--teal-pale);  color: var(--teal);     }
.section-label.complaint{ background: var(--danger-pale);color: var(--danger);   }

/* ─── FOOTER ────────────────────────────────────────────────── */
.sticky-footer {
    background: var(--white) !important;
    border-top: 1px solid var(--border) !important;
    font-size: 0.8rem !important;
    color: var(--text-light) !important;
    padding: 16px 24px !important;
}

/* ─── SCROLLBAR (Webkit) ────────────────────────────────────── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: rgba(15,45,90,0.15); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: rgba(15,45,90,0.28); }

/* ─── ALERTS & BADGES ───────────────────────────────────────── */
.badge-primary { background-color: var(--navy-light) !important; }
.badge-info    { background-color: var(--teal)       !important; }
.badge-danger  { background-color: var(--danger)     !important; }
.badge-warning { background-color: var(--warning)    !important; color: var(--white) !important; }
.badge-success { background-color: var(--success)    !important; }

/* ─── RESPONSIVE ────────────────────────────────────────────── */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem 1.2rem !important;
    }

    .h5.mb-0.font-weight-bold.text-dark {
        font-size: 1.5rem !important;
    }

    .card:hover {
        transform: none;
    }
}
</style>
</head>
<body>

<div class="container-fluid py-4 px-4">

    <!-- ═══ Page Header ═══ -->
    <div class="page-header">
        <div class="header-icon"><i class="bi bi-megaphone-fill"></i></div>
        <div>
            <h2>Complaint Management</h2>
            <p>Review, resolve, or remove resident complaints submitted to the barangay.</p>
        </div>
    </div>

    <!-- ═══ Action Message ═══ -->
    <?php if ($action_msg): ?>
    <div class="alert alert-<?= $action_type ?> alert-dismissible fade show border-0 rounded-3 mb-3 shadow-sm" role="alert">
        <i class="bi bi-<?= $action_type === 'success' ? 'check-circle' : ($action_type === 'warning' ? 'exclamation-circle' : 'trash') ?> me-2"></i>
        <?= $action_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($db_error)): ?>
    <div class="alert alert-danger rounded-3 mb-3">
        <i class="bi bi-database-x me-2"></i>
        <strong>Database Error:</strong> <?= htmlspecialchars($db_error) ?>
        <br><small>Make sure the <code>tbl_complaints</code> table has been created. Import <code>complaints_migration.sql</code>.</small>
    </div>
    <?php endif; ?>

    <!-- ═══ Stat Cards ═══ -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-all">
                <div class="stat-icon"><i class="bi bi-clipboard2-data"></i></div>
                <div>
                    <div class="stat-val"><?= $count_all ?></div>
                    <div class="stat-lbl">Total Complaints</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-pending">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-val"><?= $count_pending ?></div>
                    <div class="stat-lbl">Pending</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="stat-card stat-resolved">
                <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="stat-val"><?= $count_resolved ?></div>
                    <div class="stat-lbl">Resolved</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Filter Bar ═══ -->
    <form method="GET" class="filter-bar align-items-end">
        <!-- Quick tab filters -->
        <div class="filter-tabs me-2">
            <a href="?status=all"      class="filter-tab <?= $filter_status === 'all'      ? 'active' : '' ?>">All</a>
            <a href="?status=pending"  class="filter-tab tab-pending  <?= $filter_status === 'pending'  ? 'active' : '' ?>"><i class="bi bi-clock me-1"></i>Pending</a>
            <a href="?status=resolved" class="filter-tab tab-resolved <?= $filter_status === 'resolved' ? 'active' : '' ?>"><i class="bi bi-check-circle me-1"></i>Resolved</a>
        </div>

        <div style="flex:1; min-width:180px;">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;">SEARCH</label>
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Name, description, location…"
                   value="<?= htmlspecialchars($filter_search) ?>">
        </div>

        <div style="min-width:160px;">
            <label class="form-label mb-1" style="font-size:.75rem;font-weight:600;">CATEGORY</label>
            <select name="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <?php foreach ($all_categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $filter_category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
        <button type="submit" class="btn-filter"><i class="bi bi-search me-1"></i> Filter</button>
        <a href="admn_complaints.php" class="btn-filter-clear">Clear</a>
    </form>

    <!-- ═══ Complaint List ═══ -->
    <?php if (empty($complaints)): ?>
    <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <h5>No complaints found</h5>
        <p>There are no complaints matching the current filter.</p>
    </div>
    <?php else: ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:.85rem;color:#888;">
            Showing <strong><?= count($complaints) ?></strong> complaint<?= count($complaints) !== 1 ? 's' : '' ?>
        </span>
    </div>

    <div class="complaint-list">
    <?php foreach ($complaints as $c):
        $is_pending  = $c['status'] === 'pending';
        $card_class  = $is_pending ? 'c-pending' : 'c-resolved';
        $modal_id    = 'modal-' . $c['id'];
    ?>
    <div class="c-card <?= $card_class ?>">

        <!-- Card Header -->
        <div class="c-card-header">
            <div>
                <div class="c-id"># <?= $c['id'] ?> · <?= date('M d, Y · h:i A', strtotime($c['date_submitted'])) ?></div>
                <div class="c-name"><?= htmlspecialchars($c['full_name']) ?></div>
                <?php if ($c['contact_number']): ?>
                <div class="c-contact"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['contact_number']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($is_pending): ?>
                <span class="badge-pending"><i class="bi bi-clock-history me-1"></i>Pending</span>
                <?php else: ?>
                <span class="badge-resolved"><i class="bi bi-check-circle me-1"></i>Resolved</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta Info -->
        <div class="c-meta">
            <span><i class="bi bi-tag"></i> <span class="c-category-tag"><?= htmlspecialchars($c['category']) ?></span></span>
            <?php if ($c['location']): ?>
            <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($c['location']) ?></span>
            <?php endif; ?>
            <?php if ($c['address']): ?>
            <span><i class="bi bi-house"></i> <?= htmlspecialchars($c['address']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Description -->
        <div class="c-description"><?= nl2br(htmlspecialchars($c['description'])) ?></div>

        <!-- Photo thumbnail -->
        <?php if ($c['photo_path'] && file_exists(__DIR__ . '/' . $c['photo_path'])): ?>
        <div class="c-photo">
            <img src="<?= htmlspecialchars($c['photo_path']) ?>"
                 alt="Complaint photo"
                 data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-photo">
            <small class="text-muted d-block mt-1"><i class="bi bi-image me-1"></i>Click photo to enlarge</small>
        </div>
        <?php endif; ?>

        <!-- Admin Remarks (if any) -->
        <?php if ($c['admin_remarks']): ?>
        <div class="c-remarks">
            <strong><i class="bi bi-chat-left-text me-1"></i>Admin Remarks:</strong>
            <?= nl2br(htmlspecialchars($c['admin_remarks'])) ?>
        </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="c-actions">
            <?php if ($is_pending): ?>
            <button class="btn-resolve"
                    data-bs-toggle="modal"
                    data-bs-target="#<?= $modal_id ?>-resolve">
                <i class="bi bi-check-circle me-1"></i> Resolve
            </button>
            <?php else: ?>
            <form method="POST" class="d-inline">
                <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                <button type="submit" name="action_pending" class="btn-set-pending">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Set to Pending
                </button>
            </form>
            <?php endif; ?>

            <button class="btn-delete-card"
                    data-bs-toggle="modal"
                    data-bs-target="#<?= $modal_id ?>-delete">
                <i class="bi bi-trash3 me-1"></i> Delete
            </button>
        </div>
    </div>

    <!-- ───── Resolve Modal ───── -->
    <div class="modal fade" id="<?= $modal_id ?>-resolve" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Resolve Complaint #<?= $c['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">
                        <p class="mb-3">You are about to mark this complaint as <strong>Resolved</strong>.</p>
                        <p class="mb-2 fw-semibold" style="font-size:.85rem;">Complaint from: <span class="text-primary"><?= htmlspecialchars($c['full_name']) ?></span></p>
                        <p class="mb-3" style="font-size:.85rem;color:#666;"><?= htmlspecialchars(substr($c['description'], 0, 120)) ?><?= strlen($c['description']) > 120 ? '…' : '' ?></p>
                        <label class="form-label fw-semibold">Admin Remarks / Action Taken <span style="color:#aaa;font-weight:400">(optional)</span></label>
                        <textarea name="admin_remarks" class="form-control" rows="3"
                                  placeholder="e.g. Grass was cut by the barangay cleanup crew on April 28, 2026."
                        ><?= htmlspecialchars($c['admin_remarks'] ?? '') ?></textarea>
                        <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="action_resolve" class="btn btn-success px-4">
                            <i class="bi bi-check-circle me-1"></i> Mark as Resolved
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ───── Delete Modal ───── -->
    <div class="modal fade" id="<?= $modal_id ?>-delete" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header" style="background:#e74c3c;">
                    <h5 class="modal-title"><i class="bi bi-trash3 me-2"></i>Delete Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:3rem;"></i>
                    <h6 class="mt-3 mb-2">Are you sure?</h6>
                    <p class="text-muted" style="font-size:.875rem;">
                        This will permanently delete complaint <strong>#<?= $c['id'] ?></strong>
                        from <strong><?= htmlspecialchars($c['full_name']) ?></strong>.
                        This cannot be undone.
                    </p>
                </div>
                <div class="modal-footer justify-content-center gap-2">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                        <button type="submit" name="action_delete" class="btn btn-danger px-4">
                            <i class="bi bi-trash3 me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ───── Photo Enlargement Modal ───── -->
    <?php if ($c['photo_path'] && file_exists(__DIR__ . '/' . $c['photo_path'])): ?>
    <div class="modal fade" id="<?= $modal_id ?>-photo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-image me-2"></i>Complaint Photo — #<?= $c['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center" style="background:#111;">
                    <img src="<?= htmlspecialchars($c['photo_path']) ?>"
                         style="max-width:100%; max-height:70vh; object-fit:contain;"
                         alt="Complaint photo">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endforeach; ?>
    </div><!-- /.complaint-list -->
    <?php endif; ?>

</div><!-- /.container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($sidebar_exists) include('dashboard_sidebar_end.php'); ?>
</body>
</html>