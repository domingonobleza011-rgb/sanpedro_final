<?php
    error_reporting(E_ALL ^ E_WARNING);
    ini_set('display_errors', 0);
    define('BMIS_ROLE_REQUIRED', 'staff');
    require_once('secure_header.php');
    require_once('classes/resident.class.php');
    $userdetails = $bmis->get_userdata();
    $bmis->validate_staff_or_admin();
    $admin_name = isset($userdetails['fname']) ? $userdetails['fname'] . ' ' . $userdetails['lname'] : 'Admin';

    // ---- Handle: Approve pending registration ----
    if (isset($_POST['approve_pending'])) {
        $id_pending = (int)$_POST['id_pending'];
        if ($residentbmis->approve_pending_resident($id_pending, $admin_name)) {
            header("Location: admn_resident_pending.php?toast=approved");
        } else {
            header("Location: admn_resident_pending.php?toast=error");
        }
        exit();
    }

    // ---- Handle: Reject pending registration ----
    if (isset($_POST['reject_pending'])) {
        $id_pending = (int)$_POST['id_pending'];
        $reason     = $_POST['reject_reason'] ?? '';
        if ($residentbmis->reject_pending_resident($id_pending, $admin_name, $reason)) {
            header("Location: admn_resident_pending.php?toast=rejected");
        } else {
            header("Location: admn_resident_pending.php?toast=error");
        }
        exit();
    }

    // ---- Handle: Delete a rejected/old pending record ----
    if (isset($_POST['delete_pending'])) {
        $id_pending = (int)$_POST['id_pending'];
        $residentbmis->delete_pending_resident($id_pending);
        header("Location: admn_resident_pending.php?toast=deleted");
        exit();
    }

    $pending_list  = $residentbmis->view_pending_residents();
    $pending_count = count($pending_list);

    // Rejected records kept for reference
    $connection = $bmis->openConn();
    $stmt = $connection->prepare("SELECT * FROM tbl_resident_pending WHERE application_status = 'rejected' ORDER BY date_submitted DESC");
    $stmt->execute();
    $rejected_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

:root {
    --navy: #0f2d5a;
    --gold: #c9943a;
    --teal: #0d9488;
    --danger: #dc2626;
    --warning: #d97706;
    --success: #059669;
    --cream: #f7f8fc;
    --border: #e8ecf0;
    --radius: 14px;
}
body { font-family: 'DM Sans', -apple-system, sans-serif !important; background: var(--cream) !important; }
.page-title { font-weight: 700; color: var(--navy); }
.card-clean { border: none; border-radius: var(--radius); box-shadow: 0 6px 24px rgba(15,45,90,0.08); }
.status-pending  { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
.status-rejected { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
.nav-tabs .nav-link.active { color: var(--navy); font-weight: 700; border-bottom: 3px solid var(--gold); }
.nav-tabs .nav-link { color: #64748b; }
</style>
<?php 
    include('dashboard_sidebar_start.php');
?>
<div class="container-fluid">
    <h1 class="mb-4 text-center page-title" style="font-weight:700;">Pending Resident Registrations</h1>

    <?php if (isset($_GET['toast'])): ?>
        <div class="alert alert-<?= $_GET['toast']==='error' ? 'danger' : 'success' ?> text-center" role="alert">
            <?php
                switch ($_GET['toast']) {
                    case 'approved': echo 'Registration approved. The resident can now log in.'; break;
                    case 'rejected': echo 'Registration rejected.'; break;
                    case 'deleted':  echo 'Record removed.'; break;
                    default: echo 'Something went wrong. Please try again.';
                }
            ?>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending-panel" type="button">
                <i class="bi bi-hourglass-split me-1"></i> Awaiting Approval
                <span class="badge bg-warning text-dark ms-1"><?= $pending_count ?></span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#rejected-panel" type="button">
                <i class="bi bi-x-circle me-1"></i> Rejected
                <span class="badge bg-secondary ms-1"><?= count($rejected_list) ?></span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- PENDING -->
        <div class="tab-pane fade show active" id="pending-panel">
            <div class="card card-clean">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Valid ID</th>
                                    <th>Date Submitted</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($pending_list)): ?>
                                <?php foreach ($pending_list as $p):
                                    $fullname = htmlspecialchars($p['fname'] . ' ' . $p['mi'] . ' ' . $p['lname']);
                                    $address  = htmlspecialchars(trim($p['houseno'] . ' ' . $p['street'] . ', ' . $p['brgy'] . ', ' . $p['municipal']));
                                ?>
                                <tr>
                                    <td class="fw-bold text-start"><?= $fullname ?></td>
                                    <td>
                                        <small><?= htmlspecialchars($p['email'] ?: $p['phone_number'] ?: '—') ?></small><br>
                                        <small class="text-muted"><?= htmlspecialchars($p['contact'] ?: '') ?></small>
                                    </td>
                                    <td class="text-start"><small><?= $address ?></small></td>
                                    <td>
                                        <?php if (!empty($p['valid_id_file'])): ?>
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                            onclick="openValidIdModal('uploads/valid_ids/<?= htmlspecialchars($p['valid_id_file']) ?>', '<?= $fullname ?>')">
                                            <i class="bi bi-eye me-1"></i> View ID
                                        </button>
                                        <?php else: ?>
                                            <span class="text-muted small">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><small><?= date('M d, Y g:i A', strtotime($p['date_submitted'])) ?></small></td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <form method="post" onsubmit="return confirm('Approve this registration? The applicant will be added to Barangay Residents.');">
                                                <input type="hidden" name="id_pending" value="<?= (int)$p['id_pending'] ?>">
                                                <button type="submit" name="approve_pending" class="btn btn-success btn-sm rounded-pill px-3 fw-bold">
                                                    <i class="bi bi-check-circle-fill me-1"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                                onclick="openRejectModal(<?= (int)$p['id_pending'] ?>, '<?= $fullname ?>')">
                                                <i class="bi bi-x-circle-fill me-1"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="py-5 text-muted fst-italic">No pending registrations.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- REJECTED -->
        <div class="tab-pane fade" id="rejected-panel">
            <div class="card card-clean">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Applicant Name</th>
                                    <th>Contact</th>
                                    <th>Reason</th>
                                    <th>Reviewed By</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($rejected_list)): ?>
                                <?php foreach ($rejected_list as $p): ?>
                                <tr>
                                    <td class="fw-bold text-start"><?= htmlspecialchars($p['fname'] . ' ' . $p['lname']) ?></td>
                                    <td><small><?= htmlspecialchars($p['email'] ?: $p['phone_number'] ?: '—') ?></small></td>
                                    <td class="text-start"><small><?= htmlspecialchars($p['reject_reason'] ?: '—') ?></small></td>
                                    <td><small><?= htmlspecialchars($p['reviewed_by'] ?: '—') ?></small></td>
                                    <td>
                                        <form method="post" onsubmit="return confirm('Permanently delete this record?');">
                                            <input type="hidden" name="id_pending" value="<?= (int)$p['id_pending'] ?>">
                                            <button type="submit" name="delete_pending" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                <i class="bi bi-trash-fill me-1"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="py-5 text-muted fst-italic">No rejected registrations.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Reject Registration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Reject the registration for <strong id="rejectApplicantName"></strong>?</p>
                    <input type="hidden" name="id_pending" id="rejectIdPending">
                    <label class="form-label">Reason (optional):</label>
                    <textarea name="reject_reason" class="form-control" rows="3" placeholder="e.g. ID photo unreadable, mismatched details..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="reject_pending" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Valid ID Viewer Modal -->
<div class="modal fade" id="validIdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Valid ID — <span id="validIdApplicantName"></span></h5>
                <button type="button" class="btn-close" onclick="closeValidIdModal()"></button>
            </div>
            <div class="modal-body text-center">
                <img id="validIdImage" src="" alt="Valid ID" class="img-fluid rounded d-none" style="max-height: 75vh;">
                <iframe id="validIdFrame" src="" class="w-100 d-none" style="height: 75vh; border: 0;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function openRejectModal(id, name) {
    document.getElementById('rejectIdPending').value = id;
    document.getElementById('rejectApplicantName').textContent = name;
    var modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    modal.show();
}

function openValidIdModal(fileUrl, name) {
    var img = document.getElementById('validIdImage');
    var frame = document.getElementById('validIdFrame');
    var isPdf = fileUrl.toLowerCase().endsWith('.pdf');

    if (isPdf) {
        img.classList.add('d-none');
        img.src = '';
        frame.src = fileUrl;
        frame.classList.remove('d-none');
    } else {
        frame.classList.add('d-none');
        frame.src = '';
        img.src = fileUrl;
        img.classList.remove('d-none');
    }

    document.getElementById('validIdApplicantName').textContent = name;

    var modalEl = document.getElementById('validIdModal');
    modalEl.style.display = 'block';
    modalEl.classList.add('show');
    modalEl.removeAttribute('aria-hidden');
    modalEl.setAttribute('aria-modal', 'true');
    document.body.classList.add('modal-open');

    if (!document.getElementById('validIdBackdrop')) {
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'validIdBackdrop';
        backdrop.onclick = closeValidIdModal;
        document.body.appendChild(backdrop);
    }
}

function closeValidIdModal() {
    var modalEl = document.getElementById('validIdModal');
    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.removeAttribute('aria-modal');
    document.body.classList.remove('modal-open');

    var backdrop = document.getElementById('validIdBackdrop');
    if (backdrop) backdrop.remove();

    // Stop any playing/loading media when closed
    document.getElementById('validIdImage').src = '';
    document.getElementById('validIdFrame').src = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeValidIdModal();
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
<?php include('dashboard_sidebar_end.php'); ?>