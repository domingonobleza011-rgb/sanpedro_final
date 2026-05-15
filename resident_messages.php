<?php 
session_start();
define('BMIS_ROLE_REQUIRED', 'resident');
require_once('secure_header.php'); 
require_once 'classes/main.class.php';
$main = new BMISClass();

if (!isset($_SESSION['userdata']['id_resident'])) {
    header("Location: index.php");
    exit();
}

$userdetails = $_SESSION['userdata'];
$resident_id = $userdetails['id_resident']; 
$is_verified = $bmis->isResidentVerified($userdetails['id_resident']);

// ---- Handle: Send message to admin ----
if (isset($_POST['send_to_admin'])) {
    $message_content = $_POST['admin_message_text'];
    if ($main->sendMessageToAdmin($resident_id, $message_content)) {
        echo "<script>alert('Message sent to Admin!'); window.location.href='resident_messages.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: Could not send message.');</script>";
    }
}

// ---- Handle: Delete message ----
if (isset($_POST['delete_msg'])) {
    $id_msg = $_POST['id_msg']; 
    if ($main->deleteResidentMessage($id_msg)) { 
        echo "<script>alert('Message deleted successfully'); window.location.href='resident_messages.php';</script>";
        exit();
    }
}

// ---- Handle: Upload Valid ID ----
if (isset($_POST['upload_valid_id'])) {
    $message_note = trim($_POST['id_note'] ?? '');
    $upload_error = '';

    if (!isset($_FILES['valid_id_file']) || $_FILES['valid_id_file']['error'] !== UPLOAD_ERR_OK) {
        $upload_error = 'Please select a valid file to upload.';
    } else {
        $file = $_FILES['valid_id_file'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowed_types)) {
            $upload_error = 'Only JPG, PNG, and PDF files are allowed.';
        } elseif ($file['size'] > $max_size) {
            $upload_error = 'File size must not exceed 5MB.';
        } else {
            $upload_dir = 'uploads/valid_ids/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'validid_' . $resident_id . '_' . time() . '.' . $ext;
            $dest = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                if ($main->uploadValidID($resident_id, $new_filename, $file['name'], $file['type'], $message_note)) {
                    $main->sendMessageToAdmin($resident_id, "VALID ID SUBMITTED - Please verify my account. Note: " . ($message_note ?: 'none'));
                    echo "<script>alert('Your valid ID has been submitted! Please wait for admin approval.'); window.location.href='resident_messages.php';</script>";
                    exit();
                } else {
                    $upload_error = 'Could not save the upload record. Please try again.';
                }
            } else {
                $upload_error = 'File upload failed. Please try again.';
            }
        }
    }
    if ($upload_error) {
        echo "<script>alert('Upload Error: " . addslashes($upload_error) . "');</script>";
    }
}

$messages = $main->getResidentMessages($resident_id);
$id_uploads = $main->getResidentIDUploads($resident_id);

$has_pending = false;
$has_approved = false;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $has_pending = true;
    if ($up['status'] === 'approved') $has_approved = true;
}

$auto_open_upload = isset($_GET['upload_id']) && $_GET['upload_id'] == 1;
?>

<!DOCTYPE html> 
<html>
<head> 
    <title>Messages - Barangay San Pedro Iriga</title>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <!-- responsive tags for screen compatibility -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- custom css --> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
        <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .upload-zone { border: 2px dashed #0d6efd; border-radius: 12px; background: #f8f9ff; }
        .status-pending  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-approved { background: #d1e7dd; color: #0f5132; border: 1px solid #198754; }
        .status-rejected { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }
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
<body style="background-color: #f8f9fa;">

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

    <div class="container mt-4 mb-5">

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

        <!-- ID UPLOAD HISTORY -->
        <?php if (!empty($id_uploads)): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3 rounded-top-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-card-image me-2 text-primary"></i>Your ID Submission History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-4">File</th>
                                <th class="py-2">Note</th>
                                <th class="py-2">Date Submitted</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($id_uploads as $up): ?>
                            <tr>
                                <td class="px-4 align-middle">
                                    <i class="bi bi-file-earmark-image me-1 text-primary"></i>
                                    <small><?= htmlspecialchars($up['original_name']); ?></small>
                                </td>
                                <td class="align-middle"><small class="text-muted"><?= htmlspecialchars($up['message_note'] ?: '—'); ?></small></td>
                                <td class="align-middle"><small><?= date('M d, Y h:i A', strtotime($up['upload_date'])); ?></small></td>
                                <td class="align-middle">
                                    <?php if ($up['status'] === 'approved'): ?>
                                        <span class="badge rounded-pill status-approved px-3">&#x2705; Approved</span>
                                    <?php elseif ($up['status'] === 'rejected'): ?>
                                        <span class="badge rounded-pill status-rejected px-3">&#x274C; Rejected</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill status-pending px-3">&#x23F3; Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- MESSAGES TABLE -->
        <div class="header text-center mb-4">
            <div class="text-end mb-3">
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#sendMessageModal">
                    <i class="bi bi-send-fill me-2"></i> Message Admin
                </button>
            </div>
            <h2 class="fw-bold text-dark">Official Messages</h2>
            <p class="text-muted">Direct messages from the Barangay Administration.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light text-primary">
                                    <tr>
                                        <th class="py-3 px-4">Date Sent</th>
                                        <th class="py-3">Message</th>
                                        <th class="py-3 px-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($messages)): ?>
                                    <?php foreach($messages as $msg): ?>
                                        <?php $actual_id = isset($msg['id_msg']) ? $msg['id_msg'] : (isset($msg['id_message']) ? $msg['id_message'] : null); ?>
                                        <tr>
                                            <td class="px-4 align-middle">
                                                <small class="fw-bold"><?php echo date('M d, Y', strtotime($msg['date_sent'])); ?></small>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-truncate d-inline-block" style="max-width: 300px;">
                                                    <?php echo htmlspecialchars($msg['message_text']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 text-end align-middle">
                                                <?php if($actual_id): ?>
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2"
                                                                data-bs-toggle="modal" data-bs-target="#viewMsg<?php echo $actual_id; ?>">
                                                            Read Full
                                                        </button>
                                                        <form action="" method="POST" class="d-inline m-0" onsubmit="return confirm('Delete permanently?');">
                                                            <input type="hidden" name="id_msg" value="<?php echo $actual_id; ?>">
                                                            <button type="submit" name="delete_msg" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center py-5 text-muted">No messages found.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="resident_homepage.php" class="btn btn-light rounded-pill px-4 shadow-sm border">Back to Home</a>
                </div>
            </div>
        </div>

        <!-- View Full Message Modals -->
        <?php foreach($messages as $msg): ?>
            <?php $actual_id = isset($msg['id_msg']) ? $msg['id_msg'] : (isset($msg['id_message']) ? $msg['id_message'] : null); ?>
            <?php if($actual_id): ?>
            <div class="modal fade" id="viewMsg<?php echo $actual_id; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content shadow border-0 rounded-4">
                        <div class="modal-header bg-primary text-white rounded-top-4">
                            <h5 class="modal-title"><i class="fas fa-envelope-open-text me-2"></i> Message Details</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="badge bg-light text-dark border"><?php echo date('F j, Y', strtotime($msg['date_sent'])); ?></span>
                                <span class="badge bg-light text-dark border"><?php echo date('h:i A', strtotime($msg['date_sent'])); ?></span>
                            </div>
                            <div class="p-3 bg-light rounded" style="min-height: 100px; white-space: pre-wrap;">
                                <?php echo htmlspecialchars($msg['message_text']); ?>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- UPLOAD VALID ID MODAL -->
    <div class="modal fade" id="uploadIDModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-warning text-dark rounded-top-4">
                        <h5 class="modal-title fw-bold"><i class="bi bi-upload me-2"></i>Upload Valid ID for Verification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="upload-zone p-4 text-center mb-3">
                            <i class="bi bi-card-image fs-1 text-primary mb-2 d-block"></i>
                            <p class="mb-2 fw-bold">Select your Valid ID photo or PDF</p>
                            <small class="text-muted d-block mb-3">JPG, PNG, or PDF &bull; Max 5MB</small>
                            <input type="file" name="valid_id_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Additional Note <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="id_note" class="form-control" rows="2"
                                      placeholder="e.g., PhilSys ID - front and back combined..."></textarea>
                        </div>
                        <div class="alert alert-info py-2 px-3 rounded-3 mb-0" style="font-size:0.85rem;">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            Accepted: PhilSys, Driver's License, Passport, Voter's ID, Senior Citizen ID, SSS/GSIS ID, PRC ID
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="upload_valid_id" class="btn btn-warning fw-bold rounded-pill px-4">
                            <i class="bi bi-cloud-upload me-2"></i> Submit ID
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SEND MESSAGE MODAL -->
    <div class="modal fade" id="sendMessageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0 rounded-4">
                <form action="" method="POST">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold"><i class="fas fa-paper-plane me-2"></i> New Message to Admin</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <label class="form-label fw-bold">Concern / Message</label>
                        <textarea name="admin_message_text" class="form-control" rows="5" placeholder="Describe your concern..." required></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="send_to_admin" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-send me-1"></i> Send to Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    <?php if ($auto_open_upload && !$has_pending && !$has_approved && !$is_verified): ?>
    window.addEventListener('DOMContentLoaded', function() {
        new bootstrap.Modal(document.getElementById('uploadIDModal')).show();
    });
    <?php endif; ?>
    </script>
</body>
</html>

