<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

require_once "db.php";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!is_array($data)) {
    echo json_encode(["status"=>"error","message"=>"Invalid JSON"]);
    exit;
}

$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$confirm = trim($data['confirm_password'] ?? '');

if ($name=='' || $phone=='' || $email=='' || $password=='' || $confirm=='') {
    echo json_encode(["status"=>"error","message"=>"All fields required"]);
    exit;
}

if ($password !== $confirm) {
    echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR phone=?");
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    echo json_encode(["status"=>"error","message"=>"Email or phone already exists"]);
    exit;
}

$user_id = "EE".rand(10000,99999);

$stmt = $conn->prepare(
    "INSERT INTO users (user_id,name,phone,email,password)
     VALUES (?,?,?,?,?)"
);

$stmt->bind_param("sssss", $user_id, $name, $phone, $email, $password);

if ($stmt->execute()) {
    echo json_encode([
        "status"=>"success",
        "message"=>"Registered successfully"
    ]);
} else {
    echo json_encode([
        "status"=>"error",
        "message"=>"Insert failed"
    ]);
}
