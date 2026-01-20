<?php
header("Content-Type: application/json");
include "db.php";

// Allow resetting auto-increment to 1
$sql = "TRUNCATE TABLE bookings";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "success" => true,
        "message" => "All bookings have been deleted and ID reset to 1."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error deleting bookings: " . $conn->error
    ]);
}

$conn->close();
?>
