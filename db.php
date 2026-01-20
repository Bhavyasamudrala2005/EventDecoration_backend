<?php
// Suppress HTML error output - only JSON should be returned
error_reporting(0);
ini_set('display_errors', 0);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "localhost";   // or "127.0.0.1"
$user = "root";
$pass = "";            // default for XAMPP unless you changed it
$db   = "eventease";   // your DB name
$port = 3306;          // MUST match the port MySQL is actually using

try {
    $conn = new mysqli($host, $user, $pass, $db, $port);
    $conn->set_charset("utf8");
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        "status"  => "error",
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit;
}