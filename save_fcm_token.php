<?php
// save_fcm_token.php
// Called via fetch() from the resident pages to store the FCM token in the DB.

header('Content-Type: application/json');
ini_set('display_errors', 0);

session_start();
if (empty($_SESSION['userdata']['id_resident'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$id_resident = (int) $_SESSION['userdata']['id_resident'];
$token       = trim($_POST['token'] ?? '');

if (empty($token)) {
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit;
}

require_once 'classes/conn.php'; // gives $conn

try {
    // Upsert: insert or update the token for this resident
    $stmt = $conn->prepare("
        INSERT INTO tbl_fcm_tokens (resident_id, fcm_token, updated_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE fcm_token = VALUES(fcm_token), updated_at = NOW()
    ");
    $stmt->execute([$id_resident, $token]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("save_fcm_token error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'DB error']);
}