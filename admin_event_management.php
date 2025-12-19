<?php
header("Content-Type: application/json");
include "db.php";

$action = $_GET['action'] ?? '';

switch ($action) {

    /* 1. View all events */
    case 'events':
        $result = $conn->query("SELECT * FROM events ORDER BY created_at DESC");
        $events = [];
        while ($row = $result->fetch_assoc()) {
            $events[] = $row;
        }
        echo json_encode(["status"=>"success","events"=>$events]);
        break;

    /* 2. View event equipment */
    case 'event_equipment':
        $event_id = intval($_GET['event_id'] ?? 0);
        if ($event_id <= 0) {
            echo json_encode(["status"=>"error","message"=>"event_id required"]);
            exit;
        }

        $stmt = $conn->prepare(
            "SELECT e.name, b.quantity, b.rental_days, b.total_amount
             FROM bookings b
             JOIN equipment e ON b.equipment_id = e.id
             WHERE b.event_id = ?"
        );
        $stmt->bind_param("i",$event_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $equipment = [];
        while ($row = $res->fetch_assoc()) {
            $equipment[] = $row;
        }
        echo json_encode(["status"=>"success","equipment"=>$equipment]);
        break;

    /* 3. Track event bookings */
    case 'event_bookings':
        $event_id = intval($_GET['event_id'] ?? 0);

        $stmt = $conn->prepare(
            "SELECT id, quantity, total_amount, status, created_at
             FROM bookings WHERE event_id = ?"
        );
        $stmt->bind_param("i",$event_id);
        $stmt->execute();
        $res = $stmt->get_result();

        $bookings = [];
        while ($row = $res->fetch_assoc()) {
            $bookings[] = $row;
        }
        echo json_encode(["status"=>"success","bookings"=>$bookings]);
        break;

    /* 4. Assign operator */
    case 'assign_operator':
        $data = json_decode(file_get_contents("php://input"), true);
        $event_id = intval($data['event_id'] ?? 0);
        $operator_id = intval($data['operator_id'] ?? 0);

        if ($event_id <= 0 || $operator_id <= 0) {
            echo json_encode(["status"=>"error","message"=>"Invalid input"]);
            exit;
        }

        $stmt = $conn->prepare(
            "INSERT INTO event_operators (event_id, operator_id)
             VALUES (?, ?)"
        );
        $stmt->bind_param("ii",$event_id,$operator_id);

        if ($stmt->execute()) {
            echo json_encode([
                "status"=>"success",
                "message"=>"Operator assigned successfully"
            ]);
        } else {
            echo json_encode([
                "status"=>"error",
                "message"=>"Assignment failed"
            ]);
        }
        break;

    default:
        echo json_encode(["status"=>"error","message"=>"Invalid action"]);
}
?>
