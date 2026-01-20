<?php
header("Content-Type: application/json");
include "db.php";

// Get booking ID from query parameter
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

if ($booking_id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Booking ID is required"
    ]);
    exit;
}

try {
    // Fetch booking details with user and equipment info
    $sql = "SELECT 
                b.id,
                b.user_id,
                u.name as user_name,
                u.email as user_email,
                u.phone as user_phone,
                b.equipment_id,
                e.name as equipment_name,
                e.category as equipment_category,
                e.image_url as equipment_image,
                e.price_per_day,
                b.quantity,
                b.rental_days,
                b.time_slot,
                b.start_date,
                b.end_date,
                b.total_amount,
                b.status,
                b.booking_date
            FROM bookings b
            LEFT JOIN equipment e ON b.equipment_id = e.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Use actual start_date/end_date if available, otherwise calculate from booking_date
        if (!empty($row['start_date'])) {
            $startDate = date('d/m/Y', strtotime($row['start_date']));
        } else {
            $startDate = date('d/m/Y', strtotime($row['booking_date']));
        }
        
        if (!empty($row['end_date'])) {
            $endDate = date('d/m/Y', strtotime($row['end_date']));
        } else {
            $endDate = date('d/m/Y', strtotime($row['booking_date'] . ' + ' . $row['rental_days'] . ' days'));
        }
        
        $bookingIdFormatted = "BKG" . str_pad($row['id'], 10, "0", STR_PAD_LEFT);
        
        echo json_encode([
            "status" => "success",
            "booking" => [
                "id" => intval($row['id']),
                "booking_id" => $bookingIdFormatted,
                "user_id" => intval($row['user_id']),
                "user_name" => $row['user_name'] ?? "Guest",
                "user_email" => $row['user_email'] ?? "N/A",
                "user_phone" => $row['user_phone'] ?? "N/A",
                "equipment_id" => intval($row['equipment_id']),
                "equipment_name" => $row['equipment_name'] ?? "Equipment",
                "equipment_category" => $row['equipment_category'] ?? "General",
                "equipment_image" => $row['equipment_image'] ?? "",
                "price_per_day" => floatval($row['price_per_day']),
                "quantity" => intval($row['quantity']),
                "rental_days" => intval($row['rental_days']),
                "time_slot" => $row['time_slot'] ?? "Full Day",
                "total_amount" => floatval($row['total_amount']),
                "status" => $row['status'],
                "start_date" => $startDate,
                "end_date" => $endDate,
                "booking_date" => $row['booking_date']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Booking not found"
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch booking details: " . $e->getMessage()
    ]);
}
?>
