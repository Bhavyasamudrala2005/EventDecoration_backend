<?php
// Suppress PHP errors from being outputted with HTML tags
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any unwanted output
ob_start();

header("Content-Type: application/json");

// Clear any previous output
ob_clean();

// Include database connection
include "db.php";

$action = $_GET['action'] ?? '';

switch ($action) {

    // Add new equipment
    case 'add':
        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim($data['name'] ?? '');
        $category = trim($data['category'] ?? '');
        $type = trim($data['type'] ?? '');
        $specifications = trim($data['specifications'] ?? '');
        $price_per_day = floatval($data['price_per_day'] ?? 0);
        $quantity = intval($data['quantity'] ?? 0);
        $availability = trim($data['availability'] ?? 'Available');

        // Validate required fields
        if ($name === '' || $category === '' || $type === '' || $price_per_day <= 0 || $quantity <= 0) {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        // Validate availability value
        $valid_availability = ['Available', 'Limited', 'Unavailable'];
        if (!in_array($availability, $valid_availability)) {
            $availability = 'Available'; // Default to Available if invalid
        }

        $stmt = $conn->prepare(
            "INSERT INTO equipment (name, category, type, specifications, price_per_day, quantity, availability) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssdis", $name, $category, $type, $specifications, $price_per_day, $quantity, $availability);
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success",
                "message" => "Equipment added successfully",
                "equipment_id" => $stmt->insert_id
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add equipment: " . $conn->error]);
        }
        $stmt->close();
        break;

    // View equipment list
    case 'view':
        $low_stock_only = isset($_GET['low_stock_only']) && $_GET['low_stock_only'] == '1';
        $for_users = isset($_GET['for_users']) && $_GET['for_users'] == '1';
        
        if ($low_stock_only) {
            // Get out of stock items (quantity = 0)
            $result = $conn->query("SELECT * FROM equipment WHERE quantity = 0 ORDER BY name ASC");
        } else if ($for_users) {
            // Get only available items for users (hide out of stock items)
            $result = $conn->query("SELECT * FROM equipment WHERE quantity > 0 ORDER BY created_at DESC");
        } else {
            // Get all equipment for admin
            $result = $conn->query("SELECT * FROM equipment ORDER BY created_at DESC");
        }
        
        $equipment = [];
        while($row = $result->fetch_assoc()){
            $equipment[] = $row;
        }
        echo json_encode(["status"=>"success","equipment_list"=>$equipment, "low_stock_filter"=>$low_stock_only]);
        break;

    // Update equipment - full update of all fields
    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Get the ID - should be numeric
        $id = intval(trim($data['id'] ?? '0'));
        $name = trim($data['name'] ?? '');
        $category = trim($data['category'] ?? '');
        $type = trim($data['type'] ?? '');
        $specifications = trim($data['specifications'] ?? '');
        $price_per_day = floatval($data['price_per_day'] ?? 0);
        $availability = trim($data['availability'] ?? '');

        // Validate required fields
        if ($id <= 0) {
            echo json_encode(["status" => "error", "message" => "Equipment ID is required"]);
            exit;
        }

        if ($name === '' || $category === '' || $price_per_day <= 0) {
            echo json_encode(["status" => "error", "message" => "Name, category and price are required"]);
            exit;
        }

        // Validate availability value
        $valid_availability = ['Available', 'Limited', 'Unavailable'];
        if (!in_array($availability, $valid_availability)) {
            $availability = 'Available'; // Default to Available if invalid
        }

        // Check if equipment exists by ID
        $check_stmt = $conn->prepare("SELECT id FROM equipment WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Equipment not found in database"]);
            $check_stmt->close();
            exit;
        }
        $check_stmt->close();

        // Update equipment with integer ID
        $stmt = $conn->prepare(
            "UPDATE equipment 
             SET name = ?, category = ?, type = ?, specifications = ?, price_per_day = ?, availability = ? 
             WHERE id = ?"
        );
        $stmt->bind_param("ssssdsi", $name, $category, $type, $specifications, $price_per_day, $availability, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Equipment updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update equipment: " . $conn->error]);
        }
        $stmt->close();
        break;

    // Full update - updates all equipment fields
    case 'update_full':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $category = trim($data['category'] ?? '');
        $type = trim($data['type'] ?? '');
        $specifications = trim($data['specifications'] ?? '');
        $price_per_day = floatval($data['price_per_day'] ?? 0);
        $availability = trim($data['availability'] ?? '');

        // Validate required fields
        if ($id <= 0) {
            echo json_encode(["status" => "error", "message" => "Invalid equipment ID"]);
            exit;
        }

        if ($name === '' || $category === '' || $type === '' || $specifications === '' || $price_per_day <= 0 || $availability === '') {
            echo json_encode(["status" => "error", "message" => "All fields are required"]);
            exit;
        }

        // Validate availability value
        $valid_availability = ['Available', 'Limited', 'Unavailable'];
        if (!in_array($availability, $valid_availability)) {
            echo json_encode(["status" => "error", "message" => "Invalid availability status"]);
            exit;
        }

        // Check if equipment exists
        $check_stmt = $conn->prepare("SELECT id FROM equipment WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            echo json_encode(["status" => "error", "message" => "Equipment not found"]);
            exit;
        }

        // Update equipment
        $stmt = $conn->prepare(
            "UPDATE equipment 
             SET name = ?, category = ?, type = ?, specifications = ?, price_per_day = ?, availability = ? 
             WHERE id = ?"
        );
        $stmt->bind_param("ssssdsi", $name, $category, $type, $specifications, $price_per_day, $availability, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Equipment updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update equipment: " . $conn->error]);
        }
        $stmt->close();
        break;

    // Update image
    case 'update_image':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $image = trim($data['image_url'] ?? '');

        if($id <= 0 || $image === ''){
            echo json_encode(["status"=>"error","message"=>"Invalid input"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE equipment SET image_url=? WHERE id=?");
        $stmt->bind_param("si",$image,$id);
        if($stmt->execute()){
            echo json_encode(["status"=>"success","message"=>"Image updated successfully"]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Failed to update image"]);
        }
        break;

    // Delete equipment
    case 'delete':
        $id = intval($_GET['id'] ?? 0);
        if($id <= 0){
            echo json_encode(["status"=>"error","message"=>"Invalid equipment ID"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM equipment WHERE id=?");
        $stmt->bind_param("i",$id);
        if($stmt->execute()){
            echo json_encode(["status"=>"success","message"=>"Equipment deleted successfully"]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Failed to delete equipment"]);
        }
        break;

    default:
        echo json_encode(["status"=>"error","message"=>"Invalid action"]);
        break;
}
?>
