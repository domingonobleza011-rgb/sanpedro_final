<?php 
    error_reporting(E_ALL ^ E_WARNING);
    include('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();

    // Check if resident is verified
    $is_verified = $bmis->isResidentVerified($userdetails['id_resident']);

    $dt = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $tm = new DateTime("now", new DateTimeZone('Asia/Manila'));
    $cdate = $dt->format('Y/m/d');
    $ctime = $tm->format('H');

?>
<?php
    // 1. Get the current user ID
    $current_user_id = $userdetails['id_resident'];

    // 2. Pass the ID to the delete function
    // This calls your hide_announcement logic internally
    if(isset($_POST['delete_announcement'])) {
        $bmis->delete_announcement($current_user_id);
    }

    // 3. Fetch data filtered by the current user's "hidden" list
    $view = $bmis->view_active_announcements($current_user_id); 
?>
    



<script> 
    function logout() {
    window.location.href = "logout.php";
    }
    function profile() {
    window.location.href = "resident_profile.php";
    }
</script>


<!DOCTYPE html> 
<html>

    <head> 
    <title> Barangay San Pedro Iriga </title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- responsive tags for screen compatibility -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- custom css --> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        

    <style>

    /* Navbar Buttons */

  .service-card {
    transition: all 0.3s ease;
    border-radius: 15px;
  }

  .service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
  }

  .icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.8rem;
  }

  /* Soft Background Colors */
  .bg-primary-light { background-color: #e7f1ff; }
  .bg-success-light { background-color: #eafaf1; }
  .bg-warning-light { background-color: #fef9e7; }
  
    .btn1 {
    border-radius: 20px;
    border: none; /* Remove borders */
    color: white; /* White text */
    font-size: 16px; /* Set a font size */
    cursor: pointer; /* Mouse pointer on hover */
    margin-left: 23%;
    padding: 12px 22px;
    }

    .btn2 {
    border-radius: 20px;
    border: none; /* Remove borders */
    color: white; /* White text */
    font-size: 16px; /* Set a font size */
    cursor: pointer; /* Mouse pointer on hover */
    padding: 12px 22px;
    margin-left: .1%;
    }

    .btn3 {
    border-radius: 20px;
    border: none; /* Remove borders */
    color: white; /* White text */
    font-size: 16px; /* Set a font size */
    cursor: pointer; /* Mouse pointer on hover */
    padding: 12px 22px;
    margin-left: .1%;
    }


    .top-link {
    transition: all 0.25s ease-in-out;
    position: fixed;
    bottom: 0;
    right: 0;
    display: inline-flex;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    margin: 0 3em 3em 0;
    border-radius: 50%;
    padding: 0.25em;
    width: 80px;
    height: 80px;
    background-color: #3661D5;
    }
    .top-link.show {
    visibility: visible;
    opacity: 1;
    }
    .top-link.hide {
    visibility: hidden;
    opacity: 0;
    }
    .top-link svg {
    fill: white;
    width: 24px;
    height: 12px;
}
    .screen-reader-text {
    position: absolute;
    clip-path: inset(50%);
    margin: -1px;
    border: 0;
    padding: 0;
    width: 1px;
    height: 1px;
    overflow: hidden;
    word-wrap: normal !important;
    clip: rect(1px, 1px, 1px, 1px);
    }
    .screen-reader-text:focus {
    display: block;
    top: 5px;
    left: 5px;
    z-index: 100000;
    clip-path: none;
    background-color: #eee;
    padding: 15px 23px 14px;
    width: auto;
    height: auto;
    text-decoration: none;
    line-height: normal;
    color: #444;
    font-size: 1em;
    clip: auto !important;
    }


    /* Footer Style */
    
    .footerlinks{
        color:white;
        }
    .shfooter .collapse {
        display: inherit;
    }
        @media (max-width:767px) {
    .shfooter ul {
            margin-bottom: 0;
    }

    .shfooter .collapse {
            display: none;
    }

    .shfooter .collapse.show {
            display: block;
    }

    .shfooter .title .fa-angle-up,
    .shfooter .title[aria-expanded=true] .fa-angle-down {
            display: none;
    }

    .shfooter .title[aria-expanded=true] .fa-angle-up {
            display: block;
    }

    .shfooter .navbar-toggler {
            display: inline-block;
            padding: 0;
    }

    }

    .resize {
        text-align: center;
    }
    .resize {
        margin-top: 3rem;
        font-size: 1.25rem;
    }
    /*RESIZESCREEN ANIMATION*/
    .fa-angle-double-right {
        animation: rightanime 1s linear infinite;
    }

    .fa-angle-double-left {
        animation: leftanime 1s linear infinite;
    }
    @keyframes rightanime {
        50% {
            transform: translateX(10px);
            opacity: 0.5;
    }
        100% {
            transform: translateX(10px);
            opacity: 0;
    }
    }
    @keyframes leftanime {
        50% {
            transform: translateX(-10px);
            opacity: 0.5;
    }
        100% {
            transform: translateX(-10px);
            opacity: 0;
    }
    }

    /* Contact Chip */

    .chip {
    display: inline-block;
    padding: 0 25px;
    height: 50px;
    line-height: 50px;
    border-radius: 25px;
    background-color: #2C54C1;
    margin-top: 5px;
    }

    .chip img {
    float: left;
    margin: 0 10px 0 -25px;
    height: 50px;
    width: 50px;
    border-radius: 50%;
    }


    </style>
    <body> 

        <!-- Back-to-Top and Back Button -->

        <a data-toggle="tooltip" title="Back-To-Top" class="top-link hide" href="" id="js-top">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 6"><path d="M12 6H0l6-6z"/></svg>
            <span class="screen-reader-text">Back to top</span>
        </a>

        <!-- Eto yung navbar -->

        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="resident_homepage.php">
            <i class="bi bi-building me-1"></i> Barangay San Pedro
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" href="resident_homepage.php"><i class="fa fa-home me-1"></i> Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="resident_announcement.php"><i class="bi bi-megaphone-fill me-1"></i> Announcements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services-section"><i class="bi bi-grid-fill me-1"></i> Services</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle text-primary fw-semibold" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= $userdetails['surname'];?>, <?= $userdetails['firstname'];?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-lock me-2"></i> Change Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* ===== FACEBOOK-STYLE ANNOUNCEMENT FEED ===== */
#announcements-section {
    background-color: #f0f2f5;
    padding: 28px 0 10px;
}

.fb-feed-wrapper {
    max-width: 680px;
    margin: 0 auto;
    padding: 0 12px;
}

.fb-feed-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
}

.fb-feed-header h5 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1c1e21;
    margin: 0;
}

.fb-post-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    margin-bottom: 16px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.fb-post-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.18);
}

.fb-post-header {
    display: flex;
    align-items: center;
    padding: 12px 16px 8px;
}

.fb-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #1877f2, #0a5ecf);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.fb-post-meta {
    margin-left: 10px;
    flex: 1;
}

.fb-page-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1c1e21;
    line-height: 1.2;
}

.fb-page-name a {
    color: inherit;
    text-decoration: none;
}

.fb-post-date {
    font-size: 0.78rem;
    color: #65676b;
    display: flex;
    align-items: center;
    gap: 4px;
}

.fb-post-badge {
    display: inline-block;
    background: #e7f3ff;
    color: #1877f2;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 7px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.fb-hide-btn {
    background: none;
    border: none;
    color: #65676b;
    cursor: pointer;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    transition: background 0.15s;
    margin-left: auto;
}

.fb-hide-btn:hover {
    background: #f0f2f5;
    color: #1c1e21;
}

.fb-post-body {
    padding: 0 16px 10px;
}

.fb-post-text {
    font-size: 0.97rem;
    color: #1c1e21;
    line-height: 1.55;
    margin: 0;
    word-break: break-word;
    white-space: pre-line;
}

.fb-post-text.large-text {
    font-size: 1.2rem;
    font-weight: 500;
}

.fb-post-image {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    display: block;
    cursor: pointer;
    transition: opacity 0.2s;
}

.fb-post-image:hover {
    opacity: 0.95;
}

.fb-post-footer {
    border-top: 1px solid #e4e6ea;
    padding: 6px 16px;
    display: flex;
    gap: 4px;
}

.fb-react-btn {
    flex: 1;
    background: none;
    border: none;
    color: #65676b;
    font-size: 0.88rem;
    font-weight: 600;
    padding: 8px 0;
    border-radius: 6px;
    cursor: default;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: background 0.15s;
}

.fb-react-btn:hover {
    background: #f0f2f5;
    color: #1c1e21;
}

.fb-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #65676b;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.fb-empty-state i {
    font-size: 2.5rem;
    color: #bcc0c4;
    display: block;
    margin-bottom: 10px;
}
</style>


        <div id="down1"></div>

        <br>

        <section class="heading-section" id="services-section"> 
            <div class="container text-center"> 
                <div class="row"> 
                    <div class="col"> 
                        
                        <br>
                        <br>

                        <div class="header"> 
                            <h2> Welcome to Barangay San Pedro Iriga City </h2><bR>
                            <h3> You may select the following services offered below </h3>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <br>

<div class="container my-5">

<?php if (!$is_verified): ?>
<!-- VERIFICATION NOTICE BANNER -->
<div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 p-4" role="alert" style="border-left: 6px solid #ffc107 !important;">
    <div class="d-flex align-items-start gap-3">
        <div style="font-size: 2rem;">&#x1F512;</div>
        <div>
            <h5 class="fw-bold mb-1">Account Not Yet Verified</h5>
            <p class="mb-2">To request barangay certificates and access other services, you must first verify your identity.</p>
            <p class="mb-3"><strong>How to get verified:</strong> Go to <strong>Messages</strong>, then upload a clear photo of your valid government-issued ID (e.g., PhilSys ID, Driver's License, Passport, Voter's ID). The admin will review and approve your account.</p>
            <a href="resident_messages.php?id_resident=<?= $userdetails['id_resident'];?>&upload_id=1" class="btn btn-warning fw-bold rounded-pill px-4">
                <i class="bi bi-upload me-2"></i> Upload Valid ID Now
            </a>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 py-2 px-4" role="alert">
    <i class="bi bi-patch-check-fill me-2"></i> <strong>Account Verified</strong> &mdash; You have full access to all barangay services.
</div>
<?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">

        <!-- CERTIFICATE SERVICES (locked if not verified) -->

        <div class="col">
            <?php if ($is_verified): ?>
            <a href="services_business.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
            <?php else: ?>
            <a href="#" class="text-decoration-none" onclick="showVerifyAlert(); return false;">
            <?php endif; ?>
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm <?= !$is_verified ? 'border-secondary opacity-75' : '' ?>">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-secondary float-end">&#x1F512;</span><?php endif; ?>
                            <i class="bi bi-file-earmark-medical-fill fs-1 <?= !$is_verified ? 'text-secondary' : '' ?>"></i>
                            <h4 class="mt-2 text-dark">Business Permit</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <?php if ($is_verified): ?>
            <a href="services_brgyid.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
            <?php else: ?>
            <a href="#" class="text-decoration-none" onclick="showVerifyAlert(); return false;">
            <?php endif; ?>
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm <?= !$is_verified ? 'border-secondary opacity-75' : '' ?>">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-secondary float-end">&#x1F512;</span><?php endif; ?>
                            <i class="bi bi-person-vcard-fill fs-1 <?= !$is_verified ? 'text-secondary' : '' ?>"></i>
                            <h4 class="mt-2 text-dark">Barangay ID</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <?php if ($is_verified): ?>
            <a href="services_certofindigency.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
            <?php else: ?>
            <a href="#" class="text-decoration-none" onclick="showVerifyAlert(); return false;">
            <?php endif; ?>
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm <?= !$is_verified ? 'border-secondary opacity-75' : '' ?>">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-secondary float-end">&#x1F512;</span><?php endif; ?>
                            <i class="bi bi-briefcase-fill fs-1 <?= !$is_verified ? 'text-secondary' : '' ?>"></i>
                            <h4 class="mt-2 text-dark">Certificate of Indigency</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <?php if ($is_verified): ?>
            <a href="services_certofres.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
            <?php else: ?>
            <a href="#" class="text-decoration-none" onclick="showVerifyAlert(); return false;">
            <?php endif; ?>
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm <?= !$is_verified ? 'border-secondary opacity-75' : '' ?>">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-secondary float-end">&#x1F512;</span><?php endif; ?>
                            <i class="bi bi-house-check-fill fs-1 <?= !$is_verified ? 'text-secondary' : '' ?>"></i>
                            <h4 class="mt-2 text-dark">Certificate of Residency</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <?php if ($is_verified): ?>
            <a href="services_brgyclearance.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
            <?php else: ?>
            <a href="#" class="text-decoration-none" onclick="showVerifyAlert(); return false;">
            <?php endif; ?>
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm <?= !$is_verified ? 'border-secondary opacity-75' : '' ?>">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-secondary float-end">&#x1F512;</span><?php endif; ?>
                            <i class="bi bi-shield-lock-fill fs-1 <?= !$is_verified ? 'text-secondary' : '' ?>"></i>
                            <h4 class="mt-2 text-dark">Barangay Clearance</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- OPEN SERVICES (always accessible) -->

        <div class="col">
            <a href="resident_youth_profile.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill fs-1"></i>
                            <h4 class="mt-2 text-dark">Youth Profiling</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="services_blotter.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-exclamation-octagon-fill fs-1"></i>
                            <h4 class="mt-2 text-dark">Blotter</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="resident_messages.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <?php if (!$is_verified): ?><span class="badge bg-warning text-dark float-end">Action Needed</span><?php endif; ?>
                            <i class="bi bi-chat-dots-fill fs-1"></i>
                            <h4 class="mt-2 text-dark">Messages</h4>
                            <?php if (!$is_verified): ?><small class="text-warning fw-bold">Upload ID here</small><?php endif; ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col">
            <a href="resident_complaint.php?id_resident=<?= $userdetails['id_resident'];?>" class="text-decoration-none">
                <div class="zoom1 h-100">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi bi-info-circle-fill fs-1"></i>
                            <h4 class="mt-2 text-dark">Complaint</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

<!-- Verification Required Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-warning text-dark rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-lock-fill me-2"></i>Verification Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="font-size: 3rem;">&#x1F512;</div>
                <h5 class="mt-2 mb-3">You need to verify your account first</h5>
                <p class="text-muted">Please go to <strong>Messages</strong> and upload a valid government-issued ID. Once the admin approves your ID, you will have full access to all services.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <a href="resident_messages.php?id_resident=<?= $userdetails['id_resident'];?>&upload_id=1" class="btn btn-warning fw-bold rounded-pill px-4">
                    <i class="bi bi-upload me-2"></i>Upload ID
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function showVerifyAlert() {
    var modal = new bootstrap.Modal(document.getElementById('verifyModal'));
    modal.show();
}
</script>

        </section>

        <br>
        <br>
        <br>

        <!-- Footer -->

        <footer id="footer" class="bg-primary text-white d-flex-column text-center">
            <hr class="mt-0">

            <div class="text-center">
                <h1 class="text-white">Services</h1>
                <ul class="list-unstyled list-inline">

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Documents">
                    <i class="fas fa-file fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a href="#!" class="footerlinks sbtn btn-large mx-1" title="Card">
                    <i class="fas fa-id-card fa-2x"></i>
                    </a>
                    
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Friends">
                    <i class="fas fa-user-friends fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Blotter">
                    <i class="fas fa-user-shield fa-2x"></i>
                    </a>
                </li>

                &nbsp;

                <li class="list-inline-item">
                    <a class="footerlinks" href="#!" class="sbtn btn-large mx-1" title="Contact">
                    <i class="fas fa-phone fa-2x"></i>
                    </a>
                </li>
                </ul>
            </div>

            <hr class="mb-0">

            <!--Footer Links-->

            <div class="container text-left text-md-center">
                <div class="row">

                    <!--First column-->

                    <div class="col-md-3 mx-auto shfooter">
                        <h5 class="my-2 font-weight-bold d-none d-md-block">Documentation</h5>
                        <div class="d-md-none title" data-target="#Documentation" data-toggle="collapse">
                            <div class="mt-3 font-weight-bold">Documentation
                                <div class="float-right navbar-toggler">
                                    <i class="fas fa-angle-down"></i>
                                    <i class="fas fa-angle-up"></i>
                                </div>
                            </div>
                        </div>
                        <ul class="list-unstyled collapse" id="Documentation">
                            <li><a class="footerlinks" href="services_certofres.php">Certificate of Residency</a></li>
                            <li><a class="footerlinks" href="services_brgyclearance.php">Barangay Clearance</a></li>
                            <li><a class="footerlinks" href="services_certofindigency.php">Certificate of Indigency</a></li>
                            <li><a class="footerlinks" href="services_business.php">Business Permit</a></li>
                            <li><a class="footerlinks" href="services_brgyid.php">Barangay ID</a></li>
                        </ul>
                    </div>

                    <!--/.First column-->

                    <hr class="clearfix w-100 d-md-none mb-0">

                    <!--Third column-->

                    <div class="col-md-3 mx-auto shfooter">
                        <h5 class="my-2 font-weight-bold d-none d-md-block">Other Services</h5>
                        <div class="d-md-none title" data-target="#OtherServices" data-toggle="collapse">
                            <div class="mt-3 font-weight-bold">Other Services
                                <div class="float-right navbar-toggler">
                                    <i class="fas fa-angle-down"></i>
                                    <i class="fas fa-angle-up"></i>
                                </div>
                            </div>
                        </div>

                        <ul class="list-unstyled collapse" id="OtherServices">
                            <li><a class="footerlinks" href="services_blotter.php">Peace and Order</a></li>
                        </ul>
                    </div>

                    <!--/.Third column-->

                    <hr class="clearfix w-100 d-md-none mb-0">
 
     

                </div>
            </div>

        

        <script>
            $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
            });
        </script>

        <script>
            $(document).ready(function(){
            // Add smooth scrolling to all links
            $("a").on('click', function(event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                // Prevent default anchor click behavior
                event.preventDefault();

                // Store hash
                var hash = this.hash;

                // Using jQuery's animate() method to add smooth page scroll
                // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                });
                } // End if
            });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="../BarangaySystem/bootstrap/js/bootstrap.bundle.js" type="text/javascript"> </script>
    </body>
</html>