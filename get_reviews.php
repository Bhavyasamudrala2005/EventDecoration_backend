<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

include "db.php";

try {
    $equipment_id = isset($_GET['equipment_id']) ? intval($_GET['equipment_id']) : 0;
    
    if ($equipment_id <= 0) {
        echo json_encode(["success" => false, "message" => "Equipment ID required", "reviews" => [], "average_rating" => 0]);
        exit;
    }
    
    // Get reviews with user names
    $sql = "SELECT r.id, r.user_id, r.rating, r.review_text, r.created_at, 
                   u.name as user_name
            FROM reviews r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.equipment_id = ?
            ORDER BY r.created_at DESC
            LIMIT 50";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $equipment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    $total_rating = 0;
    $count = 0;
    
    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            "id" => intval($row['id']),
            "user_id" => intval($row['user_id']),
            "user_name" => $row['user_name'] ?? "Anonymous",
            "rating" => intval($row['rating']),
            "review_text" => $row['review_text'],
            "created_at" => $row['created_at']
        ];
        $total_rating += intval($row['rating']);
        $count++;
    }
    
    $average_rating = $count > 0 ? round($total_rating / $count, 1) : 0;
    
    echo json_encode([
        "success" => true,
        "equipment_id" => $equipment_id,
        "reviews" => $reviews,
        "review_count" => $count,
        "average_rating" => $average_rating
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage(), "reviews" => [], "average_rating" => 0]);
}

$conn->close();
?>
