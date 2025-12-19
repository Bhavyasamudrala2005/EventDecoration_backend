<?php
header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid user ID"
    ]);
    exit;
}

// Use the correct table
$stmt = $conn->prepare("DELETE FROM admins WHERE id=?");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "status" => "success",
            "message" => "Admin deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Admin not found"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete admin: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
