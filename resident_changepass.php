<?php 
    define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
    error_reporting(E_ALL ^ E_WARNING); 
    require('classes/resident.class.php');

    //$view = $residentbmis->view_single_resident($email);
    $userdetails = $residentbmis->get_userdata();
    $residentbmis->resident_changepass();
    //print_r($userdetails);

    
    
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Barangay Management System</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>

    <style>
        /* Responsive Navbar Tweaks */
        .navbar-brand {
            font-size: 1rem;
            white-space: normal;
            max-width: 70%;
        }

        /* Back-to-Top */
        .top-link {
            transition: all 0.25s ease-in-out;
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: inline-flex;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            background-color: #3661D5;
            z-index: 1000;
            text-decoration: none;
        }

        .top-link svg { fill: white; width: 20px; }

        /* Form Styling */
        .input-container {
            display: flex;
            width: 100%;
            margin-bottom: 15px;
            position: relative;
        }

        .icon {
            padding: 15px;
            background: #0d6efd;
            color: white;
            min-width: 50px;
            text-align: center;
            border-radius: 5px 0 0 5px;
        }

        .input-field {
            width: 100%;
            padding: 10px;
            outline: none;
            border: 1px solid #ced4da;
            border-radius: 0 5px 5px 0;
        }

        /* Eye Icon Positioning */
        .field-icon {
            position: absolute;
            right: 15px;
            top: 15px;
            z-index: 2;
            cursor: pointer;
            color: #666;
        }
/* Mobile Bottom Nav Styling */
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
    font-size: 0.7rem; /* Small text for mobile */
    font-weight: 500;
}

.mobile-bottom-nav .nav-item i {
    font-size: 1.4rem; /* Larger icons for easy tapping */
    margin-bottom: 2px;
}

.mobile-bottom-nav .nav-item:active {
    color: #0d6efd;
}

/* Add padding to the bottom of the body so content isn't hidden by the nav */
@media (max-width: 767px) {
    body {
        padding-bottom: 80px;
    }
}
    </style>
</head>

<body class="bg-light">


<!-- DESKTOP NAVBAR (Hidden on Mobile) -->
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

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h3 class="mb-0">Change Password</h3>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Password</label>
                                <div class="input-container">
                                    <i class="fa fa-lock icon"></i>
                                    <input class="input-field" type="password" id="password-field" name="oldpassword" placeholder="Enter Current Password" required>

<input type="hidden" name="oldpasswordverify" value="<?= $userdetails['password']?>">
                                    <span toggle="#old_password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">New Password</label>
                                <div class="input-container">
                                    <i class="fa fa-key icon"></i>
                                    <input class="input-field" id="password1" type="password" name="newpassword" placeholder="Enter New Password" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Verify Password</label>
                                <div class="input-container">
                                    <i class="fa fa-user-lock icon"></i>
                                    <input class="input-field" id="confirm_password" type="password" name="checkpassword" placeholder="Enter Verify Password" required>
                                </div>
                                <div id="message" class="small mt-1"></div>
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button class="btn btn-success btn-lg rounded-pill shadow-sm" type="submit" name="resident_changepass">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Password Visibility
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        // Match Password Validation
        $('#password1, #confirm_password').on('keyup', function () {
            if ($('#password1').val() == $('#confirm_password').val()) {
                $('#message').html('<i class="fas fa-check"></i> Passwords match').css('color', '#198754');
            } else {
                $('#message').html('<i class="fas fa-times"></i> Passwords do not match').css('color', '#dc3545');
            }
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>