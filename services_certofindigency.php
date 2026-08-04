<?php 
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
require('classes/main.class.php');
require('classes/resident.class.php');

$userdetails = $bmis->get_userdata();
$bmis->create_certofindigency();
$is_verified = $bmis->isResidentVerified($userdetails['id_resident']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Certificate of Indigency | Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }
        /* ----- HERO BANNER ----- */
        .hero-indigency {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 3rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 2rem;
        }
        .hero-indigency h1 {
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .hero-indigency .doc-icon {
            max-width: 90px;
            height: auto;
            transition: transform 0.3s ease;
        }
        .hero-indigency .doc-icon:hover {
            transform: translateY(-6px) scale(1.05);
        }
        @media (max-width: 576px) {
            .hero-indigency h1 {
                font-size: 1.8rem;
            }
            .hero-indigency .doc-icon {
                max-width: 60px;
            }
            .hero-indigency {
                padding: 2rem 1rem;
                border-radius: 0 0 24px 24px;
            }
        }

        /* ----- PROCEDURE CARDS ----- */
        .step-card {
            background: #fff;
            border: none;
            border-radius: 20px;
            padding: 1.75rem 1rem;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.25s ease, box-shadow 0.3s ease;
            text-align: center;
        }
        .step-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .step-card i {
            font-size: 3.2rem;
            color: #1b74e4;
            margin-bottom: 1rem;
        }
        .step-card h5 {
            font-weight: 700;
            color: #1c1e21;
        }
        .step-card p {
            color: #65676b;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        /* ----- REQUEST BUTTON ----- */
        .btn-request {
            background: #1b74e4;
            border: none;
            border-radius: 60px;
            padding: 0.9rem 3rem;
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
            transition: background 0.2s, transform 0.15s;
            box-shadow: 0 4px 14px rgba(27, 116, 228, 0.35);
        }
        .btn-request:hover {
            background: #0a5ecf;
            color: #fff;
            transform: scale(1.02);
        }
        .btn-request i {
            margin-right: 10px;
        }

        /* ----- MODAL STYLING ----- */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }
        .modal-header {
            background: linear-gradient(135deg, #1b74e4, #0a5ecf);
            color: #fff;
            border: none;
            padding: 1.25rem 1.5rem;
        }
        .modal-header h5 {
            font-weight: 700;
        }
        .modal-body {
            background: #f8f9fa;
            padding: 1.75rem;
        }
        .modal-footer {
            background: #fff;
            border-top: 1px solid #e4e6ea;
            padding: 1rem 1.5rem;
        }
        .form-section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #1b74e4;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .form-section-title:first-of-type {
            margin-top: 0;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid #dce0e4;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            background: #fff;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.15);
        }
        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #4b4f56;
            margin-bottom: 0.3rem;
        }
        .modal .btn-secondary {
            border-radius: 40px;
            padding: 0.6rem 1.8rem;
        }
        .modal .btn-primary {
            border-radius: 40px;
            padding: 0.6rem 2.2rem;
            background: #1b74e4;
            border: none;
            font-weight: 700;
        }
        .modal .btn-primary:hover {
            background: #0a5ecf;
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
        .top-link:hover {
            background: #0a5ecf;
        }

        /* ----- RESPONSIVE TWEAKS ----- */
        @media (max-width: 576px) {
            .step-card {
                padding: 1.25rem 0.75rem;
            }
            .step-card i {
                font-size: 2.5rem;
            }
            .btn-request {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
                width: 100%;
            }
            .modal-body {
                padding: 1.25rem;
            }
        }
        @media (min-width: 768px) {
            .hero-indigency h1 {
                font-size: 3rem;
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

<!-- ===== HERO BANNER ===== -->
<section class="hero-indigency text-center">
    <div class="container">
        <h1 class="mb-3">Certificate of Indigency</h1>
        <p class="mb-4 opacity-75" style="font-size:1.1rem;">Official certification of your indigent status for government and social services</p>
        <div class="d-flex flex-wrap justify-content-center align-items-center gap-4">
            <img class="doc-icon" src="icons/Documents/docu1.png" alt="Document">
            <img class="doc-icon" src="icons/Documents/docu3.png" alt="Document">
            <img class="doc-icon" src="icons/Documents/docu2.png" alt="Document">
        </div>
    </div>
</section>

<!-- ===== PROCEDURE STEPS ===== -->
<section class="container mb-5">
    <h2 class="text-center fw-bold mb-4">How to Apply</h2>
    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="step-card">
                <i class="bi bi-laptop"></i>
                <h5>Step 1: Fill-Up</h5>
                <p>Complete the online request form.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="step-card">
                <i class="bi bi-person-check"></i>
                <h5>Step 2: Assessment</h5>
                <p>Your information will be verified.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="step-card">
                <i class="bi bi-hand-thumbs-up"></i>
                <h5>Step 3: Approval</h5>
                <p>Your request will be reviewed and approved.</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="step-card">
                <i class="bi bi-file-earmark-check"></i>
                <h5>Step 4: Release</h5>
                <p>Claim your Certificate of Indigency.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== REQUEST BUTTON ===== -->
<section class="container text-center mb-5">
    <button type="button" class="btn btn-request" data-bs-toggle="modal" data-bs-target="#indigencyModal">
        <i class="bi bi-pencil-square"></i> Request Form
    </button>
</section>

<!-- ===== MODAL: REQUEST FORM ===== -->
<div class="modal fade" id="indigencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-hand-holding-heart me-2"></i>Certificate of Indigency Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="post" novalidate>
                <div class="modal-body">
                    <!-- Personal Info -->
                    <div class="form-section-title"><i class="bi bi-person me-1"></i> Personal Information</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input name="fname" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['firstname']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input name="mi" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['mname']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input name="lname" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['surname']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-12">
                            <label class="form-label">Nationality</label>
                            <input name="nationality" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['nationality'] ?? '') ?>" required>
                        </div>
                    </div>

                    <!-- Address Info -->
                    <div class="form-section-title mt-4"><i class="bi bi-geo-alt me-1"></i> Residential Address</div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">House No.</label>
                            <input name="houseno" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['houseno']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Street</label>
                            <input name="street" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['street']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Barangay</label>
                            <input name="brgy" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['brgy']) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Municipality</label>
                            <input name="municipal" type="text" class="form-control" value="<?= htmlspecialchars($userdetails['municipal']) ?>" required>
                        </div>
                    </div>

                    <!-- Purpose & Date -->
                    <div class="form-section-title mt-4"><i class="bi bi-file-pen me-1"></i> Request Details</div>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Purpose of Request</label>
                            <select class="form-select" name="purpose" required>
                                <option value="">Choose Purpose...</option>
                                <option value="Job/Employment">Job/Employment</option>
                                <option value="Business Establishment">Business Requirement</option>
                                <option value="Financial Transaction">Financial Transaction</option>
                                <option value="Scholarship">Scholarship</option>
                                <option value="Other important transactions.">Other important transactions.</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Request</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input name="id_resident" type="hidden" value="<?= $userdetails['id_resident'] ?>">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button name="create_certofindigency" type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
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
    topBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Tooltip initialization (if any)
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
</body>
</html>
