<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$event_id = intval($data['event_id'] ?? 0);
$items = $data['equipment'] ?? [];

if ($event_id <= 0 || empty($items)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
    exit;
}

$conn->begin_transaction();

try {
    $added = [];

    foreach ($items as $item) {
        $equipment_id = intval($item['equipment_id']);
        $qty = intval($item['quantity']);

        // Get equipment data
        $q = $conn->prepare(
            "SELECT price_per_day, quantity FROM equipment WHERE id=?"
        );
        $q->bind_param("i", $equipment_id);
        $q->execute();
        $res = $q->get_result();

        if ($res->num_rows === 0) continue;

        $row = $res->fetch_assoc();

        if ($qty > $row['quantity']) continue;

        $price = $row['price_per_day'];
        $total = $price * $qty;

        // Insert into event_equipment
        $ins = $conn->prepare(
            "INSERT INTO event_equipment
            (event_id, equipment_id, quantity, price_per_day, total_price)
            VALUES (?,?,?,?,?)"
        );
        $ins->bind_param("iiidd", $event_id, $equipment_id, $qty, $price, $total);
        $ins->execute();

        // Reduce stock
        $upd = $conn->prepare(
            "UPDATE equipment SET quantity = quantity - ? WHERE id = ?"
        );
        $upd->bind_param("ii", $qty, $equipment_id);
        $upd->execute();

        $added[] = [
            "equipment_id" => $equipment_id,
            "quantity" => $qty,
            "total_price" => $total
        ];
    }

    if (empty($added)) {
        throw new Exception("No equipment added");
    }

    $conn->commit();

    echo json_encode([
        "status" => "success",
        "items_added" => $added
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
