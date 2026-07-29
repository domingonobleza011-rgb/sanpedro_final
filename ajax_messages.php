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

$conversations = $systemObject->getConversations();
$id_uploads    = $systemObject->getPendingIDUploads();

$pending_count = 0;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $pending_count++;
}

// ── Build messages table rows HTML (one row per resident conversation) ────
// Kept in sync with admn_messages.php's table + modal markup — if you change
// one, change the other, or the 8s/60s poll will revert this view.
ob_start();
if (!empty($conversations)):
    foreach ($conversations as $conv):
        $rid     = $conv['id_resident'];
        $mfname  = htmlspecialchars($conv['fname']);
        $mfull   = htmlspecialchars($conv['fname'] . ' ' . $conv['lname']);
        $thread  = $systemObject->getConversationThread($rid);
        $lastMsg = end($thread);
?>
<tr id="msgRow<?= $rid ?>">
    <td class="align-middle">
        <input type="checkbox" class="form-check-input msg-checkbox" value="<?= $rid ?>" onchange="updateBulkToolbar()">
    </td>
    <td class="align-middle fw-bold">
        <?= $mfull ?>
        <?php if ($conv['unread_count'] > 0): ?>
            <span class="badge bg-danger rounded-pill ms-1"><?= (int)$conv['unread_count'] ?></span>
        <?php endif; ?>
    </td>
    <td class="align-middle text-muted">
        <?= htmlspecialchars(substr($conv['last_message'], 0, 50)) ?>...
    </td>
    <td class="align-middle">
        <?= date('M d, Y | h:i A', strtotime($conv['last_date'])) ?>
    </td>
    <td class="align-middle">
        <button class="btn btn-info btn-sm rounded-pill px-3 fw-bold"
                data-bs-toggle="modal" data-bs-target="#viewMsg<?= $rid ?>">
            <i class="bi bi-eye-fill me-1"></i> View
        </button>
    </td>
    <td class="align-middle">
        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3"
            onclick="openDeleteMsgModal(<?= $rid ?>, '<?= $mfname ?>')">
            <i class="bi bi-trash-fill me-1"></i> Delete
        </button>
    </td>
</tr>
<!-- View Conversation Modal - full chat thread for this resident -->
<div class="modal fade" id="viewMsg<?= $rid ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header chat-thread-header text-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="chat-avatar"><?= strtoupper(substr($mfname, 0, 1)) ?></div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0"><?= $mfull ?></h6>
                        <small class="opacity-75">Resident</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 chat-thread-bg">
                <?php foreach ($thread as $bubble): ?>
                    <?php if ($bubble['side'] === 'resident'): ?>
                        <div class="chat-row mt-3">
                            <div class="chat-avatar chat-avatar-sm"><?= strtoupper(substr($mfname, 0, 1)) ?></div>
                            <div class="chat-bubble chat-bubble-incoming">
                                <?= nl2br(htmlspecialchars($bubble['message_text'])) ?>
                            </div>
                        </div>
                        <div class="chat-timestamp">
                            <?= date('F j, Y, g:i a', strtotime($bubble['date_sent'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="chat-row chat-row-outgoing mt-3">
                            <div class="chat-bubble chat-bubble-outgoing">
                                <?= nl2br(htmlspecialchars($bubble['message_text'])) ?>
                            </div>
                        </div>
                        <div class="chat-timestamp chat-timestamp-outgoing">
                            <?= date('F j, Y, g:i a', strtotime($bubble['date_sent'])) ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="reply-box d-none mt-3" id="replyBox<?= $rid ?>">
                    <textarea class="form-control reply-textarea" id="replyText<?= $rid ?>"
                              rows="3" placeholder="Type your reply to <?= $mfname ?>..."></textarea>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                onclick="toggleReplyBox(<?= $rid ?>, false)">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3"
                                onclick="sendReply(<?= $rid ?>)">
                            <i class="bi bi-send-fill me-1"></i> Send
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0" id="viewFooter<?= $rid ?>">
                <button type="button" class="btn btn-primary rounded-pill px-4"
                        id="replyBtn<?= $rid ?>"
                        onclick="toggleReplyBox(<?= $rid ?>, true)">
                    <i class="bi bi-reply-fill me-1"></i>
                    <?= ($lastMsg && $lastMsg['side'] === 'admin') ? 'Reply Again' : 'Reply' ?>
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
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
    'msg_count'      => count($conversations),
    'pending_count'  => $pending_count,
]);
