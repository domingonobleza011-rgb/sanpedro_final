<?php
error_reporting(E_ALL ^ E_WARNING);
include('classes/resident.class.php');

$userdetails = $bmis->get_userdata();
$user_id     = $userdetails['id_resident'];

// Ensure tables exist on first run

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    // ── POST COMMENT ──────────────────────────────────────────────
    case 'add_comment':
        $ann_id = (int)($_POST['announcement_id'] ?? 0);
        $text   = trim($_POST['comment_text'] ?? '');
        if (!$ann_id || $text === '') {
            echo json_encode(['success' => false, 'message' => 'Invalid input']);
            exit;
        }
        $new_id = $bmis->add_comment($ann_id, $user_id, $text);
        $name   = $userdetails['firstname'] . ' ' . $userdetails['surname'];
        echo json_encode([
            'success'    => true,
            'id_comment' => $new_id,
            'full_name'  => htmlspecialchars($name),
            'comment_text' => htmlspecialchars($text),
            'created_at' => date('M j, Y g:i A'),
        ]);
        break;

    // ── DELETE COMMENT ────────────────────────────────────────────
    case 'delete_comment':
        $comment_id = (int)($_POST['comment_id'] ?? 0);
        if (!$comment_id) {
            echo json_encode(['success' => false]);
            exit;
        }
        $bmis->delete_comment($comment_id, $user_id);
        echo json_encode(['success' => true]);
        break;

    // ── TOGGLE REACTION ───────────────────────────────────────────
    case 'toggle_reaction':
        $ann_id       = (int)($_POST['announcement_id'] ?? 0);
        $reaction     = $_POST['reaction_type'] ?? '';
        $allowed      = ['like','love','haha','wow','sad','angry'];
        if (!$ann_id || !in_array($reaction, $allowed)) {
            echo json_encode(['success' => false]);
            exit;
        }
        $result       = $bmis->toggle_reaction($ann_id, $user_id, $reaction);
        $counts       = $bmis->get_reactions($ann_id);
        $user_reaction= $bmis->get_user_reaction($ann_id, $user_id);
        echo json_encode([
            'success'       => true,
            'result'        => $result,
            'counts'        => $counts,
            'user_reaction' => $user_reaction,
        ]);
        break;

    // ── LOAD COMMENTS ─────────────────────────────────────────────
    case 'get_comments':
        $ann_id = (int)($_GET['announcement_id'] ?? 0);
        $comments = $bmis->get_comments($ann_id);
        echo json_encode(['success' => true, 'comments' => $comments, 'user_id' => $user_id]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
