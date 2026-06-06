<?php
require_once 'classes/main.class.php';
$main = new BMISClass(); 

if (isset($_POST['send_msg'])) {
    $id = $_POST['id_resident'];
    $msg = $_POST['message'];

    if ($main->sendMessage($id, $msg)) {
        // Redirect with success
        header("Location: admn_resident_crud.php?status=sent");
    } else {
        // Redirect with error
        header("Location: admn_resident_crud.php?status=error");
    }
    exit();
}