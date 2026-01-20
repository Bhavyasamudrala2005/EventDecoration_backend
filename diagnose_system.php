<?php
header("Content-Type: text/plain");
include "db.php";

echo "=== DIAGNOSTIC REPORT ===\n";
echo "Server Time: " . date("Y-m-d H:i:s") . "\n\n";

// 1. Check book_equipment.php content
echo "1. CHECKING book_equipment.php FIX:\n";
$file_content = file_get_contents("book_equipment.php");
if ($file_content !== false) {
    if (strpos($file_content, "iiiidssssss") !== false) {
        echo "[PASS] Fix found! Type string 'iiiidssssss' is present.\n";
    } elseif (strpos($file_content, "iiiidsssssss") !== false) {
        echo "[FAIL] Old version found! Type string 'iiiidsssssss' (12 chars) is present.\n";
        echo "ACTION REQUIRED: You must copy the updated PHP files to the server.\n";
    } else {
        echo "[WARN] Could not find type string. File might be completely different.\n";
    }
} else {
    echo "[FAIL] Could not read book_equipment.php\n";
}
echo "\n";

// 2. Check Database Bookings
echo "2. CHECKING DATABASE BOOKINGS:\n";
$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
$count = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $count = $row['count'];
    echo "Total Bookings in Database: " . $count . "\n";
} else {
    echo "[FAIL] Query failed: " . $conn->error . "\n";
}

if ($count > 0) {
    echo "Latest 3 Bookings:\n";
    $result = $conn->query("SELECT id, user_id, equipment_id, total_amount, status, created_at FROM bookings ORDER BY id DESC LIMIT 3");
    while ($row = $result->fetch_assoc()) {
        echo " - ID: " . $row['id'] . " | User ID: " . $row['user_id'] . " | Item ID: " . $row['equipment_id'] . " | Status: " . $row['status'] . "\n";
    }
} else {
    echo "No bookings found.\n";
}
echo "\n";

// 3. Check Users
echo "3. CHECKING USERS:\n";
$result = $conn->query("SELECT id, name, email FROM users LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "Users found:\n";
    while ($row = $result->fetch_assoc()) {
        echo " - ID: " . $row['id'] . " | Name: " . $row['name'] . "\n";
    }
} else {
    echo "No users found in database.\n";
}



// 4. Check Debug Log
echo "\n4. CHECKING DEBUG LOG (Last 2KB):\n";
$log_file = 'debug_booking_log.txt';
if (file_exists($log_file)) {
    echo "Log file exists. Content:\n";
    echo "--------------------------------------------------\n";
    $log_content = file_get_contents($log_file);
    // Show last 2000 chars if too long
    if (strlen($log_content) > 2000) {
        echo "..." . substr($log_content, -2000);
    } else {
        echo $log_content;
    }
    echo "\n--------------------------------------------------\n";
} else {
    echo "Debug log file not found. No booking attempts recorded since logging was added.\n";
}

$conn->close();
?>
