<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$category = trim($data['category'] ?? '');
$type = trim($data['type'] ?? '');
$specifications = trim($data['specifications'] ?? '');
$price = floatval($data['price_per_day'] ?? 0);
$quantity = intval($data['quantity'] ?? 0);
$availability = trim($data['availability'] ?? '');

if ($name === '' || $category === '' `|| $type === '' ||
    $specifications === '' || $price <= 0 || $quantity <= 0 || $availability === '') {
    echo json_encode(["status"=>"error","message"=>"All fields are required"]);
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO equipment (name, category, type, specifications, price_per_day, quantity, availability)
     VALUES (?,?,?,?,?,?,?)"
);

$stmt->bind_param("ssssdis",
    $name, $category, $type, $specifications, $price, $quantity, $availability
);

if ($stmt->execute()) {
    echo json_encode(["status"=>"success","message"=>"Equipment added successfully"]);
} else {
    echo json_encode(["status"=>"error","message"=>"Insert failed"]);
}
