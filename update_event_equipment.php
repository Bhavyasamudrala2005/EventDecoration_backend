<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$item_id = intval($data['id'] ?? 0);
$new_qty = intval($data['quantity'] ?? 0);

if ($item_id <= 0 || $new_qty <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
    exit;
}

// Get existing item
$stmt = $conn->prepare(
    "SELECT equipment_id, price_per_day, quantity 
     FROM event_equipment WHERE id = ?"
);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Item not found"
    ]);
    exit;
}

$row = $res->fetch_assoc();
$equipment_id = $row['equipment_id'];
$old_qty = $row['quantity'];
$price = $row['price_per_day'];

$diff = $new_qty - $old_qty;

// Check stock if increasing
if ($diff > 0) {
    $q = $conn->prepare(
        "SELECT quantity FROM equipment WHERE id = ?"
    );
    $q->bind_param("i", $equipment_id);
    $q->execute();
    $stock = $q->get_result()->fetch_assoc()['quantity'];

    if ($diff > $stock) {
        echo json_encode([
            "status" => "error",
            "message" => "Not enough stock"
        ]);
        exit;
    }

    $updStock = $conn->prepare(
        "UPDATE equipment SET quantity = quantity - ? WHERE id = ?"
    );
    $updStock->bind_param("ii", $diff, $equipment_id);
    $updStock->execute();
}

// Restore stock if decreasing
if ($diff < 0) {
    $restore = abs($diff);
    $updStock = $conn->prepare(
        "UPDATE equipment SET quantity = quantity + ? WHERE id = ?"
    );
    $updStock->bind_param("ii", $restore, $equipment_id);
    $updStock->execute();
}

$new_total = $new_qty * $price;

$upd = $conn->prepare(
    "UPDATE event_equipment 
     SET quantity = ?, total_price = ? 
     WHERE id = ?"
);
$upd->bind_param("idi", $new_qty, $new_total, $item_id);
$upd->execute();

echo json_encode([
    "status" => "success",
    "message" => "Equipment quantity updated"
]);
?>
