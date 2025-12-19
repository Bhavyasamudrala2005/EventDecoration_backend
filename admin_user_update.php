<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id    = intval($data['id'] ?? 0);
$name  = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');

if ($id <= 0 || $name=='' || $email=='' || $phone=='') {
    echo json_encode([
        "status"=>"error",
        "message"=>"Invalid input"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "UPDATE users SET name=?, email=?, phone=? WHERE id=?"
);
$stmt->bind_param("sssi",$name,$email,$phone,$id);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"User updated successfully"
]);
?>
