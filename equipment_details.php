<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['equipment_id']) || intval($data['equipment_id']) <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "equipment_id is required"
    ]);
    exit;
}

$equipment_id = intval($data['equipment_id']);

$stmt = $conn->prepare(
    "SELECT id, category_id, name, price_per_day, quantity, image_url
     FROM equipment
     WHERE id = ?"
);
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Equipment not found"
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "equipment" => $result->fetch_assoc()
]);
?>
