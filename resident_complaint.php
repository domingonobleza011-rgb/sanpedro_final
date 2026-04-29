<?php
/**
 * resident_complaint.php
 */
error_reporting(E_ALL ^ E_WARNING);
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
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
                $filename = 'complaint_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint | San Pedro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.95);
            --primary-blue: #0d6efd; /* Matches Bootstrap Primary */
            --dark-blue: #1e3a6e;
        }

        body {
            background-color: #f4f7f6;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        /* Navbar Customization */
        .navbar { padding: 0.8rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .dropdown-menu .btn { text-align: left; width: 100%; padding: 10px 20px; font-size: 0.9rem; }
        .dropdown-menu .btn:hover { background: #f8f9fa; }

        /* Form Card */
        .glass-card {
            background: var(--glass-bg);
            border-radius: 20px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            padding: 2.5rem;
            max-width: 800px;
            margin: 2rem auto;
        }

        .form-label { font-weight: 600; color: var(--dark-blue); font-size: 0.85rem; text-transform: uppercase; }
        .upload-area { border: 2px dashed #cbd5e0; border-radius: 15px; padding: 25px; text-align: center; cursor: pointer; transition: 0.3s; }
        .upload-area:hover { border-color: var(--primary-blue); background: #fff; }
        .btn-submit { background: var(--dark-blue); color: white; padding: 12px; border-radius: 10px; font-weight: 700; width: 100%; border: none; transition: 0.3s; }
        .btn-submit:hover { background: #152950; transform: translateY(-2px); }
        .req { color: #dc3545; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="resident_homepage.php">Barangay San Pedro Management System</a>
        
        <div class="d-flex align-items-center ms-auto">
            <a href="resident_homepage.php" class="btn btn-primary me-3">
                <i class="fa fa-home fa-lg"></i> Home
            </a>
            
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i>
                    <?= $userdetails['surname'];?>, <?= $userdetails['firstname'];?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                    <li><a class="btn" href="resident_profile.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-user"></i> &nbsp; Profile</a></li>
                    <li><a class="btn" href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'];?>"><i class="fas fa-lock"></i> &nbsp; Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="btn text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp; Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="glass-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold" style="color: var(--dark-blue);">Submit a Complaint</h2>
            <p class="text-muted">Fill out the details below to notify the Barangay Office.</p>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success border-0 rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-12">
                    <label class="form-label">Full Name <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($userdetails['firstname'] . ' ' . $userdetails['surname']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" placeholder="09123456789" value="<?= htmlspecialchars($userdetails['contact'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category <span class="req">*</span></label>
                    <select name="category" class="form-select" id="catSelect" required onchange="checkOther(this)">
                        <option value="">Select Category...</option>
                        <option value="Infrastructure">Infrastructure (Roads, Lights)</option>
                        <option value="Sanitation">Environment & Sanitation</option>
                        <option value="Peace and Order">Peace & Order</option>
                        <option value="Utilities">Public Utilities</option>
                        <option value="Other">Other Issues</option>
                    </select>
                </div>

                <div class="col-12" id="otherInput" style="display:none;">
                    <label class="form-label">Specify Issue</label>
                    <input type="text" name="custom_category" class="form-control" placeholder="What kind of issue?">
                </div>

                <div class="col-12">
                    <label class="form-label">Exact Location <span class="req">*</span></label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Purok 4, near the Health Center" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Description <span class="req">*</span></label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe the situation..." required></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Attach Photo</label>
                    <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-camera fs-2 text-muted"></i>
                        <p class="mb-0 mt-2 text-muted small" id="fileName">Click to upload photo of the incident</p>
                        <input type="file" name="photo" id="fileInput" hidden onchange="updateFileName(this)">
                    </div>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" name="submit_complaint" class="btn-submit">SUBMIT COMPLAINT</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function checkOther(select) {
        document.getElementById('otherInput').style.display = (select.value === 'Other') ? 'block' : 'none';
    }
    function updateFileName(input) {
        if (input.files.length > 0) {
            document.getElementById('fileName').innerText = "File selected: " + input.files[0].name;
            document.getElementById('fileName').style.color = "#0d6efd";
        }
    }
</script>
</body>
</html>