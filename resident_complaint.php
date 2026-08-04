<?php
/**
 * resident_complaint.php
 */
error_reporting(E_ALL ^ E_WARNING);
define('BMIS_ROLE_REQUIRED', 'resident');
require('secure_header.php'); 
ini_set('display_errors', 0);
include('classes/resident.class.php');
require_once('classes/conn.php');

$userdetails = $bmis->get_userdata();
$pdo = $conn; 

$success_msg = '';
$error_msg   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {
    $full_name      = trim($_POST['full_name']      ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $address        = trim($_POST['address']        ?? '');
    $category       = trim($_POST['category']       ?? '');
    $custom_category= trim($_POST['custom_category']?? '');
    $description    = trim($_POST['description']    ?? '');
    $location       = trim($_POST['location']       ?? '');

    if ($category === 'Other' && $custom_category !== '') {
        $category = 'Other: ' . $custom_category;
    }

    if ($full_name === '' || $category === '' || $description === '' || $location === '') {
        $error_msg = 'Please fill in all required fields.';
    } elseif ($pdo === null) {
        $error_msg = 'Database connection failed.';
    } else {
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/uploads/complaints/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
            $validated = bmis_validate_image_upload($_FILES['photo']);
            if ($validated['ok']) {
                $filename = 'complaint_' . time() . '_' . $validated['safe_name'];
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $filename)) {
                    $photo_path = 'uploads/complaints/' . $filename;
                }
            }
        }

        $stmt = $pdo->prepare("INSERT INTO tbl_complaints (full_name, contact_number, address, category, description, location, photo_path, status, date_submitted) VALUES (:full_name, :contact_number, :address, :category, :description, :location, :photo_path, 'pending', NOW())");
        $stmt->execute([
            ':full_name'      => $full_name,
            ':contact_number' => $contact_number,
            ':address'        => $address,
            ':category'       => $category,
            ':description'    => $description,
            ':location'       => $location,
            ':photo_path'     => $photo_path,
        ]);
        $success_msg = 'Your complaint has been submitted successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Submit Complaint | Barangay San Pedro</title>
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/pwa/favicon-32x32.png">
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* ----- GLOBAL RESETS ----- */
        body {
            background: #f0f2f5;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            padding-bottom: 85px; /* space for mobile nav */
        }
        @media (min-width: 768px) {
            body { padding-bottom: 0; }
        }

        /* ----- PAGE CONTAINER ----- */
        .page-wrapper {
            max-width: 780px;
            width: 100%;
            margin: 0 auto;
            padding: 12px 12px 0;
        }

        /* ----- HERO HEADER ----- */
        .complaint-hero {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            padding: 2rem 1.5rem;
            border-radius: 0 0 40px 40px;
            color: #fff;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .complaint-hero h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .complaint-hero p {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .complaint-hero i {
            font-size: 2.8rem;
            display: block;
            margin-bottom: 0.5rem;
        }
        @media (max-width: 576px) {
            .complaint-hero {
                padding: 1.5rem 1rem;
                border-radius: 0 0 24px 24px;
            }
            .complaint-hero h1 {
                font-size: 1.5rem;
            }
            .complaint-hero i {
                font-size: 2.2rem;
            }
        }

        /* ----- FORM CARD ----- */
        .form-card {
            background: #fff;
            border-radius: 20px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 2rem 2rem 1.75rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 576px) {
            .form-card {
                padding: 1.25rem 1rem;
            }
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #4b4f56;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 0.3rem;
        }
        .form-label .req {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1.5px solid #dee2e6;
            padding: 0.65rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafbfc;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1b74e4;
            box-shadow: 0 0 0 3px rgba(27, 116, 228, 0.12);
            background: #fff;
        }
        .form-control-sm {
            font-size: 0.88rem;
            padding: 0.5rem 0.9rem;
        }

        /* ----- UPLOAD AREA ----- */
        .upload-area {
            border: 2px dashed #dce0e4;
            border-radius: 14px;
            padding: 1.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.25s ease;
            background: #fafbfc;
        }
        .upload-area:hover {
            border-color: #1b74e4;
            background: #f0f7ff;
        }
        .upload-area i {
            font-size: 2.2rem;
            color: #8a8f9a;
            display: block;
            margin-bottom: 0.5rem;
            transition: color 0.25s;
        }
        .upload-area:hover i {
            color: #1b74e4;
        }
        .upload-area .file-name {
            font-size: 0.85rem;
            color: #65676b;
            margin-bottom: 0;
        }
        .upload-area .file-name.has-file {
            color: #1b74e4;
            font-weight: 600;
        }

        /* ----- SUBMIT BUTTON ----- */
        .btn-submit {
            background: linear-gradient(135deg, #1b74e4 0%, #0a5ecf 100%);
            color: #fff;
            padding: 0.9rem 1.5rem;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1.05rem;
            width: 100%;
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

        /* ----- SUCCESS/ERROR ALERTS ----- */
        .alert-custom {
            border-radius: 16px;
            padding: 0.9rem 1.25rem;
            border: none;
            margin-bottom: 1.25rem;
        }
        .alert-custom-success {
            background: #d1e7dd;
            color: #0f5132;
            border-left: 5px solid #198754;
        }
        .alert-custom-danger {
            background: #f8d7da;
            color: #842029;
            border-left: 5px solid #dc3545;
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
            .form-card {
                padding: 1rem 0.9rem;
            }
            .btn-submit {
                font-size: 0.95rem;
                padding: 0.75rem 1rem;
            }
            .upload-area {
                padding: 1.25rem;
            }
            .upload-area i {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

<div class="page-wrapper">

    <!-- HERO -->
    <div class="complaint-hero">
        <i class="bi bi-info-circle-fill"></i>
        <h1>Submit a Complaint</h1>
        <p>Report issues to the Barangay Office for immediate action</p>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">

        <?php if ($success_msg): ?>
            <div class="alert-custom alert-custom-success">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert-custom alert-custom-danger">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error_msg ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">

                <!-- Full Name -->
                <div class="col-12">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-control" 
                           value="<?= htmlspecialchars($userdetails['firstname'] . ' ' . $userdetails['surname']) ?>" required>
                </div>

                <!-- Contact & Address -->
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" 
                           placeholder="09123456789" 
                           value="<?= htmlspecialchars($userdetails['contact'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" 
                           placeholder="Purok, Barangay" 
                           value="<?= htmlspecialchars($userdetails['address'] ?? '') ?>">
                </div>

                <!-- Category -->
                <div class="col-12">
                    <label class="form-label">Category <span class="req">*</span></label>
                    <select name="category" class="form-select" id="catSelect" required onchange="checkOther(this)">
                        <option value="">Select Category...</option>
                        <option value="Infrastructure">Infrastructure (Roads, Lights)</option>
                        <option value="Sanitation">Environment &amp; Sanitation</option>
                        <option value="Peace and Order">Peace &amp; Order</option>
                        <option value="Utilities">Public Utilities</option>
                        <option value="Other">Other Issues</option>
                    </select>
                </div>

                <!-- Custom Category (Other) -->
                <div class="col-12" id="otherInput" style="display:none;">
                    <label class="form-label">Specify Issue</label>
                    <input type="text" name="custom_category" class="form-control" 
                           placeholder="What kind of issue?">
                </div>

                <!-- Location -->
                <div class="col-12">
                    <label class="form-label">Exact Location <span class="req">*</span></label>
                    <input type="text" name="location" class="form-control" 
                           placeholder="e.g. Purok 4, near the Health Center" required>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <label class="form-label">Description <span class="req">*</span></label>
                    <textarea name="description" class="form-control" rows="4" 
                              placeholder="Describe the situation in detail..." required></textarea>
                </div>

                <!-- Photo Upload -->
                <div class="col-12">
                    <label class="form-label">Attach Photo</label>
                    <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-camera"></i>
                        <p class="file-name" id="fileName">Click to upload photo of the incident</p>
                        <small class="text-muted">JPG, PNG, or GIF &bull; Max 5MB</small>
                        <input type="file" name="photo" id="fileInput" hidden 
                               accept=".jpg,.jpeg,.png,.gif" onchange="updateFileName(this)">
                    </div>
                </div>

                <!-- Submit -->
                <div class="col-12 pt-2">
                    <button type="submit" name="submit_complaint" class="btn-submit">
                        <i class="bi bi-send-fill me-2"></i> Submit Complaint
                    </button>
                </div>

            </div>
        </form>
    </div>

</div><!-- end page-wrapper -->

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

    // Show/hide "Other" category input
    function checkOther(select) {
        const otherInput = document.getElementById('otherInput');
        otherInput.style.display = (select.value === 'Other') ? 'block' : 'none';
    }

    // Update file name display
    function updateFileName(input) {
        const fileName = document.getElementById('fileName');
        if (input.files.length > 0) {
            fileName.innerText = '📎 ' + input.files[0].name;
            fileName.classList.add('has-file');
        } else {
            fileName.innerText = 'Click to upload photo of the incident';
            fileName.classList.remove('has-file');
        }
    }
</script>
</body>
</html>