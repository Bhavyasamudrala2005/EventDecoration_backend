<?php
header("Content-Type: application/json");
include "db.php";

date_default_timezone_set("Asia/Kolkata"); // important!

$data = json_decode(file_get_contents("php://input"), true);

$login = trim($data['login'] ?? '');
$otp = trim($data['otp'] ?? '');
$new_password = trim($data['new_password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

if(!$login || !$otp || !$new_password || !$confirm_password){
    echo json_encode(["status"=>"error","message"=>"All fields are required"]);
    exit;
}

if($new_password !== $confirm_password){
    echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
    exit;
}

// Check OTP
$stmt = $conn->prepare("SELECT id, otp_expiry FROM users WHERE (email=? OR phone=?) AND otp=?");
$stmt->bind_param("sss", $login, $login, $otp);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["status"=>"error","message"=>"Invalid OTP or expired"]);
    exit;
}

$user = $result->fetch_assoc();

// Check if OTP is expired
$current_time = date("Y-m-d H:i:s");
if($current_time > $user['otp_expiry']){
    echo json_encode(["status"=>"error","message"=>"Invalid OTP or expired"]);
    exit;
}

// Update password and clear OTP
$stmt = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expiry=NULL WHERE id=?");
$stmt->bind_param("si", $new_password, $user['id']);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Password reset successfully"
]);
?>
