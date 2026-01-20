<?php
// Simple email test with SSL
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer_new/src/Exception.php';
require __DIR__ . '/PHPMailer_new/src/PHPMailer.php';
require __DIR__ . '/PHPMailer_new/src/SMTP.php';

echo "Testing email...\n";

try {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nudralabhavya60@gmail.com';
    $mail->Password = 'yrrs nwbk dvqt kyka';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    
    $mail->setFrom('nudralabhavya60@gmail.com', 'EventEase');
    $mail->addAddress('nudralabhavya60@gmail.com');
    
    $mail->isHTML(true);
    $mail->Subject = 'Test OTP: 123456';
    $mail->Body = '<h1>Test OTP: 123456</h1>';
    
    $mail->send();
    echo "SUCCESS! Email sent!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $mail->ErrorInfo . "\n";
}
?>
