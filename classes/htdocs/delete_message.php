<?php
require_once 'classes/main.class.php';
$systemObject = new BMISClass();

if (isset($_POST['delete_msg'])) {
    $id = $_POST['id_admin_msg'];
    
    if ($systemObject->deleteMessage($id)) {
        // Redirect back to the messages page with a success message
        header("Location: admn_messages.php?status=deleted");
    } else {
        header("Location: admn_messages.php?status=error");
    }
    exit();
}
?>