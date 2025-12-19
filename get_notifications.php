<?php
header("Content-Type: application/json");
include "db.php"; // your database connection

// Get user_id from GET or POST JSON (optional, if you want user-specific events)
$user_id = intval($_GET['user_id'] ?? 0);
if ($user_id <= 0) {
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = intval($data['user_id'] ?? 0);
}

// You can skip user_id check if events are general
// For now, we fetch all events
$sql = "SELECT id, name, date, location, description FROM events ORDER BY date DESC";
$result = $conn->query($sql);

$events = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            "id" => $row['id'],
            "name" => $row['name'],
            "date" => $row['date'],
            "location" => $row['location'],
            "description" => $row['description']
        ];
    }
}

// Return JSON
echo json_encode([
    "status" => "success",
    "events" => $events
]);
?>
