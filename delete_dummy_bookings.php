<?php
// ============================================
// DELETE ALL DUMMY BOOKINGS
// Run this file once in your browser to clear dummy data
// URL: http://localhost/your-folder/delete_dummy_bookings.php
// ============================================

header("Content-Type: application/json");
include "db.php";

try {
    // Get current count before deletion
    $result = $conn->query("SELECT COUNT(*) as count FROM bookings");
    $before = $result->fetch_assoc()['count'];
    
    // Delete ALL bookings (start fresh)
    $conn->query("DELETE FROM bookings");
    
    // Reset auto-increment
    $conn->query("ALTER TABLE bookings AUTO_INCREMENT = 1");
    
    // Get count after deletion
    $result = $conn->query("SELECT COUNT(*) as count FROM bookings");
    $after = $result->fetch_assoc()['count'];
    
    echo json_encode([
        "success" => true,
        "message" => "All dummy bookings deleted successfully!",
        "before_count" => intval($before),
        "after_count" => intval($after),
        "deleted" => intval($before) - intval($after)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error: " . $e->getMessage()
    ]);
}

$conn->close();
?>
