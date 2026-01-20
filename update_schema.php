<?php
include "db.php";

echo "<h2>Starting Database Schema Update...</h2>";

// Array of columns to add if they don't exist
$columns_to_add = [
    "start_date" => "DATE DEFAULT NULL",
    "end_date" => "DATE DEFAULT NULL",
    "time_slot" => "VARCHAR(50) DEFAULT 'Full Day'",
    "customer_name" => "VARCHAR(100) DEFAULT NULL",
    "customer_phone" => "VARCHAR(20) DEFAULT NULL",
    "delivery_address" => "TEXT DEFAULT NULL"
];

$table = "bookings";

foreach ($columns_to_add as $column => $definition) {
    // Check if column exists
    $check = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    
    if ($check->num_rows == 0) {
        // Column doesn't exist, add it
        $sql = "ALTER TABLE `$table` ADD `$column` $definition";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Successfully added column: <strong>$column</strong></p>";
        } else {
            echo "<p style='color: red;'>Error adding column $column: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>Column <strong>$column</strong> already exists.</p>";
    }
}

echo "<h3>Schema Update Complete.</h3>";
echo "<p>Please try making a new booking in the app now.</p>";

$conn->close();
?>
