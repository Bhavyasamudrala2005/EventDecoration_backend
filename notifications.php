<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id is required"
    ]);
    exit;
}

// Fetch notifications for this user or general notifications (user_id IS NULL)
$sql = "SELECT id, type, message, status, created_at
        FROM notifications
        WHERE user_id = ? OR user_id IS NULL
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}

// Return notifications
echo json_encode([
    "status" => "success",
    "user_id" => $user_id,
    "notifications" => $notifications
]);
?>
