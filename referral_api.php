<?php
// ========================================================================
// REFERRAL PROGRAM API - referral_api.php
// Handles all referral-related operations
// ========================================================================

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

// Get the action from request
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');

// Handle different actions
switch ($action) {
    case 'get_referral_code':
        getReferralCode();
        break;
    case 'apply_referral_code':
        applyReferralCode();
        break;
    case 'get_referral_stats':
        getReferralStats();
        break;
    case 'get_credits':
        getUserCredits();
        break;
    case 'get_credit_history':
        getCreditHistory();
        break;
    case 'use_credits':
        useCredits();
        break;
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action. Use: get_referral_code, apply_referral_code, get_referral_stats, get_credits, get_credit_history, use_credits"
        ]);
}

// ========================================================================
// GET OR GENERATE REFERRAL CODE FOR USER
// ========================================================================
function getReferralCode() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    // Check if user already has a referral code
    $query = "SELECT referral_code, credits FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        $code = $row['referral_code'];
        $credits = floatval($row['credits']);
        
        // If no code exists, generate one
        if (empty($code)) {
            $code = generateUniqueCode();
            
            // Update user with new code
            $conn->query("UPDATE users SET referral_code = '$code' WHERE id = $user_id");
            
            // Also insert into referral_codes table
            $conn->query("INSERT IGNORE INTO referral_codes (user_id, code) VALUES ($user_id, '$code')");
        }
        
        // Get user name for sharing
        $nameQuery = "SELECT name FROM users WHERE id = $user_id";
        $nameResult = $conn->query($nameQuery);
        $userName = "User";
        if ($nameResult && $nameRow = $nameResult->fetch_assoc()) {
            $userName = $nameRow['name'];
        }
        
        echo json_encode([
            "status" => "success",
            "referral_code" => $code,
            "credits" => $credits,
            "user_name" => $userName,
            "share_message" => "Hey! Use my referral code $code to sign up on EventEase and get ₹25 credits! Download now: https://eventease.app/download"
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}

// ========================================================================
// GENERATE UNIQUE REFERRAL CODE
// ========================================================================
function generateUniqueCode() {
    global $conn;
    
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    
    do {
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        // Check if code already exists
        $checkQuery = "SELECT id FROM users WHERE referral_code = '$code'";
        $checkResult = $conn->query($checkQuery);
    } while ($checkResult && $checkResult->num_rows > 0);
    
    return $code;
}

// ========================================================================
// APPLY REFERRAL CODE (When new user signs up)
// ========================================================================
function applyReferralCode() {
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $referral_code = isset($data['referral_code']) ? strtoupper(trim($data['referral_code'])) : '';
    
    if ($user_id <= 0 || empty($referral_code)) {
        echo json_encode(["status" => "error", "message" => "User ID and referral code are required"]);
        return;
    }
    
    // Check if user already used a referral code
    $checkUsed = "SELECT referred_by FROM users WHERE id = $user_id";
    $usedResult = $conn->query($checkUsed);
    if ($usedResult && $row = $usedResult->fetch_assoc()) {
        if (!empty($row['referred_by']) && $row['referred_by'] > 0) {
            echo json_encode(["status" => "error", "message" => "You have already used a referral code"]);
            return;
        }
    }
    
    // Find the referrer by code
    $referrerQuery = "SELECT id, name FROM users WHERE referral_code = '$referral_code'";
    $referrerResult = $conn->query($referrerQuery);
    
    if (!$referrerResult || $referrerResult->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "Invalid referral code"]);
        return;
    }
    
    $referrer = $referrerResult->fetch_assoc();
    $referrer_id = $referrer['id'];
    $referrer_name = $referrer['name'];
    
    // Check if user is trying to use their own code
    if ($referrer_id == $user_id) {
        echo json_encode(["status" => "error", "message" => "You cannot use your own referral code"]);
        return;
    }
    
    // Get reward amounts from settings
    $referrerReward = 50.00;
    $refereeReward = 25.00;
    
    $settingsQuery = "SELECT setting_key, setting_value FROM referral_settings WHERE setting_key IN ('referrer_reward', 'referee_reward')";
    $settingsResult = $conn->query($settingsQuery);
    if ($settingsResult) {
        while ($setting = $settingsResult->fetch_assoc()) {
            if ($setting['setting_key'] == 'referrer_reward') {
                $referrerReward = floatval($setting['setting_value']);
            } else if ($setting['setting_key'] == 'referee_reward') {
                $refereeReward = floatval($setting['setting_value']);
            }
        }
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update user with referred_by
        $conn->query("UPDATE users SET referred_by = $referrer_id WHERE id = $user_id");
        
        // Add credits to referrer
        $conn->query("UPDATE users SET credits = credits + $referrerReward WHERE id = $referrer_id");
        
        // Add credits to new user (referee)
        $conn->query("UPDATE users SET credits = credits + $refereeReward WHERE id = $user_id");
        
        // Record the referral
        $conn->query("INSERT INTO referrals (referrer_id, referred_id, referral_code, status, reward_amount, credited_at) 
                      VALUES ($referrer_id, $user_id, '$referral_code', 'credited', $referrerReward, NOW())");
        
        // Record credit transactions for referrer
        $conn->query("INSERT INTO credit_transactions (user_id, amount, transaction_type, description, reference_id) 
                      VALUES ($referrer_id, $referrerReward, 'referral_bonus', 'Referral bonus for inviting a friend', $user_id)");
        
        // Record credit transactions for referee
        $conn->query("INSERT INTO credit_transactions (user_id, amount, transaction_type, description, reference_id) 
                      VALUES ($user_id, $refereeReward, 'signup_bonus', 'Signup bonus using referral code', $referrer_id)");
        
        $conn->commit();
        
        echo json_encode([
            "status" => "success",
            "message" => "Referral code applied successfully!",
            "credits_earned" => $refereeReward,
            "referrer_name" => $referrer_name,
            "total_credits" => $refereeReward
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Failed to apply referral code: " . $e->getMessage()]);
    }
}

// ========================================================================
// GET REFERRAL STATISTICS FOR USER
// ========================================================================
function getReferralStats() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    // Get user's referral code and credits
    $userQuery = "SELECT referral_code, credits FROM users WHERE id = $user_id";
    $userResult = $conn->query($userQuery);
    
    if (!$userResult || $userResult->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        return;
    }
    
    $user = $userResult->fetch_assoc();
    
    // Count total referrals
    $countQuery = "SELECT COUNT(*) as total_referrals FROM referrals WHERE referrer_id = $user_id";
    $countResult = $conn->query($countQuery);
    $totalReferrals = 0;
    if ($countResult && $row = $countResult->fetch_assoc()) {
        $totalReferrals = intval($row['total_referrals']);
    }
    
    // Calculate total earnings from referrals
    $earningsQuery = "SELECT SUM(amount) as total_earnings FROM credit_transactions 
                      WHERE user_id = $user_id AND transaction_type = 'referral_bonus'";
    $earningsResult = $conn->query($earningsQuery);
    $totalEarnings = 0;
    if ($earningsResult && $row = $earningsResult->fetch_assoc()) {
        $totalEarnings = floatval($row['total_earnings']) ?: 0;
    }
    
    // Get list of referred users
    $referredQuery = "SELECT u.name, u.email, r.created_at, r.reward_amount 
                      FROM referrals r 
                      JOIN users u ON r.referred_id = u.id 
                      WHERE r.referrer_id = $user_id 
                      ORDER BY r.created_at DESC 
                      LIMIT 20";
    $referredResult = $conn->query($referredQuery);
    $referredUsers = [];
    if ($referredResult) {
        while ($row = $referredResult->fetch_assoc()) {
            $referredUsers[] = [
                "name" => $row['name'],
                "email" => maskEmail($row['email']),
                "date" => $row['created_at'],
                "reward" => floatval($row['reward_amount'])
            ];
        }
    }
    
    echo json_encode([
        "status" => "success",
        "referral_code" => $user['referral_code'],
        "current_credits" => floatval($user['credits']),
        "total_referrals" => $totalReferrals,
        "total_earnings" => $totalEarnings,
        "referred_users" => $referredUsers
    ]);
}

// ========================================================================
// MASK EMAIL FOR PRIVACY
// ========================================================================
function maskEmail($email) {
    $parts = explode('@', $email);
    if (count($parts) == 2) {
        $name = $parts[0];
        $domain = $parts[1];
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(0, strlen($name) - 2));
        return $maskedName . '@' . $domain;
    }
    return $email;
}

// ========================================================================
// GET USER CREDITS
// ========================================================================
function getUserCredits() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    $query = "SELECT credits FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode([
            "status" => "success",
            "credits" => floatval($row['credits'])
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
}

// ========================================================================
// GET CREDIT HISTORY
// ========================================================================
function getCreditHistory() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    $query = "SELECT id, amount, transaction_type, description, created_at 
              FROM credit_transactions 
              WHERE user_id = $user_id 
              ORDER BY created_at DESC 
              LIMIT 50";
    
    $result = $conn->query($query);
    $transactions = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $transactions[] = [
                "id" => intval($row['id']),
                "amount" => floatval($row['amount']),
                "type" => $row['transaction_type'],
                "description" => $row['description'],
                "date" => $row['created_at'],
                "is_credit" => in_array($row['transaction_type'], ['referral_bonus', 'signup_bonus', 'admin_credit'])
            ];
        }
    }
    
    // Get current balance
    $balanceQuery = "SELECT credits FROM users WHERE id = $user_id";
    $balanceResult = $conn->query($balanceQuery);
    $currentBalance = 0;
    if ($balanceResult && $row = $balanceResult->fetch_assoc()) {
        $currentBalance = floatval($row['credits']);
    }
    
    echo json_encode([
        "status" => "success",
        "current_balance" => $currentBalance,
        "transactions" => $transactions,
        "count" => count($transactions)
    ]);
}

// ========================================================================
// USE CREDITS FOR BOOKING
// ========================================================================
function useCredits() {
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $amount = isset($data['amount']) ? floatval($data['amount']) : 0;
    $booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;
    
    if ($user_id <= 0 || $amount <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID and amount are required"]);
        return;
    }
    
    // Check user's current balance
    $balanceQuery = "SELECT credits FROM users WHERE id = $user_id";
    $balanceResult = $conn->query($balanceQuery);
    
    if (!$balanceResult || $balanceResult->num_rows == 0) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        return;
    }
    
    $currentBalance = floatval($balanceResult->fetch_assoc()['credits']);
    
    if ($amount > $currentBalance) {
        echo json_encode([
            "status" => "error", 
            "message" => "Insufficient credits. Available: ₹" . number_format($currentBalance, 2)
        ]);
        return;
    }
    
    // Deduct credits
    $conn->begin_transaction();
    
    try {
        // Update user balance
        $conn->query("UPDATE users SET credits = credits - $amount WHERE id = $user_id");
        
        // Record transaction
        $description = $booking_id > 0 ? "Used for booking #$booking_id" : "Used for booking";
        $conn->query("INSERT INTO credit_transactions (user_id, amount, transaction_type, description, reference_id) 
                      VALUES ($user_id, -$amount, 'booking_used', '$description', $booking_id)");
        
        $conn->commit();
        
        $newBalance = $currentBalance - $amount;
        
        echo json_encode([
            "status" => "success",
            "message" => "Credits applied successfully",
            "amount_used" => $amount,
            "new_balance" => $newBalance
        ]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Failed to use credits: " . $e->getMessage()]);
    }
}
?>
