<?php
require_once 'classes/main.class.php';
$main = new BMISClass(); 

if (isset($_POST['send_msg'])) {
    $id = $_POST['id_resident'];
    $msg = $_POST['message'];

    // Optional: return the admin to the page they sent the message from
    // (e.g. a certificate request table). Falls back to the resident list
    // to preserve existing behavior for callers that don't pass it.
    $redirect = $_POST['redirect_to'] ?? 'admn_resident_crud.php';
    if (!preg_match('/^[a-zA-Z0-9_\-]+\.php(\?[^\s]*)?$/', $redirect)) {
        $redirect = 'admn_resident_crud.php';
    }
    $separator = (strpos($redirect, '?') !== false) ? '&' : '?';

    if ($main->sendMessage($id, $msg)) {
        // Redirect with success
        header("Location: $redirect{$separator}status=sent");
    } else {
        // Redirect with error
        header("Location: $redirect{$separator}status=error");
    }
    exit();
}