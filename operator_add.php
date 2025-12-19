<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name  = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');

if ($name == '' || $phone == '') {
    echo json_encode([
        "status"=>"error",
        "message"=>"Name and phone are required"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO operators (name, phone) VALUES (?, ?)"
);
$stmt->bind_param("ss", $name, $phone);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Operator added successfully",
    "operator_id"=>$stmt->insert_id
]);
?>
