<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id'] ?? 0);

if ($id<=0) {
    echo json_encode(["status"=>"error","message"=>"Invalid operator id"]);
    exit;
}

$conn->query("DELETE FROM operators WHERE id=$id");

echo json_encode([
    "status"=>"success",
    "message"=>"Operator deleted successfully"
]);
