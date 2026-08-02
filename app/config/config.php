<?php
date_default_timezone_set('Asia/Jakarta');
define('ROOTPATH', dirname(__DIR__, 2));
define('VIEWPATH', ROOTPATH . '/app/views');
define('ASSETSPATH', ROOTPATH . '/assets');
define('BASEURL', 'https://rentalmarket.ct.ws');

define('DB_HOST', 'sql301.infinityfree.com');
define('DB_USER', 'if0_42432755');
define('DB_PASS', 'acEMI4l1PFb9ibc');
define('DB_NAME', 'if0_42432755_rental_market');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>