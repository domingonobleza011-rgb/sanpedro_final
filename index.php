<?php 
    error_reporting(E_ALL ^ E_WARNING);
    
    if(!isset($_SESSION)) {
        $showdate = date("Y-m-d");
        date_default_timezone_set('Asia/Manila');
        $showtime = date("h:i:a");
        $_SESSION['storedate'] = $showdate;
        $_SESSION['storetime'] = $showdate;
        session_start();
    }

    include('autoloader.php');
    require('classes/main.class.php');
    $bmis->login();
    require('classes/staff.class.php');
    $userdetails = $bmis->get_userdata();
    $view = $staffbmis->view_staff();
   
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="google-site-verification" content="googledb459bb901e2f59e" />

    <title>Barangay San Pedro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>
    <style>
/* ============================================================
   BARANGAY SAN PEDRO — INDEX / LOGIN PAGE — IMPROVED CSS
   Design: Refined civic, warm navy + gold accent, clean serif/sans pairing
   ============================================================ */

@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
    --navy:       #0f2d5a;
    --navy-mid:   #1a4480;
    --navy-light: #2b5ea7;
    --gold:       #c9943a;
    --gold-light: #e8b86d;
    --cream:      #faf8f4;
    --white:      #ffffff;
    --text-dark:  #1a1a2e;
    --text-mid:   #4a5568;
    --text-light: #718096;
    --border:     #e2e8f0;
    --danger:     #dc3545;

    --shadow-sm:  0 2px 8px rgba(15,45,90,0.08);
    --shadow-md:  0 8px 32px rgba(15,45,90,0.12);
    --shadow-lg:  0 20px 60px rgba(15,45,90,0.18);

    --radius:     14px;
    --transition: 0.25s cubic-bezier(0.4,0,0.2,1);
}

/* ─── RESET & BASE ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

body, html {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: 'DM Sans', -apple-system, sans-serif;
    background: var(--cream);
    color: var(--text-dark);
}

/* ─── NAVBAR ────────────────────────────────────────────────── */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    z-index: 1000;
    border-bottom: 1px solid rgba(201,148,58,0.2);
    padding: 12px 36px;
    box-shadow: 0 1px 0 rgba(15,45,90,0.06);
}

.navbar-brand {
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    color: var(--navy) !important;
    font-size: 1.05rem;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.navbar-brand i {
    color: var(--gold);
    font-size: 1.1rem;
}

.nav-link {
    font-weight: 500;
    color: var(--text-mid) !important;
    margin-left: 8px;
    cursor: pointer;
    padding: 8px 14px !important;
    border-radius: 8px;
    font-size: 0.9rem;
    letter-spacing: 0.2px;
    transition: all var(--transition);
    display: flex;
    align-items: center;
    gap: 6px;
}

.nav-link:hover {
    color: var(--navy) !important;
    background: rgba(15,45,90,0.06);
}

/* ─── LAYOUT ────────────────────────────────────────────────── */
.main-container {
    display: flex;
    height: 100vh;
    width: 100vw;
    padding-top: 60px; /* navbar height */
}

/* ─── INFO PANEL (LEFT) ─────────────────────────────────────── */
.info-panel {
    flex: 1;
    background: linear-gradient(145deg, var(--navy) 0%, var(--navy-mid) 55%, var(--navy-light) 100%);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 5% 6%;
    text-align: center;
    position: relative;
    overflow: hidden;
}

/* Decorative geometric layers */
.info-panel::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -80px;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: rgba(201,148,58,0.08);
    pointer-events: none;
}

.info-panel::after {
    content: '';
    position: absolute;
    bottom: -100px;
    left: -60px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}

.info-panel img {
    width: 100px;
    height: 100px;
    object-fit: contain;
    margin-bottom: 1.8rem;
    filter: drop-shadow(0 4px 16px rgba(0,0,0,0.3));
    border-radius: 50%;
    border: 3px solid rgba(201,148,58,0.5);
    padding: 4px;
    background: rgba(255,255,255,0.08);
}

.info-panel h1 {
    font-family: 'Playfair Display', Georgia, serif;
    color: var(--white);
    font-weight: 700;
    font-size: 1.85rem;
    line-height: 1.25;
    margin-bottom: 2.5rem;
    letter-spacing: -0.3px;
}

.content-box {
    max-width: 420px;
    position: relative;
    z-index: 1;
}

/* Gold divider before sections */
.content-box h6 {
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    letter-spacing: 2px;
    color: var(--gold-light);
    text-transform: uppercase;
    font-size: 0.7rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.content-box h6::before,
.content-box h6::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(201,148,58,0.3);
}

.content-box p {
    color: rgba(255,255,255,0.72);
    font-size: 0.92rem;
    line-height: 1.7;
    margin-bottom: 2rem;
    font-weight: 300;
}

/* Bottom badge */
.info-panel-footer {
    position: absolute;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 0.72rem;
    color: rgba(255,255,255,0.3);
    letter-spacing: 1px;
    text-transform: uppercase;
    white-space: nowrap;
}

/* ─── LOGIN PANEL (RIGHT) ───────────────────────────────────── */
.login-panel {
    flex: 1;
    background: var(--white);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 5%;
    position: relative;
}

/* Subtle texture */
.login-panel::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 80% 20%, rgba(201,148,58,0.04) 0%, transparent 60%);
    pointer-events: none;
}

.form-container {
    width: 100%;
    max-width: 400px;
    position: relative;
    z-index: 1;
    animation: slideUp 0.5s cubic-bezier(0.4,0,0.2,1) both;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0);    }
}

/* Welcome tag */
.login-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(201,148,58,0.1);
    color: var(--gold);
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 20px;
    margin-bottom: 1rem;
    border: 1px solid rgba(201,148,58,0.2);
}

.login-panel h2 {
    font-family: 'Playfair Display', Georgia, serif;
    font-weight: 700;
    font-size: 2.1rem;
    margin-bottom: 0.4rem;
    color: var(--text-dark);
    letter-spacing: -0.5px;
}

.subtitle {
    color: var(--text-light);
    margin-bottom: 2.2rem;
    font-size: 0.9rem;
    font-weight: 400;
}

/* ─── FORM ELEMENTS ─────────────────────────────────────────── */
.form-label {
    font-weight: 600;
    font-size: 0.82rem;
    color: var(--text-dark);
    margin-bottom: 0.4rem;
    letter-spacing: 0.3px;
}

.input-group {
    margin-bottom: 1.4rem;
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1.5px solid var(--border);
    transition: border-color var(--transition), box-shadow var(--transition);
}

.input-group:focus-within {
    border-color: var(--navy-light);
    box-shadow: 0 0 0 3px rgba(27,68,128,0.1);
}

.input-group-text {
    background: rgba(15,45,90,0.04);
    color: var(--text-light);
    border: none;
    padding: 0 14px;
    font-size: 0.95rem;
}

.form-control {
    border: none;
    padding: 0.8rem 1rem;
    font-size: 0.92rem;
    background: var(--white);
    color: var(--text-dark);
    font-family: 'DM Sans', sans-serif;
}

.form-control::placeholder {
    color: #b0bac5;
    font-weight: 300;
}

.form-control:focus {
    box-shadow: none;
    outline: none;
    background: #fafcff;
}

/* Show password checkbox */
.form-check {
    margin-bottom: 1.8rem;
}

.form-check-input:checked {
    background-color: var(--navy-light);
    border-color: var(--navy-light);
}

.form-check-label {
    font-size: 0.83rem;
    color: var(--text-mid);
}

/* Sign in button */
.btn-signin {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
    border: none;
    padding: 0.85rem;
    font-weight: 600;
    font-size: 0.92rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    border-radius: var(--radius);
    transition: all var(--transition);
    box-shadow: 0 4px 16px rgba(15,45,90,0.3);
    position: relative;
    overflow: hidden;
}

.btn-signin::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    opacity: 0;
    transition: opacity var(--transition);
}

.btn-signin:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15,45,90,0.35);
}

.btn-signin:hover::before {
    opacity: 1;
}

.btn-signin span {
    position: relative;
    z-index: 1;
}

.btn-signin:active {
    transform: translateY(0);
}

/* Register link */
.reg-text {
    margin-top: 1.6rem;
    text-align: center;
    font-size: 0.87rem;
    color: var(--text-light);
}

.reg-text a {
    color: var(--danger);
    text-decoration: none;
    font-weight: 600;
    transition: opacity var(--transition);
}

.reg-text a:hover {
    opacity: 0.75;
    text-decoration: underline;
}

/* ─── OFFICIALS MODAL ───────────────────────────────────────── */
.modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.modal-header {
    border-bottom: none;
    padding: 28px 28px 20px;
    background: linear-gradient(135deg, var(--navy), var(--navy-light)) !important;
}

.modal-header .modal-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    letter-spacing: -0.2px;
}

.modal-body {
    padding: 24px 28px;
    background: var(--cream);
}

.modal-footer {
    border-top: 1px solid var(--border);
    background: var(--white);
    padding: 14px 24px;
}

/* Official cards */
.official-card {
    background: var(--white);
    border-radius: 16px !important;
    border: 1px solid var(--border) !important;
    transition: all var(--transition);
    box-shadow: var(--shadow-sm) !important;
}

.official-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md) !important;
    border-color: var(--gold-light) !important;
}

.official-name {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 4px;
}

.official-position {
    font-size: 0.72rem;
    letter-spacing: 1.2px;
    color: var(--navy-light);
    font-weight: 500;
    text-transform: uppercase;
}

.official-card .badge {
    font-size: 0.65rem !important;
    background: rgba(15,45,90,0.06) !important;
    color: var(--navy) !important;
    border-color: rgba(15,45,90,0.15) !important;
    padding: 6px 12px !important;
    letter-spacing: 0.8px;
}

/* Avatar ring */
.official-card .rounded-circle.bg-light {
    background: var(--cream) !important;
    border-color: var(--border) !important;
}

/* ─── RESPONSIVE ────────────────────────────────────────────── */
@media (max-width: 992px) {
    .info-panel { display: none; }
    .login-panel { background: var(--cream); }

    .form-container {
        padding: 0 8px;
    }
}

@media (max-width: 576px) {
    .navbar { padding: 10px 20px; }
    .login-panel h2 { font-size: 1.7rem; }
}
        
  
    </style>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><i class="fas fa-landmark"></i> SAN PEDRO IRIGA</a>
        
        <!-- Use navbar-nav for automatic side-by-side alignment in large screens -->
        <div class="navbar-nav ms-auto d-flex flex-row">
            <a class="nav-link me-3" data-bs-toggle="modal" data-bs-target="#officialsModal" href="#">
                <i class="fas fa-users"></i> Barangay Officials
            </a>
            <a class="nav-link" href="resident_registration.php">
                <i class="fas fa-user-plus"></i> Register
            </a>
        </div>
    </div>
</nav>
    <div class="main-container">
        
        <div class="info-panel">
            <div class="content-box">
                <img src="icons/logo.png" alt="Logo">
                <h1>Barangay San Pedro Management System</h1>
                
                <h6>Our Mission</h6>
                <p>To provide proactive, tech-driven administrative services that empower the youth and ensure transparent governance.</p>

                <h6>Our Vision</h6>
                <p>A digitally integrated community where every resident has seamless access to services.</p>
            </div>
        </div>


        <div class="login-panel">
            <div class="form-container">
                <h2>Welcome </h2>
                <p class="subtitle">Please enter your credentials to log in.</p>

                <form method="post">
                   <label class="form-label">Username or Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" placeholder="Enter Email or Phone Number" name="login_identity" required>
                    </div>

                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="passInput" placeholder="Enter password" name="password" required>
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="showCheck" onclick="togglePass()">
                        <label class="form-check-label" for="showCheck" style="font-size: 0.85rem; color: #555;">Show Password</label>
                    </div>

                    <button type="submit" name="login" class="btn btn-primary btn-signin w-100">SIGN IN</button>
                </form>

                <p class="reg-text">
                    Don't have an account? <a href="resident_registration.php">Register here</a>
                </p>
            </div>
        </div>
<!-- Modal -->
<div class="modal fade" id="officialsModal<?= $view['id_user'];?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header with a slight gradient for a premium look -->
            <div class="modal-header bg-primary text-white py-3" style="background: linear-gradient(45deg, #0d6efd, #0a58ca);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users-cog me-3 fa-2x"></i>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Sangguniang Barangay Members</h5>
                        <small class="opacity-75">San Pedro, Iriga City</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4 justify-content-center">
                    <?php 
                    if(!empty($view) && is_array($view)) { 
                        foreach($view as $row) { 
                    ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="official-card h-100 p-3 text-center border rounded-3 shadow-sm transition-hover">
                                <!-- Avatar Container -->
                                <div class="mx-auto mb-3" style="width: 100px; height: 100px;">
                                    <?php if (!empty($row['photo']) && file_exists($row['photo'])): ?>
                                        <img src="<?= $row['photo']; ?>" 
                                             class="rounded-circle shadow-sm border" 
                                             style="width: 100%; height: 100%; object-fit: cover; border: 3px solid #f8f9fa !important;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center h-100 border">
                                            <i class="fas fa-user fa-3x text-secondary"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <h6 class="official-name fw-bold mb-1">
                                    <?= htmlspecialchars($row['fname'] . ' ' . ($row['mi'] ? $row['mi'].'. ' : '') . $row['lname']); ?>
                                </h6>
                                
                                <span class="badge rounded-pill bg-light text-primary border border-primary-subtle text-uppercase px-3 py-2" style="font-size: 0.7rem;">
                                    <?= htmlspecialchars($row['position']); ?>
                                </span>
                            </div>
                        </div>
                    <?php 
                        } 
                    } else {
                        echo '<div class="col-12 text-center py-5">
                                <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No officials currently listed for this record.</p>
                              </div>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        function togglePass() {
            const passField = document.getElementById("passInput");
            passField.type = passField.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>