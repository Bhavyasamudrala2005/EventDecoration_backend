<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

// Get booking_id from POST
$data = json_decode(file_get_contents("php://input"), true);
$booking_id = intval($data['booking_id'] ?? 0);

if ($booking_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "booking_id is required"
    ]);
    exit;
}

// Fetch booking details
$sql = "SELECT b.id AS booking_id, b.user_id, b.quantity, b.rental_days, b.total_amount, 
               e.name AS equipment_name, e.price_per_day
        FROM bookings b
        JOIN equipment e ON b.equipment_id = e.id
        WHERE b.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode([
        "status" => "error",
        "message" => "Booking not found"
    ]);
    exit;
}

// Return confirmation
echo json_encode([
    "status" => "success",
    "message" => "Booking confirmed successfully",
    "booking" => [
        "booking_id" => $booking['booking_id'],
        "user_id" => $booking['user_id'],
        "equipment" => [
            "name" => $booking['equipment_name'],
            "quantity" => $booking['quantity'],
            "rental_days" => $booking['rental_days'],
            "price_per_day" => $booking['price_per_day']
        ],
        "total_amount" => $booking['total_amount']
    ]
]);
?>
