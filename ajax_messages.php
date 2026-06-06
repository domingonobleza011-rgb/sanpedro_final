<?php
/**
 * ajax_messages.php
 * Returns messages table rows + ID upload cards as JSON.
 * Called by admn_messages.php every 8 seconds via fetch().
 */
error_reporting(0);
define('BMIS_ROLE_REQUIRED', 'admin_dashboard');
require('secure_header.php');
require_once 'classes/main.class.php';

header('Content-Type: application/json');

$systemObject = new BMISClass();
$userdetails  = $systemObject->get_userdata();
$admin_name   = isset($userdetails['fname']) ? $userdetails['fname'] . ' ' . $userdetails['lname'] : 'Admin';

$messages   = $systemObject->viewMessages();
$id_uploads = $systemObject->getPendingIDUploads();

$pending_count = 0;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $pending_count++;
}

// ── Build messages table rows HTML ───────────────────────────
ob_start();
if (!empty($messages)):
    foreach ($messages as $msg):
        $mid   = $msg['id_admin_msg'];
        $mfname = htmlspecialchars($msg['fname']);
        $mfull  = htmlspecialchars($msg['fname'] . ' ' . $msg['lname']);
?>
<tr id="msgRow<?= $mid ?>">
    <td class="align-middle">
        <input type="checkbox" class="form-check-input msg-checkbox" value="<?= $mid ?>" onchange="updateBulkToolbar()">
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
                data-bs-toggle="modal" data-bs-target="#viewMsg<?= $mid ?>">
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
<!-- View Modal -->
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php
    endforeach;
else:
    echo '<tr><td colspan="6" class="text-center text-muted py-4">No messages found.</td></tr>';
endif;
$messages_html = ob_get_clean();

// ── Build ID uploads HTML ─────────────────────────────────────
ob_start();
foreach ($id_uploads as $up):
    $uid     = $up['id_upload'];
    $ufname  = htmlspecialchars($up['fname'] . ' ' . $up['lname']);
    $uemail  = htmlspecialchars($up['email'] ?? '');
    $uphone  = htmlspecialchars($up['phone_number'] ?? '');
    $utype   = htmlspecialchars($up['file_type'] ?? 'ID');
    $udate   = date('M d, Y g:i A', strtotime($up['upload_date']));
    $unote   = htmlspecialchars($up['message_note'] ?? '');
    $uresid  = $up['id_resident'];
?>
<div class="card mb-3 shadow-sm border-0 rounded-4" id="idUpload<?= $uid ?>">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-1"><?= $ufname ?></h6>
                <?php if ($uemail): ?><div class="text-muted small"><i class="bi bi-envelope me-1"></i><?= $uemail ?></div><?php endif; ?>
                <?php if ($uphone): ?><div class="text-muted small"><i class="bi bi-telephone me-1"></i><?= $uphone ?></div><?php endif; ?>
                <div class="text-muted small mt-1"><i class="bi bi-clock me-1"></i><?= $udate ?></div>
                <?php if ($unote): ?><div class="mt-2 text-secondary small"><i class="bi bi-chat-left-text me-1"></i><?= $unote ?></div><?php endif; ?>
            </div>
            <span class="badge bg-warning text-dark"><?= $utype ?></span>
        </div>
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <form method="POST" class="d-inline">
                <input type="hidden" name="id_resident" value="<?= $uresid ?>">
                <input type="hidden" name="id_upload" value="<?= $uid ?>">
                <button type="submit" name="approve_resident" class="btn btn-success btn-sm rounded-pill px-3">
                    <i class="bi bi-check-circle me-1"></i> Approve
                </button>
            </form>
            <button class="btn btn-danger btn-sm rounded-pill px-3"
                    data-bs-toggle="modal" data-bs-target="#rejectModal<?= $uid ?>">
                <i class="bi bi-x-circle me-1"></i> Reject
            </button>
            <form method="POST" class="d-inline">
                <input type="hidden" name="id_upload" value="<?= $uid ?>">
                <button type="submit" name="delete_upload" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>
</div>
<!-- Reject Modal -->
<div class="modal fade" id="rejectModal<?= $uid ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject ID Submission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <p>Rejecting <strong><?= $ufname ?></strong>'s ID submission.</p>
                    <label class="form-label fw-semibold">Reason for rejection:</label>
                    <textarea name="reject_reason" class="form-control" rows="3" placeholder="e.g. Blurry image, incorrect document..."></textarea>
                    <input type="hidden" name="id_resident" value="<?= $uresid ?>">
                    <input type="hidden" name="id_upload" value="<?= $uid ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="reject_resident" class="btn btn-danger px-4">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach;
if (empty($id_uploads)) {
    echo '<p class="text-muted text-center py-3">No pending ID verifications.</p>';
}
$uploads_html = ob_get_clean();

echo json_encode([
    'messages_html'  => $messages_html,
    'uploads_html'   => $uploads_html,
    'msg_count'      => count($messages),
    'pending_count'  => $pending_count,
]);
