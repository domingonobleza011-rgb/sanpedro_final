<?php
/* ============================================================
   SHARED RESIDENT NAVBAR
   Included by every resident-facing page (resident_*.php,
   services_*.php, setup_security_question.php).

   Edit THIS file only — the navbar links, icons, labels, and
   styling will update on every page that includes it.

   Requires $userdetails['id_resident'] to already be set by
   secure_header.php before this file is included.
   ============================================================ */

// Figure out which page we're on so we can highlight the active nav item.
$resident_nav_current = basename($_SERVER['PHP_SELF'] ?? '');

// Map each nav destination to the page(s) that should count as "active" for it.
$resident_nav_items = [
    [
        'match'  => ['resident_homepage.php'],
        'href'   => 'resident_homepage.php',
        'icon'   => 'bi-house-door-fill',
        'label'  => 'Home',
        'mobile' => 'Home',
    ],
    [
        'match'  => ['resident_announcement.php'],
        'href'   => 'resident_announcement.php',
        'icon'   => 'bi-megaphone-fill',
        'label'  => 'Announcements',
        'mobile' => 'News',
    ],
    [
        'match'  => ['resident_profile.php'],
        'href'   => 'resident_profile.php?id_resident=' . urlencode($userdetails['id_resident'] ?? ''),
        'icon'   => 'bi-person-badge',
        'label'  => 'Profile',
        'mobile' => 'Profile',
    ],
    [
        'match'  => ['resident_changepass.php'],
        'href'   => 'resident_changepass.php?id_resident=' . urlencode($userdetails['id_resident'] ?? ''),
        'icon'   => 'bi-shield-lock',
        'label'  => 'Password',
        'mobile' => 'Pass',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
</head>
<style>
/* ===== Shared Resident Nav Styling (resident_navbar.php) ===== */
.mobile-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 65px;
    background-color: #ffffff;
    display: flex;
    justify-content: space-around;
    align-items: center;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 1050;
    border-top: 1px solid #dee2e6;
}

.mobile-bottom-nav .nav-item {
    text-decoration: none;
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.7rem;
    font-weight: 500;
}

.mobile-bottom-nav .nav-item i {
    font-size: 1.4rem;
    margin-bottom: 2px;
}

.mobile-bottom-nav .nav-item:active,
.mobile-bottom-nav .nav-item.active-nav {
    color: #0d6efd;
}

.resident-desktop-nav .btn.active-nav {
    background-color: #0a58ca;
    box-shadow: inset 0 0 0 2px rgba(255,255,255,0.6);
}

/* Padding so page content isn't hidden behind the fixed mobile nav */
@media (max-width: 767px) {
    body {
        padding-bottom: 80px;
    }
}
</style>

<!-- DESKTOP NAVBAR (Hidden on Mobile) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top d-none d-md-block shadow">
    <div class="container-fluid resident-desktop-nav">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building-fill me-2"></i> Barangay San Pedro
        </a>
        <div class="d-flex ms-auto">
            <?php foreach ($resident_nav_items as $item): ?>
                <?php $isActive = in_array($resident_nav_current, $item['match'], true); ?>
                <a href="<?= htmlspecialchars($item['href']) ?>" class="btn btn-primary me-1<?= $isActive ? ' active-nav' : '' ?>">
                    <i class="bi <?= htmlspecialchars($item['icon']) ?> me-1"></i> <?= htmlspecialchars($item['label']) ?>
                </a>
            <?php endforeach; ?>
            <a href="logout.php" class="btn btn-danger ms-2"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- MOBILE BOTTOM NAV (Hidden on Desktop) -->
<div class="mobile-bottom-nav d-md-none">
    <?php foreach ($resident_nav_items as $item): ?>
        <?php $isActive = in_array($resident_nav_current, $item['match'], true); ?>
        <a href="<?= htmlspecialchars($item['href']) ?>" class="nav-item<?= $isActive ? ' active-nav' : '' ?>">
            <i class="bi <?= htmlspecialchars($item['icon']) ?>"></i>
            <span><?= htmlspecialchars($item['mobile']) ?></span>
        </a>
    <?php endforeach; ?>

    <a href="logout.php" class="nav-item text-danger">
        <i class="bi bi-box-arrow-right"></i>
        <span>Exit</span>
    </a>
</div>
