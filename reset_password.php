<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

date_default_timezone_set("Asia/Kolkata");

$data = json_decode(file_get_contents("php://input"), true);

$email = trim($data['login'] ?? '');
$otp = trim($data['otp'] ?? '');
$new_password = trim($data['new_password'] ?? '');
$confirm_password = trim($data['confirm_password'] ?? '');

if(!$email || !$otp || !$new_password || !$confirm_password){
    echo json_encode(["status"=>"error","message"=>"All fields are required"]);
    exit;
}

if($new_password !== $confirm_password){
    echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
    exit;
}

// Check OTP from otp_verification table
$stmt = $conn->prepare("SELECT id, otp_expiry FROM otp_verification WHERE email=? AND otp=?");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["status"=>"error","message"=>"Invalid OTP"]);
    exit;
}

$otp_record = $result->fetch_assoc();

// Check if OTP is expired
$current_time = date("Y-m-d H:i:s");
if($current_time > $otp_record['otp_expiry']){
    echo json_encode(["status"=>"error","message"=>"OTP has expired. Please request a new one."]);
    exit;
}

// Check if user exists with this email
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user_result = $stmt->get_result();

if($user_result->num_rows > 0) {
    // User exists - update their password
    $user = $user_result->fetch_assoc();
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $new_password, $user['id']);
    $stmt->execute();
} else {
    // User doesn't exist - create new user account
    $user_id = "EE" . rand(10000, 99999);
    $name = explode("@", $email)[0]; // Use email prefix as name
    $phone = "";
    
    $stmt = $conn->prepare("INSERT INTO users (user_id, name, email, phone, password, status) VALUES (?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("sssss", $user_id, $name, $email, $phone, $new_password);
    $stmt->execute();
}

// Delete the used OTP
$stmt = $conn->prepare("DELETE FROM otp_verification WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();

echo json_encode([
    "status"=>"success",
    "message"=>"Password reset successfully! You can now login."
]);
?>
