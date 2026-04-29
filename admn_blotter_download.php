<?php
require_once("classes/conn.php");

// 1. Get "eusebia.jpg" and turn it into "eusebia"
$input = $_GET['blot_photo'] ?? '';
$search_name = str_replace('.jpg', '', $input);

try {
    // 2. Search both fname and lname to be safe
    $q = $conn->prepare("SELECT `blot_photo` FROM `tbl_blotter` WHERE `lname` = :n OR `fname` = :n LIMIT 1");
    $q->bindParam(":n", $search_name);
    $q->execute();

    if ($q->rowCount() === 1) {
        $row = $q->fetch(PDO::FETCH_ASSOC);
        $image_data = $row['blot_photo'];

        if (ob_get_length()) ob_clean();

        header("Content-type: image/jpeg");
        header("Content-Disposition: inline; filename=\"$input\"");
        header("Content-Length: " . strlen($image_data));
        
        echo $image_data;
    } else {
        echo "No record found for name: " . htmlspecialchars($search_name);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
exit();