<?php
include 'conn.php'; // Your DB connection
session_start();

if (isset($_SESSION['resident_id']) && isset($_POST['token'])) {
    $resident_id = $_SESSION['resident_id'];
    $token = $_POST['token'];

    $stmt = $conn->prepare("INSERT INTO resident_tokens (resident_id, token) VALUES (?, ?) 
                            ON DUPLICATE KEY UPDATE last_updated = CURRENT_TIMESTAMP");
    $stmt->bind_param("is", $resident_id, $token);
    $stmt->execute();
    echo "Token saved.";
}