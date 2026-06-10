<?php
/**
 * admn_password_reset_requests.php
 * Admin page: view, approve, or reject resident password reset requests.
 */
error_reporting(E_ALL ^ E_WARNING);
require_once __DIR__ . '/classes/security.php';
bmis_session_start();
$userdetails = bmis_require_admin();
date_default_timezone_set('Asia/Manila');
include('autoloader.php');
require('classes/conn.php');

$error   = '';
$success = '';

// ── Approve: generate temp password, update resident, mark resolved ──────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_reset'])) {
    bmis_verify_csrf();
    $req_id      = (int) $_POST['req_id'];
    $id_resident = (int) $_POST['id_resident'];

    // Generate readable temp password: e.g. BrgyReset@4821
    $temp = 'BrgyReset@' . rand(1000, 9999);
    $hash = password_hash($temp, PASSWORD_DEFAULT);

    $conn->prepare("UPDATE tbl_resident SET password = ?, must_change_password = 1 WHERE id_resident = ?")
         ->execute([$hash, $id_resident]);

    $conn->prepare(
        "UPDATE tbl_password_reset_requests
         SET status='approved', temp_password=?, resolved_at=NOW(), resolved_by=?
         WHERE id=?"
    )->execute([$temp, $userdetails['fname'].' '.$userdetails['lname'], $req_id]);

    $success = "Approved. Temporary password set to: <strong>" . htmlspecialchars($temp) . "</strong> — give this to the resident in person.";
}

// ── Reject ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_reset'])) {
    bmis_verify_csrf();
    $req_id = (int) $_POST['req_id'];
    $conn->prepare(
        "UPDATE tbl_password_reset_requests SET status='rejected', resolved_at=NOW(), resolved_by=? WHERE id=?"
    )->execute([$userdetails['fname'].' '.$userdetails['lname'], $req_id]);
    $success = 'Request rejected.';
}

// ── Delete ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reset'])) {
    bmis_verify_csrf();
    $req_id = (int) $_POST['req_id'];
    $conn->prepare("DELETE FROM tbl_password_reset_requests WHERE id = ? AND status != 'pending'")
         ->execute([$req_id]);
    $success = 'Request deleted.';
}

// ── Fetch all requests ───────────────────────────────────────────────────────
$requests = $conn->query(
    "SELECT r.*, res.email, res.phone_number
     FROM tbl_password_reset_requests r
     JOIN tbl_resident res ON res.id_resident = r.id_resident
     ORDER BY FIELD(r.status,'pending','approved','rejected'), r.requested_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset Requests — Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
</head>
<body class="bg-light">
<?php include('dashboard_sidebar_start.php'); ?>
<div class="content-wrapper p-4">
    <div class="d-flex align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-key me-2 text-primary"></i>Password Reset Requests</h4>
        <span class="badge bg-danger ms-2">
            <?= count(array_filter($requests, fn($r) => $r['status']==='pending')) ?> Pending
        </span>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Resident</th>
                    <th>Contact</th>
                    <th>Requested</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($requests)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No reset requests yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['full_name']) ?></strong><br>
                        <small class="text-muted">#<?= $r['id_resident'] ?></small>
                    </td>
                    <td>
                        <?= htmlspecialchars($r['email'] ?? '—') ?><br>
                        <small><?= htmlspecialchars($r['phone_number'] ?? '—') ?></small>
                    </td>
                    <td><?= date('M d, Y g:i A', strtotime($r['requested_at'])) ?></td>
                    <td>
                        <?php
                        $badges = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'];
                        $badge  = $badges[$r['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?= $badge ?>"><?= ucfirst($r['status']) ?></span>
                        <?php if ($r['status']==='approved' && $r['temp_password']): ?>
                            <br><small class="text-muted">Temp: <?= htmlspecialchars($r['temp_password']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                    <?php if ($r['status'] === 'pending'): ?>
                        <!-- Approve -->
                        <form method="post" class="d-inline">
                            <?= bmis_csrf_field() ?>
                            <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                            <input type="hidden" name="id_resident" value="<?= $r['id_resident'] ?>">
                            <button type="submit" name="approve_reset"
                                    class="btn btn-success btn-sm"
                                    onclick="return confirm('Approve and generate temp password for <?= htmlspecialchars($r['full_name']) ?>?')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <!-- Reject -->
                        <form method="post" class="d-inline ms-1">
                            <?= bmis_csrf_field() ?>
                            <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                            <button type="submit" name="reject_reset"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Reject this request?')">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </form>
                    <?php else: ?>
                        <small class="text-muted">Resolved by <?= htmlspecialchars($r['resolved_by'] ?? '—') ?></small><br>
                        <small class="text-muted"><?= $r['resolved_at'] ? date('M d, Y', strtotime($r['resolved_at'])) : '' ?></small>
                        <form method="post" class="d-inline ms-1 mt-1">
                            <?= bmis_csrf_field() ?>
                            <input type="hidden" name="req_id" value="<?= $r['id'] ?>">
                            <button type="submit" name="delete_reset"
                                    class="btn btn-outline-danger btn-sm mt-1"
                                    onclick="return confirm('Delete this request record?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        </div>
    </div>
</div>
<?php include('dashboard_sidebar_end.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>