<?php
header("Content-Type: application/json");
include "db.php";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$equipment_id = intval($data['equipment_id'] ?? 0);

if ($equipment_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "equipment_id is required"
    ]);
    exit;
}

try {
    $sql = "SELECT id, name, description, price, stock, category, image_url 
            FROM equipment WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equipment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "equipment" => [
                "id" => intval($row['id']),
                "name" => $row['name'],
                "description" => $row['description'],
                "price" => floatval($row['price']),
                "stock" => intval($row['stock']),
                "category" => $row['category'],
                "image_url" => $row['image_url']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Equipment not found"
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
