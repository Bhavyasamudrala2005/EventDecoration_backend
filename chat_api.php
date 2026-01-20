<?php
// ========================================================================
// LIVE CHAT API - chat_api.php
// Handles all chat-related operations
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
    case 'start_conversation':
        startConversation();
        break;
    case 'send_message':
        sendMessage();
        break;
    case 'get_messages':
        getMessages();
        break;
    case 'get_conversations':
        getConversations();
        break;
    case 'close_conversation':
        closeConversation();
        break;
    case 'get_unread_count':
        getUnreadCount();
        break;
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid action. Use: start_conversation, send_message, get_messages, get_conversations, close_conversation, get_unread_count"
        ]);
}

// ========================================================================
// START NEW CONVERSATION
// ========================================================================
function startConversation() {
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : 0;
    $subject = isset($data['subject']) ? $conn->real_escape_string($data['subject']) : 'General Inquiry';
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    // Check for existing open conversation
    $checkQuery = "SELECT id FROM chat_conversations WHERE user_id = $user_id AND status = 'open' LIMIT 1";
    $checkResult = $conn->query($checkQuery);
    
    if ($checkResult && $checkResult->num_rows > 0) {
        $row = $checkResult->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "message" => "Existing conversation found",
            "conversation_id" => intval($row['id']),
            "is_new" => false
        ]);
        return;
    }
    
    // Create new conversation
    $query = "INSERT INTO chat_conversations (user_id, subject, status) VALUES ($user_id, '$subject', 'open')";
    
    if ($conn->query($query)) {
        $conversation_id = $conn->insert_id;
        
        // Send automatic welcome message from support
        $welcomeMsg = "Hello! Welcome to EventEase Support. How can we help you today?";
        $msgQuery = "INSERT INTO chat_messages (conversation_id, sender_type, sender_id, message) VALUES ($conversation_id, 'support', 1, '$welcomeMsg')";
        $conn->query($msgQuery);
        
        echo json_encode([
            "status" => "success",
            "message" => "Conversation started",
            "conversation_id" => $conversation_id,
            "is_new" => true
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to start conversation: " . $conn->error]);
    }
}

// ========================================================================
// SEND MESSAGE
// ========================================================================
function sendMessage() {
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $conversation_id = isset($data['conversation_id']) ? intval($data['conversation_id']) : 0;
    $sender_type = isset($data['sender_type']) ? $conn->real_escape_string($data['sender_type']) : 'user';
    $sender_id = isset($data['sender_id']) ? intval($data['sender_id']) : 0;
    $message = isset($data['message']) ? $conn->real_escape_string($data['message']) : '';
    
    if ($conversation_id <= 0 || empty($message)) {
        echo json_encode(["status" => "error", "message" => "Conversation ID and message are required"]);
        return;
    }
    
    // Insert message
    $query = "INSERT INTO chat_messages (conversation_id, sender_type, sender_id, message) 
              VALUES ($conversation_id, '$sender_type', $sender_id, '$message')";
    
    if ($conn->query($query)) {
        $message_id = $conn->insert_id;
        
        // Update conversation timestamp
        $conn->query("UPDATE chat_conversations SET updated_at = NOW() WHERE id = $conversation_id");
        
        // If user sent message, generate auto-reply (simulating support response)
        if ($sender_type === 'user') {
            generateAutoReply($conversation_id, $message);
        }
        
        echo json_encode([
            "status" => "success",
            "message" => "Message sent",
            "message_id" => $message_id,
            "timestamp" => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to send message: " . $conn->error]);
    }
}

// ========================================================================
// AUTO REPLY (Simulated Support - can be replaced with real agent system)
// ========================================================================
function generateAutoReply($conversation_id, $userMessage) {
    global $conn;
    
    // Simple keyword-based auto replies
    $userMessageLower = strtolower($userMessage);
    $reply = "";
    
    if (strpos($userMessageLower, 'booking') !== false || strpos($userMessageLower, 'order') !== false) {
        $reply = "For booking inquiries, please provide your booking ID and we'll look into it right away. You can also check your booking status in 'My Bookings' section.";
    } elseif (strpos($userMessageLower, 'payment') !== false || strpos($userMessageLower, 'refund') !== false) {
        $reply = "For payment or refund related queries, please share your booking ID and payment details. Our finance team typically processes refunds within 5-7 business days.";
    } elseif (strpos($userMessageLower, 'cancel') !== false) {
        $reply = "To cancel a booking, go to 'My Bookings' > Select the booking > Click 'Cancel'. Cancellation charges may apply based on the timing.";
    } elseif (strpos($userMessageLower, 'price') !== false || strpos($userMessageLower, 'cost') !== false) {
        $reply = "Our prices vary based on equipment type and rental duration. You can view all prices in the respective category sections. For bulk orders, contact us for special discounts!";
    } elseif (strpos($userMessageLower, 'delivery') !== false) {
        $reply = "We provide free delivery within 10km radius. For locations beyond that, additional delivery charges apply. Delivery is typically arranged 1 day before your event.";
    } elseif (strpos($userMessageLower, 'thank') !== false) {
        $reply = "You're welcome! Is there anything else I can help you with today?";
    } elseif (strpos($userMessageLower, 'hi') !== false || strpos($userMessageLower, 'hello') !== false) {
        $reply = "Hello! How can I assist you today? Feel free to ask about bookings, equipment, pricing, or any other queries.";
    } else {
        $replies = [
            "Thank you for your message. Our support team will review and respond shortly. For urgent matters, please call us at +91-9876543210.",
            "We've received your query. A support agent will assist you soon. Average response time is 5-10 minutes during business hours.",
            "Thanks for reaching out! Please provide more details so we can assist you better. You can also check our FAQ section for common questions."
        ];
        $reply = $replies[array_rand($replies)];
    }
    
    // Insert auto-reply with a small delay simulation
    if (!empty($reply)) {
        $reply = $conn->real_escape_string($reply);
        $query = "INSERT INTO chat_messages (conversation_id, sender_type, sender_id, message) 
                  VALUES ($conversation_id, 'support', 1, '$reply')";
        $conn->query($query);
    }
}

// ========================================================================
// GET MESSAGES FOR A CONVERSATION
// ========================================================================
function getMessages() {
    global $conn;
    
    $conversation_id = isset($_GET['conversation_id']) ? intval($_GET['conversation_id']) : 0;
    $last_id = isset($_GET['last_id']) ? intval($_GET['last_id']) : 0;
    
    if ($conversation_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Conversation ID is required"]);
        return;
    }
    
    // Get messages (optionally only newer than last_id for real-time polling)
    $query = "SELECT m.id, m.sender_type, m.sender_id, m.message, m.is_read, m.created_at,
              CASE WHEN m.sender_type = 'support' THEN 'Support Team' ELSE u.name END as sender_name
              FROM chat_messages m
              LEFT JOIN users u ON m.sender_type = 'user' AND m.sender_id = u.id
              WHERE m.conversation_id = $conversation_id";
    
    if ($last_id > 0) {
        $query .= " AND m.id > $last_id";
    }
    
    $query .= " ORDER BY m.created_at ASC";
    
    $result = $conn->query($query);
    $messages = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = [
                "id" => intval($row['id']),
                "sender_type" => $row['sender_type'],
                "sender_id" => intval($row['sender_id']),
                "sender_name" => $row['sender_name'] ?? 'Support Team',
                "message" => $row['message'],
                "is_read" => (bool)$row['is_read'],
                "timestamp" => $row['created_at']
            ];
        }
        
        // Mark messages as read
        $conn->query("UPDATE chat_messages SET is_read = TRUE WHERE conversation_id = $conversation_id AND sender_type = 'support'");
    }
    
    echo json_encode([
        "status" => "success",
        "conversation_id" => $conversation_id,
        "messages" => $messages,
        "count" => count($messages)
    ]);
}

// ========================================================================
// GET ALL CONVERSATIONS FOR A USER
// ========================================================================
function getConversations() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    $query = "SELECT c.id, c.subject, c.status, c.created_at, c.updated_at,
              (SELECT COUNT(*) FROM chat_messages WHERE conversation_id = c.id AND sender_type = 'support' AND is_read = FALSE) as unread_count,
              (SELECT message FROM chat_messages WHERE conversation_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message
              FROM chat_conversations c
              WHERE c.user_id = $user_id
              ORDER BY c.updated_at DESC";
    
    $result = $conn->query($query);
    $conversations = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $conversations[] = [
                "id" => intval($row['id']),
                "subject" => $row['subject'],
                "status" => $row['status'],
                "last_message" => $row['last_message'],
                "unread_count" => intval($row['unread_count']),
                "created_at" => $row['created_at'],
                "updated_at" => $row['updated_at']
            ];
        }
    }
    
    echo json_encode([
        "status" => "success",
        "conversations" => $conversations,
        "count" => count($conversations)
    ]);
}

// ========================================================================
// CLOSE CONVERSATION
// ========================================================================
function closeConversation() {
    global $conn;
    
    $data = json_decode(file_get_contents("php://input"), true);
    $conversation_id = isset($data['conversation_id']) ? intval($data['conversation_id']) : 0;
    
    if ($conversation_id <= 0) {
        echo json_encode(["status" => "error", "message" => "Conversation ID is required"]);
        return;
    }
    
    $query = "UPDATE chat_conversations SET status = 'closed' WHERE id = $conversation_id";
    
    if ($conn->query($query)) {
        // Send closing message
        $closeMsg = "This conversation has been closed. Thank you for contacting EventEase Support. Feel free to start a new chat if you need further assistance!";
        $conn->query("INSERT INTO chat_messages (conversation_id, sender_type, sender_id, message) VALUES ($conversation_id, 'support', 1, '$closeMsg')");
        
        echo json_encode(["status" => "success", "message" => "Conversation closed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to close conversation"]);
    }
}

// ========================================================================
// GET UNREAD MESSAGE COUNT
// ========================================================================
function getUnreadCount() {
    global $conn;
    
    $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
    
    if ($user_id <= 0) {
        echo json_encode(["status" => "error", "message" => "User ID is required"]);
        return;
    }
    
    $query = "SELECT COUNT(*) as unread_count FROM chat_messages m
              JOIN chat_conversations c ON m.conversation_id = c.id
              WHERE c.user_id = $user_id AND m.sender_type = 'support' AND m.is_read = FALSE";
    
    $result = $conn->query($query);
    $count = 0;
    
    if ($result && $row = $result->fetch_assoc()) {
        $count = intval($row['unread_count']);
    }
    
    echo json_encode([
        "status" => "success",
        "unread_count" => $count
    ]);
}
?>
