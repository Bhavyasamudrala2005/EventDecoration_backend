-- Add OTP columns to users table for password reset functionality
-- Run this SQL in phpMyAdmin or MySQL console

ALTER TABLE users 
ADD COLUMN otp VARCHAR(6) DEFAULT NULL,
ADD COLUMN otp_expiry DATETIME DEFAULT NULL;

-- Verify the columns were added
DESCRIBE users;
