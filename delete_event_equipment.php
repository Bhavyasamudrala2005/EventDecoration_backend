<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(["status"=>"error","message"=>"Invalid ID"]);
    exit;
}

// Restore stock
$q = $conn->prepare(
    "SELECT equipment_id, quantity FROM event_equipment WHERE id=?"
);
$q->bind_param("i",$id);
$q->execute();
$res = $q->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["status"=>"error","message"=>"Item not found"]);
    exit;
}

$row = $res->fetch_assoc();

$restore = $conn->prepare(
    "UPDATE equipment SET quantity = quantity + ? WHERE id = ?"
);
$restore->bind_param("ii", $row['quantity'], $row['equipment_id']);
$restore->execute();

// Delete
$del = $conn->prepare("DELETE FROM event_equipment WHERE id=?");
$del->bind_param("i",$id);
$del->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Equipment removed from event"
]);
?>
