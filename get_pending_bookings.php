<?php
header("Content-Type: application/json");
include "db.php";

$sql = "SELECT 
            b.*, 
            u.name as user_name_from_users,
            u.phone as user_phone_from_users,
            e.name as equipment_name, 
            e.category as equipment_category 
        FROM bookings b 
        LEFT JOIN users u ON b.user_id = u.id 
        LEFT JOIN equipment e ON b.equipment_id = e.id 
        WHERE b.status = 'pending' 
        ORDER BY b.booking_date DESC";

$result = $conn->query($sql);

$bookings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Fallback for names if not in booking
        if (empty($row['customer_name'])) $row['customer_name'] = $row['user_name_from_users'];
        if (empty($row['customer_phone'])) $row['customer_phone'] = $row['user_phone_from_users'];
        
        $row['booking_id'] = "BKG" . str_pad($row['id'], 5, "0", STR_PAD_LEFT);
        $bookings[] = $row;
    }
}

echo json_encode([
    "status" => "success", 
    "bookings" => $bookings, 
    "count" => count($bookings)
]);

$conn->close();
?>
