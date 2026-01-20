<?php
header("Content-Type: application/json");
include "db.php";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;

if ($booking_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Booking ID is required"
    ]);
    exit;
}

try {
    // Check if booking exists and is cancellable (not already completed or cancelled)
    $checkSql = "SELECT id, status FROM bookings WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $booking_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Booking not found"
        ]);
        exit;
    }
    
    $booking = $result->fetch_assoc();
    
    if ($booking['status'] == 'cancelled') {
        echo json_encode([
            "status" => "error",
            "message" => "Booking is already cancelled"
        ]);
        exit;
    }
    
    if ($booking['status'] == 'completed') {
        echo json_encode([
            "status" => "error",
            "message" => "Cannot cancel a completed booking"
        ]);
        exit;
    }
    
    // Cancel the booking
    $updateSql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $booking_id);
    
    if ($updateStmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Booking cancelled successfully",
            "booking_id" => $booking_id
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to cancel booking"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
