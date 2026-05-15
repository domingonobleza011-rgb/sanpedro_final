<?php
/**
 * secure_header.php
 * Include this at the very TOP of every admin/staff/resident page.
 * It starts the session, sends security headers, and enforces auth.
 *
 * Usage:
 *   Admin pages:   require_once('secure_header.php'); // defaults to admin check
 *   Staff pages:   define('BMIS_ROLE_REQUIRED', 'staff'); require_once('secure_header.php');
 *   Resident pages:define('BMIS_ROLE_REQUIRED', 'resident'); require_once('secure_header.php');
 */

require_once __DIR__ . '/classes/security.php';

bmis_session_start();
bmis_set_security_headers();

$_BMIS_ROLE = defined('BMIS_ROLE_REQUIRED') ? BMIS_ROLE_REQUIRED : 'admin';

switch ($_BMIS_ROLE) {
    case 'staff':
        $userdetails = bmis_require_staff_or_admin();
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
