<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
include "db.php";

// Android nundi vachina user_id ni teesukovali
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid User ID",
        "bookings" => [],
        "count" => 0
    ]);
    exit;
}

try {
    // Bookings table nundi data tevali, equipment details kosam JOIN cheyali
    $sql = "SELECT 
                b.id,
                b.user_id,
                e.id as equipment_id,
                COALESCE(e.name, 'Unknown Item') as equipment_name,
                COALESCE(e.category, 'General') as equipment_category,
                e.image_url as equipment_image,
                b.quantity,
                b.rental_days,
                b.total_amount,
                b.status,
                b.booking_date,
                b.start_date,
                b.end_date
            FROM bookings b
            LEFT JOIN equipment e ON b.equipment_id = e.id
            WHERE b.user_id = ?
            ORDER BY b.id DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        // Java code ki thaggattu ID formatting
        $bookingIdFormatted = "BKG" . str_pad($row['id'], 4, "0", STR_PAD_LEFT);
        
        $bookings[] = [
            "id" => intval($row['id']),
            "booking_id" => $bookingIdFormatted,
            "equipment_id" => intval($row['equipment_id']),
            "equipment_name" => $row['equipment_name'],
            "equipment_category" => $row['equipment_category'],
            "equipment_image" => $row['equipment_image'],
            "quantity" => intval($row['quantity']),
            "rental_days" => intval($row['rental_days']),
            "total_amount" => floatval($row['total_amount']),
            "status" => $row['status'],
            "booking_date" => $row['booking_date'],
            "start_date" => $row['start_date'],
            "end_date" => $row['end_date']
        ];
    }

    echo json_encode([
        "success" => true,
        "message" => count($bookings) . " bookings found",
        "bookings" => $bookings,
        "count" => count($bookings)
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage(),
        "bookings" => [],
        "count" => 0
    ]);
}

$conn->close();
?>