<?php
// Copy this file to config.php and fill in the values. config.php is git-ignored.

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$is_local = ($host === 'localhost' || strpos($host, 'localhost:') === 0 || $host === '127.0.0.1');

if ($is_local) {
    // XAMPP defaults
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'deskshop');
} else {
    // InfinityFree (see the MySQL Databases page in the control panel)
    define('DB_HOST', 'sqlXXX.infinityfree.com');
    define('DB_USER', 'if0_XXXXXXXX');
    define('DB_PASS', 'YOUR_DB_PASSWORD');
    define('DB_NAME', 'if0_XXXXXXXX_deskshop');
}
