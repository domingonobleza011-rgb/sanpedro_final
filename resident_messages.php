<?php
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
$main->markResidentMessagesRead($resident_id);

// ---- Handle: Send message to admin ----
if (isset($_POST['send_to_admin'])) {
    $message_content = $_POST['admin_message_text'];
    if ($main->sendMessageToAdmin($resident_id, $message_content)) {
        echo "<script>window.location.href='resident_messages.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error: Could not send message.');</script>";
    }
}

// ---- Handle: Delete admin->resident message ----
if (isset($_POST['delete_msg'])) {
    $id_msg = $_POST['id_msg']; 
    if ($main->deleteResidentMessage($id_msg)) { 
        echo "<script>window.location.href='resident_messages.php';</script>";
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
        // Content-sniffed validation (finfo) — $file['type'] is supplied by
        // the browser and can claim any MIME type regardless of content.
        $validated = bmis_validate_image_upload($file, /* allow_pdf */ true);

        if (!$validated['ok']) {
            $upload_error = $validated['msg'];
        } else {
            $upload_dir = 'uploads/valid_ids/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $new_filename = 'validid_' . $resident_id . '_' . time() . '_' . $validated['safe_name'];
            $dest = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                if ($main->uploadValidID($resident_id, $new_filename, $file['name'], $validated['mime'], $message_note)) {
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

// Fetch messages: from admin to resident
$admin_to_resident = $main->getResidentMessages($resident_id);

$id_uploads = $main->getResidentIDUploads($resident_id);

$has_pending = false;
$has_approved = false;
foreach ($id_uploads as $up) {
    if ($up['status'] === 'pending') $has_pending = true;
    if ($up['status'] === 'approved') $has_approved = true;
}

$auto_open_upload = isset($_GET['upload_id']) && $_GET['upload_id'] == 1;

// Build unified chat thread: merge both message arrays with a 'side' marker
// admin_to_resident => side = 'admin' (left bubbles)
// resident_to_admin => side = 'me'    (right bubbles) — we'll fetch via include trick

// Since main.class.php doesn't expose a getMyMessagesToAdmin(), we do it inline:
// Access the PDO via the class internals or re-open connection
$resident_sent_msgs = [];
try {
    $pdo = $main->openConn();
    $stmt = $pdo->prepare("SELECT id_admin_msg AS id_msg, message_text, date_sent, 'me' AS side FROM admin_messages WHERE id_resident = ? ORDER BY date_sent ASC");
    $stmt->execute([$resident_id]);
    $resident_sent_msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // silently fail; only admin replies will show
}

// Tag admin messages with side
$admin_msgs_tagged = [];
foreach ($admin_to_resident as $m) {
    $actual_id = isset($m['id_msg']) ? $m['id_msg'] : (isset($m['id_message']) ? $m['id_message'] : null);
    $admin_msgs_tagged[] = [
        'id_msg'       => $actual_id,
        'message_text' => $m['message_text'],
        'date_sent'    => $m['date_sent'],
        'side'         => 'admin'
    ];
}

// Merge & sort by date ascending
$all_messages = array_merge($admin_msgs_tagged, $resident_sent_msgs);
usort($all_messages, fn($a, $b) => strtotime($a['date_sent']) <=> strtotime($b['date_sent']));
?>

<!DOCTYPE html> 
<html>
<head> 
    <title>Messages - Barangay San Pedro Iriga</title>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <script src="https://kit.fontawesome.com/67a9b7069e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* ─── Layout ─── */
        html, body { height: 100%; margin: 0; background: #eeeef3; }
        body { display: flex; flex-direction: column; }

        .page-wrapper {
            max-width: 780px;
            width: 100%;
            margin: 0 auto;
            padding: 12px 12px 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            padding-bottom: 65px; /* mobile nav */
        }

        @media (min-width: 768px) {
            .page-wrapper { padding-bottom: 0; }
        }

        /* ─── Messenger Window ─── */
        .messenger-card {
            background: #fff;
            border-radius: 18px 18px 0 0;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }

        .messenger-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .messenger-header .admin-avatar {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .online-dot {
            display: inline-block;
            width: 10px; height: 10px;
            background: #4ade80;
            border-radius: 50%;
            margin-right: 5px;
            box-shadow: 0 0 0 2px #fff;
        }

        /* ─── Chat Body ─── */
        .chat-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-height: 0;
        }

        .chat-day-label {
            text-align: center;
            font-size: 0.72rem;
            color: #888;
            margin: 10px 0 4px;
            letter-spacing: 0.03em;
        }

        /* ─── Bubbles ─── */
        .bubble-row {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .bubble-row.me {
            flex-direction: row-reverse;
        }

        .bubble-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .bubble-avatar.admin-av { background: #0d6efd; color: #fff; }
        .bubble-avatar.me-av    { background: #6c757d; color: #fff; }

        .bubble {
            max-width: 68%;
            padding: 10px 14px;
            border-radius: 18px;
            font-size: 0.92rem;
            line-height: 1.5;
            position: relative;
            word-break: break-word;
        }

        /* Admin bubble — left, gray */
        .bubble.admin {
            background: #fff;
            color: #1a1a2e;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.10);
        }

        /* Resident bubble — right, blue */
        .bubble.me {
            background: #0d6efd;
            color: #fff;
            border-bottom-right-radius: 4px;
            box-shadow: 0 1px 3px rgba(13,110,253,0.25);
        }

        .bubble-time {
            font-size: 0.68rem;
            opacity: 0.55;
            margin-top: 4px;
            display: block;
        }

        .bubble.me .bubble-time { text-align: right; color: #dbeafe; opacity: 0.85; }
        .bubble.admin .bubble-time { color: #888; }

        /* Delete button on admin bubble */
        .bubble-delete {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 0.75rem;
            opacity: 0;
            transition: opacity 0.2s;
            cursor: pointer;
            padding: 0;
            margin-left: 4px;
            align-self: center;
        }

        .bubble-row:hover .bubble-delete { opacity: 1; }

        /* ─── Empty state ─── */
        .chat-empty {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #aaa;
            padding: 40px 0;
        }

        /* ─── Message Input Bar ─── */
        .chat-input-bar {
            background: #fff;
            border-top: 1px solid #e9ecef;
            padding: 10px 14px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
        }

        .chat-input-bar textarea {
            flex: 1;
            border: 1.5px solid #dee2e6;
            border-radius: 22px;
            padding: 9px 16px;
            font-size: 0.92rem;
            resize: none;
            outline: none;
            max-height: 120px;
            overflow-y: auto;
            line-height: 1.4;
            transition: border-color 0.2s;
        }

        .chat-input-bar textarea:focus {
            border-color: #0d6efd;
        }

        .chat-send-btn {
            width: 42px; height: 42px;
            border-radius: 50%;
            background: #0d6efd;
            color: #fff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s, transform 0.1s;
        }

        .chat-send-btn:hover   { background: #0a58ca; }
        .chat-send-btn:active  { transform: scale(0.93); }

        /* ─── ID Upload & Status badges ─── */
        .status-pending  { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .status-approved { background: #d1e7dd; color: #0f5132; border: 1px solid #198754; }
        .status-rejected { background: #f8d7da; color: #842029; border: 1px solid #dc3545; }
        .upload-zone { border: 2px dashed #0d6efd; border-radius: 12px; background: #f8f9ff; }

        /* ─── Mobile bottom nav ─── */
        
        
        
        
    </style>
</head>
<body>

<!-- DESKTOP NAV -->
<?php include __DIR__ . '/resident_navbar.php'; ?>

<div class="page-wrapper">

    <!-- VERIFICATION NOTICE -->
    <?php if (!$is_verified): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-3 p-3" role="alert" style="border-left: 5px solid #ffc107 !important;">
        <div class="d-flex align-items-start gap-3">
            <div style="font-size:1.8rem;">&#x1F512;</div>
            <div>
                <h6 class="fw-bold mb-1">Account Not Yet Verified</h6>
                <p class="mb-2 small">Upload a valid government-issued ID for the admin to verify your account.</p>
                <a href="resident_messages.php?upload_id=1" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">
                    <i class="bi bi-upload me-1"></i> Upload Valid ID
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-success border-0 shadow-sm rounded-4 mb-3 py-2 px-3 small" role="alert">
        <i class="bi bi-patch-check-fill me-2"></i> <strong>Account Verified</strong> &mdash; You have full access to all barangay services.
    </div>
    <?php endif; ?>

    <!-- ID UPLOAD HISTORY — only shown when NOT yet verified -->
    <?php if (!empty($id_uploads) && !$is_verified): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-header bg-white border-bottom py-2 px-3 rounded-top-4 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold mb-0 small"><i class="bi bi-card-image me-2 text-primary"></i>ID Submission History</h6>
            <button class="btn btn-link btn-sm text-muted p-0" data-bs-toggle="collapse" data-bs-target="#idHistoryTable">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show" id="idHistoryTable">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-2 px-3">File</th>
                                <th class="py-2">Date</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($id_uploads as $up): ?>
                            <tr>
                                <td class="px-3 align-middle">
                                    <i class="bi bi-file-earmark-image me-1 text-primary"></i>
                                    <?= htmlspecialchars($up['original_name']); ?>
                                </td>
                                <td class="align-middle text-muted"><?= date('M d, Y', strtotime($up['upload_date'])); ?></td>
                                <td class="align-middle">
                                    <?php if ($up['status'] === 'approved'): ?>
                                        <span class="badge rounded-pill status-approved px-2">&#x2705; Approved</span>
                                    <?php elseif ($up['status'] === 'rejected'): ?>
                                        <span class="badge rounded-pill status-rejected px-2">&#x274C; Rejected</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill status-pending px-2">&#x23F3; Pending</span>
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
    <?php endif; ?>

    <!-- ═══ MESSENGER WINDOW ═══ -->
    <div class="messenger-card">

        <!-- Header -->
        <div class="messenger-header">
            <div class="admin-avatar"><i class="bi bi-building-fill"></i></div>
            <div>
                <div class="fw-bold" style="font-size:1rem;">Barangay Administration</div>
                <div style="font-size:0.78rem; opacity:0.85;">
                    <span class="online-dot"></span> Barangay San Pedro, Iriga City
                </div>
            </div>
            <?php if (!$is_verified): ?>
            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-sm btn-light rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#uploadIDModal" title="Upload Valid ID">
                    <i class="bi bi-upload me-1"></i><span class="d-none d-sm-inline">Upload ID</span>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <!-- Chat Messages Body -->
        <div class="chat-body" id="chatBody">
            <?php if (empty($all_messages)): ?>
                <div class="chat-empty">
                    <i class="bi bi-chat-dots" style="font-size:2.5rem; margin-bottom:10px;"></i>
                    <div class="fw-semibold">No messages yet</div>
                    <small>Send a message to the Barangay Administration below.</small>
                </div>
            <?php else: ?>
                <?php
                $prev_date = '';
                foreach ($all_messages as $msg):
                    $this_date = date('F j, Y', strtotime($msg['date_sent']));
                    if ($this_date !== $prev_date):
                        $prev_date = $this_date;
                ?>
                    <div class="chat-day-label">— <?= $this_date ?> —</div>
                <?php endif; ?>

                <?php
                    $side = $msg['side'];
                    $is_me = ($side === 'me');
                    $time_str = date('h:i A', strtotime($msg['date_sent']));
                ?>
                <div class="bubble-row <?= $is_me ? 'me' : 'admin' ?>">
                    <div class="bubble-avatar <?= $is_me ? 'me-av' : 'admin-av' ?>">
                        <i class="bi <?= $is_me ? 'bi-person-fill' : 'bi-building-fill' ?>"></i>
                    </div>
                    <div class="bubble <?= $is_me ? 'me' : 'admin' ?>">
                        <?= nl2br(htmlspecialchars($msg['message_text'])) ?>
                        <span class="bubble-time"><?= $time_str ?></span>
                    </div>
                    <?php if (!$is_me && !empty($msg['id_msg'])): ?>
                    <form action="" method="POST" class="d-inline" onsubmit="return confirm('Delete this message?');">
                        <input type="hidden" name="id_msg" value="<?= $msg['id_msg'] ?>">
                        <button type="submit" name="delete_msg" class="bubble-delete" title="Delete">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Input Bar -->
        <div class="chat-input-bar">
            <form action="" method="POST" id="msgForm" style="display:contents;">
                <textarea name="admin_message_text" id="msgInput" rows="1"
                    placeholder="Write a message to the Barangay..." required
                    oninput="autoResize(this)"
                    onkeydown="handleEnter(event)"></textarea>
                <button type="submit" name="send_to_admin" class="chat-send-btn" title="Send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>

</div><!-- end page-wrapper -->


<!-- UPLOAD VALID ID MODAL -->
<div class="modal fade" id="uploadIDModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-warning text-dark rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-upload me-2"></i>Upload Valid ID</h5>
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

<script>
// Auto-scroll to bottom of chat
const chatBody = document.getElementById('chatBody');
if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;

// Auto-resize textarea
function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

// Send on Enter (Shift+Enter = newline)
function handleEnter(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        const textarea = document.getElementById('msgInput');
        if (textarea.value.trim()) {
            document.getElementById('msgForm').submit();
        }
    }
}

// Auto-open upload modal if triggered via URL
<?php if ($auto_open_upload && !$has_pending && !$has_approved && !$is_verified): ?>
window.addEventListener('DOMContentLoaded', function() {
    new bootstrap.Modal(document.getElementById('uploadIDModal')).show();
});
<?php endif; ?>

// ── Auto-refresh the chat thread every 5 seconds ────────────────────────────
(function () {
    let lastCount = <?= (int)count($all_messages) ?>;

    function isNearBottom() {
        // "Near bottom" = within ~80px of the latest message, so we only
        // auto-scroll if the resident was already following the conversation —
        // not if they scrolled up to re-read something older.
        return chatBody.scrollHeight - chatBody.scrollTop - chatBody.clientHeight < 80;
    }

    function poll() {
        // Don't refresh mid-interaction: an open modal (e.g. the delete confirm
        // isn't a modal here, but the Upload ID modal is) or an in-progress draft
        // shouldn't be interrupted by a DOM swap underneath them.
        if (document.querySelector('.modal.show')) return;

        fetch('ajax_resident_chat.php')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || data.error || !chatBody) return;

                const wasNearBottom = isNearBottom();
                const hasNewMessage = data.count > lastCount;
                lastCount = data.count;

                chatBody.innerHTML = data.chat_html;

                // Only auto-scroll down if there's something new AND the
                // resident hadn't scrolled away to read earlier messages.
                if (hasNewMessage && wasNearBottom) {
                    chatBody.scrollTop = chatBody.scrollHeight;
                }
            })
            .catch(function () {});
    }

    setInterval(poll, 60000);
})();
</script>
</body>

</html>
