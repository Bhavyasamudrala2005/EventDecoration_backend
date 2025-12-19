<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

// Get user_id from POST
$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id is required"
    ]);
    exit;
}

// Fetch bookings by status
$sql = "SELECT b.id AS booking_id, b.quantity, b.rental_days, b.total_amount, b.status, b.booking_date,
               e.name AS equipment_name, e.price_per_day
        FROM bookings b
        JOIN equipment e ON b.equipment_id = e.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [
    "pending" => [],
    "accepted" => [],
    "rejected" => []
];

while ($row = $result->fetch_assoc()) {
    $booking_data = [
        "booking_id" => $row['booking_id'],
        "equipment_name" => $row['equipment_name'],
        "quantity" => $row['quantity'],
        "rental_days" => $row['rental_days'],
        "price_per_day" => $row['price_per_day'],
        "total_amount" => $row['total_amount'],
        "booking_date" => $row['booking_date']
    ];

    if ($row['status'] == 'pending') {
        $bookings['pending'][] = $booking_data;
    } elseif ($row['status'] == 'accepted') {
        $bookings['accepted'][] = $booking_data;
    } elseif ($row['status'] == 'rejected') {
        $bookings['rejected'][] = $booking_data;
    }
}

// Return booking status
echo json_encode([
    "status" => "success",
    "user_id" => $user_id,
    "bookings" => $bookings
]);
?>
