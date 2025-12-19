<?php
header("Content-Type: application/json");
include "db.php"; // Your database connection

// Get POSTed JSON data
$data = json_decode(file_get_contents("php://input"), true);

$name = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$email = trim($data['email'] ?? '');
$password = trim($data['password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

// Validation
if(!$name || !$phone || !$email || !$password || !$confirm_password){
    echo json_encode(["status"=>"error","message"=>"All fields are required"]);
    exit;
}

if($password !== $confirm_password){
    echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
    exit;
}

// Check if email or phone already exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=?");
$stmt->bind_param("ss", $email, $phone);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    echo json_encode(["status"=>"error","message"=>"Email or Phone already registered"]);
    exit;
}

// Generate user_id
$user_id = "EE" . rand(10000,99999);

// Store password as plain text (not hashed)
$plain_password = $password;

// Insert user
$stmt = $conn->prepare("INSERT INTO users (user_id, name, phone, email, password) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssss", $user_id, $name, $phone, $email, $plain_password);

if($stmt->execute()){
    echo json_encode([
        "status"=>"success",
        "message"=>"Registered successfully",
        "user_id"=>$user_id,
        "password"=>$plain_password // return password in response if needed
    ]);
} else {
    echo json_encode([
        "status"=>"error",
        "message"=>"Registration failed"
    ]);
}
?>
