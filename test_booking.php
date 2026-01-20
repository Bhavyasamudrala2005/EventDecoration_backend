<?php
header("Content-Type: application/json");
include "db.php";

// Test 1: Insert a sample booking directly
$sql = "INSERT INTO bookings (
    user_id, equipment_id, quantity, rental_days, total_amount, 
    status, start_date, end_date, time_slot, 
    customer_name, customer_phone, delivery_address, booking_date
) VALUES (
    1, 1, 1, 2, 2000,
    'pending', '2026-01-16', '2026-01-18', 'Full Day',
    'Test User', '9876543210', 'Test Address', NOW()
)";

if ($conn->query($sql)) {
    $booking_id = $conn->insert_id;
    
    // Get equipment name
    $equip = $conn->query("SELECT name FROM equipment WHERE id = 1");
    $equipment_name = "Unknown";
    if ($equip && $equip->num_rows > 0) {
        $equipment_name = $equip->fetch_assoc()['name'];
    }
    
    echo json_encode([
        "success" => true,
        "message" => "Test booking created!",
        "booking_id" => $booking_id,
        "equipment_name" => $equipment_name
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode([
        "success" => false,
        "error" => $conn->error,
        "message" => "Failed to create booking"
    ], JSON_PRETTY_PRINT);
}

$conn->close();
?>
