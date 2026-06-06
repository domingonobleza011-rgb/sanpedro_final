<?php
// Must use the same session helper as all other pages
require_once('classes/security.php');
bmis_session_start();
require_once('classes/main.class.php');

// Log the logout event before destroying session
$userdata = $_SESSION['userdata'] ?? [];
$user_for_log = [
    'id_admin' => $userdata['id_admin'] ?? null,
    'fname'    => $userdata['firstname'] ?? '',
    'lname'    => $userdata['surname']   ?? '',
    'role'     => $userdata['role']      ?? '',
    'email'    => $userdata['emailadd']  ?? '',
];
$bmis->log_login_event('logout', $user_for_log);

// Properly destroy the session
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

header('Location: index.php');
exit();