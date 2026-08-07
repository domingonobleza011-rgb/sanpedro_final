<?php
error_reporting(E_ALL ^ E_WARNING);
date_default_timezone_set('Asia/Manila');
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php');
require('classes/main.class.php');
require('classes/resident.class.php');
require_once('classes/conn.php');

$userdetails = $bmis->get_userdata();
$id_resident = $userdetails['id_resident'] ?? 0;

// ── AGE GATE: block residents aged 60+ from youth profiling ──────────────────
$_age_for_gate = 0;
if (!empty($userdetails['bdate'])) {
    $_age_for_gate = (new DateTime())->diff(new DateTime($userdetails['bdate']))->y;
} elseif (!empty($userdetails['age'])) {
    $_age_for_gate = (int)$userdetails['age'];
}
if ($_age_for_gate >= 60) {
    echo "<!DOCTYPE html><html><head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <title>Not Eligible - Barangay San Pedro</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css'>
        </head><body class='bg-light d-flex align-items-center justify-content-center' style='min-height:100vh;'>
        <div class='text-center p-4' style='max-width:420px;'>
            <div style='font-size:4rem;'>&#x1F9D3;</div>
            <h4 class='fw-bold mt-3'>Not Eligible for Youth Profiling</h4>
            <p class='text-muted'>Youth profiling is only available for residents <strong>below 60 years old</strong>.
            Based on your birthdate, your current age is <strong>{$_age_for_gate}</strong>.</p>
            <a href='resident_homepage.php' class='btn btn-primary rounded-pill px-4'>
                <i class='bi bi-house-door-fill me-1'></i> Back to Home
            </a>
        </div>
        </body></html>";
    exit();
}
// ─────────────────────────────────────────────────────────────────────────────

$bmis->create_youth();

// ── RESOLVE YOUTH ID FIRST (always by id_resident only, no name fallback) ────
$youth_id_resolved = null;
$ry = $conn->prepare("SELECT id_youth FROM tbl_youth WHERE id_youth = ? LIMIT 1");
$ry->execute([$id_resident]);
$ry_row = $ry->fetch(PDO::FETCH_ASSOC);
if ($ry_row) {
    $youth_id_resolved = (int)$ry_row['id_youth'];
}

// ── ENROLL IN PROGRAM ─────────────────────────────────────────────────────────
if (isset($_POST['enroll_program'])) {
    $id_program = (int)$_POST['id_program'];

    // No profile = cannot enroll
    if (!$youth_id_resolved) {
        header("Location: resident_youth_profile.php?tab=programs&enroll=noprofile"); exit;
    }

    // Fetch this resident's own youth record directly
    $ychk = $conn->prepare("SELECT id_youth, fname, lname, contact_number FROM tbl_youth WHERE id_youth = ?");
    $ychk->execute([$youth_id_resolved]);
    $youth = $ychk->fetch(PDO::FETCH_ASSOC);

    if (!$youth) {
        header("Location: resident_youth_profile.php?tab=programs&enroll=noprofile"); exit;
    }

    // Program must exist (prevents FK constraint fatal error if id_program is 0/invalid/deleted)
    $pchk = $conn->prepare("SELECT id_program FROM tbl_youth_programs WHERE id_program = ?");
    $pchk->execute([$id_program]);
    if (!$pchk->fetch()) {
        header("Location: resident_youth_profile.php?tab=programs&enroll=invalidprogram"); exit;
    }

    // Duplicate check
    $dup = $conn->prepare("SELECT id_enrollment FROM tbl_youth_enrollment WHERE id_program = ? AND id_youth = ?");
    $dup->execute([$id_program, $youth_id_resolved]);
    if ($dup->fetch()) {
        header("Location: resident_youth_profile.php?tab=programs&enroll=duplicate"); exit;
    }

    $ins = $conn->prepare("INSERT INTO tbl_youth_enrollment (id_program, id_youth, youth_name, contact, status, enrolled_at) VALUES (?,?,?,?,?,?)");
    $ins->execute([
        $id_program,
        $youth_id_resolved,
        $youth['lname'].', '.$youth['fname'],
        $youth['contact_number'],
        'Enrolled',
        date('Y-m-d H:i:s')
    ]);
    header("Location: resident_youth_profile.php?tab=programs&enroll=success"); exit;
}

// ── FETCH BULLETINS ───────────────────────────────────────────────────────────
$type_filter = $_GET['type'] ?? '';
if ($type_filter) {
    $bs = $conn->prepare("SELECT * FROM tbl_youth_bulletin WHERE post_type = ? ORDER BY is_pinned DESC, date_posted DESC");
    $bs->execute([$type_filter]);
} else {
    $bs = $conn->query("SELECT * FROM tbl_youth_bulletin ORDER BY is_pinned DESC, date_posted DESC");
}
$bulletins  = $bs->fetchAll(PDO::FETCH_ASSOC);
$post_types = ['Announcement','Opportunity','Reminder','Achievement','General'];

// ── FETCH PROGRAMS ────────────────────────────────────────────────────────────
$status_filter = $_GET['pstatus'] ?? '';
if ($status_filter) {
    $ps = $conn->prepare("SELECT * FROM tbl_youth_programs WHERE status = ? ORDER BY event_date DESC");
    $ps->execute([$status_filter]);
} else {
    $ps = $conn->query("SELECT * FROM tbl_youth_programs ORDER BY event_date DESC");
}
$programs      = $ps->fetchAll(PDO::FETCH_ASSOC);
$prog_statuses = ['Upcoming','Ongoing','Completed','Cancelled'];

// ── MY ENROLLMENTS (only if profile exists) ───────────────────────────────────
$my_enrolled = [];
if ($youth_id_resolved) {
    $yme = $conn->prepare("SELECT id_program FROM tbl_youth_enrollment WHERE id_youth = ?");
    $yme->execute([$youth_id_resolved]);
    foreach ($yme->fetchAll(PDO::FETCH_ASSOC) as $row)
        $my_enrolled[] = (int)$row['id_program'];
}

$active_tab = $_GET['tab'] ?? 'announcements';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Youth Portal | Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        /* ----- HERO BANNER ----- */
        .youth-hero {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 2.5rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 2rem;
            text-align: center;
        }
        .youth-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .youth-hero .hero-actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 1.25rem;
        }
        .youth-hero .btn-hero {
            border-radius: 40px;
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .youth-hero .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.15);
        }
        .youth-hero .btn-hero-light {
            background: #fff;
            color: #1b74e4;
        }
        .youth-hero .btn-hero-outline {
            background: transparent;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.6);
        }
        .youth-hero .btn-hero-outline:hover {
            background: rgba(255,255,255,0.15);
            border-color: #fff;
        }
        @media (max-width: 576px) {
            .youth-hero h1 {
                font-size: 1.8rem;
            }
            .youth-hero {
                padding: 1.8rem 1rem;
                border-radius: 0 0 24px 24px;
            }
            .youth-hero .btn-hero {
                font-size: 0.85rem;
                padding: 0.5rem 1.2rem;
                width: 100%;
            }
            .youth-hero .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* ----- TABS ----- */
        .portal-tabs {
            display: flex;
            gap: 6px;
            background: #fff;
            border-radius: 14px;
            padding: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1.5px solid #dce0e4;
            margin-bottom: 1.5rem;
        }
        .portal-tab {
            flex: 1;
            text-align: center;
            padding: 0.7rem 0.5rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #65676b;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            background: transparent;
            cursor: pointer;
        }
        .portal-tab i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 3px;
        }
        .portal-tab.active, .portal-tab:hover {
            background: #1b74e4;
            color: #fff;
        }
        @media (max-width: 576px) {
            .portal-tab {
                font-size: 0.7rem;
                padding: 0.5rem 0.3rem;
            }
            .portal-tab i {
                font-size: 1rem;
            }
        }

        /* ----- FILTER CHIPS ----- */
        .chip-filter {
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1.5px solid #dce0e4;
            background: #fff;
            color: #65676b;
            text-decoration: none;
            transition: all 0.15s;
            display: inline-block;
        }
        .chip-filter:hover,
        .chip-filter.active {
            border-color: #1b74e4;
            background: #1b74e4;
            color: #fff;
        }

        /* ----- SECTION PANEL ----- */
        .section-panel {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1.5px solid #dce0e4;
        }

        /* ----- BULLETIN CARDS ----- */
        .bulletin-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1.5px solid #dce0e4;
            position: relative;
            transition: transform 0.15s, box-shadow 0.15s;
            height: 100%;
        }
        .bulletin-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.1);
        }
        .bulletin-card.pinned {
            border-color: #c9943a;
            background: linear-gradient(135deg, #fffdf7, #fff);
        }
        .pin-ribbon {
            position: absolute;
            top: -1px;
            right: 16px;
            background: #c9943a;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.2rem 0.8rem 0.5rem;
            border-radius: 0 0 10px 10px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .b-type-badge {
            display: inline-block;
            border-radius: 8px;
            padding: 0.2rem 0.8rem;
            font-size: 0.72rem;
            font-weight: 800;
            margin-bottom: 0.6rem;
        }
        .t-announcement { background: #ebf5fb; color: #2471a3; }
        .t-opportunity   { background: #e8f0fe; color: #1967d2; }
        .t-reminder      { background: #fdf3e3; color: #c9943a; }
        .t-achievement   { background: #f0eafe; color: #6200ea; }
        .t-general       { background: #f0f4f8; color: #555; }
        .b-title { font-size: 1.05rem; font-weight: 800; color: #1b74e4; margin-bottom: 0.5rem; line-height: 1.4; }
        .b-content { font-size: 0.9rem; color: #444; line-height: 1.7; margin-bottom: 0.75rem; }
        .b-meta { font-size: 0.75rem; color: #65676b; display: flex; flex-wrap: wrap; gap: 12px; }
        .b-meta span { display: flex; align-items: center; gap: 4px; }

        /* ----- PROGRAM CARDS ----- */
        .prog-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border-top: 1.5px solid #dce0e4;
            border-right: 1.5px solid #dce0e4;
            border-bottom: 1.5px solid #dce0e4;
            border-left: 5px solid #dce0e4;
            transition: transform 0.15s, box-shadow 0.15s;
            height: 100%;
        }
        .prog-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.1);
        }
        .prog-card.upcoming   { border-left-color: #1b74e4; }
        .prog-card.ongoing    { border-left-color: #27ae60; }
        .prog-card.completed  { border-left-color: #999; }
        .prog-card.cancelled  { border-left-color: #c0392b; }
        .p-title { font-size: 1.05rem; font-weight: 800; color: #1b74e4; margin: 0.4rem 0 0.3rem; }
        .p-meta { font-size: 0.79rem; color: #65676b; display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 0.6rem; }
        .p-meta span { display: flex; align-items: center; gap: 4px; }
        .p-desc { font-size: 0.875rem; color: #444; line-height: 1.65; margin-bottom: 0.75rem; }
        .p-req { font-size: 0.78rem; color: #888; margin-bottom: 0.75rem; }

        .badge-ptype {
            background: #ebf5fb;
            color: #1b74e4;
            border-radius: 7px;
            padding: 0.2rem 0.7rem;
            font-size: 0.72rem;
            font-weight: 800;
        }
        .badge-upcoming  { background: #ebf5fb; color: #2471a3; border-radius: 7px; padding: 0.2rem 0.7rem; font-size: 0.72rem; font-weight: 800; }
        .badge-ongoing   { background: #eafaf1; color: #27ae60; border-radius: 7px; padding: 0.2rem 0.7rem; font-size: 0.72rem; font-weight: 800; }
        .badge-completed { background: #f0f4f8; color: #555; border-radius: 7px; padding: 0.2rem 0.7rem; font-size: 0.72rem; font-weight: 800; }
        .badge-cancelled { background: #fde8e8; color: #c0392b; border-radius: 7px; padding: 0.2rem 0.7rem; font-size: 0.72rem; font-weight: 800; }

        /* ----- BUTTONS ----- */
        .btn-primary-custom {
            background: #1b74e4;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-primary-custom:hover {
            background: #0a5ecf;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-enrolled {
            background: #ebf5fb;
            color: #1b74e4;
            border: 1.5px solid #1b74e4;
            border-radius: 10px;
            padding: 0.5rem 1.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: default;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-closed {
            background: #f0f4f8;
            color: #999;
            border: 1.5px solid #ccc;
            border-radius: 10px;
            padding: 0.5rem 1.4rem;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: not-allowed;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* ----- TOAST ----- */
        .toast-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 700;
            box-shadow: 0 6px 24px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        }
        .toast-success { background: #1b74e4; color: #fff; }
        .toast-warning { background: #c9943a; color: #fff; }
        .toast-danger  { background: #c0392b; color: #fff; }
        @keyframes slideIn {
            from { transform: translateX(80px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* ----- EMPTY STATE ----- */
        .empty-state { text-align: center; padding: 3rem 1.5rem; color: #65676b; }
        .empty-state i { font-size: 3rem; opacity: 0.3; margin-bottom: 0.75rem; display: block; }

        /* ----- MODAL ----- */
        .modal-content {
            border: none;
            border-radius: 16px;
            overflow: hidden;
        }
        .modal-header-blue {
            background: linear-gradient(135deg, #1b74e4, #0a5ecf);
            color: #fff;
            border: none;
            padding: 1.25rem 1.5rem;
        }
        .modal-header-blue .btn-close { filter: invert(1); }
        .modal-body { background: #f8f9fa; }
        .modal-footer { background: #fff; border-top: 1px solid #dce0e4; }
        .form-control-sm, .form-select-sm {
            border-radius: 8px;
            border: 1px solid #dce0e4;
        }
        .form-control-sm:focus, .form-select-sm:focus {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.15);
        }
        .form-label {
            font-weight: 600;
            font-size: 0.75rem;
            color: #4b4f56;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ----- BACK TO TOP ----- */
        .top-link {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #1b74e4;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: opacity 0.2s, transform 0.2s;
            z-index: 999;
        }
        .top-link.hide {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.8);
        }
        .top-link svg {
            fill: #fff;
            width: 20px;
            height: 12px;
        }
        .top-link:hover {
            background: #0a5ecf;
        }
    </style>
</head>
<body>



<!-- INCLUDE NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

<!-- ── TOAST ALERTS ── -->
<?php if (isset($_GET['enroll'])):
    $tc = 'toast-success'; $ti = 'check-circle'; $tm = '';
    if ($_GET['enroll']==='success')   { $tm = 'Successfully enrolled in the program!'; }
    elseif($_GET['enroll']==='duplicate'){ $tc='toast-warning';$ti='exclamation-circle';$tm='You are already enrolled in this program.'; }
    elseif($_GET['enroll']==='noprofile'){ $tc='toast-danger';$ti='x-circle';$tm='Please complete your Youth Profile first before enrolling.'; }
    elseif($_GET['enroll']==='invalidprogram'){ $tc='toast-danger';$ti='x-circle';$tm='That program is no longer available. Please refresh and try again.'; }
?>
<div class="toast-alert <?= $tc ?>" id="toastAlert">
    <i class="fas fa-<?= $ti ?> me-2"></i><?= $tm ?>
</div>
<script>setTimeout(()=>{const t=document.getElementById('toastAlert');if(t){t.style.opacity='0';t.style.transition='opacity .5s';setTimeout(()=>t.remove(),500);}},3500);</script>
<?php endif; ?>

<!-- ── HERO ── -->
<div class="youth-hero">
    <div class="container">
        <h1><i class="bi bi-megaphone-fill me-2"></i>Youth Portal</h1>
        <p style="opacity:0.9; margin-top:0.25rem;">Your hub for SK announcements, programs, and youth activities</p>
        <div class="hero-actions">
            <button type="button" class="btn-hero btn-hero-light" data-bs-toggle="modal" data-bs-target="#youthProfilingModal">
                <i class="fas fa-id-card me-2"></i>Youth Profiling
            </button>
            <a href="?tab=programs" class="btn-hero btn-hero-outline">
                <i class="bi bi-calendar-event-fill me-2"></i>Browse Programs
            </a>
        </div>
    </div>
</div>

<!-- ── MAIN CONTENT ── -->
<div class="container py-2 pb-5">

    <!-- ── TAB SWITCHER ── -->
    <div class="portal-tabs">
        <a href="?tab=announcements" class="portal-tab <?= $active_tab==='announcements'?'active':'' ?>">
            <i class="bi bi-megaphone-fill"></i> Bulletins
        </a>
        <a href="?tab=programs" class="portal-tab <?= $active_tab==='programs'?'active':'' ?>">
            <i class="bi bi-calendar-event-fill"></i> Programs
        </a>
        <a href="?tab=myenrollments" class="portal-tab <?= $active_tab==='myenrollments'?'active':'' ?>">
            <i class="bi bi-person-check-fill"></i> My Enrollments
        </a>
    </div>

    <!-- ══════════════════════════════════════
         TAB 1 — BULLETINS
    ══════════════════════════════════════ -->
    <?php if ($active_tab === 'announcements'): ?>

    <div class="section-panel mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-0 fw-bold" style="color:#1b74e4;"><i class="bi bi-pin-angle-fill me-2"></i>SK Bulletins</h5>
                <small class="text-muted">Official announcements from your Sangguniang Kabataan</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="?tab=announcements" class="chip-filter <?= !$type_filter?'active':'' ?>">All</a>
                <?php foreach ($post_types as $pt): ?>
                <a href="?tab=announcements&type=<?= urlencode($pt) ?>" class="chip-filter <?= $type_filter===$pt?'active':'' ?>"><?= $pt ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if (empty($bulletins)): ?>
        <div class="section-panel empty-state">
            <i class="bi bi-megaphone"></i>
            <p class="fw-semibold">No bulletins posted yet.</p>
            <small class="text-muted">Check back later for updates.</small>
        </div>
    <?php else:
        $pinned  = array_filter($bulletins, fn($b) => $b['is_pinned']);
        $regular = array_filter($bulletins, fn($b) => !$b['is_pinned']);
        $tc_map  = ['Announcement'=>'t-announcement','Opportunity'=>'t-opportunity','Reminder'=>'t-reminder','Achievement'=>'t-achievement','General'=>'t-general'];
    ?>

    <?php if ($pinned): ?>
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-pin-fill" style="color:#c9943a;"></i>
        <span class="fw-bold" style="font-size:0.8rem;color:#c9943a;text-transform:uppercase;letter-spacing:0.08em;">Pinned</span>
    </div>
    <div class="row g-3 mb-4">
    <?php foreach ($pinned as $b): $tc = $tc_map[$b['post_type']] ?? 't-general'; ?>
    <div class="col-md-6 col-xl-4">
        <div class="bulletin-card pinned">
            <div class="pin-ribbon"><i class="bi bi-pin-fill me-1"></i>Pinned</div>
            <span class="b-type-badge <?= $tc ?>"><?= htmlspecialchars($b['post_type']) ?></span>
            <div class="b-title"><?= htmlspecialchars($b['post_title']) ?></div>
            <div class="b-content"><?= nl2br(htmlspecialchars($b['post_content'])) ?></div>
            <div class="b-meta">
                <span><i class="fas fa-user"></i><?= htmlspecialchars($b['posted_by']) ?></span>
                <span><i class="fas fa-calendar"></i><?= date('M d, Y', strtotime($b['date_posted'])) ?></span>
                <span><i class="fas fa-clock"></i><?= date('h:i A', strtotime($b['date_posted'])) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($regular): ?>
    <?php if ($pinned): ?>
    <div class="d-flex align-items-center gap-2 mb-3">
        <span class="fw-bold" style="font-size:0.8rem;color:#65676b;text-transform:uppercase;letter-spacing:0.08em;">Other Posts</span>
    </div>
    <?php endif; ?>
    <div class="row g-3">
    <?php foreach ($regular as $b): $tc = $tc_map[$b['post_type']] ?? 't-general'; ?>
    <div class="col-md-6 col-xl-4">
        <div class="bulletin-card">
            <span class="b-type-badge <?= $tc ?>"><?= htmlspecialchars($b['post_type']) ?></span>
            <div class="b-title"><?= htmlspecialchars($b['post_title']) ?></div>
            <div class="b-content"><?= nl2br(htmlspecialchars($b['post_content'])) ?></div>
            <div class="b-meta">
                <span><i class="fas fa-user"></i><?= htmlspecialchars($b['posted_by']) ?></span>
                <span><i class="fas fa-calendar"></i><?= date('M d, Y', strtotime($b['date_posted'])) ?></span>
                <span><i class="fas fa-clock"></i><?= date('h:i A', strtotime($b['date_posted'])) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- ══════════════════════════════════════
         TAB 2 — PROGRAMS
    ══════════════════════════════════════ -->
    <?php elseif ($active_tab === 'programs'): ?>

    <div class="section-panel mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-0 fw-bold" style="color:#1b74e4;"><i class="bi bi-calendar-event-fill me-2"></i>Youth Programs</h5>
                <small class="text-muted">Browse and enroll in SK programs and activities</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="?tab=programs" class="chip-filter <?= !$status_filter?'active':'' ?>">All</a>
                <?php foreach ($prog_statuses as $st): ?>
                <a href="?tab=programs&pstatus=<?= urlencode($st) ?>" class="chip-filter <?= $status_filter===$st?'active':'' ?>"><?= $st ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php if (empty($programs)): ?>
        <div class="section-panel empty-state">
            <i class="bi bi-calendar-x"></i>
            <p class="fw-semibold">No programs available at the moment.</p>
            <small class="text-muted">Check back later for upcoming activities.</small>
        </div>
    <?php else: ?>
    <div class="row g-3">
    <?php foreach ($programs as $p):
        $lcst = strtolower($p['status']);
        $badge_map = ['Upcoming'=>'badge-upcoming','Ongoing'=>'badge-ongoing','Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'];
        $badge = $badge_map[$p['status']] ?? 'badge-completed';
        $is_enrolled = in_array($p['id_program'], $my_enrolled);
        $can_enroll  = in_array($p['status'], ['Upcoming','Ongoing']);
    ?>
    <div class="col-md-6 col-xl-4">
        <div class="prog-card <?= $lcst ?>">
            <div class="d-flex justify-content-between align-items-start">
                <span class="badge-ptype"><?= htmlspecialchars($p['program_type']) ?></span>
                <span class="<?= $badge ?>"><?= htmlspecialchars($p['status']) ?></span>
            </div>
            <div class="p-title"><?= htmlspecialchars($p['program_title']) ?></div>
            <div class="p-meta">
                <?php if ($p['venue']): ?><span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($p['venue']) ?></span><?php endif; ?>
                <?php if ($p['event_date']): ?><span><i class="fas fa-calendar"></i><?= date('M d, Y', strtotime($p['event_date'])) ?></span><?php endif; ?>
                <?php if ($p['event_time']): ?><span><i class="fas fa-clock"></i><?= date('h:i A', strtotime($p['event_time'])) ?></span><?php endif; ?>
                <?php if ($p['slots']): ?><span><i class="fas fa-users"></i><?= $p['slots'] ?> slots</span><?php endif; ?>
            </div>
            <?php if ($p['description']): ?><div class="p-desc"><?= htmlspecialchars(substr($p['description'],0,150)).(strlen($p['description'])>150?'…':'') ?></div><?php endif; ?>
            <?php if ($p['requirements']): ?><div class="p-req"><i class="fas fa-clipboard me-1"></i><em><?= htmlspecialchars($p['requirements']) ?></em></div><?php endif; ?>

            <?php if ($is_enrolled): ?>
                <span class="btn-enrolled"><i class="bi bi-check-circle-fill"></i> Enrolled</span>
            <?php elseif (!$can_enroll): ?>
                <span class="btn-closed"><i class="bi bi-x-circle"></i> <?= htmlspecialchars($p['status']) ?></span>
            <?php elseif (!$youth_id_resolved): ?>
                <button class="btn-closed" 
                    data-bs-toggle="modal" data-bs-target="#youthProfilingModal"
                    style="cursor:pointer; border:1.5px solid #c9943a; background:#fdf3e3; color:#c9943a;">
                    <i class="bi bi-person-x-fill"></i> Complete Profile to Enroll
                </button>
            <?php else: ?>
                <button class="btn-primary-custom"
                    data-bs-toggle="modal" data-bs-target="#enrollModal"
                    data-id="<?= $p['id_program'] ?>"
                    data-title="<?= htmlspecialchars($p['program_title'],ENT_QUOTES) ?>"
                    data-date="<?= $p['event_date'] ? date('M d, Y', strtotime($p['event_date'])) : 'TBA' ?>"
                    data-venue="<?= htmlspecialchars($p['venue']??'TBA',ENT_QUOTES) ?>">
                    <i class="bi bi-person-plus-fill"></i> Enroll Now
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════
         TAB 3 — MY ENROLLMENTS
    ══════════════════════════════════════ -->
    <?php elseif ($active_tab === 'myenrollments'): ?>
    <?php
    $me_stmt = $conn->prepare("
        SELECT e.*, p.program_title, p.program_type, p.event_date, p.event_time, p.venue, p.status AS prog_status
        FROM tbl_youth_enrollment e
        JOIN tbl_youth_programs p ON e.id_program = p.id_program
        WHERE e.id_youth = ? ORDER BY e.enrolled_at DESC
    ");
    $me_stmt->execute([$youth_id_resolved ?? 0]);
    $my_programs = $me_stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="section-panel mb-4">
        <h5 class="mb-0 fw-bold" style="color:#1b74e4;"><i class="bi bi-person-check-fill me-2"></i>My Program Enrollments</h5>
        <small class="text-muted">Track your participation in SK youth programs</small>
    </div>

    <?php if (empty($my_programs)): ?>
        <div class="section-panel empty-state">
            <i class="bi bi-clipboard-x"></i>
            <p class="fw-semibold">You haven't enrolled in any programs yet.</p>
            <a href="?tab=programs" class="btn-primary-custom" style="margin:auto;">
                <i class="bi bi-calendar-event-fill"></i> Browse Programs
            </a>
        </div>
    <?php else:
        $total_my   = count($my_programs);
        $attended_c = count(array_filter($my_programs, fn($e)=>$e['status']==='Attended'));
        $enrolled_c = count(array_filter($my_programs, fn($e)=>$e['status']==='Enrolled'));
        $dropped_c  = count(array_filter($my_programs, fn($e)=>$e['status']==='Dropped'));
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="section-panel text-center py-3">
                <div style="font-size:2rem;font-weight:900;color:#1b74e4;"><?= $total_my ?></div>
                <div style="font-size:0.75rem;color:#65676b;font-weight:700;text-transform:uppercase;">Total</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="section-panel text-center py-3">
                <div style="font-size:2rem;font-weight:900;color:#2471a3;"><?= $enrolled_c ?></div>
                <div style="font-size:0.75rem;color:#65676b;font-weight:700;text-transform:uppercase;">Enrolled</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="section-panel text-center py-3">
                <div style="font-size:2rem;font-weight:900;color:#27ae60;"><?= $attended_c ?></div>
                <div style="font-size:0.75rem;color:#65676b;font-weight:700;text-transform:uppercase;">Attended</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="section-panel text-center py-3">
                <div style="font-size:2rem;font-weight:900;color:#c0392b;"><?= $dropped_c ?></div>
                <div style="font-size:0.75rem;color:#65676b;font-weight:700;text-transform:uppercase;">Dropped</div>
            </div>
        </div>
    </div>
    <div class="row g-3">
    <?php foreach ($my_programs as $ep):
        $ep_badge_map = ['Enrolled'=>'badge-upcoming','Attended'=>'badge-ongoing','Dropped'=>'badge-cancelled'];
        $ep_badge = $ep_badge_map[$ep['status']] ?? 'badge-upcoming';
        $ps_badge_map = ['Upcoming'=>'badge-upcoming','Ongoing'=>'badge-ongoing','Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'];
        $ps_badge = $ps_badge_map[$ep['prog_status']] ?? 'badge-completed';
    ?>
    <div class="col-md-6">
        <div class="section-panel" style="border-left:5px solid #1b74e4;">
            <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                <span class="badge-ptype"><?= htmlspecialchars($ep['program_type']) ?></span>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="<?= $ps_badge ?>"><?= htmlspecialchars($ep['prog_status']) ?></span>
                    <span class="<?= $ep_badge ?>">My Status: <?= htmlspecialchars($ep['status']) ?></span>
                </div>
            </div>
            <div class="p-title"><?= htmlspecialchars($ep['program_title']) ?></div>
            <div class="p-meta">
                <?php if ($ep['venue']): ?><span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($ep['venue']) ?></span><?php endif; ?>
                <?php if ($ep['event_date']): ?><span><i class="fas fa-calendar"></i><?= date('M d, Y', strtotime($ep['event_date'])) ?></span><?php endif; ?>
                <?php if ($ep['event_time']): ?><span><i class="fas fa-clock"></i><?= date('h:i A', strtotime($ep['event_time'])) ?></span><?php endif; ?>
                <span><i class="fas fa-user-check"></i>Enrolled: <?= date('M d, Y', strtotime($ep['enrolled_at'])) ?></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>

</div><!-- /container -->

<!-- ══════════════════════════════════════
     YOUTH PROFILING MODAL
══════════════════════════════════════ -->
<div class="modal fade" id="youthProfilingModal" tabindex="-1" aria-labelledby="youthProfilingTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title fw-bold" id="youthProfilingTitle">
                    <i class="fas fa-id-card me-2"></i> Youth Profile Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" class="was-validated" enctype="multipart/form-data">
                <div class="modal-body p-3 p-md-4" style="max-height:75vh;overflow-y:auto;">

                    <!-- Personal Info -->
                    <h6 class="text-primary fw-bold mb-2"><i class="fas fa-user me-1"></i> Personal Information</h6>
                    <hr class="mt-0 mb-3">
                    <div class="row g-2 mb-2">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Last Name</label>
                            <input name="lname" type="text" class="form-control form-control-sm" placeholder="Required"
                                value="<?= isset($userdetails['lname']) ? htmlspecialchars($userdetails['lname']) : '' ?>" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">First Name</label>
                            <input name="fname" type="text" class="form-control form-control-sm" placeholder="Required"
                                value="<?= isset($userdetails['fname']) ? htmlspecialchars($userdetails['fname']) : '' ?>" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input name="mi" type="text" class="form-control form-control-sm" placeholder="Required" required>
                        </div>
                    </div>

                    <!-- Demographics -->
                    <div class="row g-2 mb-2">
                        <div class="col-6 col-md-4">
                            <label class="form-label">Age</label>
                            <?php
                                $computed_age = '';
                                if (!empty($userdetails['bdate'])) {
                                    $bdate_obj = new DateTime($userdetails['bdate']);
                                    $computed_age = (new DateTime())->diff($bdate_obj)->y;
                                } elseif (!empty($userdetails['age'])) {
                                    $computed_age = $userdetails['age'];
                                }
                            ?>
                            <input name="age" type="number" class="form-control form-control-sm"
                                value="<?= htmlspecialchars($computed_age) ?>" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-select form-select-sm" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Male"   <?= (isset($userdetails['sex']) && $userdetails['sex']==='Male')   ? 'selected':'' ?>>Male</option>
                                <option value="Female" <?= (isset($userdetails['sex']) && $userdetails['sex']==='Female') ? 'selected':'' ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Civil Status</label>
                            <select name="civil_status" class="form-select form-select-sm" required>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Solo Parent">Solo Parent</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact & Education -->
                    <h6 class="text-primary fw-bold mt-3 mb-2"><i class="fas fa-at me-1"></i> Contact & Education</h6>
                    <hr class="mt-0 mb-3">
                    <div class="row g-2">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contact Number</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">+63</span>
                                <input type="text" class="form-control" name="contact_number"
                                    placeholder="9XXXXXXXXX" required pattern="\d{10}" inputmode="numeric">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-sm" name="email_address"
                                placeholder="name@example.com" required inputmode="email">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Educational Attainment</label>
                            <input type="text" class="form-control form-control-sm" name="educ_attain"
                                placeholder="e.g. College Undergraduate" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Employment Status</label>
                            <select name="emp_status" class="form-select form-select-sm" required>
                                <option value="Employed">Employed</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Self-Employed">Self-Employed</option>
                                <option value="Student">Student</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Special Skills / Interests</label>
                            <textarea class="form-control form-control-sm" name="skill_name" rows="2"
                                placeholder="e.g. Graphic Design, Public Speaking, Sports" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 gap-2 flex-nowrap">
                    <input name="id_youth" type="hidden" value="<?= $userdetails['id_resident'] ?? '' ?>">
                    <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button name="create_youth" type="submit" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill flex-grow-1 flex-md-grow-0">
                        <i class="fas fa-save me-1"></i> Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── ENROLL CONFIRMATION MODAL ── -->
<div class="modal fade" id="enrollModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Confirm Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">You are about to enroll in:</p>
                <div class="section-panel" style="border-left:4px solid #1b74e4;">
                    <div class="fw-bold" style="color:#1b74e4;font-size:1rem;" id="modal_prog_title">—</div>
                    <div class="p-meta mt-2">
                        <span><i class="fas fa-calendar"></i><span id="modal_prog_date">—</span></span>
                        <span><i class="fas fa-map-marker-alt"></i><span id="modal_prog_venue">—</span></span>
                    </div>
                </div>
                <p class="mt-3 mb-0" style="font-size:0.85rem;color:#65676b;">
                    <i class="bi bi-info-circle me-1"></i>Your profile info will be submitted. The SK admin will track your participation.
                </p>
            </div>
            <div class="modal-footer bg-light px-4 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST">
                    <input type="hidden" name="id_program" id="modal_prog_id">
                    <button type="submit" name="enroll_program" class="btn-primary-custom">
                        <i class="bi bi-check-circle-fill"></i> Confirm Enrollment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Back-to-top visibility
    const topBtn = document.getElementById('js-top');
    if (topBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                topBtn.classList.remove('hide');
            } else {
                topBtn.classList.add('hide');
            }
        });
        topBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Enroll modal data population
    document.getElementById('enrollModal').addEventListener('show.bs.modal', function(e) {
        const b = e.relatedTarget;
        document.getElementById('modal_prog_id').value          = b.dataset.id;
        document.getElementById('modal_prog_title').textContent = b.dataset.title;
        document.getElementById('modal_prog_date').textContent  = b.dataset.date;
        document.getElementById('modal_prog_venue').textContent = b.dataset.venue;
    });
</script>
</body>
</html>