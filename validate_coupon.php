<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "db.php";

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $code = strtoupper(trim($data['code'] ?? ''));
    $user_id = intval($data['user_id'] ?? 0);
    $order_amount = floatval($data['order_amount'] ?? 0);
    
    if (empty($code)) {
        echo json_encode(["success" => false, "valid" => false, "message" => "Please enter a coupon code"]);
        exit;
    }
    
    // Get coupon details
    $sql = "SELECT * FROM coupons WHERE code = ? AND is_active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo json_encode(["success" => false, "valid" => false, "message" => "Invalid coupon code"]);
        exit;
    }
    
    $coupon = $result->fetch_assoc();
    
    // Check validity dates
    $today = date('Y-m-d');
    if ($today < $coupon['valid_from'] || $today > $coupon['valid_until']) {
        echo json_encode(["success" => false, "valid" => false, "message" => "Coupon has expired or not yet valid"]);
        exit;
    }
    
    // Check usage limit
    if ($coupon['usage_limit'] !== null && $coupon['times_used'] >= $coupon['usage_limit']) {
        echo json_encode(["success" => false, "valid" => false, "message" => "Coupon usage limit reached"]);
        exit;
    }
    
    // Check minimum order amount
    if ($order_amount < $coupon['min_order_amount']) {
        echo json_encode([
            "success" => false, 
            "valid" => false, 
            "message" => "Minimum order of ₹" . number_format($coupon['min_order_amount']) . " required"
        ]);
        exit;
    }
    
    // Check if user already used this coupon
    if ($user_id > 0) {
        $usage_sql = "SELECT id FROM coupon_usage WHERE coupon_id = ? AND user_id = ?";
        $usage_stmt = $conn->prepare($usage_sql);
        $usage_stmt->bind_param("ii", $coupon['id'], $user_id);
        $usage_stmt->execute();
        $usage_result = $usage_stmt->get_result();
        
        if ($usage_result->num_rows > 0) {
            echo json_encode(["success" => false, "valid" => false, "message" => "You have already used this coupon"]);
            exit;
        }
    }
    
    // Calculate discount
    $discount_amount = 0;
    if ($coupon['discount_type'] == 'percentage') {
        $discount_amount = ($order_amount * $coupon['discount_value']) / 100;
        // Apply max discount cap if set
        if ($coupon['max_discount'] !== null && $discount_amount > $coupon['max_discount']) {
            $discount_amount = $coupon['max_discount'];
        }
    } else {
        // Fixed discount
        $discount_amount = $coupon['discount_value'];
    }
    
    // Discount cannot exceed order amount
    if ($discount_amount > $order_amount) {
        $discount_amount = $order_amount;
    }
    
    $final_amount = $order_amount - $discount_amount;
    
    echo json_encode([
        "success" => true,
        "valid" => true,
        "message" => "Coupon applied successfully!",
        "coupon_id" => intval($coupon['id']),
        "code" => $coupon['code'],
        "discount_type" => $coupon['discount_type'],
        "discount_value" => floatval($coupon['discount_value']),
        "discount_amount" => round($discount_amount, 2),
        "original_amount" => round($order_amount, 2),
        "final_amount" => round($final_amount, 2)
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "valid" => false, "message" => "Error: " . $e->getMessage()]);
}

$conn->close();
?>
