<?php
/**
 * Debug script to check bookings table
 * Access this directly to see all bookings in the database
 */
header("Content-Type: application/json");
include "db.php";

// Get all bookings from the database
$sql = "SELECT 
            b.id,
            b.user_id,
            b.equipment_id,
            b.quantity,
            b.rental_days,
            b.total_amount,
            b.status,
            b.booking_date,
            e.name as equipment_name,
            u.name as user_name
        FROM bookings b
        LEFT JOIN equipment e ON b.equipment_id = e.id
        LEFT JOIN users u ON b.user_id = u.id
        ORDER BY b.id DESC
        LIMIT 20";

$result = $conn->query($sql);

$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

// Also get all users for reference
$users_sql = "SELECT id, name, email FROM users LIMIT 10";
$users_result = $conn->query($users_sql);
$users = [];
if ($users_result) {
    while ($row = $users_result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode([
    "message" => "Debug info for bookings",
    "total_bookings" => count($bookings),
    "bookings" => $bookings,
    "users" => $users,
    "note" => "Check if user_id in bookings matches the logged-in user's id from users table"
], JSON_PRETTY_PRINT);

$conn->close();
?>
