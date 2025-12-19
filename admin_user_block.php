<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$user_id = intval($data['user_id'] ?? 0);
$status  = $data['status'] ?? '';

if ($user_id <= 0 || !in_array($status,['active','blocked'])) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid input"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE users SET status=? WHERE id=?"
);
$stmt->bind_param("si",$status,$user_id);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"User status updated"
]);
?>
