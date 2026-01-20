-- SQL to create user_addresses table
-- Run this in phpMyAdmin or MySQL console

CREATE TABLE IF NOT EXISTS `user_addresses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `address_type` VARCHAR(50) DEFAULT 'Home',
    `contact_name` VARCHAR(100),
    `address_line1` VARCHAR(255) NOT NULL,
    `address_line2` VARCHAR(255),
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100),
    `zip_code` VARCHAR(20),
    `phone` VARCHAR(20),
    `is_default` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample addresses
INSERT INTO `user_addresses` (`user_id`, `address_type`, `contact_name`, `address_line1`, `address_line2`, `city`, `state`, `zip_code`, `phone`, `is_default`) VALUES
(1, 'Home', 'John Doe', '123 Main Street', 'Apt 4B', 'New York', 'NY', '10001', '+1 234 567 8900', 1),
(1, 'Work', 'John Doe', '456 Business Ave', 'Suite 200', 'New York', 'NY', '10002', '+1 234 567 8900', 0);
