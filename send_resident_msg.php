<?php

require_once 'classes/main.class.php';

$main = new BMISClass(); 

// Determine the "recent page" or fallback to the main dashboard
$referer = $_SERVER['HTTP_REFERER'] ?? 'admn_resident_crud.php';

// --- LOGIC: SEND MESSAGE ---
if (isset($_POST['send_msg'])) {
    $id = $_POST['id_resident'];
    $msg = $_POST['message'];

    $status = $main->sendMessage($id, $msg) ? "sent" : "error";
    
    // Redirect back with status
    $connector = (parse_url($referer, PHP_URL_QUERY) ? '&' : '?');
    header("Location: " . $referer . $connector . "status=" . $status);
    exit();
}

// --- LOGIC: EDIT STAFF ---
if (isset($_POST['edit_staff'])) {
    // Assuming you pass the staff ID and the updated data via POST
    $staff_id = $_POST['staff_id'];
    $staff_data = [
        'fname' => $_POST['fname'],
        'lname' => $_POST['lname'],
        'role'  => $_POST['role']
        // add other fields as needed
    ];

    // Call your edit method (adjust method name to match your class)
    if ($main->editStaff($staff_id, $staff_data)) {
        $status = "updated";
    } else {
        $status = "edit_error";
    }

    // Redirect back with status
    $connector = (parse_url($referer, PHP_URL_QUERY) ? '&' : '?');
    header("Location: " . $referer . $connector . "status=" . $status);
    exit();
}