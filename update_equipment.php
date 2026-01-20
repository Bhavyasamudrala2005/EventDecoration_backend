<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

// Get ID - can be numeric string like "1" or alphanumeric like "SPK001"
$id_input = trim($data['id'] ?? '');
$name = trim($data['name'] ?? '');
$category = trim($data['category'] ?? '');
$type = trim($data['type'] ?? '');
$specifications = trim($data['specifications'] ?? '');
$price_per_day = floatval($data['price_per_day'] ?? 0);
$availability = trim($data['availability'] ?? '');

// Try to parse as integer first
$id = intval($id_input);

// Validate required fields
if ($id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid equipment ID: " . $id_input
    ]);
    exit;
}

if ($name === '' || $category === '' || $type === '' || $specifications === '' || $price_per_day <= 0 || $availability === '') {
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

// Check if equipment exists
$check_stmt = $conn->prepare("SELECT id FROM equipment WHERE id = ?");
$check_stmt->bind_param("i", $id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Equipment not found"
    ]);
    exit;
}

// Update equipment
$stmt = $conn->prepare(
    "UPDATE equipment 
     SET name = ?, category = ?, type = ?, specifications = ?, price_per_day = ?, availability = ? 
     WHERE id = ?"
);
$stmt->bind_param("ssssdsi", $name, $category, $type, $specifications, $price_per_day, $availability, $id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Equipment updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update equipment: " . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
