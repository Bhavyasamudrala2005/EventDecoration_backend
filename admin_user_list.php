<?php
header("Content-Type: application/json");
include "db.php";

$result = $conn->query("SELECT id, name, email, phone, status FROM users");
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode([
    "status" => "success",
    "users" => $users
]);
?>
