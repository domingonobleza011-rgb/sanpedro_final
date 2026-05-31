<?php
// dashboard_sidebar_start_sk.php — SK Portal Sidebar
// Usage: define('BMIS_ROLE_REQUIRED', 'sk'); require('secure_header.php'); include('dashboard_sidebar_start_sk.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SK Portal — Barangay San Pedro</title>
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f2d5a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Brgy San Pedro">
    <link rel="apple-touch-icon" href="/icons/pwa/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap');

:root {
    --sk-green:       #0f2d5a;
    --sk-green-mid:   #1a4480;
    --sk-green-light: #2b5ea7;
    --sk-green-pale:  #e8eef8;
    --sk-gold:        #2b5ea7;
    --sk-gold-light:  #2b5ea7;
    --sk-gold-pale:   #e8eef8;
    --cream:          #f4f7fc;
    --white:          #ffffff;
    --text-dark:      #0f2d5a;
    --text-mid:       #3a4e6e;
    --text-light:     #5a7199;
    --border:         #d0daea;
    --shadow-sm:      0 2px 8px rgba(15,45,90,0.07);
    --shadow-md:      0 6px 24px rgba(15,45,90,0.11);
    --radius:         14px;
    --radius-sm:      10px;
    --transition:     0.22s cubic-bezier(0.4,0,0.2,1);
}

body { font-family: 'DM Sans', -apple-system, sans-serif !important; background: var(--cream) !important; color: var(--text-dark) !important; }
h1,h2,h3,h4,h5,h6 { font-family: 'DM Sans', sans-serif !important; }

h4 {
    font-weight: 700 !important; font-size: 1.05rem !important;
    color: var(--sk-green) !important; letter-spacing: 0.2px;
    display: flex; align-items: center; gap: 10px;
}
h4::before {
    content: ''; display: inline-block; width: 4px; height: 20px;
    background: linear-gradient(to bottom, var(--sk-green-light), var(--sk-green-mid));
    border-radius: 4px; flex-shrink: 0;
}
hr { border-color: var(--border) !important; opacity: 1 !important; margin: 0.5rem 0 !important; }

.sidebar {
    background: linear-gradient(180deg, var(--sk-green) 0%, var(--sk-green-mid) 60%, var(--sk-green-light) 100%) !important;
    border-right: none !important;
    box-shadow: 4px 0 24px rgba(15,45,90,0.22);
}
.sidebar-brand {
    padding: 1.6rem 1rem 1.4rem !important;
    background: rgba(0,0,0,0.14) !important;
    border-bottom: 1px solid rgba(255,255,255,0.08) !important;
    gap: 10px;
}
.sidebar-brand-text {
    font-family: 'DM Sans', sans-serif !important;
    font-size: 0.82rem !important; font-weight: 700 !important;
    letter-spacing: 0.3px !important; color: rgba(255,255,255,0.95) !important;
    text-transform: none !important; line-height: 1.3;
}
.sidebar-divider { border-top-color: rgba(255,255,255,0.08) !important; margin: 0.6rem 1rem !important; }
.sidebar-heading {
    font-size: 0.65rem !important; font-weight: 700 !important;
    letter-spacing: 1.8px !important; text-transform: uppercase !important;
    color: rgba(255,255,255,0.35) !important; padding: 0.8rem 1.2rem 0.4rem !important;
}
.sidebar .nav-item .nav-link {
    color: rgba(255,255,255,0.72) !important; font-size: 0.875rem !important;
    font-weight: 400 !important; padding: 10px 20px !important;
    border-radius: 0 !important; transition: all var(--transition) !important;
    display: flex; align-items: center; gap: 10px;
    border-left: 3px solid transparent;
}
.sidebar .nav-item .nav-link i, .sidebar .nav-item .nav-link .bi {
    font-size: 0.95rem; width: 18px; text-align: center;
    flex-shrink: 0; color: rgba(255,255,255,0.5); transition: color var(--transition);
}
.sidebar .nav-item .nav-link:hover { color: var(--white) !important; background: rgba(255,255,255,0.09) !important; border-left-color: rgba(43,94,167,0.6) !important; }
.sidebar .nav-item .nav-link:hover i, .sidebar .nav-item .nav-link:hover .bi { color: rgba(255,255,255,0.9); }
.sidebar .nav-item.active .nav-link, .sidebar .nav-item .nav-link.active {
    color: var(--white) !important; background: rgba(43,94,167,0.25) !important;
    border-left-color: var(--sk-green-light) !important; font-weight: 500 !important;
}
.topbar { background: var(--white) !important; box-shadow: 0 2px 16px rgba(15,45,90,0.08) !important; border-bottom: 1px solid var(--border) !important; padding: 0 20px !important; height: 60px; align-items: center; }
.topbar .nav-item .nav-link { color: var(--text-mid) !important; font-size: 0.875rem; font-weight: 500; padding: 8px 14px !important; border-radius: 8px; transition: all var(--transition); display: flex; align-items: center; gap: 8px; }
.topbar .nav-item .nav-link:hover { background: var(--cream); color: var(--sk-green) !important; }
.topbar .text-gray-800 { color: var(--text-dark) !important; font-weight: 500; }

/* SK Badge */
.sk-badge-sidebar {
    display: inline-block; background: var(--sk-green-light); color: #fff;
    font-size: 0.6rem; font-weight: 800; letter-spacing: 1px;
    padding: 2px 7px; border-radius: 20px; margin-left: auto; flex-shrink: 0;
    text-transform: uppercase;
}
</style>

<body id="page-top">
<div id="wrapper">

    <!-- SK Sidebar -->
    <ul class="navbar-nav bg-primary sidebar sidebar-dark accordion" id="accordionSidebar">


            <div class="sidebar-brand d-flex align-items-center justify-content-center">
                Sk PORTAL<br>
                
            </div>


        <hr class="sidebar-divider my-0">

        <li class="nav-item">
            <a class="nav-link" href="sk_dashboard.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>SK Dashboard</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Youth Records</div>

        <li class="nav-item">
            <a class="nav-link" href="sk_youth_records.php">
                <i class="fas fa-users"></i>
                <span>Youth Member Records</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Programs & Activities</div>

        <li class="nav-item">
            <a class="nav-link" href="sk_programs.php">
                <i class="fas fa-calendar-check"></i>
                <span>Programs & Activities</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="sk_enrollment.php">
                <i class="fas fa-clipboard-list"></i>
                <span>Participation Tracking</span>
            </a>
        </li>

        <hr class="sidebar-divider">
        <div class="sidebar-heading">Announcements</div>

        <li class="nav-item">
            <a class="nav-link" href="sk_announcements.php">
                <i class="fas fa-bullhorn"></i>
                <span>SK Announcements</span>
            </a>
        </li>

        <hr class="sidebar-divider d-none d-md-block">
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>
    </ul>
    <!-- End of SK Sidebar -->

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow d-sm-none">
                        <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-search fa-fw"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <span class="mr-2 d-none d-lg-inline text-gray-800 small"><?= $userdetails['surname']?>, <?= $userdetails['firstname']?> <?= $userdetails['mname']?></span>
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                           
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- End Topbar -->