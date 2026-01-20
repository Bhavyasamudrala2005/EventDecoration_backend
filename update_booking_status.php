<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;
$new_status = isset($data['status']) ? strtolower(trim($data['status'])) : '';
$message = isset($data['message']) ? trim($data['message']) : '';

if ($booking_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Booking ID is required"
    ]);
    exit;
}

// Valid status values (expanded to include more common statuses)
$valid_statuses = [
    'pending', 
    'approved', 
    'accepted',
    'preparing', 
    'ready',
    'out_for_delivery', 
    'delivered',
    'completed', 
    'cancelled',
    'rejected',
    'in_progress',
    'out_of_stock'
];

// Normalize status
$new_status = strtolower($new_status);

if (empty($new_status) || !in_array($new_status, $valid_statuses)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid status. Must be one of: " . implode(", ", $valid_statuses)
    ]);
    exit;
}

try {
    // Check if booking exists and get user_id
    $checkSql = "SELECT id, user_id, status FROM bookings WHERE id = ?";
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
    $old_status = $booking['status'];
    $user_id = $booking['user_id'];
    
    // Update the booking status
    $updateSql = "UPDATE bookings SET status = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $new_status, $booking_id);
    
    if ($updateStmt->execute()) {
        // Create notification for the user
        $notification_message = $message;
        if (empty($notification_message)) {
            // Default notification message based on status
            switch ($new_status) {
                case 'approved':
                case 'accepted':
                    $notification_message = "Your booking #$booking_id has been approved!";
                    break;
                case 'preparing':
                    $notification_message = "Your order #$booking_id is being prepared.";
                    break;
                case 'ready':
                    $notification_message = "Your order #$booking_id is ready for delivery.";
                    break;
                case 'out_for_delivery':
                    $notification_message = "Your order #$booking_id is out for delivery!";
                    break;
                case 'delivered':
                case 'completed':
                    $notification_message = "Your booking #$booking_id has been delivered. Thank you!";
                    break;
                case 'cancelled':
                case 'rejected':
                    $notification_message = "Your booking #$booking_id has been cancelled.";
                    break;
                case 'out_of_stock':
                    $notification_message = "Item for booking #$booking_id is currently out of stock. We'll notify you when available.";
                    break;
                default:
                    $notification_message = "Your booking #$booking_id status has been updated to $new_status.";
            }
        }
        
        // Insert notification for user
        $notifSql = "INSERT INTO notifications (user_id, type, message, status) VALUES (?, 'booking', ?, 'unread')";
        $notifStmt = $conn->prepare($notifSql);
        $notifStmt->bind_param("is", $user_id, $notification_message);
        $notifStmt->execute();
        
        echo json_encode([
            "status" => "success",
            "message" => "Status updated from '$old_status' to '$new_status'",
            "booking_id" => $booking_id,
            "old_status" => $old_status,
            "new_status" => $new_status,
            "notification_sent" => true
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to update status"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
