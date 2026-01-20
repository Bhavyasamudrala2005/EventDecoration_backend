<?php
header("Content-Type: application/json");
include "db.php";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id is required"
    ]);
    exit;
}

try {
    // Count unread notifications for this user or general notifications (user_id IS NULL)
    $sql = "SELECT COUNT(*) as unread_count 
            FROM notifications 
            WHERE (user_id = ? OR user_id IS NULL) AND status = 'unread'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    echo json_encode([
        "status" => "success",
        "unread_count" => intval($row['unread_count'])
    ]);
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
