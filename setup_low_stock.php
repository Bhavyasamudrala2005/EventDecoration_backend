<?php
// This script updates equipment items to have "Limited" availability
// Run this once to populate Low Stock Alerts

header("Content-Type: application/json");
include "db.php";

$updates = [
    ["name" => "Stage Speakers", "availability" => "Limited", "quantity" => 3],
    ["name" => "Small Shade Tent", "availability" => "Limited", "quantity" => 2],
    ["name" => "VIP Cushioned Chairs", "availability" => "Limited", "quantity" => 4],
    ["name" => "Backdrop Decor", "availability" => "Limited", "quantity" => 3],
];

$updated = 0;
$errors = [];

foreach ($updates as $item) {
    $name = $item["name"];
    $availability = $item["availability"];
    $quantity = $item["quantity"];
    
    $sql = "UPDATE equipment SET availability = ?, quantity = ? WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchName = "%" . $name . "%";
    $stmt->bind_param("sis", $availability, $quantity, $searchName);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $updated += $stmt->affected_rows;
        }
    } else {
        $errors[] = "Failed to update: " . $name;
    }
    $stmt->close();
}

// Get current low stock items
$result = $conn->query("SELECT id, name, category, availability, quantity FROM equipment WHERE availability = 'Limited' OR quantity < 5");
$lowStockItems = [];
while ($row = $result->fetch_assoc()) {
    $lowStockItems[] = $row;
}

echo json_encode([
    "status" => "success",
    "message" => "Updated $updated equipment items to Limited availability",
    "low_stock_count" => count($lowStockItems),
    "low_stock_items" => $lowStockItems,
    "errors" => $errors
], JSON_PRETTY_PRINT);

$conn->close();
?>
