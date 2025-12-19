<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$event_id    = intval($data['event_id'] ?? 0);
$operator_id = intval($data['operator_id'] ?? 0);

if ($event_id<=0 || $operator_id<=0) {
    echo json_encode(["status"=>"error","message"=>"Invalid event or operator"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO event_operators (event_id, operator_id)
     VALUES (?,?)"
);
$stmt->bind_param("ii",$event_id,$operator_id);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Operator assigned to event successfully"
]);
