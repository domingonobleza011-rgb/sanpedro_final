<?php
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php');
error_reporting(E_ALL ^ E_WARNING);
include('classes/resident.class.php');

$userdetails = $bmis->get_userdata();
$is_verified = $bmis->isResidentVerified($userdetails['id_resident']);
$unread_msg_count = $bmis->getUnreadResidentMessageCount($userdetails['id_resident']);

$dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
$cdate = $dt->format('Y/m/d');
$ctime = $dt->format('H');

// Handle announcement hiding
if (isset($_POST['delete_announcement'])) {
    $bmis->delete_announcement($userdetails['id_resident']);
}
$announcements = $bmis->view_active_announcements($userdetails['id_resident']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Barangay San Pedro Iriga</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* ----- GLOBAL RESETS & TYPOGRAPHY ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }
        /* ----- SERVICE CARDS ----- */
        .service-grid .card {
            border: none;
            border-radius: 16px;
            transition: transform 0.25s ease, box-shadow 0.3s ease;
            background: #fff;
            height: 100%;
        }
        .service-grid .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
        }
        .service-grid .card .card-body {
            padding: 1.75rem 1rem;
        }
        .service-grid .card i {
            font-size: 2.6rem;
            color: #1b74e4;
            transition: color 0.2s;
        }
        .service-grid .card:hover i {
            color: #0a5ecf;
        }
        .service-grid .card h5 {
            font-weight: 600;
            font-size: 1rem;
            margin-top: 0.75rem;
            color: #1c1e21;
        }
        .service-grid .card .badge-verify {
            font-size: 0.7rem;
            padding: 0.3rem 0.7rem;
            border-radius: 30px;
            background: #e7f3ff;
            color: #1877f2;
            font-weight: 600;
        }
        .service-grid .card .badge-locked {
            background: #f0f2f5;
            color: #65676b;
        }
        /* ----- VERIFICATION BANNER ----- */
        .verify-banner {
            background: #fff3cd;
            border-left: 6px solid #ffc107;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }
        .verify-banner .btn-upload {
            background: #ffc107;
            border-radius: 40px;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            color: #1c1e21;
        }
        .verify-banner .btn-upload:hover {
            background: #e0a800;
        }
        /* ----- FACEBOOK-STYLE ANNOUNCEMENT FEED ----- */
        .fb-feed-wrapper {
            max-width: 680px;
            margin: 0 auto;
        }
        .fb-post-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            margin-bottom: 1.25rem;
            overflow: hidden;
        }
        .fb-post-header {
            display: flex;
            align-items: center;
            padding: 12px 16px 6px;
        }
        .fb-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1877f2, #0a5ecf);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .fb-post-meta {
            margin-left: 12px;
            flex: 1;
        }
        .fb-page-name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #1c1e21;
        }
        .fb-post-date {
            font-size: 0.75rem;
            color: #65676b;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .fb-hide-btn {
            background: none;
            border: none;
            color: #65676b;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            font-size: 1.2rem;
        }
        .fb-hide-btn:hover {
            background: #f0f2f5;
        }
        .fb-post-body {
            padding: 0 16px 12px;
        }
        .fb-post-text {
            font-size: 0.97rem;
            line-height: 1.55;
            color: #1c1e21;
            white-space: pre-line;
            word-break: break-word;
        }
        .fb-post-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            margin-top: 8px;
            border-radius: 8px;
            cursor: pointer;
        }
        .fb-post-footer {
            border-top: 1px solid #e4e6ea;
            padding: 6px 16px;
            display: flex;
            gap: 8px;
        }
        .fb-react-btn {
            flex: 1;
            background: none;
            border: none;
            color: #65676b;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .fb-react-btn:hover {
            background: #f0f2f5;
        }
        .fb-empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: #fff;
            border-radius: 16px;
            color: #65676b;
        }
        .fb-empty-state i {
            font-size: 2.8rem;
            color: #bcc0c4;
            display: block;
            margin-bottom: 12px;
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
        /* ----- RESPONSIVE TWEAKS ----- */
        @media (max-width: 576px) {
            .service-grid .card .card-body {
                padding: 1.25rem 0.75rem;
            }
            .service-grid .card i {
                font-size: 2.2rem;
            }
            .service-grid .card h5 {
                font-size: 0.9rem;
            }
            .fb-post-header {
                padding: 10px 12px 4px;
            }
            .fb-post-body {
                padding: 0 12px 10px;
            }
            .verify-banner {
                padding: 1rem;
            }
            .verify-banner .btn-upload {
                font-size: 0.85rem;
                padding: 0.4rem 1rem;
            }
        }
        @media (min-width: 992px) {
            .service-grid .card i {
                font-size: 3rem;
            }
            .service-grid .card h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>

<!-- BACK TO TOP BUTTON -->
<a class="top-link hide" id="js-top" href="#">
    <svg viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
    <span class="visually-hidden">Back to top</span>
</a>

<!-- INCLUDE NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

<main class="container-custom py-4">

    <!-- ===== VERIFICATION STATUS BANNER ===== -->
    <?php if (!$is_verified): ?>
    <div class="verify-banner d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <span style="font-size:2rem;">&#x1F512;</span>
            <div>
                <h5 class="fw-bold mb-1">Account Not Yet Verified</h5>
                <p class="mb-0 text-muted small">Upload a valid ID to access certificate services.</p>
            </div>
        </div>
        <a href="resident_messages.php?id_resident=<?= $userdetails['id_resident']; ?>&upload_id=1" class="btn btn-upload">
            <i class="bi bi-upload me-2"></i>Upload ID
        </a>
    </div>
    <?php else: ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 px-4 d-flex align-items-center gap-2">
        <i class="bi bi-patch-check-fill fs-5"></i>
        <span><strong>Account Verified</strong> &mdash; You have full access to all services.</span>
    </div>
    <?php endif; ?>

    <!-- ===== SERVICES GRID ===== -->
    <section class="mb-5">
        <h4 class="fw-bold mb-3 text-dark">Available Services</h4>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-4 g-3 service-grid">
            <!-- Each service card -->
            <?php
            $services = [
                ['name' => 'Business Permit', 'icon' => 'bi-file-earmark-medical-fill', 'link' => 'services_business.php', 'locked' => !$is_verified],
                ['name' => 'Barangay ID', 'icon' => 'bi-person-vcard-fill', 'link' => 'services_brgyid.php', 'locked' => !$is_verified],
                ['name' => 'Indigency', 'icon' => 'bi-briefcase-fill', 'link' => 'services_certofindigency.php', 'locked' => !$is_verified],
                ['name' => 'Residency', 'icon' => 'bi-house-check-fill', 'link' => 'services_certofres.php', 'locked' => !$is_verified],
                ['name' => 'Clearance', 'icon' => 'bi-shield-lock-fill', 'link' => 'services_brgyclearance.php', 'locked' => !$is_verified],
                ['name' => 'Youth Portal', 'icon' => 'bi-people-fill', 'link' => 'resident_youth_profile.php', 'locked' => false],
                ['name' => 'Messages', 'icon' => 'bi-chat-dots-fill', 'link' => 'resident_messages.php', 'locked' => false, 'badge' => $unread_msg_count > 0 ? $unread_msg_count : null],
                ['name' => 'Complaint', 'icon' => 'bi-info-circle-fill', 'link' => 'resident_complaint.php', 'locked' => false],
            ];
            foreach ($services as $s):
                $href = $s['locked'] ? '#' : $s['link'] . '?id_resident=' . $userdetails['id_resident'];
                $onclick = $s['locked'] ? 'onclick="showVerifyAlert(); return false;"' : '';
                $badge = isset($s['badge']) ? '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$s['badge'].'</span>' : '';
            ?>
            <div class="col">
                <a href="<?= $href ?>" class="text-decoration-none" <?= $onclick ?>>
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center position-relative">
                            <?= $badge ?>
                            <i class="bi <?= $s['icon'] ?> <?= $s['locked'] ? 'text-secondary' : '' ?>"></i>
                            <h5 class="text-dark"><?= $s['name'] ?></h5>
                            <?php if ($s['locked']): ?>
                            <span class="badge badge-verify badge-locked"><i class="bi bi-lock-fill me-1"></i>Locked</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

   

</main>

<!-- ===== VERIFICATION REQUIRED MODAL ===== -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-lock-fill me-2"></i>Verification Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="font-size:3rem;">&#x1F512;</div>
                <h5 class="mt-2 mb-3">You need to verify your account first</h5>
                <p class="text-muted">Please go to <strong>Messages</strong> and upload a valid government-issued ID. Once approved, you'll have full access.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="resident_messages.php?id_resident=<?= $userdetails['id_resident']; ?>&upload_id=1" class="btn btn-warning fw-bold rounded-pill px-4">
                    <i class="bi bi-upload me-2"></i>Upload ID
                </a>
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
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            topBtn.classList.remove('hide');
        } else {
            topBtn.classList.add('hide');
        }
    });

    // Smooth scroll for back-to-top
    topBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Show verification modal
    function showVerifyAlert() {
        const modal = new bootstrap.Modal(document.getElementById('verifyModal'));
        modal.show();
    }

    // Tooltip initialization (if any)
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
<!-- FCM (if needed) -->
<script type="module" src="fcm_init.js"></script>
</body>
</html>