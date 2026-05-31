<?php 
    define('BMIS_ROLE_REQUIRED', 'staff');
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
        header("Location: admn_messages.php?toast=approved");
    } else {
        header("Location: admn_messages.php?toast=error");
    }
    exit();
}

// ---- Handle: Delete upload record ----
if (isset($_POST['delete_upload'])) {
    $id_upload = (int)$_POST['id_upload'];
    $systemObject->delete_upload_record($id_upload);
    header("Location: admn_messages.php?toast=upload_deleted");
    exit();
}

// ---- Handle: Reject resident ID ----
if (isset($_POST['reject_resident'])) {
    $id_resident = (int)$_POST['id_resident'];
    $id_upload   = (int)$_POST['id_upload'];
    $reason      = $_POST['reject_reason'] ?? '';
    if ($systemObject->rejectResidentVerification($id_resident, $id_upload, $admin_name, $reason)) {
        header("Location: admn_messages.php?toast=rejected");
    } else {
        header("Location: admn_messages.php?toast=error");
    }
    exit();
}

// ---- Handle: Delete message ----
if (isset($_POST['delete_msg'])) {
    $id = $_POST['id_admin_msg'];
    $systemObject->deleteMessage($id);
    header("Location: admn_messages.php?toast=msg_deleted");
    exit();
}

// ---- Handle: Bulk delete messages ----
if (isset($_POST['bulk_delete_msg']) && !empty($_POST['msg_ids'])) {
    $ids = array_map('intval', $_POST['msg_ids']);
    foreach ($ids as $id) {
        $systemObject->deleteMessage($id);
    }
    header("Location: admn_messages.php?toast=msg_deleted");
    exit();
}

// ---- Fetch data ----
$messages   = $systemObject->viewMessages();
$id_uploads = [];
$id_uploads = $systemObject->getPendingIDUploads();

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

        /* ── Confirmation modals ── */
        .bmis-modal-backdrop {
            display: none; position: fixed; inset: 0; z-index: 9999;
            background: rgba(0,0,0,0.45); align-items: center; justify-content: center;
        }
        .bmis-modal-backdrop.open { display: flex; }
        .bmis-modal-card {
            background: #fff; border-radius: 14px; padding: 28px 32px;
            width: 100%; max-width: 430px; margin: 0 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        }
        .bmis-modal-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .bmis-modal-title  { font-size: 15px; font-weight: 600; margin: 0; color: #0f2d5a; }
        .bmis-modal-sub    { font-size: 13px; color: #6b7280; margin: 0; }
        .bmis-modal-info   { border-radius: 8px; padding: 12px 16px; margin-bottom: 18px; display: flex; align-items: center; gap: 12px; }
        .bmis-modal-info p { margin: 0; }
        .bmis-btn-cancel {
            padding: 8px 18px; font-size: 13px; border-radius: 8px; cursor: pointer;
            border: 1px solid #d1d5db; background: #fff; color: #6b7280;
        }
        .bmis-btn-confirm {
            padding: 8px 20px; font-size: 13px; font-weight: 600; border-radius: 8px;
            cursor: pointer; border: none; color: #fff;
        }

        /* ── Bulk toolbar ── */
        #bulkToolbar {
            background: #eff6ff;
            border-bottom: 1px solid #bfdbfe;
            border-radius: 12px 12px 0 0;
            transition: all 0.2s ease;
        }

        /* ── Row selected highlight ── */
        tr.row-selected {
            background-color: #eff6ff !important;
        }

        /* ── Custom checkboxes ── */
        .msg-checkbox,
        #selectAllMsgs {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #6c757d;
            border-radius: 5px;
            background-color: #fff;
            cursor: pointer;
            position: relative;
            transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
            vertical-align: middle;
            flex-shrink: 0;
        }

        /* Header checkbox sits inside dark thead — give it a lighter border */
        thead .msg-checkbox,
        thead #selectAllMsgs {
            border-color: #adb5bd;
            background-color: transparent;
        }

        /* Hover state */
        .msg-checkbox:hover,
        #selectAllMsgs:hover {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        /* Checked state */
        .msg-checkbox:checked,
        #selectAllMsgs:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        /* Checkmark via SVG background */
        .msg-checkbox:checked::after,
        #selectAllMsgs:checked::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round' d='M3 8l3.5 3.5L13 4.5'/%3E%3C/svg%3E") center / 12px no-repeat;
        }

        /* Indeterminate (dash) state for select-all */
        #selectAllMsgs:indeterminate {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        #selectAllMsgs:indeterminate::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23fff' stroke-width='2.5' stroke-linecap='round' d='M3.5 8h9'/%3E%3C/svg%3E") center / 12px no-repeat;
        }

        /* Focus ring */
        .msg-checkbox:focus-visible,
        #selectAllMsgs:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.35);
        }

        /* Checked row — checkbox accent stays blue */
        tr.row-selected .msg-checkbox {
            border-color: #0d6efd;
        }

        /* ── Success toast ── */
        #bmisToast {
            display: none; position: fixed; bottom: 28px; right: 28px;
            z-index: 10000; min-width: 300px; max-width: 400px;
        }
        #bmisToastInner {
            border-radius: 14px; padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            display: flex; align-items: flex-start; gap: 14px;
            animation: toastIn 0.3s ease;
        }
        #bmisToastIcon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

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
                                    <th class="py-3">Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($id_uploads)): ?>
                                <?php foreach ($id_uploads as $up):
                                    $uid      = $up['id_upload'];
                                    $fullname = htmlspecialchars($up['fname'] . ' ' . $up['lname']);
                                ?>
                                <tr>
                                    <td class="align-middle fw-bold"><?= $fullname ?></td>
                                    <td class="align-middle">
                                        <small><?= htmlspecialchars($up['email'] ?: $up['phone_number'] ?: '—') ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <a href="uploads/valid_ids/<?= htmlspecialchars($up['file_name']) ?>"
                                           target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View ID
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted"><?= htmlspecialchars($up['message_note'] ?: '—') ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <small><?= date('M d, Y', strtotime($up['upload_date'])) ?></small>
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
                                            <!-- Approve -->
                                            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold"
                                                onclick="openApproveModal(<?= $uid ?>, <?= (int)$up['id_resident'] ?>, '<?= $fullname ?>')">
                                                <i class="bi bi-check-circle-fill me-1"></i> Approve
                                            </button>
                                            <!-- Reject -->
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                                onclick="openRejectModal(<?= $uid ?>, <?= (int)$up['id_resident'] ?>, '<?= $fullname ?>')">
                                                <i class="bi bi-x-circle-fill me-1"></i> Reject
                                            </button>
                                        </div>
                                        <?php else: ?>
                                            <span class="text-muted small">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                            onclick="openDeleteUploadModal(<?= $uid ?>, '<?= $fullname ?>')">
                                            <i class="bi bi-trash-fill me-1"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="py-5 text-muted fst-italic">No ID submissions found.</td></tr>
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

                <!-- Bulk action toolbar (hidden until at least 1 checkbox is checked) -->
                <div id="bulkToolbar" class="d-none px-4 py-3 d-flex align-items-center gap-3 rounded-top-4">
                    <i class="bi bi-check2-square text-primary fs-5"></i>
                    <span id="selectedCount" class="fw-bold text-primary small"></span>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold"
                            onclick="openBulkDeleteModal()">
                        <i class="bi bi-trash-fill me-1"></i> Delete Selected
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                            onclick="clearSelection()">
                        <i class="bi bi-x-lg me-1"></i> Cancel
                    </button>
                </div>

                <div class="card-body p-0">
                    <table class="table table-hover text-center mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3" style="width: 48px;">
                                    <input type="checkbox" id="selectAllMsgs" class="form-check-input"
                                           onchange="toggleSelectAll(this)" title="Select all messages">
                                </th>
                                <th class="py-3">Resident Name</th>
                                <th class="py-3">Message Preview</th>
                                <th class="py-3">Date Sent</th>
                                <th class="py-3">View</th>
                                <th class="py-3">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)): ?>
                                <?php foreach ($messages as $msg):
                                    $mid      = $msg['id_admin_msg'];
                                    $mfname   = htmlspecialchars($msg['fname']);
                                    $mfull    = htmlspecialchars($msg['fname'] . ' ' . $msg['lname']);
                                ?>
                                    <tr id="msgRow<?= $mid ?>">
                                        <td class="align-middle">
                                            <input type="checkbox" class="form-check-input msg-checkbox"
                                                   value="<?= $mid ?>" onchange="updateBulkToolbar()">
                                        </td>
                                        <td class="align-middle fw-bold"><?= $mfull ?></td>
                                        <td class="align-middle text-muted">
                                            <?= htmlspecialchars(substr($msg['message_text'], 0, 50)) ?>...
                                        </td>
                                        <td class="align-middle">
                                            <?= date('M d, Y | h:i A', strtotime($msg['date_sent'])) ?>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-info btn-sm rounded-pill px-3 fw-bold"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewMsg<?= $mid ?>">
                                                <i class="bi bi-eye-fill me-1"></i> View
                                            </button>
                                        </td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3"
                                                onclick="openDeleteMsgModal(<?= $mid ?>, '<?= $mfname ?>')">
                                                <i class="bi bi-trash-fill me-1"></i> Delete
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- View Message Modal (Bootstrap) -->
                                    <div class="modal fade" id="viewMsg<?= $mid ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header bg-info text-white rounded-top-4">
                                                    <h5 class="modal-title fw-bold">Message from <?= $mfname ?></h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">
                                                    <label class="text-muted small fw-bold">FULL NAME</label>
                                                    <p class="h6 mb-3"><?= $mfull ?></p>
                                                    <label class="text-muted small fw-bold">DATE RECEIVED</label>
                                                    <p class="h6 mb-3"><?= date('F j, Y, g:i a', strtotime($msg['date_sent'])) ?></p>
                                                    <hr>
                                                    <label class="text-muted small fw-bold">MESSAGE CONTENT</label>
                                                    <div class="bg-light p-3 rounded-3 mt-1">
                                                        <?= nl2br(htmlspecialchars($msg['message_text'])) ?>
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
                                <tr><td colspan="6" class="py-5 text-muted fst-italic">No messages found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div><!-- end container -->


<!-- ════════════════════════════════════════════════════════
     HIDDEN FORMS  (submitted programmatically by JS)
═══════════════════════════════════════════════════════════ -->

<!-- Approve form -->
<form id="approveForm" method="POST" action="admn_messages.php" style="display:none;">
    <input type="hidden" name="id_resident" id="approveResidentId">
    <input type="hidden" name="id_upload"   id="approveUploadId">
    <input type="hidden" name="approve_resident" value="1">
</form>

<!-- Reject form -->
<form id="rejectForm" method="POST" action="admn_messages.php" style="display:none;">
    <input type="hidden" name="id_resident"  id="rejectResidentId">
    <input type="hidden" name="id_upload"    id="rejectUploadId">
    <input type="hidden" name="reject_reason" id="rejectReason">
    <input type="hidden" name="reject_resident" value="1">
</form>

<!-- Delete upload form -->
<form id="deleteUploadForm" method="POST" action="admn_messages.php" style="display:none;">
    <input type="hidden" name="id_upload"     id="deleteUploadId">
    <input type="hidden" name="delete_upload" value="1">
</form>

<!-- Delete message form -->
<form id="deleteMsgForm" method="POST" action="admn_messages.php" style="display:none;">
    <input type="hidden" name="id_admin_msg" id="deleteMsgId">
    <input type="hidden" name="delete_msg"   value="1">
</form>

<!-- Bulk delete messages form -->
<form id="bulkDeleteForm" method="POST" action="admn_messages.php" style="display:none;">
    <input type="hidden" name="bulk_delete_msg" value="1">
    <div id="bulkDeleteIds"></div>
</form>


<!-- ════════════════════════════════════════════════════════
     APPROVE MODAL
═══════════════════════════════════════════════════════════ -->
<div id="approveModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#d1fae5;">
        <i class="bi bi-check-circle-fill" style="color:#059669; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Approve verification</p>
        <p class="bmis-modal-sub">This will grant the resident a verified account.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#f0fdf4; border:1.5px solid #bbf7d0;">
      <i class="bi bi-person-check-fill" style="color:#059669; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="approveModalName" style="font-size:14px; font-weight:700; color:#065f46;"></p>
        <p style="font-size:12px; color:#047857;">Resident ID submission will be marked as approved.</p>
      </div>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeAllModals()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#059669,#34d399);"
              onclick="document.getElementById('approveForm').submit();">
        <i class="bi bi-check-circle-fill me-1"></i> Yes, approve
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     REJECT MODAL
═══════════════════════════════════════════════════════════ -->
<div id="rejectModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#fee2e2;">
        <i class="bi bi-x-circle-fill" style="color:#dc2626; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Reject ID submission</p>
        <p class="bmis-modal-sub">The resident will be notified of the rejection.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#fef2f2; border:1.5px solid #fecaca;">
      <i class="bi bi-person-x-fill" style="color:#dc2626; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="rejectModalName" style="font-size:14px; font-weight:700; color:#991b1b;"></p>
        <p style="font-size:12px; color:#b91c1c;">Their ID submission will be marked as rejected.</p>
      </div>
    </div>
    <div class="mb-3">
      <label style="font-size:13px; font-weight:600; color:#374151; display:block; margin-bottom:6px;">
        Reason <span style="font-weight:400; color:#9ca3af;">(optional)</span>
      </label>
      <textarea id="rejectReasonInput" rows="3" style="width:100%; border:1.5px solid #d1d5db; border-radius:8px; padding:10px 12px; font-size:13px; font-family:inherit; resize:vertical;"
                placeholder="e.g., ID is blurry, expired, or not a government-issued ID…"></textarea>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeAllModals()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#dc2626,#ef4444);"
              onclick="submitReject()">
        <i class="bi bi-x-circle-fill me-1"></i> Confirm rejection
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     DELETE UPLOAD MODAL
═══════════════════════════════════════════════════════════ -->
<div id="deleteUploadModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#fee2e2;">
        <i class="bi bi-trash-fill" style="color:#dc2626; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Delete ID submission</p>
        <p class="bmis-modal-sub">This action cannot be undone.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#fef2f2; border:1.5px solid #fecaca;">
      <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="deleteUploadModalName" style="font-size:14px; font-weight:700; color:#991b1b;"></p>
        <p style="font-size:12px; color:#b91c1c;">The ID submission record will be permanently removed.</p>
      </div>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeAllModals()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#dc2626,#ef4444);"
              onclick="document.getElementById('deleteUploadForm').submit();">
        <i class="bi bi-trash-fill me-1"></i> Yes, delete
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     DELETE MESSAGE MODAL
═══════════════════════════════════════════════════════════ -->
<div id="deleteMsgModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#fee2e2;">
        <i class="bi bi-trash-fill" style="color:#dc2626; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Delete message</p>
        <p class="bmis-modal-sub">This action cannot be undone.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#fef2f2; border:1.5px solid #fecaca;">
      <i class="bi bi-chat-left-dots-fill" style="color:#dc2626; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="deleteMsgModalName" style="font-size:14px; font-weight:700; color:#991b1b;"></p>
        <p style="font-size:12px; color:#b91c1c;">The message will be permanently deleted.</p>
      </div>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeAllModals()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#dc2626,#ef4444);"
              onclick="document.getElementById('deleteMsgForm').submit();">
        <i class="bi bi-trash-fill me-1"></i> Yes, delete
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     BULK DELETE MESSAGES MODAL
═══════════════════════════════════════════════════════════ -->
<div id="bulkDeleteModal" class="bmis-modal-backdrop">
  <div class="bmis-modal-card">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
      <div class="bmis-modal-icon" style="background:#fee2e2;">
        <i class="bi bi-trash-fill" style="color:#dc2626; font-size:18px;"></i>
      </div>
      <div>
        <p class="bmis-modal-title">Delete selected messages</p>
        <p class="bmis-modal-sub">This action cannot be undone.</p>
      </div>
    </div>
    <hr style="margin:16px 0; border-color:#e5e7eb;">
    <div class="bmis-modal-info" style="background:#fef2f2; border:1.5px solid #fecaca;">
      <i class="bi bi-exclamation-triangle-fill" style="color:#dc2626; font-size:20px; flex-shrink:0;"></i>
      <div>
        <p id="bulkDeleteCount" style="font-size:14px; font-weight:700; color:#991b1b;"></p>
        <p style="font-size:12px; color:#b91c1c;">All selected messages will be permanently deleted.</p>
      </div>
    </div>
    <div style="display:flex; gap:8px; justify-content:flex-end;">
      <button class="bmis-btn-cancel" onclick="closeAllModals()">Cancel</button>
      <button class="bmis-btn-confirm" style="background:linear-gradient(135deg,#dc2626,#ef4444);"
              onclick="submitBulkDelete()">
        <i class="bi bi-trash-fill me-1"></i> Yes, delete all
      </button>
    </div>
  </div>
</div>


<!-- ════════════════════════════════════════════════════════
     SUCCESS TOAST
═══════════════════════════════════════════════════════════ -->
<div id="bmisToast">
  <div id="bmisToastInner">
    <div id="bmisToastIcon">
      <i id="bmisToastIconI"></i>
    </div>
    <div style="flex:1; min-width:0;">
      <p id="bmisToastTitle" style="font-size:13px; font-weight:700; margin:0 0 2px;"></p>
      <p id="bmisToastMsg"   style="font-size:12px; margin:0; color:#6b7280; line-height:1.4;"></p>
    </div>
    <button onclick="closeToast()" style="background:none; border:none; cursor:pointer; color:#9ca3af; font-size:16px; padding:0; flex-shrink:0; line-height:1;">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>
</div>


<script>
// ── Modal openers ────────────────────────────────────────────
function openApproveModal(uploadId, residentId, name) {
    document.getElementById('approveUploadId').value   = uploadId;
    document.getElementById('approveResidentId').value = residentId;
    document.getElementById('approveModalName').textContent = name;
    document.getElementById('approveModal').classList.add('open');
}

function openRejectModal(uploadId, residentId, name) {
    document.getElementById('rejectUploadId').value    = uploadId;
    document.getElementById('rejectResidentId').value  = residentId;
    document.getElementById('rejectModalName').textContent = name;
    document.getElementById('rejectReasonInput').value = '';
    document.getElementById('rejectModal').classList.add('open');
}

function openDeleteUploadModal(uploadId, name) {
    document.getElementById('deleteUploadId').value = uploadId;
    document.getElementById('deleteUploadModalName').textContent = name;
    document.getElementById('deleteUploadModal').classList.add('open');
}

function openDeleteMsgModal(msgId, name) {
    document.getElementById('deleteMsgId').value = msgId;
    document.getElementById('deleteMsgModalName').textContent = 'Message from ' + name;
    document.getElementById('deleteMsgModal').classList.add('open');
}

function closeAllModals() {
    document.querySelectorAll('.bmis-modal-backdrop').forEach(m => m.classList.remove('open'));
}

function submitReject() {
    document.getElementById('rejectReason').value = document.getElementById('rejectReasonInput').value;
    document.getElementById('rejectForm').submit();
}

// Close on backdrop click
document.querySelectorAll('.bmis-modal-backdrop').forEach(function(m) {
    m.addEventListener('click', function(e) { if (e.target === m) closeAllModals(); });
});


// ── Bulk select / delete ──────────────────────────────────────

function toggleSelectAll(cb) {
    document.querySelectorAll('.msg-checkbox').forEach(function(c) {
        c.checked = cb.checked;
        c.closest('tr').classList.toggle('row-selected', cb.checked);
    });
    updateBulkToolbar();
}

function updateBulkToolbar() {
    var checked    = document.querySelectorAll('.msg-checkbox:checked');
    var all        = document.querySelectorAll('.msg-checkbox');
    var toolbar    = document.getElementById('bulkToolbar');
    var selectAll  = document.getElementById('selectAllMsgs');
    var countLabel = document.getElementById('selectedCount');

    // Update each row's highlight
    document.querySelectorAll('.msg-checkbox').forEach(function(c) {
        c.closest('tr').classList.toggle('row-selected', c.checked);
    });

    // Show/hide toolbar
    if (checked.length > 0) {
        toolbar.classList.remove('d-none');
        toolbar.classList.add('d-flex');
    } else {
        toolbar.classList.add('d-none');
        toolbar.classList.remove('d-flex');
    }

    countLabel.textContent = checked.length + ' message' + (checked.length !== 1 ? 's' : '') + ' selected';

    // Header checkbox state
    selectAll.indeterminate = checked.length > 0 && checked.length < all.length;
    selectAll.checked       = all.length > 0 && checked.length === all.length;
}

function clearSelection() {
    document.querySelectorAll('.msg-checkbox').forEach(function(c) {
        c.checked = false;
        c.closest('tr').classList.remove('row-selected');
    });
    var selectAll = document.getElementById('selectAllMsgs');
    selectAll.checked       = false;
    selectAll.indeterminate = false;
    updateBulkToolbar();
}

function openBulkDeleteModal() {
    var count = document.querySelectorAll('.msg-checkbox:checked').length;
    document.getElementById('bulkDeleteCount').textContent =
        count + ' message' + (count !== 1 ? 's' : '') + ' will be permanently deleted.';
    document.getElementById('bulkDeleteModal').classList.add('open');
}

function submitBulkDelete() {
    var ids       = Array.from(document.querySelectorAll('.msg-checkbox:checked')).map(function(c) { return c.value; });
    var container = document.getElementById('bulkDeleteIds');
    container.innerHTML = ids.map(function(id) {
        return '<input type="hidden" name="msg_ids[]" value="' + id + '">';
    }).join('');
    document.getElementById('bulkDeleteForm').submit();
}


// ── Toast ────────────────────────────────────────────────────
var toastConfigs = {
    approved:      { type: 'success', title: 'Approved',  msg: 'Resident account has been verified successfully.' },
    rejected:      { type: 'warning', title: 'Rejected',  msg: 'ID submission rejected. Resident has been notified.' },
    upload_deleted:{ type: 'delete',  title: 'Deleted',   msg: 'ID submission record permanently deleted.' },
    msg_deleted:   { type: 'delete',  title: 'Deleted',   msg: 'Message(s) permanently deleted.' },
    error:         { type: 'error',   title: 'Error',     msg: 'Something went wrong. Please try again.' },
};

function showToast(cfg) {
    var isSuccess = cfg.type === 'success';
    var isWarning = cfg.type === 'warning';
    var isError   = cfg.type === 'error';

    var bg        = isSuccess ? '#f0fdf4' : (isWarning ? '#fffbeb' : (isError ? '#fef2f2' : '#fef2f2'));
    var border    = isSuccess ? '#bbf7d0' : (isWarning ? '#fde68a' : (isError ? '#fecaca' : '#fecaca'));
    var iconBg    = isSuccess ? '#d1fae5' : (isWarning ? '#fef3c7' : '#fee2e2');
    var iconColor = isSuccess ? '#059669' : (isWarning ? '#d97706' : '#dc2626');
    var iconCls   = isSuccess ? 'bi-check-circle-fill' : (isWarning ? 'bi-exclamation-circle-fill' : 'bi-trash-fill');

    var inner = document.getElementById('bmisToastInner');
    inner.style.background = bg;
    inner.style.border     = '1.5px solid ' + border;

    var icon = document.getElementById('bmisToastIcon');
    icon.style.background  = iconBg;

    var iconI = document.getElementById('bmisToastIconI');
    iconI.className        = 'bi ' + iconCls;
    iconI.style.color      = iconColor;
    iconI.style.fontSize   = '16px';

    var titleEl = document.getElementById('bmisToastTitle');
    titleEl.textContent    = cfg.title;
    titleEl.style.color    = iconColor;

    document.getElementById('bmisToastMsg').textContent = cfg.msg;
    document.getElementById('bmisToast').style.display  = 'block';

    setTimeout(closeToast, 4500);
}

function closeToast() {
    var t = document.getElementById('bmisToast');
    t.style.opacity    = '0';
    t.style.transition = 'opacity 0.3s';
    setTimeout(function() { t.style.display = 'none'; t.style.opacity = ''; t.style.transition = ''; }, 300);
}

// Read ?toast= param and fire toast on load
(function() {
    var params = new URLSearchParams(window.location.search);
    var key    = params.get('toast');
    if (key && toastConfigs[key]) {
        showToast(toastConfigs[key]);
        history.replaceState(null, '', window.location.pathname);
    }
})();
</script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>