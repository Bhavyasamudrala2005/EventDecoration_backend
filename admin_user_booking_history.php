<?php
header("Content-Type: application/json");
include "db.php";

$user_id = intval($_GET['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode([
        "status"=>"error",
        "message"=>"user_id required"
    ]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, total_amount, status, booking_date
     FROM bookings WHERE user_id=?"
);
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode([
    "status"=>"success",
    "user_id"=>$user_id,
    "bookings"=>$bookings
]);
?>
