<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

$data = json_decode(file_get_contents("php://input"), true);

// Required inputs
$user_id      = intval($data['user_id'] ?? 0);
$equipment_id = intval($data['equipment_id'] ?? 0);
$rating       = intval($data['rating'] ?? 0);
$feedback     = trim($data['feedback'] ?? "");

// Validate inputs for submission
if ($user_id <= 0 || $equipment_id <= 0 || $rating < 1 || $rating > 5) {
    echo json_encode([
        "status" => "error",
        "message" => "user_id, equipment_id and rating (1-5) are required"
    ]);
    exit;
}

// Insert review
$stmt = $conn->prepare("INSERT INTO reviews (user_id, equipment_id, rating, feedback) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $equipment_id, $rating, $feedback);

if ($stmt->execute()) {
    // Fetch all reviews for this equipment
    $stmt2 = $conn->prepare("SELECT r.id, r.user_id, u.name AS user_name, r.rating, r.feedback, r.review_date 
                             FROM reviews r
                             JOIN users u ON r.user_id = u.id
                             WHERE r.equipment_id = ? 
                             ORDER BY r.review_date DESC");
    $stmt2->bind_param("i", $equipment_id);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Review submitted successfully",
        "reviews" => $reviews
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to submit review"
    ]);
}
?>
