<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$login = strtolower(trim($data["login"] ?? ""));
$password = trim($data["password"] ?? "");

if ($login === "" || $password === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Email or Phone not registered"
    ]);
    exit;
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=?");
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email or Phone not registered"
    ]);
    exit;
}

$user = $result->fetch_assoc();

// ✅ PLAIN PASSWORD CHECK
if ($password !== $user['password']) {
    echo json_encode([
        "status" => "error",
        "message" => "Incorrect password"
    ]);
    exit;
}

echo json_encode([
    "status" => "success",
    "user" => $user
]);
?>
