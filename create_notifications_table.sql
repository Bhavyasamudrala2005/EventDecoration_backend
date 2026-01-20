-- Create notifications table for storing admin notifications
-- Run this SQL in your phpMyAdmin or MySQL client

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,  -- NULL means broadcast to all users
    type ENUM('admin', 'booking', 'new_equipment', 'promotion') DEFAULT 'admin',
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add fcm_token column to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS fcm_token VARCHAR(500) DEFAULT NULL;

-- Optional: Create index for faster queries
CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_status ON notifications(status);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at DESC);
