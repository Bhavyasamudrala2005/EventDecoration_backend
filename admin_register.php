<?php
header("Content-Type: application/json");
include "db.php"; // Your database connection

// Get POSTed JSON data
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$name = trim($data['name'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

// Validation
if(!$username || !$password || !$name){
    echo json_encode(["status"=>"error","message"=>"All fields are required"]);
    exit;
}

if($password !== $confirm_password){
    echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
    exit;
}

// Check if email or phone already exists
$stmt = $conn->prepare("SELECT * FROM admins WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    echo json_encode(["status"=>"error","message"=>"Email or Phone already registered"]);
    exit;
}



// Store password as plain text (not hashed)
$plain_password = $password;

// Insert user
$stmt = $conn->prepare("INSERT INTO admins (username, password, name) VALUES (?,?,?)");
$stmt->bind_param("sss", $username, $password, $name);

if($stmt->execute()){
    echo json_encode([
        "status"=>"success",
        "message"=>"Registered successfully",
        "password"=>$plain_password // return password in response if needed
    ]);
} else {
    echo json_encode([
        "status"=>"error",
        "message"=>"Registration failed"
    ]);
}
?>
