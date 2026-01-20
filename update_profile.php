<?php
header("Content-Type: application/json");

// Enable errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

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

$user_id = intval($data["user_id"] ?? 0);
$name = trim($data["name"] ?? "");
$email = strtolower(trim($data["email"] ?? ""));
$phone = trim($data["phone"] ?? "");

// Validate required fields
if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid user ID"
    ]);
    exit;
}

if ($name === "" || $email === "" || $phone === "") {
    echo json_encode([
        "status" => "error",
        "message" => "Name, email, and phone are required"
    ]);
    exit;
}

// Check if email is already used by another user
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$checkStmt->bind_param("si", $email, $user_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Email is already used by another account"
    ]);
    exit;
}

// Check if phone is already used by another user
$checkStmt = $conn->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
$checkStmt->bind_param("si", $phone, $user_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone number is already used by another account"
    ]);
    exit;
}

// Update user profile
$stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
$stmt->bind_param("sssi", $name, $email, $phone, $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Profile updated successfully"
        ]);
    } else {
        // No rows affected - user might not exist or data is the same
        $verifyStmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $verifyStmt->bind_param("i", $user_id);
        $verifyStmt->execute();
        $verifyResult = $verifyStmt->get_result();
        
        if ($verifyResult->num_rows > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "Profile updated successfully"
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "User not found"
            ]);
        }
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update profile: " . $stmt->error
    ]);
}

$conn->close();
?>
