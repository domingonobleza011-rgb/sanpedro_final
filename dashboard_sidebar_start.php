<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Barangay San Pedro</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
    
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');
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
</style>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="admn_dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    
                </div>
                <div class="sidebar-brand-text">Administrator Dashboard </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="admn_dashboard.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                User Management
            </div>

            <!-- Barangay Staff CRUD -->
            <li class="nav-item">
                <a class="nav-link" href="admn_staff_crud.php">
                    <i class="fas fa-user-tie"></i>
                    <span>Barangay Staffs</span></a>
            </li>

            <!-- Resident CRUD -->
            <li class="nav-item">
                <a class="nav-link" href="admn_resident_crud.php">
                    <i class="fas fa-users"></i>
                    <span>Barangay Residents</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admn_messages.php">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Messages</span></a>
            </li>
        <li class="nav-item">
                <a class="nav-link" href="admn_complaints.php">
                    <i class="bi bi-person-exclamation"></i>
                    <span>Complaints</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Barangay Services
            </div>

            <!-- Announcement Management -->
            <li class="nav-item">
                <a class="nav-link" href="admn_announcement_crud.php">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admn_youth_profile.php">
                    <i class="fas fa-users"></i>
                    <span>Youth Profiling</span></a>
            </li>

            <!-- Certificate of Residency -->
            <li class="nav-item">
                <a class="nav-link" href="admn_certofres.php">
                    <i class="fas fa-file-word"></i>
                    <span>Certificate of Residency</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admn_brgyid.php">
                    <i class="fas fa-id-card"></i>
                    <span>Barangay ID </span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="admn_bspermit.php">
                    <i class="fas fa-file-contract"></i>
                    <span>Business Permit</span></a>
            </li>



            <!-- Barangay Clearance -->
            <li class="nav-item">
                <a class="nav-link" href="admn_brgyclearance.php">
                    <i class="fas fa-file"></i>
                    <span>Barangay Clearance</span></a>
            </li>

            <!-- Certificate of Indigency -->
            <li class="nav-item">
                <a class="nav-link" href="admn_certofindigency.php">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Certificate of Indigency</span></a>
            </li>

            <!-- Complain Blotter Report -->
            <li class="nav-item">
                <a class="nav-link" href="admn_blotterreport.php">
                    <i class="fas fa-user-shield"></i>
                    <span>Peace and Order Report</span></a>
            </li>
            <hr>
            <li class="nav-item">
                <a class="nav-link" href="admn_archive.php">
                    <i class="fas fa-archive"></i>
                    <span>Archive</span></a>
            </li>
 <li class="nav-item">
                <a class="nav-link" href="admn_settings.php">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span></a>
            </li>
            

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - User Information -->
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="index.php" id="userDropdown" role="button"
                                    aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-800 small"><?= $userdetails['surname']?>, <?= $userdetails['firstname']?> <?= $userdetails['mname']?></span>
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                </a>
                            </li>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                
                <!-- End of Topbar -->