<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$id    = intval($data['id'] ?? 0);
$name  = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');

if ($id <= 0 || $name === '' || $phone === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input"
    ]);
    exit;
}

/* Check operator exists */
$check = $conn->prepare("SELECT id FROM operators WHERE id=?");
$check->bind_param("i",$id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    echo json_encode([
        "status"=>"error",
        "message"=>"Operator not found"
    ]);
    exit;
}

/* Update operator */
$stmt = $conn->prepare(
    "UPDATE operators SET name=?, phone=? WHERE id=?"
);
$stmt->bind_param("ssi",$name,$phone,$id);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Operator updated successfully"
]);
?>
