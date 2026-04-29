<?php
/**
 * resident_complaint.php
 * Resident-facing page: submit a barangay complaint.
 */

error_reporting(E_ALL ^ E_WARNING);
ini_set('display_errors', 0);

// Use the system's own classes — same as resident_homepage.php
include('classes/resident.class.php');
require_once('classes/conn.php');

$userdetails = $bmis->get_userdata();

$pdo = $conn; // reuse the system's existing PDO connection

$success_msg = '';
$error_msg   = '';

// ---- Handle form submission ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {
    $full_name      = trim($_POST['full_name']      ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $address        = trim($_POST['address']        ?? '');
    $category       = trim($_POST['category']       ?? '');
    $custom_category= trim($_POST['custom_category']?? '');
    $description    = trim($_POST['description']    ?? '');
    $location       = trim($_POST['location']       ?? '');

    // Use custom category if "Other" was selected
    if ($category === 'Other' && $custom_category !== '') {
        $category = 'Other: ' . $custom_category;
    }

    // Basic validation
    if ($full_name === '' || $category === '' || $description === '') {
        $error_msg = 'Please fill in all required fields.';
    } elseif ($pdo === null) {
        $error_msg = 'Database connection failed. Please contact the barangay office.';
    } else {
        // Optional photo upload
        $photo_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/uploads/complaints/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $ext        = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed    = ['jpg','jpeg','png','gif','webp'];
            if (in_array($ext, $allowed)) {
                $filename   = 'complaint_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest       = $upload_dir . $filename;
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                    $photo_path = 'uploads/complaints/' . $filename;
                }
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO tbl_complaints
                (full_name, contact_number, address, category, description, location, photo_path, status, date_submitted)
            VALUES
                (:full_name, :contact_number, :address, :category, :description, :location, :photo_path, 'pending', NOW())
        ");
        $stmt->execute([
            ':full_name'      => $full_name,
            ':contact_number' => $contact_number,
            ':address'        => $address,
            ':category'       => $category,
            ':description'    => $description,
            ':location'       => $location,
            ':photo_path'     => $photo_path,
        ]);

        $success_msg = 'Your complaint has been submitted successfully! The barangay office will review and act on it shortly.';
    }
}

$categories = [
    'Infrastructure'        => ['Damaged Road / Pothole', 'Broken Street Light', 'Clogged Drainage / Flood', 'Damaged Bridge or Pathway', 'Water Supply Issue'],
    'Environment / Sanitation' => ['Tall Grass / Overgrown Vegetation', 'Illegal Dumping / Garbage', 'Stray Animals', 'Foul Odor / Pollution', 'Open Burning'],
    'Peace & Order'         => ['Noise Complaint', 'Illegal Structure / Encroachment', 'Vandalism', 'Suspicious Activity'],
    'Utilities'             => ['Power Outage (Streetlight)', 'Water Pipe Leakage', 'Internet / Signal Issue'],
    'Other'                 => ['Other'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Submit a Complaint – Barangay System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<style>
    :root {
        --primary:   #1e3a6e;
        --accent:    #f0a500;
        --light-bg:  #f4f7fb;
        --card-bg:   #ffffff;
        --border:    #dde3ee;
        --text-dark: #1a2340;
        --text-muted:#6b7a99;
    }

    * { box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', system-ui, sans-serif;
        background: var(--light-bg);
        color: var(--text-dark);
        min-height: 100vh;
    }

    /* ── Hero Header ── */
    .complaint-hero {
        background: #2a5298;
        color: #fff;
        padding: 48px 24px 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .complaint-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .complaint-hero .hero-icon {
        width: 72px; height: 72px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem;
        border: 2px solid rgba(255,255,255,0.3);
    }
    .complaint-hero h1 { font-size: 2rem; font-weight: 700; margin-bottom: 6px; }
    .complaint-hero p  { font-size: 1rem; opacity: 0.85; margin: 0; }
    .accent-bar {
        width: 60px; height: 4px;
        background: var(--accent);
        border-radius: 2px;
        margin: 14px auto 0;
    }

    /* ── Card ── */
    .complaint-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(30,58,110,0.10);
        padding: 36px 32px;
        margin: -28px auto 40px;
        max-width: 780px;
        position: relative;
        z-index: 2;
    }

    .section-label {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--primary);
        border-left: 3px solid var(--accent);
        padding-left: 8px;
        margin-bottom: 18px;
    }

    /* ── Category pills ── */
    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: 10px;
        margin-bottom: 24px;
    }
    .cat-pill {
        border: 2px solid var(--border);
        border-radius: 10px;
        padding: 10px 14px;
        cursor: pointer;
        transition: all .2s;
        display: flex; align-items: center; gap: 10px;
        font-size: 0.875rem; font-weight: 500;
        color: var(--text-dark);
        background: #fff;
        user-select: none;
    }
    .cat-pill:hover { border-color: var(--primary); background: #f0f4fb; }
    .cat-pill.selected { border-color: var(--primary); background: #e8eef9; color: var(--primary); }
    .cat-pill .pill-icon { font-size: 1.25rem; flex-shrink: 0; }

    /* ── Form Controls ── */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1.5px solid var(--border);
        font-size: 0.925rem;
        transition: border-color .2s, box-shadow .2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30,58,110,0.12);
    }
    textarea.form-control { min-height: 120px; resize: vertical; }

    /* Photo upload zone */
    .upload-zone {
        border: 2px dashed var(--border);
        border-radius: 10px;
        padding: 28px 16px;
        text-align: center;
        cursor: pointer;
        transition: all .2s;
        background: #fafbff;
    }
    .upload-zone:hover { border-color: var(--primary); background: #f0f4fb; }
    .upload-zone input[type=file] { display: none; }
    .upload-zone .upload-icon { font-size: 2.5rem; color: #b0bbcc; margin-bottom: 8px; }
    .upload-zone .upload-text { font-size: 0.875rem; color: var(--text-muted); }
    #preview-img { max-height: 180px; border-radius: 8px; display: none; margin-top: 12px; }

    /* ── Submit Button ── */
    .btn-submit {
        background: linear-gradient(135deg, var(--primary), #2a5298);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 13px 36px;
        font-size: 1rem; font-weight: 600;
        letter-spacing: 0.02em;
        transition: transform .15s, box-shadow .15s;
        width: 100%;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30,58,110,0.3);
        color: #fff;
    }

    /* ── Alert / Status ── */
    .alert-custom-success {
        background: #e6f7ee; border: 1.5px solid #52c97c;
        color: #1a5c36; border-radius: 10px; padding: 16px 20px;
    }
    .alert-custom-error {
        background: #fff0f0; border: 1.5px solid #f05252;
        color: #7a1010; border-radius: 10px; padding: 16px 20px;
    }

    /* ── Required asterisk ── */
    .req { color: #e03; font-size: 0.85em; }

    /* ── Step indicator ── */
    .step-dots { display: flex; gap: 8px; justify-content: center; margin-bottom: 28px; }
    .step-dot {
        width: 32px; height: 6px; border-radius: 3px;
        background: var(--border); transition: background .3s;
    }
    .step-dot.active { background: var(--primary); }
    .step-dot.done   { background: var(--accent); }

    @media (max-width: 576px) {
        .complaint-card { padding: 24px 16px; margin: -20px 12px 24px; }
        .complaint-hero h1 { font-size: 1.5rem; }
    }

    /* ── Back to Home button — matches about_us.php style ── */
    .btn-home-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 2px solid rgba(255,255,255,0.75);
        color: #fff;
        background: transparent;
        border-radius: 20px;
        padding: 8px 22px;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-decoration: none;
        transition: background .2s, color .2s, border-color .2s;
        margin-bottom: 18px;
    }
    .btn-home-outline:hover {
        background: RoyalBlue;
        border-color: RoyalBlue;
        color: #fff;
    }
</style>
</head>
<body>

<!-- ═══════ NAVBAR — matches resident_homepage.php exactly ═══════ -->
<nav class="navbar navbar-dark bg-primary sticky-top">
    <a class="navbar-brand" href="resident_homepage.php">
        BARANGAY SAN PEDRO
    </a>
    <div class="dropdown ml-auto">
        <button title="Your Account" class="btn btn-primary dropdown-toggle" style="margin-right: 2px;" type="button" data-toggle="dropdown">
            <?= $userdetails['surname'] ?? 'Resident' ?>, <?= $userdetails['firstname'] ?? '' ?> <?= $userdetails['mname'] ?? '' ?>
            <span class="caret" style="margin-left: 2px;"></span>
        </button>
        <ul class="dropdown-menu" style="width: 175px;">
            <a class="btn" href="resident_profile.php?id_resident=<?= $userdetails['id_resident'] ?? '' ?>">
                <i class="fas fa-user"> &nbsp; </i>Personal Profile
            </a>
            <a class="btn" href="resident_changepass.php?id_resident=<?= $userdetails['id_resident'] ?? '' ?>">
                <i class="fas fa-lock">&nbsp;</i> Change Password
            </a>
            <a class="btn" href="logout.php">
                <i class="fas fa-sign-out-alt">&nbsp;</i> Logout
            </a>
        </ul>
    </div>
</nav>

<!-- ═══════ BACK BUTTON + PAGE HEADER ═══════ -->
<div class="complaint-hero">
    <br>
    <div class="hero-icon"><i class="bi bi-megaphone-fill"></i></div>
    <h1>Submit a Complaint</h1>
    <p>Barangay San Pedro · Iriga City</p>
    <div class="accent-bar"></div>
</div>

<!-- ═══════ CARD ═══════ -->
<div class="complaint-card">

    <!-- Success / Error messages -->
    <?php if ($success_msg): ?>
    <div class="alert-custom-success mb-4 d-flex align-items-start gap-3">
        <i class="bi bi-check-circle-fill fs-4 flex-shrink-0"></i>
        <div>
            <strong>Complaint Submitted!</strong><br>
            <?= htmlspecialchars($success_msg) ?>
        </div>
    </div>
    <?php elseif ($error_msg): ?>
    <div class="alert-custom-error mb-4 d-flex align-items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
        <div><strong>Error:</strong> <?= htmlspecialchars($error_msg) ?></div>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="complaint-form" novalidate>

        <!-- ── SECTION 1: Your Info ── -->
        <div class="section-label"><i class="bi bi-person me-1"></i> Your Information</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Name <span class="req">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="e.g. Juan dela Cruz" required
                       value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" placeholder="09XX-XXX-XXXX"
                       value="<?= htmlspecialchars($_POST['contact_number'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Home Address / Purok</label>
                <input type="text" name="address" class="form-control" placeholder="e.g. Purok 3, Zone 5"
                       value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
            </div>
        </div>

        <hr class="my-4">

        <!-- ── SECTION 2: Category ── -->
        <div class="section-label"><i class="bi bi-tag me-1"></i> Complaint Category <span class="req">*</span></div>

        <div class="category-grid" id="category-grid">
            <?php
            $iconMap = [
                'Damaged Road / Pothole'           => ['bi-cone-striped',     '#e67e22'],
                'Broken Street Light'              => ['bi-lightbulb-off',    '#f0a500'],
                'Clogged Drainage / Flood'         => ['bi-droplet-fill',     '#2980b9'],
                'Damaged Bridge or Pathway'        => ['bi-bricks',           '#7f8c8d'],
                'Water Supply Issue'               => ['bi-water',            '#1a7abf'],
                'Tall Grass / Overgrown Vegetation'=> ['bi-tree',             '#27ae60'],
                'Illegal Dumping / Garbage'        => ['bi-trash-fill',       '#c0392b'],
                'Stray Animals'                    => ['bi-emoji-dizzy',      '#8e44ad'],
                'Foul Odor / Pollution'            => ['bi-wind',             '#16a085'],
                'Open Burning'                     => ['bi-fire',             '#e74c3c'],
                'Noise Complaint'                  => ['bi-volume-up-fill',   '#d35400'],
                'Illegal Structure / Encroachment' => ['bi-building-x',       '#2c3e50'],
                'Vandalism'                        => ['bi-paint-bucket',     '#8e44ad'],
                'Suspicious Activity'              => ['bi-eye-fill',         '#c0392b'],
                'Power Outage (Streetlight)'       => ['bi-plug-fill',        '#f39c12'],
                'Water Pipe Leakage'               => ['bi-droplet-half',     '#2980b9'],
                'Internet / Signal Issue'          => ['bi-wifi-off',         '#7f8c8d'],
                'Other'                            => ['bi-question-circle',  '#95a5a6'],
            ];
            $selected_cat = $_POST['category'] ?? '';
            foreach ($categories as $group => $items):
                foreach ($items as $item):
                    [$icon, $color] = $iconMap[$item] ?? ['bi-flag', '#1e3a6e'];
                    $sel = ($selected_cat === $item) ? ' selected' : '';
            ?>
            <div class="cat-pill<?= $sel ?>" data-value="<?= htmlspecialchars($item) ?>" onclick="selectCategory(this)">
                <i class="bi <?= $icon ?> pill-icon" style="color:<?= $color ?>"></i>
                <span><?= htmlspecialchars($item) ?></span>
            </div>
            <?php endforeach; endforeach; ?>
        </div>

        <!-- Hidden real input -->
        <input type="hidden" name="category" id="category-input" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>">

        <!-- Custom "Other" field -->
        <div id="custom-category-wrap" style="display:<?= (str_starts_with($_POST['category'] ?? '', 'Other')) ? 'block' : 'none' ?>;" class="mb-3">
            <label class="form-label fw-semibold">Please describe the type of complaint</label>
            <input type="text" name="custom_category" class="form-control"
                   placeholder="Briefly describe the category"
                   value="<?= htmlspecialchars($_POST['custom_category'] ?? '') ?>">
        </div>

        <hr class="my-4">

        <!-- ── SECTION 3: Details ── -->
        <div class="section-label"><i class="bi bi-card-text me-1"></i> Complaint Details</div>
        <div class="row g-3 mb-4">
            <div class="col-12">
                <label class="form-label fw-semibold">Exact Location / Area <span class="req">*</span></label>
                <input type="text" name="location" class="form-control"
                       placeholder="e.g. Near Purok 4 Chapel, along Rizal St."
                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description <span class="req">*</span></label>
                <textarea name="description" class="form-control"
                          placeholder="Please provide as much detail as possible about the problem..." required
                ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- ── SECTION 4: Photo ── -->
        <div class="section-label"><i class="bi bi-camera me-1"></i> Attach a Photo <small class="text-muted fw-normal">(optional)</small></div>
        <div class="upload-zone mb-4" onclick="document.getElementById('photo-input').click()">
            <input type="file" name="photo" id="photo-input" accept="image/*" onchange="previewPhoto(this)">
            <div class="upload-icon"><i class="bi bi-image"></i></div>
            <div class="upload-text">Click to upload a photo of the issue<br><small>JPG, PNG, GIF, WEBP · Max 5 MB</small></div>
            <img id="preview-img" src="#" alt="Preview">
        </div>

        <!-- ── SUBMIT ── -->
        <button type="submit" name="submit_complaint" class="btn btn-submit">
            <i class="bi bi-send-fill me-2"></i> Submit Complaint
        </button>

        <p class="text-center text-muted mt-3" style="font-size:0.8rem;">
            <i class="bi bi-shield-lock me-1"></i>
            Your information is kept confidential and used only for resolving your complaint.
        </p>
    </form>
</div><!-- /.complaint-card -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function selectCategory(el) {
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    const val = el.dataset.value;
    document.getElementById('category-input').value = val;
    document.getElementById('custom-category-wrap').style.display =
        val === 'Other' ? 'block' : 'none';
}

function previewPhoto(input) {
    const img = document.getElementById('preview-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
        document.querySelector('.upload-text').innerHTML =
            '<strong>' + input.files[0].name + '</strong> selected ✓';
    }
}

// Basic client-side validation
document.getElementById('complaint-form').addEventListener('submit', function(e) {
    const cat = document.getElementById('category-input').value;
    if (!cat) {
        e.preventDefault();
        alert('Please select a complaint category.');
    }
});
</script>
</body>
</html>