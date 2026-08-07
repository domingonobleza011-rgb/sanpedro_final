<?php 
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirectUrl, true, 301);
    exit;
}

// InfinityFree sends a restrictive default Permissions-Policy header that
// blocks camera access on every page ("camera is not allowed in this
// document"), regardless of what the browser's own permission prompt says.
// Sending our own header here overrides it so the Take Photo feature can
// actually request camera access on this page.
header('Permissions-Policy: camera=(self)');

require('classes/resident.class.php');
$residentbmis->create_resident();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Registration - Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    
    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        /* ----- HERO BANNER ----- */
        .hero-registration {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 2.5rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-registration::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 60%;
            height: 200%;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-registration .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-registration .hero-icons {
            display: flex;
            justify-content: center;
            gap: 1.2rem;
            margin-bottom: 0.75rem;
        }
        .hero-registration .hero-icons i {
            font-size: 2.2rem;
            opacity: 0.85;
            background: rgba(255,255,255,0.12);
            padding: 0.6rem;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }
        .hero-registration .hero-icons i:hover {
            transform: translateY(-3px) scale(1.05);
            opacity: 1;
        }
        .hero-registration h1 {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .hero-registration p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .hero-registration .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 0.3rem 1.2rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            letter-spacing: 0.5px;
        }
        @media (max-width: 576px) {
            .hero-registration {
                padding: 1.8rem 1.2rem;
                border-radius: 0 0 24px 24px;
            }
            .hero-registration h1 {
                font-size: 1.6rem;
            }
            .hero-registration .hero-icons i {
                font-size: 1.6rem;
                padding: 0.4rem;
            }
            .hero-registration .hero-icons {
                gap: 0.6rem;
            }
        }

        /* ----- FORM CARD ----- */
        .form-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 2.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        @media (max-width: 576px) {
            .form-card {
                padding: 1.25rem 1rem;
                border-radius: 16px;
            }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.72rem;
            color: #4b4f56;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 1.5px solid #dee2e6;
            padding: 0.6rem 1rem;
            font-size: 0.92rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafbfc;
            height: auto;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.12);
            background: #fff;
        }
        .form-control.is-valid, .form-select.is-valid {
            border-color: #198754;
        }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
        }

        .section-title {
            font-size: 0.8rem;
            font-weight: 700;
            color: #1b74e4;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 0.5rem;
        }
        .section-title i {
            font-size: 1rem;
            color: #1b74e4;
        }
        .section-divider {
            border: 0;
            border-top: 2px solid #e4e6ea;
            margin: 0.25rem 0 1.25rem;
            opacity: 1;
        }

        /* ----- PASSWORD STRENGTH ----- */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #8a8f9a;
            z-index: 10;
            background: none;
            border: none;
            font-size: 1rem;
        }
        .password-wrapper .toggle-password:hover {
            color: #1b74e4;
        }
        .strength-bar {
            height: 4px;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        .strength-text {
            font-size: 0.75rem;
            color: #8a8f9a;
        }

        /* ----- BUTTONS ----- */
        .btn-submit {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            color: #fff;
            padding: 0.7rem 2rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            transition: transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 14px rgba(27, 116, 228, 0.3);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(27, 116, 228, 0.35);
            color: #fff;
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .btn-back {
            border-radius: 50px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1.5px solid #dee2e6;
            color: #65676b;
            background: #fff;
            transition: all 0.2s ease;
        }
        .btn-back:hover {
            background: #f0f2f5;
            border-color: #bcc0c4;
            color: #1c1e21;
        }
        .btn-group-toggle .btn {
            border-radius: 8px;
            font-size: 0.85rem;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            border: 1.5px solid #dee2e6;
            color: #65676b;
            background: #fff;
        }
        .btn-group-toggle .btn.active {
            background: #1b74e4;
            color: #fff;
            border-color: #1b74e4;
        }
        .btn-group-toggle .btn:hover:not(.active) {
            background: #f0f2f5;
        }

        /* ----- CAMERA PANEL ----- */
        #idCameraLive video {
            max-height: 360px;
            object-fit: cover;
            border-radius: 12px;
            background: #1a1a2e;
            width: 100%;
        }
        #idCameraPreview {
            max-height: 360px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #dee2e6;
            width: 100%;
        }
        .camera-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        /* ----- TERMS MODAL ----- */
        .modal-header-custom {
            background: linear-gradient(135deg, #1b74e4, #0a5ecf);
            color: #fff;
            border: none;
        }
        .modal-header-custom .btn-close {
            filter: invert(1);
        }

        /* ----- RESPONSIVE ----- */
        @media (max-width: 576px) {
            .form-card {
                padding: 1rem 0.9rem;
            }
            .form-control, .form-select {
                font-size: 0.88rem;
                padding: 0.5rem 0.8rem;
            }
            .btn-submit, .btn-back {
                font-size: 0.85rem;
                padding: 0.6rem 1.2rem;
                width: 100%;
            }
            .btn-back {
                margin-bottom: 0.5rem;
            }
            .section-title {
                font-size: 0.7rem;
            }
            .hero-registration .hero-badge {
                font-size: 0.65rem;
            }
        }
        @media (max-width: 768px) {
            .d-flex.justify-content-end {
                flex-direction: column-reverse;
                gap: 0.5rem;
            }
            .d-flex.justify-content-end .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- ===== HERO BANNER ===== -->
<div class="hero-registration">
    <div class="hero-content">
        <h1>Resident Registration</h1>
        <p>Create your account to access barangay services</p>
        <span class="hero-badge"><i class="bi bi-building me-1"></i> Barangay San Pedro, Iriga City</span>
    </div>
</div>

<!-- ===== FORM CARD ===== -->
<div class="container">
    <div class="form-card">
        <form method="post" enctype="multipart/form-data" class="was-validated">

            <!-- Personal Information -->
            <div class="section-title">
                <i class="bi bi-person-fill"></i> Personal Information
            </div>
            <hr class="section-divider">

            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <label class="form-label">Last Name</label>
                    <input type="text" class="form-control" name="lname" placeholder="Enter Last Name" required>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">First Name</label>
                    <input type="text" class="form-control" name="fname" placeholder="Enter First Name" required>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Middle Name</label>
                    <input type="text" class="form-control" name="mi" placeholder="Enter Middle Name" required>
                </div>
            </div>

            <!-- Account Credentials -->
            <div class="section-title mt-4">
                <i class="bi bi-key-fill"></i> Account Credentials
            </div>
            <hr class="section-divider">

            <div class="row g-3">
                <div class="col-md-6 col-12">
                    <label class="form-label">Username or Phone Number</label>
                    <input type="text" class="form-control" name="login_identity" placeholder="Enter Email or Phone Number" required>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password-field" name="password" placeholder="Enter Password" required style="padding-right: 40px;">
                        <button type="button" class="toggle-password" toggle="#password-field">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="strength-bar" id="strength-bar" style="width:0%; background:#dc3545;"></div>
                        <small id="strength-text" class="strength-text">Password strength</small>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="section-title mt-4">
                <i class="bi bi-geo-alt-fill"></i> Address Information
            </div>
            <hr class="section-divider">

            <div class="row g-3">
                <div class="col-md-6 col-12">
                    <label class="form-label">House No.</label>
                    <input type="text" class="form-control" name="houseno" placeholder="Enter House No." required>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Street</label>
                    <input type="text" class="form-control" name="street" placeholder="Enter Street" required>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-3 col-6">
                    <label class="form-label">Region</label>
                    <select class="form-select" id="regionSelect" name="region" required>
                        <option value="">Loading regions...</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">Province</label>
                    <select class="form-select" id="provinceSelect" name="province" required disabled>
                        <option value="">Select Region first</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">City/Municipality</label>
                    <select class="form-select" id="citymunSelect" name="municipal" required disabled>
                        <option value="">Select Province first</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">Barangay</label>
                    <select class="form-select" id="barangaySelect" name="brgy" required disabled>
                        <option value="">Select City first</option>
                    </select>
                </div>
                <input type="hidden" id="regionCode" name="region_code">
                <input type="hidden" id="provinceCode" name="province_code">
                <input type="hidden" id="citymunCode" name="municipal_code">
                <input type="hidden" id="barangayCode" name="brgy_code">
            </div>

            <!-- Birth & Demographics -->
            <div class="section-title mt-4">
                <i class="bi bi-calendar-event-fill"></i> Birth &amp; Demographics
            </div>
            <hr class="section-divider">

            <div class="row g-3">
                <div class="col-md-4 col-12">
                    <label class="form-label">Birth Date</label>
                    <input type="date" class="form-control" name="bdate" required>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Birth Place</label>
                    <input type="text" class="form-control" name="bplace" placeholder="City, Province" required>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Nationality</label>
                    <input type="text" class="form-control" name="nationality" placeholder="e.g. Filipino" required>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-3 col-6">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status" required>
                        <option value="">Choose...</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Divorced">Divorced</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">PWD?</label>
                    <select class="form-select" name="pwd" required>
                        <option value="">Choose...</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">Sex</label>
                    <select class="form-select" name="sex" required>
                        <option value="">Choose...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label">Registered Voter?</label>
                    <select class="form-select" name="voter" required>
                        <option value="">Choose...</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-6 col-12">
                    <label class="form-label">Head of the Family?</label>
                    <select class="form-select" name="family_role" required>
                        <option value="">Choose...</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>

            <!-- Valid ID Upload -->
            <div class="section-title mt-4">
                <i class="bi bi-card-image"></i> Valid ID Upload
            </div>
            <hr class="section-divider">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Upload Valid ID (Government-issued)</label>
                    
                    <div class="btn-group btn-group-toggle mb-2" role="group" aria-label="Valid ID input method">
                        <button type="button" id="idModeUploadBtn" class="btn active">
                            <i class="bi bi-upload me-1"></i> Upload File
                        </button>
                        <button type="button" id="idModeCameraBtn" class="btn">
                            <i class="bi bi-camera me-1"></i> Take Photo
                        </button>
                    </div>

                    <!-- Upload panel -->
                    <div id="idUploadPanel">
                        <input type="file" class="form-control" id="valid_id_file_input" name="valid_id_file" accept=".jpg,.jpeg,.png,.pdf" required>
                    </div>

                    <!-- Camera panel -->
                    <div id="idCameraPanel" class="d-none">
                        <div id="idCameraLive">
                            <video id="idVideo" autoplay playsinline muted></video>
                            <div class="mt-2 d-flex gap-2">
                                <button type="button" id="idCaptureBtn" class="btn btn-primary btn-sm">
                                    <i class="bi bi-camera me-1"></i> Capture Photo
                                </button>
                            </div>
                            <div id="idCameraError" class="camera-error d-none"></div>
                        </div>
                        <div id="idCameraPreviewWrap" class="d-none">
                            <img id="idCameraPreview" alt="Captured ID preview">
                            <div class="mt-2">
                                <button type="button" id="idRetakeBtn" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Retake
                                </button>
                            </div>
                        </div>
                        <canvas id="idCanvas" class="d-none"></canvas>
                    </div>

                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-info-circle me-1"></i>
                        Accepted formats: JPG, PNG, or PDF (max 5MB), or take a live photo. 
                        Your registration will be reviewed by the barangay admin before your account is activated.
                    </small>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <hr class="section-divider mt-4">

            <div class="row g-3">
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">
                            I agree to the 
                            <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" style="text-decoration:none; color:#1b74e4; font-weight:600;">
                                Terms and Conditions
                            </a>
                        </label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <hr class="section-divider mt-3">

            <div class="d-flex justify-content-end align-items-center gap-3 flex-wrap">
                <input type="hidden" name="role" value="resident">
                <a class="btn btn-back" href="index.php">
                    <i class="bi bi-arrow-left me-1"></i> Back to Login
                </a>
                <button class="btn-submit" type="submit" name="add_resident">
                    <i class="bi bi-check-circle me-1"></i> Submit Registration
                </button>
            </div>

        </form>
    </div>
</div>

<!-- ===== TERMS MODAL ===== -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="bi bi-file-text me-2"></i> Terms and Conditions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6><strong>1. Data Privacy Act of 2012</strong></h6>
                <p>By registering, you allow Barangay San Pedro to collect and process your personal information in accordance with the Data Privacy Act. Your data will be used solely for barangay management and emergency services.</p>
                
                <h6><strong>2. Accuracy of Information</strong></h6>
                <p>You certify that all information provided is true and correct. Providing false information may lead to the cancellation of your registration or legal action.</p>
                
                <h6><strong>3. Usage Policy</strong></h6>
                <p>This account is for the exclusive use of the registered resident. Any unauthorized use of this system may result in suspension of access.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="document.getElementById('termsCheck').checked = true;">
                    I Understand
                </button>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Password Toggle ──────────────────────────────────────────────────
document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = document.querySelector(this.getAttribute('toggle'));
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    });
});

// ── Password Strength ──────────────────────────────────────────────
document.getElementById('password-field').addEventListener('input', function() {
    const password = this.value;
    const bar = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text');
    
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
    if (password.match(/\d/)) strength++;
    if (password.match(/[^a-zA-Z\d]/)) strength++;

    const colors = ['#dc3545', '#dc3545', '#f59f00', '#fd7e14', '#198754', '#198754'];
    const labels = ['', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
    const pct = [0, 25, 50, 75, 100, 100];
    
    bar.style.width = pct[strength] + '%';
    bar.style.background = colors[strength];
    text.innerHTML = strength > 0 ? 'Strength: <span style="color:' + colors[strength] + ';font-weight:600;">' + labels[strength] + '</span>' : 'Password strength';
});

// ── PSGC Cascading Address ──────────────────────────────────────────
(function () {
    const PSGC_API = 'https://psgc.cloud/api/v2';
    const regionSelect = document.getElementById('regionSelect');
    const provinceSelect = document.getElementById('provinceSelect');
    const citymunSelect = document.getElementById('citymunSelect');
    const barangaySelect = document.getElementById('barangaySelect');
    const regionCode = document.getElementById('regionCode');
    const provinceCode = document.getElementById('provinceCode');
    const citymunCode = document.getElementById('citymunCode');
    const barangayCode = document.getElementById('barangayCode');

    function resetSelect(select, placeholder, disabled = true) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = disabled;
    }

    function fillSelect(select, items, placeholder) {
        select.innerHTML = `<option value="">${placeholder}</option>`;
        items.sort((a, b) => a.name.localeCompare(b.name)).forEach(item => {
            const opt = document.createElement('option');
            opt.value = item.name;
            opt.dataset.code = item.code;
            opt.textContent = item.name;
            select.appendChild(opt);
        });
        select.disabled = false;
    }

    async function fetchJSON(url) {
        const res = await fetch(url);
        if (!res.ok) throw new Error('PSGC API request failed');
        const data = await res.json();
        return Array.isArray(data) ? data : (data.data || []);
    }

    fetchJSON(`${PSGC_API}/regions`).then(regions => fillSelect(regionSelect, regions, 'Select Region'))
        .catch(() => resetSelect(regionSelect, 'Unable to load regions'));

    regionSelect.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        regionCode.value = opt ? (opt.dataset.code || '') : '';
        resetSelect(provinceSelect, 'Select Region first');
        resetSelect(citymunSelect, 'Select Province first');
        resetSelect(barangaySelect, 'Select City first');
        provinceCode.value = '';
        citymunCode.value = '';
        barangayCode.value = '';
        if (!regionCode.value) return;
        fetchJSON(`${PSGC_API}/regions/${regionCode.value}/provinces`).then(provinces => {
            if (provinces.length > 0) {
                fillSelect(provinceSelect, provinces, 'Select Province');
            } else {
                resetSelect(provinceSelect, 'Not applicable', true);
                fetchJSON(`${PSGC_API}/regions/${regionCode.value}/cities-municipalities`)
                    .then(citymuns => fillSelect(citymunSelect, citymuns, 'Select City/Municipality'));
            }
        }).catch(() => resetSelect(provinceSelect, 'Unable to load provinces'));
    });

    provinceSelect.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        provinceCode.value = opt ? (opt.dataset.code || '') : '';
        resetSelect(citymunSelect, 'Select Province first');
        resetSelect(barangaySelect, 'Select City first');
        citymunCode.value = '';
        barangayCode.value = '';
        if (!provinceCode.value) return;
        fetchJSON(`${PSGC_API}/provinces/${provinceCode.value}/cities-municipalities`)
            .then(citymuns => fillSelect(citymunSelect, citymuns, 'Select City/Municipality'))
            .catch(() => resetSelect(citymunSelect, 'Unable to load cities'));
    });

    citymunSelect.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        citymunCode.value = opt ? (opt.dataset.code || '') : '';
        resetSelect(barangaySelect, 'Select City first');
        barangayCode.value = '';
        if (!citymunCode.value) return;
        fetchJSON(`${PSGC_API}/cities-municipalities/${citymunCode.value}/barangays`)
            .then(barangays => fillSelect(barangaySelect, barangays, 'Select Barangay'))
            .catch(() => resetSelect(barangaySelect, 'Unable to load barangays'));
    });

    barangaySelect.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        barangayCode.value = opt ? (opt.dataset.code || '') : '';
    });
})();

// ── Camera / Upload Toggle ──────────────────────────────────────────
(function () {
    const uploadBtn = document.getElementById('idModeUploadBtn');
    const cameraBtn = document.getElementById('idModeCameraBtn');
    const uploadPanel = document.getElementById('idUploadPanel');
    const cameraPanel = document.getElementById('idCameraPanel');
    const fileInput = document.getElementById('valid_id_file_input');
    const video = document.getElementById('idVideo');
    const canvas = document.getElementById('idCanvas');
    const liveWrap = document.getElementById('idCameraLive');
    const previewWrap = document.getElementById('idCameraPreviewWrap');
    const previewImg = document.getElementById('idCameraPreview');
    const captureBtn = document.getElementById('idCaptureBtn');
    const retakeBtn = document.getElementById('idRetakeBtn');
    const cameraError = document.getElementById('idCameraError');

    let stream = null;

    function stopStream() {
        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
    }

    function showError(msg) {
        cameraError.textContent = msg;
        cameraError.classList.remove('d-none');
    }

    async function startCamera() {
        cameraError.classList.add('d-none');
        previewWrap.classList.add('d-none');
        liveWrap.classList.remove('d-none');
        if (!window.isSecureContext) {
            showError('Camera access requires a secure (https://) connection. Please use the Upload File option instead.');
            return;
        }
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showError('Camera access is not supported on this browser. Please use the Upload File option.');
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } });
            video.srcObject = stream;
        } catch (err) {
            if (err.name === 'NotAllowedError') {
                showError('Camera permission was denied. Please allow camera access in your browser settings.');
            } else if (err.name === 'NotFoundError') {
                showError('No camera was found on this device. Please use the Upload File option.');
            } else {
                showError('Could not access the camera. Please use the Upload File option.');
            }
        }
    }

    function switchToUpload() {
        uploadBtn.classList.add('active');
        cameraBtn.classList.remove('active');
        uploadPanel.classList.remove('d-none');
        cameraPanel.classList.add('d-none');
        stopStream();
    }

    function switchToCamera() {
        cameraBtn.classList.add('active');
        uploadBtn.classList.remove('active');
        cameraPanel.classList.remove('d-none');
        uploadPanel.classList.add('d-none');
        startCamera();
    }

    uploadBtn.addEventListener('click', switchToUpload);
    cameraBtn.addEventListener('click', switchToCamera);

    captureBtn.addEventListener('click', function() {
        if (!stream) return;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(function(blob) {
            if (!blob) return;
            const file = new File([blob], 'valid_id_capture_' + Date.now() + '.jpg', { type: 'image/jpeg' });
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;
            previewImg.src = URL.createObjectURL(blob);
            liveWrap.classList.add('d-none');
            previewWrap.classList.remove('d-none');
            stopStream();
        }, 'image/jpeg', 0.92);
    });

    retakeBtn.addEventListener('click', function() {
        fileInput.value = '';
        previewWrap.classList.add('d-none');
        liveWrap.classList.remove('d-none');
        startCamera();
    });

    window.addEventListener('beforeunload', stopStream);
})();

// ── Draft Autosave ──────────────────────────────────────────────────
(function () {
    const DRAFT_KEY = 'bmis_resident_registration_draft';
    const form = document.querySelector('form');
    if (!form) return;

    const TEXT_FIELDS = ['lname', 'fname', 'mi', 'login_identity', 'houseno', 'street', 'bdate', 'bplace', 'nationality'];
    const SELECT_FIELDS = ['status', 'pwd', 'sex', 'voter', 'family_role'];
    const ADDRESS_SELECTS = [
        { select: 'regionSelect', code: 'regionCode' },
        { select: 'provinceSelect', code: 'provinceCode' },
        { select: 'citymunSelect', code: 'citymunCode' },
        { select: 'barangaySelect', code: 'barangayCode' },
    ];

    function readDraft() {
        try { return JSON.parse(localStorage.getItem(DRAFT_KEY) || 'null'); } catch (e) { return null; }
    }

    function saveDraft() {
        const draft = { fields: {}, selects: {}, address: {}, terms: false };
        TEXT_FIELDS.forEach(name => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el) draft.fields[name] = el.value;
        });
        SELECT_FIELDS.forEach(name => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el) draft.selects[name] = el.value;
        });
        ADDRESS_SELECTS.forEach(({ select, code }) => {
            const selEl = document.getElementById(select);
            const codeEl = document.getElementById(code);
            if (selEl) draft.address[select] = { value: selEl.value, code: codeEl ? codeEl.value : '' };
        });
        const terms = document.getElementById('termsCheck');
        draft.terms = terms ? terms.checked : false;
        try { localStorage.setItem(DRAFT_KEY, JSON.stringify(draft)); } catch (e) {}
    }

    function clearDraft() { try { localStorage.removeItem(DRAFT_KEY); } catch (e) {} }

    function showRestoredNotice() {
        const notice = document.createElement('div');
        notice.style.cssText = 'position:fixed; top:24px; right:24px; z-index:9999; background:#fff; border-left:4px solid #1b74e4; border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.13); padding:14px 18px; max-width:340px; font-family:sans-serif; font-size:13px; color:#1c1e21;';
        notice.innerHTML = '<strong><i class="bi bi-arrow-counterclockwise me-1"></i> Draft restored.</strong> We picked up where you left off. <button type="button" id="discardDraftBtn" style="margin-left:8px; background:none; border:none; color:#1b74e4; text-decoration:underline; cursor:pointer; font-size:12px;">Start over</button>';
        document.body.appendChild(notice);
        setTimeout(() => notice.remove(), 8000);
        document.getElementById('discardDraftBtn').addEventListener('click', function() {
            clearDraft();
            window.location.reload();
        });
    }

    function restoreDraft() {
        const draft = readDraft();
        if (!draft) return;
        let restoredSomething = false;
        TEXT_FIELDS.forEach(name => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el && draft.fields[name]) { el.value = draft.fields[name]; restoredSomething = true; }
        });
        SELECT_FIELDS.forEach(name => {
            const el = form.querySelector(`[name="${name}"]`);
            if (el && draft.selects[name]) { el.value = draft.selects[name]; restoredSomething = true; }
        });
        const terms = document.getElementById('termsCheck');
        if (terms) terms.checked = !!draft.terms;

        function restoreLevel(i) {
            if (i >= ADDRESS_SELECTS.length) return;
            const { select } = ADDRESS_SELECTS[i];
            const saved = draft.address[select];
            if (!saved || !saved.value) return;
            const selEl = document.getElementById(select);
            let attempts = 0;
            const poll = setInterval(function() {
                attempts++;
                const hasOption = Array.from(selEl.options).some(o => o.value === saved.value);
                if (hasOption) {
                    clearInterval(poll);
                    selEl.value = saved.value;
                    selEl.dispatchEvent(new Event('change'));
                    restoreLevel(i + 1);
                } else if (attempts > 40) {
                    clearInterval(poll);
                }
            }, 300);
        }
        if (draft.address[ADDRESS_SELECTS[0].select] && draft.address[ADDRESS_SELECTS[0].select].value) {
            restoredSomething = true;
        }
        restoreLevel(0);
        if (restoredSomething) showRestoredNotice();
    }

    const debounce = (fn, ms) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); }; };
    const debouncedSave = debounce(saveDraft, 400);

    [...TEXT_FIELDS, ...SELECT_FIELDS].forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el) el.addEventListener('input', debouncedSave);
    });
    ADDRESS_SELECTS.forEach(({ select }) => {
        const el = document.getElementById(select);
        if (el) el.addEventListener('change', debouncedSave);
    });
    const termsCheckbox = document.getElementById('termsCheck');
    if (termsCheckbox) termsCheckbox.addEventListener('change', saveDraft);

    document.addEventListener('click', function(e) {
        if (e.target && e.target.matches('.modal-footer .btn-primary')) {
            setTimeout(saveDraft, 50);
        }
    });

    const existingToast = document.getElementById('toast');
    const succeeded = existingToast && existingToast.textContent.includes('Registration Submitted');
    if (succeeded) {
        clearDraft();
    } else {
        restoreDraft();
    }
})();
</script>
</body>
</html>
