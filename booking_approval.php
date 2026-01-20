<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$booking_id = intval($data['booking_id'] ?? 0);
$action = strtolower($data['action'] ?? '');

if ($booking_id <= 0 || !in_array($action, ['accept','reject'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid booking_id or action"
    ]);
    exit;
}

/* Fetch booking */
$stmt = $conn->prepare(
    "SELECT user_id, equipment_id, quantity, status
     FROM bookings WHERE id = ?"
);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode([
        "status" => "error",
        "message" => "Booking not found"
    ]);
    exit;
}

/* ACCEPT BOOKING */
if ($action === 'accept') {

    if ($booking['status'] !== 'pending') {
        echo json_encode([
            "status" => "error",
            "message" => "Booking already processed"
        ]);
        exit;
    }

    // Reduce equipment quantity
    $updateEquip = $conn->prepare(
        "UPDATE equipment 
         SET quantity = quantity - ? 
         WHERE id = ? AND quantity >= ?"
    );
    $updateEquip->bind_param(
        "iii",
        $booking['quantity'],
        $booking['equipment_id'],
        $booking['quantity']
    );

    if (!$updateEquip->execute() || $updateEquip->affected_rows == 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Insufficient equipment quantity"
        ]);
        exit;
    }

    // Update booking status
    $stmt = $conn->prepare(
        "UPDATE bookings SET status = 'accepted' WHERE id = ?"
    );
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // Notify user - with proper columns for notifications table
    $msg = "🎉 Great news! Your booking #$booking_id has been APPROVED. We will start preparing your items.";
    $notifType = "booking";
    $notifStatus = "unread";
    
    $stmt = $conn->prepare(
        "INSERT INTO notifications (user_id, type, message, status) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $booking['user_id'], $notifType, $msg, $notifStatus);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Booking accepted successfully",
        "notification_sent" => true
    ]);
    exit;
}

/* REJECT BOOKING */
if ($action === 'reject') {

    $stmt = $conn->prepare(
        "UPDATE bookings SET status = 'rejected' WHERE id = ?"
    );
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    // Notify user - with proper columns for notifications table
    $msg = "We're sorry, your booking #$booking_id has been declined. Please contact support for more information.";
    $notifType = "booking";
    $notifStatus = "unread";
    
    $stmt = $conn->prepare(
        "INSERT INTO notifications (user_id, type, message, status) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $booking['user_id'], $notifType, $msg, $notifStatus);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Booking rejected successfully",
        "notification_sent" => true
    ]);
}
?>
