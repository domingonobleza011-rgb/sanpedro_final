<?php 
    define('BMIS_ROLE_REQUIRED', 'admin');
require('secure_header.php');
require_once 'classes/main.class.php'; 
$systemObject = new BMISClass();
$userdetails = $systemObject->get_userdata();
$admin_name = isset($userdetails['fname']) ? $userdetails['fname'] . ' ' . $userdetails['lname'] : 'Admin';

// ---- Handle: Verify (approve) resident ----
if (isset($_POST['approve_resident'])) {
    $id_resident = (int)$_POST['id_resident'];
    $id_upload   = (int)$_POST['id_upload'];
    if ($systemObject->approveResidentVerification($id_resident, $id_upload, $admin_name)) {
        header("Location: admn_messages.php?status=approved");
    } else {
        header("Location: admn_messages.php?status=error");
    }
    exit();if (isset($_POST['delete_upload'])) {
    $id_upload = $_POST['id_upload'];
    if ($bmis->delete_upload_record($id_upload)) {
        echo "<script>alert('Record deleted successfully'); window.location.href='admin_verification.php';</script>";
    } else {
        echo "<script>alert('Error deleting record');</script>";
    }
}
}
if (isset($_POST['delete_upload'])) { // Make sure this is 'delete_upload'
    $id_upload = $_POST['id_upload'];
    $bmis->delete_upload_record($id_upload);
    // Add a redirect to refresh the page and show changes
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit();
}

// ---- Handle: Reject resident ID ----
if (isset($_POST['reject_resident'])) {
    $id_resident = (int)$_POST['id_resident'];
    $id_upload   = (int)$_POST['id_upload'];
    $reason      = $_POST['reject_reason'] ?? '';
    if ($systemObject->rejectResidentVerification($id_resident, $id_upload, $admin_name, $reason)) {
        header("Location: admn_messages.php?status=rejected");
    } else {
        header("Location: admn_messages.php?status=error");
    }
    exit();
}

// ---- Handle: Delete message ----
if (isset($_POST['delete_msg'])) {
    $id = $_POST['id_admin_msg'];
    $systemObject->deleteMessage($id);
    header("Location: admn_messages.php?status=deleted");
    exit();
}

// ---- Fetch data ----
$messages     = $systemObject->viewMessages();
$id_uploads   = $systemObject->getPendingIDUploads();
$pending_count = 0;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $pending_count++;
}
?>
<?php include('dashboard_sidebar_start.php'); ?>
<!DOCTYPE html> 
<html>
<head> 
    <title>Messages - Barangay Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .status-pending  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-approved { background: #d1e7dd; color: #0f5132; border: 1px solid #198754; }
        .status-rejected { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }
        .nav-tabs .nav-link { font-weight: 600; }
        .id-preview img { max-width: 100%; max-height: 350px; border-radius: 10px; border: 1px solid #dee2e6; }
    </style>
</head>
<body>

<?php if(isset($_GET['status'])): ?>
<div class="alert alert-<?= $_GET['status'] === 'approved' ? 'success' : ($_GET['status'] === 'rejected' ? 'warning' : ($_GET['status'] === 'deleted' ? 'info' : 'danger')) ?> alert-dismissible fade show m-3" role="alert">
    <?php
    switch($_GET['status']) {
        case 'approved': echo '&#x2705; Resident account has been verified successfully.'; break;
        case 'rejected': echo '&#x274C; ID submission has been rejected. Resident was notified.'; break;
        case 'deleted':  echo '&#x1F5D1; Message deleted.'; break;
        default:         echo 'An error occurred. Please try again.';
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="container my-4">
    <h2 class="fw-bold mb-4 text-center">Resident Messages &amp; Verification</h2>

    <!-- TABS -->
    <ul class="nav nav-tabs mb-4" id="adminMsgTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="verify-tab" data-bs-toggle="tab" data-bs-target="#verify-panel" type="button">
                <i class="bi bi-shield-check me-1"></i> ID Verifications
                <?php if ($pending_count > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $pending_count ?></span>
                <?php endif; ?>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages-panel" type="button">
                <i class="bi bi-chat-dots-fill me-1"></i> Resident Messages
                <?php if (count($messages) > 0): ?>
                    <span class="badge bg-primary ms-1"><?= count($messages) ?></span>
                <?php endif; ?>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- TAB 1: ID VERIFICATIONS -->
        <div class="tab-pane fade show active" id="verify-panel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 rounded-top-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-card-image me-2 text-warning"></i>Resident ID Submissions</h5>
                    <small class="text-muted">Review and approve or reject resident valid ID uploads to grant account verification.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th class="py-3">Resident Name</th>
                                    <th class="py-3">Contact</th>
                                    <th class="py-3">File Submitted</th>
                                    <th class="py-3">Note</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Action</th>
                                    <th class="py-3"> Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($id_uploads)): ?>
                                <?php foreach ($id_uploads as $up): ?>
                                <tr>
                                    <td class="align-middle fw-bold">
                                        <?= htmlspecialchars($up['fname'] . ' ' . $up['lname']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <small><?= htmlspecialchars($up['email'] ?: $up['phone_number'] ?: '—'); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <a href="uploads/valid_ids/<?= htmlspecialchars($up['file_name']); ?>" 
                                           target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View ID
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted"><?= htmlspecialchars($up['message_note'] ?: '—'); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <small><?= date('M d, Y', strtotime($up['upload_date'])); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($up['status'] === 'approved'): ?>
                                            <span class="badge rounded-pill status-approved px-3">&#x2705; Approved</span>
                                        <?php elseif ($up['status'] === 'rejected'): ?>
                                            <span class="badge rounded-pill status-rejected px-3">&#x274C; Rejected</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill status-pending px-3">&#x23F3; Pending</span>
                                        <?php endif; ?>

                        
                                    </td>
                                    <td class="align-middle">
                                        <?php if ($up['status'] === 'pending'): ?>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <!-- Approve button -->
                                            <form method="POST" onsubmit="return confirm('Approve this resident\'s account?');">
                                                <input type="hidden" name="id_resident" value="<?= $up['id_resident'] ?>">
                                                <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                                <button type="submit" name="approve_resident" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Approve
                                                </button>
                                            </form>
                                            <!-- Reject button opens modal -->
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal<?= $up['id_upload'] ?>">
                                                <i class="bi bi-x-circle-fill me-1"></i> Reject
                                            </button>
                                        </div>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <form method="POST" onsubmit="return confirm('Delete this ID submission record?');">
                                            <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                            <button type="submit" name="delete_upload" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="bi bi-trash-fill me-1"></i> Delete
                                            </button>
                                        </form>
                                </tr>

                                <!-- Reject Modal for this upload -->
                                <div class="modal fade" id="rejectModal<?= $up['id_upload'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form method="POST">
                                                <input type="hidden" name="id_resident" value="<?= $up['id_resident'] ?>">
                                                <input type="hidden" name="id_upload" value="<?= $up['id_upload'] ?>">
                                                <div class="modal-header bg-danger text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Reject ID Submission</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p>You are rejecting the ID submitted by <strong><?= htmlspecialchars($up['fname'] . ' ' . $up['lname']); ?></strong>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Reason for Rejection <span class="text-muted fw-normal">(optional)</span></label>
                                                        <textarea name="reject_reason" class="form-control" rows="3"
                                                                  placeholder="e.g., ID is blurry, expired, or not a government-issued ID..."></textarea>
                                                    </div>
                                                    <p class="text-muted small mb-0">The resident will be notified of this rejection via their messages.</p>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="reject_resident" class="btn btn-danger fw-bold rounded-pill px-4">
                                                        <i class="bi bi-x-circle me-1"></i> Confirm Rejection
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="py-5 text-muted fst-italic">No ID submissions found.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: RESIDENT MESSAGES -->
        <div class="tab-pane fade" id="messages-panel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <table class="table table-hover text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3">Resident Name</th>
                                <th class="py-3">Message Preview</th>
                                <th class="py-3">Date Sent</th>
                                <th class="py-3">View</th>
                                <th class="py-3">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg): ?>
                                    <tr>
                                        <td class="align-middle fw-bold">
                                            <?= htmlspecialchars($msg['fname'] . ' ' . $msg['lname']); ?>
                                        </td>
                                        <td class="align-middle text-muted">
                                            <?= htmlspecialchars(substr($msg['message_text'], 0, 50)); ?>...
                                        </td>
                                        <td class="align-middle">
                                            <?= date('M d, Y | h:i A', strtotime($msg['date_sent'])); ?>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-info btn-sm rounded-pill px-3 fw-bold"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewMsg<?= $msg['id_admin_msg']; ?>">
                                                <i class="bi bi-eye-fill me-1"></i> View
                                            </button>
                                        </td>
                                        <td class="align-middle">
                                            <form action="delete_message.php" method="POST" onsubmit="return confirm('Delete this message?');">
                                                <input type="hidden" name="id_admin_msg" value="<?= $msg['id_admin_msg']; ?>">
                                                <button type="submit" name="delete_msg" class="btn btn-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash-fill me-1"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- View Message Modal -->
                                    <div class="modal fade" id="viewMsg<?= $msg['id_admin_msg']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header bg-info text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold">Message from <?= htmlspecialchars($msg['fname']); ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <label class="text-muted small fw-bold">FULL NAME</label>
                                                    <p class="h6 mb-3"><?= htmlspecialchars($msg['fname'] . ' ' . $msg['lname']); ?></p>
                                                    <label class="text-muted small fw-bold">DATE RECEIVED</label>
                                                    <p class="h6 mb-3"><?= date('F j, Y, g:i a', strtotime($msg['date_sent'])); ?></p>
                                                    <hr>
                                                    <label class="text-muted small fw-bold">MESSAGE CONTENT</label>
                                                    <div class="bg-light p-3 rounded-3 mt-1">
                                                        <?= nl2br(htmlspecialchars($msg['message_text'])); ?>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="py-5 text-muted fst-italic">No messages found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>
