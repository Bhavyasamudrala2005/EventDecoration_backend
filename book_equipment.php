<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$user_id       = intval($data['user_id'] ?? 0);
$equipment_id  = intval($data['equipment_id'] ?? 0);
$quantity      = intval($data['quantity'] ?? 0);
$rental_days   = intval($data['rental_days'] ?? 0);

if ($user_id <= 0 || $equipment_id <= 0 || $quantity <= 0 || $rental_days <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required and must be greater than 0"
    ]);
    exit;
}

// Check if equipment exists and enough quantity is available
$sql = "SELECT * FROM equipment WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$result = $stmt->get_result();
$equipment = $result->fetch_assoc();

if (!$equipment) {
    echo json_encode([
        "status" => "error",
        "message" => "Equipment not found"
    ]);
    exit;
}

if ($quantity > $equipment['quantity']) {
    echo json_encode([
        "status" => "error",
        "message" => "Requested quantity not available"
    ]);
    exit;
}

// Calculate total amount
$total_amount = $quantity * $rental_days * $equipment['price_per_day'];

// Insert booking
$sql = "INSERT INTO bookings (user_id, equipment_id, quantity, rental_days, total_amount) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiid", $user_id, $equipment_id, $quantity, $rental_days, $total_amount);

if ($stmt->execute()) {
    // Update equipment quantity
    $new_quantity = $equipment['quantity'] - $quantity;
    $conn->query("UPDATE equipment SET quantity = $new_quantity WHERE id = $equipment_id");

    echo json_encode([
        "status" => "success",
        "message" => "Booking confirmed",
        "booking" => [
            "user_id" => $user_id,
            "equipment_id" => $equipment_id,
            "quantity" => $quantity,
            "rental_days" => $rental_days,
            "total_amount" => $total_amount
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Booking failed"
    ]);
}
?>
