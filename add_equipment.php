<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$category = trim($data['category'] ?? '');
$type = trim($data['type'] ?? '');
$specifications = trim($data['specifications'] ?? '');
$price_per_day = floatval($data['price_per_day'] ?? 0);
$quantity = intval($data['quantity'] ?? 0);
$availability = trim($data['availability'] ?? '');

// Validate required fields
if ($name === '' || $category === '' || $type === '' || $specifications === '' || $price_per_day <= 0 || $quantity <= 0 || $availability === '') {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit;
}

// Validate availability value
$valid_availability = ['Available', 'Limited', 'Unavailable'];
if (!in_array($availability, $valid_availability)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid availability status"
    ]);
    exit;
}

// Insert equipment
$stmt = $conn->prepare(
    "INSERT INTO equipment (name, category, type, specifications, price_per_day, quantity, availability) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssdis", $name, $category, $type, $specifications, $price_per_day, $quantity, $availability);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Equipment added successfully",
        "equipment_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to add equipment: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
