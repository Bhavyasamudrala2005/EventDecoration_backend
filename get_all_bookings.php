<?php
header("Content-Type: application/json");
include "db.php";

// Get filter parameter (optional)
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : 'all';

try {
    // Check if customer columns exist
    $has_customer_cols = false;
    $check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'customer_name'");
    if ($check && $check->num_rows > 0) {
        $has_customer_cols = true;
    }
    
    // Build SQL based on filter - include all relevant columns
    if ($has_customer_cols) {
        $sql = "SELECT 
                    b.id,
                    b.user_id,
                    u.name as user_name,
                    u.email as user_email,
                    u.phone as user_phone,
                    b.equipment_id,
                    e.name as equipment_name,
                    e.category as equipment_category,
                    b.quantity,
                    b.rental_days,
                    b.total_amount,
                    b.status,
                    b.booking_date as created_at,
                    b.start_date,
                    b.end_date,
                    b.time_slot,
                    b.customer_name,
                    b.customer_phone,
                    b.delivery_address
                FROM bookings b
                LEFT JOIN equipment e ON b.equipment_id = e.id
                LEFT JOIN users u ON b.user_id = u.id";
    } else {
        $sql = "SELECT 
                    b.id,
                    b.user_id,
                    u.name as user_name,
                    u.email as user_email,
                    u.phone as user_phone,
                    b.equipment_id,
                    e.name as equipment_name,
                    e.category as equipment_category,
                    b.quantity,
                    b.rental_days,
                    b.total_amount,
                    b.status,
                    b.booking_date as created_at
                FROM bookings b
                LEFT JOIN equipment e ON b.equipment_id = e.id
                LEFT JOIN users u ON b.user_id = u.id";
    }
    
    if ($status_filter !== 'all') {
        $sql .= " WHERE b.status = ?";
    }
    
    $sql .= " ORDER BY b.booking_date DESC";
    
    if ($status_filter !== 'all') {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $status_filter);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $bookings = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookingIdFormatted = "BKG" . str_pad($row['id'], 10, "0", STR_PAD_LEFT);
            
            // Use actual dates if available, else calculate from booking_date
            if ($has_customer_cols && !empty($row['start_date'])) {
                $startDate = date('d/m/Y', strtotime($row['start_date']));
                $endDate = !empty($row['end_date']) ? date('d/m/Y', strtotime($row['end_date'])) : date('d/m/Y', strtotime($row['start_date'] . ' + ' . $row['rental_days'] . ' days'));
            } else {
                $startDate = date('d/m/Y', strtotime($row['created_at']));
                $endDate = date('d/m/Y', strtotime($row['created_at'] . ' + ' . $row['rental_days'] . ' days'));
            }
            
            // Use customer_name from booking if available, else fall back to user name
            $customerName = ($has_customer_cols && !empty($row['customer_name'])) ? $row['customer_name'] : ($row['user_name'] ?? "Unknown User");
            $customerPhone = ($has_customer_cols && !empty($row['customer_phone'])) ? $row['customer_phone'] : ($row['user_phone'] ?? "N/A");
            $deliveryAddress = ($has_customer_cols && !empty($row['delivery_address'])) ? $row['delivery_address'] : "N/A";
            
            $bookings[] = [
                "id" => intval($row['id']),
                "booking_id" => $bookingIdFormatted,
                "user_id" => intval($row['user_id']),
                "user_name" => $customerName,
                "user_email" => $row['user_email'] ?? "N/A",
                "user_phone" => $customerPhone,
                "equipment_id" => intval($row['equipment_id']),
                "equipment_name" => $row['equipment_name'] ?? "Unknown Equipment",
                "equipment_category" => $row['equipment_category'] ?? "General",
                "quantity" => intval($row['quantity']),
                "rental_days" => intval($row['rental_days']),
                "total_amount" => floatval($row['total_amount']),
                "status" => $row['status'],
                "start_date" => $startDate,
                "end_date" => $endDate,
                "time_slot" => $has_customer_cols ? ($row['time_slot'] ?? "Full Day") : "Full Day",
                "delivery_address" => $deliveryAddress,
                "created_at" => $row['created_at']
            ];
        }
    }
    
    echo json_encode([
        "status" => "success",
        "filter" => $status_filter,
        "count" => count($bookings),
        "bookings" => $bookings
    ]);

} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to fetch bookings: " . $e->getMessage()
    ]);
}
?>
