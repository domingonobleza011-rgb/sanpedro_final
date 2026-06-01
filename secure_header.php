<?php
require_once __DIR__ . '/classes/security.php';

bmis_session_start();
bmis_set_security_headers();

$_BMIS_ROLE = defined('BMIS_ROLE_REQUIRED') ? BMIS_ROLE_REQUIRED : 'admin';

switch ($_BMIS_ROLE) {
    case 'admin_dashboard': // ← NEW: Admin + Secretary + Punong Barangay
        $userdetails = bmis_require_admin_dashboard();
        break;
    case 'staff':
        $userdetails = bmis_require_staff_or_admin();
        if (($userdetails['role'] ?? '') === 'user' && ($userdetails['position'] ?? '') === 'Sk Chairperson') {
            http_response_code(403);
            die('Access denied. SK Chairperson can only access the SK Dashboard.');
        }
        break;
    case 'resident':
        $userdetails = bmis_require_resident();
        break;
    case 'any':
        $userdetails = bmis_require_login();
        break;
    default: // 'admin'
        $userdetails = bmis_require_admin();
        break;
}