<?php
require_once __DIR__ . '/config.php';

// Single place that opens the database connection. Returns false on failure,
// like mysqli_connect(), so existing "if (!$con)" checks keep working.
function db_connect() {
    return @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}
