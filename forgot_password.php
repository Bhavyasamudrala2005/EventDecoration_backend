<?php
// Ensure only JSON is returned, even on errors
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Catch all errors and return as JSON
set_error_handler(function($errno, $errstr) {
    echo json_encode(["status" => "error", "message" => "Server error: " . $errstr]);
    exit;
});

try {
    include "db.php";
    
    date_default_timezone_set("Asia/Kolkata");
    
    // Get input
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    
    if ($data === null) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
        exit;
    }
    
    $email = trim($data['login'] ?? '');
    
    if (empty($email)) {
        echo json_encode(["status" => "error", "message" => "Email is required"]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }
    
    // Generate 6-digit OTP
    $otp = strval(rand(100000, 999999));
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    
    // Create table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS otp_verification (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        otp VARCHAR(6) NOT NULL,
        otp_expiry DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_email (email)
    )");
    
    // Save OTP to database
    $stmt = $conn->prepare("INSERT INTO otp_verification (email, otp, otp_expiry) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE otp=VALUES(otp), otp_expiry=VALUES(otp_expiry)");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("sss", $email, $otp, $expiry);
    
    if (!$stmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Failed to save OTP"]);
        exit;
    }
    
    // Try to send email using PHPMailer if available
    $email_sent = false;
    $phpmailer_path = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    $phpmailer_new_path = __DIR__ . '/PHPMailer_new/src/PHPMailer.php';
    
    // Check for PHPMailer in different locations
    $mailer_path = null;
    if (file_exists($phpmailer_path)) {
        $mailer_path = $phpmailer_path;
        $base_path = __DIR__ . '/PHPMailer/src/';
    } elseif (file_exists($phpmailer_new_path)) {
        $mailer_path = $phpmailer_new_path;
        $base_path = __DIR__ . '/PHPMailer_new/src/';
    }
    
    if ($mailer_path !== null) {
        require_once $base_path . 'Exception.php';
        require_once $base_path . 'PHPMailer.php';
        require_once $base_path . 'SMTP.php';
        
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'samudralabhavya60@gmail.com';
            $mail->Password = 'yrrs nwbk dvqt kyka'; // App password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            $mail->setFrom('samudralabhavya60@gmail.com', 'EventEase');
            $mail->addAddress($email);
            
            $mail->isHTML(true);
            $mail->Subject = 'EventEase Password Reset OTP: ' . $otp;
            $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9f9f9;">
                <div style="background: linear-gradient(135deg, #6200EE, #9C27B0); padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                    <h1 style="color: white; margin: 0;">EventEase</h1>
                </div>
                <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px;">
                    <h2 style="color: #333;">Password Reset OTP</h2>
                    <p style="color: #666;">You requested to reset your password. Use the OTP below:</p>
                    <div style="background-color: #f5f5f5; padding: 20px; text-align: center; margin: 20px 0; border-radius: 8px;">
                        <h1 style="color: #6200EE; letter-spacing: 8px; margin: 0; font-size: 36px;">' . $otp . '</h1>
                    </div>
                    <p style="color: #666;">This OTP is valid for <strong>10 minutes</strong>.</p>
                    <p style="color: #999; font-size: 12px;">If you did not request this, please ignore this email.</p>
                    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
                    <p style="color: #999; font-size: 12px; text-align: center;">&copy; 2026 EventEase. All rights reserved.</p>
                </div>
            </div>';
            $mail->AltBody = 'Your EventEase OTP is: ' . $otp . '. Valid for 10 minutes.';
            
            $mail->send();
            $email_sent = true;
        } catch (Exception $e) {
            // Email failed, log but continue
            error_log("Email sending failed: " . $e->getMessage());
        }
    }
    
    // Return success with OTP
    echo json_encode([
        "status" => "success",
        "message" => $email_sent ? "OTP sent to your email!" : "OTP generated! Your OTP is: " . $otp,
        "otp" => $otp,
        "expiry" => $expiry,
        "email_sent" => $email_sent
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Server error: " . $e->getMessage()
    ]);
}
?>
