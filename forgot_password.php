<?php
header("Content-Type: application/json");
include "db.php";

date_default_timezone_set("Asia/Kolkata"); // important!

$data = json_decode(file_get_contents("php://input"), true);
$login = trim($data['login'] ?? '');

if(!$login){
    echo json_encode(["status"=>"error","message"=>"Email or phone required"]);
    exit;
}

// Check user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=? OR phone=?");
$stmt->bind_param("ss", $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["status"=>"error","message"=>"User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// Generate OTP and expiry
$otp = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

// Update database
$stmt = $conn->prepare("UPDATE users SET otp=?, otp_expiry=? WHERE id=?");
$stmt->bind_param("ssi", $otp, $expiry, $user['id']);
$stmt->execute();

// For testing, return OTP in response
echo json_encode([
    "status"=>"success",
    "message"=>"OTP sent successfully",
    "otp"=>$otp,
    "expiry"=>$expiry
]);
?>
