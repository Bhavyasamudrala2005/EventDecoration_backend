<?php
/**
 * Submit Support Request API
 * Receives support requests from users and stores them in the database
 */

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Include database connection
include_once 'db.php';

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->user_id) || !isset($data->category) || !isset($data->message)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields (user_id, category, message)"
    ]);
    exit();
}

// Sanitize input
$user_id = intval($data->user_id);
$user_name = isset($data->user_name) ? mysqli_real_escape_string($conn, $data->user_name) : 'Guest User';
$user_email = isset($data->user_email) ? mysqli_real_escape_string($conn, $data->user_email) : '';
$category = mysqli_real_escape_string($conn, $data->category);
$message = mysqli_real_escape_string($conn, $data->message);

// Check if support_requests table exists, if not create it
$create_table_sql = "CREATE TABLE IF NOT EXISTS support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_email VARCHAR(255),
    category VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    admin_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

mysqli_query($conn, $create_table_sql);

// Insert support request
$sql = "INSERT INTO support_requests (user_id, user_name, user_email, category, message) 
        VALUES ('$user_id', '$user_name', '$user_email', '$category', '$message')";

if (mysqli_query($conn, $sql)) {
    $ticket_id = mysqli_insert_id($conn);
    
    // Also create a notification for the admin
    $notification_sql = "INSERT INTO notifications (user_id, type, message, status) 
                         VALUES (0, 'support', 'New support request #$ticket_id from $user_name: $category', 'unread')";
    mysqli_query($conn, $notification_sql);
    
    echo json_encode([
        "success" => true,
        "message" => "Support request submitted successfully",
        "ticket_id" => $ticket_id
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to submit support request: " . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
