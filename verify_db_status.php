<?php
include 'db.php';

// Check bookings table structure
$result = $conn->query("DESCRIBE bookings");
echo "<h3>Bookings Table Structure:</h3>";
if ($result) {
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing bookings table: " . $conn->error;
}

// Check recent bookings
echo "<h3>Recent 5 Bookings:</h3>";
$recent = $conn->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
if ($recent) {
    if ($recent->num_rows > 0) {
        while ($row = $recent->fetch_assoc()) {
            echo "<pre>";
            print_r($row);
            echo "</pre>";
        }
    } else {
        echo "No bookings found.";
    }
} else {
    echo "Error fetching bookings: " . $conn->error;
}
?>
