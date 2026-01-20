<?php
header("Content-Type: application/json");
include "db.php";

// Firebase HTTP v1 API configuration
define('FIREBASE_PROJECT_ID', 'event-decorationitems');
define('SERVICE_ACCOUNT_FILE', __DIR__ . '/firebase-service-account.json');

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

$type = trim($data['type'] ?? '');
$message = trim($data['message'] ?? '');
$title = trim($data['title'] ?? 'EventEase Notification');
$user_id = isset($data['user_id']) ? intval($data['user_id']) : null;
$send_to_all = isset($data['send_to_all']) ? $data['send_to_all'] : true;

// Validate required fields
if ($message === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Notification message is required"
    ]);
    exit;
}

// Map incoming types to valid database enum values
switch ($type) {
    case 'status_alert':
    case 'reminder':
    case 'promotion':
    case 'admin':
        $type = 'admin';
        break;
    case 'booking':
        $type = 'booking';
        break;
    case 'new_equipment':
        $type = 'new_equipment';
        break;
    default:
        $type = 'admin';
}

/**
 * Get OAuth2 access token from Firebase service account
 */
function getFirebaseAccessToken() {
    if (!file_exists(SERVICE_ACCOUNT_FILE)) {
        return ['error' => 'Service account file not found'];
    }
    
    $serviceAccount = json_decode(file_get_contents(SERVICE_ACCOUNT_FILE), true);
    
    // Create JWT header
    $header = [
        'alg' => 'RS256',
        'typ' => 'JWT'
    ];
    
    // Create JWT claim set
    $now = time();
    $claim = [
        'iss' => $serviceAccount['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $now + 3600,
        'iat' => $now
    ];
    
    // Encode header and claim
    $headerEncoded = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
    $claimEncoded = rtrim(strtr(base64_encode(json_encode($claim)), '+/', '-_'), '=');
    
    // Create signature
    $signatureInput = $headerEncoded . '.' . $claimEncoded;
    $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);
    
    openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
    $signatureEncoded = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    
    $jwt = $signatureInput . '.' . $signatureEncoded;
    
    // Exchange JWT for access token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $tokenData = json_decode($response, true);
    
    if (isset($tokenData['access_token'])) {
        return ['token' => $tokenData['access_token']];
    }
    
    return ['error' => 'Failed to get access token: ' . $response];
}

/**
 * Send FCM push notification using Firebase HTTP v1 API
 */
function sendFCMNotification($tokens, $title, $message, $type) {
    if (empty($tokens)) {
        return ['success' => 0, 'failure' => 0];
    }
    
    // Get access token
    $tokenResult = getFirebaseAccessToken();
    if (isset($tokenResult['error'])) {
        return ['success' => 0, 'failure' => count($tokens), 'error' => $tokenResult['error']];
    }
    
    $accessToken = $tokenResult['token'];
    $projectId = FIREBASE_PROJECT_ID;
    $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
    
    $successCount = 0;
    $failureCount = 0;
    
    // Send to each token individually (HTTP v1 API requires individual sends)
    foreach ($tokens as $token) {
        $messagePayload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $message
                ],
                'data' => [
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'click_action' => 'OPEN_NOTIFICATION_ACTIVITY'
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'eventease_notifications'
                    ]
                ]
            ]
        ];
        
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messagePayload));
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $successCount++;
        } else {
            $failureCount++;
        }
    }
    
    return [
        'success' => $successCount,
        'failure' => $failureCount
    ];
}

try {
    $push_sent = 0;
    $push_failed = 0;
    
    // Check if fcm_token column exists
    $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'fcm_token'");
    $has_fcm_column = ($check_column && $check_column->num_rows > 0);
    
    // Combine title and message for database storage
    $full_message = $title . ": " . $message;
    
    if ($send_to_all) {
        // Send to all users - insert with NULL user_id (broadcast)
        $stmt = $conn->prepare(
            "INSERT INTO notifications (user_id, type, message, status) VALUES (NULL, ?, ?, 'unread')"
        );
        $stmt->bind_param("ss", $type, $full_message);
        
        if ($stmt->execute()) {
            $notification_id = $stmt->insert_id;
            
            // Send FCM push to all users with tokens
            if ($has_fcm_column) {
                $token_query = $conn->query("SELECT fcm_token FROM users WHERE fcm_token IS NOT NULL AND fcm_token != ''");
                $tokens = [];
                while ($row = $token_query->fetch_assoc()) {
                    $tokens[] = $row['fcm_token'];
                }
                
                if (!empty($tokens)) {
                    $fcm_result = sendFCMNotification($tokens, $title, $message, $type);
                    $push_sent = $fcm_result['success'] ?? 0;
                    $push_failed = $fcm_result['failure'] ?? 0;
                }
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Notification sent to all users",
                "notification_id" => $notification_id,
                "push_sent" => $push_sent,
                "push_failed" => $push_failed
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to send notification: " . $conn->error
            ]);
        }
        $stmt->close();
    } else {
        // Send to specific user
        if ($user_id === null || $user_id <= 0) {
            echo json_encode([
                "status" => "error",
                "message" => "User ID is required for targeted notifications"
            ]);
            exit;
        }
        
        $stmt = $conn->prepare(
            "INSERT INTO notifications (user_id, type, message, status) VALUES (?, ?, ?, 'unread')"
        );
        $stmt->bind_param("iss", $user_id, $type, $full_message);
        
        if ($stmt->execute()) {
            $notification_id = $stmt->insert_id;
            
            // Send FCM push to specific user
            if ($has_fcm_column) {
                $token_query = $conn->prepare("SELECT fcm_token FROM users WHERE id = ? AND fcm_token IS NOT NULL AND fcm_token != ''");
                $token_query->bind_param("i", $user_id);
                $token_query->execute();
                $result = $token_query->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    $fcm_result = sendFCMNotification([$row['fcm_token']], $title, $message, $type);
                    $push_sent = $fcm_result['success'] ?? 0;
                    $push_failed = $fcm_result['failure'] ?? 0;
                }
                $token_query->close();
            }
            
            echo json_encode([
                "status" => "success",
                "message" => "Notification sent to user",
                "notification_id" => $notification_id,
                "user_id" => $user_id,
                "push_sent" => $push_sent
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Failed to send notification: " . $conn->error
            ]);
        }
        $stmt->close();
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
?>
