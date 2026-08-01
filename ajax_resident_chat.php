<?php
/**
 * ajax_resident_chat.php
 * Returns the logged-in resident's merged chat thread (admin replies + their own
 * sent messages) as an HTML fragment, plus a message count.
 * Called by resident_messages.php every few seconds via fetch() to auto-refresh
 * the conversation without a full page reload.
 */
error_reporting(0);
define('BMIS_ROLE_REQUIRED', 'resident');
require_once('secure_header.php');
require_once 'classes/main.class.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userdata']['id_resident'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit();
}

$main        = new BMISClass();
$resident_id = $_SESSION['userdata']['id_resident'];

// Viewing the thread counts as reading it — keeps the homepage badge in sync
// even when new replies arrive while this page is already open.
$main->markResidentMessagesRead($resident_id);

// Same merge logic as resident_messages.php: admin_to_resident (resident_messages
// table, side='admin') + resident_to_admin (admin_messages table, side='me').
$admin_to_resident = $main->getResidentMessages($resident_id);

$resident_sent_msgs = [];
try {
    $pdo  = $main->openConn();
    $stmt = $pdo->prepare("SELECT id_admin_msg AS id_msg, message_text, date_sent, 'me' AS side FROM admin_messages WHERE id_resident = ? ORDER BY date_sent ASC");
    $stmt->execute([$resident_id]);
    $resident_sent_msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // silently fail; only admin replies will show
}

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

$all_messages = array_merge($admin_msgs_tagged, $resident_sent_msgs);
usort($all_messages, fn($a, $b) => strtotime($a['date_sent']) <=> strtotime($b['date_sent']));

// ── Build the same bubble markup resident_messages.php uses ──────────────────
ob_start();
if (empty($all_messages)):
?>
    <div class="chat-empty">
        <i class="bi bi-chat-dots" style="font-size:2.5rem; margin-bottom:10px;"></i>
        <div class="fw-semibold">No messages yet</div>
        <small>Send a message to the Barangay Administration below.</small>
    </div>
<?php
else:
    $prev_date = '';
    foreach ($all_messages as $msg):
        $this_date = date('F j, Y', strtotime($msg['date_sent']));
        if ($this_date !== $prev_date):
            $prev_date = $this_date;
?>
    <div class="chat-day-label">— <?= $this_date ?> —</div>
<?php
        endif;
        $side     = $msg['side'];
        $is_me    = ($side === 'me');
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
<?php
    endforeach;
endif;
$chat_html = ob_get_clean();

echo json_encode([
    'chat_html' => $chat_html,
    'count'     => count($all_messages),
]);
