<?php
header("Content-Type: application/json");
// Force error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

$response = [];

// 1. Check Connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}
$response['db_connection'] = "Connected successfully";

// 2. Count Rows
$tables = ['users', 'equipment', 'bookings'];
foreach ($tables as $table) {
    try {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        if ($result && $row = $result->fetch_assoc()) {
            $response["counts"][$table] = $row['count'];
        } else {
            $response["counts"][$table] = "Table not found or empty";
        }
    } catch (Exception $e) {
        $response["counts"][$table] = "Error: " . $e->getMessage();
    }
}

// 3. Show Last 5 Bookings
$sql = "SELECT id, user_id, equipment_id, status, booking_date FROM bookings ORDER BY id DESC LIMIT 5";
$result = $conn->query($sql);
$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$response['recent_bookings'] = $bookings;

// 4. Check Pending
$pending = $conn->query("SELECT count(*) as c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
$response['pending_count'] = $pending;

echo json_encode($response, JSON_PRETTY_PRINT);
?>
