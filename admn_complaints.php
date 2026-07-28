<?php
error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
 
require_once 'classes/conn.php'; // gives $conn (PDO)
$pdo = $conn;
 
// ── Load staff list for assignment dropdown ───────────────────
$staff_list = $pdo->query(
    "SELECT id_user, fname, lname, position
     FROM tbl_user
     WHERE role = 'user'
     ORDER BY lname, fname"
)->fetchAll(PDO::FETCH_ASSOC);
 
$admin_name = trim(
    ($_SESSION['userdata']['firstname'] ?? '') . ' ' .
    ($_SESSION['userdata']['surname']   ?? '')
);
 
// ── Handle POST actions ───────────────────────────────────────
$action_msg  = '';
$action_type = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    // Assign
    if (isset($_POST['action_assign'])) {
        $id          = (int)$_POST['complaint_id'];
        $id_user     = (int)$_POST['assigned_to'];
        $staff_row   = $pdo->prepare("SELECT fname, lname FROM tbl_user WHERE id_user = ?");
        $staff_row->execute([$id_user]);
        $sf = $staff_row->fetch(PDO::FETCH_ASSOC);
        $aname = $sf ? trim($sf['fname'] . ' ' . $sf['lname']) : '';
 
        $pdo->prepare("UPDATE tbl_complaints
                       SET assigned_to=?, assigned_name=?, assigned_at=NOW(),
                           assigned_by=?, status='in_progress', date_updated=NOW()
                       WHERE id=?")
            ->execute([$id_user ?: null, $aname, $admin_name, $id]);
 
        $action_msg  = "Complaint #$id assigned to <strong>$aname</strong> and set to <em>In Progress</em>.";
        $action_type = 'info';
    }
 
    // Unassign
    if (isset($_POST['action_unassign'])) {
        $id = (int)$_POST['complaint_id'];
        $pdo->prepare("UPDATE tbl_complaints
                       SET assigned_to=NULL, assigned_name=NULL, assigned_at=NULL,
                           assigned_by=NULL, status='pending', date_updated=NOW()
                       WHERE id=?")
            ->execute([$id]);
        $action_msg  = "Complaint #$id has been unassigned and set back to <em>Pending</em>.";
        $action_type = 'warning';
    }
 
    // Resolve
    if (isset($_POST['action_resolve'])) {
        $id      = (int)$_POST['complaint_id'];
        $remarks = trim($_POST['admin_remarks'] ?? '');
        $pdo->prepare("UPDATE tbl_complaints SET status='resolved', admin_remarks=?, date_updated=NOW() WHERE id=?")
            ->execute([$remarks, $id]);
        $action_msg  = "Complaint #$id marked as <strong>Resolved</strong>.";
        $action_type = 'success';
    }
 
    // Set Pending
    if (isset($_POST['action_pending'])) {
        $id = (int)$_POST['complaint_id'];
        $pdo->prepare("UPDATE tbl_complaints SET status='pending', date_updated=NOW() WHERE id=?")->execute([$id]);
        $action_msg  = "Complaint #$id reverted to <strong>Pending</strong>.";
        $action_type = 'warning';
    }
 
    // Delete
    if (isset($_POST['action_delete'])) {
        $id  = (int)$_POST['complaint_id'];
        $row = $pdo->query("SELECT photo_path FROM tbl_complaints WHERE id=$id")->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['photo_path'] && file_exists(__DIR__ . '/' . $row['photo_path'])) {
            unlink(__DIR__ . '/' . $row['photo_path']);
        }
        $pdo->exec("DELETE FROM tbl_complaints WHERE id=$id");
        $action_msg  = "Complaint #$id permanently deleted.";
        $action_type = 'danger';
    }
}
 
// ── Filters ──────────────────────────────────────────────────
$filter_status   = $_GET['status']   ?? 'all';
$filter_category = $_GET['category'] ?? '';
$filter_search   = trim($_GET['search'] ?? '');
$filter_assigned = $_GET['assigned']  ?? '';
 
$where  = [];
$params = [];
 
if ($filter_status !== 'all') {
    $where[] = 'status = :status';
    $params[':status'] = $filter_status;
}
if ($filter_category !== '') {
    $where[] = 'category LIKE :cat';
    $params[':cat'] = '%' . $filter_category . '%';
}
if ($filter_search !== '') {
    $where[] = '(full_name LIKE :s OR description LIKE :s2 OR location LIKE :s3)';
    $params[':s'] = $params[':s2'] = $params[':s3'] = '%' . $filter_search . '%';
}
if ($filter_assigned === 'me' && !empty($_SESSION['userdata']['id_user'])) {
    $where[] = 'assigned_to = :aid';
    $params[':aid'] = (int)$_SESSION['userdata']['id_user'];
} elseif ($filter_assigned === 'unassigned') {
    $where[] = 'assigned_to IS NULL';
}
 
$sql = 'SELECT * FROM tbl_complaints';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY date_submitted DESC';
 
$count_all        = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints")->fetchColumn();
$count_pending    = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='pending'")->fetchColumn();
$count_inprogress = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='in_progress'")->fetchColumn();
$count_resolved   = (int)$pdo->query("SELECT COUNT(*) FROM tbl_complaints WHERE status='resolved'")->fetchColumn();
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
$all_categories = $pdo->query("SELECT DISTINCT category FROM tbl_complaints ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
 
$sidebar_exists = file_exists(__DIR__ . '/dashboard_sidebar_start.php');
if ($sidebar_exists) include('dashboard_sidebar_start.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complaint Management – Barangay Admin</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');
:root {
    --navy:#0f2d5a; --navy-mid:#1a4480; --navy-light:#2b5ea7; --navy-pale:#e8eef7;
    --gold:#c9943a; --gold-light:#e8b86d; --gold-pale:#fdf3e3;
    --teal:#0d9488; --teal-pale:#e0f2f0;
    --danger:#dc2626; --danger-pale:#fef2f2;
    --warning:#d97706; --warning-pale:#fffbeb;
    --success:#059669; --success-pale:#ecfdf5;
    --inprog:#7c3aed; --inprog-pale:#f5f3ff; --inprog-bd:#a78bfa;
    --cream:#f7f8fc; --border:#e8ecf0;
    --shadow-sm:0 2px 8px rgba(15,45,90,.07); --shadow-md:0 6px 24px rgba(15,45,90,.11);
    --radius:14px; --radius-sm:10px;
}
body { font-family:'DM Sans',system-ui,sans-serif !important; background:var(--cream) !important; color:#1a1a2e !important; }
/* ── Sidebar theme ── */
.sidebar { background:linear-gradient(180deg,var(--navy) 0%,var(--navy-mid) 60%,#153560 100%) !important; box-shadow:4px 0 24px rgba(15,45,90,.18); }
.sidebar-brand { padding:1.6rem 1rem 1.4rem !important; background:rgba(0,0,0,.12) !important; border-bottom:1px solid rgba(255,255,255,.08) !important; }
.sidebar-brand-text { font-family:'DM Sans',sans-serif !important; font-size:.82rem !important; font-weight:600 !important; color:rgba(255,255,255,.95) !important; text-transform:none !important; }
.sidebar-divider { border-top-color:rgba(255,255,255,.08) !important; margin:.6rem 1rem !important; }
.sidebar-heading { font-size:.65rem !important; font-weight:700 !important; letter-spacing:1.8px !important; text-transform:uppercase !important; color:rgba(255,255,255,.35) !important; padding:.8rem 1.2rem .4rem !important; }
.sidebar .nav-item .nav-link { color:rgba(255,255,255,.72) !important; font-size:.875rem !important; padding:10px 20px !important; border-left:3px solid transparent; transition:all .22s; display:flex; align-items:center; gap:10px; }
.sidebar .nav-item .nav-link:hover { color:#fff !important; background:rgba(255,255,255,.07) !important; border-left-color:rgba(201,148,58,.5) !important; }
.sidebar .nav-item.active .nav-link,.sidebar .nav-item .nav-link.active { color:#fff !important; background:rgba(201,148,58,.15) !important; border-left-color:var(--gold) !important; font-weight:500 !important; }
.topbar { background:#fff !important; box-shadow:0 2px 16px rgba(15,45,90,.08) !important; border-bottom:1px solid var(--border) !important; padding:0 20px !important; height:60px; }
#content-wrapper { background:var(--cream) !important; }
.container-fluid { padding:1.5rem 2rem !important; }
/* ── Page header ── */
.page-header { background:linear-gradient(135deg,#1e3a6e 0%,#2a5298 100%); color:#fff; border-radius:var(--radius); padding:28px 32px; margin-bottom:28px; display:flex; align-items:center; gap:18px; box-shadow:0 6px 24px rgba(30,58,110,.18); }
.page-header .header-icon { width:60px; height:60px; border-radius:14px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:1.75rem; flex-shrink:0; }
.page-header h2 { margin:0; font-size:1.6rem; font-weight:700; }
.page-header p  { margin:4px 0 0; opacity:.8; font-size:.9rem; }
/* ── Stat cards ── */
.stat-card { border-radius:12px; padding:20px 22px; display:flex; align-items:center; gap:16px; box-shadow:var(--shadow-sm); border:1.5px solid transparent; transition:transform .15s; }
.stat-card:hover { transform:translateY(-2px); }
.stat-card .stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; flex-shrink:0; }
.stat-card .stat-val { font-size:1.9rem; font-weight:800; line-height:1; }
.stat-card .stat-lbl { font-size:.78rem; text-transform:uppercase; letter-spacing:.06em; font-weight:600; opacity:.65; margin-top:3px; }
.stat-all      { background:#fff; border-color:#dde3ee; }
.stat-all      .stat-icon { background:#eef2f9; color:var(--navy); }
.stat-all      .stat-val  { color:var(--navy); }
.stat-pending  { background:var(--warning-pale); border-color:#f0a500; }
.stat-pending  .stat-icon { background:#ffe9b0; color:#c07800; }
.stat-pending  .stat-val  { color:#c07800; }
.stat-inprog   { background:var(--inprog-pale); border-color:var(--inprog-bd); }
.stat-inprog   .stat-icon { background:#ede9fe; color:var(--inprog); }
.stat-inprog   .stat-val  { color:var(--inprog); }
.stat-resolved { background:var(--success-pale); border-color:#27ae60; }
.stat-resolved .stat-icon { background:#c0f0d0; color:#1a7a40; }
.stat-resolved .stat-val  { color:#1a7a40; }
/* ── Filter bar ── */
.filter-bar { background:#fff; border-radius:12px; padding:18px 22px; margin-bottom:22px; box-shadow:var(--shadow-sm); display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
.filter-bar .form-control,.filter-bar .form-select { border-radius:8px; font-size:.875rem; border:1.5px solid #dde3ee; }
.filter-bar .form-control:focus,.filter-bar .form-select:focus { border-color:var(--navy); box-shadow:0 0 0 3px rgba(30,58,110,.1); }
.btn-filter { background:var(--navy); color:#fff; border-radius:8px; border:none; padding:8px 20px; font-size:.875rem; font-weight:600; }
.btn-filter:hover { background:#16326a; color:#fff; }
.btn-filter-clear { background:#f0f2f6; color:#555; border-radius:8px; border:none; padding:8px 16px; font-size:.875rem; }
.filter-tabs { display:flex; gap:8px; flex-wrap:wrap; }
.filter-tab { padding:7px 16px; border-radius:8px; border:1.5px solid #dde3ee; font-size:.82rem; font-weight:600; cursor:pointer; background:#fff; color:#666; text-decoration:none; transition:all .15s; white-space:nowrap; }
.filter-tab:hover { border-color:var(--navy); background:var(--navy); color:#fff; }
.filter-tab.active { border-color:var(--navy); background:var(--navy); color:#fff; }
.filter-tab.tab-pending.active   { border-color:#f0a500; background:#fff8ec; color:#7a5200; }
.filter-tab.tab-inprog.active    { border-color:var(--inprog-bd); background:var(--inprog-pale); color:var(--inprog); }
.filter-tab.tab-resolved.active  { border-color:#27ae60; background:#eaf7ef; color:#145a30; }
/* ── Complaint cards ── */
.complaint-list { display:flex; flex-direction:column; gap:16px; }
.c-card { background:#fff; border-radius:var(--radius); padding:22px 24px; box-shadow:var(--shadow-sm); border-left:5px solid #dde3ee; transition:box-shadow .2s; }
.c-card:hover { box-shadow:var(--shadow-md); }
.c-card.c-pending   { border-left-color:#f0a500; }
.c-card.c-inprog    { border-left-color:var(--inprog); }
.c-card.c-resolved  { border-left-color:#27ae60; }
.c-card-header { display:flex; flex-wrap:wrap; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:14px; }
.c-id   { font-size:.7rem; color:#aaa; font-weight:600; letter-spacing:.08em; text-transform:uppercase; }
.c-name { font-size:1.05rem; font-weight:700; color:var(--navy); }
.c-contact { font-size:.82rem; color:#888; }
.badge-pending  { background:#fff8ec; color:#7a5200;  border:1px solid #f0a500;        border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:700; }
.badge-inprog   { background:var(--inprog-pale); color:var(--inprog); border:1px solid var(--inprog-bd); border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:700; }
.badge-resolved { background:#eaf7ef; color:#145a30; border:1px solid #27ae60;        border-radius:6px; padding:4px 10px; font-size:.75rem; font-weight:700; }
.c-meta { display:flex; flex-wrap:wrap; gap:16px; margin-bottom:12px; font-size:.85rem; color:#555; }
.c-meta span { display:flex; align-items:center; gap:5px; }
.c-category-tag { display:inline-block; background:#eef2f9; color:var(--navy); border-radius:6px; padding:3px 10px; font-size:.78rem; font-weight:600; }
.c-description { font-size:.9rem; color:#333; border-left:3px solid #dde3ee; padding-left:12px; margin:12px 0; line-height:1.55; }
.c-remarks { background:#f8f9fa; border-radius:8px; padding:10px 14px; font-size:.85rem; color:#444; margin-top:10px; border:1px solid #e8eaef; }
/* Assignment box */
.assign-box { background:var(--inprog-pale); border:1.5px solid var(--inprog-bd); border-radius:10px; padding:10px 14px; margin-top:10px; font-size:.85rem; display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.assign-box .assign-label { color:var(--inprog); font-weight:700; }
.assign-box .assign-name  { color:#3b0d8a; font-weight:600; }
.assign-box .assign-meta  { color:#6d4cc9; font-size:.78rem; }
/* Photo */
.c-photo { margin:12px 0; }
.c-photo img { max-height:160px; border-radius:8px; border:1px solid #dde3ee; cursor:pointer; }
/* Action buttons */
.c-actions { display:flex; flex-wrap:wrap; gap:8px; margin-top:16px; }
.btn-assign  { background:var(--inprog); color:#fff; border:none; border-radius:8px; padding:7px 18px; font-size:.85rem; font-weight:600; transition:background .15s; cursor:pointer; }
.btn-assign:hover { background:#6d28d9; color:#fff; }
.btn-resolve { background:#27ae60; color:#fff; border:none; border-radius:8px; padding:7px 18px; font-size:.85rem; font-weight:600; cursor:pointer; }
.btn-resolve:hover { background:#1f8f4e; color:#fff; }
.btn-unassign { background:#f3f4f6; color:#555; border:1.5px solid #d1d5db; border-radius:8px; padding:7px 16px; font-size:.85rem; font-weight:600; cursor:pointer; }
.btn-unassign:hover { background:#e5e7eb; }
.btn-set-pending { background:#f0a500; color:#fff; border:none; border-radius:8px; padding:7px 18px; font-size:.85rem; font-weight:600; cursor:pointer; }
.btn-set-pending:hover { background:#c88900; color:#fff; }
.btn-delete-card { background:#e74c3c; color:#fff; border:none; border-radius:8px; padding:7px 18px; font-size:.85rem; font-weight:600; cursor:pointer; }
.btn-delete-card:hover { background:#c0392b; color:#fff; }
.empty-state { text-align:center; padding:60px 20px; color:#aaa; }
.empty-state i { font-size:4rem; display:block; margin-bottom:16px; }
.modal-header { background:var(--navy); color:#fff; border-radius:12px 12px 0 0; }
.modal-header .btn-close { filter:invert(1); }
@media(max-width:600px) { .c-card { padding:16px; } .page-header { padding:20px; } }
</style>
</head>
<body>
<div class="container-fluid py-4 px-4">
 
    <div class="page-header">
        <div class="header-icon"><i class="bi bi-megaphone-fill"></i></div>
        <div>
            <h2>Complaint Management</h2>
            <p>View, assign, resolve, or delete resident complaints.</p>
        </div>
    </div>
 
    <?php if ($action_msg): ?>
    <div class="alert alert-<?= $action_type ?> alert-dismissible fade show border-0 rounded-3 mb-3 shadow-sm" role="alert">
        <?= $action_msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
 
    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-sm-3">
            <div class="stat-card stat-all">
                <div class="stat-icon"><i class="bi bi-clipboard2-data"></i></div>
                <div><div class="stat-val" id="cmp-stat-all"><?= $count_all ?></div><div class="stat-lbl">Total</div></div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="stat-card stat-pending">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div><div class="stat-val" id="cmp-stat-pending"><?= $count_pending ?></div><div class="stat-lbl">Pending</div></div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="stat-card stat-inprog">
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
                <div><div class="stat-val" id="cmp-stat-inprog"><?= $count_inprogress ?></div><div class="stat-lbl">In Progress</div></div>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="stat-card stat-resolved">
                <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                <div><div class="stat-val" id="cmp-stat-resolved"><?= $count_resolved ?></div><div class="stat-lbl">Resolved</div></div>
            </div>
        </div>
    </div>
 
    <!-- Filter Bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-tabs me-2">
            <a href="?status=all"         class="filter-tab <?= $filter_status==='all'         ? 'active' : '' ?>">All</a>
            <a href="?status=pending"     class="filter-tab tab-pending  <?= $filter_status==='pending'     ? 'active' : '' ?>"><i class="bi bi-clock me-1"></i>Pending</a>
            <a href="?status=in_progress" class="filter-tab tab-inprog   <?= $filter_status==='in_progress' ? 'active' : '' ?>"><i class="bi bi-person-check me-1"></i>In Progress</a>
            <a href="?status=resolved"    class="filter-tab tab-resolved <?= $filter_status==='resolved'    ? 'active' : '' ?>"><i class="bi bi-check-circle me-1"></i>Resolved</a>
            <a href="?assigned=unassigned" class="filter-tab <?= $filter_assigned==='unassigned' ? 'active' : '' ?>"><i class="bi bi-person-x me-1"></i>Unassigned</a>
        </div>
        <div style="flex:1;min-width:180px;">
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
                <option value="<?= htmlspecialchars($cat) ?>" <?= $filter_category===$cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
        <button type="submit" class="btn-filter"><i class="bi bi-search me-1"></i> Filter</button>
        <a href="admn_complaints.php" class="btn-filter-clear">Clear</a>
    </form>
 
    <!-- Complaint List -->
    <?php if (empty($complaints)): ?>
    <div class="empty-state"><i class="bi bi-inbox"></i><h5>No complaints found</h5><p>No complaints match the current filter.</p></div>
    <?php else: ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span style="font-size:.85rem;color:#888;">Showing <strong><?= count($complaints) ?></strong> complaint<?= count($complaints)!==1?'s':'' ?></span>
    </div>
 
    <div class="complaint-list" id="complaint-list-container">
    <?php foreach ($complaints as $c):
        $status     = $c['status'];
        $card_class = $status === 'pending' ? 'c-pending' : ($status === 'in_progress' ? 'c-inprog' : 'c-resolved');
        $modal_id   = 'modal-' . $c['id'];
    ?>
    <div class="c-card <?= $card_class ?>">
        <div class="c-card-header">
            <div>
                <div class="c-id"># <?= $c['id'] ?> · <?= date('M d, Y · h:i A', strtotime($c['date_submitted'])) ?></div>
                <div class="c-name"><?= htmlspecialchars($c['full_name']) ?></div>
                <?php if ($c['contact_number']): ?>
                <div class="c-contact"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($c['contact_number']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($status === 'pending'): ?>
                    <span class="badge-pending"><i class="bi bi-clock-history me-1"></i>Pending</span>
                <?php elseif ($status === 'in_progress'): ?>
                    <span class="badge-inprog"><i class="bi bi-person-check me-1"></i>In Progress</span>
                <?php else: ?>
                    <span class="badge-resolved"><i class="bi bi-check-circle me-1"></i>Resolved</span>
                <?php endif; ?>
            </div>
        </div>
 
        <div class="c-meta">
            <span><i class="bi bi-tag"></i> <span class="c-category-tag"><?= htmlspecialchars($c['category']) ?></span></span>
            <?php if ($c['location']): ?><span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($c['location']) ?></span><?php endif; ?>
            <?php if ($c['address']): ?><span><i class="bi bi-house"></i> <?= htmlspecialchars($c['address']) ?></span><?php endif; ?>
        </div>
 
        <div class="c-description"><?= nl2br(htmlspecialchars($c['description'])) ?></div>
 
        <?php if (!empty($c['photo_path'])): ?>
        <div class="c-photo">
            <img src="<?= htmlspecialchars($c['photo_path']) ?>" alt="Complaint photo"
                 data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-photo">
            <small class="text-muted d-block mt-1"><i class="bi bi-image me-1"></i>Click to enlarge</small>
        </div>
        <?php endif; ?>
 
        <!-- Assignment info box -->
        <?php if (!empty($c['assigned_name'])): ?>
        <div class="assign-box">
            <i class="bi bi-person-check-fill" style="color:var(--inprog);font-size:1.1rem;"></i>
            <div>
                <span class="assign-label">Assigned to: </span>
                <span class="assign-name"><?= htmlspecialchars($c['assigned_name']) ?></span>
                <?php if ($c['assigned_at']): ?>
                <span class="assign-meta"> · <?= date('M d, Y h:i A', strtotime($c['assigned_at'])) ?></span>
                <?php endif; ?>
                <?php if ($c['assigned_by']): ?>
                <span class="assign-meta"> · by <?= htmlspecialchars($c['assigned_by']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
 
        <?php if ($c['admin_remarks']): ?>
        <div class="c-remarks">
            <strong><i class="bi bi-chat-left-text me-1"></i>Admin Remarks:</strong>
            <?= nl2br(htmlspecialchars($c['admin_remarks'])) ?>
        </div>
        <?php endif; ?>
 
        <!-- Action Buttons -->
        <div class="c-actions">
            <?php if ($status === 'pending' || $status === 'in_progress'): ?>
            <button class="btn-assign" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-assign">
                <i class="bi bi-person-plus me-1"></i> <?= empty($c['assigned_name']) ? 'Assign' : 'Reassign' ?>
            </button>
            <?php endif; ?>
 
            <?php if (!empty($c['assigned_name']) && $status !== 'resolved'): ?>
            <form method="POST" class="d-inline">
                <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                <button type="submit" name="action_unassign" class="btn-unassign">
                    <i class="bi bi-person-dash me-1"></i> Unassign
                </button>
            </form>
            <?php endif; ?>
 
            <?php if ($status !== 'resolved'): ?>
            <button class="btn-resolve" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-resolve">
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
 
            <button class="btn-delete-card" data-bs-toggle="modal" data-bs-target="#<?= $modal_id ?>-delete">
                <i class="bi bi-trash3 me-1"></i> Delete
            </button>
        </div>
    </div>
 
    <!-- ── Assign Modal ── -->
    <div class="modal fade" id="<?= $modal_id ?>-assign" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header" style="background:var(--inprog);">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Assign Complaint #<?= $c['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">
                        <p class="mb-1" style="font-size:.85rem;color:#666;">
                            Complaint from <strong><?= htmlspecialchars($c['full_name']) ?></strong> — <?= htmlspecialchars(mb_substr($c['description'], 0, 100)) ?>…
                        </p>
                        <hr class="my-3">
                        <label class="form-label fw-semibold">Assign to Staff Member</label>
                        <select name="assigned_to" class="form-select" required>
                            <option value="">— Select staff —</option>
                            <?php foreach ($staff_list as $sf): ?>
                            <option value="<?= $sf['id_user'] ?>" <?= $c['assigned_to'] == $sf['id_user'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sf['fname'] . ' ' . $sf['lname']) ?>
                                <?php if ($sf['position']): ?>(<?= htmlspecialchars($sf['position']) ?>)<?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="mt-3 p-3 rounded-2" style="background:var(--inprog-pale);font-size:.82rem;color:var(--inprog);">
                            <i class="bi bi-info-circle me-1"></i>
                            Status will automatically change to <strong>In Progress</strong> when assigned.
                        </div>
                        <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="action_assign" class="btn text-white px-4" style="background:var(--inprog);">
                            <i class="bi bi-person-check me-1"></i> Confirm Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
 
    <!-- ── Resolve Modal ── -->
    <div class="modal fade" id="<?= $modal_id ?>-resolve" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Resolve Complaint #<?= $c['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body p-4">
                        <p class="mb-2 fw-semibold" style="font-size:.85rem;">From: <span class="text-primary"><?= htmlspecialchars($c['full_name']) ?></span></p>
                        <p class="mb-3" style="font-size:.85rem;color:#666;"><?= htmlspecialchars(mb_substr($c['description'],0,120)) ?>…</p>
                        <label class="form-label fw-semibold">Admin Remarks / Action Taken <small class="text-muted fw-normal">(optional)</small></label>
                        <textarea name="admin_remarks" class="form-control" rows="3"
                            placeholder="e.g. Addressed by the assigned officer on June 12."><?= htmlspecialchars($c['admin_remarks'] ?? '') ?></textarea>
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
 
    <!-- ── Delete Modal ── -->
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
                    <p class="text-muted" style="font-size:.875rem;">Permanently deletes complaint <strong>#<?= $c['id'] ?></strong> from <strong><?= htmlspecialchars($c['full_name']) ?></strong>. Cannot be undone.</p>
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
 
    <!-- ── Photo Modal ── -->
    <?php if (!empty($c['photo_path'])): ?>
    <div class="modal fade" id="<?= $modal_id ?>-photo" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-image me-2"></i>Photo — #<?= $c['id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center" style="background:#111;">
                    <img src="<?= htmlspecialchars($c['photo_path']) ?>" style="max-width:100%;max-height:70vh;object-fit:contain;" alt="Complaint photo">
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
 
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
 
</div>
 

<script>
(function () {
    var params = new URLSearchParams(window.location.search);
    function buildUrl() {
        return 'ajax_complaints.php?status='   + encodeURIComponent(params.get('status')   || 'all')
                               + '&category=' + encodeURIComponent(params.get('category') || '')
                               + '&search='   + encodeURIComponent(params.get('search')   || '')
                               + '&assigned=' + encodeURIComponent(params.get('assigned') || '');
    }
    function animateNum(el, v) {
        if (!el) return;
        var old = parseInt(el.textContent, 10);
        el.textContent = v;
        if (!isNaN(old) && old !== v) {
            el.style.transition = 'color .4s';
            el.style.color = v > old ? '#059669' : '#dc2626';
            setTimeout(function(){ el.style.color=''; }, 1200);
        }
    }
    function poll() {
        if (document.querySelector('.modal.show')) return;
        fetch(buildUrl())
            .then(function(r){ return r.json(); })
            .then(function(d){
                if (d.error) return;
                animateNum(document.getElementById('cmp-stat-all'),      d.count_all);
                animateNum(document.getElementById('cmp-stat-pending'),  d.count_pending);
                animateNum(document.getElementById('cmp-stat-inprog'),   d.count_inprogress);
                animateNum(document.getElementById('cmp-stat-resolved'), d.count_resolved);
                var c = document.getElementById('complaint-list-container');
                if (c) c.innerHTML = d.html || '<p class="text-muted text-center py-5">No complaints found.</p>';
            }).catch(function(){});
    }
    poll();
    setInterval(poll, 60000);
})();
</script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php if ($sidebar_exists) include('dashboard_sidebar_end.php'); ?>
</body>
</html>