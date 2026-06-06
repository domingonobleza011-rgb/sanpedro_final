<?php
error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php');
require('classes/main.class.php');
require('classes/resident.class.php');
require_once('classes/conn.php');

$userdetails = $bmis->get_userdata();
$id_resident = $userdetails['id_resident'] ?? 0;
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

    // Duplicate check
    $dup = $conn->prepare("SELECT id_enrollment FROM tbl_youth_enrollment WHERE id_program = ? AND id_youth = ?");
    $dup->execute([$id_program, $youth_id_resolved]);
    if ($dup->fetch()) {
        header("Location: resident_youth_profile.php?tab=programs&enroll=duplicate"); exit;
    }

    $ins = $conn->prepare("INSERT INTO tbl_youth_enrollment (id_program, id_youth, youth_name, contact, status) VALUES (?,?,?,?,?)");
    $ins->execute([
        $id_program,
        $youth_id_resolved,
        $youth['lname'].', '.$youth['fname'],
        $youth['contact_number'],
        'Enrolled'
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YOUTH PORTAL — Barangay San Pedro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* ── TOKENS — matches resident_youth_profiling.php blue theme ── */
        .container1
            {
                background-color: #3498DB;
                height: 342px;
                color: black;
                font-family: Arial, Helvetica, sans-serif;
                text-align: center;
            }
        :root {
            --primary:      #3661D5;
            --primary-dark: #3498DB;
            --primary-pale: #ebf5fb;
            --gold:         #c9943a;
            --gold-pale:    #fdf3e3;
            --bg:           #f0f4f8;
            --card-bg:      #ffffff;
            --border:       #d6e4f0;
            --text:         #1a2a3a;
            --muted:        #6888a0;
        }

        body { background: var(--bg); font-family: 'Segoe UI', system-ui, sans-serif; color: var(--text); }
        .mobile-bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0; height: 65px;
            background: #fff; display: flex; justify-content: space-around; align-items: center;
            box-shadow: 0 -2px 10px rgba(0,0,0,.1); z-index: 1050; border-top: 1px solid #dee2e6;
        }
        .mobile-bottom-nav .nav-item { text-decoration:none;color:#6c757d;display:flex;flex-direction:column;align-items:center;font-size:.7rem;font-weight:500; }
        .mobile-bottom-nav .nav-item i { font-size:1.4rem;margin-bottom:2px; }
        .mobile-bottom-nav .nav-item.active-nav { color: var(--primary); }
        @media (max-width:767px) { body { padding-bottom: 80px; } }

        /* ── HERO — same blue gradient as the profiling page ── */
        .page-hero {
            background: var(--primary-dark);
            padding: 36px 24px 30px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .page-hero::before {
            content: '';
            position: absolute; inset: 0;
        }
        .page-hero h1 { font-size: clamp(1.6rem, 4vw, 2.4rem); font-weight: 800; margin: 0; position:relative; }
        .page-hero p  { opacity: .85; margin: 8px 0 0; font-size: .92rem; position:relative; }
        .hero-actions { margin-top: 22px; position: relative; display: flex; justify-content: center; gap: 10px; flex-wrap: wrap; }

        /* ── TABS ── */
        .sk-tabs { display:flex; gap:6px; background:var(--card-bg); border-radius:14px; padding:6px; box-shadow:0 2px 8px rgba(0,0,0,.06); border:1.5px solid var(--border); }
        .sk-tab {
            flex:1; text-align:center; padding:10px 6px; border-radius:10px; font-size:.85rem;
            font-weight:700; color:var(--muted); text-decoration:none; transition:all .2s; border:none; background:transparent; cursor:pointer;
        }
        .sk-tab.active, .sk-tab:hover { background: var(--primary); color:#fff; }
        .sk-tab i { display:block; font-size:1.1rem; margin-bottom:2px; }

        /* ── FILTER CHIPS ── */
        .chip-bar { display:flex; flex-wrap:wrap; gap:8px; }
        .chip-filter {
            padding:5px 14px; border-radius:20px; font-size:.78rem; font-weight:700;
            border:1.5px solid var(--border); background:var(--card-bg); color:var(--muted);
            text-decoration:none; transition:all .15s;
        }
        .chip-filter:hover, .chip-filter.active { border-color:var(--primary); background:var(--primary); color:#fff; }

        /* ── SECTION PANEL ── */
        .section-panel { background:var(--card-bg); border-radius:14px; padding:20px 22px; box-shadow:0 2px 8px rgba(52,152,219,.07); border:1.5px solid var(--border); }

        /* ── BULLETIN CARDS ── */
        .bulletin-card {
            background:var(--card-bg); border-radius:16px; padding:22px;
            box-shadow:0 2px 10px rgba(0,0,0,.06); border:1.5px solid var(--border);
            position:relative; transition:transform .15s, box-shadow .15s;
        }
        .bulletin-card:hover { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
        .bulletin-card.pinned { border-color:var(--gold); background:linear-gradient(135deg,#fffdf7,#fff); }
        .pin-ribbon {
            position:absolute; top:-1px; right:16px; background:var(--gold); color:#fff;
            font-size:.65rem; font-weight:800; padding:3px 10px 6px; border-radius:0 0 10px 10px;
            letter-spacing:.8px; text-transform:uppercase;
        }
        .b-type-badge { display:inline-block; border-radius:8px; padding:3px 11px; font-size:.72rem; font-weight:800; margin-bottom:10px; }
        .t-announcement { background:#ebf5fb; color:#2471a3; }
        .t-opportunity   { background:#e8f0fe; color:#1967d2; }
        .t-reminder      { background:#fdf3e3; color:#c9943a; }
        .t-achievement   { background:#f0eafe; color:#6200ea; }
        .t-general       { background:#f0f4f8; color:#555; }
        .b-title   { font-size:1rem; font-weight:800; color:var(--primary-dark); margin-bottom:8px; line-height:1.4; }
        .b-content { font-size:.875rem; color:#444; line-height:1.7; margin-bottom:12px; }
        .b-meta    { font-size:.74rem; color:var(--muted); display:flex; flex-wrap:wrap; gap:10px; }
        .b-meta span { display:flex; align-items:center; gap:4px; }

        /* ── PROGRAM CARDS ── */
        .prog-card {
            background:var(--card-bg); border-radius:16px; padding:22px;
            box-shadow:0 2px 10px rgba(0,0,0,.06);
            border-top:1.5px solid var(--border); border-right:1.5px solid var(--border); border-bottom:1.5px solid var(--border);
            border-left:5px solid var(--border);
            transition:transform .15s, box-shadow .15s;
        }
        .prog-card:hover   { transform:translateY(-3px); box-shadow:0 8px 28px rgba(0,0,0,.1); }
        .prog-card.upcoming   { border-left-color:var(--primary); }
        .prog-card.ongoing    { border-left-color:#27ae60; }
        .prog-card.completed  { border-left-color:#999; }
        .prog-card.cancelled  { border-left-color:#c0392b; }
        .p-title { font-size:1.05rem; font-weight:800; color:var(--primary-dark); margin:8px 0 6px; }
        .p-meta  { font-size:.79rem; color:var(--muted); display:flex; flex-wrap:wrap; gap:10px; margin-bottom:10px; }
        .p-meta span { display:flex; align-items:center; gap:4px; }
        .p-desc  { font-size:.875rem; color:#444; line-height:1.65; margin-bottom:12px; }
        .p-req   { font-size:.78rem; color:#888; margin-bottom:14px; }

        .badge-ptype     { background:var(--primary-pale); color:var(--primary-dark); border-radius:7px; padding:3px 10px; font-size:.72rem; font-weight:800; }
        .badge-upcoming  { background:#ebf5fb; color:#2471a3; border-radius:7px; padding:3px 10px; font-size:.72rem; font-weight:800; }
        .badge-ongoing   { background:#eafaf1; color:#27ae60; border-radius:7px; padding:3px 10px; font-size:.72rem; font-weight:800; }
        .badge-completed { background:#f0f4f8; color:#555; border-radius:7px; padding:3px 10px; font-size:.72rem; font-weight:800; }
        .badge-cancelled { background:#fde8e8; color:#c0392b; border-radius:7px; padding:3px 10px; font-size:.72rem; font-weight:800; }

        /* ── BUTTONS ── */
        .btn-primary-custom {
            background:var(--primary); color:#fff; border:none; border-radius:10px;
            padding:8px 20px; font-size:.85rem; font-weight:700; cursor:pointer;
            transition:all .2s; display:inline-flex; align-items:center; gap:6px; text-decoration:none;
        }
        .btn-primary-custom:hover { background:var(--primary-dark); color:#fff; transform:translateY(-1px); }
        .btn-enrolled {
            background:var(--primary-pale); color:var(--primary-dark); border:1.5px solid var(--primary);
            border-radius:10px; padding:8px 20px; font-size:.85rem; font-weight:700;
            cursor:default; display:inline-flex; align-items:center; gap:6px;
        }
        .btn-closed {
            background:#f0f4f8; color:#999; border:1.5px solid #ccc;
            border-radius:10px; padding:8px 20px; font-size:.85rem; font-weight:700;
            cursor:not-allowed; display:inline-flex; align-items:center; gap:6px;
        }

        /* ── TOAST ── */
        .toast-alert {
            position:fixed; top:20px; right:20px; z-index:9999;
            padding:14px 22px; border-radius:12px; font-size:.875rem; font-weight:700;
            box-shadow:0 6px 24px rgba(0,0,0,.15); animation:slideIn .3s ease;
        }
        .toast-success { background:var(--primary); color:#fff; }
        .toast-warning { background:var(--gold); color:#fff; }
        .toast-danger  { background:#c0392b; color:#fff; }
        @keyframes slideIn { from{transform:translateX(80px);opacity:0} to{transform:translateX(0);opacity:1} }

        /* ── EMPTY STATE ── */
        .empty-state { text-align:center; padding:50px 20px; color:var(--muted); }
        .empty-state i { font-size:3rem; opacity:.3; margin-bottom:12px; display:block; }

        /* ── MODAL HEADER ── */
        .modal-header-blue { background:var(--primary); color:#fff; border-radius:12px 12px 0 0; }
        .modal-header-blue .btn-close { filter:invert(1); }
    </style>
</head>
<body>

<!-- ── DESKTOP NAVBAR ── -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-md-block shadow">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building-fill me-2"></i> Barangay San Pedro
        </a>
        <div class="d-flex ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-1"><i class="bi bi-house-door-fill me-1"></i> Home</a>
            <a href="resident_announcement.php" class="btn btn-primary me-1"><i class="bi bi-megaphone-fill me-1"></i> Announcements</a>
            <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-person-badge me-1"></i> Profile</a>
            <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="btn btn-primary me-1"><i class="bi bi-shield-lock me-1"></i> Password</a>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- MOBILE BOTTOM NAV (Hidden on Desktop) -->
<div class="mobile-bottom-nav d-md-none">
    <a href="resident_homepage.php" class="nav-item">
        <i class="bi bi-house-door-fill"></i>
        <span>Home</span>
    </a>
    <a href="resident_announcement.php" class="nav-item">
        <i class="bi bi-megaphone-fill"></i>
        <span>News</span>
    </a>
    <a href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-person-badge"></i>
        <span>Profile</span>
    </a>
    <a href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>" class="nav-item">
        <i class="bi bi-shield-lock"></i>
        <span>Pass</span>
    </a>
    <a href="logout.php" class="nav-item text-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span>Exit</span>
    </a>
</div>

<!-- ── TOAST ALERTS ── -->
<?php if (isset($_GET['enroll'])):
    $tc = 'toast-success'; $ti = 'check-circle'; $tm = '';
    if ($_GET['enroll']==='success')   { $tm = 'Successfully enrolled in the program!'; }
    elseif($_GET['enroll']==='duplicate'){ $tc='toast-warning';$ti='exclamation-circle';$tm='You are already enrolled in this program.'; }
    elseif($_GET['enroll']==='noprofile'){ $tc='toast-danger';$ti='x-circle';$tm='Please complete your Youth Profile first before enrolling.'; }
?>
<div class="toast-alert <?= $tc ?>" id="toastAlert">
    <i class="fas fa-<?= $ti ?> me-2"></i><?= $tm ?>
</div>
<script>setTimeout(()=>{const t=document.getElementById('toastAlert');if(t){t.style.opacity='0';t.style.transition='opacity .5s';setTimeout(()=>t.remove(),500);}},3500);</script>
<?php endif; ?>

<!-- ── HERO ── -->
<div class="page-hero">
    <h1><i class="bi bi-megaphone-fill me-2"></i>YOUTH PORTAL</h1>
    <p>Stay updated with the latest SK announcements and youth programs in Barangay San Pedro.</p>
    <div class="hero-actions">
        <!-- Youth Profiling Button -->
        <button type="button"
            class="btn btn-light fw-bold px-4 py-2"
            style="border-radius:10px;color:var(--primary-dark);font-size:.95rem;"
            data-bs-toggle="modal" data-bs-target="#youthProfilingModal">
            <i class="fas fa-id-card me-2"></i>Youth Profiling
        </button>
        <a href="?tab=programs" class="btn btn-outline-light fw-bold px-4 py-2" style="border-radius:10px;font-size:.95rem;">
            <i class="bi bi-calendar-event-fill me-2"></i>Browse Programs
        </a>
    </div>
</div>

<div class="container py-4">

    <!-- ── TAB SWITCHER ── -->
    <div class="sk-tabs mb-4">
        <a href="?tab=announcements" class="sk-tab <?= $active_tab==='announcements'?'active':'' ?>">
            <i class="bi bi-megaphone-fill"></i> Bulletins
        </a>
        <a href="?tab=programs" class="sk-tab <?= $active_tab==='programs'?'active':'' ?>">
            <i class="bi bi-calendar-event-fill"></i> Programs
        </a>
        <a href="?tab=myenrollments" class="sk-tab <?= $active_tab==='myenrollments'?'active':'' ?>">
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
                <h5 class="mb-0 fw-bold" style="color:var(--primary-dark);"><i class="bi bi-pin-angle-fill me-2"></i>SK Bulletins</h5>
                <small class="text-muted">Official announcements from your Sangguniang Kabataan</small>
            </div>
            <div class="chip-bar">
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
        </div>
    <?php else:
        $pinned  = array_filter($bulletins, fn($b) => $b['is_pinned']);
        $regular = array_filter($bulletins, fn($b) => !$b['is_pinned']);
        $tc_map  = ['Announcement'=>'t-announcement','Opportunity'=>'t-opportunity','Reminder'=>'t-reminder','Achievement'=>'t-achievement','General'=>'t-general'];
    ?>

    <?php if ($pinned): ?>
    <div class="d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-pin-fill" style="color:var(--gold);"></i>
        <span class="fw-bold" style="font-size:.8rem;color:var(--gold);text-transform:uppercase;letter-spacing:.08em;">Pinned</span>
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
        <span class="fw-bold" style="font-size:.8rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;">Other Posts</span>
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
                <h5 class="mb-0 fw-bold" style="color:var(--primary-dark);"><i class="bi bi-calendar-event-fill me-2"></i>Youth Programs</h5>
                <small class="text-muted">Browse and enroll in SK programs and activities</small>
            </div>
            <div class="chip-bar">
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
        <h5 class="mb-0 fw-bold" style="color:var(--primary-dark);"><i class="bi bi-person-check-fill me-2"></i>My Program Enrollments</h5>
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
        <div class="col-6 col-md-3"><div class="section-panel text-center py-3"><div style="font-size:2rem;font-weight:900;color:var(--primary);"><?= $total_my ?></div><div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase;">Total</div></div></div>
        <div class="col-6 col-md-3"><div class="section-panel text-center py-3"><div style="font-size:2rem;font-weight:900;color:#2471a3;"><?= $enrolled_c ?></div><div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase;">Enrolled</div></div></div>
        <div class="col-6 col-md-3"><div class="section-panel text-center py-3"><div style="font-size:2rem;font-weight:900;color:#27ae60;"><?= $attended_c ?></div><div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase;">Attended</div></div></div>
        <div class="col-6 col-md-3"><div class="section-panel text-center py-3"><div style="font-size:2rem;font-weight:900;color:#c0392b;"><?= $dropped_c ?></div><div style="font-size:.75rem;color:var(--muted);font-weight:700;text-transform:uppercase;">Dropped</div></div></div>
    </div>
    <div class="row g-3">
    <?php foreach ($my_programs as $ep):
        $ep_badge_map = ['Enrolled'=>'badge-upcoming','Attended'=>'badge-ongoing','Dropped'=>'badge-cancelled'];
        $ep_badge = $ep_badge_map[$ep['status']] ?? 'badge-upcoming';
        $ps_badge_map = ['Upcoming'=>'badge-upcoming','Ongoing'=>'badge-ongoing','Completed'=>'badge-completed','Cancelled'=>'badge-cancelled'];
        $ps_badge = $ps_badge_map[$ep['prog_status']] ?? 'badge-completed';
    ?>
    <div class="col-md-6">
        <div class="section-panel" style="border-left:5px solid var(--primary);">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge-ptype"><?= htmlspecialchars($ep['program_type']) ?></span>
                <div class="d-flex gap-2">
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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:15px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title fw-bold" id="youthProfilingTitle">
                    <i class="fas fa-id-card me-2"></i> Youth Profile Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" class="was-validated" enctype="multipart/form-data">
                <div class="modal-body p-4">

                    <!-- Name -->
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="fw-bold small text-uppercase form-label">Last Name</label>
                            <input name="lname" type="text" class="form-control" placeholder="Required"
                                value="<?= isset($userdetails['lname']) ? htmlspecialchars($userdetails['lname']) : '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small text-uppercase form-label">First Name</label>
                            <input name="fname" type="text" class="form-control" placeholder="Required"
                                value="<?= isset($userdetails['fname']) ? htmlspecialchars($userdetails['fname']) : '' ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="fw-bold small text-uppercase form-label">M.I.</label>
                            <input name="mi" type="text" class="form-control" placeholder="Optional"
                                value="<?= isset($userdetails['mi']) ? htmlspecialchars($userdetails['mi']) : '' ?>">
                        </div>
                    </div>

                    <!-- Demographics -->
                    <div class="row g-3 mt-1">
                        <div class="col-4">
                            <label class="fw-bold small text-uppercase form-label">Age</label>
                            <input name="age" type="number" class="form-control"
                                value="<?= isset($userdetails['age']) ? htmlspecialchars($userdetails['age']) : '' ?>" required>
                        </div>
                        <div class="col-4">
                            <label class="fw-bold small text-uppercase form-label">Sex</label>
                            <select name="sex" class="form-select" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Male"   <?= (isset($userdetails['sex']) && $userdetails['sex']==='Male')   ? 'selected':'' ?>>Male</option>
                                <option value="Female" <?= (isset($userdetails['sex']) && $userdetails['sex']==='Female') ? 'selected':'' ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <label class="fw-bold small text-uppercase form-label">Civil Status</label>
                            <select name="civil_status" class="form-select" required>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Solo Parent">Solo Parent</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Contact & Education -->
                    <h6 class="text-primary fw-bold mt-4 mb-2"><i class="fas fa-at me-1"></i> Contact & Education</h6>
                    <hr class="mt-0">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase form-label">Contact Number</label>
                            <div class="input-group">
                                <span class="input-group-text small">+63</span>
                                <input type="text" class="form-control" name="contact_number" placeholder="9XXXXXXXXX" required pattern="\d{10}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase form-label">Email Address</label>
                            <input type="email" class="form-control" name="email_address" placeholder="name@example.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase form-label">Educational Attainment</label>
                            <input type="text" class="form-control" name="educ_attain" placeholder="e.g. College Undergraduate" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold small text-uppercase form-label">Employment Status</label>
                            <select name="emp_status" class="form-select" required>
                                <option value="Employed">Employed</option>
                                <option value="Unemployed">Unemployed</option>
                                <option value="Self-Employed">Self-Employed</option>
                                <option value="Student">Student</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="fw-bold small text-uppercase form-label">Special Skills / Interests</label>
                            <textarea class="form-control" name="skill_name" rows="2" placeholder="e.g. Graphic Design, Public Speaking, Sports" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <input name="id_youth" type="hidden" value="<?= $userdetails['id_resident'] ?? '' ?>">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button name="create_youth" type="submit" class="btn btn-primary px-5 fw-bold">
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
        <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
            <div class="modal-header modal-header-blue">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Confirm Enrollment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">You are about to enroll in:</p>
                <div class="section-panel" style="border-left:4px solid var(--primary);">
                    <div class="fw-bold" style="color:var(--primary-dark);font-size:1rem;" id="modal_prog_title">—</div>
                    <div class="p-meta mt-2">
                        <span><i class="fas fa-calendar"></i><span id="modal_prog_date">—</span></span>
                        <span><i class="fas fa-map-marker-alt"></i><span id="modal_prog_venue">—</span></span>
                    </div>
                </div>
                <p class="mt-3 mb-0" style="font-size:.85rem;color:var(--muted);">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
