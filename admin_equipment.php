<?php
header("Content-Type: application/json");
include "db.php"; // Database connection

$action = $_GET['action'] ?? '';

switch ($action) {

    // Add new equipment
    case 'add':
        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim($data['name'] ?? '');
        $price = floatval($data['price_per_day'] ?? 0);
        $quantity = intval($data['quantity'] ?? 0);
        $image = trim($data['image_url'] ?? '');

        if ($name === '' || $price <= 0 || $quantity <= 0) {
            echo json_encode(["status"=>"error","message"=>"Invalid input"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO equipment (name, price_per_day, quantity, image_url) VALUES (?,?,?,?)");
        $stmt->bind_param("sdis",$name,$price,$quantity,$image);
        if ($stmt->execute()) {
            echo json_encode(["status"=>"success","message"=>"Equipment added successfully","equipment_id"=>$stmt->insert_id]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Failed to add equipment"]);
        }
        break;

    // View equipment list
    case 'view':
        $result = $conn->query("SELECT * FROM equipment ORDER BY created_at DESC");
        $equipment = [];
        while($row = $result->fetch_assoc()){
            $equipment[] = $row;
        }
        echo json_encode(["status"=>"success","equipment_list"=>$equipment]);
        break;

    // Update price & quantity
    case 'update':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $price = floatval($data['price_per_day'] ?? 0);
        $quantity = intval($data['quantity'] ?? 0);

        if($id <= 0 || $price <= 0 || $quantity < 0){
            echo json_encode(["status"=>"error","message"=>"Invalid input"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE equipment SET price_per_day=?, quantity=? WHERE id=?");
        $stmt->bind_param("dii",$price,$quantity,$id);
        if($stmt->execute()){
            echo json_encode(["status"=>"success","message"=>"Equipment updated successfully"]);
        } else {
            echo json_encode(["status"=>"error","message"=>"Failed to update equipment"]);
        }
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
