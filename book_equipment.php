<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = intval($data['user_id'] ?? 1);
$equipment_id = intval($data['equipment_id']);
$quantity = intval($data['quantity'] ?? 1);
$rental_days = intval($data['rental_days'] ?? 1);
$total_amount = floatval($data['total_amount'] ?? 0);
$start_date = $data['start_date'] ?? date('Y-m-d');
$time_slot = $data['time_slot'] ?? 'Full Day';
$customer_name = $data['customer_name'] ?? 'Unknown';
$customer_phone = $data['customer_phone'] ?? '';
$delivery_address = $data['delivery_address'] ?? '';

$end_date = date('Y-m-d', strtotime($start_date . " + $rental_days days"));

// Robust insertion
$stmt = $conn->prepare("INSERT INTO bookings (user_id, equipment_id, quantity, rental_days, total_amount, status, start_date, end_date, time_slot, customer_name, customer_phone, delivery_address, booking_date) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, NOW())");

if ($stmt) {
    $stmt->bind_param("iiiidssssss", $user_id, $equipment_id, $quantity, $rental_days, $total_amount, $start_date, $end_date, $time_slot, $customer_name, $customer_phone, $delivery_address);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;
        
        // Fetch equipment name for response
        $equip = $conn->query("SELECT name FROM equipment WHERE id = $equipment_id");
        $e_name = ($equip && $r=$equip->fetch_assoc()) ? $r['name'] : "Equipment";

        echo json_encode([
            "success" => true,
            "status" => "success",
            "message" => "Booking successful!",
            "booking" => [
                "id" => $booking_id,
                "equipment_name" => $e_name,
                "status" => "pending",
                "total_amount" => $total_amount
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Database execute error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Database prepare error: " . $conn->error]);
}

$conn->close();
?>
