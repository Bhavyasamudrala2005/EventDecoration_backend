<?php
header("Content-Type: application/json");
include "db.php";

$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');
$data = json_decode(file_get_contents("php://input"), true);

if (empty($action) && !empty($data['action'])) {
    $action = $data['action'];
}

switch ($action) {
    case 'get':
        getAddresses();
        break;
    case 'add':
        addAddress($data);
        break;
    case 'update':
        updateAddress($data);
        break;
    case 'delete':
        deleteAddress($data);
        break;
    case 'set_default':
        setDefaultAddress($data);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

function getAddresses() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 1;
    
    $sql = "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $addresses = [];
    while ($row = $result->fetch_assoc()) {
        $addresses[] = [
            "id" => intval($row['id']),
            "user_id" => intval($row['user_id']),
            "address_type" => $row['address_type'],
            "contact_name" => $row['contact_name'],
            "address_line1" => $row['address_line1'],
            "address_line2" => $row['address_line2'],
            "city" => $row['city'],
            "state" => $row['state'],
            "zip_code" => $row['zip_code'],
            "phone" => $row['phone'],
            "is_default" => (bool)$row['is_default']
        ];
    }
    
    echo json_encode([
        "status" => "success",
        "addresses" => $addresses,
        "count" => count($addresses)
    ]);
}

function addAddress($data) {
    global $conn;
    
    $user_id = intval($data['user_id'] ?? 1);
    $address_type = trim($data['address_type'] ?? 'Home');
    $contact_name = trim($data['contact_name'] ?? '');
    $address_line1 = trim($data['address_line1'] ?? '');
    $address_line2 = trim($data['address_line2'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $zip_code = trim($data['zip_code'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $is_default = isset($data['is_default']) ? (int)$data['is_default'] : 0;
    
    if (empty($address_line1) || empty($city)) {
        echo json_encode(["status" => "error", "message" => "Address and city are required"]);
        return;
    }
    
    // If this is set as default, unset other defaults
    if ($is_default) {
        $conn->query("UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");
    }
    
    $sql = "INSERT INTO user_addresses (user_id, address_type, contact_name, address_line1, address_line2, city, state, zip_code, phone, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssssi", $user_id, $address_type, $contact_name, $address_line1, $address_line2, $city, $state, $zip_code, $phone, $is_default);
    
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Address added successfully",
            "address_id" => $conn->insert_id
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to add address"]);
    }
}

function updateAddress($data) {
    global $conn;
    
    $id = intval($data['id'] ?? 0);
    $address_type = trim($data['address_type'] ?? 'Home');
    $contact_name = trim($data['contact_name'] ?? '');
    $address_line1 = trim($data['address_line1'] ?? '');
    $address_line2 = trim($data['address_line2'] ?? '');
    $city = trim($data['city'] ?? '');
    $state = trim($data['state'] ?? '');
    $zip_code = trim($data['zip_code'] ?? '');
    $phone = trim($data['phone'] ?? '');
    
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Address ID is required"]);
        return;
    }
    
    $sql = "UPDATE user_addresses SET address_type = ?, contact_name = ?, address_line1 = ?, address_line2 = ?, city = ?, state = ?, zip_code = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $address_type, $contact_name, $address_line1, $address_line2, $city, $state, $zip_code, $phone, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Address updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update address"]);
    }
}

function deleteAddress($data) {
    global $conn;
    
    $id = intval($data['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Address ID is required"]);
        return;
    }
    
    $sql = "DELETE FROM user_addresses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Address deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete address"]);
    }
}

function setDefaultAddress($data) {
    global $conn;
    
    $id = intval($data['id'] ?? 0);
    $user_id = intval($data['user_id'] ?? 1);
    
    if ($id <= 0) {
        echo json_encode(["status" => "error", "message" => "Address ID is required"]);
        return;
    }
    
    // Unset all defaults for this user
    $conn->query("UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");
    
    // Set new default
    $sql = "UPDATE user_addresses SET is_default = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Default address updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to set default address"]);
    }
}
?>
