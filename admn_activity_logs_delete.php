<?php
/**
 * admn_activity_logs_delete.php
 * Accepts a JSON POST: { "ids": [1, 2, 3, …] }
 * Returns:            { "success": true }  or  { "success": false, "message": "…" }
 */
require('secure_header.php');
require('classes/main.class.php');

header('Content-Type: application/json');

// Must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Decode body
$body = json_decode(file_get_contents('php://input'), true);
$ids  = $body['ids'] ?? [];

// Validate
if (empty($ids) || !is_array($ids)) {
    echo json_encode(['success' => false, 'message' => 'No IDs provided.']);
    exit;
}

// Sanitise – keep only positive integers
$ids = array_filter(array_map('intval', $ids), fn($id) => $id > 0);

if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'Invalid IDs.']);
    exit;
}

// ---- Run the delete via your existing class ----
// Option A – if your class exposes a method:
//   $result = $bmis->delete_activity_logs($ids);

// Option B – raw PDO query (replace $bmis->pdo with your actual connection property):
try {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $bmis->pdo->prepare("DELETE FROM activity_logs WHERE id IN ($placeholders)");
    $stmt->execute(array_values($ids));

    echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
} catch (Exception $e) {
    // Log internally; never expose raw DB errors to the client
    error_log('Bulk delete error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}