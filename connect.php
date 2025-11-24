<?php
// Database connection configuration from Environment Variables
$host = getenv('DB_HOST') ?: 'mysql';
$database = getenv('DB_NAME') ?: 'survival_war';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'changeme';

// Parse query string if needed
parse_str($_SERVER['QUERY_STRING'] ?? '', $query_params);
if (!empty($query_params)) {
    foreach ($query_params as $key => $value) {
        $_GET[$key] = $value;
    }
}

// Initialize mysqli
$db = mysqli_init();
if (!$db) {
    die("mysqli_init failed");
}

// Set connection timeout
mysqli_options($db, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

// Connect to database
// Note: MySQL 9.2+ uses SSL by default. We rely on the internal network.
// If connection fails, we might need to explicitly disable SSL verify.
if (!mysqli_real_connect($db, $host, $user, $password, $database)) {
    die("Connect Error (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
}

// Set character set to utf8mb4
mysqli_set_charset($db, "utf8mb4");

// Escape input data to prevent SQL injection
// Note: get_magic_quotes_gpc() is removed in PHP 8.0+, so we assume it's false/gone.
// We just escape everything.

function escape_array($array, $db) {
    return array_map(function($val) use ($db) {
        return mysqli_real_escape_string($db, $val);
    }, $array);
}

$_GET = escape_array($_GET, $db);
$_POST = escape_array($_POST, $db);
$_COOKIE = escape_array($_COOKIE, $db);
?>
