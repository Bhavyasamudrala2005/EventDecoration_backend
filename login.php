<?php
header("Content-Type: application/json");

// TEMPORARILY ENABLE ERRORS FOR DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";

// Read raw input
$rawInput = file_get_contents("php://input");

// Decode JSON safely
$data = json_decode($rawInput, true);

if (!is_array($data)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON input"
    ]);
    exit;
}

$login = strtolower(trim($data["login"] ?? ""));
$password = trim($data["password"] ?? "");

if ($login === "" || $password === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Login and password required"
    ]);
    exit;
}

// SQL query
$stmt = $conn->prepare(
    "SELECT * FROM users WHERE LOWER(email)=? OR phone=?"
);
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email or Phone not registered"
    ]);
    exit;
}

$user = $result->fetch_assoc();

// PLAIN TEXT PASSWORD CHECK (AS PER YOUR DB)
if ($password !== trim($user['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Incorrect password"
    ]);
    exit;
}

// SUCCESS RESPONSE
echo json_encode([
    "status" => "success",
    "user" => [
        "id" => $user['id'],
        "user_id" => $user['user_id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "phone" => $user['phone']
    ]
]);
