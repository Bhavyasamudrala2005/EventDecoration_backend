<?php
// ============================================
// DEBUG: Check Equipment and Bookings Link
// Run in browser to see if equipment details are linked properly
// ============================================

header("Content-Type: application/json");
include "db.php";

$result = [];

// Check equipment table
$equip_check = $conn->query("SELECT id, name, category FROM equipment ORDER BY id LIMIT 20");
$equipment = [];
if ($equip_check) {
    while ($row = $equip_check->fetch_assoc()) {
        $equipment[] = $row;
    }
}
$result['equipment_count'] = count($equipment);
$result['equipment_samples'] = $equipment;

// Check bookings with equipment names
$booking_check = $conn->query("
    SELECT b.id, b.equipment_id, e.name as equipment_name, b.customer_name, b.status
    FROM bookings b
    LEFT JOIN equipment e ON b.equipment_id = e.id
    ORDER BY b.id DESC
    LIMIT 10
");
$bookings = [];
if ($booking_check) {
    while ($row = $booking_check->fetch_assoc()) {
        $bookings[] = $row;
    }
}
$result['bookings_count'] = count($bookings);
$result['bookings_with_equipment'] = $bookings;

// Check if equipment_id in bookings exists in equipment table
$orphan_check = $conn->query("
    SELECT DISTINCT b.equipment_id 
    FROM bookings b 
    LEFT JOIN equipment e ON b.equipment_id = e.id 
    WHERE e.id IS NULL
");
$orphans = [];
if ($orphan_check) {
    while ($row = $orphan_check->fetch_assoc()) {
        $orphans[] = $row['equipment_id'];
    }
}
$result['orphan_equipment_ids'] = $orphans;
$result['message'] = count($orphans) > 0 
    ? "WARNING: These equipment_ids in bookings don't exist in equipment table: " . implode(",", $orphans)
    : "OK: All bookings have valid equipment references";

echo json_encode($result, JSON_PRETTY_PRINT);
$conn->close();
?>
