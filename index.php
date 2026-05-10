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
        :root {
            --primary-blue: #007bff;
            --dark-blue: #0056b3;
            --text-gray: #6c757d;
        }

        /* Remove all default margins and padding for true full screen */
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevents accidental scrolling */
            font-family: 'Segoe UI', Roboto, sans-serif;
        }
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid #dee2e6;
            padding: 10px 30px;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-blue) !important;
            font-size: 1.2rem;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin-left: 20px;
            cursor: pointer;
        }

        .nav-link:hover {
            color: var(--primary-blue) !important;
        }
        .main-container {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Left Section: Information (Branding) */
        .info-panel {
            flex: 1;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5%;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .info-panel img {
            width: 120px;
            margin-bottom: 2rem;
        }

        .info-panel h1 {
            color: var(--primary-blue);
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 3rem;
        }

        .content-box {
            max-width: 450px;
        }

        .content-box h6 {
            font-weight: bold;
            letter-spacing: 1px;
            color: #333;
            text-transform: uppercase;
        }

        .content-box p {
            color: var(--text-gray);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        /* Right Section: Login Form */
        .login-panel {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5%;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
        }

        .login-panel h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: var(--text-gray);
            margin-bottom: 2.5rem;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group-text {
            background-color: transparent;
            color: var(--text-gray);
            border-right: none;
        }

        .form-control {
            border-left: none;
            padding: 0.75rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .btn-signin {
            background-color: var(--primary-blue);
            border: none;
            padding: 0.8rem;
            font-weight: bold;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-signin:hover {
            background-color: var(--dark-blue);
        }

        .reg-text {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .reg-text a {
            color: #dc3545;
            text-decoration: none;
            font-weight: bold;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .info-panel {
                display: none; /* Only show form on mobile */
            }
        }
        .official-card {
        background: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .official-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-color: #0d6efd !important;
    }

    .official-name {
        color: #333;
        font-size: 1rem;
    }

    /* Prevents layout shifts if names are long */
    .official-card h6 {
        min-height: 2.4em;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .official-name {
        font-size: 1.1rem;
        color: #2d3436;
        margin-bottom: 2px;
        text-transform: capitalize;
    }

    .official-position {
        font-size: 0.85rem;
        letter-spacing: 1px;
        color: #0d6efd;
    }

    .modal-content {
        border: none;
        border-radius: 20px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 25px;
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