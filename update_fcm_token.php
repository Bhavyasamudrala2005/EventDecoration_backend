<?php
header("Content-Type: application/json");
include "db.php";

// Support both form-urlencoded and JSON input
$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if (strpos($contentType, 'application/json') !== false) {
    // JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $fcm_token = trim($data['fcm_token'] ?? '');
} else {
    // Form-urlencoded input (from Android app)
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $fcm_token = trim($_POST['fcm_token'] ?? '');
}

if ($user_id <= 0 || empty($fcm_token)) {
    echo json_encode([
        "status" => "error",
        "message" => "User ID and FCM token are required"
    ]);
    exit;
}

try {
    // Check if fcm_token column exists in users table
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'fcm_token'");
    
    if ($check_column->num_rows == 0) {
        // Add column if it doesn't exist
        $conn->query("ALTER TABLE users ADD COLUMN fcm_token VARCHAR(255) DEFAULT NULL");
    }
    
    // Update user's FCM token
    $stmt = $conn->prepare("UPDATE users SET fcm_token = ? WHERE id = ?");
    $stmt->bind_param("si", $fcm_token, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode([
                "status" => "success",
                "message" => "FCM token updated successfully"
            ]);
        } else {
            echo json_encode([
                "status" => "success",
                "message" => "Token already up to date or user not found"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update token: " . $conn->error
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
