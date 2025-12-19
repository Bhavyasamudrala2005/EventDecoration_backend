<?php
header("Content-Type: application/json");
include "db.php";

$result = $conn->query("SELECT * FROM operators ORDER BY id DESC");

$operators = [];
while ($row = $result->fetch_assoc()) {
    $operators[] = $row;
}

echo json_encode([
    "status"=>"success",
    "operators"=>$operators
]);
