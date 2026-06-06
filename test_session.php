<?php
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

$sessionPath = __DIR__ . '/sessions';
if (!is_dir($sessionPath)) mkdir($sessionPath, 0755, true);
session_save_path($sessionPath);

session_start();

$_SESSION['test'] = 'working';

echo '<pre>';
echo 'Session ID: ' . session_id() . "\n";
echo 'Session Status: ' . session_status() . "\n";
echo 'Session Path: ' . session_save_path() . "\n";
echo 'Session Data: '; print_r($_SESSION);
echo 'Cookies: '; print_r($_COOKIE);
echo 'PHP Version: ' . phpversion() . "\n";
echo '</pre>';
?>