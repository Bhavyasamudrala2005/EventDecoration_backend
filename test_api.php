<?php
// Simple test file to verify API is working
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "status" => "success",
    "message" => "API is working correctly!",
    "server_time" => date("Y-m-d H:i:s"),
    "php_version" => phpversion()
]);
?>
