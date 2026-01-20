<?php
// Test script to debug email sending
header("Content-Type: text/html");

echo "<h2>Email Sending Test</h2>";

// Check if PHPMailer exists
$phpmailer_path = __DIR__ . '/PHPMailer_new/src/PHPMailer.php';
echo "<p>PHPMailer path: $phpmailer_path</p>";
echo "<p>PHPMailer exists: " . (file_exists($phpmailer_path) ? "YES" : "NO") . "</p>";

if(!file_exists($phpmailer_path)) {
    echo "<p style='color:red'>ERROR: PHPMailer not found!</p>";
    exit;
}

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer_new/src/Exception.php';
require __DIR__ . '/PHPMailer_new/src/PHPMailer.php';
require __DIR__ . '/PHPMailer_new/src/SMTP.php';

echo "<p>PHPMailer loaded successfully!</p>";

// Test email
$test_email = "nudralabhavya60@gmail.com"; // Send test to yourself
$otp = rand(100000, 999999);

try {
    $mail = new PHPMailer(true);
    
    // Enable debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = 'html';
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nudralabhavya60@gmail.com';
    $mail->Password = 'yrrsnwbkdvqtkyka';  // Without spaces
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('nudralabhavya60@gmail.com', 'EventEase');
    $mail->addAddress($test_email);
    
    $mail->isHTML(true);
    $mail->Subject = 'Test OTP: ' . $otp;
    $mail->Body = '<h1>Test OTP: ' . $otp . '</h1>';
    
    $mail->send();
    echo "<p style='color:green'>SUCCESS! Email sent to $test_email</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>ERROR: " . $mail->ErrorInfo . "</p>";
}
?>
