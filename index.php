<?php
error_reporting(E_ALL ^ E_WARNING);

// Start session using the same helper used by all other pages
require_once __DIR__ . '/classes/security.php';
bmis_session_start();

date_default_timezone_set('Asia/Manila');
$_SESSION['storedate'] = date("Y-m-d");
$_SESSION['storetime'] = date("h:i:a");

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
    <link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0f2d5a">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Brgy San Pedro">
<link rel="apple-touch-icon" href="/icons/pwa/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- responsive tags for screen compatibility -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- custom css --> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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
              .login-error-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #fff0f0;
    border: 1px solid #fca5a5;
    border-left: 4px solid #ef4444;
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 20px;
    animation: shakeError 0.4s ease, fadeInDown 0.3s ease;
    position: relative;
}

.login-error-icon {
    color: #ef4444;
    flex-shrink: 0;
    width: 22px;
    height: 22px;
}

.login-error-icon svg {
    width: 100%;
    height: 100%;
}

.login-error-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
    flex: 1;
}

.login-error-content strong {
    font-size: 14px;
    color: #b91c1c;
    font-weight: 600;
}
.forgot-link {
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--primary-blue);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 20px;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}

.forgot-link:hover {
    color: var(--dark-blue);
    background-color: #e8f0fe;
    border-color: #c2d4f8;
    text-decoration: none;
}
.login-error-content span {
    font-size: 13px;
    color: #7f1d1d;
}

.login-error-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #ef4444;
    cursor: pointer;
    line-height: 1;
    padding: 0 4px;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.login-error-close:hover {
    opacity: 1;
}

@keyframes shakeError {
    0%, 100% { transform: translateX(0); }
    20%       { transform: translateX(-6px); }
    40%       { transform: translateX(6px); }
    60%       { transform: translateX(-4px); }
    80%       { transform: translateX(4px); }
}

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
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
<?php if (!empty($_SESSION['login_error'])): ?>
    <div class="login-error-alert" id="loginErrorAlert">
        <div class="login-error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <div class="login-error-content">
            <strong>Login Failed</strong>
            <span><?= htmlspecialchars($_SESSION['login_error']) ?></span>
        </div>
        <button class="login-error-close" onclick="document.getElementById('loginErrorAlert').remove()">×</button>
    </div>
    <?php unset($_SESSION['login_error']); ?>
    <?php endif; ?>
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

                    <div class="d-flex justify-content-end mb-2">
    <a href="forgot_password.php" class="forgot-link">
        <i class="fas fa-key me-1"></i>Forgot Password?
    </a>
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
<div class="modal fade" id="officialsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header with a slight gradient for a premium look -->
            <div class="modal-header bg-primary text-white py-3" style="background: linear-gradient(45deg, #0f2d5a, #1a4480);">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users-cog me-3 fa-2x" style="color: var(--gold);"></i>
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
                        // Define your explicit target order from the list mode options
                        $positionOrder = [
                            "Punong Barangay",
                            "Secretary",
                            "Treasurer",
                            "Clerk",
                            "Book Keeper",
                            "Committee on Appropriation",
                            "Committee on Health",
                            "Committee on Women and Children",
                            "Committee on Education",
                            "Committee on Peace and Order",
                            "Committee on Infrastructure",
                            "Committee on Ways and Means",
                            "Committee on Agriculture",
                            "Committee on Tourism",
                            "IPMRR Representative",
                            "Sk Chairperson"
                        ];

                        // Sort the $view data based on our position array sequence
                        usort($view, function($a, $b) use ($positionOrder) {
                            $posA = array_search($a['position'], $positionOrder);
                            $posB = array_search($b['position'], $positionOrder);
                            
                            // If a position isn't in our array list, send it to the bottom
                            $posA = ($posA === false) ? 999 : $posA;
                            $posB = ($posB === false) ? 999 : $posB;
                            
                            return $posA <=> $posB;
                        });

                        foreach($view as $row) { 
                    ?>
<?php
                        // ── Role descriptions per position ────────────────────────────────────
                        $roleDescriptions = [
                            'Punong Barangay' => [
                                'icon'  => 'fa-star',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Heads the Sangguniang Barangay and presides over all sessions',
                                    'Signs and executes all barangay ordinances and resolutions',
                                    'Supervises barangay officials and employees',
                                    'Enforces all laws and ordinances applicable within the barangay',
                                    'Represents the barangay in all official transactions and matters',
                                    'Calls and presides meetings of the Sangguniang Barangay',
                                ]
                            ],
                            'Secretary' => [
                                'icon'  => 'fa-file-alt',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Records and prepares minutes of Sangguniang Barangay sessions',
                                    'Maintains barangay records, archives and official documents',
                                    'Certifies barangay records and official documents',
                                    'Issues certified copies of barangay documents upon request',
                                    'Assists in preparing barangay ordinances and resolutions',
                                ]
                            ],
                            'Treasurer' => [
                                'icon'  => 'fa-coins',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Collects and receives all taxes, fees, and other charges due to the barangay',
                                    'Disburses funds as authorized by the Punong Barangay',
                                    'Maintains and safeguards all barangay funds and assets',
                                    'Prepares financial statements and budget reports',
                                    'Issues official receipts for all collections and payments',
                                ]
                            ],
                            'Clerk' => [
                                'icon'  => 'fa-clipboard',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Assists the Secretary in recording and filing documents',
                                    'Processes and releases barangay certifications and clearances',
                                    'Manages day-to-day administrative correspondence',
                                    'Maintains a logbook of daily transactions and requests',
                                ]
                            ],
                            'Book Keeper' => [
                                'icon'  => 'fa-book',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Records all financial transactions in the barangay ledger',
                                    'Prepares financial summaries and accounting reports',
                                    'Reconciles accounts and verifies financial entries',
                                    'Assists the Treasurer in budgeting and audit preparation',
                                ]
                            ],
                            'Committee on Appropriation' => [
                                'icon'  => 'fa-hand-holding-usd',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Reviews and recommends the Annual Budget of the Barangay',
                                    'Evaluates proposed appropriations and expenditures',
                                    'Ensures funds are allocated properly and transparently',
                                    'Monitors utilization of appropriated barangay funds',
                                ]
                            ],
                            'Committee on Health' => [
                                'icon'  => 'fa-heartbeat',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Oversees barangay health programs and sanitation drives',
                                    'Coordinates with health centers for community health services',
                                    'Promotes health awareness and disease prevention campaigns',
                                    'Monitors the health and welfare of barangay constituents',
                                ]
                            ],
                            'Committee on Women and Children' => [
                                'icon'  => 'fa-female',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Formulates programs for the welfare of women and children',
                                    'Addresses cases of abuse and violence against women and children',
                                    'Coordinates with DSWD and other agencies for assistance',
                                    'Promotes gender sensitivity and children\'s rights in the barangay',
                                ]
                            ],
                            'Committee on Education' => [
                                'icon'  => 'fa-graduation-cap',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Supports educational programs and scholarships in the barangay',
                                    'Liaises with schools and DepEd for educational initiatives',
                                    'Promotes literacy and continuing education for all residents',
                                    'Assists learners in need of financial and academic support',
                                ]
                            ],
                            'Committee on Peace and Order' => [
                                'icon'  => 'fa-shield-alt',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Maintains peace, order, and public safety in the barangay',
                                    'Coordinates with PNP and BFP for security concerns',
                                    'Mediates and resolves community disputes and conflicts',
                                    'Oversees the Barangay Tanod and community watch programs',
                                ]
                            ],
                            'Committee on Infrastructure' => [
                                'icon'  => 'fa-hard-hat',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Plans and oversees barangay infrastructure projects',
                                    'Monitors construction and maintenance of roads and pathways',
                                    'Coordinates with DPWH and LGU for infrastructure funding',
                                    'Ensures public facilities are maintained and serviceable',
                                ]
                            ],
                            'Committee on Ways and Means' => [
                                'icon'  => 'fa-chart-line',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Identifies and develops sources of barangay revenue',
                                    'Proposes income-generating projects for the community',
                                    'Reviews and recommends measures to improve financial capacity',
                                    'Assists in maximizing IRA and other resource allocations',
                                ]
                            ],
                            'Committee on Agriculture' => [
                                'icon'  => 'fa-seedling',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Supports farmers and fisherfolk in the barangay',
                                    'Coordinates with DA for agricultural programs and assistance',
                                    'Promotes sustainable farming and food security initiatives',
                                    'Assists in distributing farm inputs and livelihood support',
                                ]
                            ],
                            'Committee on Tourism' => [
                                'icon'  => 'fa-map-marked-alt',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Promotes local tourism attractions and heritage sites',
                                    'Coordinates with DOT and LGU for tourism development',
                                    'Organizes cultural events and festivals in the barangay',
                                    'Supports local tourism-based livelihood programs',
                                ]
                            ],
                            'IPMRR Representative' => [
                                'icon'  => 'fa-hands',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Represents the interests of indigenous peoples in the barangay',
                                    'Coordinates with NCIP for indigenous peoples\' rights and welfare',
                                    'Promotes cultural heritage and traditions of indigenous communities',
                                    'Assists IP members in accessing government programs and services',
                                ]
                            ],
                            'Sk Chairperson' => [
                                'icon'  => 'fa-bolt',
                                'color' => '#0f2d5a',
                                'roles' => [
                                    'Heads the Sangguniang Kabataan and presides over SK sessions',
                                    'Implements youth development programs and activities',
                                    'Manages the SK budget and funds for youth programs',
                                    'Represents the youth sector in Sangguniang Barangay sessions',
                                    'Coordinates with national youth agencies and organizations',
                                ]
                            ],
                        ];
                        // ─────────────────────────────────────────────────────────────────────
                        ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="official-card h-100 p-3 text-center border rounded-3 shadow-sm transition-hover"
                                 style="cursor: pointer;"
                                 onclick="showOfficialRole(
                                    '<?= htmlspecialchars(addslashes($row['fname'] . ' ' . ($row['mi'] ? $row['mi'].'. ' : '') . $row['lname']), ENT_QUOTES) ?>',
                                    '<?= htmlspecialchars(addslashes($row['position']), ENT_QUOTES) ?>',
                                    '<?= !empty($row['photo']) && file_exists($row['photo']) ? htmlspecialchars(addslashes($row['photo']), ENT_QUOTES) : '' ?>'
                                 )">
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

                                <div class="mt-2">
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fas fa-eye me-1"></i>Tap to view roles
                                    </small>
                                </div>
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

<!-- ── Role Detail Modal ────────────────────────────────────────────────────── -->
<div class="modal fade" id="officialRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <!-- Dynamic header -->
            <div class="modal-header text-white py-4 px-4" id="roleModalHeader" style="background: linear-gradient(135deg, #0f2d5a, #1a4480);">
                <div class="d-flex align-items-center gap-3">
                    <div id="roleAvatarWrap" style="width:64px; height:64px; flex-shrink:0;">
                        <!-- filled by JS -->
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0" id="roleModalName">—</h5>
                        <small class="opacity-75" id="roleModalPosition">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">
                <p class="fw-semibold text-uppercase mb-3" style="font-size: 0.78rem; letter-spacing: 1.2px; color: #888;">
                    <i class="fas fa-tasks me-1"></i> Assigned Roles &amp; Responsibilities
                </p>
                <ul class="list-unstyled mb-0" id="roleModalList">
                    <!-- filled by JS -->
                </ul>
            </div>

            <div class="modal-footer border-0 bg-light px-4 py-3">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- ─────────────────────────────────────────────────────────────────────────── -->
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

        // ── Role data injected from PHP ───────────────────────────────────────
        const ROLE_DATA = <?php echo json_encode($roleDescriptions ?? []); ?>;
        // ─────────────────────────────────────────────────────────────────────

        function showOfficialRole(name, position, photoSrc) {
            const data    = ROLE_DATA[position] || null;
            const header  = document.getElementById('roleModalHeader');
            const nameEl  = document.getElementById('roleModalName');
            const posEl   = document.getElementById('roleModalPosition');
            const listEl  = document.getElementById('roleModalList');
            const avatarW = document.getElementById('roleAvatarWrap');

            // Name & position
            nameEl.textContent = name;
            posEl.textContent  = position;

            // Header accent color
            const accentColor = data ? data.color : '#0f2d5a';
            header.style.background = `linear-gradient(135deg, ${accentColor}ee, ${accentColor}99)`;

            // Avatar
            if (photoSrc) {
                avatarW.innerHTML = `<img src="${photoSrc}" class="rounded-circle border border-white border-2"
                    style="width:64px;height:64px;object-fit:cover;">`;
            } else {
                avatarW.innerHTML = `<div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center"
                    style="width:64px;height:64px;">
                    <i class="fas fa-user fa-2x text-white"></i></div>`;
            }

            // Roles list
            if (data && data.roles && data.roles.length) {
                listEl.innerHTML = data.roles.map(r => `
                    <li class="d-flex align-items-start gap-2 mb-3">
                        <span class="mt-1 flex-shrink-0" style="color:${accentColor};">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span style="font-size:0.92rem; color:#444; line-height:1.5;">${r}</span>
                    </li>`).join('');
            } else {
                listEl.innerHTML = `<li class="text-muted" style="font-size:0.9rem;">
                    No specific roles listed for this position.</li>`;
            }

            // Show the role modal (on top of officials modal)
            const roleModal = new bootstrap.Modal(document.getElementById('officialRoleModal'));
            roleModal.show();
        }
    </script>
    <script src="/js/pwa.js"></script>
</body>
</html>