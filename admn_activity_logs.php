<?php
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
require('classes/main.class.php');

$userdetails = $bmis->get_userdata();

// ── FLEXIBLE ADMIN CHECK ─────────────────────────────────────────────
$admin_roles = ['admin', 'Administrator', 'Admin', 'ADMIN', 'administrator'];
$user_role   = $userdetails['role'] ?? '';
$is_admin    = in_array($user_role, $admin_roles);

if (!$is_admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// ── CSRF TOKEN ────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// ── POST HANDLER ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $body = json_decode(file_get_contents('php://input'), true);

    // CSRF check (shared by all POST actions)
    if (!isset($body['csrf_token']) || $body['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'CSRF token mismatch.']);
        exit;
    }

    $action = $body['action'] ?? '';

    // ── BULK DELETE (by checkbox IDs) ─────────────────────────────────
    if ($action === 'bulk_delete') {
        $ids_raw = $body['ids'] ?? [];
        if (!is_array($ids_raw)) {
            echo json_encode(['success' => false, 'message' => 'Invalid IDs format.']);
            exit;
        }
        $ids = array_filter(array_map('intval', $ids_raw), fn($id) => $id > 0);
        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No valid IDs provided.']);
            exit;
        }
        try {
            $conn = $bmis->openConn();
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $conn->prepare("DELETE FROM tbl_activity_log WHERE id_log IN ($placeholders)");
            $stmt->execute(array_values($ids));
            $deleted = $stmt->rowCount();
            $countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_activity_log");
            $countStmt->execute();
            $newTotal = (int)$countStmt->fetchColumn();
            echo json_encode(['success' => true, 'deleted' => $deleted, 'new_total' => $newTotal]);
        } catch (Exception $e) {
            error_log('Bulk delete error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    // ── RANGE COUNT (preview how many will be deleted) ─────────────────
    if ($action === 'range_count') {
        $range = $body['range'] ?? '';
        $cutoff = getRangeCutoff($range);
        if (!$cutoff) {
            echo json_encode(['success' => false, 'message' => 'Invalid range.']);
            exit;
        }
        try {
            $conn  = $bmis->openConn();
            $stmt  = $conn->prepare("SELECT COUNT(*) FROM tbl_activity_log WHERE created_at < ?");
            $stmt->execute([$cutoff]);
            $count = (int)$stmt->fetchColumn();
            echo json_encode(['success' => true, 'count' => $count, 'cutoff' => $cutoff]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    // ── RANGE DELETE ───────────────────────────────────────────────────
    if ($action === 'delete_by_range') {
        $range = $body['range'] ?? '';
        $cutoff = getRangeCutoff($range);
        if (!$cutoff) {
            echo json_encode(['success' => false, 'message' => 'Invalid range.']);
            exit;
        }
        try {
            $conn  = $bmis->openConn();
            $stmt  = $conn->prepare("DELETE FROM tbl_activity_log WHERE created_at < ?");
            $stmt->execute([$cutoff]);
            $deleted = $stmt->rowCount();
            $countStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_activity_log");
            $countStmt->execute();
            $newTotal = (int)$countStmt->fetchColumn();
            echo json_encode(['success' => true, 'deleted' => $deleted, 'new_total' => $newTotal]);
        } catch (Exception $e) {
            error_log('Range delete error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    exit;
}

// ── HELPER: compute cutoff date from range key ────────────────────────
function getRangeCutoff(string $range): string|false {
    $now = new DateTime();
    return match($range) {
        'week'    => (clone $now)->modify('-1 week')->format('Y-m-d H:i:s'),
        'month'   => (clone $now)->modify('-1 month')->format('Y-m-d H:i:s'),
        '3months' => (clone $now)->modify('-3 months')->format('Y-m-d H:i:s'),
        '6months' => (clone $now)->modify('-6 months')->format('Y-m-d H:i:s'),
        'year'    => (clone $now)->modify('-1 year')->format('Y-m-d H:i:s'),
        default   => false,
    };
}

// ── GET: pagination & filters ─────────────────────────────────────────
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20;
$filters  = [
    'search'    => trim($_GET['search']    ?? ''),
    'module'    => trim($_GET['module']    ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to'   => trim($_GET['date_to']   ?? ''),
];

$result      = $bmis->get_activity_logs($filters, $page, $per_page);
$logs        = $result['rows'];
$total       = $result['total'];
$total_pages = max(1, (int)ceil($total / $per_page));

include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrf_token; ?>">
    <title>BMIS - Activity Logs</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue-deep:   #1a2e4d;
            --blue-mid:    #2e5fa3;
            --blue-bright: #4a90d9;
            --blue-glow:   rgba(74,144,217,.16);
            --gold:        #c9a84c;
            --ink:         #0f1825;
            --mist:        #f4f7fb;
            --border:      rgba(46,95,163,.16);
            --card-shadow: 0 20px 60px rgba(26,46,77,.12),0 2px 8px rgba(26,46,77,.07);
            --danger:      #d63031;
            --danger-bg:   #fde8e8;
            --orange:      #e17055;
            --orange-bg:   #fff3ef;
        }
        body { background:var(--mist); font-family:'DM Sans',sans-serif; }
        .page-heading { display:flex; align-items:center; gap:14px; margin-bottom:28px; }
        .page-heading .head-icon {
            width:46px; height:46px; border-radius:12px;
            background:linear-gradient(135deg,var(--blue-deep),var(--blue-mid));
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:18px; box-shadow:0 4px 14px rgba(46,95,163,.3);
        }
        .page-heading h1 { font-family:'Playfair Display',serif; font-size:22px; color:var(--blue-deep); margin:0; }
        .page-heading p  { font-size:12px; color:#7a91b0; margin:2px 0 0; }
        .tab-bar { display:flex; gap:4px; margin-bottom:20px; }
        .tab-bar a {
            padding:9px 18px; border-radius:10px; font-size:13px; font-weight:500;
            color:#7a91b0; text-decoration:none; border:1.5px solid transparent; transition:all .2s;
        }
        .tab-bar a:hover { background:#eef3fb; color:var(--blue-mid); }
        .tab-bar a.active { background:var(--blue-mid); color:#fff; box-shadow:0 4px 12px rgba(46,95,163,.28); }
        .filter-card {
            background:#fff; border-radius:16px; box-shadow:var(--card-shadow);
            padding:20px 24px; margin-bottom:20px;
            display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end;
        }
        .filter-card .fg { display:flex; flex-direction:column; gap:5px; }
        .filter-card label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; color:var(--blue-deep); }
        .filter-card input,
        .filter-card select {
            padding:8px 12px; border:1.5px solid var(--border); border-radius:8px;
            font-family:'DM Sans',sans-serif; font-size:13px; color:var(--ink);
            background:var(--mist); outline:none; transition:border-color .2s,box-shadow .2s;
        }
        .filter-card input:focus,
        .filter-card select:focus { border-color:var(--blue-bright); background:#fff; box-shadow:0 0 0 3px var(--blue-glow); }
        .fg-search { flex:1; min-width:200px; }
        .btn-filter {
            padding:9px 20px; border-radius:8px; border:none;
            background:linear-gradient(135deg,var(--blue-mid),var(--blue-bright));
            color:#fff; font-size:13px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; gap:6px;
            box-shadow:0 4px 12px rgba(46,95,163,.25);
        }
        .btn-reset {
            padding:9px 16px; border-radius:8px; border:1.5px solid var(--border);
            background:transparent; color:#7a91b0; font-size:13px; cursor:pointer;
            text-decoration:none; display:inline-flex; align-items:center;
        }
        .main-card { background:#fff; border-radius:20px; box-shadow:var(--card-shadow); overflow:hidden; }
        .main-card::before {
            content:''; display:block; height:5px;
            background:linear-gradient(90deg,var(--blue-deep),var(--blue-bright),var(--gold));
        }
        .card-header-strip {
            background:linear-gradient(135deg,var(--blue-deep),var(--blue-mid));
            padding:18px 28px; display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:10px;
        }
        .card-header-strip h2 { font-family:'Playfair Display',serif; font-size:18px; color:#fff; margin:0; }
        .card-header-strip .header-right { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .card-header-strip .total-badge { font-size:12px; color:rgba(255,255,255,.6); }

        /* ── Delete by Range button ── */
        .btn-purge {
            padding:7px 15px; border-radius:8px; border:1.5px solid rgba(255,255,255,.3);
            background:rgba(255,255,255,.12); color:#fff; font-size:12px; font-weight:600;
            cursor:pointer; display:inline-flex; align-items:center; gap:7px;
            transition:background .2s,border-color .2s; white-space:nowrap;
        }
        .btn-purge:hover { background:rgba(214,48,49,.55); border-color:rgba(214,48,49,.7); }
        .btn-purge i { font-size:11px; }

        .bulk-bar {
            display:none; align-items:center; gap:12px;
            padding:12px 24px; background:#fff8e1;
            border-bottom:1px solid #ffe082;
            animation:slideDown .2s ease;
        }
        .bulk-bar.visible { display:flex; }
        @keyframes slideDown {
            from { opacity:0; transform:translateY(-6px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .bulk-count { font-size:13px; font-weight:600; color:var(--blue-deep); flex:1; }
        .bulk-count span {
            display:inline-block; background:var(--blue-mid); color:#fff;
            border-radius:20px; padding:1px 9px; font-size:12px; margin-right:4px;
        }
        .btn-delete-bulk {
            padding:8px 18px; border-radius:8px; border:none;
            background:var(--danger); color:#fff; font-size:13px; font-weight:600; cursor:pointer;
            display:inline-flex; align-items:center; gap:7px;
            box-shadow:0 3px 10px rgba(214,48,49,.25); transition:opacity .2s,transform .1s;
        }
        .btn-delete-bulk:hover { opacity:.88; transform:translateY(-1px); }
        .btn-deselect {
            padding:8px 14px; border-radius:8px; border:1.5px solid var(--border);
            background:#fff; color:#7a91b0; font-size:13px; cursor:pointer;
            display:inline-flex; align-items:center; gap:6px;
        }
        .log-table { width:100%; border-collapse:collapse; }
        .log-table thead th {
            padding:11px 16px; font-size:11px; font-weight:600; letter-spacing:.5px;
            text-transform:uppercase; color:#7a91b0;
            background:#f8fafd; border-bottom:1px solid var(--border); white-space:nowrap;
        }
        .log-table thead th:first-child { width:40px; text-align:center; }
        .log-table tbody tr { border-bottom:1px solid #f0f4fb; transition:background .15s; }
        .log-table tbody tr:hover { background:#f5f8fe; }
        .log-table tbody tr.row-selected { background:#eef4ff !important; }
        .log-table td { padding:11px 16px; font-size:13px; color:var(--ink); vertical-align:middle; }
        .log-table td.muted { color:#7a91b0; }
        .log-table td:first-child { text-align:center; }
        .cb-wrap { display:inline-flex; align-items:center; justify-content:center; }
        .cb-wrap input[type="checkbox"] { display:none; }
        .cb-wrap label {
            width:17px; height:17px; border-radius:5px; cursor:pointer;
            border:2px solid var(--border); background:#fff;
            display:flex; align-items:center; justify-content:center;
            transition:background .15s,border-color .15s;
        }
        .cb-wrap input:checked + label { background:var(--blue-mid); border-color:var(--blue-mid); }
        .cb-wrap input:checked + label::after {
            content:''; width:4px; height:8px;
            border:2px solid #fff; border-top:none; border-left:none;
            transform:rotate(45deg) translate(-1px,-1px);
        }
        .badge-module {
            display:inline-block; font-size:10px; font-weight:600;
            padding:2px 9px; border-radius:20px; letter-spacing:.3px;
        }
        .mod-admin    { background:#dbe9ff; color:#185FA5; }
        .mod-resident { background:#e3f3e9; color:#1e6e3e; }
        .mod-document { background:#fef3dc; color:#9a6c00; }
        .mod-blotter  { background:#fde8e8; color:#a32d2d; }
        .mod-settings { background:#ede8fb; color:#4a34a8; }
        .mod-default  { background:#eef1f6; color:#5a6a80; }
        .badge-action {
            display:inline-block; font-size:10px; font-weight:600;
            padding:2px 9px; border-radius:20px; background:#eef3ff; color:#2e5fa3; letter-spacing:.2px;
        }
        .empty-state { text-align:center; padding:60px 20px; color:#a0b4cc; }
        .empty-state i { font-size:40px; margin-bottom:12px; display:block; }
        .empty-state p { font-size:14px; margin:0; }
        .pagination-row {
            padding:16px 24px; display:flex; align-items:center;
            justify-content:space-between; border-top:1px solid var(--border);
            flex-wrap:wrap; gap:10px;
        }
        .pagination-row > span { font-size:12px; color:#7a91b0; }
        .pagination-links { display:flex; gap:4px; }
        .pagination-links a, .pagination-links span {
            padding:5px 11px; border-radius:7px; font-size:12px; font-weight:500;
            border:1.5px solid var(--border); text-decoration:none; color:var(--blue-mid); transition:background .15s;
        }
        .pagination-links a:hover { background:var(--blue-glow); }
        .pagination-links .current { background:var(--blue-mid); color:#fff; border-color:var(--blue-mid); }
        .pagination-links .disabled { color:#c0cdd8; pointer-events:none; }

        /* ── Shared Modal Base ── */
        .modal-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(15,24,37,.45); backdrop-filter:blur(3px);
            z-index:9999; align-items:center; justify-content:center;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:#fff; border-radius:18px; padding:32px 28px;
            max-width:400px; width:92%; text-align:center;
            box-shadow:0 24px 80px rgba(15,24,37,.22); animation:popIn .2s ease;
        }
        @keyframes popIn {
            from { transform:scale(.93); opacity:0; }
            to   { transform:scale(1);   opacity:1; }
        }
        .modal-icon {
            width:56px; height:56px; border-radius:50%;
            background:var(--danger-bg); color:var(--danger);
            font-size:22px; display:flex; align-items:center; justify-content:center;
            margin:0 auto 16px;
        }
        .modal-icon.orange { background:var(--orange-bg); color:var(--orange); }
        .modal-box h3 { font-family:'Playfair Display',serif; color:var(--blue-deep); margin:0 0 8px; font-size:18px; }
        .modal-box p  { font-size:13px; color:#7a91b0; margin:0 0 24px; }
        .modal-box p strong { color:var(--danger); }
        .modal-actions { display:flex; gap:10px; justify-content:center; }
        .btn-modal-cancel {
            padding:10px 22px; border-radius:9px; border:1.5px solid var(--border);
            background:#fff; color:#5a6a80; font-size:13px; font-weight:600; cursor:pointer;
        }
        .btn-modal-confirm {
            padding:10px 22px; border-radius:9px; border:none;
            background:var(--danger); color:#fff; font-size:13px; font-weight:600; cursor:pointer;
            box-shadow:0 4px 12px rgba(214,48,49,.3);
        }

        /* ── Range Delete Modal extras ── */
        .range-modal-box {
            max-width:460px;
        }
        .range-options {
            display:grid; grid-template-columns:1fr 1fr; gap:10px;
            margin:18px 0 20px; text-align:left;
        }
        .range-opt {
            position:relative; cursor:pointer;
        }
        .range-opt input[type="radio"] { display:none; }
        .range-opt label {
            display:flex; align-items:center; gap:10px;
            padding:12px 14px; border-radius:10px; border:2px solid var(--border);
            cursor:pointer; transition:border-color .18s,background .18s;
            font-size:13px; font-weight:500; color:var(--ink);
        }
        .range-opt label .range-icon {
            width:32px; height:32px; border-radius:8px; flex-shrink:0;
            background:var(--mist); display:flex; align-items:center; justify-content:center;
            font-size:14px; color:var(--blue-mid); transition:background .18s,color .18s;
        }
        .range-opt label .range-label { flex:1; }
        .range-opt label .range-label small { display:block; font-size:10px; color:#9ab0c8; font-weight:400; }
        .range-opt input:checked + label {
            border-color:var(--danger); background:#fff5f5;
        }
        .range-opt input:checked + label .range-icon {
            background:var(--danger-bg); color:var(--danger);
        }

        /* preview count pill */
        .range-preview {
            font-size:12px; color:#7a91b0; margin:-8px 0 18px;
            min-height:20px; transition:all .2s;
        }
        .range-preview .preview-pill {
            display:inline-block; background:var(--danger-bg);
            color:var(--danger); font-weight:700; border-radius:20px;
            padding:2px 11px; font-size:12px; margin-left:4px;
        }
        .range-preview .preview-pill.zero { background:#e8f5e9; color:#2e7d32; }
        .range-preview.loading { color:#b0bec5; font-style:italic; }

        .btn-modal-confirm.orange { background:var(--orange); box-shadow:0 4px 12px rgba(225,112,85,.3); }
        .btn-modal-confirm:disabled { opacity:.5; cursor:not-allowed; }

        /* success toast */
        .toast {
            position:fixed; bottom:28px; right:28px; z-index:99999;
            background:var(--blue-deep); color:#fff; border-radius:12px;
            padding:13px 20px; font-size:13px; font-weight:500;
            display:flex; align-items:center; gap:10px;
            box-shadow:0 8px 32px rgba(15,24,37,.22);
            transform:translateY(20px); opacity:0; pointer-events:none;
            transition:transform .3s ease, opacity .3s ease;
        }
        .toast.show { transform:translateY(0); opacity:1; }
        .toast i { color:var(--gold); font-size:15px; }

        @media(max-width:768px) {
            .filter-card { flex-direction:column; }
            .fg-search { min-width:100%; }
            .log-table thead th:nth-child(7),
            .log-table td:nth-child(7) { display:none; }
            .range-options { grid-template-columns:1fr; }
        }
    </style>
</head>
<body id="page-top">
<div class="container-fluid mt-4">
    <div class="tab-bar">
        <a href="admn_activity_logs.php" class="active"><i class="fas fa-tasks mr-1"></i> Activity Logs</a>
        <a href="admn_login_history.php"><i class="fas fa-sign-in-alt mr-1"></i> Login History</a>
    </div>
    
    <form method="GET" action="">
        <div class="filter-card">
            <div class="fg fg-search">
                <label>Search</label>
                <input type="text" name="search" placeholder="Name, action, description…" value="<?php echo htmlspecialchars($filters['search']); ?>">
            </div>
            <div class="fg">
                <label>Module</label>
                <select name="module">
                    <option value="">All Modules</option>
                    <?php foreach(['Admin','Resident','Document','Blotter','Settings','Announcement','Staff'] as $m): ?>
                    <option value="<?php echo $m; ?>" <?php echo $filters['module']===$m?'selected':''; ?>><?php echo $m; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="fg">
                <label>Date From</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>">
            </div>
            <div class="fg">
                <label>Date To</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>">
            </div>
            <div class="fg">
                <label>&nbsp;</label>
                <div style="display:flex;gap:8px;">
                    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
                    <a href="admn_activity_logs.php" class="btn-reset">Clear</a>
                </div>
            </div>
        </div>
    </form>

    <div class="main-card">
        <div class="card-header-strip">
            <h2><i class="fas fa-list-alt mr-2"></i>Activity Log</h2>
            <div class="header-right">
                <span class="total-badge" id="totalRecordsSpan"><?php echo number_format($total); ?> records</span>
                <?php if($is_admin): ?>
                <button type="button" class="btn-purge" id="btnOpenRangeModal">
                    <i class="fas fa-calendar-times"></i> Delete by Range
                </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if(empty($logs)): ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>No activity logs found<?php echo array_filter($filters)?' matching your filters.' :' yet.'; ?></p>
        </div>
        <?php else: ?>

        <div class="bulk-bar" id="bulkBar">
            <div class="bulk-count"><span id="selectedCount">0</span> row(s) selected</div>
            <button type="button" class="btn-deselect" id="btnDeselect">
                <i class="fas fa-times"></i> Deselect All
            </button>
            <button type="button" class="btn-delete-bulk" id="btnDeleteSelected">
                <i class="fas fa-trash-alt"></i> Delete Selected
            </button>
        </div>

        <div style="overflow-x:auto;">
        <table class="log-table" id="logTable">
            <thead>
                <tr>
                    <th>
                        <div class="cb-wrap">
                            <input type="checkbox" id="chkAll">
                            <label for="chkAll"></label>
                        </div>
                    </th>
                    <th>#</th>
                    <th>Admin</th>
                    <th>Module</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Date &amp; Time</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $row_num = ($page - 1) * $per_page + 1;
            foreach($logs as $log):
                $mod_class = match(strtolower($log['module'])) {
                    'admin'    => 'mod-admin',
                    'resident' => 'mod-resident',
                    'document' => 'mod-document',
                    'blotter'  => 'mod-blotter',
                    'settings' => 'mod-settings',
                    default    => 'mod-default',
                };
                $log_id = (int)$log['id_log'];
            ?>
            <tr data-id="<?php echo $log_id; ?>">
                <td>
                    <div class="cb-wrap">
                        <input type="checkbox" class="row-chk" id="chk<?php echo $log_id; ?>" value="<?php echo $log_id; ?>">
                        <label for="chk<?php echo $log_id; ?>"></label>
                    </div>
                </td>
                <td class="muted"><?php echo $row_num++; ?></td>
                <td>
                    <div style="font-weight:600;"><?php echo htmlspecialchars($log['admin_name']); ?></div>
                    <div style="font-size:11px;color:#9ab0c8;"><?php echo htmlspecialchars($log['role']); ?></div>
                </td>
                <td><span class="badge-module <?php echo $mod_class; ?>"><?php echo htmlspecialchars($log['module']); ?></span></td>
                <td><span class="badge-action"><?php echo htmlspecialchars($log['action']); ?></span></td>
                <td style="max-width:300px;"><?php echo htmlspecialchars($log['description']); ?></td>
                <td class="muted" style="font-size:12px;font-family:monospace;"><?php echo htmlspecialchars($log['ip_address'] ?? '—'); ?></td>
                <td class="muted" style="white-space:nowrap;">
                    <?php
                        $dt = new DateTime($log['created_at']);
                        echo $dt->format('M j, Y') . '<br><span style="font-size:11px;">' . $dt->format('h:i A') . '</span>';
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <div class="pagination-row">
            <span>Showing <?php echo min(($page-1)*$per_page+1, $total); ?>–<?php echo min($page*$per_page, $total); ?> of <?php echo number_format($total); ?></span>
            <div class="pagination-links">
                <?php
                $query_params = array_merge($filters, ['page' => null]);
                $base_url = '?' . http_build_query(array_filter($query_params));
                if($page > 1): ?>
                    <a href="<?php echo $base_url . '&page=' . ($page-1); ?>"><i class="fas fa-chevron-left"></i></a>
                <?php else: ?>
                    <span class="disabled"><i class="fas fa-chevron-left"></i></span>
                <?php endif;

                $start = max(1, $page-2);
                $end   = min($total_pages, $page+2);
                for($i=$start; $i<=$end; $i++):
                    if($i === $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo $base_url . '&page=' . $i; ?>"><?php echo $i; ?></a>
                    <?php endif;
                endfor;

                if($page < $total_pages): ?>
                    <a href="<?php echo $base_url . '&page=' . ($page+1); ?>"><i class="fas fa-chevron-right"></i></a>
                <?php else: ?>
                    <span class="disabled"><i class="fas fa-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ══════════════════════════════════════════════════════
     Modal 1: Bulk Delete (checkbox-selected rows)
════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon"><i class="fas fa-trash-alt"></i></div>
        <h3>Delete Selected Logs?</h3>
        <p>You are about to permanently delete <strong id="modalCount">0</strong> log record(s). This cannot be undone.</p>
        <div class="modal-actions">
            <button class="btn-modal-cancel" id="btnModalCancel">Cancel</button>
            <button class="btn-modal-confirm" id="btnModalConfirm"><i class="fas fa-trash-alt mr-1"></i> Yes, Delete</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════
     Modal 2: Delete by Range
════════════════════════════════════════════════════════ -->
<div class="modal-overlay" id="rangeModal">
    <div class="modal-box range-modal-box">
        <div class="modal-icon orange"><i class="fas fa-calendar-times"></i></div>
        <h3>Delete Logs by Date Range</h3>
        <p style="margin-bottom:6px;">Select a range — all logs <strong style="color:var(--orange);">older than</strong> the chosen period will be permanently removed.</p>

        <div class="range-options">
            <div class="range-opt">
                <input type="radio" name="purge_range" id="r_week" value="week">
                <label for="r_week">
                    <span class="range-icon"><i class="fas fa-calendar-week"></i></span>
                    <span class="range-label">Last Week <small>Older than 7 days</small></span>
                </label>
            </div>
            <div class="range-opt">
                <input type="radio" name="purge_range" id="r_month" value="month">
                <label for="r_month">
                    <span class="range-icon"><i class="fas fa-calendar-alt"></i></span>
                    <span class="range-label">Last Month <small>Older than 30 days</small></span>
                </label>
            </div>
            <div class="range-opt">
                <input type="radio" name="purge_range" id="r_3months" value="3months">
                <label for="r_3months">
                    <span class="range-icon"><i class="fas fa-calendar-check"></i></span>
                    <span class="range-label">Last 3 Months <small>Older than 90 days</small></span>
                </label>
            </div>
            <div class="range-opt">
                <input type="radio" name="purge_range" id="r_6months" value="6months">
                <label for="r_6months">
                    <span class="range-icon"><i class="fas fa-calendar"></i></span>
                    <span class="range-label">Last 6 Months <small>Older than 180 days</small></span>
                </label>
            </div>
            <div class="range-opt" style="grid-column:1/-1;">
                <input type="radio" name="purge_range" id="r_year" value="year">
                <label for="r_year">
                    <span class="range-icon"><i class="fas fa-history"></i></span>
                    <span class="range-label">Last Year <small>Older than 365 days</small></span>
                </label>
            </div>
        </div>

        <div class="range-preview" id="rangePreview">← Select a range to see how many records will be deleted</div>

        <div class="modal-actions">
            <button class="btn-modal-cancel" id="btnRangeCancel">Cancel</button>
            <button class="btn-modal-confirm orange" id="btnRangeConfirm" disabled>
                <i class="fas fa-trash-alt mr-1"></i> Delete These Logs
            </button>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div class="toast" id="toast"><i class="fas fa-check-circle"></i> <span id="toastMsg"></span></div>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const totalSpan = document.getElementById('totalRecordsSpan');

    // ── helpers ────────────────────────────────────────────────────────
    function showToast(msg) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3500);
    }

    function postJSON(body) {
        return fetch('admn_activity_logs.php', {
            method : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body   : JSON.stringify({ ...body, csrf_token: CSRF })
        }).then(r => r.json());
    }

    // ════════════════════════════════════════════════════════════════════
    // SECTION 1: Bulk delete (checkbox rows)
    // ════════════════════════════════════════════════════════════════════
    const chkAll        = document.getElementById('chkAll');
    const bulkBar       = document.getElementById('bulkBar');
    const selectedCount = document.getElementById('selectedCount');
    const modalCount    = document.getElementById('modalCount');
    const deleteModal   = document.getElementById('deleteModal');
    const btnDeselect   = document.getElementById('btnDeselect');
    const btnDelete     = document.getElementById('btnDeleteSelected');
    const btnCancel     = document.getElementById('btnModalCancel');
    const btnConfirm    = document.getElementById('btnModalConfirm');

    if (chkAll) {
        function getChecked() {
            return [...document.querySelectorAll('.row-chk:checked')];
        }

        function updateBulkBar() {
            const checked = getChecked();
            const n = checked.length;
            selectedCount.textContent = n;
            modalCount.textContent    = n;
            bulkBar.classList.toggle('visible', n > 0);
            const all = document.querySelectorAll('.row-chk');
            chkAll.checked       = n === all.length && all.length > 0;
            chkAll.indeterminate = n > 0 && n < all.length;
        }

        chkAll.addEventListener('change', function () {
            document.querySelectorAll('.row-chk').forEach(cb => {
                cb.checked = this.checked;
                cb.closest('tr').classList.toggle('row-selected', this.checked);
            });
            updateBulkBar();
        });

        document.querySelectorAll('.row-chk').forEach(cb => {
            cb.addEventListener('change', function () {
                this.closest('tr').classList.toggle('row-selected', this.checked);
                updateBulkBar();
            });
        });

        btnDeselect.addEventListener('click', function () {
            document.querySelectorAll('.row-chk').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('row-selected');
            });
            chkAll.checked = false;
            chkAll.indeterminate = false;
            updateBulkBar();
        });

        btnDelete.addEventListener('click', function () {
            if (getChecked().length === 0) return;
            deleteModal.classList.add('open');
        });

        btnCancel.addEventListener('click', () => deleteModal.classList.remove('open'));
        deleteModal.addEventListener('click', e => { if (e.target === deleteModal) deleteModal.classList.remove('open'); });

        btnConfirm.addEventListener('click', function () {
            const ids = getChecked().map(cb => cb.value);
            if (!ids.length) return;

            btnConfirm.disabled = true;
            btnConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting…';

            postJSON({ action: 'bulk_delete', ids })
            .then(data => {
                if (data.success) {
                    ids.forEach(id => {
                        const row = document.querySelector(`tr[data-id="${id}"]`);
                        if (row) row.remove();
                    });
                    if (data.new_total !== undefined) {
                        totalSpan.textContent = data.new_total.toLocaleString() + ' records';
                    }
                    deleteModal.classList.remove('open');
                    chkAll.checked = false;
                    chkAll.indeterminate = false;
                    updateBulkBar();
                    showToast(`${data.deleted} log(s) deleted successfully.`);
                    if (document.querySelectorAll('tbody tr').length === 0) location.reload();
                } else {
                    alert(data.message || 'Delete failed. Please try again.');
                }
            })
            .catch(() => alert('Network error. Please try again.'))
            .finally(() => {
                btnConfirm.disabled = false;
                btnConfirm.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Yes, Delete';
            });
        });
    }

    // ════════════════════════════════════════════════════════════════════
    // SECTION 2: Delete by Range
    // ════════════════════════════════════════════════════════════════════
    const btnOpenRange  = document.getElementById('btnOpenRangeModal');
    const rangeModal    = document.getElementById('rangeModal');
    const btnRangeCancel  = document.getElementById('btnRangeCancel');
    const btnRangeConfirm = document.getElementById('btnRangeConfirm');
    const rangePreview    = document.getElementById('rangePreview');

    if (!btnOpenRange) return; // not admin – stop here

    let previewCount  = 0;
    let selectedRange = '';
    let previewTimer  = null;

    btnOpenRange.addEventListener('click', () => {
        // reset state
        document.querySelectorAll('input[name="purge_range"]').forEach(r => r.checked = false);
        rangePreview.className = 'range-preview';
        rangePreview.innerHTML = '← Select a range to see how many records will be deleted';
        btnRangeConfirm.disabled = true;
        selectedRange = '';
        rangeModal.classList.add('open');
    });

    btnRangeCancel.addEventListener('click', () => rangeModal.classList.remove('open'));
    rangeModal.addEventListener('click', e => { if (e.target === rangeModal) rangeModal.classList.remove('open'); });

    // Live preview when a range radio changes
    document.querySelectorAll('input[name="purge_range"]').forEach(radio => {
        radio.addEventListener('change', function () {
            selectedRange = this.value;
            btnRangeConfirm.disabled = true;

            rangePreview.className = 'range-preview loading';
            rangePreview.textContent = 'Counting records…';

            clearTimeout(previewTimer);
            previewTimer = setTimeout(() => {
                postJSON({ action: 'range_count', range: selectedRange })
                .then(data => {
                    if (!data.success) {
                        rangePreview.className = 'range-preview';
                        rangePreview.textContent = 'Could not retrieve count.';
                        return;
                    }
                    previewCount = data.count;
                    rangePreview.className = 'range-preview';
                    if (previewCount === 0) {
                        rangePreview.innerHTML = 'No records found older than this range. <span class="preview-pill zero">0 records</span>';
                        btnRangeConfirm.disabled = true;
                    } else {
                        const cutoffFmt = new Date(data.cutoff).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
                        rangePreview.innerHTML = `Records before <strong>${cutoffFmt}</strong> that will be deleted: <span class="preview-pill">${previewCount.toLocaleString()} records</span>`;
                        btnRangeConfirm.disabled = false;
                    }
                })
                .catch(() => {
                    rangePreview.className = 'range-preview';
                    rangePreview.textContent = 'Network error while counting.';
                });
            }, 300);
        });
    });

    // Confirm range delete
    btnRangeConfirm.addEventListener('click', function () {
        if (!selectedRange || previewCount === 0) return;

        // Extra confirmation for large deletions
        const label = document.querySelector(`input[name="purge_range"]:checked`)
                              ?.closest('.range-opt')
                              ?.querySelector('.range-label')
                              ?.firstChild?.textContent?.trim() ?? selectedRange;

        if (!confirm(`⚠️ You are about to permanently delete ${previewCount.toLocaleString()} log record(s) older than "${label}". This cannot be undone. Continue?`)) return;

        btnRangeConfirm.disabled = true;
        btnRangeConfirm.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Deleting…';

        postJSON({ action: 'delete_by_range', range: selectedRange })
        .then(data => {
            if (data.success) {
                rangeModal.classList.remove('open');
                if (data.new_total !== undefined) {
                    totalSpan.textContent = data.new_total.toLocaleString() + ' records';
                }
                showToast(`${data.deleted.toLocaleString()} log(s) purged successfully.`);
                // Reload after short delay so the toast is visible
                setTimeout(() => location.reload(), 1800);
            } else {
                alert(data.message || 'Delete failed. Please try again.');
                btnRangeConfirm.disabled = false;
                btnRangeConfirm.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Delete These Logs';
            }
        })
        .catch(() => {
            alert('Network error. Please try again.');
            btnRangeConfirm.disabled = false;
            btnRangeConfirm.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Delete These Logs';
        });
    });
})();
</script>
</body>
</html>