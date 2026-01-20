<?php
header("Content-Type: application/json");
include "db.php";

echo "<h2>Database Connection Check</h2>";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "✓ Connected to database successfully<br><br>";

// Check if bookings table exists
$result = $conn->query("SHOW TABLES LIKE 'bookings'");
if ($result->num_rows > 0) {
    echo "✓ Bookings table exists<br><br>";
} else {
    echo "✗ Bookings table NOT FOUND<br><br>";
}

// Check bookings structure
echo "<h3>Bookings Table Structure:</h3>";
$result = $conn->query("DESCRIBE bookings");
if ($result) {
    echo "<pre>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "</pre>";
}

// Count total bookings
$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
$row = $result->fetch_assoc();
echo "<h3>Total Bookings: " . $row['count'] . "</h3>";

// Show all bookings
echo "<h3>All Bookings:</h3>";
$result = $conn->query("SELECT b.*, e.name as equipment_name FROM bookings b LEFT JOIN equipment e ON b.equipment_id = e.id ORDER BY b.id DESC LIMIT 20");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Equipment</th><th>Qty</th><th>Days</th><th>Amount</th><th>Status</th><th>Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . ($row['equipment_name'] ?? 'Equipment #' . $row['equipment_id']) . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>" . $row['rental_days'] . "</td>";
        echo "<td>₹" . $row['total_amount'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "<td>" . ($row['booking_date'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>No bookings found in database!</p>";
}

// Check users table
echo "<h3>Users in Database:</h3>";
$result = $conn->query("SELECT id, name, email FROM users LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>No users found!</p>";
}

// Check equipment table for Flower Stands
echo "<h3>Flower Stands Equipment:</h3>";
$result = $conn->query("SELECT * FROM equipment WHERE name LIKE '%Flower%' OR id = 20");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Price: ₹" . $row['price_per_day'] . "<br>";
    }
} else {
    echo "<p style='color:red'>Flower Stands not found in equipment table!</p>";
    echo "<p>Run this SQL: INSERT INTO equipment (id, name, category, type, specifications, price_per_day, quantity, availability) VALUES (20, 'Flower Stands', 'Decoration Items', 'Floral Decor', 'Metal, Adjustable height, Set of 10', 2000.00, 40, 'Available');</p>";
}

$conn->close();
?>
