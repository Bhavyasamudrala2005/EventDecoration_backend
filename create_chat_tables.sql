-- ========================================================================
-- LIVE CHAT SUPPORT - DATABASE TABLES
-- Run this SQL in phpMyAdmin to create chat tables
-- ========================================================================

-- 1. Chat Conversations Table
-- Stores each chat session between user and support
CREATE TABLE IF NOT EXISTS chat_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) DEFAULT 'General Inquiry',
    status ENUM('open', 'closed', 'pending') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 2. Chat Messages Table
-- Stores individual messages in each conversation
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_type ENUM('user', 'support') NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES chat_conversations(id) ON DELETE CASCADE
);

-- 3. Support Agents Table (Optional - for admin support staff)
CREATE TABLE IF NOT EXISTS support_agents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    is_online BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========================================================================
-- SAMPLE DATA
-- ========================================================================

-- Insert a default support agent
INSERT INTO support_agents (id, name, email, password, is_online) VALUES
(1, 'Support Team', 'support@eventease.com', 'support123', TRUE)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- ========================================================================
-- VERIFY TABLES CREATED
-- ========================================================================
SHOW TABLES LIKE 'chat%';
DESCRIBE chat_conversations;
DESCRIBE chat_messages;
