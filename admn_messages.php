<?php 
    define('BMIS_ROLE_REQUIRED', 'admin');
require('secure_header.php');
require_once 'classes/main.class.php'; 
$systemObject = new BMISClass();
$userdetails = $systemObject->get_userdata();
$admin_name = isset($userdetails['fname']) ? $userdetails['fname'] . ' ' . $userdetails['lname'] : 'Admin';

// ---- Handle: Verify (approve) resident ----
if (isset($_POST['approve_resident'])) {
    $id_resident = (int)$_POST['id_resident'];
    $id_upload   = (int)$_POST['id_upload'];
    if ($systemObject->approveResidentVerification($id_resident, $id_upload, $admin_name)) {
        header("Location: admn_messages.php?status=approved");
    } else {
        header("Location: admn_messages.php?status=error");
    }
    exit();if (isset($_POST['delete_upload'])) {
    $id_upload = $_POST['id_upload'];
    if ($bmis->delete_upload_record($id_upload)) {
        echo "<script>alert('Record deleted successfully'); window.location.href='admin_verification.php';</script>";
    } else {
        echo "<script>alert('Error deleting record');</script>";
    }
}
}
if (isset($_POST['delete_upload'])) { // Make sure this is 'delete_upload'
    $id_upload = $_POST['id_upload'];
    $bmis->delete_upload_record($id_upload);
    // Add a redirect to refresh the page and show changes
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// ---- Handle: Reject resident ID ----
if (isset($_POST['reject_resident'])) {
    $id_resident = (int)$_POST['id_resident'];
    $id_upload   = (int)$_POST['id_upload'];
    $reason      = $_POST['reject_reason'] ?? '';
    if ($systemObject->rejectResidentVerification($id_resident, $id_upload, $admin_name, $reason)) {
        header("Location: admn_messages.php?status=rejected");
    } else {
        header("Location: admn_messages.php?status=error");
    }
    exit();
}

// ---- Handle: Delete message ----
if (isset($_POST['delete_msg'])) {
    $id = $_POST['id_admin_msg'];
    $systemObject->deleteMessage($id);
    header("Location: admn_messages.php?status=deleted");
    exit();
}

// ---- Fetch data ----
$messages     = $systemObject->viewMessages();
$id_uploads   = $systemObject->getPendingIDUploads();
$pending_count = 0;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $pending_count++;
}
?>
<?php include('dashboard_sidebar_start.php'); ?>
<!DOCTYPE html> 
<html>
<head> 
    <title>Messages - Barangay Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .status-pending  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-approved { background: #d1e7dd; color: #0f5132; border: 1px solid #198754; }
        .status-rejected { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }
        .nav-tabs .nav-link { font-weight: 600; }
        .id-preview img { max-width: 100%; max-height: 350px; border-radius: 10px; border: 1px solid #dee2e6; }
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

<?php if(isset($_GET['status'])): ?>
<div class="alert alert-<?= $_GET['status'] === 'approved' ? 'success' : ($_GET['status'] === 'rejected' ? 'warning' : ($_GET['status'] === 'deleted' ? 'info' : 'danger')) ?> alert-dismissible fade show m-3" role="alert">
    <?php
    switch($_GET['status']) {
        case 'approved': echo '&#x2705; Resident account has been verified successfully.'; break;
        case 'rejected': echo '&#x274C; ID submission has been rejected. Resident was notified.'; break;
        case 'deleted':  echo '&#x1F5D1; Message deleted.'; break;
        default:         echo 'An error occurred. Please try again.';
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container my-4">
    <h2 class="fw-bold mb-4 text-center">Resident Messages &amp; Verification</h2>

    <!-- TABS -->
    <ul class="nav nav-tabs mb-4" id="adminMsgTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify-panel" type="button">
                <i class="bi bi-shield-check me-1"></i> ID Verifications
                <?php if ($pending_count > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $pending_count ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-panel" type="button">
                <i class="bi bi-chat-dots-fill me-1"></i> Resident Messages
                <?php if (count($messages) > 0): ?>
                    <span class="badge bg-primary ms-1"><?= count($messages) ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB 1: ID VERIFICATIONS -->
        <div class="tab-pane fade show active" id="verify-panel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 rounded-top-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-card-image me-2 text-warning"></i>Resident ID Submissions</h5>
                    <small class="text-muted">Review and approve or reject resident valid ID uploads to grant account verification.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th class="py-3">Resident Name</th>
                                    <th class="py-3">Contact</th>
                                    <th class="py-3">File Submitted</th>
                                    <th class="py-3">Note</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Action</th>
                                    <th class="py-3"> Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($id_uploads)): ?>
                                <?php foreach ($id_uploads as $up): ?>
                                <tr>
                                    <td class="align-middle fw-bold">
                                        <?= htmlspecialchars($up['fname'] . ' ' . $up['lname']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <small><?= htmlspecialchars($up['email'] ?: $up['phone_number'] ?: '—'); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <a href="uploads/valid_ids/<?= htmlspecialchars($up['file_name']); ?>" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View ID
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted"><?= htmlspecialchars($up['message_note'] ?: '—'); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <small><?= date('M d, Y', strtotime($up['upload_date'])); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($up['status'] === 'approved'): ?>
                                            <span class="badge rounded-pill status-approved px-3">&#x2705; Approved</span>
                                        <?php elseif ($up['status'] === 'rejected'): ?>
                                            <span class="badge rounded-pill status-rejected px-3">&#x274C; Rejected</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill status-pending px-3">&#x23F3; Pending</span>
                                        <?php endif; ?>

                        
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($up['status'] === 'pending'): ?>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <!-- Approve button -->
                                            <form method="POST" onsubmit="return confirm('Approve this resident\'s account?');">
                                                <input type="hidden" name="id_resident" value="<?= $up['id_resident'] ?>">
                                                <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                                <button type="submit" name="approve_resident" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Approve
                                                </button>
                                            </form>
                                            <!-- Reject button opens modal -->
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal<?= $up['id_upload'] ?>">
                                                <i class="bi bi-x-circle-fill me-1"></i> Reject
                                            </button>
                                        </div>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <form method="POST" onsubmit="return confirm('Delete this ID submission record?');">
                                            <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                            <button type="submit" name="delete_upload" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="bi bi-trash-fill me-1"></i> Delete
                                            </button>
                                        </form>
                                </tr>

                                <!-- Reject Modal for this upload -->
                                <div class="modal fade" id="rejectModal<?= $up['id_upload'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form method="POST">
                                                <input type="hidden" name="id_resident" value="<?= $up['id_resident'] ?>">
                                                <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                                <div class="modal-header bg-danger text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Reject ID Submission</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p>You are rejecting the ID submitted by <strong><?= htmlspecialchars($up['fname'] . ' ' . $up['lname']); ?></strong>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Reason for Rejection <span class="text-muted fw-normal">(optional)</span></label>
                                                        <textarea name="reject_reason" class="form-control" rows="3"
                                                                  placeholder="e.g., ID is blurry, expired, or not a government-issued ID..."></textarea>
                                                    </div>
                                                    <p class="text-muted small mb-0">The resident will be notified of this rejection via their messages.</p>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="reject_resident" class="btn btn-danger fw-bold rounded-pill px-4">
                                                        <i class="bi bi-x-circle me-1"></i> Confirm Rejection
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="py-5 text-muted fst-italic">No ID submissions found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RESIDENT MESSAGES -->
        <div class="tab-pane fade" id="messages-panel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <table class="table table-hover text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3">Resident Name</th>
                                <th class="py-3">Message Preview</th>
                                <th class="py-3">Date Sent</th>
                                <th class="py-3">View</th>
                                <th class="py-3">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td class="align-middle fw-bold">
                                            <?= htmlspecialchars($msg['fname'] . ' ' . $msg['lname']); ?>
                                        </td>
                                        <td class="align-middle text-muted">
                                            <?= htmlspecialchars(substr($msg['message_text'], 0, 50)); ?>...
                                        </td>
                                        <td class="align-middle">
                                            <?= date('M d, Y | h:i A', strtotime($msg['date_sent'])); ?>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-info btn-sm rounded-pill px-3 fw-bold"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewMsg<?= $msg['id_admin_msg']; ?>">
                                                <i class="bi bi-eye-fill me-1"></i> View
                                            </button>
                                        </td>
                                        <td class="align-middle">
                                            <form action="delete_message.php" method="POST" onsubmit="return confirm('Delete this message?');">
                                                <input type="hidden" name="id_admin_msg" value="<?= $msg['id_admin_msg']; ?>">
                                                <button type="submit" name="delete_msg" class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash-fill me-1"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- View Message Modal -->
                                    <div class="modal fade" id="viewMsg<?= $msg['id_admin_msg']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header bg-info text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold">Message from <?= htmlspecialchars($msg['fname']); ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <label class="text-muted small fw-bold">FULL NAME</label>
                                                    <p class="h6 mb-3"><?= htmlspecialchars($msg['fname'] . ' ' . $msg['lname']); ?></p>
                                                    <label class="text-muted small fw-bold">DATE RECEIVED</label>
                                                    <p class="h6 mb-3"><?= date('F j, Y, g:i a', strtotime($msg['date_sent'])); ?></p>
                                                    <hr>
                                                    <label class="text-muted small fw-bold">MESSAGE CONTENT</label>
                                                    <div class="bg-light p-3 rounded-3 mt-1">
                                                        <?= nl2br(htmlspecialchars($msg['message_text'])); ?>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="py-5 text-muted fst-italic">No messages found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
