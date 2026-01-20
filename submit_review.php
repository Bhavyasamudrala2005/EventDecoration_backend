<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $user_id = intval($data['user_id'] ?? 0);
    $equipment_id = intval($data['equipment_id'] ?? 0);
    $booking_id = intval($data['booking_id'] ?? 0);
    $rating = intval($data['rating'] ?? 0);
    $review_text = trim($data['review_text'] ?? '');
    
    // Validation
    if ($user_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid user ID"]);
        exit;
    }
    
    if ($equipment_id <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid equipment ID"]);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) {
        echo json_encode(["success" => false, "message" => "Rating must be between 1 and 5"]);
        exit;
    }
    
    // Check if user already reviewed this booking
    $check_sql = "SELECT id FROM reviews WHERE user_id = ? AND booking_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $booking_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "You have already reviewed this booking"]);
        exit;
    }
    
    // Insert review
    $sql = "INSERT INTO reviews (user_id, equipment_id, booking_id, rating, review_text) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $user_id, $equipment_id, $booking_id, $rating, $review_text);
    
    if ($stmt->execute()) {
        $review_id = $conn->insert_id;
        
        echo json_encode([
            "success" => true,
            "message" => "Review submitted successfully!",
            "review_id" => $review_id
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to submit review: " . $stmt->error]);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$conn->close();
?>
